<?php

namespace Oneup\SiblingNavigation\Module\Event;

use Oneup\SiblingNavigation\Helper\Event\EventHelper;

class SiblingNavigation extends \Module
{
    protected $strTemplate = 'mod_sibling_navigation_event';

    public function generate()
    {
        if (TL_MODE == 'BE') {
            $objTemplate = new \BackendTemplate('be_wildcard');

            $objTemplate->wildcard = '### '
                . utf8_strtoupper($GLOBALS['TL_LANG']['FMD']['sibling_navigation_event'][0])
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

        $helper = new EventHelper();

        $siblingNavigation = $helper->generateSiblingNavigation($objPage, $this->snn_event_calendars);

        $this->Template->prev      = $siblingNavigation['prev'];
        $this->Template->next      = $siblingNavigation['next'];
        $this->Template->prevTitle = $siblingNavigation['prevTitle'];
        $this->Template->nextTitle = $siblingNavigation['nextTitle'];
        $this->Template->objPrev   = $siblingNavigation['objPrev'];
        $this->Template->objNext   = $siblingNavigation['objNext'];
    }
}
