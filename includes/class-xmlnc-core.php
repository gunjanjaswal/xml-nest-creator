<?php
/**
 * Core sitemap generation functionality.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class XMLNC_Core {

	public function __construct() {
		add_action( 'init', array( $this, 'add_rewrite_rules' ) );
		add_filter( 'query_vars', array( $this, 'add_query_vars' ) );
		add_action( 'template_redirect', array( $this, 'render_sitemap' ), 0 );

		// Flush rules functionality.
		add_action( 'init', array( $this, 'maybe_flush_rewrite_rules' ), 99 );
	}

	/**
	 * Flush rewrite rules if the transient is set (on activation).
	 */
	public function maybe_flush_rewrite_rules() {
		if ( get_transient( 'xmlnc_flush_rewrite_rules' ) ) {
			flush_rewrite_rules();
			delete_transient( 'xmlnc_flush_rewrite_rules' );
		}
	}

	/**
	 * Add rewrite rule for sitemap.xml.
	 */
	public function add_rewrite_rules() {
		add_rewrite_rule( '^sitemap\.xml$', 'index.php?xmlnc_sitemap=1', 'top' );
	}

	/**
	 * Add custom query variable.
	 */
	public function add_query_vars( $vars ) {
		$vars[] = 'xmlnc_sitemap';
		return $vars;
	}

	/**
	 * Render the sitemap content.
	 */
	public function render_sitemap() {
		if ( get_query_var( 'xmlnc_sitemap' ) ) {
			// Clean any existing output to avoid XML errors.
			if ( ob_get_length() ) {
				ob_clean();
			}
			
			header( 'Content-Type: application/xml; charset=utf-8' );

			echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
			echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

			$options    = get_option( 'xmlnc_options' );
			$post_types = isset( $options['post_types'] ) ? $options['post_types'] : array( 'post', 'page' );
			$taxonomies = isset( $options['taxonomies'] ) ? $options['taxonomies'] : array( 'category' );

			// 1. Process Post Types.
			if ( ! empty( $post_types ) ) {
				$args = array(
					'post_type'      => $post_types,
					'post_status'    => 'publish',
					'posts_per_page' => 50000,
					'fields'         => 'ids',
				);

				$query = new WP_Query( $args );

				if ( $query->have_posts() ) {
					foreach ( $query->posts as $post_id ) {
						$url      = get_permalink( $post_id );
						$modified = get_post_modified_time( 'c', true, $post_id );

						echo "\t<url>\n";
						echo "\t\t<loc>" . esc_url( $url ) . "</loc>\n";
						echo "\t\t<lastmod>" . esc_html( $modified ) . "</lastmod>\n";
						echo "\t</url>\n";
					}
				}
			}

			// 2. Process Taxonomies.
			if ( ! empty( $taxonomies ) ) {
				$terms = get_terms( array(
					'taxonomy'   => $taxonomies,
					'hide_empty' => true,
				) );

				if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
					foreach ( $terms as $term ) {
						$url = get_term_link( $term );
						
						if ( is_wp_error( $url ) ) {
							continue;
						}

						echo "\t<url>\n";
						echo "\t\t<loc>" . esc_url( $url ) . "</loc>\n";
						echo "\t</url>\n";
					}
				}
			}

			echo '</urlset>';
			exit;
		}
	}
}
