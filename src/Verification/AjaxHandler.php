<?php
/**
 * Verification AJAX endpoint.
 *
 * @package HOAY
 */

namespace HOAY\Verification;

use DateTimeZone;
use HOAY\Settings\Options;
use HOAY\Support\Sanitizer;

defined( 'ABSPATH' ) || exit;

/**
 * Handles `wp_ajax_*` for the verification form.
 *
 * Both the logged-in (`wp_ajax_hoay_verify`) and anonymous
 * (`wp_ajax_nopriv_hoay_verify`) hooks point here — the front-end gate
 * applies to anonymous visitors, but registered users behind the gate
 * (rare but possible) need the same code path.
 */
final class AjaxHandler {

	/**
	 * Wire the AJAX hooks.
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'wp_ajax_hoay_verify', array( $this, 'handle' ) );
		add_action( 'wp_ajax_nopriv_hoay_verify', array( $this, 'handle' ) );
	}

	/**
	 * Verify the submission, set the cookie on success, return JSON.
	 *
	 * @return void
	 */
	public function handle() {
		check_ajax_referer( 'hoay_verify' );

		if ( ! Options::get( 'enabled' ) ) {
			wp_send_json_error( array( 'message' => __( 'Verification is disabled.', 'how-old-are-you' ) ), 403 );
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		$raw_mode = isset( $_POST['mode'] ) ? sanitize_text_field( wp_unslash( $_POST['mode'] ) ) : '';
		$mode     = '' === $raw_mode
			? (string) Options::get( 'verification_mode' )
			: Sanitizer::enum( $raw_mode, Sanitizer::VERIFICATION_MODES, (string) Options::get( 'verification_mode' ) );

		$result = ( 'dob' === $mode ) ? $this->verify_dob() : $this->verify_confirm();

		/**
		 * Filter the verification result before sending the JSON response.
		 *
		 * @param array $result {
		 *     @type bool   $passed  True if the visitor passed.
		 *     @type string $reason  When failed: "underage", "invalid", "disabled".
		 *     @type string $message Human-readable explanation.
		 * }
		 * @param string $mode  Verification mode that was used.
		 */
		$result = apply_filters( 'hoay_verification_result', $result, $mode );

		if ( ! empty( $result['passed'] ) ) {
			$days     = (int) Options::get( 'cookie_lifetime_days', 30 );
			$lifetime = $days * DAY_IN_SECONDS;
			CookieManager::set_verified(
				(string) Options::get( 'cookie_name' ),
				$lifetime,
				(string) Options::get( 'cookie_same_site' )
			);
			wp_send_json_success(
				array(
					'message' => __( 'Verified. Welcome.', 'how-old-are-you' ),
				)
			);
		}

		$status_code = ( isset( $result['reason'] ) && 'invalid' === $result['reason'] ) ? 400 : 200;
		wp_send_json_error( $result, $status_code );
	}

	/**
	 * Validate a DOB submission.
	 *
	 * @return array{passed:bool, reason?:string, message:string}
	 */
	private function verify_dob() {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( ! isset( $_POST['dob'] ) ) {
			return array(
				'passed'  => false,
				'reason'  => 'invalid',
				'message' => __( 'Please enter a date of birth.', 'how-old-are-you' ),
			);
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		$dob = sanitize_text_field( wp_unslash( $_POST['dob'] ) );
		$age = AgeCalculator::age_from_dob( $dob, $this->site_timezone() );

		if ( null === $age ) {
			return array(
				'passed'  => false,
				'reason'  => 'invalid',
				'message' => __( 'That date of birth doesn\'t look right.', 'how-old-are-you' ),
			);
		}

		$min = (int) Options::get( 'minimum_age' );
		if ( $age < $min ) {
			return array(
				'passed'  => false,
				'reason'  => 'underage',
				'message' => __( 'You are not old enough to enter this site.', 'how-old-are-you' ),
			);
		}

		return array(
			'passed'  => true,
			'message' => __( 'Verified.', 'how-old-are-you' ),
		);
	}

	/**
	 * Validate a confirm-mode submission.
	 *
	 * @return array{passed:bool, reason?:string, message:string}
	 */
	private function verify_confirm() {
		// Nonce is verified at the top of handle() via check_ajax_referer().
		// phpcs:disable WordPress.Security.NonceVerification.Missing
		$choice = isset( $_POST['confirm'] )
			? sanitize_text_field( wp_unslash( $_POST['confirm'] ) )
			: '';
		// phpcs:enable WordPress.Security.NonceVerification.Missing

		if ( 'yes' === $choice ) {
			return array(
				'passed'  => true,
				'message' => __( 'Verified.', 'how-old-are-you' ),
			);
		}

		return array(
			'passed'  => false,
			'reason'  => 'underage',
			'message' => __( 'You are not old enough to enter this site.', 'how-old-are-you' ),
		);
	}

	/**
	 * Build a DateTimeZone for the site, falling back to UTC.
	 *
	 * @return DateTimeZone
	 */
	private function site_timezone() {
		try {
			return wp_timezone();
		} catch ( \Exception $e ) {
			return new DateTimeZone( 'UTC' );
		}
	}
}
