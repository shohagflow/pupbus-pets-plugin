<?php
/**
 * Login, registration, notices, assets, shortcode, admin, and auth POST handlers.
 *
 * @package Pupbus_Pets
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Flash messages (query arg).
 */
function pupbus_pets_render_notice(): string
{
    if (!isset($_GET['pupbus_pets_message'])) {
        return '';
    }

    $message_key = sanitize_key(wp_unslash($_GET['pupbus_pets_message']));
    $messages = array(
        'register_success' => array(__('Account created successfully! Welcome to the Pup Crew!', 'pupbus-pets'), 'success'),
        'register_error' => array(__('Could not create account. Please check your data.', 'pupbus-pets'), 'error'),
        'login_success' => array(__('Login successful.', 'pupbus-pets'), 'success'),
        'login_error' => array(__('Invalid email or password.', 'pupbus-pets'), 'error'),
        'profile_saved' => array(__('Profile updated successfully.', 'pupbus-pets'), 'success'),
        'profile_error' => array(__('Profile update failed.', 'pupbus-pets'), 'error'),
    );

    if (!isset($messages[$message_key])) {
        return '';
    }

    $message = $messages[$message_key][0];
    $tone = $messages[$message_key][1];
    $class = 'success' === $tone ? 'pupbus-pets-notice pupbus-pets-notice--success' : 'pupbus-pets-notice pupbus-pets-notice--error';

    return sprintf(
        '<div class="%1$s">%2$s</div>',
        esc_attr($class),
        esc_html($message)
    );
}

function pupbus_pets_redirect_with_message(string $message, string $url = ''): void
{
    if (empty($url)) {
        $url = wp_get_referer();
        if (!$url) {
            $url = home_url('/');
        }
    }

    $url = remove_query_arg('pupbus_pets_message', $url);
    $url = add_query_arg('pupbus_pets_message', $message, $url);

    wp_safe_redirect($url);
    exit;
}

function pupbus_pets_get_post_value(string $key): string
{
    if (!isset($_POST[$key])) {
        return '';
    }

    return sanitize_text_field(wp_unslash($_POST[$key]));
}



/**
 * Inline SVG icons.
 *
 * @param string $name Icon key.
 * @return string
 */
function pupbus_pets_icon(string $name): string
{
    $icons = array(
        'mail' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m22 7-8.991 5.727a2 2 0 0 1-2.009 0L2 7"/><rect width="20" height="16" x="2" y="4" rx="2"/></svg>',
        'lock' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>',
        'user' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>',
        'phone' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>',
        'map' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>',
        'home' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>',
        'phone-call' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/><path d="M14.05 2a9 9 0 0 1 8 8"/><path d="M14.05 6a5 5 0 0 1 4 4"/></svg>',
    );

    return $icons[$name] ?? '';
}

/**
 * Login + registration markup (plain CSS classes — no Tailwind dependency for layout).
 */
function pupbus_pets_auth_form_markup(): string
{
    ob_start();
    ?>
    <div class="pupbus-pets-auth-shell">
        <div class="pupbus-pets-auth-card">
            <?php echo wp_kses_post(pupbus_pets_render_notice()); ?>

            <div class="pupbus-pets-tab-panel" data-pupbus-pets-panel="login">
                <div class="pupbus-pets-auth-header">
                    <div class="pupbus-pets-auth-dog" aria-hidden="true">🐕</div>
                    <h1 class="pupbus-pets-auth-title"><?php esc_html_e('Welcome Back!', 'pupbus-pets'); ?></h1>
                    <p class="pupbus-pets-auth-subtitle"><?php esc_html_e("Sign in to manage your pup's adventures", 'pupbus-pets'); ?></p>
                </div>
                <div class="pupbus-pets-auth-body">
                    <form method="post" class="pupbus-pets-auth-form-stack">
                        <?php wp_nonce_field('pupbus_pets_login_action', 'pupbus_pets_login_nonce'); ?>
                        <input type="hidden" name="pupbus_pets_action" value="login">

                        <div class="pupbus-pets-field">
                            <label class="pupbus-pets-field-label" for="pupbus-pets-login-email"><?php esc_html_e('Email Address', 'pupbus-pets'); ?></label>
                            <div class="pupbus-pets-input-wrap">
                                <?php echo pupbus_pets_icon('mail'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                <input id="pupbus-pets-login-email" type="email" name="pupbus_pets_email" placeholder="<?php esc_attr_e('your@email.com', 'pupbus-pets'); ?>" required autocomplete="email">
                            </div>
                        </div>
                        <div class="pupbus-pets-field">
                            <label class="pupbus-pets-field-label" for="pupbus-pets-login-pass"><?php esc_html_e('Password', 'pupbus-pets'); ?></label>
                            <div class="pupbus-pets-input-wrap">
                                <?php echo pupbus_pets_icon('lock'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                <input id="pupbus-pets-login-pass" type="password" name="pupbus_pets_password" placeholder="••••••••" required autocomplete="current-password">
                            </div>
                        </div>

                        <div class="pupbus-pets-auth-row-between">
                            <label class="pupbus-pets-remember">
                                <input type="checkbox" name="pupbus_pets_remember" value="1">
                                <span><?php esc_html_e('Remember me', 'pupbus-pets'); ?></span>
                            </label>
                            <a class="pupbus-pets-forgot" href="<?php echo esc_url(wp_lostpassword_url()); ?>"><?php esc_html_e('Forgot password?', 'pupbus-pets'); ?></a>
                        </div>

                        <button type="submit" class="pupbus-pets-btn-coral"><?php esc_html_e('Sign In', 'pupbus-pets'); ?></button>
                    </form>

                    <p class="pupbus-pets-auth-footer-link">
                        <?php esc_html_e('New here?', 'pupbus-pets'); ?>
                        <button type="button" class="pupbus-pets-link-coral" data-pupbus-pets-tab="register"><?php esc_html_e('Registration', 'pupbus-pets'); ?></button>
                    </p>
                </div>
            </div>

            <div class="pupbus-pets-tab-panel pupbus-pets-hidden" data-pupbus-pets-panel="register">
                <div class="pupbus-pets-auth-header">
                    <div class="pupbus-pets-auth-dog" aria-hidden="true">🐕</div>
                    <h1 class="pupbus-pets-auth-title"><?php esc_html_e('Join the Pup Crew', 'pupbus-pets'); ?></h1>
                    <p class="pupbus-pets-auth-subtitle"><?php esc_html_e('Create your account to get started', 'pupbus-pets'); ?></p>
                </div>
                <div class="pupbus-pets-auth-body">
                    <div class="pupbus-pets-register-wrap">
                        <form method="post" class="pupbus-pets-register-form">
                            <?php wp_nonce_field('pupbus_pets_register_action', 'pupbus_pets_register_nonce'); ?>
                            <input type="hidden" name="pupbus_pets_action" value="register">

                            <div class="pupbus-pets-form-field">
                                <label class="pupbus-pets-form-label" for="pupbus-pets-reg-name"><?php esc_html_e('Full Name', 'pupbus-pets'); ?></label>
                                <div class="pupbus-pets-input-wrapper">
                                    <?php echo pupbus_pets_icon('user'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                    <input id="pupbus-pets-reg-name" type="text" name="pupbus_pets_name" placeholder="<?php esc_attr_e('Sarah Johnson', 'pupbus-pets'); ?>" required autocomplete="name">
                                </div>
                            </div>
                            <div class="pupbus-pets-form-field">
                                <label class="pupbus-pets-form-label" for="pupbus-pets-reg-email"><?php esc_html_e('Email', 'pupbus-pets'); ?></label>
                                <div class="pupbus-pets-input-wrapper">
                                    <?php echo pupbus_pets_icon('mail'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                    <input id="pupbus-pets-reg-email" type="email" name="pupbus_pets_email" placeholder="<?php esc_attr_e('your@email.com', 'pupbus-pets'); ?>" required autocomplete="email">
                                </div>
                            </div>
                            <div class="pupbus-pets-form-field">
                                <label class="pupbus-pets-form-label" for="pupbus-pets-reg-pass"><?php esc_html_e('Password', 'pupbus-pets'); ?></label>
                                <div class="pupbus-pets-input-wrapper">
                                    <?php echo pupbus_pets_icon('lock'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                    <input id="pupbus-pets-reg-pass" type="password" name="pupbus_pets_password" placeholder="<?php esc_attr_e('At least 6 characters', 'pupbus-pets'); ?>" minlength="6" required autocomplete="new-password">
                                </div>
                            </div>
                            <div class="pupbus-pets-form-field">
                                <label class="pupbus-pets-form-label" for="pupbus-pets-reg-phone"><?php esc_html_e('Phone Number', 'pupbus-pets'); ?></label>
                                <div class="pupbus-pets-input-wrapper">
                                    <?php echo pupbus_pets_icon('phone'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                    <input id="pupbus-pets-reg-phone" type="text" name="pupbus_pets_phone" placeholder="<?php esc_attr_e('(512) 555-0123', 'pupbus-pets'); ?>" required>
                                </div>
                            </div>
                            <div class="pupbus-pets-form-field">
                                <label class="pupbus-pets-form-label" for="pupbus-pets-reg-hood"><?php esc_html_e('Neighborhood', 'pupbus-pets'); ?></label>
                                <div class="pupbus-pets-input-wrapper">
                                    <?php echo pupbus_pets_icon('map'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                    <input id="pupbus-pets-reg-hood" type="text" name="pupbus_pets_neighborhood" placeholder="<?php esc_attr_e('East Austin', 'pupbus-pets'); ?>" required>
                                </div>
                            </div>
                            <div class="pupbus-pets-form-field">
                                <label class="pupbus-pets-form-label" for="pupbus-pets-reg-addr"><?php esc_html_e('Home Address', 'pupbus-pets'); ?></label>
                                <div class="pupbus-pets-input-wrapper">
                                    <?php echo pupbus_pets_icon('home'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                    <input id="pupbus-pets-reg-addr" type="text" name="pupbus_pets_home_address" placeholder="<?php esc_attr_e('1234 Oak Street, Austin, TX', 'pupbus-pets'); ?>" required>
                                </div>
                            </div>
                            <div class="pupbus-pets-form-field">
                                <label class="pupbus-pets-form-label" for="pupbus-pets-reg-em-name"><?php esc_html_e('Emergency Contact Name', 'pupbus-pets'); ?></label>
                                <div class="pupbus-pets-input-wrapper">
                                    <?php echo pupbus_pets_icon('user'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                    <input id="pupbus-pets-reg-em-name" type="text" name="pupbus_pets_emergency_contact_name" placeholder="<?php esc_attr_e('John Doe', 'pupbus-pets'); ?>">
                                </div>
                            </div>
                            <div class="pupbus-pets-form-field">
                                <label class="pupbus-pets-form-label" for="pupbus-pets-reg-em"><?php esc_html_e('Emergency Contact Phone', 'pupbus-pets'); ?></label>
                                <div class="pupbus-pets-input-wrapper">
                                    <?php echo pupbus_pets_icon('phone-call'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                    <input id="pupbus-pets-reg-em" type="text" name="pupbus_pets_emergency_phone" placeholder="<?php esc_attr_e('(512) 555-0124', 'pupbus-pets'); ?>" required>
                                </div>
                            </div>

                            <button type="submit" class="pupbus-pets-register-btn"><?php esc_html_e('Registration', 'pupbus-pets'); ?></button>
                        </form>

                        <p class="pupbus-pets-auth-footer-link">
                            <?php esc_html_e('Already a member?', 'pupbus-pets'); ?>
                            <button type="button" class="pupbus-pets-link-coral" data-pupbus-pets-tab="login"><?php esc_html_e('Sign in', 'pupbus-pets'); ?></button>
                        </p>
                    </div>
                </div>
            </div>
        </div>


    </div>
    <?php
    return (string) ob_get_clean();
}

function pupbus_pets_portal_shortcode(): string
{
    ob_start();
    ?>
    <section class="pupbus-pets-wrapper pupbus-pets-portal-section">
        <?php if (is_user_logged_in()) : ?>
            <?php echo wp_kses_post(pupbus_pets_profile_markup()); ?>
        <?php else : ?>
            <?php echo pupbus_pets_auth_form_markup(); ?>
        <?php endif; ?>
    </section>
    <?php
    return (string) ob_get_clean();
}
add_shortcode('pupbus_pets_portal', 'pupbus_pets_portal_shortcode');

function pupbus_pets_handle_register(): void
{
    if (!isset($_POST['pupbus_pets_register_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['pupbus_pets_register_nonce'])), 'pupbus_pets_register_action')) {
        pupbus_pets_redirect_with_message('register_error');
    }

    $name = pupbus_pets_get_post_value('pupbus_pets_name');
    $email = sanitize_email(wp_unslash($_POST['pupbus_pets_email'] ?? ''));
    $password = (string) ($_POST['pupbus_pets_password'] ?? '');
    $phone = pupbus_pets_get_post_value('pupbus_pets_phone');
    $neighborhood = pupbus_pets_get_post_value('pupbus_pets_neighborhood');
    $home_address = pupbus_pets_get_post_value('pupbus_pets_home_address');
    $emergency_phone = pupbus_pets_get_post_value('pupbus_pets_emergency_phone');
    $emergency_name = pupbus_pets_get_post_value('pupbus_pets_emergency_contact_name');

    if (!$name || !$email || strlen($password) < 6 || email_exists($email)) {
        pupbus_pets_redirect_with_message('register_error');
    }

    $email_local = explode('@', $email, 2);
    $raw_base = isset($email_local[0]) ? $email_local[0] : 'user';
    $username_base = sanitize_user($raw_base, true);
    if ('' === $username_base) {
        $username_base = 'user';
    }
    $username = $username_base;
    $counter = 1;

    while (username_exists($username)) {
        $username = $username_base . $counter;
        $counter++;
    }

    $user_id = wp_create_user($username, $password, $email);
    if (is_wp_error($user_id)) {
        pupbus_pets_redirect_with_message('register_error');
    }

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

    // Auto login the user
    wp_set_current_user($user_id);
    wp_set_auth_cookie($user_id, true);
    do_action('wp_login', $username, get_user_by('id', $user_id));

    // Redirect to profile page
    $redirect_url = wp_get_referer() ? wp_get_referer() : home_url('/');
    pupbus_pets_redirect_with_message('register_success', $redirect_url);
}

function pupbus_pets_handle_login(): void
{
    if (!isset($_POST['pupbus_pets_login_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['pupbus_pets_login_nonce'])), 'pupbus_pets_login_action')) {
        pupbus_pets_redirect_with_message('login_error');
    }

    $email = sanitize_email(wp_unslash($_POST['pupbus_pets_email'] ?? ''));
    $password = (string) ($_POST['pupbus_pets_password'] ?? '');

    if (!$email || !$password) {
        pupbus_pets_redirect_with_message('login_error');
    }

    $user = get_user_by('email', $email);
    if (!$user instanceof WP_User) {
        pupbus_pets_redirect_with_message('login_error');
    }

    $remember = isset($_POST['pupbus_pets_remember']) && '1' === (string) wp_unslash($_POST['pupbus_pets_remember']);

    $signon = wp_signon(
        array(
            'user_login' => $user->user_login,
            'user_password' => $password,
            'remember' => $remember,
        ),
        is_ssl()
    );

    if (is_wp_error($signon)) {
        pupbus_pets_redirect_with_message('login_error');
    }

    wp_set_current_user($user->ID);
    wp_set_auth_cookie($user->ID, $remember, is_ssl());
    do_action('wp_login', $user->user_login, $user);

    pupbus_pets_redirect_with_message('login_success');
}

function pupbus_pets_handle_actions(): void
{
    if ('POST' !== ($_SERVER['REQUEST_METHOD'] ?? '')) {
        return;
    }

    $action = isset($_POST['pupbus_pets_action']) ? sanitize_key(wp_unslash($_POST['pupbus_pets_action'])) : '';

    if ('register' === $action) {
        pupbus_pets_handle_register();
    }

    if ('login' === $action) {
        pupbus_pets_handle_login();
    }

    if ('profile_update' === $action) {
        pupbus_pets_handle_profile_update();
    }

    if ('logout' === $action) {
        pupbus_pets_handle_logout();
    }

    if ('pets_update' === $action) {
        pupbus_pets_handle_pets_update();
    }

    if ('pet_add' === $action) {
        pupbus_pets_handle_pet_add();
    }
}
add_action('init', 'pupbus_pets_handle_actions');

function pupbus_pets_register_admin_menu(): void
{
    add_menu_page(
        'Pupbus Pets',
        'Pupbus Pets',
        'manage_options',
        'pupbus-pets',
        'pupbus_pets_render_admin_page',
        'dashicons-pets',
        26
    );
}
add_action('admin_menu', 'pupbus_pets_register_admin_menu');

function pupbus_pets_render_admin_page(): void
{
    if (!current_user_can('manage_options')) {
        return;
    }

    $shortcode = '[pupbus_pets_portal]';
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Pupbus Pets', 'pupbus-pets'); ?></h1>
        <p><?php esc_html_e('Copy this shortcode and paste it into any page to show the login, registration, and profile portal.', 'pupbus-pets'); ?></p>

        <table class="form-table" role="presentation">
            <tbody>
                <tr>
                    <th scope="row"><label for="pupbus-pets-shortcode"><?php esc_html_e('Portal Shortcode', 'pupbus-pets'); ?></label></th>
                    <td>
                        <input
                            id="pupbus-pets-shortcode"
                            type="text"
                            class="regular-text code"
                            readonly
                            value="<?php echo esc_attr($shortcode); ?>"
                        />
                        <button type="button" class="button button-primary" id="pupbus-pets-copy-btn"><?php esc_html_e('Copy Shortcode', 'pupbus-pets'); ?></button>
                        <p class="description" id="pupbus-pets-copy-status"><?php esc_html_e('Ready to copy.', 'pupbus-pets'); ?></p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <script>
        (function () {
            var copyButton = document.getElementById('pupbus-pets-copy-btn');
            var shortcodeField = document.getElementById('pupbus-pets-shortcode');
            var statusLabel = document.getElementById('pupbus-pets-copy-status');

            if (!copyButton || !shortcodeField || !statusLabel) {
                return;
            }

            copyButton.addEventListener('click', function () {
                shortcodeField.select();
                shortcodeField.setSelectionRange(0, shortcodeField.value.length);

                var copied = false;
                if (navigator.clipboard && window.isSecureContext) {
                    navigator.clipboard.writeText(shortcodeField.value).then(function () {
                        statusLabel.textContent = '<?php echo esc_js(__('Shortcode copied.', 'pupbus-pets')); ?>';
                    }).catch(function () {
                        copied = document.execCommand('copy');
                        statusLabel.textContent = copied ? '<?php echo esc_js(__('Shortcode copied.', 'pupbus-pets')); ?>' : '<?php echo esc_js(__('Copy failed. Please copy manually.', 'pupbus-pets')); ?>';
                    });
                    return;
                }

                copied = document.execCommand('copy');
                statusLabel.textContent = copied ? '<?php echo esc_js(__('Shortcode copied.', 'pupbus-pets')); ?>' : '<?php echo esc_js(__('Copy failed. Please copy manually.', 'pupbus-pets')); ?>';
            });
        })();
    </script>
    <?php
}
