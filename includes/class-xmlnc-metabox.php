<?php
/**
 * Per-post sitemap controls (exclude, priority, changefreq) via a meta box.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class XMLNC_Metabox {

	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
		add_action( 'save_post', array( $this, 'save' ), 10, 2 );
	}

	/**
	 * Register the meta box for every post type selected in the settings.
	 */
	public function add_meta_box() {
		$options    = get_option( 'xmlnc_options' );
		$post_types = isset( $options['post_types'] ) ? (array) $options['post_types'] : array( 'post', 'page' );

		foreach ( $post_types as $pt ) {
			if ( ! post_type_exists( $pt ) ) {
				continue;
			}

			add_meta_box(
				'xmlnc_meta',
				__( 'XML Sitemap', 'xml-nest-creator' ),
				array( $this, 'render' ),
				$pt,
				'side',
				'default'
			);
		}
	}

	/**
	 * Render the meta box fields.
	 *
	 * @param WP_Post $post Current post.
	 */
	public function render( $post ) {
		wp_nonce_field( 'xmlnc_meta_save', 'xmlnc_meta_nonce' );

		$exclude    = (string) get_post_meta( $post->ID, '_xmlnc_exclude', true );
		$priority   = (string) get_post_meta( $post->ID, '_xmlnc_priority', true );
		$changefreq = (string) get_post_meta( $post->ID, '_xmlnc_changefreq', true );

		$frequencies = array( 'always', 'hourly', 'daily', 'weekly', 'monthly', 'yearly', 'never' );
		?>
		<p>
			<label>
				<input type="checkbox" name="xmlnc_exclude" value="1" <?php checked( $exclude, '1' ); ?>>
				<?php esc_html_e( 'Exclude this content from the sitemap', 'xml-nest-creator' ); ?>
			</label>
		</p>
		<p>
			<label for="xmlnc_priority"><strong><?php esc_html_e( 'Priority', 'xml-nest-creator' ); ?></strong></label><br>
			<select name="xmlnc_priority" id="xmlnc_priority">
				<option value="" <?php selected( $priority, '' ); ?>><?php esc_html_e( '— Default —', 'xml-nest-creator' ); ?></option>
				<?php for ( $i = 10; $i >= 0; $i-- ) :
					$val = number_format( $i / 10, 1 ); ?>
					<option value="<?php echo esc_attr( $val ); ?>" <?php selected( $priority, $val ); ?>><?php echo esc_html( $val ); ?></option>
				<?php endfor; ?>
			</select>
		</p>
		<p>
			<label for="xmlnc_changefreq"><strong><?php esc_html_e( 'Change frequency', 'xml-nest-creator' ); ?></strong></label><br>
			<select name="xmlnc_changefreq" id="xmlnc_changefreq">
				<option value="" <?php selected( $changefreq, '' ); ?>><?php esc_html_e( '— Default —', 'xml-nest-creator' ); ?></option>
				<?php foreach ( $frequencies as $freq ) : ?>
					<option value="<?php echo esc_attr( $freq ); ?>" <?php selected( $changefreq, $freq ); ?>><?php echo esc_html( ucfirst( $freq ) ); ?></option>
				<?php endforeach; ?>
			</select>
		</p>
		<?php
	}

	/**
	 * Persist the meta box values.
	 *
	 * @param int     $post_id Post ID.
	 * @param WP_Post $post    Post object.
	 */
	public function save( $post_id, $post ) {
		if ( ! isset( $_POST['xmlnc_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['xmlnc_meta_nonce'] ) ), 'xmlnc_meta_save' ) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Exclude flag.
		if ( ! empty( $_POST['xmlnc_exclude'] ) ) {
			update_post_meta( $post_id, '_xmlnc_exclude', '1' );
		} else {
			delete_post_meta( $post_id, '_xmlnc_exclude' );
		}

		// Priority.
		$priority = isset( $_POST['xmlnc_priority'] ) ? sanitize_text_field( wp_unslash( $_POST['xmlnc_priority'] ) ) : '';
		if ( '' !== $priority && is_numeric( $priority ) ) {
			update_post_meta( $post_id, '_xmlnc_priority', number_format( (float) $priority, 1 ) );
		} else {
			delete_post_meta( $post_id, '_xmlnc_priority' );
		}

		// Change frequency.
		$allowed    = array( 'always', 'hourly', 'daily', 'weekly', 'monthly', 'yearly', 'never' );
		$changefreq = isset( $_POST['xmlnc_changefreq'] ) ? sanitize_text_field( wp_unslash( $_POST['xmlnc_changefreq'] ) ) : '';
		if ( in_array( $changefreq, $allowed, true ) ) {
			update_post_meta( $post_id, '_xmlnc_changefreq', $changefreq );
		} else {
			delete_post_meta( $post_id, '_xmlnc_changefreq' );
		}
	}
}
