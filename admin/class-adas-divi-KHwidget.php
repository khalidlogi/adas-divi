<?php

defined('ABSPATH') || exit;

class Adas_Divi_KHwidget
{
    public function __construct()
    {

        add_action('wp_dashboard_setup', array($this, 'register_adas_table_dashboard_widget'));
        // AJAX handler to update the option value
        add_action('wp_ajax_update_data_saving_option', array($this, 'update_data_saving_option'));

    }


    /**
     * Register widget
     */
    public function register_adas_table_dashboard_widget()
    {

        wp_add_dashboard_widget(
            'my_adas_table_dashboard_widget',
            'Adas Divi Add-on',
            array($this, 'adas_dashboard_widget_display')
        );

    }


    /**
     * Update data saving option value
     */
    public function update_data_saving_option()
    {

        if (current_user_can('manage_options')) {
            $value_data_ischecked = $_POST['value_data_ischecked'];
            update_option('Enable_data_saving_checkbox', $value_data_ischecked);
        }
        wp_die();

    }


    /**
     * Diplay informations
     */
    public function adas_dashboard_widget_display()
    {

        global $wpdb;
        $table_name = $wpdb->prefix . 'divi_table';
        $table_exists = $wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") === $table_name;
        if (!$table_exists) {
            $message = __("Adas database data does not exist. Please try reactivating the plugin", "adasdividb");
            $output = sprintf("<br><div>%s</div>", $message);           
            echo $output;        
        } else {

            ?>

            <label class="switch">
                <input <?php if (get_option('Enable_data_saving_checkbox') !== '1') {
                    echo 'checked';
                } ?> type="checkbox"
                    id="switch_button_data_saving">
                <span class="slider round"></span>
            </label><strong> Activate/Deactivate Data saving </strong>
            <br>

            <script>
                jQuery(document).ready(function ($) {

                    // Event listener for the switch button change
                    $('#switch_button_data_saving').change(function () {
                        UpdateDataOptionValue();
                    });


                    function UpdateDataOptionValue() {
                        var checkboxValue = $('#switch_button_data_saving').prop('checked') ? 'null' : '1';
                        $.ajax({
                            url: ajaxurl,
                            type: 'POST',
                            data: {
                                action: 'update_data_saving_option',
                                value_data_ischecked: checkboxValue
                            },
                            success: function (response) {
                                console.log('Data option value updated successfully.');
                            },
                            error: function (error) {
                                console.error('Error updating data option value.');
                            }
                        });
                    }
                });
            </script>

            <?php
            //Display dates and
            $this->get_form_counts_and_recent_dates();
            $this->adas_custom_widget_display();

        }

    }


    /**
     * Retrieve dates
     */
    public function get_form_counts_and_recent_dates()
    {
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'divi_table';

        // Get the form IDs and count the number of forms for each form ID
        $sql = "SELECT DISTINCT contact_form_id FROM {$table_name}";
        $results = $wpdb->get_results($sql);

        if (!empty($results)) {
            $form_counts = array();

            foreach ($results as $row) {
                $form_id = sanitize_text_field($row->contact_form_id);

                $sql = "SELECT COUNT(*) AS count FROM {$table_name} WHERE contact_form_id IN ('$form_id')";
                $result2 = $wpdb->get_results($sql);
                $row2 = $result2[0];

                $form_counts[$form_id] = $row2->count;
            }

            echo '<br>';
            // Print the number of forms for each form ID
            foreach ($form_counts as $form_id => $count) {
                $form_id_label = __('Form ID:', 'adasdividb');
                $number_of_forms_label = __('Number of forms:', 'adasdividb');
                
                printf("<strong>%s</strong> %s, <strong>%s</strong> %s<br>",
                    $form_id_label,
                    $form_id,
                    $number_of_forms_label,
                    $count
                );               
                }

            // Get the date of last submissions
            $last_submissions_label = __('Last three submissions', 'adasdividb');
            printf('<br><h3><strong>%s</strong></h3>', $last_submissions_label);            
            $result = class_divi_KHdb::getInstance()->get_last_three_dates();
            foreach ($result as $result) {
                echo esc_attr($result) . '<br>';
            }
        }

    }

    
    /**
     * Show last entry
     */
    public function adas_custom_widget_display()
    {

        global $wpdb;
        $table_name = $wpdb->prefix . 'divi_table';
        $query = "SELECT id, form_values, contact_form_id, date_submitted
        FROM {$table_name}
        ORDER BY id DESC
        LIMIT 1";

        $results = $wpdb->get_results($query);
        if (!$results) {

            if (class_divi_KHdb::getInstance()->is_table_empty() === true) {
                $message = __('Add entries to your form and try again.', 'adasdividb');
                $link_label = __('Settings DB', 'adasdividb');
                $link_url = esc_url(admin_url('admin.php?page=khdiviwplist.php'));
                
                echo sprintf(
                    '<br><div style="text-align: center; color: red;">%s <a style="text-align: center; color: black;" href="%s">%s</a></div>',
                    $message,
                    $link_url,
                    $link_label
                );
            }
        } else {
            foreach ($results as $result) {
                $date = $result->date_submitted;
                $serialized_data = sanitize_text_field($result->form_values);
                $form_id = sanitize_text_field($result->contact_form_id);
                $id = intval($result->id);

                // Unserialize the serialized form value
                $unserialized_data = unserialize($serialized_data);
                $form_values[] = array(
                    'contact_form_id' => $form_id,
                    'id' => $id,
                    'date' => $date,
                    'data' => $unserialized_data,
                    'fields' => $unserialized_data,
                );

                // Display the data
                $recent_record_label = __('The most recent record', 'adasdividb');
                printf('<br><h3><strong>%s</strong></h3>', $recent_record_label);

                foreach ($form_values as $form_value) {
                $id_label = __('ID:', 'adasdividb');
                $date_label = __('Date:', 'adasdividb');
                printf('<strong>%s</strong> %s<br>', $id_label, $form_value['id']);
                printf('<strong>%s</strong> %s<br>', $date_label, $form_value['date']);

                    // Access and display the unserialized data
                    foreach ($form_value['data'] as $key => $value) {
                        if (is_array($value)) {
                            if (array_key_exists('value', $value)) {
                                $value = sanitize_text_field($value['value']);
                            }
                        }

                        printf("<strong>%s</strong> : %s <br>", $key, $value);

                    }

                    echo "<br>";
                }
            }
        }

    }
}

new Adas_Divi_KHwidget();