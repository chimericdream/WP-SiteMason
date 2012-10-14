<?php
global $wpsm_plugins;

$wpsm_plugins = array(
    'wpsm-plugins-collapse-arch' => array(
        'title'       => 'Collapsing Archives',
        'field_name'  => 'collapse_arch',
        'note'        => 'This enables you to have either a widget or template '
                       . 'function to enable collapsing archives in your '
                       . 'sidebar. Useful if you want to display your archives '
                       . 'but don\'t want to take up a lot of space with them.',
        'option_name' => 'wpsm-plugins-collapse-arch',
        'file'        => 'collapse-archives',
        'depends'     => array(),
    ),
    'wpsm-plugins-raw' => array(
        'title'       => 'Raw content shortcode',
        'field_name'  => 'raw_shortcode',
        'note'        => 'This enables a shortcode to embed raw content into '
                       . 'your pages using the following syntax:</p><p>[raw]'
                       . 'Your content here.[/raw]</p><p>The content will '
                       . 'not be parsed for automatic &lt;br&gt; tags or '
                       . '&lt;p&gt; tags, but HTML inside the [raw] tags will '
                       . 'still be parsed normally by the browser.',
        'option_name' => 'wpsm-plugins-raw',
        'file'        => 'raw',
        'depends'     => array(),
    ),
    'wpsm-plugins-twitter' => array(
        'title'       => 'Twitter Feed',
        'field_name'  => 'twitter',
        'note'        => 'This enables a plugin to show a Twitter feed in your '
                       . 'sidebar.',
        'option_name' => 'wpsm-plugins-twitter',
        'file'        => 'twitter',
        'depends'     => array(),
    ),
);

//set defaults
foreach ($wpsm_plugins as $name => $p) {
    if (!get_option($p['option_name'])) {
        add_option($p['option_name'], false);
    }
}

function wpsm_plugin_page() {
    global $wpsm_plugins;
    $saved = false;
    if ($_REQUEST['action'] == 'save') {
        foreach ($wpsm_plugins as $p) {
            $value = ($_POST[$p['field_name']] == 'true') ? true : false;
            update_option($p['option_name'], $value);
        }

        $saved = true;
    }
    ?>
    <div class="wrap">
        <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
            <div id="icon-themes" class="icon32"></div> <h2><?php echo WPSM_THEME_NAME; ?></h2>
    <?php if ($saved) { ?><div class="updated fade" id="message"><p><strong>Settings saved.</strong></p></div><?php } ?>
            <p>These plugins are theme specific and will only apply when the current theme (<strong><?php echo WPSM_THEME_NAME; ?></strong>) is enabled.</p><br />
            <!-- START SECTION -->
            <div class="section">
                <div class="section-title">
                    <h3>Available plugins</h3>
                    <input type="submit" value="Save Changes" class="button-primary" name="Submit"/>
                    <input type="hidden" name="action" value="save" />
                    <div class="clear"></div>
                </div>

                <!-- START OPTIONS -->
                <?php
                foreach ($wpsm_plugins as $p) {
                    $title       = $p['title'];
                    $field_name  = $p['field_name'];
                    $note        = $p['note'];
                    $option_name = $p['option_name'];
                    wpsm_plugin_option_html($title, $field_name, $note, $option_name);
                }
                ?>
                <!-- END OPTIONS -->
            </div>
            <!-- END SECTION -->
        </form>
    </div>
    <div style="clear:both;height:20px;"></div>
    <?php
} //end wpsm_plugin_page

function wpsm_plugin_option_html($title, $field_name, $note, $option_name)
{
    global $wpsm_plugins;
    ?>
                <div class="option">
                    <label for="<?php echo $field_name; ?>_yes"><?php echo $title; ?></label>
                    <div class="description">
                        <p><?php echo $note; ?></p>
                        <?php if (!empty($wpsm_plugins[$option_name]['depends'])) : ?>
                        <h5>Dependencies:</h5>
                        <ul>
                        <?php foreach ($wpsm_plugins[$option_name]['depends'] as $d) : ?>
                            <li>
                                <?php echo $wpsm_plugins[$d]['title']; ?>:
                                <?php if (check_wpsm_plugin($d)) : ?>
                                <span class="success">Active</span>
                                <?php else : ?>
                                <span class="error">Inactive</span>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                        </ul>
                        <?php endif; ?>
                    </div>
                    <fieldset>
                        <label for="<?php echo $field_name; ?>_yes"><input type="radio" value="true" id="<?php echo $field_name; ?>_yes" name="<?php echo $field_name; ?>"<?php
    if ((bool) get_option($option_name) == true) {
        echo ' checked="checked"';
    }
    ?> /> Yes</label><br />
                        <label for="<?php echo $field_name; ?>_no"><input type="radio" value="false" id="<?php echo $field_name; ?>_no" name="<?php echo $field_name; ?>"<?php
    if ((bool) get_option($option_name) == false) {
        echo ' checked="checked"';
    }
    ?> /> No</label>
                    </fieldset>
                    <div class="clear"></div>
                </div>
    <?php
} //end wpsm_plugin_option_html

function wpsm_plugin_main_page() {
    ?>
    <div class="wrap">
        <div id="icon-themes" class="icon32"></div> <h2><?php echo WPSM_THEME_NAME; ?></h2>
        <p>As you enable plugins in the menu above ("<?php echo WPSM_THEME_NAME; ?>"->"Plugins"), their options pages will show up here.</p>
    </div>
    <?php
} //end wpsm_plugin_main_page