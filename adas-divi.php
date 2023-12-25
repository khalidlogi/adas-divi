<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://web-pro.store
 * @since             1.0.0
 * @package           Adas_Divi
 *
 * @wordpress-plugin
 * Plugin Name:       Adas Form Db Divi
 * Plugin URI:        https://url
 * Description:       Description: Enhance WPForms with a powerful database feature for effortless storage and organization of form submissions.

 * Version:           1.0.0
 * Author:            khalidlogi
 * Author URI:        https://web-pro.store/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       adas-divi
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('ADAS_DIVI_VERSION', '1.0.0');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-adas-divi-activator.php
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
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'my_plugin_action_links');

function my_plugin_action_links($links)
{
	$settings_link = '<a href="' . admin_url('/options-general.php?page=khdiviwplist.php') . '">Settings</a>';
	$links[] = $settings_link;
	return $links;
}

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-adas-divi.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_adas_divi()
{

	$plugin = new Adas_Divi();
	$plugin->run();

}
run_adas_divi();