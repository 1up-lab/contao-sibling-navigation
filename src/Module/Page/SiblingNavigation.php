<?php

namespace Oneup\SiblingNavigation\Module\Page;

use Oneup\SiblingNavigation\Helper\Page\PageHelper;

class SiblingNavigation extends \Module
{
    protected $strTemplate = 'mod_sibling_navigation_page';

    public function generate()
    {
        if (TL_MODE == 'BE') {
            $objTemplate = new \BackendTemplate('be_wildcard');

            $objTemplate->wildcard = '### '
                . utf8_strtoupper($GLOBALS['TL_LANG']['FMD']['sibling_navigation_page'][0])
                . ' ###';

            $objTemplate->title = $this->headline;
            $objTemplate->id = $this->id;
            $objTemplate->link = $this->name;
            $objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

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
