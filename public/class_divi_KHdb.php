<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
class class_divi_KHdb
{
    protected $table_name;
    private $formid;
    private static $instance;
    public function __construct()
    {

        global $wpdb;
        $this->table_name = $wpdb->prefix . 'divi_table';

        if ($this->is_table_empty() != false) {
            $this->formid = $this->retrieve_form_id();
        }

        $this->items_per_page = get_option('items_per_page') ? get_option('items_per_page') : 10;

    }


    /**
     * Check if the Divi Theme is active.
     */
    public function is_divi_active()
    {
        //See if the Divi Theme is active
        if ('Divi' === wp_get_theme()->get('Name')) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * Count the number of items in the database table.
     */
    public function count_items($formid = null)
    {

        global $wpdb;
        // Check if table exists
        $table_name = $wpdb->prefix . 'divi_table';
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
            // Table does not exist, exit the function
            return;
        }
        if (empty($formid)) {
            // Select all rows.
            $query = "SELECT COUNT(DISTINCT id) FROM {$this->table_name}";
            $items_count = $wpdb->get_var($query);
        } else {
            // Formid is an array
            if (strpos($formid, ',') !== false) {
                $formid = str_replace(' ', '', $formid);
                $formid = explode(',', $formid); // Split the string into an array of ID

                $placeholders = array_fill(0, count($formid), '%d');
                $placeholders = implode(', ', $placeholders);

                $query = $wpdb->prepare(
                    "SELECT COUNT(DISTINCT id) FROM {$this->table_name} WHERE contact_form_id IN ($placeholders)",
                    $formid
                );
                $items_count = $wpdb->get_var($query);
            } else {
                $query = $wpdb->prepare(
                    "SELECT COUNT(DISTINCT id) FROM {$this->table_name} WHERE contact_form_id = %s",
                    $formid
                );
                $items_count = $wpdb->get_var($query);
            }

        }
        return $items_count;
    }


    /**
     * Function to retrieve form id from Database.
     */
    function retrieve_form_id()
    {

        global $wpdb;
        // Check if the table exists
        $table_name = $wpdb->prefix . 'divi_table';
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
            // Table does not exist, exit the function
            return;
        }

        if ($this->is_table_empty() === true) {
            $divi_form_id = 0;
        }

        $divi_formid = maybe_unserialize(get_option('divi_form_id_setting'));

        if (empty($divi_formid)) {
            // Check if there is a form Ids in the database
            $sql = "SELECT DISTINCT contact_form_id FROM {$this->table_name}";
            $results = $wpdb->get_results($sql);

            if ($results === false) {
                $divi_form_id = null;
                exit();
            }
            if (!empty($results)) {
                // Create an array to store the number of forms for each form ID
                $form_id = array();
                foreach ($results as $row) {
                    $form_id[] = $row->contact_form_id;
                }
                $divi_form_id = implode(' , ', $form_id);
            }

        } else if (is_array($divi_formid)) {
            if (count($divi_formid) === 1) {
                $divi_form_id = $divi_formid[0];
            } else {
                $divi_form_id = implode(', ', $divi_formid);
            }
        }

        return $divi_form_id;

    }


     /**
     * Function to get last three dates.
     */
    public static function get_last_three_dates()
    {
        global $wpdb;
        $table = $wpdb->prefix . 'divi_table';
        $query = "SELECT DISTINCT date_submitted FROM {$table} ORDER BY date_submitted DESC LIMIT 3";
        $results = $wpdb->get_results($query);

        $dates = array();
        foreach ($results as $result) {
            $dates[] = $result->date_submitted;
        }
        return $dates;
    }


    /**
     * Function to check if there is no data in a database table.
     *
     * @return bool True if the table is empty, false if it has data.
     */
    function is_table_empty()
    {

        global $wpdb;

        // Check if the table exists
        if ($wpdb->get_var("SHOW TABLES LIKE '$this->table_name'") != $this->table_name) {
            // Table does not exist, exit the function
            return;
        }
        $count = $wpdb->get_var("SELECT COUNT(*) FROM $this->table_name");
        if ($count === '0') {
            return true; // Table is empty

        } else {
            return false; // Table has data
        }
    }


    /**
     * Get the first and last date from the database.
     */
    function getDate()
    {

        global $wpdb;
        $first_date_query = $wpdb->get_var("SELECT MIN(date_submitted) FROM $this->table_name");
        $last_date_query = $wpdb->get_var("SELECT MAX(date_submitted) FROM $this->table_name");
        $datecsv = "Initial Date: $first_date_query | Final Date: $last_date_query";
        return esc_html($datecsv);

    }


     /**
     * Function to retrieve form values for pdf export.
     */
    public function retrieve_form_values_pdf($formid = '')
    {

        global $wpdb;
        $query = "SELECT * FROM {$this->table_name} ORDER BY date_submitted DESC";
        $results = $wpdb->get_results($query);

        if (!$results) {
            error_log("Database error: ");
        } else {
            foreach ($results as $result) {
                $date = sanitize_text_field($result->date_submitted);
                $serialized_data = sanitize_text_field($result->form_values);
                $form_id = sanitize_text_field($result->contact_form_id);
                $id = absint($result->id);

                // Unserialize the serialized form value
                $unserialized_data = unserialize($serialized_data);
                $form_values[] = array(
                    'contact_form_id' => $form_id,
                    'id' => $id,
                    'date' => $date,
                    'data' => $unserialized_data,
                    'fields' => $unserialized_data,
                );
            }
            return $form_values;
        }
        
    }


     /**
     * Function to retrieve all data from the custom table.
     */
    public function retrieve_form_values($formid = '', $offset = '', $items_per = '', $LIMIT = '')
    {

        global $wpdb;
        // Check if the table exists
        if ($wpdb->get_var("SHOW TABLES LIKE '$this->table_name'") != $this->table_name) {
            // Table does not exist, exit the function
            return;
        }

        if (!empty($formid)) {
            $formid = $formid;
        } else {
            $formid = $this->formid;
        }

        if (!empty($items_per)) {
            $items_per = $items_per;
        }

        if ($formid !== null) {
            $formids = explode(',', $formid); 
        }

        $formids = array_map('trim', $formids); 
        $formid_placeholders = implode(',', array_fill(0, count($formids), '%s')); // create placeholders for each id

        if (!empty($formid)) {
            $results = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT DISTINCT id, form_values, contact_form_id, date_submitted FROM {$this->table_name} 
                     WHERE contact_form_id IN ($formid_placeholders) LIMIT   $offset,
                    $items_per",
                    $formids 
                )

            );
        }

        if (!$results) {
            error_log("Database error: ");
        } else {
            foreach ($results as $result) {
                $date = sanitize_text_field($result->date_submitted);
                $serialized_data = sanitize_text_field($result->form_values);
                $form_id = sanitize_text_field($result->contact_form_id);
                $date = $result->date_submitted;
                $id = absint($result->id);

                // Unserialize the serialized form value
                $unserialized_data = unserialize($serialized_data);
                $form_values[] = array(
                    'contact_form_id' => $form_id,
                    'id' => $id,
                    'date' => $date,
                    'data' => $unserialized_data,
                    'fields' => $unserialized_data,
                );

            }
            return $form_values;
        }

    }


     /**
     * Static method to get the instance of the class
     */
    public static function getInstance()
    {

        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;

    }
}

class_divi_KHdb::getInstance();
