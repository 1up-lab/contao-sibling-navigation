<?php

namespace Oneup\SiblingNavigation\Helper\Event;

class EventHelper extends \Backend
{
    public function __construct()
    {
        parent::__construct();
        $this->import('BackendUser', 'User');
    }

    public function getCalendars()
    {
        if (!$this->User->isAdmin && !is_array($this->User->calendars)) {
            return [];
        }

        $calendars = [];
        $objCalendars = $this->Database->execute("SELECT id, title FROM tl_calendar ORDER BY title");

        while ($objCalendars->next()) {
            if ($this->User->hasAccess($objCalendars->id, 'news')) {
                $calendars[$objCalendars->id] = $objCalendars->title;
            }
        }

        return $calendars;
    }
}
