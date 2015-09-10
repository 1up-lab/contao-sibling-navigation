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

    public function generateSiblingNavigation($objPage, $type = 'regular', $hidden = '', $unpublished = '')
    {
        $prev = null;
        $next = null;

        $published = "published = '1'";
        $hide = "hide = ''";

        if ('1' === $unpublished) {
            $published = "(published = '1' OR published = '')";
        }

        if ('1' === $hidden) {
            $hide = "(hide = '' OR hide = '1')";
        }

        $prev = \PageModel::findAll([
            'column' => [
                "pid = $objPage->pid",
                $published,
                $hide,
                "type = '$type'",
                "sorting < $objPage->sorting",
            ],
            'order' => 'sorting DESC',
            'limit' => 1,
        ]);

        $next = \PageModel::findAll([
            'column' => [
                "pid = $objPage->pid",
                $published,
                $hide,
                "type = '$type'",
                "sorting > $objPage->sorting",
            ],
            'order' => 'sorting ASC',
            'limit' => 1,
        ]);

        return array (
            'prev'      => $prev ? $this->generateFrontendUrl($prev->row()) : null,
            'next'      => $next ? $this->generateFrontendUrl($next->row()) : null,
            'prevTitle' => $prev ? $prev->title : '',
            'nextTitle' => $next ? $next->title : '',
            'objPrev'   => $prev,
            'objNext'   => $next,
        );
    }
}
