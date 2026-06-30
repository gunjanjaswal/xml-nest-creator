<?php
/**
 * Search engine notification: robots.txt Sitemap directive and IndexNow pings.
 *
 * IndexNow is used instead of the legacy GET /ping?sitemap= endpoints, which
 * Google (2023) and Bing have deprecated. IndexNow is honoured by Bing,
 * Yandex, Seznam and others, and notifies them the moment content changes.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class XMLNC_Ping {

	const KEY_FILE = 'xmlnc-indexnow.txt';

	public function __construct() {
		add_filter( 'robots_txt', array( $this, 'robots_txt' ), 10, 2 );

		add_action( 'init', array( $this, 'add_rewrite_rules' ) );
		add_filter( 'query_vars', array( $this, 'add_query_vars' ) );
		add_action( 'template_redirect', array( $this, 'serve_key_file' ), 0 );

		add_action( 'transition_post_status', array( $this, 'maybe_ping' ), 10, 3 );
	}

	/**
	 * Append the sitemap location to robots.txt.
	 *
	 * @param string $output Existing robots.txt content.
	 * @param bool   $public Whether the site is public.
	 * @return string
	 */
	public function robots_txt( $output, $public ) {
		$options = get_option( 'xmlnc_options' );
		$enabled = ! isset( $options['robots'] ) || ! empty( $options['robots'] );

		if ( $public && $enabled ) {
			$output .= "\nSitemap: " . esc_url( home_url( '/sitemap.xml' ) ) . "\n";
		}

		return $output;
	}

	/**
	 * Register the rewrite rule that serves the IndexNow key file.
	 */
	public function add_rewrite_rules() {
		add_rewrite_rule( '^' . self::KEY_FILE . '$', 'index.php?xmlnc_indexnow_key=1', 'top' );
	}

	/**
	 * Register the query var for the key file.
	 */
	public function add_query_vars( $vars ) {
		$vars[] = 'xmlnc_indexnow_key';
		return $vars;
	}

	/**
	 * Output the IndexNow key as plain text when its file URL is requested.
	 */
	public function serve_key_file() {
		if ( ! get_query_var( 'xmlnc_indexnow_key' ) ) {
			return;
		}

		if ( ob_get_length() ) {
			ob_clean();
		}

		header( 'Content-Type: text/plain; charset=utf-8' );
		echo esc_html( $this->get_key() );
		exit;
	}

	/**
	 * Ping IndexNow when a post becomes (or stays) published.
	 *
	 * @param string  $new_status New post status.
	 * @param string  $old_status Old post status.
	 * @param WP_Post $post       Post object.
	 */
	public function maybe_ping( $new_status, $old_status, $post ) {
		$options = get_option( 'xmlnc_options' );

		if ( empty( $options['indexnow'] ) ) {
			return;
		}

		if ( 'publish' !== $new_status ) {
			return;
		}

		$post_types = isset( $options['post_types'] ) ? (array) $options['post_types'] : array( 'post', 'page' );
		if ( ! in_array( $post->post_type, $post_types, true ) ) {
			return;
		}

		// Skip excluded posts.
		if ( '1' === (string) get_post_meta( $post->ID, '_xmlnc_exclude', true ) ) {
			return;
		}

		$url = get_permalink( $post );
		if ( ! $url ) {
			return;
		}

		$this->submit( $url );
	}

	/**
	 * Submit a single URL to the IndexNow API.
	 *
	 * @param string $url URL to notify.
	 */
	private function submit( $url ) {
		$host = wp_parse_url( home_url(), PHP_URL_HOST );
		$key  = $this->get_key();

		$body = array(
			'host'        => $host,
			'key'         => $key,
			'keyLocation' => home_url( '/' . self::KEY_FILE ),
			'urlList'     => array( $url ),
		);

		wp_remote_post( 'https://api.indexnow.org/indexnow', array(
			'timeout'  => 5,
			'blocking' => false,
			'headers'  => array( 'Content-Type' => 'application/json; charset=utf-8' ),
			'body'     => wp_json_encode( $body ),
		) );
	}

	/**
	 * Get (or lazily generate) the persistent IndexNow key.
	 *
	 * @return string 32-character hex key.
	 */
	private function get_key() {
		$key = get_option( 'xmlnc_indexnow_key' );

		if ( ! $key ) {
			$key = bin2hex( random_bytes( 16 ) );
			update_option( 'xmlnc_indexnow_key', $key, false );
		}

		return $key;
	}
}
