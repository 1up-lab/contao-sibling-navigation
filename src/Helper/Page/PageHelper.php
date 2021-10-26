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
        $time = time();

        $published = "published = '1' AND (start='' OR start<='$time') AND (stop='' OR stop>'$time')";
        $hide = "hide = ''";

        if ('1' === $unpublished) {
            $published = "(published = '1' OR published = '')";
        }

        if ('1' === $hidden) {
            $hide = "(hide = '' OR hide = '1')";
        }

        $prev = \PageModel::findAll([
            'column' => [
                "pid = ?",
                $published,
                $hide,
                "type = ?",
                "sorting < ?",
            ],
            'value' => [
                $objPage->pid,
                $type,
                $objPage->sorting,
            ],
            'order' => 'sorting DESC',
            'limit' => 1,
        ]);

        $next = \PageModel::findAll([
            'column' => [
                "pid = ?",
                $published,
                $hide,
                "type = ?",
                "sorting > ?",
            ],
            'value' => [
                $objPage->pid,
                $type,
                $objPage->sorting,
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
