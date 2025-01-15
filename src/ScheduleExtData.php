<?php
/**
 * Create extra data for sermons, series, topics, bible.
 *
 * @package     DRPPSM\ScheduleExtData
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2024, Daryl Peterson
 * @license     https://www.gnu.org/licenses/gpl-3.0.txt
 * @since       1.0.0
 */

namespace DRPPSM;

use DRPPSM\Constants\Meta;
use DRPPSM\Interfaces\Executable;
use DRPPSM\Interfaces\Registrable;
use stdClass;
use WP_Post;

defined( 'ABSPATH' ) || exit;

/**
 * Create extra data for sermons, series, topics, bible.
 *
 * @package     DRPPSM\ScheduleExtData
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2024, Daryl Peterson
 * @license     https://www.gnu.org/licenses/gpl-3.0.txt
 * @since       1.0.0
 */
class ScheduleExtData implements Executable, Registrable {



	private string $hook;

	private array $hook_args;


	/**
	 * Initialize object properties.
	 *
	 * @since 1.0.0
	 */
	protected function __construct() {
		$this->hook      = 'drppsm_scheduler';
		$this->hook_args = array( $this, 'do_schedule' );
	}

	/**
	 * Initialize and register.
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
	 * @return null|bool
	 * @since 1.0.0
	 */
	public function register(): ?bool {
		$this->add_event();

		if ( ! has_filter( 'deactivate_' . FILE, array( $this, 'deactivate' ) ) ) {
			return false;
		}

		register_deactivation_hook( FILE, array( $this, 'deactivate' ) );

		return true;
	}

	/**
	 * Add event.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function add_event(): void {

		if ( ! wp_next_scheduled( $this->hook, $this->hook_args ) ) {
			wp_schedule_event( strtotime( '3am tomorrow' ), 'daily', $this->hook, $this->hook_args );
		}
	}

	/**
	 * Deactivate scheduling.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function deactivate() {

		wp_clear_scheduled_hook( $this->hook, $this->hook_args );
	}

	/**
	 * Add cron schedule.
	 *
	 * @param array $schedules List of schedules.
	 * @return array
	 * @since 1.0.0
	 */
	public function add_cron_schedule( array $schedules ) {
		$schedules['daily_3am'] = array(
			'interval' => DAY_IN_SECONDS,
			'display'  => __( 'Daily 3am' ),
		);

		return $schedules;
	}



	/**
	 * Do schedule.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function do_schedule(): void {

		$this->set_series_post_info();
	}


	/**
	 * Set series info.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function set_series_post_info(): void {

		$list = TaxQueries::get_terms_with_images(
			array(
				'taxonomy' => DRPPSM_TAX_SERIES,
				'order'    => 'ASC',
				'orderby'  => 'name',
			)
		);

		if ( ! $list ) {
			return;
		}

		/**
		 * @var \WP_Term $item
		 */
		foreach ( $list as $item ) {

			$data = new \stdClass();

			$post_args = array(
				'post_type' => DRPPSM_PT_SERMON,
				'taxonomy'  => DRPPSM_TAX_SERIES,
				'terms'     => $item->term_id,
			);
			$post_list = TaxQueries::get_term_posts( $post_args );

			if ( ! $post_list ) {
				continue;
			}

			$data = $this->get_series_info( $item->term_id, $post_list );

			$key = 'drppsm_series_info_' . $item->term_id;
			set_transient( $key, $data, 8 * HOUR_IN_SECONDS );
		}
	}

	private function get_series_info( int $series_id, array $post_list ): stdClass {
		$obj           = new \stdClass();
		$obj->preacher = $this->init_object();
		$obj->topics   = $this->init_object();
		$obj->dates    = array();

		/**
		 * @var \WP_Post $post_item Post for series.
		 */
		foreach ( $post_list as $post_item ) {

			$date         = get_post_meta( $post_item->ID, Meta::DATE, true );
			$obj->dates[] = $date;

			$preacher_terms = get_the_terms( $post_item->ID, DRPPSM_TAX_PREACHER );

			if ( $preacher_terms ) {

				$this->set_term_info( $obj->preacher, $preacher_terms );
				// $obj->preacher->cnt   = count( $obj->preacher->names );

			}

			$topics = get_the_terms( $post_item->ID, DRPPSM_TAX_TOPICS );

			if ( $topics ) {
				$this->set_term_info( $obj->topics, $topics );
				$obj->topics->cnt = count( $obj->topics->names );
			} else {
				$obj->topics->cnt = 0;
			}
		}

		return $obj;
	}


	private function init_object() {
		$obj        = new \stdClass();
		$obj->names = array();
		$obj->terms = array();
		$obj->cnt   = 0;
		return $obj;
	}


	private function set_term_info( stdClass &$object, array $term_list ) {

		/**
		 * @var \WP_Term $item
		 */
		foreach ( $term_list as $item ) {
			if ( ! in_array( $item->name, $object->names ) ) {
				$object->names[] = $item->name;
				$object->terms[] = $item;
			}
		}

		$object->cnt = count( $object->names );
	}

	private function get_topic_info() {
	}
}
