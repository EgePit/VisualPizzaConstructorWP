<div id="vconst_settings">
    <h2>Admin settings</h2>
    <form method="post" action="<?php echo admin_url() ?>admin-post.php?action=save_settings">
        <?php $visual_constructor->get_pizzeria_settings();?>
        <input type="hidden" name="pizzeria" value="<?php echo $post->ID ?>"/>
        <button>Save</button>
    </form>
</div>