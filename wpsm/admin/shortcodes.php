<?php
require_once dirname(__FILE__) . '/common.php';

global $wpsm_shortcodes;

$wpsm_shortcodes = array(
    'wpsm-shortcodes-raw' => array(
        'title'       => 'Raw content shortcode',
        'field_name'  => 'raw_shortcode',
        'note'        => 'This enables a shortcode to embed raw content into '
                       . 'your pages using the following syntax:</p><p>[raw]'
                       . 'Your content here.[/raw]</p><p>The content will '
                       . 'not be parsed for automatic &lt;br&gt; tags or '
                       . '&lt;p&gt; tags, but HTML inside the [raw] tags will '
                       . 'still be parsed normally by the browser.',
        'option_name' => 'wpsm-shortcodes-raw',
        'file'        => 'raw',
        'depends'     => array(),
    ),
);

//set defaults
foreach ($wpsm_shortcodes as $name => $s) {
    if (!get_option($s['option_name'])) {
        add_option($s['option_name'], false);
    }
}

function wpsm_shortcode_page() {
    global $wpsm_shortcodes;
    wpsm_item_page($wpsm_shortcodes, 'shortcodes');
} //end wpsm_shortcode_page

function wpsm_shortcode_main_page() {
    ?>
    <div class="wrap">
        <div id="icon-themes" class="icon32"></div> <h2><?php echo WPSM_THEME_NAME; ?></h2>
        <p>As you enable shortcodes in the menu above ("<?php echo WPSM_THEME_NAME; ?>"->"Shortcodes"), their options pages will show up here.</p>
    </div>
    <?php
} //end wpsm_shortcode_main_page