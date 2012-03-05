<?php
//Redirect to the main plugin options page if form has been submitted
if (isset($_GET['action'])) {
    if ($_GET['action'] == 'delete' && isset($_GET['updated'])) {
        wp_redirect(admin_url('admin.php?page=theme-plugins-gcal&updated=deleted'));
    }
}

add_settings_section('gce_delete', 'Delete Feed', 'gce_delete_main_text', 'delete_feed');
add_settings_field('gce_delete_id_field', 'Feed ID', 'gce_delete_id_field', 'delete_feed', 'gce_delete');
add_settings_field('gce_delete_title_field', 'Feed Title', 'gce_delete_title_field', 'delete_feed', 'gce_delete');

//Main text
function gce_delete_main_text() {
    ?>
    <p>Are you want you want to delete this feed? (Remember to remove / adjust any widgets or shortcodes associated with this feed).</p>
    <?php
}

//ID
function gce_delete_id_field() {
    $options = get_option(GCE_OPTIONS_NAME);
    $options = $options[$_GET['id']];
    ?>
    <input type="text" disabled="disabled" value="<?php echo $options['id']; ?>" size="3" />
    <input type="hidden" name="gce_options[id]" value="<?php echo $options['id']; ?>" />
    <?php
}

//Title
function gce_delete_title_field() {
    $options = get_option(GCE_OPTIONS_NAME);
    $options = $options[$_GET['id']];
    ?>
    <input type="text" name="gce_options[title]" disabled="disabled" value="<?php echo $options['title']; ?>" size="50" />
    <?php
}