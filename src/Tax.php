<?php
/**
 * Taxonomy constants.
 *
 * @package     Proclaim Sermon Manager
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2024, Daryl Peterson
 * @license     https://www.gnu.org/licenses/gpl-3.0.txt
 * @since       1.0.0
 */

namespace DRPPSM;

use DRPPSM\Constants\Caps;
use DRPPSM\Interfaces\Executable;

/**
 * Taxonomy constants.
 *
 * @package     Proclaim Sermon Manager
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2024, Daryl Peterson
 * @license     https://www.gnu.org/licenses/gpl-3.0.txt
 * @since       1.0.0
 */
class Tax implements Executable {

	public const BIBLE_BOOK   = 'drppsm_bible';
	public const PREACHER     = 'drppsm_preacher';
	public const SERVICE_TYPE = 'drppsm_stype';
	public const SERIES       = 'drppsm_series';
	public const TOPICS       = 'drppsm_topics';


	public const LIST = array(
		self::BIBLE_BOOK,
		self::PREACHER,
		self::SERVICE_TYPE,
		self::SERIES,
		self::TOPICS,
	);

	public const CAPS = array(
		'manage_terms' => Caps::MANAGE_CATAGORIES,
		'edit_terms'   => Caps::MANAGE_CATAGORIES,
		'delete_terms' => Caps::MANAGE_CATAGORIES,
		'assign_terms' => Caps::MANAGE_CATAGORIES,
	);


	protected function __construct() {
	}

	/**
	 * Initailize and register hooks.
	 *
	 * @return Tax
	 * @since 1.0.0
	 */
	public static function exec(): Tax {
		$obj = new self();
		return $obj;
	}
}
