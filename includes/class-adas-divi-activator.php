<?php

/**
 * Fired during plugin activation
 *
 * @link       https://web-pro.store
 * @since      1.0.0
 *
 * @package    Adas_Divi
 * @subpackage Adas_Divi/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Adas_Divi
 * @subpackage Adas_Divi/includes
 * @author     khalidlogi <KHALIDLOGI@GMAIL.COM>
 */
class Adas_Divi_Activator
{

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate()
	{
		global $wpdb;
		$is_divi_active = class_divi_KHdb::getInstance()->is_divi_active();
		self::Checking_things();
		// Table name
		$table_name = $wpdb->prefix . 'divi_table';

		// SQL query to create the table
		$sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
        form_values LONGTEXT,
        page_id INT,
        page_name VARCHAR(255),
        page_url VARCHAR(255),
        date_submitted TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        read_status INT DEFAULT 0,
        read_date TIMESTAMP NULL,
        contact_form_id VARCHAR(255)
    )";

		// Execute the query
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);

	}

	public static function Checking_things(){
	global $wp_version;
	$is_divi_active = class_divi_KHdb::getInstance()->is_divi_active();


	$php = '5.3';
	$wp  = '3.8';


	if ( !$is_divi_active ) {
		add_action('admin_notices', 'my_plugin_divi_theme_not_active_notice');

		/*deactivate_plugins( basename( __FILE__ ) );
		wp_die(
			'<p>' .
			sprintf(
				__( 'Divi Theme is not ACTIVE. Please Activate the theme and try again!', 'my_plugin' ),
				$php
			)
			. '</p> <a href="' . admin_url( 'plugins.php' ) . '">' . __( 'go back', 'my_plugin' ) . '</a>'
		);*/
	}
	function my_plugin_divi_theme_not_active_notice() {
		//if (!is_plugin_active('divi/divi.php')) {
			echo '<div class="notice notice-error">
					<p>' . __('The Divi theme is not active. Please activate the Divi theme to use this plugin.', 'my_plugin') . '</p>
				  </div>';
		//}
	}
	
	if ( version_compare( PHP_VERSION, $php, '<' ) ) {
		deactivate_plugins( basename( __FILE__ ) );
		wp_die(
			'<p>' .
			sprintf(
				__( 'This plugin can not be activated because it requires a PHP version greater than %1$s. Your PHP version can be updated by your hosting company.', 'my_plugin' ),
				$php
			)
			. '</p> <a href="' . admin_url( 'plugins.php' ) . '">' . __( 'go back', 'my_plugin' ) . '</a>'
		);
	}

	if ( version_compare( $wp_version, $wp, '<' ) ) {
		deactivate_plugins( basename( __FILE__ ) );
		wp_die(
			'<p>' .
			sprintf(
				__( 'This plugin can not be activated because it requires a WordPress version greater than %1$s. Please go to Dashboard &#9656; Updates to gran the latest version of WordPress .', 'my_plugin' ),
				$php
			)
			. '</p> <a href="' . admin_url( 'plugins.php' ) . '">' . __( 'go back', 'my_plugin' ) . '</a>'
		);
	}
}
	}
	
	



