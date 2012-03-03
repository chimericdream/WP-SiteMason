<?php
//Redirect to the main plugin options page if form has been submitted
if (isset($_GET['action'])) {
    if ('refresh' == $_GET['action'] && isset($_GET['updated'])) {
        wp_redirect(admin_url('admin.php?page=theme-plugins-gcal&updated=refreshed'));
    }
}

add_settings_section('gce_refresh', 'Refresh Feed Cache', 'gce_refresh_main_text', 'refresh_feed');
//Unique ID                                  //Title                            //Function                //Page         //Section ID
add_settings_field('gce_refresh_id_field', 'Feed ID', 'gce_refresh_id_field', 'refresh_feed', 'gce_refresh');
add_settings_field('gce_refresh_title_field', 'Feed Title', 'gce_refresh_title_field', 'refresh_feed', 'gce_refresh');

//Main text
function gce_refresh_main_text() {
    ?>
    <p>The plugin will automatically refresh the cache when it expires, but you can manually clear the cache now by clicking the button below.</p>
    <p>Are you want you want to clear the cache data for this feed?</p>
    <?php
}

//ID
function gce_refresh_id_field() {
    $options = get_option(GCE_OPTIONS_NAME);
    $options = $options[$_GET['id']];
    ?>
    <input type="text" disabled="disabled" value="<?php echo $options['id']; ?>" size="3" />
    <input type="hidden" name="gce_options[id]" value="<?php echo $options['id']; ?>" />
    <?php
}

//Title
function gce_refresh_title_field() {
    $options = get_option(GCE_OPTIONS_NAME);
    $options = $options[$_GET['id']];
    ?>
    <input type="text" name="gce_options[title]" disabled="disabled" value="<?php echo $options['title']; ?>" size="50" />
    <?php
}