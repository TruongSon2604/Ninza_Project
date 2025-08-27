<?php
 
/**
* Auto require all files in folder features/
*/
 
$features_path = WOO_NINZA_PLUGIN_PATH . 'includes/features';
 
foreach (glob($features_path . '/*.php') as $feature_file) {
    require_once $feature_file;
}