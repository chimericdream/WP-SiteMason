<?php
require_once dirname(__FILE__) . '/common.php';

global $wpsm_widgets;

$wpsm_widgets = array(
    'wpsm-widgets-collapse-arch' => array(
        'title'       => 'Collapsing Archives',
        'field_name'  => 'collapse_arch',
        'note'        => 'This enables you to have either a widget or template '
                       . 'function to enable collapsing archives in your '
                       . 'sidebar. Useful if you want to display your archives '
                       . 'but don\'t want to take up a lot of space with them.',
        'option_name' => 'wpsm-widgets-collapse-arch',
        'file'        => 'collapse-archives',
        'depends'     => array(),
    ),
    'wpsm-widgets-twitter' => array(
        'title'       => 'Twitter Feed',
        'field_name'  => 'twitter',
        'note'        => 'This enables a widget to show a Twitter feed in your '
                       . 'sidebar.',
        'option_name' => 'wpsm-widgets-twitter',
        'file'        => 'twitter',
        'depends'     => array(),
    ),
);

//set defaults
foreach ($wpsm_widgets as $name => $p) {
    if (!get_option($p['option_name'])) {
        add_option($p['option_name'], false);
    }
}

function wpsm_widget_page() {
    global $wpsm_widgets;
    wpsm_item_page($wpsm_widgets, 'widgets');
} //end wpsm_widget_page

function wpsm_widget_main_page() {
    ?>
    <div class="wrap">
        <div id="icon-themes" class="icon32"></div> <h2><?php echo WPSM_THEME_NAME; ?></h2>
        <p>As you enable widgets in the menu above ("<?php echo WPSM_THEME_NAME; ?>"->"Widgets"), their options pages will show up here.</p>
    </div>
    <?php
} //end wpsm_widget_main_page