<?php
/**
 * Admin settings page view.
 *
 * Variables provided by SettingsPage::render():
 *
 * @var array<string,mixed> $options   Current settings, merged over defaults.
 * @var string              $group     Settings group slug.
 * @var string              $page_slug Admin page slug.
 *
 * @package HOAY
 */

defined( 'ABSPATH' ) || exit;

$logo_url = $options['logo_attachment_id']
	? (string) wp_get_attachment_image_url( (int) $options['logo_attachment_id'], 'medium' )
	: '';
?>
<div class="wrap hoay-settings">
	<h1><?php esc_html_e( 'Age Verification', 'how-old-are-you' ); ?></h1>
	<p class="description"><?php esc_html_e( 'Block under-age visitors from your public site with a customizable verification gate.', 'how-old-are-you' ); ?></p>

	<form action="options.php" method="post">
		<?php settings_fields( $group ); ?>

		<h2 class="title"><?php esc_html_e( 'Behavior', 'how-old-are-you' ); ?></h2>
		<table class="form-table" role="presentation">
			<tr>
				<th scope="row"><label for="hoay-enabled"><?php esc_html_e( 'Enable gate', 'how-old-are-you' ); ?></label></th>
				<td>
					<label>
						<input type="checkbox" id="hoay-enabled" name="hoay_settings[enabled]" value="1" <?php checked( ! empty( $options['enabled'] ) ); ?> />
						<?php esc_html_e( 'Block under-age visitors on the public frontend.', 'how-old-are-you' ); ?>
					</label>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="hoay-min-age"><?php esc_html_e( 'Minimum age', 'how-old-are-you' ); ?></label></th>
				<td>
					<input type="number" id="hoay-min-age" name="hoay_settings[minimum_age]" min="1" max="120" step="1" value="<?php echo esc_attr( (string) $options['minimum_age'] ); ?>" />
					<p class="description"><?php esc_html_e( 'Visitors below this age cannot enter.', 'how-old-are-you' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Verification mode', 'how-old-are-you' ); ?></th>
				<td>
					<fieldset>
						<label>
							<input type="radio" name="hoay_settings[verification_mode]" value="dob" <?php checked( 'dob', $options['verification_mode'] ); ?> />
							<?php esc_html_e( 'Date of birth — calculate age from the entered DOB.', 'how-old-are-you' ); ?>
						</label><br />
						<label>
							<input type="radio" name="hoay_settings[verification_mode]" value="confirm" <?php checked( 'confirm', $options['verification_mode'] ); ?> />
							<?php esc_html_e( 'Confirmation — visitor clicks "I am over X" / "I am under X".', 'how-old-are-you' ); ?>
						</label>
					</fieldset>
				</td>
			</tr>
		</table>

		<h2 class="title"><?php esc_html_e( 'Cookie', 'how-old-are-you' ); ?></h2>
		<table class="form-table" role="presentation">
			<tr>
				<th scope="row"><label for="hoay-cookie-name"><?php esc_html_e( 'Cookie name', 'how-old-are-you' ); ?></label></th>
				<td><input type="text" id="hoay-cookie-name" name="hoay_settings[cookie_name]" value="<?php echo esc_attr( $options['cookie_name'] ); ?>" class="regular-text" /></td>
			</tr>
			<tr>
				<th scope="row"><label for="hoay-cookie-lifetime"><?php esc_html_e( 'Cookie lifetime (days)', 'how-old-are-you' ); ?></label></th>
				<td><input type="number" id="hoay-cookie-lifetime" name="hoay_settings[cookie_lifetime_days]" min="1" max="365" step="1" value="<?php echo esc_attr( (string) $options['cookie_lifetime_days'] ); ?>" /></td>
			</tr>
			<tr>
				<th scope="row"><label for="hoay-cookie-samesite"><?php esc_html_e( 'SameSite', 'how-old-are-you' ); ?></label></th>
				<td>
					<select id="hoay-cookie-samesite" name="hoay_settings[cookie_same_site]">
						<?php foreach ( array( 'Lax', 'Strict', 'None' ) as $option ) : ?>
							<option value="<?php echo esc_attr( $option ); ?>" <?php selected( $option, $options['cookie_same_site'] ); ?>><?php echo esc_html( $option ); ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
		</table>

		<h2 class="title"><?php esc_html_e( 'Messages', 'how-old-are-you' ); ?></h2>
		<p class="description"><?php esc_html_e( 'Use {age} to insert the configured minimum age.', 'how-old-are-you' ); ?></p>
		<table class="form-table" role="presentation">
			<tr>
				<th scope="row"><label for="hoay-heading"><?php esc_html_e( 'Heading', 'how-old-are-you' ); ?></label></th>
				<td><input type="text" id="hoay-heading" name="hoay_settings[heading_text]" value="<?php echo esc_attr( $options['heading_text'] ); ?>" class="regular-text" /></td>
			</tr>
			<tr>
				<th scope="row"><label for="hoay-body"><?php esc_html_e( 'Body text', 'how-old-are-you' ); ?></label></th>
				<td><textarea id="hoay-body" name="hoay_settings[body_text]" rows="3" class="large-text"><?php echo esc_textarea( $options['body_text'] ); ?></textarea></td>
			</tr>
			<tr>
				<th scope="row"><label for="hoay-dob-label"><?php esc_html_e( 'DOB field label', 'how-old-are-you' ); ?></label></th>
				<td><input type="text" id="hoay-dob-label" name="hoay_settings[dob_label]" value="<?php echo esc_attr( $options['dob_label'] ); ?>" class="regular-text" /></td>
			</tr>
			<tr>
				<th scope="row"><label for="hoay-yes-label"><?php esc_html_e( '"Over X" button label', 'how-old-are-you' ); ?></label></th>
				<td><input type="text" id="hoay-yes-label" name="hoay_settings[confirm_yes_label]" value="<?php echo esc_attr( $options['confirm_yes_label'] ); ?>" class="regular-text" /></td>
			</tr>
			<tr>
				<th scope="row"><label for="hoay-no-label"><?php esc_html_e( '"Under X" button label', 'how-old-are-you' ); ?></label></th>
				<td><input type="text" id="hoay-no-label" name="hoay_settings[confirm_no_label]" value="<?php echo esc_attr( $options['confirm_no_label'] ); ?>" class="regular-text" /></td>
			</tr>
			<tr>
				<th scope="row"><label for="hoay-submit-label"><?php esc_html_e( 'Submit button label', 'how-old-are-you' ); ?></label></th>
				<td><input type="text" id="hoay-submit-label" name="hoay_settings[submit_label]" value="<?php echo esc_attr( $options['submit_label'] ); ?>" class="regular-text" /></td>
			</tr>
			<tr>
				<th scope="row"><label for="hoay-reject-heading"><?php esc_html_e( 'Rejection heading', 'how-old-are-you' ); ?></label></th>
				<td><input type="text" id="hoay-reject-heading" name="hoay_settings[rejection_heading]" value="<?php echo esc_attr( $options['rejection_heading'] ); ?>" class="regular-text" /></td>
			</tr>
			<tr>
				<th scope="row"><label for="hoay-reject-body"><?php esc_html_e( 'Rejection body', 'how-old-are-you' ); ?></label></th>
				<td><textarea id="hoay-reject-body" name="hoay_settings[rejection_body]" rows="3" class="large-text"><?php echo esc_textarea( $options['rejection_body'] ); ?></textarea></td>
			</tr>
		</table>

		<h2 class="title"><?php esc_html_e( 'Appearance', 'how-old-are-you' ); ?></h2>
		<table class="form-table" role="presentation">
			<tr>
				<th scope="row"><?php esc_html_e( 'Logo', 'how-old-are-you' ); ?></th>
				<td>
					<input type="hidden" id="hoay-logo-id" name="hoay_settings[logo_attachment_id]" value="<?php echo esc_attr( (string) $options['logo_attachment_id'] ); ?>" />
					<div class="hoay-media-preview" data-empty="<?php esc_attr_e( 'No logo selected', 'how-old-are-you' ); ?>">
						<?php if ( $logo_url ) : ?>
							<img src="<?php echo esc_url( $logo_url ); ?>" alt="" />
						<?php else : ?>
							<span class="hoay-media-empty"><?php esc_html_e( 'No logo selected', 'how-old-are-you' ); ?></span>
						<?php endif; ?>
					</div>
					<button type="button" class="button hoay-media-pick"><?php esc_html_e( 'Choose image', 'how-old-are-you' ); ?></button>
					<button type="button" class="button-link-delete hoay-media-clear"><?php esc_html_e( 'Remove', 'how-old-are-you' ); ?></button>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="hoay-bg-color"><?php esc_html_e( 'Background color', 'how-old-are-you' ); ?></label></th>
				<td><input type="text" id="hoay-bg-color" class="hoay-color" name="hoay_settings[background_color]" value="<?php echo esc_attr( $options['background_color'] ); ?>" /></td>
			</tr>
			<tr>
				<th scope="row"><label for="hoay-opacity"><?php esc_html_e( 'Overlay opacity', 'how-old-are-you' ); ?></label></th>
				<td><input type="number" id="hoay-opacity" name="hoay_settings[overlay_opacity]" min="0" max="1" step="0.01" value="<?php echo esc_attr( (string) $options['overlay_opacity'] ); ?>" /></td>
			</tr>
			<tr>
				<th scope="row"><label for="hoay-panel-color"><?php esc_html_e( 'Panel color', 'how-old-are-you' ); ?></label></th>
				<td><input type="text" id="hoay-panel-color" class="hoay-color" name="hoay_settings[panel_color]" value="<?php echo esc_attr( $options['panel_color'] ); ?>" /></td>
			</tr>
			<tr>
				<th scope="row"><label for="hoay-text-color"><?php esc_html_e( 'Text color', 'how-old-are-you' ); ?></label></th>
				<td><input type="text" id="hoay-text-color" class="hoay-color" name="hoay_settings[text_color]" value="<?php echo esc_attr( $options['text_color'] ); ?>" /></td>
			</tr>
			<tr>
				<th scope="row"><label for="hoay-accent-color"><?php esc_html_e( 'Accent color', 'how-old-are-you' ); ?></label></th>
				<td><input type="text" id="hoay-accent-color" class="hoay-color" name="hoay_settings[accent_color]" value="<?php echo esc_attr( $options['accent_color'] ); ?>" /></td>
			</tr>
			<tr>
				<th scope="row"><label for="hoay-custom-css"><?php esc_html_e( 'Custom CSS', 'how-old-are-you' ); ?></label></th>
				<td>
					<textarea id="hoay-custom-css" name="hoay_settings[custom_css]" rows="6" class="large-text code"><?php echo esc_textarea( $options['custom_css'] ); ?></textarea>
					<p class="description"><?php esc_html_e( 'Scoped to the verification overlay. Tags and obvious script sequences are stripped on save.', 'how-old-are-you' ); ?></p>
				</td>
			</tr>
		</table>

		<h2 class="title"><?php esc_html_e( 'Exclusions', 'how-old-are-you' ); ?></h2>
		<table class="form-table" role="presentation">
			<tr>
				<th scope="row"><label for="hoay-excluded-paths"><?php esc_html_e( 'Excluded paths', 'how-old-are-you' ); ?></label></th>
				<td>
					<textarea id="hoay-excluded-paths" name="hoay_settings[excluded_paths]" rows="4" class="large-text code"><?php echo esc_textarea( $options['excluded_paths'] ); ?></textarea>
					<p class="description"><?php esc_html_e( 'One path per line. Paths starting with the listed value bypass the gate (e.g. /privacy, /contact).', 'how-old-are-you' ); ?></p>
				</td>
			</tr>
		</table>

		<?php submit_button(); ?>
	</form>
</div>
