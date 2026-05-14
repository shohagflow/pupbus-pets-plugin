<?php
/**
 * Apply Now form markup and handlers.
 *
 * @package Pupbus_Pets
 */

if (! defined( 'ABSPATH' )) {
	exit;
}

/**
 * Handle Apply Now form submission.
 */
function pupbus_pets_handle_apply_now(): void
{
	if (! isset( $_POST['pupbus_pets_apply_now_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['pupbus_pets_apply_now_nonce'] )), 'pupbus_pets_apply_now_action' )) {
		return;
	}

	$parent_name = sanitize_text_field( wp_unslash( $_POST['parent_name'] ?? '' ));
	$parent_email = sanitize_email( wp_unslash( $_POST['parent_email'] ?? '' ));
	$parent_phone = sanitize_text_field( wp_unslash( $_POST['parent_phone'] ?? '' ));
	$neighborhood = sanitize_text_field( wp_unslash( $_POST['neighborhood'] ?? '' ));
	$dog_name = sanitize_text_field( wp_unslash( $_POST['dog_name'] ?? '' ));
	$dog_breed = sanitize_text_field( wp_unslash( $_POST['dog_breed'] ?? '' ));
	$dog_age = sanitize_text_field( wp_unslash( $_POST['dog_age'] ?? '' ));
	$session_preference = sanitize_text_field( wp_unslash( $_POST['session_preference'] ?? '' ));
	$pup_notes = sanitize_textarea_field( wp_unslash( $_POST['pup_notes'] ?? '' ));

	if (!$parent_name || !$parent_email || !$parent_phone || !$neighborhood || !$dog_name || !$dog_breed || !$dog_age || !$session_preference) {
		if (wp_doing_ajax()) {
			wp_send_json_error('Missing required fields');
		} else {
			wp_safe_redirect( home_url( '/' ) );
			exit;
		}
	}

	global $wpdb;
	$table_name = $wpdb->prefix . 'pupbus_applications';
	
	$wpdb->insert(
		$table_name,
		array(
			'parent_name' => $parent_name,
			'parent_email' => $parent_email,
			'parent_phone' => $parent_phone,
			'neighborhood' => $neighborhood,
			'dog_name' => $dog_name,
			'dog_breed' => $dog_breed,
			'dog_age' => $dog_age,
			'session_preference' => $session_preference,
			'pup_notes' => $pup_notes,
			'submitted_at' => current_time('mysql')
		),
		array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')
	);

	$redirect_url = wp_get_referer() ? wp_get_referer() : home_url( '/' );
	$redirect_url = add_query_arg( 'pupbus_pets_message', 'apply_success', $redirect_url );
	wp_safe_redirect( $redirect_url );
	exit;
}

/**
 * Shortcode for Apply Now form.
 */
function pupbus_pets_apply_now_shortcode(): string
{
	pupbus_pets_enqueue_assets();
	
	$is_success = isset($_GET['pupbus_pets_message']) && $_GET['pupbus_pets_message'] === 'apply_success';
	
	ob_start();
	?>
	<div class="pupbus-pets-apply-shell">
		<?php if (!$is_success) : ?>
			<div class="pupbus-pets-apply-card">
				<div class="pupbus-pets-apply-header">
					<h2 class="pupbus-pets-apply-title"><?php esc_html_e( 'Pup Parent Information', 'pupbus-pets' ); ?></h2>
				</div>
				
				<form method="post" class="pupbus-pets-apply-form">
					<?php wp_nonce_field( 'pupbus_pets_apply_now_action', 'pupbus_pets_apply_now_nonce' ); ?>
					<input type="hidden" name="pupbus_pets_action" value="apply_now">

					<!-- Parent Info Section -->
					<div class="pupbus-pets-apply-section">
						<div class="pupbus-pets-apply-field">
							<label class="pupbus-pets-apply-label" for="apply-parent-name"><?php esc_html_e( 'Your Name *', 'pupbus-pets' ); ?></label>
							<input type="text" id="apply-parent-name" name="parent_name" placeholder="<?php esc_attr_e( 'John Smith', 'pupbus-pets' ); ?>" required>
						</div>

						<div class="pupbus-pets-apply-grid">
							<div class="pupbus-pets-apply-field">
								<label class="pupbus-pets-apply-label" for="apply-parent-email"><?php esc_html_e( 'Email *', 'pupbus-pets' ); ?></label>
								<input type="email" id="apply-parent-email" name="parent_email" placeholder="<?php esc_attr_e( 'john@example.com', 'pupbus-pets' ); ?>" required>
							</div>
							<div class="pupbus-pets-apply-field">
								<label class="pupbus-pets-apply-label" for="apply-parent-phone"><?php esc_html_e( 'Phone *', 'pupbus-pets' ); ?></label>
								<input type="tel" id="apply-parent-phone" name="parent_phone" placeholder="<?php esc_attr_e( '(512) 555-0123', 'pupbus-pets' ); ?>" required>
							</div>
						</div>

						<div class="pupbus-pets-apply-field">
							<label class="pupbus-pets-apply-label" for="apply-neighborhood"><?php esc_html_e( 'Neighborhood *', 'pupbus-pets' ); ?></label>
							<input type="text" id="apply-neighborhood" name="neighborhood" placeholder="<?php esc_attr_e( 'e.g., East Austin, Hyde Park, etc.', 'pupbus-pets' ); ?>" required>
						</div>
					</div>

					<div class="pupbus-pets-apply-divider"></div>

					<!-- Pup Info Section -->
					<div class="pupbus-pets-apply-header">
						<h2 class="pupbus-pets-apply-title"><?php esc_html_e( "Your Pup's Information", 'pupbus-pets' ); ?></h2>
					</div>

					<div class="pupbus-pets-apply-section">
						<div class="pupbus-pets-apply-grid">
							<div class="pupbus-pets-apply-field">
								<label class="pupbus-pets-apply-label" for="apply-dog-name"><?php esc_html_e( "Dog's Name *", 'pupbus-pets' ); ?></label>
								<input type="text" id="apply-dog-name" name="dog_name" placeholder="<?php esc_attr_e( 'Max', 'pupbus-pets' ); ?>" required>
							</div>
							<div class="pupbus-pets-apply-field">
								<label class="pupbus-pets-apply-label" for="apply-dog-breed"><?php esc_html_e( 'Breed *', 'pupbus-pets' ); ?></label>
								<input type="text" id="apply-dog-breed" name="dog_breed" placeholder="<?php esc_attr_e( 'Golden Retriever', 'pupbus-pets' ); ?>" required>
							</div>
						</div>

						<div class="pupbus-pets-apply-field">
							<label class="pupbus-pets-apply-label" for="apply-dog-age"><?php esc_html_e( 'Age *', 'pupbus-pets' ); ?></label>
							<input type="text" id="apply-dog-age" name="dog_age" placeholder="<?php esc_attr_e( '3 years', 'pupbus-pets' ); ?>" required>
						</div>

						<div class="pupbus-pets-apply-field">
							<label class="pupbus-pets-apply-label" for="apply-session-pref"><?php esc_html_e( 'Session Preference *', 'pupbus-pets' ); ?></label>
							<div class="pupbus-pets-apply-select-wrap">
								<select id="apply-session-pref" name="session_preference" required>
									<option value="" disabled selected><?php esc_html_e( 'Select a preference', 'pupbus-pets' ); ?></option>
									<option value="morning"><?php esc_html_e( 'Morning', 'pupbus-pets' ); ?></option>
									<option value="afternoon"><?php esc_html_e( 'Afternoon', 'pupbus-pets' ); ?></option>
									<option value="full-day"><?php esc_html_e( 'Full Day', 'pupbus-pets' ); ?></option>
								</select>
							</div>
						</div>

						<div class="pupbus-pets-apply-field">
							<label class="pupbus-pets-apply-label" for="apply-pup-notes"><?php esc_html_e( 'Tell us about your pup! (Optional)', 'pupbus-pets' ); ?></label>
							<textarea id="apply-pup-notes" name="pup_notes" rows="4" placeholder="<?php esc_attr_e( 'Personality, favorite activities, any special needs or behaviors we should know about...', 'pupbus-pets' ); ?>"></textarea>
						</div>
					</div>

					<div class="pupbus-pets-apply-footer">
						<button type="submit" class="pupbus-pets-apply-btn">
							<span><?php esc_html_e( 'Submit Application', 'pupbus-pets' ); ?></span>
							<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/></svg>
						</button>
						<p class="pupbus-pets-apply-disclaimer">
							<?php esc_html_e( 'By submitting, you agree to our terms and conditions. We\'ll never share your information.', 'pupbus-pets' ); ?>
						</p>
					</div>
				</form>
			</div>
		<?php else : ?>
			<div class="pupbus-pets-apply-card pupbus-pets-apply-card--success">
				<div class="pupbus-pets-success-icon-wrap">
					<div class="pupbus-pets-success-icon">
						<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
					</div>
				</div>

				<h1 class="pupbus-pets-success-title"><?php esc_html_e( 'Application Submitted!', 'pupbus-pets' ); ?></h1>
				
				<p class="pupbus-pets-success-text">
					<?php esc_html_e( 'Thank you for applying to join the founding Pup Crew!', 'pupbus-pets' ); ?><br>
					<?php esc_html_e( "We'll review your application and get back to you within 2-3 business days.", 'pupbus-pets' ); ?>
				</p>

				<div class="pupbus-pets-success-next-box">
					<h3 class="pupbus-pets-success-next-title"><?php esc_html_e( "What's Next?", 'pupbus-pets' ); ?></h3>
					<ul class="pupbus-pets-success-next-list">
						<li><?php esc_html_e( "We'll review your pup's profile", 'pupbus-pets' ); ?></li>
						<li><?php esc_html_e( 'Schedule a brief phone call to discuss fit', 'pupbus-pets' ); ?></li>
						<li><?php esc_html_e( 'Coordinate your neighborhood Pup Stop location', 'pupbus-pets' ); ?></li>
						<li><?php esc_html_e( 'Welcome to the Pack!', 'pupbus-pets' ); ?></li>
					</ul>
				</div>
			</div>
		<?php endif; ?>
	</div>
	<?php
	return (string) ob_get_clean();
}
add_shortcode( 'pupbus_pets_apply_now', 'pupbus_pets_apply_now_shortcode' );

/**
 * Shortcode for Application Success page (legacy).
 */
function pupbus_pets_apply_success_shortcode(): string
{
	pupbus_pets_enqueue_assets();
	return pupbus_pets_apply_now_shortcode();
}
add_shortcode( 'pupbus_pets_apply_success', 'pupbus_pets_apply_success_shortcode' );
