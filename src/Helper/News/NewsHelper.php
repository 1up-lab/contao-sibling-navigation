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

    public function generateSiblingNavigation($objPage, $newsArchives)
    {
        // Set the item from the auto_item parameter
        if (!isset($_GET['items']) && $GLOBALS['TL_CONFIG']['useAutoItem'] && isset($_GET['auto_item'])) {
            \Input::setGet('items', \Input::get('auto_item'));
        }

        // Do not index or cache the page if no news item has been specified
        if (!\Input::get('items')) {
            $objPage->noSearch = 1;
            $objPage->cache = 0;

            return [];
        }

        $this->news_archives = $this->sortOutProtected(deserialize($newsArchives));

        // Return if there are no archives
        if (!is_array($this->news_archives) || empty($this->news_archives)) {
            return [];
        }

        $alias = \Input::get('items');

        $current = \NewsModel::findByIdOrAlias($alias);

        if (!in_array($current->pid, $this->news_archives)) {
            $this->news_archives = [$current->pid];
        }

        // find prev
        $prev = \NewsModel::findAll([
            'column' => [
                "pid IN (".implode(',', $this->news_archives).")",
                "published = '1'",
                "tl_news.date < $current->date",
                "tl_news.time < $current->time",
            ],
            'order' => 'tl_news.time DESC, tl_news.date DESC',
            'limit' => 1,
        ]);

        if ($prev) {
            $prev = $prev->current();
        }

        $next = \NewsModel::findAll([
            'column' => [
                "pid IN (".implode(',', $this->news_archives).")",
                "published = '1'",
                "tl_news.date > $current->date",
                "tl_news.time > $current->time",
            ],
            'order' => 'tl_news.time ASC, tl_news.date ASC',
            'limit' => 1,
        ]);

        if ($next) {
            $next = $next->current();
        }

        // take care, prev/next are swapped
        return [
            'prev'     => $this->generateNewsUrl($objPage, $next),
            'next'     => $this->generateNewsUrl($objPage, $prev),
        ];
    }

    protected function sortOutProtected($archives)
    {
        if (BE_USER_LOGGED_IN || !is_array($archives) || empty($archives)) {
            return $archives;
        }

        $this->import('FrontendUser', 'User');
        $objArchive = \NewsArchiveModel::findMultipleByIds($archives);
        $arrArchives = [];

        if ($objArchive !== null) {
            while ($objArchive->next()) {
                if ($objArchive->protected) {
                    if (!FE_USER_LOGGED_IN) {
                        continue;
                    }

                    $groups = deserialize($objArchive->groups);

                    if (!is_array($groups) || empty($groups) || !count(array_intersect($groups, $this->User->groups))) {
                        continue;
                    }
                }

                $arrArchives[] = $objArchive->id;
            }
        }

        return $arrArchives;
    }

    protected function generateNewsUrl($objPage, $news = null)
    {
        if (null === $news) {
            return null;
        }

        $strUrl = $this->generateFrontendUrl(
            $objPage->row(),
            (($GLOBALS['TL_CONFIG']['useAutoItem'] && !$GLOBALS['TL_CONFIG']['disableAlias'])
                ?  '/%s'
                : '/items/%s'), $objPage->language
        );

        $strUrl = sprintf(
            $strUrl,
            (($news->alias != '' && !$GLOBALS['TL_CONFIG']['disableAlias'])
                ? $news->alias
                : $news->id)
        );

        return $strUrl;
    }
}
