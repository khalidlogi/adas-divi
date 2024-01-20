<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://web-pro.store
 * @since      1.0.0
 *
 * @package    Adas_Divi
 * @subpackage Adas_Divi/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Adas_Divi
 * @subpackage Adas_Divi/includes
 * @author     khalidlogi <KHALIDLOGI@GMAIL.COM>
 */
class Adas_Divi_Deactivator {

	/**
	 * deactivate function
	 */
	public static function deactivate() {

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

			}

	}
