<?php
add_action('admin_menu', 'wtf_collapse_arch_admin');

function wtf_collapse_arch_admin()
{
    $collapse_arch_page = add_submenu_page('theme-plugins', 'Collapsing Archives', 'Collapsing Archives', 'manage_options', 'theme-plugins-collapse-arch', 'wtf_collapse_arch_plugin_page');
    add_action('admin_head-' . $collapse_arch_page, 'wtf_header');
} //end wtf_twitter_admin

function wtf_collapse_arch_plugin_page()
{
    check_admin_referer();

    $title = 'Archives';
    $text = '';
    $showPostCount = 'yes';
    $archSortOrder = 'DESC';
    $defaultExpand = '';
    $number = '%i%';
    $expand = true;
    $customExpand = '';
    $customCollapse = '';
    $noTitle = '';
    $includeOrExcludeCategories = 'include';
    $includeOrExcludeYears = 'include';
    $categoriesToFilter = '';
    $yearsToFilter = '';
    $postTitleLength = '';
    $postDateAppend = 'after';
    $showPosts = 'yes';
    $linkToArch = 'yes';
    $showArchives = 'no';
    $expandYears = true;
    $expandCurrentYear = 'yes';
    $showYearCount = 'yes';
    $expandCurrentMonth = 'yes';
    $expandMonths = true;
    $showMonthCount = 'yes';
    $showMonths = 'yes';
    $showPostTitle = 'yes';
    $showPostDate = 'no';
    $postDateFormat = 'm/d';
    $animate = true;
    $debug = false;

    $saved = false;
    if ($_REQUEST['action'] == 'save') {
        $use_css = ($_POST['use_css'] == 'true') ? true : false;
        update_option('wtf-twitter-default-account', strip_tags(stripslashes($_POST['default_account'])));
        update_option('wtf-twitter-tweet-limit',     strip_tags(stripslashes($_POST['tweet_limit'])));
        update_option('wtf-twitter-time-limit',      strip_tags(stripslashes($_POST['time_limit'])));
        update_option('wtf-twitter-use-css',         $use_css);

        $saved = true;
    }
    ?>
        <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
            <div id="icon-themes" class="icon32"></div> <h2><?php echo WTF_THEME_NAME; ?></h2>
            <?php if ($saved) { ?><div class="updated fade" id="message"><p><strong>Settings saved.</strong></p></div><?php } ?>
            <p>These settings are theme specific and will only apply when the current theme (<strong><?php echo WTF_THEME_NAME; ?></strong>) is enabled.</p><br />
            <!-- START SECTION -->
            <div class="section">
                <div class="section-title">
                    <h3>Collapsing Archives Options</h3>
                    <input type="submit" value="Save Changes" class="button-primary" name="Submit"/>
                    <input type="hidden" name="action" value="save" />
                    <div class="clear"></div>
                </div>

                <!-- START OPTIONS -->
                <div class="option">
                    <label for="default_account">Default Twitter Account</label>
                    <span class="description">This is the default account to be used for any sidebar widgets.</span>
                    <input type="text" class="regular-text" value="<?php echo get_option('wtf-twitter-default-account'); ?>" id="default_account" name="default_account"/>
                    <div class="clear"></div>
                </div>

                <div class="option">
                    <label for="tweet_limit">Tweet Limit</label>
                    <span class="description">The maximum number of tweets to retrieve at a time.</span>
                    <input type="text" class="regular-text" value="<?php echo get_option('wtf-twitter-tweet-limit'); ?>" id="tweet_limit" name="tweet_limit"/>
                    <div class="clear"></div>
                </div>

                <div class="option">
                    <label for="time_limit">Time Limit</label>
                    <span class="description">This is the minimum time, in minutes, the widget will wait before fetching new tweets.</span>
                    <input type="text" class="regular-text" value="<?php echo get_option('wtf-twitter-time-limit'); ?>" id="time_limit" name="time_limit"/>
                    <div class="clear"></div>
                </div>

                <div class="option">
                    <label for="css_yes">Use CSS</label>
                    <span class="description">Enable the default styles for the Twitter widget. Turn this off if you prefer to use your own CSS rules.</span>
                    <fieldset>
                        <label for="css_yes"><input type="radio" value="true" id="css_yes" name="use_css"<?php
                        if ((bool) get_option('wtf-twitter-use-css') == true) {
                            echo ' checked="checked"';
                        }
                        ?> /> Yes</label><br />
                        <label for="css_no"><input type="radio" value="false" id="css_no" name="use_css"<?php
                        if ((bool) get_option('wtf-twitter-use-css') == false) {
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
} //end wtf_collapse_arch_plugin_page

/*
function wtf_twitter_plugin_page()
{
    $saved = false;
    if ($_REQUEST['action'] == 'save') {
        $use_css = ($_POST['use_css'] == 'true') ? true : false;
        update_option('wtf-twitter-default-account', strip_tags(stripslashes($_POST['default_account'])));
        update_option('wtf-twitter-tweet-limit',     strip_tags(stripslashes($_POST['tweet_limit'])));
        update_option('wtf-twitter-time-limit',      strip_tags(stripslashes($_POST['time_limit'])));
        update_option('wtf-twitter-use-css',         $use_css);

        $saved = true;
    }
    ?>
    <div class="wrap">
        <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
            <div id="icon-themes" class="icon32"></div> <h2><?php echo WTF_THEME_NAME; ?></h2>
            <?php if ($saved) { ?><div class="updated fade" id="message"><p><strong>Settings saved.</strong></p></div><?php } ?>
            <p>These settings are theme specific and will only apply when the current theme (<strong><?php echo WTF_THEME_NAME; ?></strong>) is enabled.</p><br />
            <!-- START SECTION -->
            <div class="section">
                <div class="section-title">
                    <h3>Twitter Options</h3>
                    <input type="submit" value="Save Changes" class="button-primary" name="Submit"/>
                    <input type="hidden" name="action" value="save" />
                    <div class="clear"></div>
                </div>

                <!-- START OPTIONS -->
                <div class="option">
                    <label for="default_account">Default Twitter Account</label>
                    <span class="description">This is the default account to be used for any sidebar widgets.</span>
                    <input type="text" class="regular-text" value="<?php echo get_option('wtf-twitter-default-account'); ?>" id="default_account" name="default_account"/>
                    <div class="clear"></div>
                </div>

                <div class="option">
                    <label for="tweet_limit">Tweet Limit</label>
                    <span class="description">The maximum number of tweets to retrieve at a time.</span>
                    <input type="text" class="regular-text" value="<?php echo get_option('wtf-twitter-tweet-limit'); ?>" id="tweet_limit" name="tweet_limit"/>
                    <div class="clear"></div>
                </div>

                <div class="option">
                    <label for="time_limit">Time Limit</label>
                    <span class="description">This is the minimum time, in minutes, the widget will wait before fetching new tweets.</span>
                    <input type="text" class="regular-text" value="<?php echo get_option('wtf-twitter-time-limit'); ?>" id="time_limit" name="time_limit"/>
                    <div class="clear"></div>
                </div>

                <div class="option">
                    <label for="css_yes">Use CSS</label>
                    <span class="description">Enable the default styles for the Twitter widget. Turn this off if you prefer to use your own CSS rules.</span>
                    <fieldset>
                        <label for="css_yes"><input type="radio" value="true" id="css_yes" name="use_css"<?php
                        if ((bool) get_option('wtf-twitter-use-css') == true) {
                            echo ' checked="checked"';
                        }
                        ?> /> Yes</label><br />
                        <label for="css_no"><input type="radio" value="false" id="css_no" name="use_css"<?php
                        if ((bool) get_option('wtf-twitter-use-css') == false) {
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
} //end wtf_twitter_plugin_page
*/