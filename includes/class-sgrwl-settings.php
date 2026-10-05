<?php
/**
 * Admin settings for Google reCAPTCHA keys.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SGRWL_Settings {

	const OPTION_GROUP = 'sgrwl_settings_group';
	const OPTION_NAME  = 'sgrwl_settings';
	const PAGE_SLUG    = 'sgrwl-settings';

	/**
	 * Register settings hooks.
	 */
	public function register() {
		add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_init', array( $this, 'maybe_disable_option_autoload' ) );
		add_action( 'add_option_' . self::OPTION_NAME, array( $this, 'disable_option_autoload' ) );
		add_action( 'update_option_' . self::OPTION_NAME, array( $this, 'disable_option_autoload' ) );
	}

	/**
	 * Get a single setting value.
	 *
	 * @param string $key     Setting key.
	 * @param string $default Default value.
	 * @return string
	 */
	public function get( $key, $default = '' ) {
		$options = get_option( self::OPTION_NAME, array() );

		return isset( $options[ $key ] ) ? (string) $options[ $key ] : $default;
	}

	/**
	 * Whether both keys are configured.
	 *
	 * @return bool
	 */
	public function is_configured() {
		return '' !== $this->get( 'site_key' ) && '' !== $this->get( 'secret_key' );
	}

	/**
	 * Add settings page under Settings.
	 */
	public function add_settings_page() {
		add_options_page(
			__( 'Sikora Google reCAPTCHA on WordPress Login', 'sikora-google-recaptcha-on-wordpress-login' ),
			__( 'Sikora Google reCAPTCHA on WordPress Login', 'sikora-google-recaptcha-on-wordpress-login' ),
			'manage_options',
			self::PAGE_SLUG,
			array( $this, 'render_settings_page' )
		);
	}

	/**
	 * Register option and fields.
	 */
	public function register_settings() {
		register_setting(
			self::OPTION_GROUP,
			self::OPTION_NAME,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( $this, 'sanitize_settings' ),
				'default'           => array(
					'site_key'   => '',
					'secret_key' => '',
				),
				'autoload'          => false, // keep secret out of alloptions
			)
		);

		add_settings_section(
			'sgrwl_main_section',
			__( 'Google reCAPTCHA v2', 'sikora-google-recaptcha-on-wordpress-login' ),
			array( $this, 'render_section_description' ),
			self::PAGE_SLUG
		);

		add_settings_field(
			'sgrwl_site_key',
			__( 'ID', 'sikora-google-recaptcha-on-wordpress-login' ),
			array( $this, 'render_site_key_field' ),
			self::PAGE_SLUG,
			'sgrwl_main_section'
		);

		add_settings_field(
			'sgrwl_secret_key',
			__( 'Secret Key', 'sikora-google-recaptcha-on-wordpress-login' ),
			array( $this, 'render_secret_key_field' ),
			self::PAGE_SLUG,
			'sgrwl_main_section'
		);
	}

	/**
	 * Sanitize saved settings.
	 *
	 * @param array $input Raw input.
	 * @return array
	 */
	public function sanitize_settings( $input ) {
		$output = array(
			'site_key'   => '',
			'secret_key' => '',
		);

		if ( isset( $input['site_key'] ) ) {
			$output['site_key'] = trim( sanitize_text_field( $input['site_key'] ) );
		}

		if ( isset( $input['secret_key'] ) ) {
			$output['secret_key'] = trim( sanitize_text_field( $input['secret_key'] ) );
		}

		return $output;
	}

	/**
	 * Ensure an existing option row is not autoloaded.
	 */
	public function maybe_disable_option_autoload() {
		global $wpdb;

		$autoload = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT autoload FROM {$wpdb->options} WHERE option_name = %s LIMIT 1",
				self::OPTION_NAME
			)
		);

		if ( null === $autoload ) {
			return;
		}

		if ( in_array( (string) $autoload, array( 'no', 'off', 'auto-off' ), true ) ) {
			return;
		}

		$this->disable_option_autoload();
	}

	/**
	 * Store settings without autoload so the secret is not loaded on every request.
	 */
	public function disable_option_autoload() {
		if ( function_exists( 'wp_set_option_autoload' ) ) {
			wp_set_option_autoload( self::OPTION_NAME, false );
			return;
		}

		global $wpdb;

		$wpdb->update(
			$wpdb->options,
			array( 'autoload' => 'no' ),
			array( 'option_name' => self::OPTION_NAME ),
			array( '%s' ),
			array( '%s' )
		);

		wp_cache_delete( self::OPTION_NAME, 'options' );
		wp_cache_delete( 'alloptions', 'options' );
	}

	/**
	 * Section help text.
	 */
	public function render_section_description() {
		echo '<ul class="sgrwl-settings-bullets">';
		echo '<li>' . esc_html__(
			'Values for the fields below should be created with Google\'s reCAPTCHA admin console.',
			'sikora-google-recaptcha-on-wordpress-login'
		) . '</li>';
		echo '<li>' . esc_html__(
			'The reCAPTCHA element will not appear on the WordPress login page if both values are missing.',
			'sikora-google-recaptcha-on-wordpress-login'
		) . '</li>';
		echo '</ul>';
	}

	/**
	 * Site key field.
	 */
	public function render_site_key_field() {
		$value = $this->get( 'site_key' );
		printf(
			'<input type="text" class="regular-text" name="%1$s[site_key]" value="%2$s" autocomplete="off" />',
			esc_attr( self::OPTION_NAME ),
			esc_attr( $value )
		);
	}

	/**
	 * Secret key field.
	 */
	public function render_secret_key_field() {
		$value = $this->get( 'secret_key' );
		printf(
			'<input type="password" class="regular-text" name="%1$s[secret_key]" value="%2$s" autocomplete="off" />',
			esc_attr( self::OPTION_NAME ),
			esc_attr( $value )
		);
	}

	/**
	 * Render settings page markup.
	 */
	public function render_settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		?>
		<div class="wrap">
			<style>
				.sgrwl-settings-section > ul.sgrwl-settings-bullets {
					list-style: disc;
					margin: 0.5em 0 1em 1.5em;
					padding-left: 1.5em;
				}
				.sgrwl-settings-section > .form-table {
					margin-left: 1.5em;
					width: auto;
				}
				.sgrwl-settings-section > .form-table th {
					width: 1%;
					padding: 10px 8px 6px 0;
					vertical-align: top;
					white-space: nowrap;
					line-height: 2;
				}
				.sgrwl-settings-section > .form-table td {
					padding: 6px 0;
					vertical-align: top;
				}
			</style>
			<h1 style="font-weight: 700;"><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<form action="options.php" method="post">
				<?php settings_fields( self::OPTION_GROUP ); ?>
				<div class="sgrwl-settings-section">
					<?php do_settings_sections( self::PAGE_SLUG ); ?>
				</div>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}
}
