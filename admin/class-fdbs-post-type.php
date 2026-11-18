<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FDBS_Post_Type {

	public static function register() {
		$labels = array(
			'name'               => __( 'Feeds', 'feed-de-botones-simple' ),
			'singular_name'      => __( 'Feed', 'feed-de-botones-simple' ),
			'menu_name'          => __( 'Feeds', 'feed-de-botones-simple' ),
			'name_admin_bar'     => __( 'Feed', 'feed-de-botones-simple' ),
			'add_new'            => __( 'Agregar nuevo', 'feed-de-botones-simple' ),
			'add_new_item'       => __( 'Agregar nuevo feed', 'feed-de-botones-simple' ),
			'new_item'           => __( 'Nuevo feed', 'feed-de-botones-simple' ),
			'edit_item'          => __( 'Editar feed', 'feed-de-botones-simple' ),
			'view_item'          => __( 'Ver feed', 'feed-de-botones-simple' ),
			'all_items'          => __( 'Todos los feeds', 'feed-de-botones-simple' ),
			'search_items'       => __( 'Buscar feeds', 'feed-de-botones-simple' ),
			'not_found'          => __( 'No se encontraron feeds.', 'feed-de-botones-simple' ),
			'not_found_in_trash' => __( 'No se encontraron feeds en la papelera.', 'feed-de-botones-simple' ),
		);

		$args = array(
			'labels'             => $labels,
			'public'             => false,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'show_in_admin_bar'  => true,
			'show_in_nav_menus'  => false,
			'show_in_rest'       => false,
			'has_archive'        => false,
			'rewrite'            => false,
			'menu_position'      => 25,
			'menu_icon'          => 'dashicons-menu-alt',
			'supports'           => array( 'title' ),
			'capability_type'    => 'post',
		);

		register_post_type( 'fdbs_feed', $args );
	}
}