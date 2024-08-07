<?php
/**
 * Sermon post type configuration.
 *
 * @package Sermon Manager
 */

namespace DRPPSM;

$permalinks = App::init()->permalinks();
use DRPPSM\Constants\Caps;
use DRPPSM\Constants\PT;

return array(
	'labels'              => array(
		'name'                  => __( 'Sermons', 'drppsm' ),
		'singular_name'         => __( 'Sermon', 'drppsm' ),
		'all_items'             => __( 'Sermons', 'drppsm' ),
		'menu_name'             => _x( 'Sermons', 'menu', 'drppsm' ),
		'add_new'               => __( 'Add New', 'drppsm' ),
		'add_new_item'          => __( 'Add new sermon', 'drppsm' ),
		'edit'                  => __( 'Edit', 'drppsm' ),
		'edit_item'             => __( 'Edit sermon', 'drppsm' ),
		'new_item'              => __( 'New sermon', 'drppsm' ),
		'view'                  => __( 'View sermon', 'drppsm' ),
		'view_item'             => __( 'View sermon', 'drppsm' ),
		'search_items'          => __( 'Search sermon', 'drppsm' ),
		'not_found'             => __( 'No sermons found', 'drppsm' ),
		'not_found_in_trash'    => __( 'No sermons found in trash', 'drppsm' ),
		'featured_image'        => __( 'Sermon image', 'drppsm' ),
		'set_featured_image'    => __( 'Set sermon image', 'drppsm' ),
		'remove_featured_image' => __( 'Remove sermon image', 'drppsm' ),
		'use_featured_image'    => __( 'Use as sermon image', 'drppsm' ),
		'insert_into_item'      => __( 'Insert to sermon', 'drppsm' ),
		'uploaded_to_this_item' => __( 'Uploaded to this sermon', 'drppsm' ),
		'filter_items_list'     => __( 'Filter sermon', 'drppsm' ),
		'items_list_navigation' => __( 'Sermon navigation', 'drppsm' ),
		'items_list'            => __( 'Sermon list', 'drppsm' ),
	),
	'public'              => true,
	'show_ui'             => true,
	'capability_type'     => PT::SERMON,
	'capabilities'        => array(
		Caps::MANAGE_CATAGORIES => Caps::MANAGE_CATAGORIES,
		Caps::MANAGE_SETTINGS   => Caps::MANAGE_SETTINGS,
	),
	'map_meta_cap'        => true,
	'publicly_queryable'  => true,
	'exclude_from_search' => false,
	'show_in_menu'        => true,
	'menu_icon'           => 'dashicons-drppsm-holy-spirit',
	'hierarchical'        => false,
	'rewrite'             => array(
		'slug'       => $permalinks[ PT::SERMON ],
		'with_front' => false,
	),
	'query_var'           => true,
	'show_in_nav_menus'   => true,
	'show_in_rest'        => true,
	'has_archive'         => true,

	'supports'            => array(
		'title',
		'thumbnail',
		'publicize',
		'wpcom-markdown',
		'comments',
		'entry-views',
		'elementor',
		'excerpt',
		'revisions',
		'author',
		// 'editor',
	),
);
