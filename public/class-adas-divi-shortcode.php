<?php


class Adas_Divi_Shortcode
{

    private $mydb;
    private $view_options;
    private $mysetts;
    private $table_name;

    private $mylink;
    private $text_color;
    private $label_color;
    private $bgcolor;
    private $formbyid;
    private $exportbgcolor;
    private $items_per_page;

    private $formCount;


    public function __construct()
    {

        global $wpdb;
        $this->table_name = $wpdb->prefix . 'divi_table';
        $this->view_options = get_option('view_option') ?: 'normal';
        $this->label_color = get_option('khdivi_label_color') ?: '#bfa1a1';
        $this->text_color = get_option('khdivi_text_color');
        $this->bgcolor = get_option('khdivi_bg_color') ?: '#408c4f';
        $this->exportbgcolor = get_option('khdivi_exportbg_color') ?: '#408c4f';
        $this->isnotif = get_option('Enable_notification_checkbox') ?: '0';
        //$this->items_per_page = get_option('number_id_setting') ?: '2';
        $this->items_per_page = 2;

        $this->formbyid = class_divi_KHdb::getInstance()->retrieve_form_id();
        error_log('$this->formbyid: ' . ($this->formbyid));
        error_log('in ' . __FILE__ . ' on line ' . __LINE__);

        $this->formCount = class_divi_KHdb::getInstance()->count_items($this->formbyid);
        //$this->formCount = 10;

        add_action('init', [&$this, 'init']);

    }

    public function init()
    {
        // Add Shortcodes
        add_shortcode('divi_data', [&$this, 'display_form_values_shortcode_table']);

    }

    public function dividata($atts)
    {

        ob_start();
        echo 'sssssssssssssss';
        return ob_get_clean();

    }

    function generate_pagination($formCount, $items_per_page)
    {


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
            echo '<span style="color:' . $this->text_color . ';" class="value">' . esc_html($value) . '</span>';
        }

    }
    function display_form_values_shortcode_table($atts)
    {
        error_log('display_form_values_shortcode_table called');

        global $wpdb;
        $is_wpforms_active = true;

        $atts = shortcode_atts(
            array(
                'id' => '',
            ),
            $atts
        );

        $current_page = max(1, get_query_var('paged'));
        $offset = ($current_page - 1) * $this->items_per_page;
        //$totalCount = KHdb::getInstance()->count_items();
        if ($this->formCount != 0 && $this->items_per_page != 0) {

            $total_pages = ceil($this->formCount / $this->items_per_page);
        }

        // see if user do not have authorization 
        if (!current_user_can('manage_options')) {
            // Assuming you have a link that takes users to the login page, you can add the referer URL as a query parameter.

            ob_start();

            echo '<div style="text-align: center; color: red;">You are not authorized to access this page. <a href="' . wp_login_url(add_query_arg('redirect', 'wpfurl')) . '">Login</a></div>';                //echo 'login: ' . wp_login_url();

            return ob_get_clean();

        } else {

            //get the form id
            if (!empty($atts['id'])) {
                $formbyid = $atts['id'];
            } else {
                $formbyid = $this->formbyid;
                error_log('formbyid' . $formbyid);

            }

            //error_log('display the changed form id'.$formbyid);
            // retrieve form values
            //$form_values = KHdb::getInstance()->retrieve_form_values($formbyid, $offset);
            $form_values = class_divi_KHdb::getInstance()->retrieve_form_values($this->formbyid, $offset, $this->items_per_page, '');



            //Check if there is at least one entry
            if (class_divi_KHdb::getInstance()->is_table_empty() === true) {
                ob_start();

                echo '<div style="text-align: center; color: red;">No data available! Please add entries to your form and try again.';
                echo ' <a style="text-align: center; color: black;" href="' . admin_url('admin.php?page=khwplist.php') . '">Settings
                DB</a></div>';

                return ob_get_clean();

            } else {
                ob_start();

                //include edit-form file
                include_once KHFORM_PATH . '../Inc/html/edit_popup.php';
                echo '<br>
                <div class="form-wraper">';
                if (!$is_wpforms_active) {
                    echo '<div style="color:red;"><i class="fas fa-exclamation-circle"></i> Wpforms is not ACTIVE</div>';
                }
                echo '
                    Visit the <a href="' . admin_url('admin.php?page=khwplist.php') .
                    '"> settings page </a> to update the form ID value.';

                if ($form_values) {
                    echo '<div class="khcontainer">';
                    echo 'Number of forms submitted: ' . class_divi_KHdb::getInstance()->count_items($formbyid);
                    if (!empty($formbyid)) {
                        echo '<br> Default form id: ' . (($formbyid === '1') ? 'Show all forms' : $formbyid);
                    }
                }
                // Start table
                echo '<div class="form-data-container">';
                echo '<table style="border: 1px solid black;">';

                // Table header
                echo '<tr>';
                echo '<th>ID</th>';
                echo '<th>Form ID</th>';
                echo '<th>Data</th>';
                echo '</tr>';

                foreach ($form_values as $form_value) {
                    error_log('$form_values data: ' . print_r($form_value['data'], true));
                    error_log('in ' . __FILE__ . ' on line ' . __LINE__);

                    $form_id = ($form_value['contact_form_id']);
                    $id = intval($form_value['id']);
                    $date = $form_value['date'];

                    // Table row
                    echo '<tr style="border: .5px solid black;" >';
                    echo '<td style="border: .5px solid black;  padding: 10px; text-align: center;">' . $id . '</td>';
                    echo '<td style="border: 1px solid black;  padding: 10px; text-align: center;">' . $form_id . '</td>';
                    echo '<td width="80%" style="border: 1px solid black;">';

                    echo '<div>Date : "' . $date . '" </div>';


                    // Table data
                    foreach ($form_value['data'] as $key => $value) {


                        $message = "Variable \$value: Type: " . gettype($value) . ", Value: " . var_export($value, true);

                        error_log($message);
                        if (empty($value)) {
                            continue;
                        }

                        echo '<div>';
                        echo '<span>' . $key . ': </span>';




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
                    //<button class="delete-btn" data-form-id="' . esc_attr($id) . '"
                    //data-nonce="' . wp_create_nonce('ajax-nonce') . '">
                    //<i class="fas fa-trash"></i></button>
                    echo '<button class="editbtn" 
                    data-form-id="' . esc_attr($form_id) . '" data-id="' . esc_attr($id) . '"><i
                    class="fas fa-edit"></i></button>';
                    echo '</div>';


                    echo '</td>';
                    echo '</tr>';
                }

                // End table
                echo '</table>';

                echo '<button id="tag-user-button">Tag User</button>';
                echo '<div id="tag-emails"> </div>';

                echo '<textarea id="email-textarea" rows="4" cols="50" style="display: none;"></textarea>';
                echo '<button id="send-email-button" style="display: none;">Send Email</button>';

                echo '<div class="pagination-links">';


                //if ($this->formCount != 0 && $this->items_per_page != 0) {
                //$this->generate_pagination($this->formCount, $this->items_per_page);
                //}


                $current_page = max(1, get_query_var('paged'));
                $total_pages = ceil($this->formCount / $this->items_per_page);

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

                echo '<button style="background:' . $this->exportbgcolor . ';" class="export-btn"><i class="fas fa-download"></i> Export as CSV</button>';
                echo '<button style="background:' . $this->exportbgcolor . ';" class="export-btn-pdf"><i class="fas fa-download"></i> Export as PDF</button>';
                echo '</div>';

                return ob_get_clean();
            }
        }
    }





}