<?php
/**
 * Verification overlay (standalone document).
 *
 * Variables provided by Renderer::render():
 *
 * @var array<string,mixed> $options   Current settings.
 * @var int                 $min_age   Minimum age requirement.
 * @var string              $mode      `dob` or `confirm`.
 * @var string              $logo_url  Logo URL or empty.
 * @var string              $nonce     hoay_verify nonce.
 * @var string              $ajax_url  admin-ajax URL.
 * @var string              $max_dob   Today's Y-m-d (DOB upper bound).
 * @var string              $min_dob   120 years ago (DOB lower bound).
 * @var string              $assets    URL to /assets/.
 * @var string              $css_ver   Asset version.
 * @var string              $site_url  Site home URL.
 * @var string              $site_name Site title.
 * @var string              $lang      Language code.
 *
 * @package HOAY
 */

defined( 'ABSPATH' ) || exit;

use HOAY\Frontend\Renderer;
use HOAY\Settings\Options;

$css_vars     = Renderer::css_variables( $options );
$heading      = Options::interpolate_age( (string) $options['heading_text'] );
$body_text    = Options::interpolate_age( (string) $options['body_text'] );
$dob_label    = Options::interpolate_age( (string) $options['dob_label'] );
$yes_label    = Options::interpolate_age( (string) $options['confirm_yes_label'] );
$no_label     = Options::interpolate_age( (string) $options['confirm_no_label'] );
$submit_label = Options::interpolate_age( (string) $options['submit_label'] );
$reject_head  = Options::interpolate_age( (string) $options['rejection_heading'] );
$reject_body  = Options::interpolate_age( (string) $options['rejection_body'] );
$custom_css   = (string) $options['custom_css'];
?><!DOCTYPE html>
<html lang="<?php echo esc_attr( $lang ); ?>">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<meta name="robots" content="noindex,nofollow" />
	<title><?php echo esc_html( $heading . ' — ' . $site_name ); ?></title>
	<link rel="stylesheet" href="<?php echo esc_url( $assets . 'css/frontend.css?ver=' . rawurlencode( $css_ver ) ); ?>" />
	<style id="hoay-vars">.hoay-overlay { <?php echo esc_html( $css_vars ); ?> }<?php if ( '' !== $custom_css ) : ?> <?php echo wp_strip_all_tags( $custom_css ); // Sanitised in Sanitizer::css. ?> <?php endif; ?></style>
</head>
<body class="hoay-body">
	<div class="hoay-overlay" role="dialog" aria-modal="true" aria-labelledby="hoay-heading">
		<div class="hoay-panel">
			<?php if ( $logo_url ) : ?>
				<img class="hoay-logo" src="<?php echo esc_url( $logo_url ); ?>" alt="" />
			<?php endif; ?>

			<div class="hoay-step hoay-step--ask" data-step="ask">
				<h1 id="hoay-heading" class="hoay-heading"><?php echo esc_html( $heading ); ?></h1>

				<?php if ( '' !== trim( $body_text ) ) : ?>
					<p class="hoay-body-text"><?php echo esc_html( $body_text ); ?></p>
				<?php endif; ?>

				<form id="hoay-form" class="hoay-form" novalidate
					data-mode="<?php echo esc_attr( $mode ); ?>"
					data-min-age="<?php echo esc_attr( (string) $min_age ); ?>"
					data-ajax="<?php echo esc_url( $ajax_url ); ?>"
					data-nonce="<?php echo esc_attr( $nonce ); ?>">

					<?php if ( 'dob' === $mode ) : ?>
						<label for="hoay-dob" class="hoay-label"><?php echo esc_html( $dob_label ); ?></label>
						<input type="date" id="hoay-dob" name="dob"
							required
							min="<?php echo esc_attr( $min_dob ); ?>"
							max="<?php echo esc_attr( $max_dob ); ?>"
							class="hoay-input"
							autocomplete="bday" />
						<button type="submit" class="hoay-button hoay-button--primary"><?php echo esc_html( $submit_label ); ?></button>
					<?php else : ?>
						<div class="hoay-confirm-row">
							<button type="submit" class="hoay-button hoay-button--primary" data-confirm="yes"><?php echo esc_html( $yes_label ); ?></button>
							<button type="submit" class="hoay-button hoay-button--ghost" data-confirm="no"><?php echo esc_html( $no_label ); ?></button>
						</div>
					<?php endif; ?>

					<p class="hoay-error" role="alert" aria-live="polite" hidden></p>
				</form>
			</div>

			<div class="hoay-step hoay-step--reject" data-step="reject" hidden>
				<h1 class="hoay-heading"><?php echo esc_html( $reject_head ); ?></h1>
				<?php if ( '' !== trim( $reject_body ) ) : ?>
					<p class="hoay-body-text"><?php echo esc_html( $reject_body ); ?></p>
				<?php endif; ?>
			</div>
		</div>
	</div>

	<script src="<?php echo esc_url( $assets . 'js/frontend.js?ver=' . rawurlencode( $css_ver ) ); ?>"></script>
</body>
</html>
<?php // phpcs:ignore Squiz.PHP.EmbeddedPhp.SpacingAfterOpen ?>
