<?php

namespace Oneup;

class SiblingNavigation extends \ContentElement
{
    protected $strTemplate = 'ce_sibling_navigation';

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
        switch($objPage->type) {
            case 'newspage':
                $siblings = $this->generateSiblingNavigationFromNews($objPage);
                break;
            default:
                $siblings = $this->generateSiblingNavigationFromPage($objPage);
        }

        return $siblings;
    }

    protected function generateSiblingNavigationFromNews($objPage)
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

    protected function generateSiblingNavigationFromPage($objPage)
    {
        $prev = null;
        $next = null;

        $objParent = \PageModel::findByPk($objPage->pid);
        $pageOrder = deserialize($objParent->sushiOrder);
        $total = count($pageOrder);

        if (in_array($objPage->id, $pageOrder)) {
            $idx = array_search($objPage->id, $pageOrder);

            /*
             * thank you php a.k.a. sporadic satan!
             * http://php.net/manual/en/language.operators.arithmetic.php
             *
             * The result of the modulus operator % has the same sign as the dividend — that is,
             * the result of $a % $b will have the same sign as $a
             *
             * a mod b = ( a % b + b) % b
             */
            $prev = $pageOrder[($idx-1 % $total + $total) % $total];
            $next = $pageOrder[($idx+1 % $total + $total) % $total];
        }

        return array (
            'prev' => $this->generateFrontendUrl(\PageModel::findByPk($prev)->row()),
            'this' => $this->generateFrontendUrl($objParent->row()),
            'next' => $this->generateFrontendUrl(\PageModel::findByPk($next)->row()),
        );
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
