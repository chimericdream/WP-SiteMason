<?php
require_once dirname(__FILE__) . '/common.php';

global $wpsm_utilities;

$wpsm_utilities = array(
);

//set defaults
foreach ($wpsm_utilities as $name => $p) {
    if (!get_option($p['option_name'])) {
        add_option($p['option_name'], false);
    }
}

function wpsm_utility_page() {
    global $wpsm_utilities;
    wpsm_item_page($wpsm_utilities, 'utilities');
} //end wpsm_utility_page

function wpsm_utility_main_page() {
    ?>
    <div class="wrap">
        <div id="icon-themes" class="icon32"></div> <h2><?php echo WPSM_THEME_NAME; ?></h2>
        <p>As you enable utilities in the menu above ("<?php echo WPSM_THEME_NAME; ?>"->"Utilities"), their options pages will show up here.</p>
    </div>
    <?php
} //end wpsm_utility_main_page