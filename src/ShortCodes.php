<?php

/**
 * Shortcodes class.
 *
 * @package     Proclaim Sermon Manager
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2024, Daryl Peterson
 * @license     https://www.gnu.org/licenses/gpl-3.0.txt
 * @since       1.0.0
 */

namespace DRPPSM;

defined( 'ABSPATH' ) || exit;

use DRPPSM\Constants\Bible;
use DRPPSM\Interfaces\Executable;
use DRPPSM\Interfaces\Registrable;
use WP_Error;
use WP_Query;
use WP_Term;

/**
 * Shortcodes class.
 *
 * @package     Proclaim Sermon Manager
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2024, Daryl Peterson
 * @license     https://www.gnu.org/licenses/gpl-3.0.txt
 * @since       1.0.0
 */
class ShortCodes implements Executable, Registrable {

	/**
	 * Sermon post type.
	 *
	 * @var string
	 * @since 1.0.0
	 */
	private string $pt_sermon;


	/**
	 * Lastest sermon shortcode
	 *
	 * @var string
	 * @since 1.0.0
	 */
	private string $sc_latest_sermon;

	/**
	 * Terms short code.
	 *
	 * @var string
	 * @since 1.0.0
	 */
	private string $sc_terms;

	/**
	 * Taxonomy mapping.
	 *
	 * @var array
	 * @since 1.0.0
	 */
	private array $tax_map;

	/**
	 * Initialize object properties.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	protected function __construct() {
		$this->pt_sermon        = DRPPSM_PT_SERMON;
		$this->sc_latest_sermon = DRPPSM_SC_SERMON_LATEST;
		$this->sc_terms         = DRPPSM_SC_TERMS;
		$this->tax_map          = DRPPSM_TAX_MAP;
	}

	public static function exec(): Executable {
		$obj = new self();
		$obj->register();
		return $obj;
	}

	public function register(): ?bool {
		if ( shortcode_exists( $this->sc_latest_sermon ) ) {
			return false;
		}

		// add_shortcode( $this->sc_latest_series, array( $this, 'latest_series' ) );

		add_shortcode( $this->sc_latest_sermon, array( $this, 'display_sermon_latest' ) );

		add_shortcode( DRPPSM_SC_LIST_PODCAST, array( $this, 'podcasts_list' ) );
		add_shortcode( $this->sc_terms, array( $this, 'term_list' ) );

		add_shortcode( DRPPSM_SC_SERMON_IMAGES, array( $this, 'display_images' ) );
		add_shortcode( DRPPSM_SC_SERMONS, array( $this, 'sermons' ) );
		add_shortcode( DRPPSM_SC_SERMON_SORTING, array( $this, 'sermon_sorting' ) );
		return true;
	}

	public function series_image( array $atts ): void {
		$atts = $this->fix_atts( $atts );

		// Default options.
		$args = array(
			'image_class'      => 'latest-series-image',
			'size'             => 'large',
			'show_title'       => 'yes',
			'title_wrapper'    => 'h3',
			'title_class'      => 'latest-series-title',
			'service_type'     => '',
			'show_description' => 'yes',
			'wrapper_class'    => 'latest-series',
		);

		// Join default and user options.
		$args = shortcode_atts( $args, $atts, 'latest_series' );
	}

	/**
	 * Display latest sermon.
	 *
	 * @param array $atts
	 * @return string
	 * @since 1.0.0
	 *
	 *
	 * #### Atts parameter
	 * *defaults shown with ()*
	 * - **filter_by** :Options "series", "preachers", "topics", "books", "service_type"
	 * - **filter_value** : Use the "slug" related to the taxonomy field you want to filter by. ('')
	 * - **image_size** : { sermon_small, sermon_medium, sermon_wide, thumbnail, medium, large, full } ect. (sermon_medium)
	 * - **per_page** : Number of sermons to display. (10)
	 * - **order** : "DESC" for descending; "ASC" for ascending. (DESC)
	 * - **orderby** : Options "date", "id", "none", "title", "name", "rand", "comment_count"
	 *
	 * ```
	 * // Example using all options.
	 * [drppsm_sermon_latest orderby="date" order="desc" filter_by="series" filter_value="at-the-cross" image_size="sermon_medium" per_page="5"]
	 * ```
	 */
	public function display_sermon_latest( array $atts ): string {

		$atts = $this->fix_atts( $atts );

		// Default options.
		$args = array(
			'filter_by'    => '',
			'filter_value' => '',
			'order'        => 'DESC',
			'orderby'      => '',
			'per_page'     => 10,
			'image_size'   => ImageSize::SERMON_MEDIUM,
		);

		// Merge default and user options.
		$args = shortcode_atts( $args, $atts, $this->sc_latest_sermon );

		// Make sure orderby is correct.
		$args['orderby'] = $this->get_order_by( $args );

		// Set query args.
		$query_args = array(
			'post_type'      => $this->pt_sermon,
			'posts_per_page' => $args['per_page'],
			'order'          => $args['order'],
			'orderby'        => $args['orderby'],
			'post_status'    => 'publish',
		);

		$query_args = $this->set_filter( $args, $query_args );
		$query      = new WP_Query( $query_args );

		// Add query to the args.
		$args['query'] = $query;

		$output = '';

		ob_start();
		if ( $query->have_posts() ) {

			while ( $query->have_posts() ) {
				$query->the_post();
				global $post;

				/**
				 * Allows for filtering shortcode output.
				 * - Filters are prefixed with drppsmf_
				 *
				 * @param string $shortcode Shortcode name.
				 * @param string $post Current post.
				 * @param array $args Arguments from shortcode plus defaults.
				 * @return string
				 * @since 1.0.0
				 */
				$override = apply_filters(
					DRPPSMF_SC_OUTPUT_OVRD,
					DRPPSM_SC_SERMON_LATEST,
					$post,
					$args
				);

				if ( $override !== $this->sc_latest_sermon ) {
					$output .= $override;
					continue;
				}
				get_partial( 'content-sermon-archive', $args );

			}

			wp_reset_postdata();
		} else {
			get_partial( 'content-sermon-none' );
		}

		$result = ob_get_clean();

		if ( $output !== '' ) {
			$result = $output;
		}
		return $result;
	}

	/**
	 * Display podcast list.
	 *
	 * @param array $atts
	 * @return void
	 * @since 1.0.0
	 */
	public function podcast_list( array $atts ): void {
		$atts = $this->fix_atts( $atts );
	}

	/**
	 * Display simple unordered term list.
	 *
	 * @param array $atts Attribute list.
	 * @return string
	 * @since 1.0.0
	 *
	 * #### Atts Parameters
	 * - **display** : Options "series", "preachers", "topics", "books", "serives_types".
	 * - **order** : Options "DESC" for descending; "ASC" for ascending.
	 * - **orderby** : Options "name" (default), "id", "count", "slug", "term_group", "none"
	 *
	 * ```
	 * // An example using all three options.
	 * [list_sermons display="preachers" order="DESC" orderby="id"]
	 * ```
	 */
	public function term_list( array $atts ): string {
		$atts = $this->fix_atts( $atts );

		// Default options.
		$defaults = array(
			'display' => 'series',
			'order'   => 'ASC',
			'orderby' => 'name',
		);

		// Join default and user options.
		$args = shortcode_atts( $defaults, $atts, DRPPSM_SC_TERMS );

		// Fix taxonomy
		$args['display'] = $this->convert_taxonomy_name( $args['display'], true );

		$query_args = array(
			'taxonomy' => $args['display'],
			'orderby'  => $args['orderby'],
			'order'    => $args['order'],
		);

		if ( 'date' === $query_args['orderby'] ) {
			$query_args['orderby']        = 'meta_value_num';
			$query_args['meta_key']       = 'sermon_date';
			$query_args['meta_compare']   = '<=';
			$query_args['meta_value_num'] = time();
		}

		// Get items.
		$terms = get_terms( $query_args );

		if ( $terms instanceof WP_Error ) {
			Logger::error(
				array(
					'ERROR' => $terms->get_error_message(),
					$terms->get_error_data(),
				)
			);
			return 'Shortcode Error';
		}

		if ( count( $terms ) > 0 ) {
			// Sort books by order.
			if ( DRPPSM_TAX_BIBLE === $args['display'] && 'book' === $args['orderby'] ) {
				// Book order.
				$books = Bible::BOOKS;

				// Assign every book a number.
				foreach ( $terms as $term ) {
					$ordered_terms[ array_search( $term->name, $books ) ] = $term;
				}

				// Order the numbers (books).
				ksort( $ordered_terms );
				$terms = $ordered_terms;
			}

			$list = '<ul id="list-sermons">';
			foreach ( $terms as $term ) {
				$list .= '<li><a href="' . esc_url( get_term_link( $term, $term->taxonomy ) ) . '" title="' . $term->name . '">' . $term->name . '</a></li>';
			}
			$list .= '</ul>';

			return $list;
		} else {
			// If nothing has been found.
			return 'No ' . $this->convert_taxonomy_name( $args['display'], true ) . ' found.';
		}
	}

	/**
	 * Display sermons.
	 *
	 * @param array $attr
	 * @return void
	 * @since 1.0.0
	 *
	 * #### Atts Parameters
	 * - **per_page** : Define how many sermons to show per page. Overrides the WordPress setting.
	 * - **sermons** : Use comma separated list of individual sermon IDs to show just them.
	 * - **order** : "DESC" for descending; "ASC" for ascending
	 * - **orderby** : Options "date" (default), "id", "none", "title", "name", "rand", "comment_count"
	 * - **filter_by** : Options "series", "preachers", "topics", "books", "service_type". ('')
	 * - **filter_value** : Use the "slug" related to the taxonomy field you want to filter by. ('')
	 * - **hide_pagination** : Set to 1 to hide.
	 * - **image_size** : { sermon_small, sermon_medium, sermon_wide, thumbnail, medium, large, full } any added with add_image_size().
	 * - **year** : Show only sermons created in the specified year.
	 * - **month** : Show only sermons created in the specified month, regardless of year.
	 * - **week** : Show only sermons created in the specified week.
	 * - **day** : Show only sermons created on the specified day.
	 * - **after** : Show only sermons created after the specified date.
	 * - **before** :Show only sermons created before the specified date.
	 */
	public function sermons( array $atts ): void {
		$atts = $this->fix_atts( $atts );
	}

	/**
	 * Display sermon sorting.
	 *
	 * @param array $attr
	 * @return void
	 * @since 1.0.0
	 */
	public function sermon_sorting( array $atts ): void {
		$atts = $this->fix_atts( $atts );
	}

	public function get_latest_series_image_id( $series = 0 ) {
		if ( 0 !== $series && is_numeric( $series ) ) {
			$series = intval( $series );
		} elseif ( $series instanceof WP_Term ) {
			$series = $series->term_id;
		} else {
			return null;
		}

		$associations = array();

		// @todo Create function
		// $associations = sermon_image_plugin_get_associations();
		$tt_id = absint( $series );

		if ( array_key_exists( $tt_id, $associations ) ) {
			$id = absint( $associations[ $tt_id ] );

			return $id;
		}

		return null;
	}

	/**
	 * Fix attributes.
	 *
	 * @param array $atts
	 * @return array
	 * @since 1.0.0
	 */
	private function fix_atts( array $atts ): array {
		foreach ( $atts as &$att ) {
			$att = unquote( $att );
		}
		return $atts;
	}

	private function get_order_by( array $args ): string {
		$orderby = strtolower( $args['orderby'] );

		if ( ! in_array( $orderby, DRPPSM_SERMON_ORDER_BY ) ) {
			$orderby = Settings::get( Settings::ARCHIVE_ORDER_BY, 'date' );
		}

		return $orderby;
	}

	/**
	 * Convert between friendly and unfriendly taxomomy names.
	 *
	 * @param string $name Search for string.
	 * @param bool   $friendly If true will convert friendly => unfriendly else unfriendly => friendly\
	 *               In the event of no conversion orginal $name is returned.
	 *
	 * @return string \
	 *               The converted taxonomy or orginal supplied argument.
	 * @since 1.0.0
	 *
	 * ```
	 * // Example friendly to unfriendly.
	 * $this->convert_taxonomy_name('series',true); # returns drppms_series
	 *
	 * ```
	 */
	private function convert_taxonomy_name( string $name, bool $friendly = false ): string {
		$result = $name;

		// friendly => unfriendly
		if ( $friendly ) {

			// Lets go ahead and pluralize it.
			if ( substr( $name, -1 ) !== 's' ) {
				$name .= 's';
			}

			if ( key_exists( $name, $this->tax_map ) ) {
				$result = $this->tax_map[ $name ];
			}

			// unfriendly => friendly
		} else {

			$match = array_search( $name, $this->tax_map );
			if ( $match ) {
				$result = $match;
			}
		}

		return $result;
	}

	/**
	 * Set filter if needed.
	 *
	 * @param array $args
	 * @param array $query_args
	 * @return array
	 */
	private function set_filter( array $args, array $query_args ) {

		// Check if there is anything to do !
		if ( ! isset( $args['filter_by'] ) || ! isset( $args['filter_value'] ) ) {
			return $query_args;
		}

		if ( empty( $args['filter_by'] ) || empty( $args['filter_value'] ) ) {
			return $query_args;
		}

		// Term string to array.
		$terms = explode( ',', $args['filter_value'] );
		if ( empty( $terms ) ) {
			return $query_args;
		}

		$field = 'slug';
		if ( is_numeric( $terms[0] ) ) {
			$field = 'id';
		}

		Logger::debug( 'HERE 3' );

		foreach ( $terms as &$term ) {
			$term = trim( $term );

			if ( 'id' === $field ) {
				// Remove if it's not an ID.
				if ( ! is_numeric( $term ) ) {
					unset( $term );
					continue;
				}

				// Convert to int.
				$term = intval( $term );
			} else {

				// It's a slug so sanitize it.
				$term = sanitize_title( $term );
			}
		}

		$query_args['tax_query'] = array(
			array(
				'taxonomy' => $this->convert_taxonomy_name( $args['filter_by'], true ),
				'field'    => 'slug',
				'terms'    => $terms,
			),
		);

		$tax_list = array_values( $this->tax_map );
		foreach ( $tax_list as $filter ) {
			if ( ! empty( $_GET[ $filter ] ) ) {
				if ( empty( $query_args['tax_query']['custom'] ) || empty( $query_args['tax_query'] ) ) {
					$query_args['tax_query'] = array();
				}

				$query_args['tax_query'][0][] = array(
					'taxonomy' => $filter,
					'field'    => 'slug',
					'terms'    => sanitize_title_for_query( $_GET[ $filter ] ),
				);

				$query_args['tax_query']['custom'] = true;
			}

			if ( ! empty( $_POST[ $filter ] ) ) {
				if ( empty( $query_args['tax_query']['custom'] ) || empty( $query_args['tax_query'] ) ) {
					$query_args['tax_query'] = array();
				}

				$query_args['tax_query'][0][] = array(
					'taxonomy' => $filter,
					'field'    => 'slug',
					'terms'    => sanitize_title_for_query( $_POST[ $filter ] ),
				);

				$query_args['tax_query']['custom'] = true;
			}
		}

		if ( ! empty( $query_args['tax_query'] ) && count( $query_args['tax_query'] ) > 1 && ! empty( $query_args['tax_query']['custom'] ) ) {
			unset( $query_args['tax_query']['custom'] );
		}

		return $query_args;
	}







	/**
	 *
	 *
	 * #### Atts parameter
	 * *defaults shown with ()*
	 *
	 * - **image_class** : Opptions any CSS class you want applied to the image. ( latest-series-image )
	 * - **image_size** : { sermon_small, sermon_medium, sermon_wide, thumbnail, medium, large, full } ect.
	 * - **show_title** : Show or hide the series title. ( true )
	 * - **title_wrapper** Any HTML tag p, h1, h2, h3, h4, h5, h6, div ( h3)
	 * - **title_class** Any CSS class you want applied to the title wrapper. ( latest-series-title )
	 * - **service_type** Use the service type slug to show the latest series from a particular service type.
	 * - **show_desc** – Show or hide the series description. (false )
	 * - **wrapper_class** : Any CSS class you want applied to the div which wraps the output. { default: latest-series }        */
}
