<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://web-pro.store
 * @since      1.0.0
 *
 * @package    Adas_Divi
 * @subpackage Adas_Divi/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Adas_Divi
 * @subpackage Adas_Divi/includes
 * @author     khalidlogi <KHALIDLOGI@GMAIL.COM>
 */
class Adas_Divi
{

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Adas_Divi_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct()
	{



		if (defined('ADAS_DIVI_VERSION')) {
			$this->version = ADAS_DIVI_VERSION;
		} else {
			$this->version = '1.0.0';
		}

		$this->plugin_name = 'adas-divi';

		$this->load_dependencies();
		$this->setup_constants();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}


	private function setup_constants()
	{

		// Plugin version.
		if (!defined('KHFORM_DOMAIN')) {
			define('KHFORM_DOMAIN', 'khwpformsdb');
		}
		// Plugin version.
		if (!defined('KHFORM_VERSION')) {
			define('KHFORM_VERSION', $this->version);
		}

		// Plugin Folder Path.
		if (!defined('KHFORM_PATH')) {
			define('KHFORM_PATH', plugin_dir_path(__FILE__));
		}

		/* Plugin Folder URL.
																																							  if (!defined('WPFORMS_PLUGIN_URL')) {
																																								  define('KHFORM_URL', plugin_dir_url(__FILE__));
																																							  }*/
	}


	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Adas_Divi_Loader. Orchestrates the hooks of the plugin.
	 * - Adas_Divi_i18n. Defines internationalization functionality.
	 * - Adas_Divi_Admin. Defines all hooks for the admin area.
	 * - Adas_Divi_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies()
	{

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-adas-divi-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-adas-divi-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-adas-divi-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-adas-divi-public.php';

		require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-adas-divi-shortcode.php';

		require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-adas-divi-logs.php';


		require_once plugin_dir_path(dirname(__FILE__)) . 'public/class_divi_KHdb.php';

		// Settings file
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-adas-divi-settings.php';

		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/adas-enqueue.php';

		require_once plugin_dir_path(dirname(__FILE__)) . 'public/class_divi_KHPDF.php';

		require_once plugin_dir_path(dirname(__FILE__)) . 'public/class_divi_KHCSV.php';



		$this->loader = new Adas_Divi_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Adas_Divi_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale()
	{

		$plugin_i18n = new Adas_Divi_i18n();

		$this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');

	}



	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks()
	{

		$plugin_admin = new Adas_Divi_Admin($this->get_plugin_name(), $this->get_version());

		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks()
	{

		$plugin_public = new Adas_Divi_Public($this->get_plugin_name(), $this->get_version());

		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');

		// Activate if enabled
		$isdataenabled = get_option('Enable_data_saving_checkbox');


		//if (!empty($isdataenabled)) {
		//}
		//if (has_action('et_pb_contact_form_submit')) {
		// The hook 'et_pb_contact_form_submit' exists
		$this->loader->add_action('et_pb_contact_form_submit', $plugin_public, 'add_new_post', 10, 3);
		error_log("The hook 'et_pb_contact_form_submit' exists.");
		//} else {
		// The hook 'et_pb_contact_form_submit' does not exist
		error_log("The hook 'et_pb_contact_form_submit' does not exist.");
		//}

		$this->loader->add_action('wp_ajax_get_form_values', $plugin_public, 'get_form_values');
		$this->loader->add_action('wp_ajax_nopriv_get_form_values', $plugin_public, 'get_form_values');

		$this->loader->add_action('wp_ajax_update_form_values', $plugin_public, 'update_form_values');
		$this->loader->add_action('wp_ajax_nopriv_update_form_values', $plugin_public, 'update_form_values');

		$this->loader->add_action('wp_ajax_delete_form_row', $plugin_public, 'delete_form_row');
		$this->loader->add_action('wp_ajax_nopriv_delete_form_row', $plugin_public, 'delete_form_row');


		$this->loader->add_action('wp_ajax_update_send_email', $plugin_public, 'send_email');
		$this->loader->add_action('wp_ajax_nopriv_send_email', $plugin_public, 'send_email');

		//Redirect to thank you page
		//$this->loader->add_action('template_redirect', $plugin_public, 'template_redirect');






		//tag useres
		// Enqueue JavaScript and CSS files

		//$this->loader->add_action('wp_ajax_tag_user_get_user_names', $plugin_public, 'tag_user_get_user_names');
		//$this->loader->add_action('wp_ajax_nopriv_tag_user_get_user_names', $plugin_public, 'tag_user_get_user_names');

		// Insitantiate shortcode Class
		new Adas_Divi_Shortcode();
		new Adas_Divi_Logs();


	}



	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run()
	{
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name()
	{
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Adas_Divi_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader()
	{
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version()
	{
		return $this->version;
	}

}