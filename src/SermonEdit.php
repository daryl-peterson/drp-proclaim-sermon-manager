<?php
/**
 * Admin sermon post edit / add.
 *
 * @package     Proclaim Sermon Manager
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2024, Daryl Peterson
 * @license     https://www.gnu.org/licenses/gpl-3.0.txt
 * @since       1.0.0
 */

namespace DRPPSM;

defined( 'ABSPATH' ) || exit;

use DRPPSM\App;
use DRPPSM\Constants\PT;
use DRPPSM\Constants\Tax;
use DRPPSM\Interfaces\Initable;
use DRPPSM\Interfaces\OptionsInt;
use DRPPSM\Interfaces\Registrable;
use DRPPSM\Logging\Logger;
use DRPPSM\PostTypeUtils;
use DRPPSM\SermonDetail;
use DRPPSM\SermonFiles;
use DRPPSM\TaxUtils;

/**
 * Admin sermon post edit / add.
 *
 * @package     Proclaim Sermon Manager
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2024, Daryl Peterson
 * @license     https://www.gnu.org/licenses/gpl-3.0.txt
 * @since       1.0.0
 */
class SermonEdit implements Initable, Registrable {

	/**
	 * Post type
	 *
	 * @var string
	 * @since 1.0.0
	 */
	protected string $post_type;

	/**
	 * Options Interface.
	 *
	 * @var OptionsInt
	 * @since 1.0.0
	 */
	protected OptionsInt $options;

	/**
	 * Initialize object.
	 *
	 * @var OptionsInt $options Options interface.
	 * @since 1.0.0
	 */
	protected function __construct() {
		$this->post_type = PT::SERMON;
		$this->options   = App::init()->get( OptionsInt::class );
	}

	/**
	 * Get initialize object.
	 *
	 * @return SermonEdit
	 * @since 1.0.0
	 */
	public static function init(): SermonEdit {
		return new self();
	}

	/**
	 * Register callbacks.
	 *
	 * @return null|bool Returns true as default.
	 * @since 1.0.0
	 */
	public function register(): ?bool {
		if ( ! is_admin() && ! defined( 'PHPUNIT_TESTING' ) ) {
			return false;
		}

		// @todo fix this
		// add_action( 'pre_get_posts', array( $this, 'fix_ordering' ), 90 );
		add_filter( 'use_block_editor_for_post_type', array( $this, 'disable_gutenberg' ), 10, 2 );
		add_action( 'cmb2_admin_init', array( $this, 'show_meta_boxes' ) );
		add_action( 'save_post_drppsm', array( $this, 'save_post' ), 40, 3 );
		add_action( 'admin_menu', array( $this, 'remove_meta_boxes' ) );
		return true;
	}

	/**
	 * Save post
	 *
	 * @param integer  $post_id Post ID.
	 * @param \WP_Post $post WordPress post.
	 * @param boolean  $update Not currently used.
	 * @return integer
	 */
	public function save_post( int $post_id, \WP_Post $post, bool $update ): int {
		try {
			if ( ! PostTypeUtils::is_savable( $post_id, $post ) ) {
				return $post_id;
			}

			// @todo Call a save function.

			return $post_id;
			// @codeCoverageIgnoreStart
		} catch ( \Throwable $th ) {
			Logger::error(
				array(
					'MESSAGE' => $th->getMessage(),
					'TRACE'   => $th->getTrace(),
				)
			);
			// @codeCoverageIgnoreEnd
		}

		return $post_id;
	}

	/**
	 * Fix date.
	 *
	 * @param mixed $value Still trying to figure this out.
	 * @return mixed Not sure yet.
	 *
	 * @todo Fix this.
	 */
	public function fix_date( $value ) {
		Logger::debug( array( 'VALUE' => $value ) );

		return $value;
	}

	/**
	 * Disable gutenberg editor for this post type.
	 *
	 * @param boolean $current_status Current status.
	 * @param string  $post_type Post type.
	 * @return boolean
	 */
	public function disable_gutenberg( bool $current_status, string $post_type ): bool {
		if ( PT::SERMON === $post_type ) {
			return false;
		}

		return (bool) $current_status;
	}

	/**
	 * Display metaboxes.
	 *
	 * @return void
	 */
	public function show_meta_boxes(): void {
		App::init()->get( SermonDetail::class )->show();
		App::init()->get( SermonFiles::class )->show();
	}

	/**
	 * Fix ordering
	 *
	 * @param \WP_Query $query Working on this.
	 * @return void
	 *
	 * @since 1.0.0
	 *
	 * @todo fix this
	 */
	public function fix_ordering( \WP_Query $query ): void {
		$pt = $this->post_type;
		if ( is_admin() || ! $query->is_main_query() ) {
			return;
		}

		$taxonomies = TaxUtils::get_taxonomies( $pt );
		if ( ! is_post_type_archive( $pt ) && ! is_tax( $taxonomies ) ) {
			return;
		}

		$opts    = $this->options;
		$orderby = $opts->get( 'archive_orderby', '' );
		$order   = $opts->get( 'archive_order', 'desc' );

		switch ( $orderby ) {
			case 'date_preached':
				$query->set( 'meta_key', 'sermon_date' );
				$query->set( 'meta_value_num', time() );
				$query->set( 'meta_compare', '<=' );
				$query->set( 'orderby', 'meta_value_num' );
				break;
			case 'date_published':
				$query->set( 'orderby', 'date' );
				break;
			case 'title':
			case 'random':
			case 'id':
				$query->set( 'orderby', $orderby );
				break;
		}

		$query->set( 'order', strtoupper( $order ) );

		$query->set( 'posts_per_page', $opts->get( 'sermon_count', get_option( 'posts_per_page' ) ) );
	}

	/**
	 * Remove meta boxes.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function remove_meta_boxes(): void {
		inc_remove_meta_box();
		remove_meta_box( 'postcustom', $this->post_type, 'normal' );
		remove_meta_box( 'tagsdiv-' . Tax::SERVICE_TYPE, $this->post_type, 'high' );
		remove_meta_box( 'commentsdiv', $this->post_type, 'normal' );
		remove_meta_box( 'revisionsdiv', $this->post_type, 'normal' );
	}
}
