<?php
/**
 * Sermon Settings.
 *
 * @package     DRPPSM\SPSermon
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2024, Daryl Peterson
 * @license     https://www.gnu.org/licenses/gpl-3.0.txt
 * @since       1.0.0
 */

namespace DRPPSM;

use CMB2;
use DRPPSM\Interfaces\Executable;
use DRPPSM\Interfaces\Registrable;
use DRPPSM\Traits\ExecutableTrait;

defined( 'ABSPATH' ) || exit;

/**
 * Sermon Settings.
 *
 * @package     DRPPSM\SPSermon
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2024, Daryl Peterson
 * @license     https://www.gnu.org/licenses/gpl-3.0.txt
 * @since       1.0.0
 */
class SPSermon extends SPBase implements Executable, Registrable {
	use ExecutableTrait;

	/**
	 * Key used in storing options.
	 *
	 * @var string
	 */
	public string $option_key;

	/**
	 * Initialize object properties.
	 *
	 * @since 1.0.0
	 */
	protected function __construct() {
		$this->option_key = Settings::OPTION_KEY_SERMONS;
		parent::__construct();
	}

	/**
	 * Register metaboxes.
	 *
	 * @param callable $display_cb Callback to display on form.
	 * @return void
	 * @since 1.0.0
	 */
	public function register(): ?bool {

		if ( ! is_admin() || has_action( Action::SETTINGS_REGISTER_FORM, array( $this, 'register_metaboxes' ) ) ) {
			return false;
		}

		add_action( Action::SETTINGS_REGISTER_FORM, array( $this, 'register_metaboxes' ) );
		add_filter( Filter::SETTINGS_REMOVE_SUBMENU, array( $this, 'set_menu' ) );
		return true;
	}

	/**
	 * Register metaboxes.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function register_metaboxes( callable $display_cb ): void {
		$menu_title = __( 'Settings', 'drppsm' );
		$title      = 'Proclaim ' . __( 'Sermon Manager Settings', 'drppsm' );

		/**
		 * Registers main options page menu item and form.
		 */
		$args = array(
			'id'           => Settings::OPTION_KEY_SERMONS,
			'title'        => $title,
			'menu_title'   => $menu_title,
			'object_types' => array( 'options-page' ),
			'option_key'   => Settings::OPTION_KEY_SERMONS,
			'parent_slug'  => AdminSettings::SLUG,
			'tab_group'    => AdminSettings::TAB_GROUP,
			'tab_title'    => 'Sermons',
			'display_cb'   => $display_cb,
		);

		$cmb = new_cmb2_box( $args );
		$this->add_seperator( $cmb, __( 'Sermon Settings', 'drppsm' ) );
		$this->date_format( $cmb );
		$this->sermon_count( $cmb );
		$this->add_seperator( $cmb, __( 'Archive Settings', 'drppsm' ) );
		$this->archive_order_by( $cmb );
		$this->archive_order( $cmb );
		$this->sermon_layout( $cmb );

		$this->common_base_slug( $cmb );
		$this->add_seperator( $cmb, __( 'Sermon Labels', 'drppsm' ) );
		$this->sermon_single( $cmb );
		$this->sermon_plural( $cmb );
	}

	/**
	 * Add common base slug.
	 *
	 * @param CMB2 $cmb CMB2 Object.
	 * @return void
	 * @since 1.0.0
	 */
	private function common_base_slug( CMB2 $cmb ): void {

		$desc  = __( 'If this option is checked, the taxonomies would also be under the slug set above.', 'drppsm' );
		$desc .= $this->dot();
		$s1    = '<code>' . __( '/sermons/series/jesus', 'drppsm' ) . '</code>';
		$s2    = '<code>' . __( '/sermons/preacher/mark', 'drppsm' ) . '</code>';

		$desc .= wp_sprintf(
			// translators: %1$s Example series path, effectively <code>/sermons/series/jesus</code>.
			// translators: %2$s Example preacher path, effectively <code>/sermons/preacher/mark</code>.
			__( 'For example, by default, series named “Jesus” would be under %1$s, preacher “Mark” would be under %2$s, and so on.', 'drppsm' ),
			$s1,
			$s2
		);

		$cmb->add_field(
			array(
				'id'        => Settings::COMMON_BASE_SLUG,
				'name'      => __( 'Common Base Slug', 'drppsm' ),
				'type'      => 'checkbox',
				'after_row' => $this->description( $desc ),
			)
		);
	}

	/**
	 * Add date format field.
	 *
	 * @param CMB2 $cmb CMB2 Object.
	 * @return void
	 * @since 1.0.0
	 */
	private function date_format( CMB2 $cmb ): void {
		$desc = __( 'Used only in admin area, when creating a new Sermon', 'drppsm' );
		$cmb->add_field(
			array(
				'id'        => Settings::DATE_FORMAT,
				'name'      => __( 'Date Format', 'drppsm' ),
				'type'      => 'select',
				'options'   => array(
					'F j, Y, g:i A' => 'Febuary 15, 1971, 5:00 AM',
					'F j, Y'        => 'Febuary 15, 1971',
					'M j, Y'        => 'Feb 15, 1971',
					'm/d/Y'         => '02/15/1971',
					'Y/m/d'         => '1971/02/15',
					'Y-m-d'         => '1971-02-15',
				),
				'after_row' => $this->description( $desc ),
			)
		);
	}

	/**
	 * Add archive order by.
	 *
	 * @param CMB2 $cmb CMB2 object.
	 * @return void
	 * @since 1.0.0
	 */
	private function archive_order_by( CMB2 $cmb ): void {
		$desc  = __( 'Changes the way sermons are ordered by default.', 'drppsm' ) . ' ';
		$desc .= __( 'Affects the RSS feed and shown date as well. Default "Date Preached".', 'drppsm' );
		$cmb->add_field(
			array(
				'id'               => Settings::ARCHIVE_ORDER_BY,
				'name'             => __( 'Order sermons by', 'drppsm' ),
				'type'             => 'select',
				'show_option_none' => true,
				'options'          => array(
					'date_preached' => 'Date Preached',
					'date'          => 'Date Published',
					'title'         => 'Title',
					'ID'            => 'ID',
					'random'        => 'Random',
				),
				'after_row'        => $this->description( $desc ),
			)
		);
	}

	/**
	 * Add archive order.
	 *
	 * @param CMB2 $cmb CMB2 object.
	 * @return void
	 * @since 1.0.0
	 */
	private function archive_order( CMB2 $cmb ): void {
		$desc = __( 'Related to the setting above. Default descending.', 'drppsm' );
		$cmb->add_field(
			array(
				'id'               => Settings::ARCHIVE_ORDER,
				'name'             => __( 'Order direction', 'drppsm' ),
				'type'             => 'select',
				'show_option_none' => true,
				'options'          => array(
					'desc' => 'Descending',
					'asc'  => 'Ascending',
				),
				'after_row'        => $this->description( $desc ),
			)
		);
	}

	/**
	 * Add sermon layout field.
	 *
	 * @param CMB2 $cmb CMB2 Object.
	 * @return void
	 * @since 1.0.0
	 */
	private function sermon_layout( CMB2 $cmb ): void {
		$desc = __( 'How sermon archive pages will be displayed.', 'drppsm' );
		$cmb->add_field(
			array(
				'id'        => Settings::SERMON_LAYOUT,
				'name'      => __( 'Layout', 'drppsm' ),
				'type'      => 'select',
				'options'   => array(
					'grid'    => 'Grid',
					'row'     => 'Row',
					'classic' => 'Classic',
				),
				'after_row' => $this->description( $desc ),
			)
		);
	}

	/**
	 * Add sermon count field.
	 *
	 * @param CMB2 $cmb CMB2 Object.
	 * @return void
	 * @since 1.0.0
	 */
	private function sermon_count( CMB2 $cmb ): void {
		$desc = __( 'Affects only the default number, other settings will override it', 'drppsm' );
		$cmb->add_field(
			array(
				'id'         => Settings::SERMON_COUNT,
				'name'       => __( 'Per Page', 'drppsm' ),
				'type'       => 'text',
				'attributes' => array(
					'type'    => 'number',
					'pattern' => '\d*',
				),
				'after_row'  => $this->description( $desc ),
			)
		);
	}

	/**
	 * Add sermon single label.
	 *
	 * @param CMB2 $cmb
	 * @return void
	 * @since 1.0.0
	 */
	private function sermon_single( CMB2 $cmb ): void {
		$s1 = '<code>' . __( '/sermon/mark', 'drppsm' ) . '</code>';
		$s2 = '<code>' . __( '/lecture/mark', 'drppsm' ) . '</code>';

		$desc  = DRPPSM_MSG_LABEL_SINGLE . '<br>';
		$desc .= wp_sprintf(
			// translators: %1$s Default sermon slug/path. Effectively <code>/sermon/mark</code>.
			// translators: %2$s Example lecture slug/path. Effectively <code>/lecture/mark</code>.
			__( 'Changing "Sermon" to "Lecture" would result in %1$s becoming %2$s.', 'drppsm' ),
			$s1,
			$s2
		);
		$desc .= '<br>' . DRPPSM_MSG_SLUG_NOTE;

		$cmb->add_field(
			array(
				'id'        => Settings::SERMON_SINGULAR,
				'name'      => __( 'Singular Label', 'drppsm' ),
				'type'      => 'text',
				'after_row' => $this->description( $desc ),
			)
		);
	}

	/**
	 * Add sermon plural label.
	 *
	 * @param CMB2 $cmb
	 * @return void
	 * @since 1.0.0
	 */
	private function sermon_plural( CMB2 $cmb ): void {
		$s1 = '<code>' . __( '/sermons/', 'drppsm' ) . '</code>';
		$s2 = '<code>' . __( '/lectures/', 'drppsm' ) . '</code>';

		$desc  = DRPPSM_MSG_LABEL_PLURAL . '<br>';
		$desc .= wp_sprintf(
			// translators: %1$s Default series slug/path. Effectively <code>/sermons/</code>.
			// translators: %2$s Example listings slug/path. Effectively <code>/lectures/</code>.
			__( 'Changing "Sermons" to "Lectures" would result in %1$s becoming %2$s.', 'drppsm' ),
			$s1,
			$s2
		);
		$desc .= '<br>' . DRPPSM_MSG_SLUG_NOTE;

		$cmb->add_field(
			array(
				'id'        => Settings::SERMON_PLURAL,
				'name'      => __( 'Plural Label', 'drppsm' ),
				'type'      => 'text',
				'after_row' => $this->description( $desc ),
			)
		);
	}
}
