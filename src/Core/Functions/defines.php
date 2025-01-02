<?php
/**
 * Define statements here.
 *
 * @package     Proclaim Sermon Manager
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2024, Daryl Peterson
 * @license     https://www.gnu.org/licenses/gpl-3.0.txt
 * @since       1.0.0
 */
namespace DRPPSM;

if ( defined( 'DRPPSM_DEFINES' ) ) {
	return;
}

define( 'DRPPSM_DEFINES', true );

/**
 * UNIT TESTING
 */
define(
	'DRPPSM_TESTING',
	'PHPUNIT_TESTING'
);

/**
 * SERMON DEFINES
 */
define(
	'DRPPSM_SERMON_ORDER_BY',
	array(
		'date',
		'preached',
		'date_preached',
		'published',
		'date_published',
		'id',
		'none',
		'title',
		'name',
		'rand',
		'comment_count',
		'post_date',
	)
);

/**
 * SHORTCODES DEFINES
 */
define( 'DRPPSM_SC_LATEST_SERIES', 'drppsm_latest_series' );
define( 'DRPPSM_SC_LATEST_SERMON', 'drppsm_latest_sermon' );
define( 'DRPPSM_SC_LIST_PODCAST', 'drppsm_list_podcasts' );
define( 'DRPPSM_SC_LIST_SERMONS', 'drppsm_list_sermons' );
define( 'DRPPSM_SC_SERMONS', 'drppsm_sermons' );
define( 'DRPPSM_SC_SERMON_IMAGES', 'drppsm_sermon_images' );
define( 'DRPPSM_SC_SERMON_SORTING', 'drppsm_sermon_sorting' );



/**
 * MESSAGES
 */
define(
	'DRPPSM_MSG_FILE_NOT_EXIST',
	__( 'File does not exist.', 'drppsm' )
);

define(
	'DRPPSM_MSG_FAILED_PARTIAL',
	__( 'Failed loading partial file.', 'drppsm' )
);


define(
	'DRPPSM_MSG_LABEL_SINGLE',
	__( 'The label should be in the singular form.', 'drppsm' )
);



/*
|--------------------------------------------------------------------------
| Filters
|--------------------------------------------------------------------------
| This sections begins the list of defined filters.
*/

/**
 * Allows for filtering shortcode output.
 * - Filters are prefixed with drppsmf_
 *
 * @param string $shortcode Shortcode name.
 * @param string $post Current post.
 * @param array $args Arguments from shortcode plus defaults.
 * @return string
 * @since 1.0.0
 */
define( 'DRPPSMF_SC_OUTPUT_OVRD', 'drppsmf_sc_output_ovrd' );


/**
 * Allows for filtering admin sermon inputs.
 * - Filters are prefixed with drppsmf_
 *
 * @param array $output HTML Inputs for admin sermons page.
 * @since 1.0.0
 */
define( 'DRPPSMF_ADMIN_SERMON', 'drppsmf_admin_sermons' );


/**
 * Allows for filtering the name of the template with path.
 * - Filters are prefixed with drppsmf_
 * - Used in Templates class.
 *
 * @param string $name File name.
 * @param array  $args Array of variables to pass to template.
 * @return string $name File name.
 * @since 1.0.0
 */
define( 'DRPPSMF_TPL_PARTIAL', 'drppsm_tpl_partial' );


/**
 * Filters for remove submenus of admin menu.
 * - Filters are prefixed with drppsmf_
 * - Fires before before sub menus are displayed.
 *
 * @param array $submenus Sub menus to hide.
 * @return array
 * @since 1.0.0
 */
define( 'DRPPSMF_SETTINGS_RSM', 'drppsmf_settings_rsm' );



define( 'DRPPSMF_SETTINGS_MM', 'drppsmf_settings_mm' );

/**
 * Get pagination links
 * - Filters are prefixed with drppsmf_
 *
 * @param integer $items Total records.
 * @param integer $limit Per page.
 * @param integer $page Page number.
 * @return string
 * @since 1.0.0
 */
define( 'DRPPSMF_PAGINATION_GET', 'drppsm_pagination_get' );



/**
 * Filters the date a post was preached
 *
 * @param string $date                  Modified and sanitized date
 * @param string $orig_date             Original date from the database
 * @param string $format                Date format
 * @param bool   $force_unix_sanitation If the sanitation is forced
 * @since 1.0.0
 */
define( 'DRPPSMF_SERMON_DATES', 'drppsmf_sermon_dates' );


/**
 * Allows for filtering taxonomy sorting.
 * - Filters are prefixed with drppsmf_
 * - Used in Templates.php
 *
 * ```php
 * # Example parameters
 * array(
 *      array(
 *          'className' => 'drppsm-sort-preacher',
 *          'taxonomy'  => Tax::PREACHER,
 *          'title'     => get_taxonomy_field( Tax::PREACHER, 'singular_name' ),
 *      ),
 * );
 *
 * ```
 *
 * @param array $args Array of arrays.
 * @since 1.0.0
 */
define( 'DRPPSMF_TAX_SORTING', 'drppsm_fltr_tax_sorting' );



/*
|--------------------------------------------------------------------------
| Actions
|--------------------------------------------------------------------------
| This sections begins the list of defined actions.
*/


/**
 * Action after template error occurs.
 * - Actions are prefixed with drppsma_
 *
 * @return void
 * @since 1.0.0
 */
define( 'DRPPSMA_TPL_ERROR', 'drppsma_tpl_error' );

/**
 * Action to flush rewrite rules.
 * - Actions are prefixed with drppsma_
 *
 * @return void
 * @since 1.0.0
 */
define( 'DRPPSMA_FLUSH_REWRITE', 'drppsma_flush_rewrite' );

















define(
	'DRPPSM_SETTINGS_COMMENTS_NAME',
	__( 'Allow Comments', 'drppsm' )
);


define(
	'DRPPSM_SETTINGS_PLAYER_NAME',
	__( 'Audio & Video Player', 'drppsm' )
);

define(
	'DRPPSM_SETTINGS_MENU_ICON_NAME',
	__( 'Menu Icon', 'drppsm' )
);

define(
	'DRPPSM_SETTINGS_DATE_FORMAT_NAME',
	__( 'Sermon Date Format', 'drppsm' )
);

define(
	'DRPPSM_SETTINGS_SERMON_COUNT_NAME',
	__( 'Sermons Per Page', 'drppsm' )
);

define(
	'DRPPSM_SETTING_PREACHER_LABEL',
	__( 'Preacher', 'drppsm' )
);
