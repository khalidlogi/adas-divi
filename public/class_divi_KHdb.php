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
        //$this->count_items();
    }

    /**
     *  Deletes a row from the database table based on the specified ID
     *
     * @return bool
     */
    public function delete_tabledb()
    {
        global $wpdb;

        $table_name = $wpdb->prefix . $this->table_name;

        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) {
            // The table exists, let's drop it
            $sql = "DROP TABLE $table_name;";

            if ($wpdb->query($sql) !== false) {
                // Table dropped successfully
                return true;
            } else {
                // Error occurred while dropping the table
                return false;
            }
        } else {
            // Table doesn't exist
            return false;
        }
    }

    public function is_wpforms_active()
    {
        /* Check if get_plugins() function exists. This is required on the front end
        if (!function_exists('get_plugins')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        if (
            is_plugin_active('wpforms-lite/wpforms.php') || is_plugin_active('wpforms/wpforms.php')
        ) {
            return true;
        } else {
            return false;
        }*/
    }


    /**
     * Create the table kh_wpfomdb2
     *
     * @return Array
     */
    public function create_tabledb()
    {
        /*
         global $wpdb;

         $charset_collate = $wpdb->get_charset_collate();

         $sql = "CREATE TABLE IF NOT EXISTS " . $this->table_name . " (
                 id bigint(20) NOT NULL AUTO_INCREMENT,
                 form_id INT(11) NOT NULL,
                 date_submitted DATETIME NOT NULL,
                 form_value LONGTEXT NOT NULL,
                 PRIMARY KEY (id)
             ) $charset_collate;";

         require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
         dbDelta($sql);
         */
    }

    function delete_data($id)
    {
        global $wpdb;
        // Delete the row with the specified form_id

        try {
            // Prepared statement for security
            $wpdb->prepare(
                "DELETE FROM {$this->table_name} WHERE id = %d",
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
            //error_log('$items_count: where $formid is empty ' . print_r($items_count, true));
            //error_log('in ' . __FILE__ . ' on line ' . __LINE__);
            // Return the count of items. 
        } else {

            // existing array handling code
            if (strpos($formid, ',') !== false) {
                //error_log('formid items: ' . print_r($formid, true));

                $formid = str_replace(' ', '', $formid);
                $formid = explode(',', $formid); // Split the string into an array of IDs
                //error_log('$formid items ssss: ' . print_r($formid, true));
                //error_log('in ' . __FILE__ . ' on line ' . __LINE__);

                $placeholders = array_fill(0, count($formid), '%d');
                $placeholders = implode(', ', $placeholders);
                //error_log('$placeholders: ' . print_r($placeholders, true));
                //error_log('in ' . __FILE__ . ' on line ' . __LINE__);
                $query = $wpdb->prepare(
                    "SELECT COUNT(DISTINCT id) FROM {$this->table_name} WHERE contact_form_id IN ($placeholders)",
                    $formid
                );
                $items_count = $wpdb->get_var($query);
                //error_log($wpdb->last_error);
                //error_log('empty($formid) ' . print_r($items_count, true));
                //error_log('in ' . __FILE__ . ' on line ' . __LINE__);
                // Return the count of items. 
            } else {
                $query = $wpdb->prepare(
                    "SELECT COUNT(DISTINCT id) FROM {$this->table_name} WHERE contact_form_id = %s",
                    $formid
                );
                $items_count = $wpdb->get_var($query);

            }
            return $items_count;

        }


        /*if (!empty($formid)) {

        }



        // Initialize the count to zero.
        $items_count = 0;

        // Set the form ID to the provided value.
        $this->formid = $formid;

        if ($formid === null) {
            // Select all rows.
            $query = "SELECT COUNT(DISTINCT id) FROM {$this->table_name}";
        } else {
            // If $formid is provided, select rows where form_id matches.
            if (strpos($formid, ',') !== false) {
                $query = $wpdb->prepare(
                    "SELECT COUNT(DISTINCT id) FROM {$this->table_name} WHERE contact_form_id IN ($placeholders)",
                    $formid
                );
            } else {
                $query = $wpdb->prepare(
                    "SELECT COUNT(DISTINCT id) FROM {$this->table_name} WHERE contact_form_id = $formid ");
            }
        }

        // Retrieve the count from the database.
        $items_count = $wpdb->get_var($query);
        // Return the count of items. 
        return $items_count;*/
        //return $formid;

    }


    /**
     * Function to retrieve form id from Database.
     *
     * @return mixed True if the table is empty, false if it has data.
     */
    function retrieve_form_id()
    {
        global $wpdb;
        $divi_form_id = maybe_unserialize(get_option('divi_form_id_setting'));

        if (is_array($divi_form_id)) {

            if (count($divi_form_id) === 1) {

                $divi_form_id = $divi_form_id[0];
            } else {
                $divi_form_id = implode(', ', $divi_form_id);
            }
            return $divi_form_id;
        }

        error_log('divi_form_id' . print_r($divi_form_id, true));
        echo ($divi_form_id);

        if (empty($divi_form_id)) {
            // see if there is a form Ids in the database
            // Get the form IDs
            $sql = "SELECT DISTINCT contact_form_id FROM {$this->table_name}";
            $results = $wpdb->get_results($sql);

            if (!empty($results)) {
                error_log('$results: dfdf ' . print_r($results, true));
                error_log('in ' . __FILE__ . ' on line ' . __LINE__);
                // Create an array to store the number of forms for each form ID
                $form_id = array();

                // Loop through the results and count the number of forms for each form ID
                foreach ($results as $row) {
                    $form_id[] = $row->contact_form_id;
                }
                $form_id = implode(' , ', $form_id);
                error_log('$form_id: implode ' . print_r($form_id, true));
                error_log('in ' . __FILE__ . ' on line ' . __LINE__);
                return $form_id;
            } else {
                return $divi_form_id;
            }

        }
    }
    /*if (is_array($divi_form_id)) {
        $form_ids = array();

        foreach ($divi_form_id as $value) {
            $form_ids[] = $value;

        }

        $divi_form_id = implode(' , ', $form_ids);
        return $divi_form_id;
    } else {
        return $divi_form_id;
    }*/




    /**
     * Function to retrieve last three dates.
     *
     * @return bool True if the table is empty, false if it has data.
     */
    public static function get_last_three_dates()
    {
        global $wpdb;
        $table = $wpdb->prefix . 'wpforms_db2';
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


    public function retrieve_form_values($formid = '', $offset = '', $items_per = '', $LIMIT = '')
    {
        global $wpdb;

        if (!empty($formid)) {
            $formid = $formid;
        } else {
            $formid = $this->formid;
        }

        if (!empty($items_per)) {
            $items_per = $items_per;
        } else {
            $items_per = (get_option('number_id_setting')) ?: '2';
        }

        /*
        //check if there is a limit
        if (!empty($LIMIT) && (!empty($formid))) {
            $results = $wpdb->prepare(

                "SELECT id, contact_form_id, form_values FROM $this->table_name WHERE contact_form_id = %s ORDER BY id DESC LIMIT %d",
                $formid,
                $LIMIT
            );
            //error_log('$results: 1' . print_r($results, true));
            //error_log('in ' . __FILE__ . ' on line ' . __LINE__);
        } else {
            if (empty($items_per)) {
                $results = $wpdb->get_results("SELECT id, contact_form_id, form_values FROM  $this->table_name ");
                //error_log('$results: empty($items_per) ' . print_r($results, true));
                //error_log('in ' . __FILE__ . ' on line ' . __LINE__);

            } else {
                if ($formid === null) {
                    $results = $wpdb->get_results("SELECT id, contact_form_id, form_values FROM  $this->table_name  ORDER BY id DESC ");
                    //error_log('$results: formid  null' . print_r($results, true));
                    //error_log('in ' . __FILE__ . ' on line ' . __LINE__);

                } else {
                    $results = $wpdb->get_results(

                        $wpdb->prepare(
                            "SELECT id, contact_form_id, form_values FROM {$this->table_name} WHERE contact_form_id IN (%s) ORDER BY id DESC LIMIT %d, %d",
                            $formid,
                           
                            $offset,
                         
                            $items_per
                           
                        )
                    );   //error_log('$offset: ' . print_r($offset, true)); 
                            //error_log('in ' . __FILE__ . ' on line ' . __LINE__); 
                     //error_log('$formid: ' . print_r($formid, true)); 
                            //error_log('in ' . __FILE__ . ' on line ' . __LINE__); 
                    //error_log('else results: ' . print_r($results, true));
                    //error_log('in ' . __FILE__ . ' on line ' . __LINE__);
                    //error_log('offser is working');
                }
            }
        }

        $results = $wpdb->get_results("SELECT * FROM  $this->table_name ");

        if ($results === false) {
            //error_log("SQL Error: " . $wpdb->last_error);
            return false;
        }

        $form_values = array();

        foreach ($results as $result) {
            $serialized_data = $result->form_values;
            $form_id = $result->contact_form_id;
            $date = $result->date_submitted;
            // //error_log('$form_id: ' . print_r($form_id, true));
            ////error_log('in ' . __FILE__ . ' on line ' . __LINE__);
            $id = $result->id;

            // Unserialize the serialized form value
            $unserialized_data = unserialize($serialized_data);
            ////error_log('form_value[data]' . print_r($unserialized_data, true));


            // Add the 'Comment or Message' value to the form_values array
            $form_values[] = array(
                'contact_form_id' => $form_id,
                'id' => $id,
                'date' => $date,
                'data' => $unserialized_data,
                'fields' => $unserialized_data,
            );

        }
*/

        // Run the query


        $results = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT DISTINCT id, form_values, contact_form_id,date_submitted FROM {$this->table_name} LIMIT %d, %d",
                $offset,
                $items_per
            )
        );

        // Print and inspect results
        //print_r($results);

        // Log any errors
        if (!$results) {
            //error_log("Database error: " . $wpdb->last_error);

        } else {
            foreach ($results as $result) {
                $date = $result->date_submitted;

                //print_r($result);
                //error_log('$result to test date: ' . print_r($result, true));
                //error_log('in ' . __FILE__ . ' on line ' . __LINE__);

                $serialized_data = $result->form_values;
                //error_log('$serialized_data: ' . print_r($serialized_data, true));
                //error_log('in ' . __FILE__ . ' on line ' . __LINE__);
                $form_id = $result->contact_form_id;
                $date = $result->date_submitted;
                //error_log('in ' . __FILE__ . ' on line ' . __LINE__);
                // //error_log('$form_id: ' . print_r($form_id, true));
                ////error_log('in ' . __FILE__ . ' on line ' . __LINE__);
                $id = $result->id;

                // Unserialize the serialized form value
                $unserialized_data = unserialize($serialized_data);
                ////error_log('form_value[data]' . print_r($unserialized_data, true));


                // Add the 'Comment or Message' value to the form_values array
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
    /* public function retrieve_form_values($formid = 'et_pb_contact_form_0', $offset = '', $items_per = '', $LIMIT = '')
     {

         // $wpdb->get_results("SELECT * FROM $this->table_name LIMIT $offset, $items_per_page");

         global $wpdb;


         if (!empty($formid)) {
             $formid = $formid;
         } else {
             $formid = $this->formid;
         }

         if (!empty($items_per)) {
             $items_per = $items_per;
         } else {
             $items_per = (get_option('number_id_setting')) ?: '10';
         }


         //check if there is a limit
         if (!empty($LIMIT) && (!empty($formid))) {
             //$results = $wpdb->get_results("SELECT id, form_id, form_value FROM  $this->table_name ORDER BY id DESC LIMIT $LIMIT");
             //$results = $wpdb->get_results("SELECT id, form_id, form_value FROM $this->table_name WHERE form_id = $formid ORDER BY id DESC LIMIT $LIMIT");
             $results = $wpdb->prepare(
                 "SELECT id, contact_form_id, form_values FROM $this->table_name WHERE contact_form_id = %s ORDER BY id DESC LIMIT %d",
                 $formid,
                 $LIMIT
             );
         } else {
             if (empty($items_per)) {
                 $results = $wpdb->get_results("SELECT id, contact_form_id, form_values FROM  $this->table_name ");
             } else {
                 if ($formid === null) {
                     $results = $wpdb->get_results("SELECT id, contact_form_id, form_values FROM  $this->table_name  ORDER BY id DESC ");
                 } else {
                     $results = $wpdb->get_results("SELECT id, contact_form_id, form_values FROM  $this->table_name  where form_id IN($formid) ORDER BY id DESC
                 LIMIT  $offset, $items_per");
                     //error_log('offser is working');
                 }
             }
         }


         //var_dump($results);
         if ($results === false) {
             //error_log("SQL Error: " . $wpdb->last_error);
             return false;
         }

         $form_values = array();

         foreach ($results as $result) {
             $serialized_data = $result->form_values;
             $form_id = $result->contact_form_id;
             $id = $result->id;

             // Unserialize the serialized form value
             $unserialized_data = unserialize($serialized_data);

             // Add the 'Comment or Message' value to the form_values array
             $form_values[] = array(
                 'form_id' => $form_id,
                 'id' => $id,
                 'data' => $unserialized_data,
                 'fields' => $unserialized_data,
             );

         }

         return $form_values;
     }*/


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