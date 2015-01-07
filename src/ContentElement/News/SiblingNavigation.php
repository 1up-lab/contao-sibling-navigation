<?php

namespace Oneup\SiblingNavigation\ContentElement\News;

class SiblingNavigation extends \ContentElement
{
    protected $strTemplate = 'ce_sibling_navigation_news';

    public function generate()
    {
        if (TL_MODE == 'BE')
        {
            $objTemplate = new \BackendTemplate('be_wildcard');

            $objTemplate->wildcard = '### ' . utf8_strtoupper($GLOBALS['TL_LANG']['CTE']['oneup_sibling_navigation'][0]) . ' ###';

            return $objTemplate->parse();
        }

        return parent::generate();
    }

    protected function generateSiblingNavigation($objPage)
    {
        // Set the item from the auto_item parameter
        if (!isset($_GET['items']) && $GLOBALS['TL_CONFIG']['useAutoItem'] && isset($_GET['auto_item']))
        {
            \Input::setGet('items', \Input::get('auto_item'));
        }

        // Do not index or cache the page if no news item has been specified
        if (!\Input::get('items'))
        {
            global $objPage;
            $objPage->noSearch = 1;
            $objPage->cache = 0;
            return '';
        }

        $alias = \Input::get('items');

        $current = \NewsModel::findByIdOrAlias($alias);

        // find prev
        $prev = \NewsModel::findAll(array(
            'column' => array(
                "pid = '$current->pid'",
                "published = '1'",
                "tl_news.tstamp < $current->tstamp"
            ),
            'order' => 'tl_news.tstamp DESC',
            'limit' => 1
        ));

        if ($prev) {
            $prev = $prev->current();
        }

        $next = \NewsModel::findAll(array(
            'column' => array(
                "pid = '$current->pid'",
                "published = '1'",
                "tl_news.tstamp > $current->tstamp"
            ),
            'order' => 'tl_news.tstamp ASC',
            'limit' => 1
        ));

        if ($next) {
            $next = $next->current();
        }

        // take care, prev/next are swapped
        return array(
            'prev' => $this->generateNewsUrl($objPage, $next),
            'this' => $this->generateNewsUrl($objPage, $current),
            'next' => $this->generateNewsUrl($objPage, $prev)
        );
    }

    protected function generateNewsUrl($objPage, $news)
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
