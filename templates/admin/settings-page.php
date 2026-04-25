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

$logo_url     = $options['logo_attachment_id']
	? (string) wp_get_attachment_image_url( (int) $options['logo_attachment_id'], 'medium' )
	: '';
$bg_image_url = $options['background_image_id']
	? (string) wp_get_attachment_image_url( (int) $options['background_image_id'], 'medium' )
	: '';

$default_bots = implode( ', ', array_slice( \HOAY\Support\BotDetector::DEFAULT_TOKENS, 0, 8 ) ) . ', …';
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
			<tr>
				<th scope="row"><?php esc_html_e( 'DOB input style', 'how-old-are-you' ); ?></th>
				<td>
					<fieldset>
						<label>
							<input type="radio" name="hoay_settings[dob_input_style]" value="native" <?php checked( 'native', $options['dob_input_style'] ); ?> />
							<?php esc_html_e( 'Native HTML5 date input (browser picker, follows the document language).', 'how-old-are-you' ); ?>
						</label><br />
						<label>
							<input type="radio" name="hoay_settings[dob_input_style]" value="selects" <?php checked( 'selects', $options['dob_input_style'] ); ?> />
							<?php esc_html_e( 'Site-localized dropdowns (day / month / year — month names always follow the site language).', 'how-old-are-you' ); ?>
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

		<h2 class="title"><?php esc_html_e( 'Appearance — Logo', 'how-old-are-you' ); ?></h2>
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
					<button type="button" class="button hoay-media-pick" data-target="logo"><?php esc_html_e( 'Choose image', 'how-old-are-you' ); ?></button>
					<button type="button" class="button-link-delete hoay-media-clear" data-target="logo"><?php esc_html_e( 'Remove', 'how-old-are-you' ); ?></button>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="hoay-logo-max"><?php esc_html_e( 'Logo max width (px)', 'how-old-are-you' ); ?></label></th>
				<td><input type="number" id="hoay-logo-max" name="hoay_settings[logo_max_width_px]" min="40" max="400" step="1" value="<?php echo esc_attr( (string) $options['logo_max_width_px'] ); ?>" /></td>
			</tr>
		</table>

		<h2 class="title"><?php esc_html_e( 'Appearance — Background', 'how-old-are-you' ); ?></h2>
		<table class="form-table" role="presentation">
			<tr>
				<th scope="row"><label for="hoay-bg-color"><?php esc_html_e( 'Background color', 'how-old-are-you' ); ?></label></th>
				<td><input type="text" id="hoay-bg-color" class="hoay-color" name="hoay_settings[background_color]" value="<?php echo esc_attr( $options['background_color'] ); ?>" /></td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Background image', 'how-old-are-you' ); ?></th>
				<td>
					<input type="hidden" id="hoay-bg-image-id" name="hoay_settings[background_image_id]" value="<?php echo esc_attr( (string) $options['background_image_id'] ); ?>" />
					<div class="hoay-media-preview hoay-media-preview--bg" data-empty="<?php esc_attr_e( 'No background image selected', 'how-old-are-you' ); ?>">
						<?php if ( $bg_image_url ) : ?>
							<img src="<?php echo esc_url( $bg_image_url ); ?>" alt="" />
						<?php else : ?>
							<span class="hoay-media-empty"><?php esc_html_e( 'No background image selected', 'how-old-are-you' ); ?></span>
						<?php endif; ?>
					</div>
					<button type="button" class="button hoay-media-pick" data-target="bg-image"><?php esc_html_e( 'Choose image', 'how-old-are-you' ); ?></button>
					<button type="button" class="button-link-delete hoay-media-clear" data-target="bg-image"><?php esc_html_e( 'Remove', 'how-old-are-you' ); ?></button>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="hoay-bg-size"><?php esc_html_e( 'Background image size', 'how-old-are-you' ); ?></label></th>
				<td>
					<select id="hoay-bg-size" name="hoay_settings[background_image_size]">
						<?php foreach ( array( 'cover', 'contain', 'auto' ) as $option ) : ?>
							<option value="<?php echo esc_attr( $option ); ?>" <?php selected( $option, $options['background_image_size'] ); ?>><?php echo esc_html( $option ); ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="hoay-opacity"><?php esc_html_e( 'Overlay opacity', 'how-old-are-you' ); ?></label></th>
				<td><input type="number" id="hoay-opacity" name="hoay_settings[overlay_opacity]" min="0" max="1" step="0.01" value="<?php echo esc_attr( (string) $options['overlay_opacity'] ); ?>" /></td>
			</tr>
			<tr>
				<th scope="row"><label for="hoay-blur"><?php esc_html_e( 'Backdrop blur (px)', 'how-old-are-you' ); ?></label></th>
				<td>
					<input type="number" id="hoay-blur" name="hoay_settings[backdrop_blur_px]" min="0" max="32" step="1" value="<?php echo esc_attr( (string) $options['backdrop_blur_px'] ); ?>" />
					<p class="description"><?php esc_html_e( 'Frosted-glass effect over the background. Set to 0 to disable.', 'how-old-are-you' ); ?></p>
				</td>
			</tr>
		</table>

		<h2 class="title"><?php esc_html_e( 'Appearance — Panel', 'how-old-are-you' ); ?></h2>
		<table class="form-table" role="presentation">
			<tr>
				<th scope="row"><label for="hoay-panel-color"><?php esc_html_e( 'Panel color', 'how-old-are-you' ); ?></label></th>
				<td><input type="text" id="hoay-panel-color" class="hoay-color" name="hoay_settings[panel_color]" value="<?php echo esc_attr( $options['panel_color'] ); ?>" /></td>
			</tr>
			<tr>
				<th scope="row"><label for="hoay-panel-width"><?php esc_html_e( 'Panel width (px)', 'how-old-are-you' ); ?></label></th>
				<td><input type="number" id="hoay-panel-width" name="hoay_settings[panel_width_px]" min="320" max="720" step="1" value="<?php echo esc_attr( (string) $options['panel_width_px'] ); ?>" /></td>
			</tr>
			<tr>
				<th scope="row"><label for="hoay-panel-padding"><?php esc_html_e( 'Panel padding (px)', 'how-old-are-you' ); ?></label></th>
				<td><input type="number" id="hoay-panel-padding" name="hoay_settings[panel_padding_px]" min="16" max="64" step="1" value="<?php echo esc_attr( (string) $options['panel_padding_px'] ); ?>" /></td>
			</tr>
			<tr>
				<th scope="row"><label for="hoay-panel-radius"><?php esc_html_e( 'Panel border radius (px)', 'how-old-are-you' ); ?></label></th>
				<td><input type="number" id="hoay-panel-radius" name="hoay_settings[panel_radius_px]" min="0" max="32" step="1" value="<?php echo esc_attr( (string) $options['panel_radius_px'] ); ?>" /></td>
			</tr>
		</table>

		<h2 class="title"><?php esc_html_e( 'Appearance — Typography', 'how-old-are-you' ); ?></h2>
		<table class="form-table" role="presentation">
			<tr>
				<th scope="row"><label for="hoay-font-family"><?php esc_html_e( 'Font family', 'how-old-are-you' ); ?></label></th>
				<td>
					<input type="text" id="hoay-font-family" class="regular-text" name="hoay_settings[font_family]" value="<?php echo esc_attr( $options['font_family'] ); ?>" placeholder='"Inter", system-ui, sans-serif' />
					<p class="description"><?php esc_html_e( 'CSS font stack. Leave empty to inherit a system default. Quotes, commas, hyphens, and spaces only.', 'how-old-are-you' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="hoay-font-size"><?php esc_html_e( 'Body font size (px)', 'how-old-are-you' ); ?></label></th>
				<td><input type="number" id="hoay-font-size" name="hoay_settings[font_size_base_px]" min="12" max="24" step="1" value="<?php echo esc_attr( (string) $options['font_size_base_px'] ); ?>" /></td>
			</tr>
			<tr>
				<th scope="row"><label for="hoay-heading-size"><?php esc_html_e( 'Heading font size (px)', 'how-old-are-you' ); ?></label></th>
				<td><input type="number" id="hoay-heading-size" name="hoay_settings[heading_size_px]" min="16" max="48" step="1" value="<?php echo esc_attr( (string) $options['heading_size_px'] ); ?>" /></td>
			</tr>
			<tr>
				<th scope="row"><label for="hoay-text-color"><?php esc_html_e( 'Text color', 'how-old-are-you' ); ?></label></th>
				<td><input type="text" id="hoay-text-color" class="hoay-color" name="hoay_settings[text_color]" value="<?php echo esc_attr( $options['text_color'] ); ?>" /></td>
			</tr>
			<tr>
				<th scope="row"><label for="hoay-text-align"><?php esc_html_e( 'Text alignment', 'how-old-are-you' ); ?></label></th>
				<td>
					<select id="hoay-text-align" name="hoay_settings[text_align]">
						<?php foreach ( array( 'left', 'center', 'right' ) as $option ) : ?>
							<option value="<?php echo esc_attr( $option ); ?>" <?php selected( $option, $options['text_align'] ); ?>><?php echo esc_html( $option ); ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
		</table>

		<h2 class="title"><?php esc_html_e( 'Appearance — Controls', 'how-old-are-you' ); ?></h2>
		<table class="form-table" role="presentation">
			<tr>
				<th scope="row"><label for="hoay-accent-color"><?php esc_html_e( 'Accent color', 'how-old-are-you' ); ?></label></th>
				<td><input type="text" id="hoay-accent-color" class="hoay-color" name="hoay_settings[accent_color]" value="<?php echo esc_attr( $options['accent_color'] ); ?>" /></td>
			</tr>
			<tr>
				<th scope="row"><label for="hoay-button-radius"><?php esc_html_e( 'Button border radius (px)', 'how-old-are-you' ); ?></label></th>
				<td><input type="number" id="hoay-button-radius" name="hoay_settings[button_radius_px]" min="0" max="32" step="1" value="<?php echo esc_attr( (string) $options['button_radius_px'] ); ?>" /></td>
			</tr>
			<tr>
				<th scope="row"><label for="hoay-input-radius"><?php esc_html_e( 'Input border radius (px)', 'how-old-are-you' ); ?></label></th>
				<td><input type="number" id="hoay-input-radius" name="hoay_settings[input_radius_px]" min="0" max="32" step="1" value="<?php echo esc_attr( (string) $options['input_radius_px'] ); ?>" /></td>
			</tr>
		</table>

		<h2 class="title"><?php esc_html_e( 'Appearance — Custom CSS', 'how-old-are-you' ); ?></h2>
		<table class="form-table" role="presentation">
			<tr>
				<th scope="row"><label for="hoay-custom-css"><?php esc_html_e( 'Custom CSS', 'how-old-are-you' ); ?></label></th>
				<td>
					<textarea id="hoay-custom-css" name="hoay_settings[custom_css]" rows="8" class="large-text code"><?php echo esc_textarea( $options['custom_css'] ); ?></textarea>
					<p class="description">
						<?php esc_html_e( 'Scoped to the verification overlay. Tags and obvious script sequences are stripped on save. The following CSS variables are available:', 'how-old-are-you' ); ?>
						<code>--hoay-bg</code>, <code>--hoay-panel</code>, <code>--hoay-text</code>, <code>--hoay-accent</code>,
						<code>--hoay-opacity</code>, <code>--hoay-blur</code>,
						<code>--hoay-panel-width</code>, <code>--hoay-panel-padding</code>, <code>--hoay-panel-radius</code>,
						<code>--hoay-button-radius</code>, <code>--hoay-input-radius</code>,
						<code>--hoay-font-size</code>, <code>--hoay-heading-size</code>,
						<code>--hoay-text-align</code>, <code>--hoay-logo-max</code>, <code>--hoay-bg-size</code>.
					</p>
				</td>
			</tr>
		</table>

		<h2 class="title"><?php esc_html_e( 'Crawlers', 'how-old-are-you' ); ?></h2>
		<p class="description"><?php esc_html_e( 'Let search engines and social-media unfurlers see the real page so the site can be indexed and link previews work. The age gate stays in place for human visitors.', 'how-old-are-you' ); ?></p>
		<table class="form-table" role="presentation">
			<tr>
				<th scope="row"><label for="hoay-seo-bot-bypass"><?php esc_html_e( 'Bypass for crawlers', 'how-old-are-you' ); ?></label></th>
				<td>
					<label>
						<input type="checkbox" id="hoay-seo-bot-bypass" name="hoay_settings[seo_bot_bypass]" value="1" <?php checked( ! empty( $options['seo_bot_bypass'] ) ); ?> />
						<?php esc_html_e( 'Allow known search engines and social-media unfurlers to see the real page.', 'how-old-are-you' ); ?>
					</label>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="hoay-seo-bot-uas"><?php esc_html_e( 'Crawler user agents', 'how-old-are-you' ); ?></label></th>
				<td>
					<textarea id="hoay-seo-bot-uas" name="hoay_settings[seo_bot_user_agents]" rows="6" class="large-text code" placeholder="Googlebot&#10;Bingbot&#10;facebookexternalhit&#10;Twitterbot"><?php echo esc_textarea( $options['seo_bot_user_agents'] ); ?></textarea>
					<p class="description">
						<?php esc_html_e( 'One token per line, case-insensitive substring match. Leave blank to use the built-in defaults:', 'how-old-are-you' ); ?>
						<code><?php echo esc_html( $default_bots ); ?></code>
					</p>
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
