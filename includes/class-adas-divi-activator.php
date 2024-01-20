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
	 * Create Db on activation 
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
		add_action('admin_notices', 'adas_divi_theme_not_active_notice');
	}
	function adas_divi_theme_not_active_notice() {

			echo '<div class="notice notice-error">
					<p>' . __('The Divi theme is not active. Please activate the Divi theme to use this plugin.', 'adasdividb') . '</p>
				  </div>';

	}
	
	if ( version_compare( PHP_VERSION, $php, '<' ) ) {
		deactivate_plugins( basename( __FILE__ ) );
		wp_die(
			'<p>' .
			esc_html_e( 'This plugin cannot be activated because it requires a PHP version greater than your current PHP version. Your PHP version can be updated by your hosting company.', 'adasdividb' )
			. '</p> <a href="' . esc_url(admin_url( 'plugins.php' )) . '">' . __( 'Go back', 'adasdividb' ) . '</a>',
			'Plugin Activation Error',
			array( 'response' => 200 )
		);
	}

	if ( version_compare( $wp_version, $wp, '<' ) ) {
		deactivate_plugins( basename( __FILE__ ) );
		wp_die(
			'<p>' .
			esc_html_e( 'This plugin cannot be activated because it requires a WordPress version greater than your current WordPress version. Please go to Dashboard; Updates to grab the latest version of WordPress.', 'adasdividb' )
			. '</p> <a href="' . esc_url(admin_url( 'plugins.php' )) . '">' . __( 'Go back', 'adasdividb' ) . '</a>',
			'Plugin Activation Error',
			array( 'response' => 200 )
		);
	}
}
	}
	
	



