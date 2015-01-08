<?php

namespace Oneup\SiblingNavigation\Helper\Event;

class EventHelper extends \Backend
{
    public function __construct()
    {
        parent::__construct();
        $this->import('BackendUser', 'User');
    }

    public function getCalendars()
    {
        if (!$this->User->isAdmin && !is_array($this->User->calendars)) {
            return [];
        }

        $calendars = [];
        $objCalendars = $this->Database->execute("SELECT id, title FROM tl_calendar ORDER BY title");

        while ($objCalendars->next()) {
            if ($this->User->hasAccess($objCalendars->id, 'news')) {
                $calendars[$objCalendars->id] = $objCalendars->title;
            }
        }

        return $calendars;
    }

    public function generateSiblingNavigation($objPage, $eventCalendars)
    {
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

        $this->calendars = $this->sortOutProtected(deserialize($eventCalendars));

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

        return [
            'prev'      => $this->generateEventUrl($objPage, $prev),
            'next'      => $this->generateEventUrl($objPage, $next),
            'prevTitle' => $prev->title,
            'nextTitle' => $next->title,
            'objPrev'   => $prev,
            'objNext'   => $next,
        ];
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

    protected function generateEventUrl($objPage, $news = null)
    {
        if (null === $news) {
            return null;
        }

        $strUrl = $this->generateFrontendUrl(
            $objPage->row(),
            (($GLOBALS['TL_CONFIG']['useAutoItem'] && !$GLOBALS['TL_CONFIG']['disableAlias'])
                ?  '/%s'
                : '/items/%s'), $objPage->language
        );

        $strUrl = sprintf(
            $strUrl,
            (($news->alias != '' && !$GLOBALS['TL_CONFIG']['disableAlias'])
                ? $news->alias
                : $news->id)
        );

        return $strUrl;
    }
}
