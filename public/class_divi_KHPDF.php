<?php

use Dompdf\Dompdf;

error_reporting(E_ALL);

if (!class_exists('class_divi_KHPDF')) {

    class class_divi_KHPDF
    {
        protected $myselectedformid;
        /**
         * Construct method
         */
        public function __construct()
        {
            $this->text_color = 'black';
            //Check if there is at least one entry
            if (class_divi_KHdb::getInstance()->is_table_empty() !== true) {
                add_action('wp_ajax_export_form_data_pdf', array($this, 'export_form_data_pdf'));
                add_action('wp_ajax_nopriv_export_form_data_pdf', array($this, 'export_form_data_pdf'));
            }

            require_once dirname(__DIR__) . '/vendor/autoload.php';

        }

        public function export_form_data_pdf()
        {
            global $wpdb;
            $this->myselectedformid = class_divi_KHdb::getInstance()->retrieve_form_id();

            try {
                $dompdf = new Dompdf();
                $formbyid = $this->myselectedformid;
                $form_values = class_divi_KHdb::getInstance()->retrieve_form_values_pdf($formbyid);

                foreach ($form_values as $form_value) {

                    $form_id = $form_value['contact_form_id'];
                    $data = $form_value['data'];
                    // Toggle the $isOddRow flag to alternate background colors
                    $isOddRow = !$isOddRow;
                    // Define the background color based on $isOddRow
                    $background_color = $isOddRow ? ' #f2f2f2' : 'white';

                    foreach ($data as $key => $value) {

                        if (empty($value)) {
                            continue;
                        }

                        // Generate the HTML table
                $html_table = ' ';
                $html_table .= '<td style="padding:10px; border-bottom:1px solid #ccc; color:blue;">' . $value . '</td>';
                $html_table .= '</tr>';
                    }
                }
                $html_table .= '</tbody></table>';
                $html_table .= '<table style="margin-bottom:1px; width:100%; border-collapse:collapse; border:1px solid #ccc; font-family: Arial, sans-serif; font-size: 14px;">';
                $html_table .= '<thead style=" background-color: #007acc;color: #fff;font-weight: bold;">

                <tr>
                <th >ID</th>
                <th >Form ID</th>
                <th >Field</th>
                <th >Value</th>
                </tr>
                </thead>';
                $html_table .= '<tbody>';

                $isOddRow = false; // Initialize as false
                foreach ($form_values as $form_value) {

                    $form_id = ($form_value['contact_form_id']);
                    $form_id = 'Form_ID_' . preg_replace('/\D/', '', $form_id);

                    $id = intval($form_value['id']);
                    $date = $form_value['date'];

                    // Toggle the $isOddRow flag to alternate background colors
                    $isOddRow = !$isOddRow;
                    // Define the CSS class for the row based on $isOddRow
                    $rowClass = $isOddRow ? 'odd-row' : '';
                    $background_color = $isOddRow ? ' #f2f2f2' : 'white';

                    foreach ($form_value['data'] as $key => $value) {
                        ////error_log(print_r($data, true));
                        $id = $form_value['id'];

                        if (is_array($value)) {
                            if (array_key_exists('value', $value)) {
                                $value = $value['value'];
                            }
                        }

                        $value = empty($value) ? "----" : $value;

                        $html_table .= '<tr style="background: ' . $background_color . '; border-bottom: 1px solid #ccc;">';
                        $html_table .= '<td style="padding:10px; border-bottom:1px solid #ccc; color:Charcoal;">' . $id . '</td>';
                        $html_table .= '<td style="padding:10px; border-bottom:1px solid #ccc; color:gray;">' . $form_id . '</td>';
                        $html_table .= '<td style="padding:10px; border-bottom:1px solid #ccc; ">' . $key . '</td>';

                        // Check if value is an email
                        if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
                            echo '';
                            $html_table .= '<td style="padding:10px; border-bottom:1px solid #ccc; color:blue;">
                            <a href="mailto:' . esc_attr($value) . '">' . esc_html($value) . '</a> </td>';

                        } else {
                            $html_table .= '<td style="padding:10px; border-bottom:1px solid #ccc; color:blue;">' . $value . '</td>';
                        }
                        $html_table .= '</tr>';
                    }
                }

                $html_table .= '</tbody></table>';
                $dompdf->loadHtml($html_table);
                $dompdf->setPaper('A4', 'landscape');
                // Render the HTML as PDF
                $dompdf->render();
                $dompdf->stream('filename.pdf', array("Attachment" => 0));
                wp_die(); //Terminate
           
            } catch (Exception $e) {
                // Handle exceptions.
                wp_die('Error: ' . $e->getMessage(), 'Error', ['response' => 500]);
            }
        }
    }
}

new class_divi_KHPDF();
