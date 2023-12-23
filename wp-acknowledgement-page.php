<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              
 * @since             1.0.0
 * @package           wp-acknowledgment-page
 *
 * @wordpress-plugin
 * Plugin Name:       wp-acknowledgment-page
 * Plugin URI:        
 * Description:       Redirect "Subscriber" users to an acknowledgment page and record their acknowledgment status.
 * Version:           1.0.0
 * Author:            Neosmith20
 * Author URI:        
 */
// Redirect "Subscriber" users to the acknowledgment page
function redirect_subscriber_users() {
    if (is_user_logged_in()) {
        $ack_page_slug = 'your-acknowledgment-page-slug'; // Replace with your actual page slug
        $ack_page_url = home_url($ack_page_slug);
        $subscriber_role = 'Subscriber'; // Replace with your actual role name

        if (current_user_can($subscriber_role) && !is_page($ack_page_slug) && !isset($_GET['acknowledged'])) {
            wp_redirect(add_query_arg('redirect_to', urlencode(home_url($_SERVER['REQUEST_URI'])), $ack_page_url));
            exit;
        }
    }
}
add_action('template_redirect', 'redirect_subscriber_users');

// Add acknowledgment buttons to the acknowledgment page
function add_acknowledgment_buttons() {
    if (is_page() && is_user_logged_in() && !isset($_GET['acknowledged'])) {
        $acknowledge_url = home_url('/');
        $do_not_acknowledge_url = 'https://www.google.com'; // Replace with your desired URL
        $user_role = 'Not Verified'; // Replace with your actual role name

        echo '<div style="text-align:center; margin-top:20px;">';
        echo '<form method="post">';
        echo '<input type="submit" name="acknowledge" value="Yes, I acknowledge" style="margin-right:10px;">';
        echo '<input type="submit" name="do_not_acknowledge" value="No, I don\'t">';
        echo '</form>';
        echo '</div>';

        if (isset($_POST['acknowledge'])) {
            // Record acknowledgment status, set user meta, and redirect to home page
            $user_id = get_current_user_id();
            $acknowledgment_status = get_user_meta($user_id, 'acknowledgment_status', true);

            if (!$acknowledgment_status) {
                update_user_meta($user_id, 'acknowledgment_status', true);
                wp_redirect($acknowledge_url);
                exit;
            }
        }

        if (isset($_POST['do_not_acknowledge'])) {
            // Record acknowledgment status and redirect based on user role
            $user_id = get_current_user_id();
            $acknowledgment_status = get_user_meta($user_id, 'acknowledgment_status', true);

            if (!$acknowledgment_status) {
                update_user_meta($user_id, 'acknowledgment_status', false);

                // Redirect based on user role
                if ($user_role === 'Not Verified') {
                    wp_redirect(get_edit_user_link($user_id));
                } else {
                    wp_redirect($do_not_acknowledge_url);
                }

                exit;
            }
        }
    }
}
add_action('wp_footer', 'add_acknowledgment_buttons');

// Add acknowledgment status to user meta (only once)
function save_acknowledgment_status() {
    $user_id = get_current_user_id();
    $acknowledgment_status = get_user_meta($user_id, 'acknowledgment_status', true);

    if (is_page() && is_user_logged_in() && !isset($_GET['acknowledged']) && !$acknowledgment_status) {
        // Record acknowledgment status if not recorded before
        update_user_meta($user_id, 'acknowledgment_status', false);
    }
}
add_action('template_redirect', 'save_acknowledgment_status');

// Display acknowledgment status in the admin area
function display_acknowledgment_status_column($columns) {
    $columns['acknowledgment_status'] = 'Acknowledgment Status';
    return $columns;
}
add_filter('manage_users_columns', 'display_acknowledgment_status_column');

function display_acknowledgment_status($value, $column_name, $user_id) {
    if ('acknowledgment_status' == $column_name) {
        $acknowledgment_status = get_user_meta($user_id, 'acknowledgment_status', true);
        $value = $acknowledgment_status ? 'Acknowledged' : 'Not Acknowledged';
    }
    return $value;
}
add_filter('manage_users_custom_column', 'display_acknowledgment_status', 10, 3);

// Add ability for admin to change acknowledgment status manually
function add_acknowledgment_status_field($user) {
    if (current_user_can('manage_options')) {
        $acknowledgment_status = get_user_meta($user->ID, 'acknowledgment_status', true);
        ?>
        <h3>Acknowledgment Status</h3>
        <table class="form-table">
            <tr>
                <th><label for="acknowledgment_status">Acknowledgment Status</label></th>
                <td>
                    <select name="acknowledgment_status" id="acknowledgment_status">
                        <option value="1" <?php selected($acknowledgment_status, true); ?>>Acknowledged</option>
                        <option value="0" <?php selected($acknowledgment_status, false); ?>>Not Acknowledged</option>
                    </select>
                </td>
            </tr>
        </table>
        <?php
    }
}
add_action('show_user_profile', 'add_acknowledgment_status_field');
add_action('edit_user_profile', 'add_acknowledgment_status_field');

function save_acknowledgment_status_field($user_id) {
    if (current_user_can('manage_options')) {
        $acknowledgment_status = isset($_POST['acknowledgment_status']) ? (bool)$_POST['acknowledgment_status'] : false;
        update_user_meta($user_id, 'acknowledgment_status', $acknowledgment_status);
    }
}
add_action('personal_options_update', 'save_acknowledgment_status_field');
add_action('edit_user_profile_update', 'save_acknowledgment_status_field');
