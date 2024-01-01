<?php


class Adas_Divi_Shortcode
{
    private $table_name;
    private $formbyid;
    private $items_per_page;
    private $formCount;
    private $option;


    public function __construct()
    {

        global $wpdb;
        $this->table_name = 'divi_table';

        $options = [
            'khdivi_label_color' => '#bfa1a1',
            'khdivi_text_color' => null,
            'khdivi_exportbg_color' => '#408c4f',
            'khdivi_bg_color' => '#f8f7f7',
            'khdivi_exportbg_color' => '#408c4f',
            'items_per_page' => 10,
        ];

        foreach ($options as $option => $default) {
            $value = get_option($option, $default);
            $this->{$option} = $value;
        }

        //get the form id 
        $this->formbyid = class_divi_KHdb::getInstance()->retrieve_form_id();
        error_log('$this->formbyid: ' . print_r($this->formbyid, true)); 
        error_log('in ' . __FILE__ . ' on line ' . __LINE__); 

        // get the number of forms
        $this->formCount = class_divi_KHdb::getInstance()->count_items($this->formbyid);

        add_action('init', [&$this, 'init']);

    }

    public function init()
    {
        // Add Shortcodes
        add_shortcode('divi_data', [&$this, 'display_form_values_shortcode_table']);

    }


    function display_value($value, $key)
    {

        if (strtoupper($key) === 'ADMIN_NOTE') {
            echo '<span class="value" style="color: red; font-weight:bold;">' . esc_html(strtoupper($value)) . '</span>';
        } elseif (filter_var($value, FILTER_VALIDATE_EMAIL)) {
            echo '<a href="mailto:' . $value . '">' . $value . '</a>';
        } elseif (is_numeric($value)) {
            echo '<a href="https://wa.me/' . $value . '">' . $value . '</a>';
        } else {
            echo '<span style="color:' . $this->khdivi_text_color . ';" class="value">' . esc_html($value) . '</span>';
        }

    }
    function display_form_values_shortcode_table($atts)
    {

        $atts = shortcode_atts(
            array(
                'id' => '',
            ),
            $atts
        );
        global $wpdb;
        $is_divi_active = class_divi_KHdb::getInstance()->is_divi_active();
       
        
        // see if user do not have authorization 
        if (!current_user_can('manage_options')) {

            ob_start();
            echo '<div style="text-align: center; color: red;">You are not authorized to access this page. <a href="' . wp_login_url(add_query_arg('redirect', 'wpfurl')) . '">Login</a></div>';
            return ob_get_clean();

        } else {

            //get the form id
            if (!empty($atts['id'])) {
                $formbyid = $atts['id'];
            } else {
                $formbyid = $this->formbyid;
            }

            //Retrieve data from db
            //error_log('$form_values: from shortcoe' . print_r($form_values, true));
            //error_log('in ' . __FILE__ . ' on line ' . __LINE__);

            //Check if there is at least one entry
            if (class_divi_KHdb::getInstance()->is_table_empty() === true) {
                ob_start();
                echo '<div style="text-align: center; color: red;">No data available! Please add entries to your form and try again.';
                echo ' <a style="text-align: center; color: black;" href="' . admin_url('admin.php?page=khdiviwplist.php') . '">Settings
                DB</a></div>';
                return ob_get_clean();
            } else {
                $current_page = max(1, get_query_var('paged'));
                $offset = ($current_page - 1) * (int) $this->items_per_page;
                if ((int) $this->items_per_page !== 0) {
                    $total_pages = ceil($this->formCount / (int) $this->items_per_page);
                } 


                $form_values = class_divi_KHdb::getInstance()->retrieve_form_values($this->formbyid, $offset, $this->items_per_page, '');
                ob_start();
                //include edit-form file
                include_once KHFORM_PATH . '../Inc/html/edit_popup.php';
                echo '<br>
                <div class="form-wraper">';
                if (!$is_divi_active) {
                    echo '<div style="color:red;"><i class="fas fa-exclamation-circle"></i> Divi Theme is not ACTIVE</div>';
                }
                echo '
                    Visit the <a href="' . admin_url('options-general.php?page=khdiviwplist.php') .
                    '"> settings page </a> to update the form ID value.';

                if ($form_values) {
                    echo '<div class="khcontainer">';
                    echo 'Number of forms submitted: ' . class_divi_KHdb::getInstance()->count_items($formbyid) . '<br>';
                    echo 'Number of entries per page: ' . $this->items_per_page;

                    if (!empty($formbyid)) {
                        echo '<br> Default form id: <span style="color:blue;">' . (($formbyid === '1') ? 'Show all forms' : $formbyid) . '</span>';
                    }
                }
                // Start table
                echo '<div class="form-data-container">';
                echo '<table style="border: 1px solid black; background:' . $this->khdivi_bg_color . ';>';

                // Table header
                echo '<tr>';
                echo '<th>ID</th>';
                echo '<th>Form ID</th>';
                echo '<th>Data</th>';
                echo '</tr>';

                foreach ($form_values as $form_value) {

                    $form_id = ($form_value['contact_form_id']);
                    $id = intval($form_value['id']);
                    $date = $form_value['date'];

                    // Table row
                    echo '<tr style="border: .5px solid black;" >';
                    echo '<td style="border: .5px solid black;  padding: 10px; text-align: center;">' . $id . '</td>';
                    echo '<td style="border: 1px solid black;  padding: 10px; text-align: center;">' . $form_id . '</td>';
                    echo '<td width="80%" style="border: 1px solid black;">';
                    echo '<span style="color:' . $this->khdivi_label_color . ';">Date:</span> 
                    <span style="color:' . $this->khdivi_text_color . ';">' . $date . ' </span>';


                    // Table data
                    foreach ($form_value['data'] as $key => $value) {

                        if (empty($value)) {
                            continue;
                        }

                        echo '<div>';
                        echo '<span id="datakey" style="color:' . $this->khdivi_label_color . ';">' . $key . ': </span>';

                        if (is_array($value)) {
                            if (array_key_exists('value', $value)) {
                                $this->display_value($value['value'], $key);
                            } else {
                                foreach ($value as $val) {
                                    $this->display_value($val, $key);
                                }
                            }
                        } else {
                            $this->display_value($value, $key);
                        }

                        echo '</div>';
                    }

                    echo '<div class="delete-edit-wraper">';
                    echo '<button class="deletebtn" data-form-id="' . esc_attr($id) . '" data-nonce="' . wp_create_nonce('ajax-nonce') . '">
                    <i class="fas fa-trash"></i></button>';
                    echo '<button class="editbtn" 
                    data-form-id="' . esc_attr($form_id) . '" data-id="' . esc_attr($id) . '"><i
                    class="fas fa-edit"></i></button>';
                    echo '</div>';
                    echo '</td>';
                    echo '</tr>';
                }
                echo '</table>';
                echo '<div class="pagination-links">';
                echo paginate_links(
                    array(
                        'base' => esc_url(add_query_arg('paged', '%#%')),
                        'format' => '',
                        'prev_text' => __('&laquo; Previous'),
                        'next_text' => __('Next &raquo;'),
                        'total' => $total_pages,
                        'current' => $current_page,
                    )
                );
                echo '</div>';

                echo '<button style="background:' . $this->khdivi_exportbg_color . ';" class="export-btn"><i class="fas fa-download"></i> Export as CSV</button>';
                echo '<button style="background:' . $this->khdivi_exportbg_color . ';" class="export-btn-pdf"><i class="fas fa-download"></i> Export as PDF</button>';
                echo '</div>';

                return ob_get_clean();
            }
        }
    }

}