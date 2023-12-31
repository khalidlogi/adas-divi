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
        $this->formid = $this->retrieve_form_id();
        $this->items_per_page = get_option('items_per_page') ? get_option('items_per_page') : 10;
    }


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
     *
     * @param int|null $formid The form ID to filter by. If null, counts all items.
     *
     * @return int The number of items in the database table.
     */
    public function count_items($formid = null)
    {
        global $wpdb;
        
        if (empty($formid)) {
            // Select all rows.
            $query = "SELECT COUNT(DISTINCT id) FROM {$this->table_name}";
            $items_count = $wpdb->get_var($query);
        } else {
           // formid is an array
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
           
        } return $items_count;
    }


    /**
     * Function to retrieve form id from Database.
     *
     * @return mixed $divi_form_id.
     */
    function retrieve_form_id()
    {
        global $wpdb;
        $divi_formid = maybe_unserialize(get_option('divi_form_id_setting'));
       
        if (empty($divi_formid)) {
            // see if there is a form Ids in the database
            $sql = "SELECT DISTINCT contact_form_id FROM {$this->table_name}";
            $results = $wpdb->get_results($sql);

            if ($results === false) {
                // handle error
                return 0;
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
        error_log('divi_form_id' . print_r($divi_form_id, true));
        return $divi_form_id;

      
    }
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
        error_log('$first_date_query: ' . print_r($first_date_query, true));
        error_log('in ' . __FILE__ . ' on line ' . __LINE__);
        $last_date_query = $wpdb->get_var("SELECT MAX(date_submitted) FROM $this->table_name");
        $datecsv = "Initial Date: $first_date_query | Final Date: $last_date_query";
        return $datecsv;
    }


    public function retrieve_form_values_pdf($formid = '')
    {
        global $wpdb;
        $query = "SELECT * FROM {$this->table_name} ORDER BY date_submitted DESC";
        $results = $wpdb->get_results($query);

        if (!$results) {
            error_log("Database error: " . $wpdb->last_error);
        } else {
            foreach ($results as $result) {
                $date = $result->date_submitted;
                $serialized_data = $result->form_values;
                $form_id = $result->contact_form_id;
                $date = $result->date_submitted;
                $id = $result->id;

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

    public function retrieve_form_values($formid = '', $offset = '', $items_per = '', $LIMIT = '')
    {
        global $wpdb;
        if (!empty($formid)) {
            $formid = $formid;
        } else {
            $formid = $this->formid;
           
        }
        error_log('$formid: in retrieve_form_values ' . print_r($formid, true)); 
        error_log('in ' . __FILE__ . ' on line ' . __LINE__); 

        if (!empty($items_per)) {
            $items_per = $items_per;
        }
   
        $formids = explode(',', $formid); // split the form ids
        $formids = array_map('trim', $formids); // trim whitespace
        $formid_placeholders = implode(',', array_fill(0, count($formids), '%s')); // create placeholders for each id
        
        if( !empty($formid)){
            $results = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT DISTINCT id, form_values, contact_form_id, date_submitted FROM {$this->table_name} WHERE contact_form_id IN ($formid_placeholders) LIMIT   $offset,
                    $items_per",
                     $formids   // Argument unpacking handles all arguments
                )
            
        );}
        
        if (!$results) {
            error_log("Database error: " . $wpdb->last_error);
        } else {
            foreach ($results as $result) {
                $date = $result->date_submitted;
                $serialized_data = $result->form_values;
                $form_id = $result->contact_form_id;
                $date = $result->date_submitted;
                $id = $result->id;

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
     *  Function to retrieve and unserialize the form values from the database.
     *
     * @since 1.0.0
     */
    public function retrieve_form_values2()
    {
        global $wpdb;
        $form_values = array();

        // Retrieve the 'form_value' column from the database
        $results = $wpdb->get_results("SELECT id,contact_form_id, form_values FROM $this->table_name");
        if (!$results) {
            //error_log('get_results working KHdb class : ' . $wpdb->last_error);
        }

        foreach ($results as $result) {
            $serialized_data = $result->form_valuess;
            $form_id = $result->contact_form_id;
            $id = $result->id;

            // Unserialize the serialized form value
            $unserialized_data = unserialize($serialized_data);

            $form_values[] = array(
                'form_id' => $form_id,
                'data' => $unserialized_data,
                'id' => $id,


            );
        }

        return $form_values;
    }

    // Static method to get the instance of the class
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}

class_divi_KHdb::getInstance();
