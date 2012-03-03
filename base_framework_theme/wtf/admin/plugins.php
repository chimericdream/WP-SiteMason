<?php
//set defaults
if (!get_option('wtf-plugins-wp-calendar')) {
    add_option('wtf-plugins-wp-calendar', false);
}
if (!get_option('wtf-plugins-collapse-arch')) {
    add_option('wtf-plugins-collapse-arch', false);
}
if (!get_option('wtf-plugins-google-calendar')) {
    add_option('wtf-plugins-google-calendar', false);
}
if (!get_option('wtf-plugins-twitter')) {
    add_option('wtf-plugins-twitter', false);
}

function wtf_plugin_page() {
    $saved = false;
    if ($_REQUEST['action'] == 'save') {
        $wp_cal = ($_POST['wp_calendar'] == 'true') ? true : false;
        $gcal = ($_POST['gcal'] == 'true') ? true : false;
        $twitter = ($_POST['twitter'] == 'true') ? true : false;
        $collapse_arch = ($_POST['collapse_arch'] == 'true') ? true : false;
        update_option('wtf-plugins-wp-calendar', $wp_cal);
        update_option('wtf-plugins-google-calendar', $gcal);
        update_option('wtf-plugins-twitter', $twitter);
        update_option('wtf-plugins-collapse-arch', $collapse_arch);

        $saved = true;
    }
    ?>
    <div class="wrap">
        <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
            <div id="icon-themes" class="icon32"></div> <h2><?php echo WTF_THEME_NAME; ?></h2>
    <?php if ($saved) { ?><div class="updated fade" id="message"><p><strong>Settings saved.</strong></p></div><?php } ?>
            <p>These plugins are theme specific and will only apply when the current theme (<strong><?php echo WTF_THEME_NAME; ?></strong>) is enabled.</p><br />
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
                $plugins = array(
                    array(
                        'title'       => 'Calendar (Google)',
                        'field_name'  => 'gcal',
                        'note'        => 'This enables you to use a Google Calendar feed for embedding in your site and sidebar.',
                        'option_name' => 'wtf-plugins-google-calendar',
                    ),
                    array(
                        'title'       => 'Calendar (WordPress)',
                        'field_name'  => 'wp_calendar',
                        'note'        => 'This enables you to use a WordPress-based calendar for embedding in your site and sidebar.',
                        'option_name' => 'wtf-plugins-wp-calendar',
                    ),
                    array(
                        'title'       => 'Collapsing Archives',
                        'field_name'  => 'collapse_arch',
                        'note'        => 'This enables you to have either a widget or template function to enable collapsing archives in your sidebar. Useful if you want to display your archives but don\'t want to take up a lot of space with them.',
                        'option_name' => 'wtf-plugins-collapse-arch',
                    ),
                    array(
                        'title'       => 'Twitter Feed',
                        'field_name'  => 'twitter',
                        'note'        => 'This enables a plugin to show a Twitter feed in your sidebar.',
                        'option_name' => 'wtf-plugins-twitter',
                    ),
                );
                foreach ($plugins as $p) {
                    $title       = $p['title'];
                    $field_name  = $p['field_name'];
                    $note        = $p['note'];
                    $option_name = $p['option_name'];
                    wtf_plugin_option_html($title, $field_name, $note, $option_name);
                }
                ?>
                <!-- END OPTIONS -->
            </div>
            <!-- END SECTION -->
        </form>
    </div>
    <div style="clear:both;height:20px;"></div>
    <?php
} //end wtf_plugin_page

function wtf_plugin_option_html($title, $field_name, $note, $option_name)
{
    ?>
                <div class="option">
                    <label for="<?php echo $field_name; ?>_yes"><?php echo $title; ?></label>
                    <span class="description"><?php echo $note; ?></span>
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
} //end wtf_plugin_option_html

function wtf_plugin_main_page() {
    ?>
    <div class="wrap">
        <div id="icon-themes" class="icon32"></div> <h2><?php echo WTF_THEME_NAME; ?></h2>
        <p>As you enable plugins in the menu above ("<?php echo WTF_THEME_NAME; ?>"->"Plugins"), their options pages will show up here.</p>
    </div>
    <?php
} //end wtf_plugin_main_page