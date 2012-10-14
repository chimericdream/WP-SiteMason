<?php
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
    $saved = false;
    if ($_REQUEST['action'] == 'save') {
        foreach ($wpsm_widgets as $p) {
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
            <p>These widgets are theme specific and will only apply when the current theme (<strong><?php echo WPSM_THEME_NAME; ?></strong>) is enabled.</p><br />
            <!-- START SECTION -->
            <div class="section">
                <div class="section-title">
                    <h3>Available widgets</h3>
                    <input type="submit" value="Save Changes" class="button-primary" name="Submit"/>
                    <input type="hidden" name="action" value="save" />
                    <div class="clear"></div>
                </div>

                <!-- START OPTIONS -->
                <?php
                foreach ($wpsm_widgets as $p) {
                    $title       = $p['title'];
                    $field_name  = $p['field_name'];
                    $note        = $p['note'];
                    $option_name = $p['option_name'];
                    wpsm_widget_option_html($title, $field_name, $note, $option_name);
                }
                ?>
                <!-- END OPTIONS -->
            </div>
            <!-- END SECTION -->
        </form>
    </div>
    <div style="clear:both;height:20px;"></div>
    <?php
} //end wpsm_widget_page

function wpsm_widget_option_html($title, $field_name, $note, $option_name)
{
    global $wpsm_widgets;
    ?>
                <div class="option">
                    <label for="<?php echo $field_name; ?>_yes"><?php echo $title; ?></label>
                    <div class="description">
                        <p><?php echo $note; ?></p>
                        <?php if (!empty($wpsm_widgets[$option_name]['depends'])) : ?>
                        <h5>Dependencies:</h5>
                        <ul>
                        <?php foreach ($wpsm_widgets[$option_name]['depends'] as $d) : ?>
                            <li>
                                <?php echo $wpsm_widgets[$d]['title']; ?>:
                                <?php if (check_wpsm_item($d)) : ?>
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
} //end wpsm_widget_option_html

function wpsm_widget_main_page() {
    ?>
    <div class="wrap">
        <div id="icon-themes" class="icon32"></div> <h2><?php echo WPSM_THEME_NAME; ?></h2>
        <p>As you enable widgets in the menu above ("<?php echo WPSM_THEME_NAME; ?>"->"Widgets"), their options pages will show up here.</p>
    </div>
    <?php
} //end wpsm_widget_main_page