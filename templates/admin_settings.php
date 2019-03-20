<form method="post" action="<?php echo admin_url() ?>admin-post.php?action=save_admin_settings">
<?php echo $this->get_superadmin_settings_page($saved_styles); ?>
<button type="submit">Save</button>
</form>
