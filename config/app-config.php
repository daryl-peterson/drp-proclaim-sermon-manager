<?php
/**
 * App configuration.
 *
 * @package     Proclaim Sermon Manager
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2024, Daryl Peterson
 * @license     https://www.gnu.org/licenses/gpl-3.0.txt
 * @since       1.0.0
 */

namespace DRPPSM;

defined( 'ABSPATH' ) || exit;

use DRPPSM\Interfaces\BibleLoadInt;
use DRPPSM\Interfaces\NoticeInt;
use DRPPSM\Interfaces\OptionsInt;
use DRPPSM\Interfaces\PermaLinkInt;
use DRPPSM\Interfaces\PluginInt;
use DRPPSM\Interfaces\PostTypeSetupInt;
use DRPPSM\Interfaces\RequirementsInt;
use DRPPSM\Interfaces\RolesInt;
use DRPPSM\Interfaces\TextDomainInt;
use DRPPSM\Interfaces\ImageSizeInt;

return array(
	NoticeInt::class        => function (): NoticeInt {
		return Notice::init();
	},

	OptionsInt::class       => function (): OptionsInt {
		return Options::init();
	},

	RolesInt::class         => function (): RolesInt {
		return Roles::exec();
	},

	RequirementsInt::class  => function (): RequirementsInt {
		return Requirements::exec();
	},

	TextDomainInt::class    => function (): TextDomainInt {
		return TextDomain::exec();
	},

	ImageSizeInt::class     => function (): ImageSizeInt {
		return ImageSize::exec();
	},

	BibleLoadInt::class     => function (): BibleLoadInt {
		return BibleLoad::exec();
	},

	PermaLinkInt::class     => PermaLinks::class,
	PluginInt::class        => Plugin::class,

	PostTypeSetupInt::class => PostTypeSetup::class,
);
