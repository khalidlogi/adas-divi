<?php

use Dompdf\Dompdf;

error_reporting(E_ALL);

if (!class_exists('class_divi_KHPDF')) {
    class class_divi_KHPDF
    {

        protected $myselectedformid;
        protected $mydb;

        /**
         * Construct method
         */
        public function __construct()
        {
            // $mydb = new KHdb();
            $this->text_color = 'black';
            add_action('wp_ajax_export_form_data_pdf', array($this, 'export_form_data_pdf'));
            add_action('wp_ajax_nopriv_export_form_data_pdf', array($this, 'export_form_data_pdf'));

            require_once dirname(__DIR__) . '/vendor/autoload.php';

        }

        function display_value($value, $key)
        {

            /* if (strtoupper($key) === 'ADMIN_NOTE') {
                 echo '<span class="value" style="color: red; font-weight:bold;">' . esc_html(strtoupper($value)) . '</span>';
             } elseif (filter_var($value, FILTER_VALIDATE_EMAIL)) {
                 echo '<a href="mailto:' . $value . '">' . $value . '</a>';
             } elseif (is_numeric($value)) {
                 echo '<a href="https://wa.me/' . $value . '">' . $value . '</a>';
             } else {
                 echo '<span style="color:' . $this->text_color . ';" class="value">' . esc_html($value) . '</span>';
             }*/

        }

        // call back function for data export as PDF using /Mpdf
        public function export_form_data_pdf()
        {
            global $wpdb;
            $this->myselectedformid = class_divi_KHdb::getInstance()->retrieve_form_id();

            try {
                $dompdf = new Dompdf();

                $formbyid = $this->myselectedformid;

                $form_values = class_divi_KHdb::getInstance()->retrieve_form_values_pdf($formbyid);
                error_log('$form_values: pdf' . print_r($form_values, true));
                error_log('in ' . __FILE__ . ' on line ' . __LINE__);

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



                        //} else {
                        $html_table .= '<td style="padding:10px; border-bottom:1px solid #ccc; color:blue;">' . $value . '</td>';
                        //}

                        $html_table .= '</tr>';
                    }
                }

                $html_table .= '</tbody></table>';
                // Create new TCPDF instance
                $dompdf = new Dompdf();



                // Generate the HTML table
                $html_table = ' ';
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
                // (Optional) Setup the paper size and orientation
                $dompdf->setPaper('A4', 'landscape');

                // Render the HTML as PDF
                $dompdf->render();

                // Output the generated PDF to Browser
                //$dompdf->stream();
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