<?php

namespace Oneup\SiblingNavigation\ContentElement\Event;

class SiblingNavigation extends \ContentElement
{
    protected $strTemplate = 'ce_sibling_navigation_event';

    public function generate()
    {
        if (TL_MODE == 'BE') {
            $objTemplate = new \BackendTemplate('be_wildcard');

            $objTemplate->wildcard = '### '.utf8_strtoupper($GLOBALS['TL_LANG']['CTE']['sibling_navigation_event'][0]).' ###';

            return $objTemplate->parse();
        }

        return parent::generate();
    }

    protected function sortOutProtected($calendars)
    {
        if (BE_USER_LOGGED_IN || !is_array($calendars) || empty($calendars)) {
            return $calendars;
        }

        $this->import('FrontendUser', 'User');
        $objCalendars = \CalendarModel::findMultipleByIds($calendars);
        $calendars = [];

        if ($objCalendars !== null) {
            while ($objCalendars->next()) {
                if ($objCalendars->protected) {
                    if (!FE_USER_LOGGED_IN) {
                        continue;
                    }

                    $groups = deserialize($objCalendars->groups);

                    if (!is_array($groups) || empty($groups) || !count(array_intersect($groups, $this->User->groups))) {
                        continue;
                    }
                }

                $calendars[] = $objCalendars->id;
            }
        }

        return $calendars;
    }

    protected function generateSiblingNavigation($objPage)
    {
        global $objPage;

        // Set the item from the auto_item parameter
        if (!isset($_GET['items']) && $GLOBALS['TL_CONFIG']['useAutoItem'] && isset($_GET['auto_item'])) {
            \Input::setGet('items', \Input::get('auto_item'));
        }

        // Do not index or cache the page if no news item has been specified
        if (!\Input::get('items')) {
            $objPage->noSearch = 1;
            $objPage->cache = 0;

            return [];
        }

        $this->calendars = $this->sortOutProtected(deserialize($this->snn_event_calendars));

        // Return if there are no archives
        if (!is_array($this->calendars) || empty($this->calendars)) {
            return [];
        }

        $alias = \Input::get('items');

        $current = \CalendarEventsModel::findByIdOrAlias($alias);

        if (!in_array($current->pid, $this->calendars)) {
            $this->calendars = [$current->pid];
        }

        // find prev
        $prev = \CalendarEventsModel::findAll([
            'column' => [
                "pid IN (".implode(',', $this->calendars).")",
                "published = '1'",
                "tl_calendar_events.startTime < $current->startTime",
            ],
            'order' => 'tl_calendar_events.startTime DESC',
            'limit' => 1,
        ]);

        if ($prev) {
            $prev = $prev->current();
        }

        $next = \CalendarEventsModel::findAll([
            'column' => [
                "pid IN (".implode(',', $this->calendars).")",
                "published = '1'",
                "tl_calendar_events.startTime > $current->startTime",
            ],
            'order' => 'tl_calendar_events.startTime ASC',
            'limit' => 1,
        ]);

        if ($next) {
            $next = $next->current();
        }

        // take care, prev/next are swapped
        return array(
            'prev' => $this->generateNewsUrl($objPage, $next),
            'this' => $this->generateFrontendUrl($objPage->row(), null, $objPage->language),
            'next' => $this->generateNewsUrl($objPage, $prev),
        );
    }

    protected function generateNewsUrl($objPage, $news = null)
    {
        if (null === $news) {
            return null;
        }

        $strUrl = $this->generateFrontendUrl($objPage->row(), (($GLOBALS['TL_CONFIG']['useAutoItem'] && !$GLOBALS['TL_CONFIG']['disableAlias']) ?  '/%s' : '/items/%s'), $objPage->language);
        $strUrl = sprintf($strUrl, (($news->alias != '' && !$GLOBALS['TL_CONFIG']['disableAlias']) ? $news->alias : $news->id));

        return $strUrl;
    }

    protected function compile()
    {
        global $objPage;

        $siblingNavigation = $this->generateSiblingNavigation($objPage);

        $this->Template->prev = $siblingNavigation['prev'];
        $this->Template->self = $siblingNavigation['this'];
        $this->Template->next = $siblingNavigation['next'];
        $this->Template->prevText = $GLOBALS['TL_LANG']['CSTM']['prevText'];
        $this->Template->selfText = $GLOBALS['TL_LANG']['CSTM']['selfText'];
        $this->Template->nextText = $GLOBALS['TL_LANG']['CSTM']['nextText'];
    }
}
