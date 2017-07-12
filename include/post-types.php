<?php
add_action( 'init', function() {
	$labels = [
		'name'                  => _x( 'Servers', 'Post Type General Name', "ass-server-status" ),
		'singular_name'         => _x( 'Server', 'Post Type Singular Name', "ass-server-status" ),
		'menu_name'             => __( 'Server', "ass-server-status" ),
		'name_admin_bar'        => __( 'Server', "ass-server-status" ),
		'archives'              => __( 'Server Archives', "ass-server-status" ),
		'attributes'            => __( 'Server Attributes', "ass-server-status" ),
		'parent_item_colon'     => __( 'Parent Server', "ass-server-status" ),
		'all_items'             => __( 'All Servers', "ass-server-status" ),
		'add_new_item'          => __( 'Add New Server', "ass-server-status" ),
		'add_new'               => __( 'Add New', "ass-server-status" ),
		'new_item'              => __( 'New Server', "ass-server-status" ),
		'edit_item'             => __( 'Edit Server', "ass-server-status" ),
		'update_item'           => __( 'Update Server', "ass-server-status" ),
		'view_item'             => __( 'View Server', "ass-server-status" ),
		'view_items'            => __( 'View Servers', "ass-server-status" ),
		'search_items'          => __( 'Search Servers', "ass-server-status" ),
		'not_found'             => __( 'Not found', "ass-server-status" ),
		'not_found_in_trash'    => __( 'Not found in Trash', "ass-server-status" ),
		'featured_image'        => __( 'Featured Image', "ass-server-status" ),
		'set_featured_image'    => __( 'Set featured image', "ass-server-status" ),
		'remove_featured_image' => __( 'Remove featured image', "ass-server-status" ),
		'use_featured_image'    => __( 'Use as featured image', "ass-server-status" ),
		'insert_into_item'      => __( 'Insert into item', "ass-server-status" ),
		'uploaded_to_this_item' => __( 'Uploaded to this item', "ass-server-status" ),
		'items_list'            => __( 'Items list', "ass-server-status" ),
		'items_list_navigation' => __( 'Items list navigation', "ass-server-status" ),
		'filter_items_list'     => __( 'Filter items list', "ass-server-status" ),
	];

	$args = [
		'label'                 => __( 'Server', "ass-server-status" ),
		'description'           => __( 'Represents the given details for a server.', "ass-server-status" ),
		'labels'                => $labels,
		'supports'              => [ 'title', 'editor', 'page-attributes' ],
		'hierarchical'          => false,
		'public'                => false,
		'show_ui'               => false,
		'show_in_menu'          => false,
		'menu_position'         => 5,
		'show_in_admin_bar'     => false,
		'show_in_nav_menus'     => false,
		'can_export'            => true,
		'has_archive'           => false,		
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'capability_type'       => 'page',
	];

	register_post_type( 'ass-server', $args );
}, 0 );