<?php

/**
 * Sermon single series meta
 *
 * @package     DRPPSM/Views/Partials
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2024, Daryl Peterson
 * @license     https://www.gnu.org/licenses/gpl-3.0.txt
 * @since       1.0.0
 */

namespace DRPPSM;

defined( 'ABSPATH' ) || exit;

$tax_series = DRPPSM_TAX_SERIES;

if ( ! has_term( '', $tax_series, $post->ID ) ) {
	return;
}

?>

<div class="drppsm-single-meta-item">
	<div class="drppsm-single-meta-prefix"><?php echo ucwords( Settings::get( Settings::SERIES ) ); ?></div>
	<div class="drppsm-single-meta-text"><?php echo get_the_term_list( $post->ID, $tax_series ); ?></div>
</div>
