<?php

/**
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

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Retrieve and return form values
	 *
	 * @return  array $fields
	 *
	 */
	function get_form_values()
	{
		global $wpdb;
		$table_name = $wpdb->prefix . 'divi_table';



		$form_id = ($_POST['form_id']);
		$id = intval($_POST['id']);

		// Fetch form_value from the wpform_db2 table based on the form_id
		$query = $wpdb->prepare("SELECT id, form_values FROM $table_name WHERE id = %d", $id);
		$serialized_data = $wpdb->get_results($query);

		if ($wpdb->last_error) {
			wp_send_json_error('Error: ' . $wpdb->last_error);
		}

		if ($serialized_data) {
			// Unserialize the serialized form value
			$unserialized_data = unserialize($serialized_data[0]->form_values);
			$fields = array();

			foreach ($unserialized_data as $key => $value) {
				if (is_array($value)) {
					if (array_key_exists('value', $value)) {
						$newvalue = $value["value"];
					} else {
						$newvalue = $value;
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
		} else {
			wp_send_json_error('Form values not found for the given form_id.');
		}

	}

	function send_email($recipient = '', $message = '')
	{
		wp_send_json_success('Email sent successfully');
	}


	function delete_form_row()
	{
		global $wpdb;
		$table_name = $wpdb->prefix . 'divi_table';

		error_log('function delete_form_row() ');
		$id = intval($_POST['id']);

		error_log('function delete_form_row() is is ' . print_r($id, true));
		error_log('in ' . __FILE__ . ' on line ' . __LINE__);

		if (!$id) {
			wp_send_json_error('Invalid ID');
			exit;
		}
		// Check permissions
		if (!current_user_can('delete_posts')) {
			wp_send_json_error('Insufficient permissions');
			exit;
		}

		// Check for nonce security      
		if (!wp_verify_nonce($_POST['nonce'], 'ajax-nonce')) {
			wp_send_json_error('Busted');
			die('Busted!');
		}
		try {
			// Prepared statement for security
			$wpdb->query(
				$wpdb->prepare(
					"DELETE FROM {$table_name} WHERE id = %d",
					$id
				)
			);

			// Log success
			wp_send_json_success("Entry with ID $id deleted successfully");
		} catch (Exception $e) {
			// Log error
			error_log("Error deleting entry: {$e->getMessage()}");
			// Handle error gracefully, e.g., display user-friendly message
		}

		wp_send_json_success('ssssssssssssssssssssss ."' . $id . '"');

		exit;
	}
	/**
	 * delete form row by its id
	 */
	function delete_form_row2()
	{
		global $wpdb;
		$table_name = $wpdb->prefix . 'divi_table';


		$id = intval($_POST['id']);
		error_log('function delete_form_row() is is ' . print_r($id, true));
		error_log('in ' . __FILE__ . ' on line ' . __LINE__);

		if (!$id) {
			wp_send_json_error('Invalid ID');
			exit;
		}

		// Check permissions
		if (!current_user_can('delete_posts')) {
			wp_send_json_error('Insufficient permissions');
			exit;
		}

		// Check for nonce security      
		if (!wp_verify_nonce($_POST['nonce'], 'ajax-nonce')) {
			die('Busted!');
		}


		try {
			// Prepared statement for security
			$wpdb->prepare(
				"DELETE FROM {$table_name} WHERE id = %d",
				$id
			);

			// Execute deletion
			$wpdb->query();

			// Log success
			error_log("Entry with ID $id deleted successfully");
		} catch (Exception $e) {
			// Log error
			error_log("Error deleting entry: {$e->getMessage()}");
			// Handle error gracefully, e.g., display user-friendly message
		}



		if (!$wpdb->delete()) {
			wp_send_json_error('Error deleting');
			exit;

		}
		wp_send_json_success('deleted successfully');
		exit;

	}




	/**
	 *  Update form values
	 *
	 * @return void
	 */
	function update_form_values()
	{

		global $wpdb;
		$table_name = $wpdb->prefix . 'divi_table';

		// Retrieve the serialized form data from the AJAX request
		$form_data = sanitize_text_field($_POST['formData']);
		$form_id = ($_POST['contact_form_id']);
		$id = intval($_POST['id']);

		// Parse the serialized form data
		parse_str($form_data, $fields);
		////error_log('$fields222222: ' . print_r($fields, true));
		////error_log('in ' . __FILE__ . ' on line ' . __LINE__);


		if (!$id) {
			wp_send_json_error('Invalid ID');
			exit;
		}

		// Check permissions
		if (!current_user_can('delete_posts')) {
			wp_send_json_error('Insufficient permissions');
			exit;
		}

		// Check for nonce security      
		if (!wp_verify_nonce($_POST['nonceupdate'], 'nonceupdate')) {
			die('Busted!');
		}

		$status = $wpdb->update(
			$table_name,
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




	/** me:
	 * Adds a new post to the lwp_form_submission CPT when a Divi form is submitted
	 *
	 * @param array $processed_fields_values	Processed fields values
	 * @param array $et_contact_error	 		Whether there is an error on the form entry submit process or not
	 * @param array $contact_form_info	 		An array of post row actions.
	 */
	function add_new_post($processed_fields_values, $et_contact_error, $contact_form_info)
	{

		global $wpdb;
		$table_name = $wpdb->prefix . 'divi_table';

		if ($et_contact_error == true) {
			return;
		}
		////error_log('Processed Fields Values: ' . print_r($processed_fields_values, true));
		////error_log('ET Contact Error: ' . print_r($et_contact_error, true));
		////error_log('Contact Form Info: ' . print_r($contact_form_info, true));

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
			$table_name,
			array(
				'form_values' => $form_values,
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
				'%s' // Data format
			)
		);

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Adas_Divi_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Adas_Divi_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/adas-divi-public.css', array(), $this->version, 'all');
		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/adas-divi-public.css', array(), $this->version, 'all');
		//add_action('wp_enqueue_scripts', array($this, 'enqueue_form_values_css'));





	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function shortcoe_callback()
	{

		echo ' dddddd';
		wp_die();

	}


	/* AJAX callback function to fetch user names
																	  function tag_user_get_user_names()
																	  {
																		  $args = array('orderby' => 'display_name');
																		  $wp_user_query = new WP_User_Query($args);
																		  $authors = $wp_user_query->get_results();
																		  $user_emails = array();

																		  if (!empty($authors)) {

																			  foreach ($authors as $author) {
																				  $author_info = get_userdata($author->ID);
																				  $user_emails[] = $author_info->user_email;
																				  //error_log('$user_emails: ' . print_r($user_emails, true));
																				  //error_log('in ' . __FILE__ . ' on line ' . __LINE__);
																			  }
																			  //error_log('user_emails: ' . print_r($user_emails, true));
																			  //error_log('in ' . __FILE__ . ' on line ' . __LINE__);
																			  wp_send_json_success($user_emails);
																		  } else {
																			  wp_send_json_error('No results');
																		  }





																	  }*/


	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Adas_Divi_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Adas_Divi_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/adas-divi-public.js', array('jquery'), $this->version, false);
		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/tag-user-script.js', array('jquery'), $this->version, false);

		// Localize the script with custom variables for AJAX.
		//wp_localize_script('custom-script', 'custom_vars', array('ajax_url' => admin_url('admin-ajax.php')));

	}

}