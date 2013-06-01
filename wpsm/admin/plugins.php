<?php
require_once dirname(__FILE__) . '/common.php';

global $wpsm_plugins;

$wpsm_plugins = array(
);

//set defaults
foreach ($wpsm_plugins as $name => $p) {
    if (!get_option($p['option_name'])) {
        add_option($p['option_name'], false);
    }
}

function wpsm_plugin_page() {
    global $wpsm_plugins;
    wpsm_item_page($wpsm_plugins, 'plugins');
} //end wpsm_plugin_page

function wpsm_plugin_main_page() {
    ?>
    <div class="wrap">
        <div id="icon-themes" class="icon32"></div> <h2><?php echo WPSM_THEME_NAME; ?></h2>
        <p>As you enable plugins in the menu above ("<?php echo WPSM_THEME_NAME; ?>"->"Plugins"), their options pages will show up here.</p>
    </div>
    <?php
} //end wpsm_plugin_main_page