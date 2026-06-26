<?php
/**
 * Plugin Name: Fast Track Review Badge
 * Plugin URI: https://fasttrackurgentcare.com/
 * Description: Displays custom review badges using Smash Balloon Reviews Pro data.
 * Version: 1.0.0
 * Author: Garv Sharma
 * License: GPL-2.0+
 * Requires PHP: 7.4
 * Text Domain: fasttrack-review-badge
 */

if (!defined('ABSPATH')) {
    exit;
}

/*
|--------------------------------------------------------------------------
| Plugin Constants
|--------------------------------------------------------------------------
*/

define('FTRB_VERSION', '1.0.0');
define('FTRB_PLUGIN_FILE', __FILE__);
define('FTRB_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('FTRB_PLUGIN_URL', plugin_dir_url(__FILE__));

/*
|--------------------------------------------------------------------------
| Includes
|--------------------------------------------------------------------------
*/

require_once FTRB_PLUGIN_DIR . 'includes/class-plugin.php';
require_once FTRB_PLUGIN_DIR . 'includes/class-data.php';
require_once FTRB_PLUGIN_DIR . 'includes/class-renderer.php';

/*
|--------------------------------------------------------------------------
| Initialize Plugin
|--------------------------------------------------------------------------
*/

add_action('plugins_loaded', array('FTRB_Plugin', 'instance'));

/*
|--------------------------------------------------------------------------
| Theme Helper
|--------------------------------------------------------------------------
|
| This is the ONLY function your theme should ever call.
|
*/

if (!function_exists('ft_review_badge_output')) {

    function ft_review_badge_output($value)
    {
        return FTRB_Plugin::instance()->output($value);
    }

}
