<?php


if (!class_exists('class_divi_KHCSV')) {

    class class_divi_KHCSV
    {

        private $db;
        private $myselectedformid;
        public function __construct()
        {
            $this->myselectedformid = class_divi_KHdb::getInstance()->retrieve_form_id();

            // Add AJAX action hooks
            add_action('wp_ajax_export_form_data', array($this, 'export_form_data'));
            add_action('wp_ajax_nopriv_export_form_data', array($this, 'export_form_data'));

            //$this->export_form_data();
        }


        /**
         * Callback function for CSV export
         */
        public function export_form_data()
        {
            global $wpdb;

            global $wpdb;
            $this->myselectedformid = class_divi_KHdb::getInstance()->retrieve_form_id();

            $form_values = class_divi_KHdb::getInstance()->retrieve_form_values_pdf($this->myselectedformid);
            // Retrieve the form values from the database
            if (empty($form_values)) {
                wp_send_json_error('Error fetching data');
                wp_die();
            }

            // Call the getDate() method
            $datecsv = class_divi_KHdb::getInstance()->getDate();

            // Start building the CSV table
            //$csv_table = "Date: $datecsv\n";
            $csv_table = "ID, Form ID, Field, Value\n";

            foreach ($form_values as $form_value) {
                //error_log('$form_values data: ' . print_r($form_value['data'], true));
                //error_log('in ' . __FILE__ . ' on line ' . __LINE__);

                $form_id = ($form_value['contact_form_id']);
                $id = intval($form_value['id']);
                $date = $form_value['date'];

                foreach ($form_value['data'] as $key => $value) {
                    ////error_log(print_r($data, true));
                    $id = $form_value['id'];

                    if (is_array($value)) {
                        if (array_key_exists('value', $value)) {
                            $value = $value['value'];
                        }
                    }

                    if (is_array($value) && array_key_exists('value', $value)) {
                        $value = str_replace(',', '\,', $value);
                        $value = str_replace('"', '\"', $value);
                    }

                    // Add row to CSV table
                    $csv_table .= "$id, $form_id, \"$key\", \"$value\"\n";
                }
            }

            // Set the response headers for downloading
            header('Content-Type:text/csv');
            header('Content-Disposition: attachment; filename="WPForms-Data-Entries-' . date('Y-m-d') . '.csv"');

            // Output the CSV table
            echo $csv_table;

            wp_die(); // This is required to terminate immediately and return a proper response
        }
    }
}

new class_divi_KHCSV();