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

?>
<div class="drppsm-single-meta-item">
	<div class="drppsm-single-title-prefix"></div>
	<div class="drppsm-single-title-text">
		<a href="<?php the_permalink( $post->id ); ?>">
			<h4><?php the_title( '', '' ); ?></h4>
		</a>
	</div>
</div>






