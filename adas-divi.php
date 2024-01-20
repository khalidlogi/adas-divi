<?php

/**
 * @wordpress-plugin
 * Plugin Name:       Adas Forms DB
 * Plugin URI:        https://web-pro.store/adas-divi-add-on/
 * Description:       Enhance Divi Contact Form with a powerful database feature for effortless storage and organization of form submissions.

 * Version:           1.0.0
 * Author:            khalidlogi
 * Author URI:        https://web-pro.store/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       adasdividb
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

/**
 * Currently plugin version.
 */
define('ADAS_Divi_VERSION', '1.0.0');

/**
 * The code that runs during plugin activation.
 */
function activate_adas_divi()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-adas-divi-activator.php';
	Adas_Divi_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-adas-divi-deactivator.php
 */
function deactivate_adas_divi()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-adas-divi-deactivator.php';
	Adas_Divi_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_adas_divi');
register_deactivation_hook(__FILE__, 'deactivate_adas_divi');


/**
 * Add links in plugin page
 */
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'adas_action_links');

function adas_action_links($links) {
    $settings_link = '<a href="' . esc_url(admin_url('/options-general.php?page=khdiviwplist.php')) . '">Settings</a>';
    $links[] = $settings_link;
    return $links;
}

/**
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-adas-divi.php';

/**
 * Begins execution of the plugin.
 */
function run_adas_divi()
{

	$plugin = new Adas_Divi();
	$plugin->run();

}
run_adas_divi();