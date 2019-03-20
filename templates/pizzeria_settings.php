<?php get_header();
if(!current_user_can('administrator') && ((int)get_post_meta($post->ID, 'owner', true) !== get_current_user_id())) {
    echo 'Not Allowed!';
    return;
}
include_once('pizzeria_settings_form.php');
get_footer();