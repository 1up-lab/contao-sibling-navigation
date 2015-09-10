<?php

$GLOBALS['TL_DCA']['tl_content']['palettes']['sibling_navigation_news']  = '
    {type_legend},type;
    {config_legend},snn_news_archives;
    {protected_legend:hide},protected;
    {expert_legend:hide},guests,cssID,space;
    {invisible_legend:hide},invisible,start,stop';

$GLOBALS['TL_DCA']['tl_content']['palettes']['sibling_navigation_event'] = '
    {type_legend},type;
    {config_legend},snn_event_calendars;
    {protected_legend:hide},protected;
    {expert_legend:hide},guests,cssID,space;
    {invisible_legend:hide},invisible,start,stop';

$GLOBALS['TL_DCA']['tl_content']['palettes']['sibling_navigation_page'] = '
    {type_legend},type;
    {protected_legend:hide},protected;
    {expert_legend:hide},guests,cssID,space;
    {invisible_legend:hide},invisible,start,stop';

$GLOBALS['TL_DCA']['tl_content']['fields']['snn_news_archives'] = [
    'label'                   => &$GLOBALS['TL_LANG']['tl_content']['snn_news_archives'],
    'exclude'                 => true,
    'inputType'               => 'checkbox',
    'options_callback'        => [
        'Oneup\SiblingNavigation\Helper\News\NewsHelper',
        'getNewsArchives',
    ],
    'eval'                    => [
        'multiple' => true,
        'mandatory' => true,
    ],
    'sql'                     => "blob NULL",
];

$GLOBALS['TL_DCA']['tl_content']['fields']['snn_event_calendars'] = [
    'label'                   => &$GLOBALS['TL_LANG']['tl_content']['snn_event_calendars'],
    'exclude'                 => true,
    'inputType'               => 'checkbox',
    'options_callback'        => [
        'Oneup\SiblingNavigation\Helper\Event\EventHelper',
        'getCalendars',
    ],
    'eval'                    => [
        'multiple' => true,
        'mandatory' => true,
    ],
    'sql'                     => "blob NULL",
];
