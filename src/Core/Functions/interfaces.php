<?php
/**
 * Helper methods to get interfaces.
 *
 * @package     Proclaim Sermon Manager
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2024, Daryl Peterson
 * @license     https://www.gnu.org/licenses/gpl-3.0.txt
 * @since       1.0.0
 */

namespace DRPPSM;

defined( 'ABSPATH' ) || exit;

use DRPPSM\Interfaces\BibleLoaderInt;
use DRPPSM\Interfaces\OptionsInt;
use DRPPSM\Interfaces\RequirementsInt;
use DRPPSM\Interfaces\RolesInt;
use DRPPSM\Interfaces\TextDomainInt;
use DRPPSM\Interfaces\ImageSizeInt;
use DRPPSM\Interfaces\NoticeInt;
use DRPPSM\Interfaces\PermaLinkInt;
use DRPPSM\Logging\LogWritterInt;

/**
 * Get requirements interface.
 *
 * @return RequirementsInt
 * @since 1.0.0
 */
function requirements(): RequirementsInt {
	return app()->get( RequirementsInt::class );
}

/**
 * Get roles interface.
 *
 * @return RolesInt
 * @since 1.0.0
 */
function roles(): RolesInt {
	return app()->get( RolesInt::class );
}

/**
 * Get options interface.
 *
 * @return OptionsInt Options inteface.
 * @since 1.0.0
 */
function options(): OptionsInt {
	return app()->get( OptionsInt::class );
}

/**
 * Get text domain interface.
 *
 * @return TextDomainInt
 * @since 1.0.0
 */
function textdomain(): TextDomainInt {
	return app()->get( TextDomainInt::class );
}

/**
 * Get image size interface.
 *
 * @return ImageSizeInt
 * @since 1.0.0
 */
function imagesize(): ImageSizeInt {
	return app()->get( ImageSizeInt::class );
}

/**
 * Get notice interface.
 *
 * @return NoticeInt
 * @since 1.0.0
 */
function notice(): NoticeInt {
	return app()->get( NoticeInt::class );
}

/**
 * Get permalink interface.
 *
 * @return array
 * @since 1.0.0
 */
function permalinks(): array {
	return app()->get( PermaLinkInt::class )->get();
}

/**
 * Get bible loader interface.
 *
 * @return BibleLoaderInt
 * @since 1.0.0
 */
function bible_loader(): BibleLoaderInt {
	return app()->get( BibleLoaderInt::class );
}

/**
 * Get log writter interface.
 *
 * @return LogWritterInt
 * @since 1.0.0
 */
function log_writter(): LogWritterInt {
	return app()->get( LogWritterInt::class );
}
