<?php
/**
 * Login page reCAPTCHA rendering and verification.
 *
 * Shows the Google reCAPTCHA v2 widget on wp-login.php when keys are configured
 * and blocks authentication unless Google returns a successful response for
 * this site's hostname.
 *
 * @package SGRWL
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // prevent direct access
}

/**
 * Integrates Google reCAPTCHA with the WordPress login flow.
 */
class SGRWL_Login {

	/**
	 * Google siteverify endpoint.
	 *
	 * @var string
	 */
	const VERIFY_URL = 'https://www.google.com/recaptcha/api/siteverify';

	/**
	 * Settings helper used for keys and configuration checks.
	 *
	 * @var SGRWL_Settings
	 */
	private $settings;

	/**
	 * @param SGRWL_Settings $settings Settings instance.
	 */
	public function __construct( SGRWL_Settings $settings ) {
		$this->settings = $settings;
	}

	/**
	 * Register login-page assets, markup, and authentication filter.
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'login_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'login_form', array( $this, 'render_widget' ) );
		add_action( 'login_footer', array( $this, 'print_recaptcha_script' ), 5 );
		// after core credential checks (priority 20) so a wp_error is not overwritten
		add_filter( 'authenticate', array( $this, 'verify_recaptcha' ), 99, 3 );
	}

	/**
	 * Widen the login box and style the reCAPTCHA wrapper when configured.
	 *
	 * @return void
	 */
	public function enqueue_styles() {
		if ( ! $this->settings->is_configured() ) {
			return;
		}

		$css = 'body.login #login{width:350px;}'
			. '#login .sgrwl-recaptcha-wrapper{margin:12px 0 16px;clear:both;}'
			. '#login .sgrwl-recaptcha-wrapper .g-recaptcha{display:block;width:304px;max-width:100%;}';

		wp_register_style( 'sgrwl-login', false, array(), SGRWL_VERSION );
		wp_enqueue_style( 'sgrwl-login' );
		wp_add_inline_style( 'sgrwl-login', $css );
	}

	/**
	 * Output the reCAPTCHA widget container on the login form.
	 *
	 * @return void
	 */
	public function render_widget() {
		if ( ! $this->settings->is_configured() ) {
			return;
		}
		?>
		<div class="sgrwl-recaptcha-wrapper">
			<div id="sgrwl-recaptcha" class="g-recaptcha" data-sitekey="<?php echo esc_attr( $this->settings->get( 'site_key' ) ); ?>"></div>
		</div>
		<?php
	}

	/**
	 * Print Google reCAPTCHA api.js with explicit render after the form exists.
	 *
	 * @return void
	 */
	public function print_recaptcha_script() {
		if ( ! $this->settings->is_configured() ) {
			return;
		}

		$site_key = $this->settings->get( 'site_key' );
		$api_url  = add_query_arg(
			array(
				'onload' => 'sgrwlRecaptchaOnload',
				'render' => 'explicit',
			),
			'https://www.google.com/recaptcha/api.js'
		);
		?>
		<script>
		function sgrwlRecaptchaOnload() {
			var el = document.getElementById('sgrwl-recaptcha');
			if (!el || typeof grecaptcha === 'undefined') {
				return;
			}
			if (el.getAttribute('data-sgrwl-rendered') === '1') {
				return;
			}
			grecaptcha.render(el, { sitekey: <?php echo wp_json_encode( $site_key ); ?> });
			el.setAttribute('data-sgrwl-rendered', '1');
		}
		</script>
		<script src="<?php echo esc_url( $api_url ); ?>" async defer></script>
		<?php
	}

	/**
	 * Require a successful reCAPTCHA before allowing login.
	 *
	 * Runs after core credential authentication so a failed captcha cannot be
	 * replaced by a successful username/password result.
	 *
	 * @param WP_User|WP_Error|null $user     Authenticated user, error, or null.
	 * @param string                $username Submitted username.
	 * @param string                $password Submitted password.
	 * @return WP_User|WP_Error|null
	 */
	public function verify_recaptcha( $user, $username, $password ) {
		if ( empty( $username ) && empty( $password ) ) {
			return $user; // skip empty login attempts
		}

		if ( ! $this->settings->is_configured() ) {
			return $user;
		}

		$token = isset( $_POST['g-recaptcha-response'] )
			? sanitize_text_field( wp_unslash( $_POST['g-recaptcha-response'] ) )
			: '';

		if ( '' === $token ) {
			return new WP_Error(
				'sgrwl_missing_recaptcha',
				__( '<strong>Error:</strong> Please complete the reCAPTCHA.', 'sikora-google-recaptcha-on-wordpress-login' )
			);
		}

		$response = wp_remote_post(
			self::VERIFY_URL,
			array(
				'timeout' => 10,
				'body'    => array(
					'secret'   => $this->settings->get( 'secret_key' ),
					'response' => $token,
					'remoteip' => $this->get_remote_ip(),
				),
			)
		);

		if ( is_wp_error( $response ) ) {
			$this->log_error(
				sprintf(
					'Google reCAPTCHA verify request failed: %s',
					$response->get_error_message()
				)
			);

			return new WP_Error(
				'sgrwl_recaptcha_request_failed',
				__( '<strong>Error:</strong> Could not verify reCAPTCHA. Please try again.', 'sikora-google-recaptcha-on-wordpress-login' )
			);
		}

		$body = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( ! is_array( $body ) || true !== $body['success'] ) {
			return new WP_Error(
				'sgrwl_invalid_recaptcha',
				__( '<strong>Error:</strong> reCAPTCHA verification failed. Please try again.', 'sikora-google-recaptcha-on-wordpress-login' )
			);
		}

		$hostname = isset( $body['hostname'] ) ? (string) $body['hostname'] : '';
		if ( ! $this->is_hostname_allowed( $hostname ) ) {
			$this->log_error(
				sprintf(
					'reCAPTCHA hostname mismatch. Received "%s"; allowed: %s',
					$hostname,
					implode( ', ', $this->get_allowed_hostnames() )
				)
			);

			return new WP_Error(
				'sgrwl_invalid_recaptcha_hostname',
				__( '<strong>Error:</strong> reCAPTCHA verification failed. Please try again.', 'sikora-google-recaptcha-on-wordpress-login' )
			);
		}

		return $user;
	}

	/**
	 * Whether Google's reported hostname matches this site.
	 *
	 * @param string $hostname Hostname from the siteverify response.
	 * @return bool
	 */
	private function is_hostname_allowed( $hostname ) {
		$hostname = strtolower( trim( $hostname ) );
		if ( '' === $hostname ) {
			return false;
		}

		return in_array( $hostname, $this->get_allowed_hostnames(), true );
	}

	/**
	 * Hostnames allowed for Google reCAPTCHA responses.
	 *
	 * @return string[]
	 */
	private function get_allowed_hostnames() {
		$allowed = array();

		foreach ( array( home_url(), site_url() ) as $url ) {
			$host = wp_parse_url( $url, PHP_URL_HOST );
			if ( is_string( $host ) && '' !== $host ) {
				$allowed[] = strtolower( $host );
			}
		}

		return array_values( array_unique( $allowed ) );
	}

	/**
	 * Log an error when WordPress debug logging is enabled.
	 *
	 * @param string $message Error message.
	 * @return void
	 */
	private function log_error( $message ) {
		if ( ! defined( 'WP_DEBUG_LOG' ) || ! WP_DEBUG_LOG ) {
			return;
		}

		error_log( 'Sikora Google reCAPTCHA: ' . $message );
	}

	/**
	 * Best-effort client IP for Google verification.
	 *
	 * Uses REMOTE_ADDR only; proxy headers are ignored to avoid spoofing.
	 *
	 * @return string
	 */
	private function get_remote_ip() {
		if ( ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
			$ip = sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) );
			return filter_var( $ip, FILTER_VALIDATE_IP ) ? $ip : '';
		}

		return '';
	}
}
