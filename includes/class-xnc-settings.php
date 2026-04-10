<?php
/**
 * Settings functionality.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class XNC_Settings {

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
		register_setting( 'xnc_settings_group', 'xnc_options', array(
			'sanitize_callback' => array( $this, 'sanitize_options' ),
		) );

		add_settings_section(
			'xnc_general_section',
			'General Settings',
			array( $this, 'render_general_section' ),
			'xml-nest-creator'
		);

		add_settings_field(
			'xnc_post_types',
			'Include Post Types',
			array( $this, 'render_post_types_field' ),
			'xml-nest-creator',
			'xnc_general_section'
		);

		add_settings_field(
			'xnc_taxonomies',
			'Include Taxonomies',
			array( $this, 'render_taxonomies_field' ),
			'xml-nest-creator',
			'xnc_general_section'
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

		return $sanitized;
	}

	public function render_general_section() {
		echo '<p>Select the content types you want to include in the <code>sitemap.xml</code>.</p>';
	}

	public function render_post_types_field() {
		$options = get_option( 'xnc_options' );
		// Default to post and page if not set.
		$selected_post_types = isset( $options['post_types'] ) ? $options['post_types'] : array( 'post', 'page' );
		
		$post_types = get_post_types( array( 'public' => true ), 'objects' );
		
		foreach ( $post_types as $pt ) {
			if ( $pt->name === 'attachment' ) { continue; }
			$checked = in_array( $pt->name, (array) $selected_post_types, true ) ? 'checked' : '';
			echo '<label><input type="checkbox" name="xnc_options[post_types][]" value="' . esc_attr( $pt->name ) . '" ' . $checked . '> ' . esc_html( $pt->label ) . '</label><br>';
		}
	}

	public function render_taxonomies_field() {
		$options = get_option( 'xnc_options' );
		// Default to category if not set.
		$selected_taxonomies = isset( $options['taxonomies'] ) ? $options['taxonomies'] : array( 'category' );
		
		$taxonomies = get_taxonomies( array( 'public' => true ), 'objects' );
		
		foreach ( $taxonomies as $tax ) {
			if ( $tax->name === 'post_format' ) { continue; }
			$checked = in_array( $tax->name, (array) $selected_taxonomies, true ) ? 'checked' : '';
			echo '<label><input type="checkbox" name="xnc_options[taxonomies][]" value="' . esc_attr( $tax->name ) . '" ' . $checked . '> ' . esc_html( $tax->label ) . '</label><br>';
		}
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
				settings_fields( 'xnc_settings_group' );
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
