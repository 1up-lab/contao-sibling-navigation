<?php

$GLOBALS['TL_DCA']['tl_module']['palettes']['sibling_navigation_news']  = '
    {title_legend},name,headline,type;
    {config_legend},snn_news_archives;
    {protected_legend:hide},protected;
    {expert_legend:hide},guests,cssID,space;
    {invisible_legend:hide},invisible,start,stop';

$GLOBALS['TL_DCA']['tl_module']['palettes']['sibling_navigation_event'] = '
    {title_legend},name,headline,type;
    {config_legend},snn_event_calendars;
    {protected_legend:hide},protected;
    {expert_legend:hide},guests,cssID,space;
    {invisible_legend:hide},invisible,start,stop';

$GLOBALS['TL_DCA']['tl_module']['palettes']['sibling_navigation_page'] = '
    {title_legend},name,headline,type;
    {config_legend},pageType,hidden,unpublished;
    {protected_legend:hide},protected;
    {expert_legend:hide},guests,cssID,space;
    {invisible_legend:hide},invisible,start,stop';

$GLOBALS['TL_DCA']['tl_module']['fields']['snn_news_archives'] = [
    'label'                   => &$GLOBALS['TL_LANG']['tl_module']['snn_news_archives'],
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

$GLOBALS['TL_DCA']['tl_module']['fields']['snn_event_calendars'] = [
    'label'                   => &$GLOBALS['TL_LANG']['tl_module']['snn_event_calendars'],
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

$GLOBALS['TL_DCA']['tl_module']['fields']['pageType'] = [
    'label'             => &$GLOBALS['TL_LANG']['tl_module']['pageType'],
    'default'           => 'regular',
    'inputType'         => 'select',
    'exclude'           => true,
    'options'           => array_keys($GLOBALS['TL_PTY']),
    'eval'              => [
        'helpwizard'        => true,
        'submitOnChange'    => true,
        'tl_class'          => 'w50',
    ],
    'reference'         => &$GLOBALS['TL_LANG']['PTY'],
    'sql'               => "varchar(32) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_module']['fields']['hidden'] =[
    'label'     => &$GLOBALS['TL_LANG']['tl_module']['hidden'],
    'exclude'   => true,
    'inputType' => 'checkbox',
    'eval'      => [
        'tl_class' => 'clr',
    ],
    'sql'       => "char(1) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_module']['fields']['unpublished'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_module']['unpublished'],
    'exclude'   => true,
    'inputType' => 'checkbox',
    'eval'      => [
        'tl_class' => 'w50',
    ],
    'sql'       => "char(1) NOT NULL default ''",
];
