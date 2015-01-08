<?php

namespace Oneup\SiblingNavigation\Module\News;

use Oneup\SiblingNavigation\Helper\News\NewsHelper;

class SiblingNavigation extends \Module
{
    protected $strTemplate = 'mod_sibling_navigation_news';

    public function generate()
    {
        if (TL_MODE == 'BE') {
            $objTemplate = new \BackendTemplate('be_wildcard');

            $objTemplate->wildcard = '### '
                . utf8_strtoupper($GLOBALS['TL_LANG']['MOD']['sibling_navigation_news'][0])
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

        $helper = new NewsHelper();

        $siblingNavigation = $helper->generateSiblingNavigation($objPage, $this->snn_news_archives);

        $this->Template->prev     = $siblingNavigation['prev'];
        $this->Template->overview = $siblingNavigation['overview'];
        $this->Template->next     = $siblingNavigation['next'];
    }
}
