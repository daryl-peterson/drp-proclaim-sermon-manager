<?php

/**
 * Template class.
 *
 * @package     Proclaim Sermon Manager
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2024, Daryl Peterson
 * @license     https://www.gnu.org/licenses/gpl-3.0.txt
 * @since       1.0.0
 */

namespace DRPPSM;

defined( 'ABSPATH' ) || exit;

use DRPPSM\Constants\PT;
use DRPPSM\Constants\Tax;
use DRPPSM\Interfaces\Executable;
use DRPPSM\Interfaces\Registrable;
use WP_Post;

/**
 * Template class.
 *
 * @package     Proclaim Sermon Manager
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2024, Daryl Peterson
 * @license     https://www.gnu.org/licenses/gpl-3.0.txt
 * @since       1.0.0
 */
class Templates implements Executable, Registrable {

	/**
	 * Post type
	 *
	 * @var string
	 */
	private string $pt;

	/**
	 * Post types and taxonomies allowed.
	 *
	 * @var array
	 */
	private array $types;

	/**
	 * Initialize object properties.
	 *
	 * @since 1.0.0
	 */
	protected function __construct() {
		$this->pt    = PT::SERMON;
		$this->types = array_merge( Tax::LIST, array( PT::SERMON ) );
	}

	/**
	 * Initialize object and register hooks.
	 *
	 * @return Templates
	 * @since 1.0.0
	 */
	public static function exec(): Templates {

		$obj = new self();
		$obj->register();
		return $obj;
	}

	/**
	 * Register hooks.
	 *
	 * @return bool Returns true if hooks were registered, otherwise false.
	 * @since 1.0.0
	 */
	public function register(): ?bool {

		if ( has_filter( 'template_include', array( $this, 'get_template' ) ) ) {
			return false;
		}
		add_filter( 'template_include', array( $this, 'get_template' ), 10, 1 );
		return true;
	}

	/**
	 * Get table for use.
	 *
	 * @param string $template Template name.
	 * @return string
	 * @since 1.0.0
	 */
	public function get_template( string $template ): string {
		$default_file = '';

		if ( is_singular( $this->pt ) ) {
			$default_file = 'single-drppsm_sermon.php';
		} elseif ( is_tax( get_object_taxonomies( $this->pt ) ) ) {
			$term = get_queried_object();

			if ( is_tax( Tax::LIST ) ) {
				$default_file = $this->get_tax_template();
			} else {
				$default_file = 'archive-drppsm_sermon.php';
			}
		} elseif ( is_post_type_archive( $this->pt ) ) {
			$default_file = $this->get_archive_template();
		}

		if ( $default_file ) {
			if ( file_exists( get_stylesheet_directory() . '/' . $default_file ) ) {
				return get_stylesheet_directory() . '/' . $default_file;
			}

			return DRPPSM_PATH . 'views/' . $default_file;
		}

		return $template;
	}

	/**
	 * Get partial template.
	 *
	 * - `/wp-contents/themes/<theme_name>/partials/<partial_name>.php`
	 * - `/wp-contents/themes/<theme_name>/template-parts/<partial_name>.php`
	 * - `/wp-contents/themes/<theme_name>/<partial_name>.php`
	 *
	 * @param string $name File name.
	 * @param array  $args Array of variables to pass to template.
	 * @return void
	 * @since 1.0.0
	 */
	public static function get_partial( string $name, null|array $args = array() ): void {

		/**
		 * Allows for filtering partial content.
		 *
		 * @param string $name File name.
		 * @param array  $args Array of variables to pass to template.
		 * @since 1.0.0
		 */
		$content = apply_filters( DRPPSM_FLTR_TPL_PARTIAL, $name, $args );
		if ( ! empty( $content ) && $content !== $name ) {
			echo $content;
			return;
		}

		if ( false === strpos( $name, '.php' ) ) {
			$name .= '.php';
		}

		/**
		 * No template partial so let's continue.
		 */
		$paths = array(
			'partials/',
			'template-parts/',
			'',
		);

		foreach ( $paths as $path ) {
			$partial = locate_template( $path . $name );

			if ( $partial ) {
				break;
			}
		}
		Logger::debug(
			array(
				'NAME'             => $name,
				'PARTIAL TEMPLATE' => $partial,
			)
		);

		if ( $partial ) {
			load_template( $partial, false, $args );
		} elseif ( file_exists( DRPPSM_PATH . 'views/partials/' . $name ) ) {
			load_template( DRPPSM_PATH . 'views/partials/' . $name, false, $args );
		} else {
			$title  = DRPPSM_TITLE;
			$error  = DRPPSM_MSG_FAILED_PARTIAL;
			$error .= str_replace( '.php', '', $name );

			echo "<b>$title</b>:<i>$error</i>." . DRPPSM_MSG_FILE_NOT_EXIST . '</p>';
		}
	}


	/**
	 * Get sermon single.
	 *
	 * @param null|WP_Post $post_new Post object.
	 * @return void
	 * @since 1.0.0
	 */
	public static function sermon_single( ?WP_Post $post_new = null ): void {

		if ( null === $post_new ) {
			global $post;
			$post_org = clone ($post);
		} else {
			$post_org = clone ($post_new);
		}

		/**
		 * Allows you to modify the sermon HTML on single sermon pages.
		 *
		 * @param WP_Post $post Sermon post object.
		 * @since 1.0.0
		 */
		$output = apply_filters( DRPPSM_FLTR_SERMON_SINGLE, $post_org );
		if ( ! $output instanceof WP_Post && is_string( $output ) ) {
			echo $output;
			return;
		}

		// Get the partial.
		self::get_partial( 'content-sermon-single' );
	}

	/**
	 * Get single template.
	 *
	 * @return null|string Return file name if true,otherwise null.
	 * @since 1.0.0
	 */
	private function get_single_template(): ?string {
		if ( ! is_singular( $this->pt ) ) {
			Logger::debug( 'NOT SINGLE TEMPLATE' );
			return null;
		}
		Logger::debug( "IT'S A SINGLE TEMPLATE" );
		return "single-{$this->pt}.php";
	}

	/**
	 * Get archive template.
	 *
	 * @return null|string Return file name if true,otherwise null.
	 * @since 1.0.0
	 */
	public function get_archive_template(): ?string {
		if ( ! is_post_type_archive( $this->pt ) ) {
			Logger::debug( 'NOT A ARCHIVE TEMPLATE' );
			return null;
		}
		Logger::debug( "IT'S A ARCHIVE TEMPLATE" );
		return "archive-{$this->pt}.php";
	}

	/**
	 * Get taxonomy template.
	 *
	 * @return null|string Return file name if true,otherwise null.
	 * @since 1.0.0
	 */
	private function get_tax_template(): ?string {
		/*
		if ( is_tax( Tax::LIST ) ) {
			Logger::debug( 'NOT A TAX TEMPLATE' );
			return null;
		}
		*/

		$term          = get_queried_object();
		$template_file = "taxonomy-{$term->taxonomy}.php";

		if ( ! file_exists( get_stylesheet_directory() . '/' . $template_file ) ) {
			$template_file = "archive-{$this->pt}.php";
		}
		return $template_file;
	}
}
