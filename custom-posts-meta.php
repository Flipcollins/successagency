<?php
/*
	========================
	@author : Farai
	========================
*/

$contact = get_option('activate_contact');
if (@$contact == 1) {

    add_action('init', 'success_contact_custom_post_type');

    add_filter('manage_success-contact_posts_columns', 'success_set_contact_columns');
    add_action('manage_success-contact_posts_custom_column', 'success_contact_custom_column', 10, 2);

    add_action('add_meta_boxes', 'success_contact_add_meta_box');
    add_action('save_post', 'success_save_contact_email_data');

}

/* MAIL CONTACT FORM  */
function success_contact_custom_post_type()
{
    $labels = array(
        'name' => 'Messages',
        'singular_name' => 'Message',
        'menu_name' => 'Messages',
        'name_admin_bar' => 'Message'
    );

    $args = array(
        'labels' => $labels,
        'show_ui' => true,
        'show_in_menu' => true,
        'capability_type' => 'post',
        'hierarchical' => false,
        'menu_position' => 26,
        'menu_icon' => 'dashicons-email-alt',
        'supports' => array('title', 'editor', 'author')
    );

    register_post_type('success-contact', $args);

}

function success_set_contact_columns($columns)
{
    $newColumns = array();
    $newColumns['title'] = 'Full Name';
    $newColumns['message'] = 'Message';
    $newColumns['email'] = 'Success Agency Mailer';
    $newColumns['date'] = 'Date';
    return $newColumns;
}

function success_contact_custom_column($column, $post_id)
{

    switch ($column) {

        case 'message' :
            echo get_the_excerpt();
            break;

        case 'email' :
            $email = get_post_meta($post_id, '_contact_email_value_key', true);
            echo '<a href="mailto:' . $email . '">' . $email . '</a>';
            break;
    }

}

/* DEFINE CONTACT META BOXES */

function success_contact_add_meta_box()
{
    add_meta_box('contact_email', 'Mail Address', 'success_contact_email_callback', 'success-contact', 'side');
}

function success_contact_email_callback($post)
{
    wp_nonce_field('success_save_contact_email_data', 'success_contact_email_meta_box_nonce');

    $value = get_post_meta($post->ID, '_contact_email_value_key', true);

    echo '<label for="success_contact_email_field">Success Agency Mailer: </label>';
    echo '<input type="email" id="success_contact_email_field" name="success_contact_email_field" value="' . esc_attr($value) . '" size="25" />';
}

function success_save_contact_email_data($post_id)
{

    if (!isset($_POST['success_contact_email_meta_box_nonce'])) {
        return;
    }

    if (!wp_verify_nonce($_POST['success_contact_email_meta_box_nonce'], 'success_save_contact_email_data')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (!isset($_POST['success_contact_email_field'])) {
        return;
    }

    $my_data = sanitize_text_field($_POST['success_contact_email_field']);

    update_post_meta($post_id, '_contact_email_value_key', $my_data);

}













