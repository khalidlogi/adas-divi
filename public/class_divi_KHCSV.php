<?php


if (!class_exists('class_divi_KHCSV')) {

    class class_divi_KHCSV
    {
        private $myselectedformid;
        public function __construct()
        {

            $this->myselectedformid = sanitize_text_field(class_divi_KHdb::getInstance()->retrieve_form_id());
            if (class_divi_KHdb::getInstance()->is_table_empty() !== true) {
            add_action('wp_ajax_export_form_data', array($this, 'export_form_data'));
            add_action('wp_ajax_nopriv_export_form_data', array($this, 'export_form_data'));
            }

        }

        
        /**
         * Callback function for CSV export
         */
        public function export_form_data()
        {

            global $wpdb;
            $this->myselectedformid = class_divi_KHdb::getInstance()->retrieve_form_id();
            
            // Retrieve the form values from the database
            $form_values = class_divi_KHdb::getInstance()->retrieve_form_values_pdf($this->myselectedformid);
            
            if (empty($form_values)) {
                wp_die();
            }

            printf(
                "%s, %s, Field, Value\n",
                __('ID', 'adasdividb'),
                __('Form ID', 'adasdividb')
            );

            if($form_values){                
                foreach ($form_values as $form_value) {
                $form_id = sanitize_text_field($form_value['contact_form_id']);
                $id = intval($form_value['id']);
                //$date = $form_value['date'];

                foreach ($form_value['data'] as $key => $value) {
                    $id = $form_value['id'];
                    if (is_array($value)) {
                        if (array_key_exists('value', $value)) {
                            $value = $value['value'];
                        } else {
                            $value = $value;
                        }
                    }

                    if (is_array($value) && array_key_exists('value', $value)) {
                        $value = str_replace(',', '\,', $value);
                        $value = str_replace('"', '\"', $value);
                    }

                    // Add row to CSV table
                    $csv_table .= sprintf("%d, %s, \"%s\", \"%s\"\n", intval($id), esc_html($form_id), esc_html($key), esc_html($value));                }
            }
        }

            // Set the response headers for downloading
            header('Content-Type:text/csv');
            header('Content-Disposition: attachment; filename="DIVI-Contact-Entries-' . date('Y-m-d') . '.csv"');

            // Output the CSV table
            echo $csv_table;

            wp_die(); 
        }
    }
}

new class_divi_KHCSV();