<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FDBS_Admin_Columns {

	public function init() {
		add_filter( 'manage_fdbs_feed_posts_columns', array( $this, 'set_columns' ) );
		add_action( 'manage_fdbs_feed_posts_custom_column', array( $this, 'render_column' ), 10, 2 );

		add_filter( 'post_row_actions', array( $this, 'add_duplicate_action' ), 10, 2 );
		add_action( 'admin_action_fdbs_duplicate_feed', array( $this, 'handle_duplicate_action' ) );
	}

	public function set_columns( $columns ) {
		$new_columns = array();

		foreach ( $columns as $key => $label ) {
			$new_columns[ $key ] = $label;

			if ( 'title' === $key ) {
				$new_columns['fdbs_shortcode'] = __( 'Shortcode', 'feed-de-botones-simple' );
				$new_columns['fdbs_buttons_count'] = __( 'Botones', 'feed-de-botones-simple' );
			}
		}

		return $new_columns;
	}

	public function render_column( $column, $post_id ) {
		if ( 'fdbs_shortcode' === $column ) {
			$shortcode = FDBS_Helpers::get_shortcode( $post_id );
			?>
			<input
				type="text"
				readonly
				class="widefat"
				value="<?php echo esc_attr( $shortcode ); ?>"
				onclick="this.select();"
			>
			<?php
		}

		if ( 'fdbs_buttons_count' === $column ) {
        	$buttons = get_post_meta( $post_id, '_fdbs_buttons', true );
        
        	if ( ! is_array( $buttons ) ) {
        		$buttons = array();
        	}
        
        	$buttons = array_filter(
        		$buttons,
        		function ( $button ) {
        			return is_array( $button ) && ! empty( $button['title'] );
        		}
        	);
        
        	echo esc_html( count( $buttons ) );
        }
	}

	public function add_duplicate_action( $actions, $post ) {
		if ( 'fdbs_feed' !== $post->post_type ) {
			return $actions;
		}

		if ( ! current_user_can( 'edit_posts' ) ) {
			return $actions;
		}

		$url = wp_nonce_url(
			admin_url( 'admin.php?action=fdbs_duplicate_feed&post=' . $post->ID ),
			'fdbs_duplicate_feed_' . $post->ID
		);

		$actions['fdbs_duplicate'] = '<a href="' . esc_url( $url ) . '">' . esc_html__( 'Duplicar', 'feed-de-botones-simple' ) . '</a>';

		return $actions;
	}

	public function handle_duplicate_action() {
		if ( ! isset( $_GET['post'] ) ) {
			wp_die( esc_html__( 'Feed no válido.', 'feed-de-botones-simple' ) );
		}

		$post_id = absint( $_GET['post'] );

		if ( ! $post_id ) {
			wp_die( esc_html__( 'Feed no válido.', 'feed-de-botones-simple' ) );
		}

		check_admin_referer( 'fdbs_duplicate_feed_' . $post_id );

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			wp_die( esc_html__( 'No tienes permisos para duplicar este feed.', 'feed-de-botones-simple' ) );
		}

		$original = get_post( $post_id );

		if ( ! $original || 'fdbs_feed' !== $original->post_type ) {
			wp_die( esc_html__( 'Feed no encontrado.', 'feed-de-botones-simple' ) );
		}

		$new_post_id = wp_insert_post(
			array(
				'post_type'   => 'fdbs_feed',
				'post_status' => 'draft',
				'post_title'  => $original->post_title . ' (copia)',
			)
		);

		if ( is_wp_error( $new_post_id ) || ! $new_post_id ) {
			wp_die( esc_html__( 'No se pudo duplicar el feed.', 'feed-de-botones-simple' ) );
		}

		$enabled_fields = get_post_meta( $post_id, '_fdbs_enabled_fields', true );
		$buttons        = get_post_meta( $post_id, '_fdbs_buttons', true );

		if ( is_array( $enabled_fields ) ) {
			update_post_meta( $new_post_id, '_fdbs_enabled_fields', $enabled_fields );
		}

		if ( is_array( $buttons ) ) {
			update_post_meta( $new_post_id, '_fdbs_buttons', $buttons );
		}

		wp_safe_redirect(
			admin_url( 'post.php?post=' . $new_post_id . '&action=edit' )
		);
		exit;
	}
}