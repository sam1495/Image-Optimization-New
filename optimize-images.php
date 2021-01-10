<?php
/**
 * Plugin Name: Optimize Images
 * Plugin URI:
 * Description:
 * Version: 0.1.0
 * Author: Sakib
 */

if (!defined('IMG_OPTIMIZE_LIB')) {
    define( 'IMG_OPTIMIZE_LIB', plugin_dir_path(__FILE__));
}

if (!defined('IMG_OPTIMIZE_ASSET_DIR')) {
    define( 'IMG_OPTIMIZE_ASSET_DIR', plugin_dir_url(__FILE__).'assets/');
}



include('includes/class-image-optimize.php');

function optimize_plugin_action_links($links, $file)
{
    if ($file === plugin_basename(__FILE__)) {
        $settings_link = '<a href="?page=optimize-media-images">Settings</a>';
        array_unshift($links, $settings_link);
    }

    return $links;
}
add_filter('plugin_action_links', 'optimize_plugin_action_links', 10, 2);
