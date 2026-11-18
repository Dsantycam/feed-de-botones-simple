<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

$feeds = get_posts(
	array(
		'post_type'      => 'fdbs_feed',
		'post_status'    => 'any',
		'numberposts'    => -1,
		'fields'         => 'ids',
		'suppress_filters' => false,
	)
);

if ( ! empty( $feeds ) && is_array( $feeds ) ) {
	foreach ( $feeds as $feed_id ) {
		delete_post_meta( $feed_id, '_fdbs_enabled_fields' );
		delete_post_meta( $feed_id, '_fdbs_buttons' );
		delete_post_meta( $feed_id, '_fdbs_layout' );
		wp_delete_post( $feed_id, true );
	}
}