<?php

namespace Oneup\SiblingNavigation\Helper\Page;

class PageHelper extends \Backend
{
    /**
     * Import the back end user object
     */
    public function __construct()
    {
        parent::__construct();
        $this->import('BackendUser', 'User');
    }

    public function generateSiblingNavigation($objPage)
    {
        $prev = null;
        $next = null;

        $prev = \PageModel::findAll([
            'column' => [
                "pid = $objPage->pid",
                "published ='1'",
                "hide = ''",
                "type = 'regular'",
                "sorting < $objPage->sorting",
            ],
            'order' => 'sorting DESC',
            'limit' => 1,
        ]);

        $next = \PageModel::findAll([
            'column' => [
                "pid = $objPage->pid",
                "published ='1'",
                "hide = ''",
                "type = 'regular'",
                "sorting > $objPage->sorting",
            ],
            'order' => 'sorting ASC',
            'limit' => 1,
        ]);

        return array (
            'prev'      => $prev ? $this->generateFrontendUrl($prev->row()) : null,
            'next'      => $next ? $this->generateFrontendUrl($next->row()) : null,
            'prevTitle' => $next->title,
            'nextTitle' => $prev->title,
            'objPrev'   => $prev,
            'objNext'   => $next,
        );
    }
}
