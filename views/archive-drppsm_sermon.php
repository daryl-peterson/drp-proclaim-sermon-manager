<?php
/**
 * Sermon archive template.
 *
 * @package     DRPPSM
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2024, Daryl Peterson
 * @license     https://www.gnu.org/licenses/gpl-3.0.txt
 * @since       1.0.0
 */

namespace DRPPSM;

$qv_tax  = get_query_var( 'taxonomy' );
$qv_term = get_query_var( 'type' );



/**
 * Make sure array exist. Other functions will need it.
 */
if ( ! isset( $args ) ) {
	$args = array();
}

if ( ! did_action( 'get_header' ) ) {
	get_header();
}

get_partial( 'sermon-wrapper-start' );
echo sermon_sorting();

if ( have_posts() ) {
	new SermonImageList();

	wp_reset_postdata();
} else {
	get_partial( 'no-posts' );
}

get_partial( 'sermon-wrapper-end' );

if ( ! did_action( 'get_footer' ) ) {
	get_footer();
}
