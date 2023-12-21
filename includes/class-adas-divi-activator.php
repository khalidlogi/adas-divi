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
		// Hook into plugin activation
		register_activation_hook(__FILE__, 'create_divi_table');

		global $wpdb;

		// Table name
		$table_name = $wpdb->prefix . 'DIVI_TABLE';

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



}