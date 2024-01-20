<?php

class Adas_Divi_Shortcode
{
    private $table_name;
    private $formbyid;
    private $items_per_page;
    private $formCount;

    public function __construct()
    {

        global $wpdb;
        $this->table_name = $wpdb->prefix . 'divi_table';
        // Get the form id
        $this->formbyid = sanitize_text_field(class_divi_KHdb::getInstance()->retrieve_form_id());
        // Get forms count 
        $this->formCount = sanitize_text_field(class_divi_KHdb::getInstance()->count_items($this->formbyid));

        $options = [
            'khdivi_label_color' => '#bfa1a1',
            'khdivi_text_color' => null,
            'khdivi_exportbg_color' => '#408c4f',
            'khdivi_bg_color' => '#f8f7f7',
            'items_per_page' => 10,
        ];

        foreach ($options as $option => $default) {
            $value = get_option($option, $default);
            $this->{$option} = esc_attr($value);
        }

        add_action('init', [ &$this, 'init']);

    }

    public function init()
    {

        add_shortcode('adas', [ &$this, 'display_form_values_shortcode_table']);

    }

    
    /**
     * Display the formatted value based on the key
     */
    public function display_value($value, $key)
    {

        if (strtoupper($key) === 'ADMIN_NOTE') {
            printf('<span class="value" style="color: red; font-weight:bold;"> %s</span>', esc_html(strtoupper($value)));
        } elseif (filter_var($value, FILTER_VALIDATE_EMAIL)) {
            printf('<a style="color:%s;" class="adaslink" href="mailto:%s"> %s</a>',$this->khdivi_text_color,esc_html($value),esc_html($value));
        } elseif (is_numeric($value)) {
            printf('<a style="color:%s;" class="adaslink" href="https://wa.me/%s"> %s</a>',$this->khdivi_text_color,esc_html($value),esc_html($value));        
        } else {
            printf('<span style="color:%s;" class="value"> %s</span>',$this->khdivi_text_color,esc_attr(stripslashes($value)));        
        }

    }


    /**
     * Display the form values as a shortcode table
     */
    public function display_form_values_shortcode_table($atts)
    {

        global $wpdb;
        $is_divi_active = class_divi_KHdb::getInstance()->is_divi_active();

        // Check if the table exists
        if ($wpdb->get_var("SHOW TABLES LIKE '$this->table_name'") != $this->table_name) {
            // Table does not exist, exit the function
            return;
        }

        $atts = shortcode_atts(
            array(
                'id' => '',
            ),
            $atts
        );

        if (!current_user_can('manage_options')) {
            ob_start();
            //Show nothing
            echo '';
            return ob_get_clean();

        } else {
           // Get  form id
            if (!empty($atts['id'])) {
                $formbyid = $atts['id'];
            } else {
                $formbyid = $this->formbyid;
            }

            // Check if there is at least one entry
            if (class_divi_KHdb::getInstance()->is_table_empty() === true) {
                ob_start();
                $message = __('No data available! Please add entries to your form and try again.', 'adasdividb');
                $link_text = __('Settings DB', 'adasdividb');
                $link_url = esc_url(admin_url('admin.php?page=khdiviwplist.php'));
                
                $output = sprintf(
                    '<div style="text-align: center; color: red;">%s <a style="text-align: center; color: black;" href="%s">%s</a></div>',
                    esc_html($message),
                    $link_url,
                    esc_html($link_text)
                );               
                echo $output;              
                $output_buffer = ob_get_clean();
                return $output_buffer;
            } 
            else {
                $current_page = max(1, get_query_var('paged'));
                $offset = ($current_page - 1) * (int) $this->items_per_page;
                if ((int) $this->items_per_page !== 0) {
                    $total_pages = ceil($this->formCount / (int) $this->items_per_page);
                }

                $form_values = class_divi_KHdb::getInstance()->retrieve_form_values($this->formbyid, $offset, $this->items_per_page, '');
                ob_start();
                // Include edit-form file
                include_once KHFORM_PATH . '../Inc/html/edit_popup.php';
                echo '
                <div class="form-wraper">';
                if (!$is_divi_active) {
                    $message = __('Divi Theme is not ACTIVE', 'adasdividb');
                    printf(
                        '<div style="color:red;"><i class="fas fa-exclamation-circle"></i> %s</div>',
                        esc_html($message)
                    );
                }

                $update_form_id = __('To update the form ID value', 'adasdividb');
                $settings_page = __('settings page', 'adasdividb');
                $visit = __('Visit the', 'adasdividb');
                $link_url = esc_url(admin_url('admin.php?page=khdiviwplist.php'));
                
                
                printf(
                    $visit . ' <a href="%s">' . $settings_page . '</a> ' . $update_form_id . '.<br>',
                    $link_url
                );
                    
                if ($form_values) {
                    $count = sanitize_text_field(class_divi_KHdb::getInstance()->count_items($formbyid));
                    printf('<p>' . __('Number of forms submitted:', 'adasdividb'). ' %s</p>', esc_html($count));                    
                    
                    echo '<div class="form-data-container">';

                    foreach ($form_values as $form_value) {
                        $form_id = sanitize_text_field($form_value['contact_form_id']);
                        $form_id = preg_replace('/\D/', '', $form_id);
                        $id = intval($form_value['id']);
                        $date = $form_value['date'];

                        //Delete button
                        echo '<div class="form-set-container" style="background:' . esc_attr($this->khdivi_bg_color) . ';"
                        data-id="' . esc_attr($id) . '">';
                        echo '<button class="delete-btn" data-form-id="' . esc_attr($id) . '"
                        data-nonce="' . wp_create_nonce('ajax-nonce') . '">
                        <i class="fas fa-trash"></i></button>';

                        //Edit button
                        echo '<button class="edit-btn delete-btn2" data-form-id="' . esc_attr($form_id) . '"
                        data-id="' . esc_attr($id) . '"><i class="fas fa-edit"></i></button>';

                        echo '<div class="form-id-container">';
                        
                        $id_label = __('ID', 'adasdividb');
                        $id_text = esc_html($id);
                        
                        printf(
                            '<div class="form-id-label id"><span style="color:%s;"> %s </span>: <span style="color:%s;"> %s </span></div>',
                            $this->khdivi_label_color,
                            $id_label,
                            $this->khdivi_text_color,
                            $id_text
                        );                    

                        // Form ID 
                        $form_id_label = __('Form ID:', 'adasdividb');
                        printf(
                            '<span style="color:%s;" class="form-id-label">%s</span>',
                            $this->khdivi_label_color,
                            $form_id_label
                        );                       
                        
                        printf(
                                '<span style="color:%s;" class="form-id-value">%s</span></div>',
                                $this->khdivi_text_color,
                                esc_html($form_id)
                            );    
                            
                        $date_label = __('Date:', 'adasdividb');
                        $date_text = esc_html($date);                       
                        printf(
                            '<div id="datakey" style="color:%s;"><span class="field-label"> %s</span><span style="color:%s;" class="value"> %s</span></div>',
                            $this->khdivi_label_color,
                            $date_label,
                            $this->khdivi_text_color,
                            $date_text
                        );                       
                        
                        // Key values data
                        foreach ($form_value['data'] as $key => $value) {

                            if (empty($value)) {
                                continue;
                            }

                            echo '<div class="form-data-container">';                            
                            $key_label = esc_html($key);
                            $output = sprintf(
                                '<span class="field-label" style="color:%s;">%s:</span>',
                                $this->khdivi_label_color,
                                $key_label
                            );
                            echo $output;

                            // Check is  key is an array
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

                        echo '</div>';
                    }
                }
                echo '<div class="pagination-links">';
                echo paginate_links(
                    array(
                        'base' => esc_url(add_query_arg('paged', '%#%')),
                        'format' => '',
                        'prev_text' => ('&laquo; Previous'),
                        'next_text' => ('Next; &raquo'),
                        'total' => $total_pages,
                        'current' => $current_page,
                    )
                );
                echo '</div>';

                printf(
                    '<div class="adassharebutton"><button style="background:%s;" class="export-btn">%s</button>',
                    $this->khdivi_exportbg_color,
                    __('Export as CSV', 'adasdividb')
                );
                printf(
                    '<button style="background:%s;" class="export-btn export-btn-pdf">%s</button></div>',
                    $this->khdivi_exportbg_color,
                    __('Export as PDF', 'adasdividb')
                );

                return ob_get_clean();

            }
        }
    }

}
