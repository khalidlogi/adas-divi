<?php


defined('ABSPATH') || exit;

class Adas_Divi_KHwidget {
    private $mydb;
    public function __construct() {
        add_action('wp_dashboard_setup', array($this, 'register_divi_table_dashboard_widget'));
        // AJAX handler to update the option value
        add_action('wp_ajax_update_data_saving_option', array($this, 'update_data_saving_option'));
    }

    function register_divi_table_dashboard_widget() {
        wp_add_dashboard_widget(
            'my_divi_table_dashboard_widget',
            'Adas Divi Add-on',
            array($this, 'my_divi_table_dashboard_widget_display')
        );

    }
    function my_divi_table_dashboard_widget_display() {
        global $wpdb;
        $table_name = $wpdb->prefix.'divi_table';
        $table_exists = $wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") === $table_name;
        if(!$table_exists){
            echo ("<br><div>Adas database data does not exist. Pleach try reactivation the plugin</div>");
        }else{

        ?>

    <label class="switch">
        <input <?php if(get_option('Enable_data_saving_checkbox') !== '1') {
                    echo 'checked';} ?> type="checkbox" id="switch_button_data_saving">
        <span class="slider round"></span>
    </label><strong> Activate/Deactivate Data saving </strong>
    <br>

<script>
jQuery(document).ready(function($) {

    // Event listener for the switch button change
    $('#switch_button_data_saving').change(function() {
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
            success: function(response) {
                console.log('Data option value updated successfully.');
            },
            error: function(error) {
                console.error('Error updating data option value.');
            }
        });
    }
});
</script>

<?php
        // Get the form IDs
        $sql = "SELECT DISTINCT contact_form_id FROM {$table_name}";
        $results = $wpdb->get_results($sql);

        if(!empty($results)) {
            
            $form_counts = array();

            // Loop through the results and count the number of forms for each form ID
            foreach($results as $row) {
                $form_id = $row->contact_form_id;

                $sql = "SELECT COUNT(*) AS count FROM {$table_name} WHERE contact_form_id IN ('$form_id')";
                $result2 = $wpdb->get_results($sql);
                $row2 = $result2[0];

                $form_counts[$form_id] = $row2->count;
            }

            // Print the number of forms for each form ID
            foreach($form_counts as $form_id => $count) {
                echo "<strong>Form ID:</strong> $form_id,<strong> Number of forms:</strong> $count<br>";
            }
            echo '<br><strong>Recently Published</strong> <br>';

        }
        
        //get the date of last submissions
        $result = class_divi_KHdb::getInstance()->get_last_three_dates();
        foreach($result as $result) {
            echo $result.'<br>';
        }
        //Display last entry
        $this->my_first_custom_widget_display();

    }}

       function my_first_custom_widget_display() {
       
        global $wpdb;
        $table_name = $wpdb->prefix.'divi_table';
        $query = "SELECT id, form_values, contact_form_id, date_submitted
        FROM {$table_name}
        ORDER BY id DESC
        LIMIT 1"; 

        $results = $wpdb->get_results($query);
        if (!$results) {
        error_log("Database error: " . $wpdb->last_error);

        if (class_divi_KHdb::getInstance()->is_table_empty() === true) {
            echo '<div style="text-align: center; color: red;">Add entries to your form and try again.';
            echo ' <a style="text-align: center; color: black;" href="' . admin_url('admin.php?page=khdiviwplist.php') . '">Settings
            DB</a></div>';
        } 
        } else {
        foreach ($results as $result) {
        $date = $result->date_submitted;
        $serialized_data = $result->form_values;
        $form_id = $result->contact_form_id;
        $id = $result->id;

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
        foreach ($form_values as $form_value) {
        echo "<br><strong>Contact Form ID: </strong>" . $form_value['contact_form_id'] . "<br>";
        echo "<strong>ID:</strong> " . $form_value['id'] . "<br>";
        echo "<strong>Date:</strong> " . $form_value['date'] . "<br>";
        
        // Access and display the unserialized data
        foreach ($form_value['data'] as $key => $value) {
            if (is_array($value)) {
                if (array_key_exists('value', $value)) {
                    $value = $value['value'];
                }
            }

            echo "<strong>  $key </strong> :   $value  <br>";
        }
        
        echo "<br>";
    }
}
    }

    // update the data saving option value
    function update_data_saving_option() {
        if(current_user_can('manage_options')) {
            $new_value = $_POST['value_data_ischecked'];
            update_option('Enable_data_saving_checkbox', $new_value);
        }
        wp_die();
    }
   
}}
new Adas_Divi_KHwidget();