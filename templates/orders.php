<?php get_header();
if(!current_user_can('administrator') && ((int)get_post_meta($post->ID, 'owner', true) !== get_current_user_id())) {
    echo 'Not Allowed!';
    return;
}
?>
    <div id="vconst_orders">
        <h2>Orders</h2>
        <?php echo $visual_constructor->get_pizzeria_orders_html() ?>
    </div>
<?php
get_footer();