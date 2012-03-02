<?php

function wtf_plugin_page() {
    $saved = false;
    if ($_REQUEST['action'] == 'save') {
        $wp_cal = ($_POST['wp_calendar'] == 'true') ? true : false;
        $gcal = ($_POST['gcal'] == 'true') ? true : false;
        $twitter = ($_POST['twitter'] == 'true') ? true : false;
        update_option('wtf-plugins-wp-calendar', $wp_cal);
        update_option('wtf-plugins-google-calendar', $gcal);
        update_option('wtf-plugins-twitter', $twitter);

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
                <div class="option">
                    <label for="twitter_yes">Twitter Feed</label>
                    <span class="description">This enables a plugin to show a Twitter feed in your sidebar.</span>
                    <fieldset>
                        <label for="twitter_yes"><input type="radio" value="true" id="twitter_yes" name="twitter"<?php
    if ((bool) get_option('wtf-plugins-twitter') == true) {
        echo ' checked="checked"';
    }
    ?> /> Yes</label><br />
                        <label for="twitter_no"><input type="radio" value="false" id="twitter_no" name="twitter"<?php
    if ((bool) get_option('wtf-plugins-twitter') == false) {
        echo ' checked="checked"';
    }
    ?> /> No</label>
                    </fieldset>
                    <div class="clear"></div>
                </div>

                <div class="option">
                    <label for="gcal_yes">Google Calendar</label>
                    <span class="description">This enables you to use a Google Calendar feed for embedding in your site and sidebar.</span>
                    <fieldset>
                        <label for="gcal_yes"><input type="radio" value="true" id="gcal_yes" name="gcal"<?php
    if ((bool) get_option('wtf-plugins-google-calendar') == true) {
        echo ' checked="checked"';
    }
    ?> /> Yes</label><br />
                        <label for="gcal_no"><input type="radio" value="false" id="gcal_no" name="gcal"<?php
    if ((bool) get_option('wtf-plugins-google-calendar') == false) {
        echo ' checked="checked"';
    }
    ?> /> No</label>
                    </fieldset>
                    <div class="clear"></div>
                </div>

                <div class="option">
                    <label for="wp_calendar_yes">WordPress Calendar</label>
                    <span class="description">This enables you to use a WordPress-based calendar for embedding in your site and sidebar.</span>
                    <fieldset>
                        <label for="wp_calendar_yes"><input type="radio" value="true" id="wp_calendar_yes" name="wp_calendar"<?php
    if ((bool) get_option('wtf-plugins-wp-calendar') == true) {
        echo ' checked="checked"';
    }
    ?> /> Yes</label><br />
                        <label for="wp_calendar_no"><input type="radio" value="false" id="wp_calendar_no" name="wp_calendar"<?php
    if ((bool) get_option('wtf-plugins-wp-calendar') == false) {
        echo ' checked="checked"';
    }
    ?> /> No</label>
                    </fieldset>
                    <div class="clear"></div>
                </div>
                <!-- END OPTIONS -->
            </div>
            <!-- END SECTION -->
        </form>
    </div>
    <div style="clear:both;height:20px;"></div>
    <?php
} //end wtf_plugin_page

function wtf_plugin_main_page() {
    ?>
    <div class="wrap">
        <div id="icon-themes" class="icon32"></div> <h2><?php echo WTF_THEME_NAME; ?></h2>
        <p>As you enable plugins in the menu above ("<?php echo WTF_THEME_NAME; ?>"->"Plugins"), their options pages will show up here.</p>
    </div>
    <?php
} //end wtf_plugin_main_page