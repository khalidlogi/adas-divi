<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}


/**
 * ClioWP Settings Page plugin main class
 *
 */
class Adas_Divi_Settings
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




    /**
     * Settings Page title.
     *
     * @var string
     */
    private $page_title;

    /**
     * Menu title.
     *
     * @var string
     */
    private $menu_title;

    /**
     * Capability to access Settings page.
     *
     * @var string
     */
    private $capability;

    /**
     * Menu slug.
     *
     * @var string
     */
    private $menu_slug;

    /**
     * Settings form action.
     *
     * @var string
     */
    private $form_action;

    /**
     * Option group.
     *
     * @var string
     */
    private $option_group;

    /**
     * Constructor
     */
    public function __construct()
    {



        // parameters.
        $this->page_title = esc_html__('Adas Divi Database Add-on | shortcode: [adas_divi]', 'adasdividb');
        $this->menu_title = esc_html__('Adas divi Settings', 'adasdividb');
        $this->capability = 'manage_options';
        $this->menu_slug = 'khdiviwplist.php';

        $this->form_action = 'options.php';

        $this->option_group = 'adasdividb_sp_plugin';

        // actions.
        add_action('admin_menu', array($this, 'add_settings_page'));
        add_action('admin_init', array($this, 'add_settings'));


    }


    /**
     * Adds a submenu page to the Settings main menu.
     */
    public function add_settings_page()
    {
        /**
         * Params for add_options_page
         *
         * @param  string       $page_title The text to be displayed in the title tags of the page when the menu is selected.
         * @param  string       $menu_title The text to be used for the menu.
         * @param  string       $capability The capability required for this menu to be displayed to the user.
         * @param  string       $menu_slug  The slug name to refer to this menu by (should be unique for this menu).
         * @param  callable     $callback   Optional. The function to be called to output the content for this page.
         * @param  int          $position   Optional. The position in the menu order this item should appear.
         * @return string|false The resulting page's hook_suffix, or false if the user does not have the capability required.
         */
        add_options_page(
            $this->page_title,
            $this->menu_title,
            $this->capability,
            $this->menu_slug,
            array($this, 'settings_page_html')
        );
    }

    /**
     * Compose settings
     */
    public function add_settings()
    {

        // Define Sections ----------------------------------------------------.

        /**
         * Adds a new section to a settings page.
         *
         * @param string   $id       Slug-name to identify the section. Used in the 'id' attribute of tags.
         * @param string   $title    Formatted title of the section. Shown as the heading for the section.
         * @param callable $callback Function that echos out any content at the top of the section (between heading and fields).
         * @param string   $page     The slug-name of the settings page on which to show the section. Built-in pages include
         *                           'general', 'reading', 'writing', 'discussion', 'media', etc. Create your own using
         *                           add_options_page();
         */
        add_settings_section(
            'cliowp_settings_page_section1',
            '<span class="label_setting label-primary"><i class="fas fa-database"></i> Database settings</span>',            null,
            $this->menu_slug
        );

        add_settings_section(
            'cliowp_settings_page_section2',
            __('<span class="label_setting label-primary"><i class="fas fa-paint-brush"></i>
            Style', 'adasdividb'),
            null,
            $this->menu_slug
        );

        // Input text field ---------------------------------------------------.

        /**
         * Adds a new field to a section of a settings page.
         */


        /**
         * Registers a setting and its data.
         * 
         */


        // MultiSelect field --------------------------------------------------.
        add_settings_field(
            'divi_form_id_setting',
            __('<span class="label_setting">Divi\' Form ID', 'adasdividb'),
            array($this, 'multiselect1_html'),
            $this->menu_slug,
            'cliowp_settings_page_section1'
        );

        register_setting(
            $this->option_group,
            'divi_form_id_setting',
        );

        // Number of entries in page --------------------------------------------------.
        add_settings_field(
            'items_per_page',
            __('<span class="label_setting">Entries Per Page', 'adasdividb'),
            array($this, 'number_page_html'),
            $this->menu_slug,
            'cliowp_settings_page_section1'
        );

        register_setting(
            $this->option_group,
            'items_per_page',
            array(
                'default' => '10',
            )
        );

        // Checkbox field -----------------------------------------------------.
        add_settings_field(
            'Enable_data_saving_checkbox',
            __('<span class="label_setting"><i class="far fa-pause-circle"></i> Data Saving', 'adasdividb'),
            array($this, 'checkbox1_html'),
            $this->menu_slug,
            'cliowp_settings_page_section1'
        );

        register_setting(
            $this->option_group,
            'Enable_data_saving_checkbox',
            array(
                
            )
        );

        // Color field for wraper--------------------------------------------------------.
        add_settings_field(
            'khdivi_bg_color',
            __('<span class="label_setting">Background Color', 'adasdividb'),
            array($this, 'color1_html'),
            $this->menu_slug,
            'cliowp_settings_page_section2'
        );

        register_setting(
            $this->option_group,
            'khdivi_bg_color',
            array(
                'default' => '#ebe0e0',
            )
        );

        // Color field for text--------------------------------------------------------.
        add_settings_field(
            'khdivi_text_color',
            __('<span class="label_setting">Text Color', 'adasdividb'),
            array($this, 'color_html2'),
            $this->menu_slug,
            'cliowp_settings_page_section2'
        );

        register_setting(
            $this->option_group,
            'khdivi_text_color',
        );

        // Color field for label--------------------------------------------------------.
        add_settings_field(
            'khdivi_label_color',
            __('<span class="label_setting">Label Text Color', 'adasdividb'),
            array($this, 'color_html3'),
            $this->menu_slug,
            'cliowp_settings_page_section2'
        );

        register_setting(
            $this->option_group,
            'khdivi_label_color',
        );

        // bg Color for export button --------------------------------------------------------.
        add_settings_field(
            'khdivi_exportbg_color',
            __('<span class="label_setting">Export Button Background Color', 'adasdividb'),
            array($this, 'color_exportbg_html'),
            $this->menu_slug,
            'cliowp_settings_page_section2'
        );

        register_setting(
            $this->option_group,
            'khdivi_exportbg_color',
            array(
                'default' => '#408c4f',
            )
        );


    }


    /**
     * Sanitize input1
     *
     * @param string $input The input value.
     */
    public function sanitize_input1($input)
    {
        if (true === empty(trim($input))) {
            add_settings_error(
                'cliowp_sp_input1',
                'cliowp_sp_input1_error',
                esc_html__('Input1 cannot be empty', 'adasdividb'),
            );
            return get_option('cliowp_sp_input1');
        }

        return sanitize_text_field($input);
    }

    /**
     * Create HTML for number1 field
     *
     * @param array $args Arguments passed.
     */
    function number1_html()
    {
        global $wpdb;
        $table_name = 'divi_table';
        $is_divi_active = class_divi_KHdb::getInstance()->is_divi_active();
        $results_formids = $wpdb->get_results("SELECT DISTINCT contact_form_id FROM $table_name");

        $form_id = get_option('divi_form_id_setting');

        if (!$is_divi_active) {
            // The plugin is not activated
            printf(' <br>Divi THEME IS NOT ACTIVE!!');
        }

       else {
            echo '<div class="form-field">';
            echo '<select  name="divi_form_id_setting" id="divi_form_id_setting">';
            // Initialize an empty array to store form_id values
            foreach ($results_formids as $row) {
                $selected = ($row->contact_form_id == $form_id) ? 'selected' : '';
                echo '<option value="' . esc_attr($row->contact_form_id) . '" ' . $selected . '>' . esc_html($row->contact_form_id) . '</option>';

            }
            $selected_all = ($form_id == '1') ? 'selected' : '';
            echo '<option value="1" ' . $selected_all . '>All forms</option>';
            echo '</select>';
            echo '</div>';
        }

    }

    /**
     * Create HTML for checkbox1 field
     */
    public function checkbox1_html()
    {
        $opt = get_option( 'Enable_data_saving_checkbox' );
        $value = isset( $opt ) && $opt == 1 ? 1 : '0';       
        ?>
        <input class="form-control Enable_data_saving_checkbox" type="checkbox" name="Enable_data_saving_checkbox"
        value="1" <?php checked( 1, $value ); ?>
         >
<?php    }
    
    


    /**
     * Create HTML for Notification checkbox field
     */
    public function number_page_html()
    {

        $numberperpage = get_option('items_per_page');

        echo '<input class="form-control-items_per_page" type="text" name="items_per_page" value="' . esc_attr($numberperpage) . '" />';
    }



    /**
     * Create HTML for multiselect1 field
     */
    public function multiselect1_html()
    {
        global $wpdb;
        $is_divi_active = class_divi_KHdb::getInstance()->is_divi_active();

        $table_name = $wpdb->prefix . 'divi_table';
        $results_formids = $wpdb->get_results("SELECT DISTINCT contact_form_id FROM $table_name");

        if (!$is_divi_active) {
            // The plugin is not activated
            printf('<div class="warning-text">Divi Theme is not active! <i class="far fa-exclamation-triangle"></i></div>');        }

      
       

        if (count($results_formids) > 0) {
            $selected_values = get_option('divi_form_id_setting');
            ?>

            <?php
            //esc_attr_e('<h1><</h1>', 'adasdividb');
            $message = sprintf(esc_html__('To select multiple IDs, press and hold the Ctrl button while selecting IDs.'));
            $html_message = sprintf('<div class="information-text">%s</div>', wpautop($message));
            echo wp_kses_post($html_message); ?>

            </option>

            <select name="divi_form_id_setting[]" class="divi_form_select_id_setting" multiple>
                <?php
                foreach ($results_formids as $form_id) {
                    $option_value = esc_attr($form_id->contact_form_id);
                    echo "<option value='" . esc_html($option_value) . "' " . esc_html($this->cliowp_multiselected($selected_values, $option_value)) . ">
                Form ID: $option_value</option>";
                }
                ?>
            </select>

        <?php } else {

            /* translators: %s: PHP version */
            $message = sprintf(esc_html__('Currently, no data has been submitted. Kindly submit at least one form using Divi Contact Form.'));
            $html_message = sprintf('<div class="warning-text">%s</div>', wpautop($message));
            echo wp_kses_post($html_message);

        }
    }

    /**
     * Utility function to check if value is selected
     *
     * @param array|string $selected_values Array (or empty string) returned by get_option().
     * @param string       $current_value Value to check if it is selected.
     *
     * @return string
     */
    private function cliowp_multiselected($selected_values, string $current_value): string
    {
        if (is_array($selected_values) && in_array($current_value, $selected_values, true)) {
            return 'selected';
        }

        return '';
    }


    /**
     * Create HTML for color1 field
     */
    public function color1_html()
    {
        ?>
        <input type="color" name="khdivi_bg_color" value="<?php echo esc_attr(get_option('khdivi_bg_color')); ?>">
        <?php
    }

    /**
     * Create HTML for color1 field
     */
    public function color_html2()
    {
        ?>
        <input type="color" name="khdivi_text_color" value="<?php echo esc_attr(get_option('khdivi_text_color')); ?>">
        <?php
    }

    /**
     * Create HTML for label color field
     */
    public function color_html3()
    {
        ?>
        <input type="color" name="khdivi_label_color" value="<?php echo esc_attr(get_option('khdivi_label_color')); ?>">
        <?php
    }

    /**
     * Create HTML for label color field
     */
    public function color_exportbg_html()
    {
        ?>
        <input type="color" name="khdivi_exportbg_color" value="<?php echo esc_attr(get_option('khdivi_exportbg_color')); ?>">
        <?php
    }



    /**
     * Create Settings Page HTML
     */
    public function settings_page_html()
    {
        ?>

        <div class="wrap">
            <h1>
                <?php echo esc_attr($this->page_title); ?>
            </h1>
            <form action="<?php echo esc_attr($this->form_action); ?>" method="POST">
                <?php
                settings_fields($this->option_group);
                do_settings_sections($this->menu_slug);
                submit_button('Save Settings', 'primary', 'adas-divi-submit-button');
                ?>
            </form>
        </div>

        <?php
    }

    /**
     * Loads plugin's translated strings.
     */
    public function load_languages()
    {
        /**
         * Params of load_plugin_textdomain
         */
        load_plugin_textdomain(
            'adasdividb',
            false,
            dirname(plugin_basename(__FILE__)) . '/languages'
        );
    }
}

$cliowp_settings_page = new Adas_Divi_Settings();