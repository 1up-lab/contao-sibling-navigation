<?php

namespace Oneup\SiblingNavigation\ContentElement\News;

use Oneup\SiblingNavigation\Helper\News\NewsHelper;

class SiblingNavigation extends \ContentElement
{
    protected $strTemplate = 'ce_sibling_navigation_news';

    public function generate()
    {
        if (TL_MODE == 'BE') {
            $objTemplate = new \BackendTemplate('be_wildcard');

            $objTemplate->wildcard = '### '
                . utf8_strtoupper($GLOBALS['TL_LANG']['CTE']['sibling_navigation_news'][0])
                . ' ###';

            return $objTemplate->parse();
        }

        return parent::generate();
    }



    protected function compile()
    {
        global $objPage;

        $helper = new NewsHelper();

        $siblingNavigation = $helper->generateSiblingNavigation($objPage, $this->snn_news_archives);

        $this->Template->prev      = $siblingNavigation['prev'];
        $this->Template->next      = $siblingNavigation['next'];
        $this->Template->prevTitle = $siblingNavigation['prevTitle'];
        $this->Template->nextTitle = $siblingNavigation['nextTitle'];
        $this->Template->objPrev   = $siblingNavigation['objPrev'];
        $this->Template->objNext   = $siblingNavigation['objNext'];
    }
}
