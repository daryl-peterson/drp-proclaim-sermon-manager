<?php
/**
 * Taxonomy template.
 *
 * @package     DRPPSM
 * @subpackage  Template
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2024, Daryl Peterson
 * @license     https://www.gnu.org/licenses/gpl-3.0.txt
 * @since       1.0.0
 */

namespace DRPPSM;

$qv_tax  = get_query_var( 'taxonomy' );
$qv_term = get_query_var( $term_name );
$qv_play = get_query_var( 'play' );

if ( ! did_action( 'get_header' ) ) {
	get_header();
}

get_partial( Template::WRAPPER_START );


if ( empty( $qv_term ) ) {
	new TaxImageList(
		array(
			'display' => $qv_tax,
			'size'    => get_tax_image_size( 'full', $term_name ),
		)
	);
} elseif ( have_posts() ) {
	new TaxArchive( $qv_tax, $qv_term );
	wp_reset_postdata();
} else {
	get_partial( 'no-posts' );
}

get_partial( Template::WRAPPER_END );

if ( ! did_action( 'get_footer' ) ) {
	get_footer();
}
