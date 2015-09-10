<?php

namespace Oneup\SiblingNavigation\ContentElement\Page;

use Oneup\SiblingNavigation\Helper\Page\PageHelper;

class SiblingNavigation extends \ContentElement
{
    protected $strTemplate = 'ce_sibling_navigation_page';

    public function generate()
    {
        if (TL_MODE == 'BE') {
            $objTemplate = new \BackendTemplate('be_wildcard');

            $objTemplate->wildcard = '### '
                . utf8_strtoupper($GLOBALS['TL_LANG']['CTE']['sibling_navigation_page'][0])
                . ' ###';

            return $objTemplate->parse();
        }

        return parent::generate();
    }



    protected function compile()
    {
        global $objPage;

        $helper = new PageHelper();

        $siblingNavigation = $helper->generateSiblingNavigation($objPage, $this->pageType, $this->hidden, $this->unpublished);

        $this->Template->prev      = $siblingNavigation['prev'];
        $this->Template->next      = $siblingNavigation['next'];
        $this->Template->prevTitle = $siblingNavigation['prevTitle'];
        $this->Template->nextTitle = $siblingNavigation['nextTitle'];
        $this->Template->objPrev   = $siblingNavigation['objPrev'];
        $this->Template->objNext   = $siblingNavigation['objNext'];
    }
}
