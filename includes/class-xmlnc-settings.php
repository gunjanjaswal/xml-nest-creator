<?php
/**
 * Settings functionality.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class XMLNC_Settings {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

	public function add_settings_page() {
		add_options_page(
			'XML Nest Creator',
			'XML Nest Creator',
			'manage_options',
			'xml-nest-creator',
			array( $this, 'render_settings_page' )
		);
	}

	public function register_settings() {
		register_setting( 'xmlnc_settings_group', 'xmlnc_options', array(
			'sanitize_callback' => array( $this, 'sanitize_options' ),
		) );

		add_settings_section(
			'xmlnc_general_section',
			'General Settings',
			array( $this, 'render_general_section' ),
			'xml-nest-creator'
		);

		add_settings_field(
			'xmlnc_post_types',
			'Include Post Types',
			array( $this, 'render_post_types_field' ),
			'xml-nest-creator',
			'xmlnc_general_section'
		);

		add_settings_field(
			'xmlnc_taxonomies',
			'Include Taxonomies',
			array( $this, 'render_taxonomies_field' ),
			'xml-nest-creator',
			'xmlnc_general_section'
		);

		add_settings_section(
			'xmlnc_options_section',
			'Sitemap Options',
			array( $this, 'render_options_section' ),
			'xml-nest-creator'
		);

		add_settings_field(
			'xmlnc_priority',
			'Default Priority',
			array( $this, 'render_priority_field' ),
			'xml-nest-creator',
			'xmlnc_options_section'
		);

		add_settings_field(
			'xmlnc_changefreq',
			'Default Change Frequency',
			array( $this, 'render_changefreq_field' ),
			'xml-nest-creator',
			'xmlnc_options_section'
		);

		add_settings_field(
			'xmlnc_enable_images',
			'Image Sitemap',
			array( $this, 'render_enable_images_field' ),
			'xml-nest-creator',
			'xmlnc_options_section'
		);

		add_settings_field(
			'xmlnc_robots',
			'robots.txt',
			array( $this, 'render_robots_field' ),
			'xml-nest-creator',
			'xmlnc_options_section'
		);

		add_settings_field(
			'xmlnc_indexnow',
			'IndexNow',
			array( $this, 'render_indexnow_field' ),
			'xml-nest-creator',
			'xmlnc_options_section'
		);
	}

	public function sanitize_options( $input ) {
		$sanitized = array();

		if ( isset( $input['post_types'] ) && is_array( $input['post_types'] ) ) {
			$sanitized['post_types'] = array_map( 'sanitize_text_field', $input['post_types'] );
		} else {
			$sanitized['post_types'] = array();
		}

		if ( isset( $input['taxonomies'] ) && is_array( $input['taxonomies'] ) ) {
			$sanitized['taxonomies'] = array_map( 'sanitize_text_field', $input['taxonomies'] );
		} else {
			$sanitized['taxonomies'] = array();
		}

		// Default priority (0.0 - 1.0, or empty for none).
		if ( isset( $input['priority'] ) && '' !== $input['priority'] && is_numeric( $input['priority'] ) ) {
			$priority              = min( 1, max( 0, (float) $input['priority'] ) );
			$sanitized['priority'] = number_format( $priority, 1 );
		} else {
			$sanitized['priority'] = '';
		}

		// Default change frequency.
		$allowed_freq = array( 'always', 'hourly', 'daily', 'weekly', 'monthly', 'yearly', 'never' );
		if ( isset( $input['changefreq'] ) && in_array( $input['changefreq'], $allowed_freq, true ) ) {
			$sanitized['changefreq'] = $input['changefreq'];
		} else {
			$sanitized['changefreq'] = '';
		}

		// Toggles.
		$sanitized['enable_images'] = empty( $input['enable_images'] ) ? 0 : 1;
		$sanitized['robots']        = empty( $input['robots'] ) ? 0 : 1;
		$sanitized['indexnow']      = empty( $input['indexnow'] ) ? 0 : 1;

		return $sanitized;
	}

	public function render_general_section() {
		echo '<p>Select the content types you want to include in the <code>sitemap.xml</code>.</p>';
	}

	public function render_post_types_field() {
		$options = get_option( 'xmlnc_options' );
		// Default to post and page if not set.
		$selected_post_types = isset( $options['post_types'] ) ? $options['post_types'] : array( 'post', 'page' );
		
		$post_types = get_post_types( array( 'public' => true ), 'objects' );
		
		foreach ( $post_types as $pt ) {
			if ( $pt->name === 'attachment' ) { continue; }
			$checked = in_array( $pt->name, (array) $selected_post_types, true ) ? 'checked' : '';
			echo '<label><input type="checkbox" name="xmlnc_options[post_types][]" value="' . esc_attr( $pt->name ) . '" ' . esc_attr( $checked ) . '> ' . esc_html( $pt->label ) . '</label><br>';
		}
	}

	public function render_taxonomies_field() {
		$options = get_option( 'xmlnc_options' );
		// Default to category if not set.
		$selected_taxonomies = isset( $options['taxonomies'] ) ? $options['taxonomies'] : array( 'category' );
		
		$taxonomies = get_taxonomies( array( 'public' => true ), 'objects' );
		
		foreach ( $taxonomies as $tax ) {
			if ( $tax->name === 'post_format' ) { continue; }
			$checked = in_array( $tax->name, (array) $selected_taxonomies, true ) ? 'checked' : '';
			echo '<label><input type="checkbox" name="xmlnc_options[taxonomies][]" value="' . esc_attr( $tax->name ) . '" ' . esc_attr( $checked ) . '> ' . esc_html( $tax->label ) . '</label><br>';
		}
	}


	public function render_options_section() {
		echo '<p>Fine-tune the generated sitemap and how search engines are notified.</p>';
	}

	public function render_priority_field() {
		$options  = get_option( 'xmlnc_options' );
		$priority = isset( $options['priority'] ) ? $options['priority'] : '';
		?>
		<select name="xmlnc_options[priority]">
			<option value="" <?php selected( $priority, '' ); ?>>— None —</option>
			<?php for ( $i = 10; $i >= 0; $i-- ) :
				$val = number_format( $i / 10, 1 ); ?>
				<option value="<?php echo esc_attr( $val ); ?>" <?php selected( $priority, $val ); ?>><?php echo esc_html( $val ); ?></option>
			<?php endfor; ?>
		</select>
		<p class="description">Default <code>&lt;priority&gt;</code> for each URL. Per-post overrides win.</p>
		<?php
	}

	public function render_changefreq_field() {
		$options     = get_option( 'xmlnc_options' );
		$changefreq  = isset( $options['changefreq'] ) ? $options['changefreq'] : '';
		$frequencies = array( 'always', 'hourly', 'daily', 'weekly', 'monthly', 'yearly', 'never' );
		?>
		<select name="xmlnc_options[changefreq]">
			<option value="" <?php selected( $changefreq, '' ); ?>>— None —</option>
			<?php foreach ( $frequencies as $freq ) : ?>
				<option value="<?php echo esc_attr( $freq ); ?>" <?php selected( $changefreq, $freq ); ?>><?php echo esc_html( ucfirst( $freq ) ); ?></option>
			<?php endforeach; ?>
		</select>
		<p class="description">Default <code>&lt;changefreq&gt;</code> for each URL. Per-post overrides win.</p>
		<?php
	}

	public function render_enable_images_field() {
		$options = get_option( 'xmlnc_options' );
		$checked = ! empty( $options['enable_images'] );
		?>
		<label>
			<input type="checkbox" name="xmlnc_options[enable_images]" value="1" <?php checked( $checked ); ?>>
			Include each post's featured image as an <code>&lt;image:image&gt;</code> entry.
		</label>
		<?php
	}

	public function render_robots_field() {
		$options = get_option( 'xmlnc_options' );
		// Default on when the option has never been saved.
		$checked = ! isset( $options['robots'] ) || ! empty( $options['robots'] );
		?>
		<label>
			<input type="checkbox" name="xmlnc_options[robots]" value="1" <?php checked( $checked ); ?>>
			Add the <code>Sitemap:</code> line to <code>robots.txt</code>.
		</label>
		<?php
	}

	public function render_indexnow_field() {
		$options = get_option( 'xmlnc_options' );
		$checked = ! empty( $options['indexnow'] );
		?>
		<label>
			<input type="checkbox" name="xmlnc_options[indexnow]" value="1" <?php checked( $checked ); ?>>
			Ping search engines via <strong>IndexNow</strong> (Bing, Yandex &amp; others) when content is published or updated.
		</label>
		<p class="description">Replaces the deprecated Google/Bing sitemap ping with the modern IndexNow protocol.</p>
		<?php
	}

	public function render_settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$sitemap_url = home_url( '/sitemap.xml' );
		?>
		<div class="wrap">
			<h1>XML Nest Creator <a href="<?php echo esc_url( $sitemap_url ); ?>" target="_blank" class="page-title-action">View Sitemap</a></h1>
			<p>This plugin generates a dynamic <code>sitemap.xml</code> and automatically overrides Yoast SEO and Rank Math sitemaps specifically for the XML generation.</p>
			<hr>
			<form action="options.php" method="post">
				<?php
				settings_fields( 'xmlnc_settings_group' );
				do_settings_sections( 'xml-nest-creator' );
				submit_button();
				?>
			</form>
			
			<hr style="margin-top: 30px;">
			<div style="margin-top: 20px; padding: 15px; background: #fff; border: 1px solid #ccd0d4; border-left: 4px solid #2271b1; max-width: 600px;">
				<h3 style="margin-top: 0;">Need Help or Customization?</h3>
				<p style="margin-bottom: 0;">
					<strong>Developed by:</strong> Gunjan Jaswal<br>
					<strong>Website:</strong> <a href="https://www.gunjanjaswal.me" target="_blank">www.gunjanjaswal.me</a><br>
					<strong>Contact:</strong> <a href="mailto:hello@gunjanjaswal.me">hello@gunjanjaswal.me</a>
				</p>
			</div>
		</div>
		<?php
	}
}
