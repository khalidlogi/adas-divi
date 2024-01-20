<?php 

use Dompdf\Dompdf;

if (!class_exists('class_divi_KHPDF')) {

    class class_divi_KHPDF
    {
        protected $myselectedformid;

        public function __construct()
        {
            $this->text_color = 'black';
            if (class_divi_KHdb::getInstance()->is_table_empty() !== true) {
                add_action('wp_ajax_export_form_data_pdf', array($this, 'export_form_data_pdf'));
                add_action('wp_ajax_nopriv_export_form_data_pdf', array($this, 'export_form_data_pdf'));
            }

            require_once dirname(__DIR__) . '/vendor/autoload.php';
        }


        /**
         * Display the formatted value based on the key
         */
        public function export_form_data_pdf()
        {

            global $wpdb;
            $this->myselectedformid = class_divi_KHdb::getInstance()->retrieve_form_id();

            try {
                $dompdf = new Dompdf();
                $formbyid = $this->myselectedformid;
                $form_values = class_divi_KHdb::getInstance()->retrieve_form_values_pdf($formbyid);

               $html_table = sprintf(
                '<table style="margin-bottom:1px; width:100%%; border-collapse:collapse; border:1px solid #ccc; font-family: Arial, sans-serif; font-size: 14px;">
                    <thead style="background-color: #007acc;color: #fff;font-weight: bold;">
                        <tr>
                            <th>ID</th>
                            <th>Form ID</th>
                            <th>Field</th>
                            <th>Value</th>
                        </tr>
                    </thead>
                    <tbody>'
);

                $isOddRow = false;
                if($form_values){
                foreach ($form_values as $form_value) {
                    $form_id = $form_value['contact_form_id'];
                    $form_id = 'Form_ID_' . preg_replace('/\D/', '', $form_id);
                    $id = intval($form_value['id']);
                    $date = $form_value['date'];

                    $isOddRow = !$isOddRow;
                    $rowClass = $isOddRow ? 'odd-row' : '';
                    $background_color = $isOddRow ? '#f2f2f2' : 'white';

                    foreach ($form_value['data'] as $key => $value) {
                        $id = $form_value['id'];

                        if (is_array($value)) {
                            if (array_key_exists('value', $value)) {
                                $value = $value['value'];
                            } else {
                                $value = $value;
                            }
                        }

                        $value = empty($value) ? "----" : esc_attr($value);
                        $html_table .= sprintf('<tr style="background: %s; border-bottom: 1px solid #ccc;">',$background_color);
                        $html_table .= sprintf('<td style="padding:10px; border-bottom:1px solid #ccc; color:Charcoal;">%d</td>', $id);
                        $html_table .= sprintf('<td style="padding:10px; border-bottom:1px solid #ccc; color:gray;">%s</td>', $form_id);
                        $html_table .= sprintf('<td style="padding:10px; border-bottom:1px solid #ccc;">%s</td>', $key);

                        if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
                            $html_table .= sprintf('<td style="padding:10px; border-bottom:1px solid #ccc; color:blue;"><a href="mailto:%s">%s</a></td>', esc_attr($value), esc_html($value));
                        } else {
                            $html_table .= sprintf('<td style="padding:10px; border-bottom:1px solid #ccc; color:blue;">%s</td>', esc_attr($value));
                        }

                        $html_table .= sprintf('</tr>');
                    }
                }}

                $html_table .= sprintf('</tbody></table>');
                $dompdf->loadHtml($html_table);
                $dompdf->setPaper('A4', 'landscape');
                $dompdf->render();
                $filename = 'mydocument_' . date('Y-m-d') . '.pdf'; // Set the filename with the current date
                $dompdf->stream($filename, array("Attachment" => 0));
                wp_die();
            } catch (Exception $e) {
                wp_die(sprintf('Error: %s', $e->getMessage()), 'Error', ['response' => 500]);
            }
        }
    }
}

new class_divi_KHPDF();
