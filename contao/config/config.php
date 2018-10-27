<?php

$GLOBALS['TL_CTE']['links'] = array_merge($GLOBALS['TL_CTE']['links'], [
    'sibling_navigation_news'  => 'Oneup\SiblingNavigation\ContentElement\News\SiblingNavigation',
    'sibling_navigation_event' => 'Oneup\SiblingNavigation\ContentElement\Event\SiblingNavigation',
    'sibling_navigation_page'  => 'Oneup\SiblingNavigation\ContentElement\Page\SiblingNavigation',
]);

if (array_key_exists('news', $GLOBALS['FE_MOD'])) {
    $GLOBALS['FE_MOD']['news'] = array_merge($GLOBALS['FE_MOD']['news'], [
        'sibling_navigation_news'  => 'Oneup\SiblingNavigation\Module\News\SiblingNavigation',
    ]);
}


if (array_key_exists('events', $GLOBALS['FE_MOD'])) {
    $GLOBALS['FE_MOD']['events'] = array_merge($GLOBALS['FE_MOD']['events'], [
        'sibling_navigation_event' => 'Oneup\SiblingNavigation\Module\Event\SiblingNavigation',
    ]);
}

$GLOBALS['FE_MOD']['navigationMenu'] = array_merge($GLOBALS['FE_MOD']['navigationMenu'], [
    'sibling_navigation_page'  => 'Oneup\SiblingNavigation\Module\Page\SiblingNavigation',
]);

