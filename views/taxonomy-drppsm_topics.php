<?php
/**
 * Sermon topic taxonomy.
 *
 * @package     DRPPSM/Views/Partials
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2024, Daryl Peterson
 * @license     https://www.gnu.org/licenses/gpl-3.0.txt
 * @since       1.0.0
 */

namespace DRPPSM;

defined( 'ABSPATH' ) || exit;

if ( ! did_action( 'get_header' ) ) {
	get_header();
}

get_partial( 'sermon-wrapper-start' );
get_partial( 'content-sermon-filtering' );

if ( have_posts() ) {

	while ( have_posts() ) {
		the_post();
		sermon_excerpt();
	}
	wp_reset_postdata();
} else {
	get_partial( 'no-posts' );
}

get_partial( 'sermon-wrapper-end' );

if ( ! did_action( 'get_footer' ) ) {
	get_footer();
}
