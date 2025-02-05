<?php
/**
 * Shortcode wrapper start.
 *
 * @package     DRPPSM
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2024, Daryl Peterson
 * @license     https://www.gnu.org/licenses/gpl-3.0.txt
 * @since       1.0.0
 */

namespace DRPPSM;

?>
<div class="drppsm-error-wrap">
	<article class="drppsm-template-error">
		<b><?php echo esc_html( $title ); ?></b>:<i><?php echo esc_html( $error ); ?></i>
	</article>
</div>