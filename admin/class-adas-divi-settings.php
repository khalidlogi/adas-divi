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
        $this->page_title = esc_html__('Adas divi Database Add-on | shortcode: [divi_data]', 'adasdividb');
        $this->menu_title = esc_html__('Adas-divi-Db-Addon', 'adasdividb');
        $this->capability = 'manage_options';
        $this->menu_slug = 'khdiviwplist.php';

        $this->form_action = 'options.php';

        $this->option_group = 'adasdividb_sp_plugin';

        // actions.
        add_action('admin_menu', array($this, 'add_settings_page'));
        add_action('admin_init', array($this, 'add_settings'));


    }


    function divi_settings_link($links_array)
    {
        $Settings = '<a href="admin.php?page=khdiviwplist.php">Settings</a>';
        array_unshift($links_array, $Settings);
        return $links_array;
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
            __('<span class="label_setting label-primary">Database settings ', 'adasdividb'),
            null,
            $this->menu_slug
        );

        add_settings_section(
            'cliowp_settings_page_section2',
            __('<span class="label_setting label-primary">Appearnces', 'adasdividb'),
            null,
            $this->menu_slug
        );

        // Input text field ---------------------------------------------------.

        /**
         * Adds a new field to a section of a settings page.
         *
         * Part of the Settings API. Use this to define a settings field that will show
         * as part of a settings section inside a settings page. The fields are shown using
         * do_settings_fields() in do_settings_sections().
         *
         * The $callback argument should be the name of a function that echoes out the
         * HTML input tags for this setting field. Use get_option() to retrieve existing
         * values to show.
         *
         * @param string   $id       Slug-name to identify the field. Used in the 'id' attribute of tags.
         * @param string   $title    Formatted title of the field. Shown as the label for the field
         *                           during output.
         * @param callable $callback Function that fills the field with the desired form inputs. The
         *                           function should echo its output.
         * @param string   $page     The slug-name of the settings page on which to show the section
         *                           (general, reading, writing, ...).
         * @param string   $section  Optional. The slug-name of the section of the settings page
         *                           in which to show the box. Default 'default'.
         * @param array    $args     {
         *                           Optional. Extra arguments used when outputting the field.
         *
         *     @type string $label_for When supplied, the setting title will be wrapped
         *                             in a `<label>` element, its `for` attribute populated
         *                             with this value.
         *     @type string $class     CSS Class to be added to the `<tr>` element when the
         *                             field is output.
         * }
         */


        /**
         * Registers a setting and its data.
         *
         * @param string $option_group A settings group name. Should correspond to an allowed option key name.
         *                             Default allowed option key names include 'general', 'discussion', 'media',
         *                             'reading', 'writing', and 'options'.
         * @param string $option_name The name of an option to sanitize and save.
         * @param array  $args {
         *     Data used to describe the setting when registered.
         *
         *     @type string     $type              The type of data associated with this setting.
         *                                         Valid values are 'string', 'boolean', 'integer', 'number', 'array', and 'object'.
         *     @type string     $description       A description of the data attached to this setting.
         *     @type callable   $sanitize_callback A callback function that sanitizes the option's value.
         *     @type bool|array $show_in_rest      Whether data associated with this setting should be included in the REST API.
         *                                         When registering complex settings, this argument may optionally be an
         *                                         array with a 'schema' key.
         *     @type mixed      $default           Default value when calling `get_option()`.
         * }
         */


        // MultiSelect field --------------------------------------------------.
        add_settings_field(
            'divi_form_id_setting',
            __('<span class="label_setting">divi\' Form id', 'adasdividb'),
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
            __('<span class="label_setting">Number of entries per Page', 'adasdividb'),
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
            __('<span class="label_setting">Pause Data saving', 'adasdividb'),
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
            __('<span class="label_setting">Export button bg Color', 'adasdividb'),
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

        if (empty($results_formids)) {
            printf(__('It appears that there are no form entries detected. Please add a form using the divi plugin and submit at least one form.'));

            if ($is_divi_active) {
                // The plugin is not activated
                printf(' <br>DIVI THEME IS NOT ACTIVE!!');
            }
        } else {
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
     * Create HTML for select1 field
     */
    public function select1_html()
    {
        ?>
        <select name="cliowp_sp_select1">
            <option value="1" <?php selected(get_option('cliowp_sp_select1'), '1'); ?>>
                <?php esc_attr_e('Option1', 'adasdividb'); ?>
            </option>
            <option value="2" <?php selected(get_option('cliowp_sp_select1'), '2'); ?>>
                <?php esc_attr_e('Option2', 'adasdividb'); ?>
            </option>
            <option value="3" <?php selected(get_option('cliowp_sp_select1'), '3'); ?>>
                <?php esc_attr_e('Option3', 'adasdividb'); ?>
            </option>
        </select>
        <?php
    }

    /**
     * Sanitize select1
     *
     * @param string $input The selected value.
     */
    public function sanitize_select1($input)
    {
        $valid_input = array('1', '2', '3');
        if (false === in_array($input, $valid_input, true)) {
            add_settings_error(
                'cliowp_sp_select1',
                'cliowp_sp_select1_error',
                esc_html__('Invalid option for Select1', 'adasdividb'),
            );
            return get_option('cliowp_sp_select1');
        }
        return $input;
    }

    /**
     * Create HTML for checkbox1 field
     */
    public function checkbox1_html()
    {
        $opt = get_option( 'Enable_data_saving_checkbox' );
        $value = isset( $opt ) && $opt == 1 ? 1 : '0';       
        ?>
        <input class="form-control" type="checkbox" name="Enable_data_saving_checkbox"
        value="1" <?php checked( 1, $value ); ?>
         >
<?php    }
    
    

    public function view_option_html()
    {
        $selected_option = get_option('view_option') ?: 'normal';
        $table_view_selected = ($selected_option === 'table') ? 'checked' : '';
        $normal_view_selected = ($selected_option === 'normal') ? 'checked' : '';
        ?>
        <style>

        </style>
        <label class="view-option-label">
            <input type="radio" name="view_option" value="table" <?php echo $table_view_selected; ?>>
            <img width="220" src="<?php echo plugins_url('/assets/img/table.png', dirname(__FILE__)); ?>" alt="Table View">
        </label>
        <label class="view-option-label">
            <input type="radio" name="view_option" value="normal" <?php echo $normal_view_selected; ?>>
            <img width="220" src="<?php echo plugins_url('/assets/img/normal.png', dirname(__FILE__)); ?>" alt="Table View">
        </label>
        <?php
    }

    /**
     * Create HTML for Notification checkbox field
     */
    public function checkbox2_html()
    {
        ?>
        <input class="my-custom-checkbox" type="checkbox" name="Enable_notification_checkbox" value="1" <?php checked(get_option('Enable_notification_checkbox'), '1'); ?>>
        <?php
    }

    /**
     * Create HTML for Notification checkbox field
     */
    public function number_page_html()
    {

        $numberperpage = get_option('items_per_page');

        echo '<input class="form-control " type="text" name="items_per_page" value="' . esc_attr($numberperpage) . '" />';
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
        //////error_log(print_r($results_formids, true));

        if (!$is_divi_active) {
            // The plugin is not activated
            printf(' <h1 class="warning-text">DIVI Theme is not active!</h1>');
        }

        if (count($results_formids) > 0) {
            $selected_values = get_option('divi_form_id_setting');
            ?>

            <?php
            //esc_attr_e('<h1><</h1>', 'adasdividb');
            $message = sprintf(esc_html__('To select multiple IDs, press and hold the Ctrl button while selecting IDs.'));
            $html_message = sprintf('<div class="information-text">%s</div>', wpautop($message));
            echo wp_kses_post($html_message); ?>

            </option>

            <select name="divi_form_id_setting[]" multiple>
                <?php
                foreach ($results_formids as $form_id) {
                    //////error_log(print_r($form_id, true));
                    $option_value = esc_attr($form_id->contact_form_id);
                    // $selected = in_array($option_value, $selected_values) ? 'selected' : '';
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
                submit_button();
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