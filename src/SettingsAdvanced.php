<?php
/**
 * Advanced settings.
 *
 * @package     Proclaim Sermon Manager
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2024, Daryl Peterson
 * @license     https://www.gnu.org/licenses/gpl-3.0.txt
 * @since       1.0.0
 */
namespace DRPPSM;

use CMB2;
use DRPPSM\Constants\Actions;

/**
 * Class description
 *
 * @package
 * @category
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2024, Daryl Peterson
 * @license     https://www.gnu.org/licenses/gpl-3.0.txt
 * @since       1.0.0
 */
class SettingsAdvanced extends SettingsBase {
	public string $option_key = Settings::OPTION_KEY_ADVANCED;

	/**
	 * Initailize and register hooks.
	 *
	 * @return self
	 * @since 1.0.0
	 */
	public static function exec(): self {
		$obj = new self();
		$obj->register();
		return $obj;
	}

	/**
	 * Register hooks.
	 *
	 * @return boolean|null Always true.
	 * @since 1.0.0
	 */
	public function register(): ?bool {
		add_action( Actions::SETTINGS_REGISTER_FORM, array( $this, 'register_metaboxes' ) );
		add_filter( DRPPSMF_SETTINGS_RSM, array( $this, 'set_menu' ) );
		return true;
	}

	/**
	 * Register metaboxes.
	 *
	 * @param callable $display_cb Display callback.
	 * @return void
	 * @since 1.0.0
	 */
	public function register_metaboxes( callable $display_cb ) {
		$title = 'Proclaim ' . __( 'Sermon Manager Settings', 'drppsm' );

		/**
		 * Registers main options page menu item and form.
		 */
		$args = array(
			'id'           => $this->option_key,
			'title'        => $title,
			'object_types' => array( 'options-page' ),
			'option_key'   => $this->option_key,
			'parent_slug'  => AdminSettings::SLUG,
			'tab_group'    => AdminSettings::TAB_GROUP,
			'tab_title'    => 'Advanced',
			'display_cb'   => $display_cb,
		);

		$cmb = new_cmb2_box( $args );
		$this->add_seperator( $cmb, __( 'Bible Settings', 'drppsm' ) );
		$this->bible_book_load( $cmb );
		$this->bible_book_sort( $cmb );
	}


	private function bible_book_load( CMB2 $cmb ) {
		$desc = __(
			'Select this to reload books',
			'drppsm'
		);
		$cmb->add_field(
			array(
				'id'        => Settings::BIBLE_BOOK_LOAD,
				'name'      => __( 'Load Books', 'drppsm' ),
				'type'      => 'checkbox',
				'default'   => Settings::get( Settings::BIBLE_BOOK_LOAD, true ),
				'after_row' => $this->description( $desc ),
			)
		);
	}

	private function bible_book_sort( CMB2 $cmb ) {
		$desc = __(
			'Orders book in filtering by biblical order, rather than alphabetical. Default checked.',
			'drppsm'
		);
		$cmb->add_field(
			array(
				'id'        => Settings::BIBLE_BOOK_SORT,
				'name'      => __( 'Sort Books', 'drppsm' ),
				'type'      => 'checkbox',
				'default'   => Settings::get( Settings::BIBLE_BOOK_SORT, true ),
				'after_row' => $this->description( $desc ),
			)
		);
	}
}
