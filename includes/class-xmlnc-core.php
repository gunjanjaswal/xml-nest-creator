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
	 * Add rewrite rules for the sitemap index and the per-object sub-sitemaps.
	 */
	public function add_rewrite_rules() {
		// Sitemap index.
		add_rewrite_rule( '^sitemap\.xml$', 'index.php?xmlnc_sitemap=index', 'top' );

		// Taxonomy sub-sitemap: sitemap-tax-{taxonomy}.xml
		add_rewrite_rule(
			'^sitemap-tax-([a-z0-9_-]+)\.xml$',
			'index.php?xmlnc_sitemap=tax&xmlnc_obj=$matches[1]',
			'top'
		);

		// Post type sub-sitemap (paged): sitemap-{post_type}-{page}.xml
		add_rewrite_rule(
			'^sitemap-([a-z0-9_-]+)-([0-9]+)\.xml$',
			'index.php?xmlnc_sitemap=pt&xmlnc_obj=$matches[1]&xmlnc_page=$matches[2]',
			'top'
		);
	}

	/**
	 * Add custom query variables.
	 */
	public function add_query_vars( $vars ) {
		$vars[] = 'xmlnc_sitemap';
		$vars[] = 'xmlnc_obj';
		$vars[] = 'xmlnc_page';
		return $vars;
	}

	/**
	 * Route the request to the correct renderer.
	 */
	public function render_sitemap() {
		$type = get_query_var( 'xmlnc_sitemap' );

		if ( ! $type ) {
			return;
		}

		// Clean any existing output to avoid XML errors.
		if ( ob_get_length() ) {
			ob_clean();
		}

		header( 'Content-Type: application/xml; charset=utf-8' );
		header( 'X-Robots-Tag: noindex, follow', true );

		switch ( $type ) {
			case 'index':
				$this->render_index();
				break;
			case 'pt':
				$this->render_post_type_sitemap(
					sanitize_key( get_query_var( 'xmlnc_obj' ) ),
					max( 1, (int) get_query_var( 'xmlnc_page' ) )
				);
				break;
			case 'tax':
				$this->render_taxonomy_sitemap( sanitize_key( get_query_var( 'xmlnc_obj' ) ) );
				break;
		}

		exit;
	}

	/**
	 * Output the XML prolog with a reference to the supplied XSL stylesheet.
	 *
	 * @param string $stylesheet Stylesheet file name inside assets/.
	 */
	private function xml_header( $stylesheet ) {
		echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
		echo '<?xml-stylesheet type="text/xsl" href="' . esc_url( XMLNC_PLUGIN_URL . 'assets/' . $stylesheet ) . '"?>' . "\n";
	}

	/**
	 * Render the sitemap index listing every sub-sitemap.
	 */
	private function render_index() {
		$options    = get_option( 'xmlnc_options' );
		$post_types = isset( $options['post_types'] ) ? (array) $options['post_types'] : array( 'post', 'page' );
		$taxonomies = isset( $options['taxonomies'] ) ? (array) $options['taxonomies'] : array( 'category' );

		$this->xml_header( 'sitemap-index.xsl' );
		echo '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

		// Post type sub-sitemaps (paginated).
		foreach ( $post_types as $pt ) {
			if ( ! post_type_exists( $pt ) ) {
				continue;
			}

			$counts    = wp_count_posts( $pt );
			$published = isset( $counts->publish ) ? (int) $counts->publish : 0;

			if ( $published < 1 ) {
				continue;
			}

			$pages   = (int) ceil( $published / XMLNC_PER_PAGE );
			$lastmod = $this->latest_post_modified( $pt );

			for ( $page = 1; $page <= $pages; $page++ ) {
				$loc = home_url( '/sitemap-' . $pt . '-' . $page . '.xml' );
				echo "\t<sitemap>\n";
				echo "\t\t<loc>" . esc_url( $loc ) . "</loc>\n";
				if ( $lastmod ) {
					echo "\t\t<lastmod>" . esc_html( $lastmod ) . "</lastmod>\n";
				}
				echo "\t</sitemap>\n";
			}
		}

		// Taxonomy sub-sitemaps.
		foreach ( $taxonomies as $tax ) {
			if ( ! taxonomy_exists( $tax ) ) {
				continue;
			}

			$term_count = get_terms( array(
				'taxonomy'   => $tax,
				'hide_empty' => true,
				'fields'     => 'count',
			) );
			if ( is_wp_error( $term_count ) || (int) $term_count < 1 ) {
				continue;
			}

			$loc = home_url( '/sitemap-tax-' . $tax . '.xml' );
			echo "\t<sitemap>\n";
			echo "\t\t<loc>" . esc_url( $loc ) . "</loc>\n";
			echo "\t</sitemap>\n";
		}

		echo '</sitemapindex>';
	}

	/**
	 * Render a single page of a post type sub-sitemap.
	 *
	 * @param string $post_type Post type name.
	 * @param int    $page      1-based page number.
	 */
	private function render_post_type_sitemap( $post_type, $page ) {
		$options       = get_option( 'xmlnc_options' );
		$post_types    = isset( $options['post_types'] ) ? (array) $options['post_types'] : array( 'post', 'page' );
		$enable_images = ! empty( $options['enable_images'] );

		// Only serve post types the admin opted into.
		if ( ! in_array( $post_type, $post_types, true ) || ! post_type_exists( $post_type ) ) {
			$this->xml_header( 'sitemap.xsl' );
			echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></urlset>';
			return;
		}

		$default_priority   = isset( $options['priority'] ) ? $options['priority'] : '';
		$default_changefreq = isset( $options['changefreq'] ) ? $options['changefreq'] : '';

		$args = array(
			'post_type'              => $post_type,
			'post_status'            => 'publish',
			'posts_per_page'         => XMLNC_PER_PAGE,
			'paged'                  => $page,
			'orderby'                => 'modified',
			'order'                  => 'DESC',
			'no_found_rows'          => true,
			'ignore_sticky_posts'    => true,
			'update_post_term_cache' => false,
			// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- Required to honour per-post sitemap exclusion.
			'meta_query'             => array(
				'relation' => 'OR',
				array(
					'key'     => '_xmlnc_exclude',
					'compare' => 'NOT EXISTS',
				),
				array(
					'key'     => '_xmlnc_exclude',
					'value'   => '1',
					'compare' => '!=',
				),
			),
		);

		$query = new WP_Query( $args );

		$this->xml_header( 'sitemap.xsl' );
		if ( $enable_images ) {
			echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n";
		} else {
			echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
		}

		if ( $query->have_posts() ) {
			foreach ( $query->posts as $post ) {
				$post_id    = $post->ID;
				$url        = get_permalink( $post_id );
				$modified   = get_post_modified_time( 'c', true, $post_id );
				$priority   = get_post_meta( $post_id, '_xmlnc_priority', true );
				$changefreq = get_post_meta( $post_id, '_xmlnc_changefreq', true );

				if ( '' === $priority ) {
					$priority = $default_priority;
				}
				if ( '' === $changefreq ) {
					$changefreq = $default_changefreq;
				}

				echo "\t<url>\n";
				echo "\t\t<loc>" . esc_url( $url ) . "</loc>\n";
				echo "\t\t<lastmod>" . esc_html( $modified ) . "</lastmod>\n";
				if ( '' !== $changefreq ) {
					echo "\t\t<changefreq>" . esc_html( $changefreq ) . "</changefreq>\n";
				}
				if ( '' !== $priority ) {
					echo "\t\t<priority>" . esc_html( number_format( (float) $priority, 1 ) ) . "</priority>\n";
				}

				if ( $enable_images ) {
					$this->render_post_images( $post_id );
				}

				echo "\t</url>\n";
			}
		}

		echo '</urlset>';
	}

	/**
	 * Output <image:image> entries for a post (featured image only, kept lean).
	 *
	 * @param int $post_id Post ID.
	 */
	private function render_post_images( $post_id ) {
		$thumb_id = get_post_thumbnail_id( $post_id );

		if ( ! $thumb_id ) {
			return;
		}

		$src = wp_get_attachment_image_url( $thumb_id, 'full' );

		if ( ! $src ) {
			return;
		}

		echo "\t\t<image:image>\n";
		echo "\t\t\t<image:loc>" . esc_url( $src ) . "</image:loc>\n";
		echo "\t\t</image:image>\n";
	}

	/**
	 * Render a taxonomy sub-sitemap (all terms, single file).
	 *
	 * @param string $taxonomy Taxonomy name.
	 */
	private function render_taxonomy_sitemap( $taxonomy ) {
		$options    = get_option( 'xmlnc_options' );
		$taxonomies = isset( $options['taxonomies'] ) ? (array) $options['taxonomies'] : array( 'category' );

		$this->xml_header( 'sitemap.xsl' );

		if ( ! in_array( $taxonomy, $taxonomies, true ) || ! taxonomy_exists( $taxonomy ) ) {
			echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></urlset>';
			return;
		}

		echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

		$terms = get_terms( array(
			'taxonomy'   => $taxonomy,
			'hide_empty' => true,
			'number'     => XMLNC_PER_PAGE,
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

		echo '</urlset>';
	}

	/**
	 * Get the most recent modified date (ISO 8601) for a post type.
	 *
	 * @param string $post_type Post type name.
	 * @return string ISO 8601 date or empty string.
	 */
	private function latest_post_modified( $post_type ) {
		$latest = get_posts( array(
			'post_type'        => $post_type,
			'post_status'      => 'publish',
			'posts_per_page'   => 1,
			'orderby'          => 'modified',
			'order'            => 'DESC',
			'no_found_rows'    => true,
			'fields'           => 'ids',
		) );

		if ( empty( $latest ) ) {
			return '';
		}

		return get_post_modified_time( 'c', true, $latest[0] );
	}
}
