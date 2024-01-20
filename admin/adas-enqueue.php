<?php


defined('ABSPATH') || exit;

class EnqueueClass
{

    /**
     * Enqueue styles 
     */public function __construct()
    {

        add_action('wp_enqueue_scripts', array($this, 'enqueue_form_values_css'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_custom_script'));
        add_action('admin_enqueue_scripts', array($this, 'admin_styles'));

    }


    /**
     * Styles for Dashboard
     */
    function admin_styles()
    {

        wp_enqueue_style('admin_style', plugin_dir_url(__FILE__) . '../assets/css/admin.css');
        wp_enqueue_style('admin_style', plugin_dir_url(__FILE__) . '../assets/css/bootstrap.min.css');
        wp_enqueue_style('font-awesome', plugin_dir_url(__FILE__) . '../assets/css/font-awesome.css');

    }


    /**
     * Enqueue CSS styles for the form values.
     */
    function enqueue_form_values_css()
    {
        // Enqueue your custom CSS.
        wp_enqueue_style('font-awesome', plugin_dir_url(__FILE__) . '../assets/css/font-awesome.css');

        // Enqueue jQuery UI stylesheet (optional).
        wp_enqueue_style('jquery-ui-style', plugin_dir_url(__FILE__) . 'assets/css/jquery-ui.css');
    }


    /**
     * Enqueue custom JavaScript script.
     */
    function enqueue_custom_script()
    {
        
        // Enqueue your custom JavaScript.
        wp_enqueue_script('custom-script', plugin_dir_url(__FILE__) . '../assets/js/custom-script.js', array('jquery'), '1.0', true);

        // Localize that shit.
        wp_localize_script('custom-script', 'custom_vars', array('ajax_url' => admin_url('admin-ajax.php')));

        // Enqueue jQuery UI scripts (core and droppable) (optional).
        wp_enqueue_script('jquery-ui-core', plugin_dir_url(__FILE__) . '../assets/js/jquery-ui-core', array('jquery'), '1.0', true);
        wp_enqueue_script('jquery-ui-droppable');

    }

}

new EnqueueClass();