<?php
/**
 * Permalinks interface
 *
 * @package     Proclaim Sermon Manager
 *
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2024, Daryl Peterson
 * @license     https://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @since       1.0.0
 */

namespace DRPPSM\Interfaces;

/**
 * Permalinks interface
 *
 * @package     Proclaim Sermon Manager
 *
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2024, Daryl Peterson
 * @license     https://www.gnu.org/licenses/gpl-3.0.txt
 * @since       1.0.0
 */
interface PermaLinkInt extends Initable, Registrable {

	/**
	 * Return permalink array.
	 *
	 * @since 1.0.0
	 */
	public function get(): array;
}
