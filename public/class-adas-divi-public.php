<?php

/**
 * 
 * The public-facing functionality of the plugin.
 *
 * @link       https://web-pro.store
 * @since      1.0.0
 *
 * @package    Adas_Divi
 * @subpackage Adas_Divi/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Adas_Divi
 * @subpackage Adas_Divi/public
 * @author     khalidlogi <KHALIDLOGI@GMAIL.COM>
 */
class Adas_Divi_Public
{

	private $table_name;
	private $plugin_name;
	private $version;


	/**
	 * Initialize the class and set its properties.
	 */
	public function __construct($plugin_name, $version)
	{

		global $wpdb;
		$this->table_name = $wpdb->prefix . 'divi_table';
		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	
	/**
	 * Retrieve and return form values
	 */
	function get_form_values()
	{

		global $wpdb;
		$id = intval($_POST['id']);

		// Fetch form_value from the wpform_db2 table based on the form_id
		$query = $wpdb->prepare("SELECT id, form_values FROM $this->table_name WHERE id = %d", $id);
		$serialized_data = $wpdb->get_results($query);

		if ($wpdb->last_error) {
			//wp_send_json_error('Error: ' . $wpdb->last_error);
		}

		if ($serialized_data) {
			// Unserialize the serialized form value
			$unserialized_data = unserialize($serialized_data[0]->form_values);
			$fields = array();

			foreach ($unserialized_data as $key => $value) {
				if (is_array($value)) {
					if (array_key_exists('value', $value)) {
						$newvalue = stripslashes($value["value"]);
					} else {
						$newvalue = stripslashes($value);
					}
				} else {
					$newvalue = $value;
				}
				$fields[] = array(
					'name' => $key,
					'value' => $newvalue,
				);
			}
			wp_send_json_success(array('fields' => $fields));
		} 

	}


	/**
	 * Delete row by ID
	 */
	function delete_form_row()
	{
		global $wpdb;
		$id = intval($_POST['id']);

		if (!$id) {
			exit;
		}
		// Check permissions
		if (!current_user_can('manage_options')) {
			exit;
		}

		// Check for nonce security      
		if (!wp_verify_nonce($_POST['nonce'], 'ajax-nonce')) {
			die();
		}
		try {
			// Prepared statement for security
			$wpdb->query(
				$wpdb->prepare(
					"DELETE FROM {$this->table_name} WHERE id = %d",
					$id
				)
			);

		} catch (Exception $e) {
			error_log("Error deleting entry: {$e->getMessage()}");		
		}

		exit;
	}


	/**
	 *  Update form values
	 */
	function update_form_values()
	{

		global $wpdb;
		// Retrieve the serialized form data from the AJAX request
		$form_data = sanitize_text_field($_POST['formData']);
		$id = intval($_POST['id']);

		// Parse the serialized form data
		parse_str(stripslashes($form_data), $fields);

		if (!$id) {
			exit;
		}

		// Check permissions
		if (!current_user_can('manage_options')) {
			exit;
		}

		// Check for nonce security      
		if (!wp_verify_nonce($_POST['nonceupdate'], 'nonceupdate')) {
			die('Busted!');
		}

		$status = $wpdb->update(
			$this->table_name,
			array('form_values' => serialize($fields)),
			array('id' => $id)
		);

		if ($status === false) {
			// An error occurred, send an error response
			$error_message = $wpdb->last_error;
			wp_send_json_error(array('message' => $error_message));
		} else {
			// Update was successful, send a success response
			wp_send_json_success(array('message' => 'Update successful!', 'fieldsfromupdate' => $fields));
		}

		

	}


	/**
	 * Save entry when a Divi form is submitted
	 */
	function add_new_post($processed_fields_values, $et_contact_error, $contact_form_info)
	{

		global $wpdb;
		
		if ($et_contact_error === true) {
			return;
		}

		// Serialize the array data
		$form_values = serialize($processed_fields_values);
		$page_id = get_the_ID();

		// page submitted on details
		$page_id = $page_id;
		$page_name = get_the_title($page_id);
		$page_url = get_permalink($page_id);
		$date_submitted = current_time('mysql');
		$read_status = false;
		$read_date = null;
		$contact_form_id = sanitize_text_field($contact_form_info['contact_form_id']);

		// Insert the serialized data into the database
		$wpdb->insert(
			$this->table_name,
			array(
				'form_values' => sanitize_text_field($form_values),
				'page_id' => $page_id,
				'page_name' => $page_name,
				'page_url' => $page_url,
				'date_submitted' => $date_submitted,
				'read_status' => $read_status,
				'read_date' => $read_date,
				'contact_form_id' => $contact_form_id
			),
			array(
				'%s',
				'%d',
				'%s',
				'%s',
				'%s',
				'%d',
				'%s',
				'%s' 
			)
		);
	}

	
	/**
	 * Register the stylesheets for the public-facing side of the site.
	 */
	public function enqueue_styles()
	{

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/adas-divi-public.css', array(), $this->version, 'all');
		
	}
	

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 */
	public function enqueue_scripts()
	{
		
		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/adas-divi-public.js', array('jquery'), $this->version, false);

	}

}