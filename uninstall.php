<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * @link       https://web-pro.store
 * @since      1.0.0
 *
 * @package    Adas_Divi
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Delete all form settings stored
$options_to_delete = [
    'khdivi_label_color',
    'khdivi_text_color',
    'khdivi_exportbg_color',
    'khdivi_bg_color',
    'items_per_page',
	'divi_form_id_setting',
	'items_per_page',
	'Enable_data_saving_checkbox',
];

foreach ($options_to_delete as $option_name) {
    delete_option($option_name);
}
