<?php

namespace Oneup\SiblingNavigation\Helper\News;

class NewsHelper extends \Backend
{
    /**
     * Import the back end user object
     */
    public function __construct()
    {
        parent::__construct();
        $this->import('BackendUser', 'User');
    }

    /**
     * Get all news archives and return them as array
     * @return array
     */
    public function getNewsArchives()
    {
        if (!$this->User->isAdmin && !is_array($this->User->news)) {
            return [];
        }

        $arrArchives = [];
        $objArchives = $this->Database->execute("SELECT id, title FROM tl_news_archive ORDER BY title");

        while ($objArchives->next()) {
            if ($this->User->hasAccess($objArchives->id, 'news')) {
                $arrArchives[$objArchives->id] = $objArchives->title;
            }
        }

        return $arrArchives;
    }
}
