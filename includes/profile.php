<?php
/**
 * User profile, pet profiles markup, and related POST handlers.
 *
 * @package Pupbus_Pets
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Default empty pet row.
 *
 * @return array<string, string>
 */
function pupbus_pets_default_pet_row(): array
{
    return array(
        'id' => uniqid('pet_', true),
        'pet_name' => '',
        'breed' => '',
        'sex' => '',
        'birth_date' => '',
        'weight' => '',
        'color' => '',
        'microchip_id' => '',
        'rabies_until' => '',
        'dhpp_until' => '',
        'bordetella_until' => '',
        'vet_name' => '',
        'vet_clinic' => '',
        'vet_phone' => '',
        'allergies' => '',
        'medications' => '',
        'special_needs' => '',
        'behavior_notes' => '',
    );
}

/**
 * @param int $user_id User ID.
 * @return array<int, array<string, string>>
 */
function pupbus_pets_get_pets_for_user(int $user_id): array
{
    $raw = get_user_meta($user_id, 'pupbus_pets_pets_json', true);
    if (!is_string($raw) || '' === $raw) {
        return array();
    }

    $data = json_decode($raw, true);
    if (!is_array($data)) {
        return array();
    }

    return $data;
}

/**
 * @param int                               $user_id User ID.
 * @param array<int, array<string, string>> $pets    Pet rows.
 */
function pupbus_pets_save_pets_for_user(int $user_id, array $pets): void
{
    update_user_meta($user_id, 'pupbus_pets_pets_json', wp_json_encode($pets));
}

/**
 * @param array<string, mixed> $row Raw POST row.
 * @return array<string, string>
 */
function pupbus_pets_sanitize_pet_row(array $row): array
{
    $defaults = pupbus_pets_default_pet_row();
    $out = array();

    foreach ($defaults as $key => $_default) {
        if (!isset($row[$key])) {
            $out[$key] = '';
            continue;
        }

        $val = $row[$key];
        if (is_array($val)) {
            $val = '';
        }

        $val = wp_unslash((string) $val);

        if (in_array($key, array('special_needs', 'behavior_notes'), true)) {
            $out[$key] = sanitize_textarea_field($val);
        } else {
            $out[$key] = sanitize_text_field($val);
        }
    }

    if ('' === ($out['id'] ?? '')) {
        $out['id'] = uniqid('pet_', true);
    }

    return $out;
}

function pupbus_pets_handle_pets_update(): void
{
    if (!is_user_logged_in()) {
        pupbus_pets_redirect_with_message('profile_error');
    }

    if (!isset($_POST['pupbus_pets_pets_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['pupbus_pets_pets_nonce'])), 'pupbus_pets_pets_action')) {
        pupbus_pets_redirect_with_message('profile_error');
    }

    $user_id = get_current_user_id();

    if (!empty($_POST['pupbus_pets_remove_pet'])) {
        $remove_id = sanitize_text_field(wp_unslash((string) $_POST['pupbus_pets_remove_pet']));
        $pets = pupbus_pets_get_pets_for_user($user_id);
        $pets = array_values(
            array_filter(
                $pets,
                static function ($pet) use ($remove_id) {
                    return isset($pet['id']) && (string) $pet['id'] !== $remove_id;
                }
            )
        );
        pupbus_pets_save_pets_for_user($user_id, $pets);
        pupbus_pets_redirect_with_message('profile_saved');
    }

    $rows = isset($_POST['pupbus_pets_pets']) && is_array($_POST['pupbus_pets_pets']) ? $_POST['pupbus_pets_pets'] : array();
    $sanitized = array();

    foreach ($rows as $row) {
        if (!is_array($row)) {
            continue;
        }
        $sanitized[] = pupbus_pets_sanitize_pet_row($row);
    }

    pupbus_pets_save_pets_for_user($user_id, $sanitized);
    pupbus_pets_redirect_with_message('profile_saved');
}

function pupbus_pets_handle_pet_add(): void
{
    if (!is_user_logged_in()) {
        pupbus_pets_redirect_with_message('profile_error');
    }

    if (!isset($_POST['pupbus_pets_pet_add_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['pupbus_pets_pet_add_nonce'])), 'pupbus_pets_pet_add_action')) {
        pupbus_pets_redirect_with_message('profile_error');
    }

    $user_id = get_current_user_id();
    $pets = pupbus_pets_get_pets_for_user($user_id);
    $pets[] = pupbus_pets_default_pet_row();
    pupbus_pets_save_pets_for_user($user_id, $pets);
    pupbus_pets_redirect_with_message('profile_saved');
}

function pupbus_pets_handle_profile_update(): void
{
    if (!is_user_logged_in()) {
        pupbus_pets_redirect_with_message('profile_error');
    }

    if (!isset($_POST['pupbus_pets_profile_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['pupbus_pets_profile_nonce'])), 'pupbus_pets_profile_action')) {
        pupbus_pets_redirect_with_message('profile_error');
    }

    $user_id = get_current_user_id();
    $name = pupbus_pets_get_post_value('pupbus_pets_name');
    $phone = pupbus_pets_get_post_value('pupbus_pets_phone');
    $neighborhood = pupbus_pets_get_post_value('pupbus_pets_neighborhood');
    $home_address = pupbus_pets_get_post_value('pupbus_pets_home_address');
    $emergency_phone = pupbus_pets_get_post_value('pupbus_pets_emergency_phone');
    $emergency_name = pupbus_pets_get_post_value('pupbus_pets_emergency_contact_name');

    wp_update_user(
        array(
            'ID' => $user_id,
            'display_name' => $name,
        )
    );

    update_user_meta($user_id, 'pupbus_pets_name', $name);
    update_user_meta($user_id, 'pupbus_pets_phone', $phone);
    update_user_meta($user_id, 'pupbus_pets_neighborhood', $neighborhood);
    update_user_meta($user_id, 'pupbus_pets_home_address', $home_address);
    update_user_meta($user_id, 'pupbus_pets_emergency_phone', $emergency_phone);
    update_user_meta($user_id, 'pupbus_pets_emergency_contact_name', $emergency_name);

    pupbus_pets_redirect_with_message('profile_saved');
}

function pupbus_pets_handle_logout(): void
{
    if (!isset($_POST['pupbus_pets_logout_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['pupbus_pets_logout_nonce'])), 'pupbus_pets_logout_action')) {
        pupbus_pets_redirect_with_message('profile_error');
    }

    wp_logout();
    pupbus_pets_redirect_with_message('login_success');
}

/**
 * Logged-in member area markup.
 *
 * @return string
 */
function pupbus_pets_profile_markup(): string
{
    $user_id = get_current_user_id();
    $user = wp_get_current_user();

    $name = get_user_meta($user_id, 'pupbus_pets_name', true);
    $phone = get_user_meta($user_id, 'pupbus_pets_phone', true);
    $neighborhood = get_user_meta($user_id, 'pupbus_pets_neighborhood', true);
    $home_address = get_user_meta($user_id, 'pupbus_pets_home_address', true);
    $emergency_phone = get_user_meta($user_id, 'pupbus_pets_emergency_phone', true);
    $emergency_name = get_user_meta($user_id, 'pupbus_pets_emergency_contact_name', true);

    if (!$name) {
        $name = $user->display_name;
    }
    
    if (!$phone) {
        $phone = '';
    }
    
    if (!$neighborhood) {
        $neighborhood = '';
    }
    
    if (!$home_address) {
        $home_address = '';
    }
    
    if (!$emergency_phone) {
        $emergency_phone = '';
    }

    $joined = '';
    if (!empty($user->user_registered)) {
        $joined = date_i18n('F Y', strtotime($user->user_registered));
    }

    $pets = pupbus_pets_get_pets_for_user($user_id);

    ob_start();
    ?>
    <div class="pupbus-pets-profile-wrap">
        <?php echo wp_kses_post(pupbus_pets_render_notice()); ?>

        <section class="pupbus-pets-new-profile-section">
            <div class="pupbus-pets-new-profile-header">
                <div class="pupbus-pets-new-profile-header-left">
                    <div class="pupbus-pets-new-profile-avatar">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M18 20a6 6 0 0 0-12 0"></path>
                            <circle cx="12" cy="10" r="4"></circle>
                            <circle cx="12" cy="12" r="10"></circle>
                        </svg>
                    </div>
                    <div>
                        <h3 class="pupbus-pets-new-profile-name"><?php echo esc_html($name ?: __('Member', 'pupbus-pets')); ?></h3>
                        <p class="pupbus-pets-new-profile-subtitle">
                            <?php
                            echo esc_html(sprintf(
                                /* translators: %s: joined month/year */
                                __('Founding Member · %s', 'pupbus-pets'),
                                $joined ?: __('New member', 'pupbus-pets')
                            ));
                            ?>
                        </p>
                    </div>
                </div>
                <form method="post" id="pupbus-pets-owner-form">
                    <?php wp_nonce_field('pupbus_pets_profile_action', 'pupbus_pets_profile_nonce'); ?>
                    <input type="hidden" name="pupbus_pets_action" value="profile_update">
                    <button type="submit" class="pupbus-pets-new-profile-edit-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z"></path>
                            <path d="m15 5 4 4"></path>
                        </svg>
                        <?php esc_html_e('Edit Profile', 'pupbus-pets'); ?>
                    </button>
                </form>
            </div>

            <div class="pupbus-pets-new-profile-content">
                <div class="pupbus-pets-new-profile-grid">
                    <div class="pupbus-pets-new-profile-field">
                        <div class="pupbus-pets-new-profile-field-label">
                            <?php echo pupbus_pets_icon('user'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                            <?php esc_html_e('Full Name', 'pupbus-pets'); ?>
                        </div>
                        <div class="pupbus-pets-new-profile-field-value">
                            <input type="text" name="pupbus_pets_name" value="<?php echo esc_attr($name); ?>" placeholder="<?php esc_attr_e('Sarah Johnson', 'pupbus-pets'); ?>" required form="pupbus-pets-owner-form">
                        </div>
                    </div>
                    <div class="pupbus-pets-new-profile-field">
                        <div class="pupbus-pets-new-profile-field-label">
                            <?php echo pupbus_pets_icon('mail'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                            <?php esc_html_e('Email Address', 'pupbus-pets'); ?>
                        </div>
                        <div class="pupbus-pets-new-profile-field-value">
                            <span><?php echo esc_html($user->user_email); ?></span>
                        </div>
                    </div>
                    <div class="pupbus-pets-new-profile-field">
                        <div class="pupbus-pets-new-profile-field-label">
                            <?php echo pupbus_pets_icon('phone'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                            <?php esc_html_e('Phone Number', 'pupbus-pets'); ?>
                        </div>
                        <div class="pupbus-pets-new-profile-field-value">
                            <input type="text" name="pupbus_pets_phone" value="<?php echo esc_attr($phone); ?>" placeholder="<?php esc_attr_e('(512) 555-0123', 'pupbus-pets'); ?>" required form="pupbus-pets-owner-form">
                        </div>
                    </div>
                    <div class="pupbus-pets-new-profile-field">
                        <div class="pupbus-pets-new-profile-field-label">
                            <?php echo pupbus_pets_icon('map'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                            <?php esc_html_e('Neighborhood', 'pupbus-pets'); ?>
                        </div>
                        <div class="pupbus-pets-new-profile-field-value">
                            <input type="text" name="pupbus_pets_neighborhood" value="<?php echo esc_attr($neighborhood); ?>" placeholder="<?php esc_attr_e('East Austin', 'pupbus-pets'); ?>" required form="pupbus-pets-owner-form">
                        </div>
                    </div>
                    <div class="pupbus-pets-new-profile-field pupbus-pets-new-profile-field-full">
                        <div class="pupbus-pets-new-profile-field-label">
                            <?php echo pupbus_pets_icon('home'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                            <?php esc_html_e('Home Address', 'pupbus-pets'); ?>
                        </div>
                        <div class="pupbus-pets-new-profile-field-value">
                            <input type="text" name="pupbus_pets_home_address" value="<?php echo esc_attr($home_address); ?>" placeholder="<?php esc_attr_e('1234 Oak Street, Austin, TX', 'pupbus-pets'); ?>" required form="pupbus-pets-owner-form">
                        </div>
                    </div>
                    <div class="pupbus-pets-new-profile-field pupbus-pets-new-profile-field-full">
                        <div class="pupbus-pets-new-profile-field-label">
                            <?php echo pupbus_pets_icon('phone-call'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                            <?php esc_html_e('Emergency Contact Phone', 'pupbus-pets'); ?>
                        </div>
                        <div class="pupbus-pets-new-profile-field-value">
                            <input type="text" name="pupbus_pets_emergency_phone" value="<?php echo esc_attr($emergency_phone); ?>" placeholder="<?php esc_attr_e('(512) 555-0124', 'pupbus-pets'); ?>" required form="pupbus-pets-owner-form">
                        </div>
                    </div>
                </div>
            </div>
        </section>



        <p class="pupbus-pets-copy-footer"><?php esc_html_e('© Pupbus Pets · All rights reserved', 'pupbus-pets'); ?></p>
    </div>
    <?php
    return (string) ob_get_clean();
}
