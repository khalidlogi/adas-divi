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
	protected $plugin_name;
	protected $version;
	

	/**
	 * Define the core functionality of the plugin.
	 */
	public function __construct()
	{

		if (defined('ADAS_Divi_VERSION')) {
			$this->version = ADAS_Divi_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'adas';
		$this->load_dependencies();
		$this->setup_constants();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	private function setup_constants()
	{

		// Plugin Folder Path.
		if (!defined('KHFORM_PATH')) {
			define('KHFORM_PATH', plugin_dir_path(__FILE__));
		}
	
	}


	/**
	 * Load the required dependencies for this plugin.
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
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-adas-divi-settings.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-adas-divi-KHwidget.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/adas-enqueue.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-adas-divi-public.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-adas-divi-shortcode.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/class_divi_KHdb.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/class_divi_KHPDF.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/class_divi_KHCSV.php';
		$this->loader = new Adas_Divi_Loader();

	}

	
	/**
	 * Define the locale for this plugin for internationalization.
	 */
	private function set_locale()
	{

		$plugin_i18n = new Adas_Divi_i18n();
		$plugin_i18n->set_domain( $this->get_plugin_name() );

		$this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');

	}


	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 */
	private function define_admin_hooks()
	{

		$plugin_admin = new Adas_Divi_Admin($this->get_plugin_name(), $this->get_version());
		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');

	}


	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 */
	private function define_public_hooks()
	{

		$plugin_public = new Adas_Divi_Public($this->get_plugin_name(), $this->get_version());

		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');

		// Activate if enabled
		$isdataenabled = get_option('Enable_data_saving_checkbox');

		if ($isdataenabled !== '1') {
			$this->loader->add_action('et_pb_contact_form_submit', $plugin_public, 'add_new_post', 10, 3);
		}

		$this->loader->add_action('wp_ajax_get_form_values', $plugin_public, 'get_form_values');
		$this->loader->add_action('wp_ajax_nopriv_get_form_values', $plugin_public, 'get_form_values');

		$this->loader->add_action('wp_ajax_update_form_values', $plugin_public, 'update_form_values');
		$this->loader->add_action('wp_ajax_nopriv_update_form_values', $plugin_public, 'update_form_values');

		$this->loader->add_action('wp_ajax_delete_form_row', $plugin_public, 'delete_form_row');
		$this->loader->add_action('wp_ajax_nopriv_delete_form_row', $plugin_public, 'delete_form_row');

		new Adas_Divi_Shortcode();


	}


	
	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 */
	public function run()
	{
		$this->loader->run();
	}


	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 */
	public function get_plugin_name()
	{
		return $this->plugin_name;
	}


	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 */
	public function get_loader()
	{
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 */
	public function get_version()
	{
		return $this->version;
	}

}