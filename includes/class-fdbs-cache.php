<?php
/**
 * Limpia la caché de las páginas que muestran un feed cuando ese feed cambia.
 *
 * El shortcode se genera en cada visita, pero los plugins de caché de página
 * (y la caché de elementos de Elementor) guardan el HTML ya armado de la página
 * que lo contiene. Esa página no "cambió", así que su caché no se renueva sola
 * al editar el feed. Aquí se buscan las páginas que usan el feed y se purgan.
 *
 * Todo va protegido: si un plugin de caché no está, su llamada simplemente no
 * se hace, y un error en la purga nunca impide guardar el feed.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FDBS_Cache {

	const NOTICE_TRANSIENT = 'fdbs_cache_notice_';

	public function init() {
		// Prioridad 20: después de que FDBS_Save_Feed guarde los botones.
		add_action( 'save_post_fdbs_feed', array( $this, 'on_feed_saved' ), 20, 2 );
		add_action( 'trashed_post', array( $this, 'on_feed_changed' ) );
		add_action( 'untrashed_post', array( $this, 'on_feed_changed' ) );
		add_action( 'before_delete_post', array( $this, 'on_feed_changed' ) );
		add_action( 'admin_notices', array( $this, 'render_notice' ) );
	}

	public function on_feed_saved( $post_id, $post ) {
		if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
			return;
		}

		if ( ! $post instanceof WP_Post || 'auto-draft' === $post->post_status ) {
			return;
		}

		$this->purge_for_feed( $post_id );
	}

	public function on_feed_changed( $post_id ) {
		if ( 'fdbs_feed' !== get_post_type( $post_id ) ) {
			return;
		}

		$this->purge_for_feed( $post_id );
	}

	/**
	 * Busca dónde se usa el feed y limpia la caché de esos sitios.
	 *
	 * @return array{posts:int[],all:bool}
	 */
	public function purge_for_feed( $feed_id ) {
		$result = array(
			'posts' => array(),
			'all'   => false,
		);

		try {
			$usage = $this->find_usage( $feed_id );

			foreach ( $usage['posts'] as $post_id ) {
				$this->purge_post( $post_id );
			}

			if ( $usage['all'] || apply_filters( 'fdbs_purge_all_cache_on_feed_save', false, $feed_id ) ) {
				$this->purge_all();
				$usage['all'] = true;
			}

			$result = $usage;
		} catch ( \Throwable $e ) {
			// Nunca romper el guardado del feed por un fallo de un plugin de caché.
			$result['error'] = $e->getMessage();
		}

		$user_id = get_current_user_id();
		if ( $user_id && ( $result['posts'] || $result['all'] ) ) {
			set_transient( self::NOTICE_TRANSIENT . $user_id, $result, MINUTE_IN_SECONDS );
		}

		return $result;
	}

	/**
	 * Entradas/páginas que muestran el feed, y si hace falta purgar todo
	 * (feed dentro de una plantilla, bloque reutilizable o widget).
	 *
	 * @return array{posts:int[],all:bool}
	 */
	public function find_usage( $feed_id ) {
		global $wpdb;

		$pattern = $this->get_shortcode_pattern( $feed_id );
		$like    = '%' . $wpdb->esc_like( 'fdbs_feed' ) . '%';
		$found   = array();

		// 1) Contenido normal (editor de bloques, clásico, Divi, WPBakery...).
		$rows = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT ID, post_content FROM {$wpdb->posts}
				WHERE post_type NOT IN ('revision', 'fdbs_feed')
				AND post_status IN ('publish', 'private', 'future')
				AND post_content LIKE %s",
				$like
			)
		);

		foreach ( $rows as $row ) {
			if ( preg_match( $pattern, $row->post_content ) ) {
				$found[] = (int) $row->ID;
			}
		}

		// 2) Constructores que guardan su contenido en metadatos (Elementor, Beaver, Bricks...).
		$rows = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT pm.post_id, pm.meta_value FROM {$wpdb->postmeta} pm
				INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
				WHERE p.post_type NOT IN ('revision', 'fdbs_feed')
				AND p.post_status IN ('publish', 'private', 'future')
				AND pm.meta_value LIKE %s",
				$like
			)
		);

		foreach ( $rows as $row ) {
			if ( preg_match( $pattern, $row->meta_value ) ) {
				$found[] = (int) $row->post_id;
			}
		}

		$found = array_values( array_unique( $found ) );
		$posts = array();
		$all   = false;

		foreach ( $found as $post_id ) {
			$type = get_post_type( $post_id );

			// Plantillas, partes de plantilla, bloques reutilizables, plantillas de Elementor:
			// aparecen en páginas que no podemos enumerar, así que se purga todo.
			if ( ! $type || ! is_post_type_viewable( $type ) ) {
				$all = true;
				continue;
			}

			$posts[] = $post_id;
		}

		// 3) Widgets (texto, HTML, bloque).
		if ( ! $all ) {
			foreach ( array( 'widget_text', 'widget_custom_html', 'widget_block' ) as $option ) {
				$value = get_option( $option );

				if ( $value && preg_match( $pattern, maybe_serialize( $value ) ) ) {
					$all = true;
					break;
				}
			}
		}

		return array(
			'posts' => apply_filters( 'fdbs_feed_usage_posts', $posts, $feed_id ),
			'all'   => $all,
		);
	}

	/**
	 * Expresión que reconoce [fdbs_feed id="X"] o [fdbs_feed slug="..."] de este feed,
	 * también con las comillas escapadas que guardan los constructores en JSON.
	 */
	public function get_shortcode_pattern( $feed_id ) {
		$feed_id = absint( $feed_id );
		$slug    = get_post_field( 'post_name', $feed_id );
		$quotes  = '[\\\\"\'\s]*';

		$alternatives = array( 'id\s*=' . $quotes . $feed_id . '(?![0-9])' );

		if ( is_string( $slug ) && '' !== $slug ) {
			// Los feeds en la papelera tienen el slug con el sufijo "__trashed".
			$slug           = preg_replace( '/__trashed(-\d+)?$/', '', $slug );
			$alternatives[] = 'slug\s*=' . $quotes . preg_quote( $slug, '/' ) . '(?![\w-])';
		}

		return '/fdbs_feed[^\]]*?(?:' . implode( '|', $alternatives ) . ')/i';
	}

	/**
	 * Limpia la caché de una sola entrada/página en los plugins de caché más usados.
	 */
	public function purge_post( $post_id ) {
		$post_id = (int) $post_id;
		$url     = get_permalink( $post_id );

		clean_post_cache( $post_id );

		// Elementor 3.22+: caché de elementos (guarda el HTML del widget de shortcode).
		delete_post_meta( $post_id, '_elementor_element_cache' );

		$this->safely(
			function () use ( $post_id, $url ) {
				// LiteSpeed Cache.
				do_action( 'litespeed_purge_post', $post_id );

				// WP Rocket.
				if ( function_exists( 'rocket_clean_post' ) ) {
					rocket_clean_post( $post_id );
				}

				// W3 Total Cache.
				if ( function_exists( 'w3tc_flush_post' ) ) {
					w3tc_flush_post( $post_id );
				}

				// WP Super Cache.
				if ( function_exists( 'wpsc_delete_post_cache' ) ) {
					wpsc_delete_post_cache( $post_id );
				}

				// WP Fastest Cache.
				do_action( 'wpfc_clear_post_cache_by_id', false, $post_id );

				// SiteGround Optimizer.
				if ( $url && function_exists( 'sg_cachepress_purge_cache' ) ) {
					sg_cachepress_purge_cache( $url );
				}

				// Hummingbird.
				do_action( 'wphb_clear_page_cache', $post_id );

				// Cache Enabler.
				do_action( 'cache_enabler_clear_page_cache_by_post', $post_id );

				// WP-Optimize.
				if ( class_exists( 'WPO_Page_Cache' ) && method_exists( 'WPO_Page_Cache', 'delete_single_post_cache' ) ) {
					WPO_Page_Cache::delete_single_post_cache( $post_id );
				}

				// WP Engine.
				if ( class_exists( 'WpeCommon' ) && method_exists( 'WpeCommon', 'purge_varnish_cache' ) ) {
					WpeCommon::purge_varnish_cache( $post_id );
				}

				// Para integrar cualquier otra caché (hosting, CDN...).
				do_action( 'fdbs_purge_post_cache', $post_id, $url );
			}
		);
	}

	/**
	 * Limpia toda la caché de página (solo cuando el feed está en plantillas o widgets).
	 */
	public function purge_all() {
		delete_post_meta_by_key( '_elementor_element_cache' );

		$this->safely(
			function () {
				do_action( 'litespeed_purge_all' );

				if ( function_exists( 'rocket_clean_domain' ) ) {
					rocket_clean_domain();
				}

				if ( function_exists( 'w3tc_flush_all' ) ) {
					w3tc_flush_all();
				}

				if ( function_exists( 'wp_cache_clear_cache' ) ) {
					wp_cache_clear_cache();
				}

				do_action( 'wpfc_clear_all_cache' );

				if ( function_exists( 'sg_cachepress_purge_cache' ) ) {
					sg_cachepress_purge_cache();
				}

				do_action( 'wphb_clear_page_cache' );
				do_action( 'cache_enabler_clear_complete_cache' );

				if ( function_exists( 'wpo_cache_flush' ) ) {
					wpo_cache_flush();
				}

				do_action( 'breeze_clear_all_cache' );
				do_action( 'rt_nginx_helper_purge_all' );

				if ( class_exists( 'WpeCommon' ) && method_exists( 'WpeCommon', 'purge_varnish_cache' ) ) {
					WpeCommon::purge_varnish_cache();
				}

				do_action( 'fdbs_purge_all_cache' );
			}
		);
	}

	private function safely( $callback ) {
		try {
			$callback();
		} catch ( \Throwable $e ) {
			// Un plugin de caché con fallos no debe afectar al feed.
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log( 'Feed de Botones Simple: error al limpiar caché: ' . $e->getMessage() ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			}
		}
	}

	/**
	 * Aviso tras guardar: confirma qué páginas se refrescaron.
	 */
	public function render_notice() {
		$user_id = get_current_user_id();
		$result  = $user_id ? get_transient( self::NOTICE_TRANSIENT . $user_id ) : false;

		if ( ! is_array( $result ) ) {
			return;
		}

		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
		if ( ! $screen || 'fdbs_feed' !== $screen->post_type ) {
			return;
		}

		delete_transient( self::NOTICE_TRANSIENT . $user_id );

		if ( ! empty( $result['all'] ) ) {
			$message = 'Feed de Botones: se limpió la caché del sitio para que los cambios se vean de inmediato (el feed está en una plantilla o widget).';
		} else {
			$count   = count( $result['posts'] );
			$message = sprintf(
				'Feed de Botones: se limpió la caché de %d %s que usa%s este feed, así que los cambios ya se ven.',
				$count,
				1 === $count ? 'página' : 'páginas',
				1 === $count ? '' : 'n'
			);
		}

		echo '<div class="notice notice-success is-dismissible"><p>' . esc_html( $message ) . '</p></div>';
	}
}
