<?php


class Adas_Divi_Logs
{


    public function __construct()
    {

        //$this->formCount = 10;

        add_action('init', [&$this, 'init']);

    }

    public function init()
    {
        // Add Shortcodes
        add_shortcode('display_logs', [&$this, 'display_logs']);

    }



    function display_logs($atts)
    {
        //error_log('display_form_values_shortcode_table called');

        global $wpdb;
        ob_start();
        // Get all tables with the WordPress database prefix
        $tables = $wpdb->get_results("SHOW TABLES LIKE '{$wpdb->prefix}%'", ARRAY_N);

        // Display the table names in a user-friendly format
        echo "<h2>Prefix: {$wpdb->prefix} </h2>";
        echo "<h2> Table Names: </h2>";

        echo "<ul>";
        foreach ($tables as $table) {
            // Extract the table name from the array
            $table_name = $table[0];
            echo "<li>$table_name</li>";
        }
        echo "</ul>";

        ini_set('log_errors', 1);
        /* $error_log = WP_CONTENT_DIR . '/debug.log'; // Path to debug.log file

         if (file_exists($error_log)) {
             $errors = file_get_contents($error_log);
             $errors = preg_replace('/(warning)/i', '<span style="color:red;">$1</span>', $errors);
             return '<pre>' . $errors . '</pre>';
         } else {
             return 'No error log found.';
         }
 */
        return ob_get_clean();

    }





}