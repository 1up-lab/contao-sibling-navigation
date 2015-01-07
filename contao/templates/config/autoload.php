<?php

/**
 * Register the namespaces
 */
ClassLoader::addNamespaces(array
(
    'Oneup',
));




/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
    // Elements
    'Oneup\SiblingNavigation'  => 'system/modules/oneup-sibling-navigation/elements/SiblingNavigation.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
    'ce_sibling_navigation'       => 'system/modules/oneup-sibling-navigation/templates/',
));

