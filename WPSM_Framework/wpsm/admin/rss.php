<?php
//set defaults
if (!get_option('wpsm_rss_url')) {
    add_option('wpsm_rss_url', '');
}
if (!get_option('wpsm_rss_email')) {
    add_option('wpsm_rss_email', '');
}
if (!get_option('wpsm_exclude_rss_cats')) {
    add_option('wpsm_exclude_rss_cats', '');
}

function wpsm_rss_page() {
    $saved = false;
    if ($_REQUEST['action'] == 'save') {

        update_option('wpsm_rss_url', strip_tags(stripslashes($_POST['rss_url'])));
        update_option('wpsm_rss_email', strip_tags(stripslashes($_POST['email_url'])));
        update_option('wpsm_exclude_rss_cats', strip_tags(stripslashes($_POST['exclude_rss_cats'])));

        $saved = true;
    }
    ?>
    <div class="wrap">
        <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
            <div id="icon-themes" class="icon32"></div> <h2><?php echo WPSM_THEME_NAME; ?></h2>
            <?php if ($saved) { ?><div class="updated fade" id="message"><p><strong>Settings saved.</strong></p></div><?php } ?>
            <p>These settings are theme specific and will only apply when the current theme (<strong><?php echo WPSM_THEME_NAME; ?></strong>) is enabled.</p><br />
            <!-- START SECTION -->
            <div class="section">
                <div class="section-title">
                    <h3>Feed Options</h3>
                    <input type="submit" value="Save Changes" class="button-primary" name="Submit"/>
                    <input type="hidden" name="action" value="save" />
                    <div class="clear"></div>
                </div>

                <!-- START OPTIONS -->
                <div class="option">
                    <label for="email_url">Subscribe by Email URL</label>
                    <span class="description">Put your email subscription URL in here.</span>
                    <input type="text" class="regular-text" value="<?php echo get_option('wpsm_rss_email'); ?>" id="email_url" name="email_url"/>
                    <div class="clear"></div>
                </div>

                <div class="option">
                    <label for="rss_url">RSS URL</label>
                    <span class="description">Put your Feedburner (or other) custom feed URL in here.</span>
                    <input type="text" class="regular-text" value="<?php echo get_option('wpsm_rss_url'); ?>" id="rss_url" name="rss_url"/>
                    <div class="clear"></div>
                </div>

                <div class="option">
                    <label for="exclude_rss_cats">Exclude Categories from RSS Feed</label>
                    <span class="description">Comma separated list of Category ID's prefixed with a minus "-" (e.g. -3,-6,-11).</span>
                    <input type="text" class="regular-text" value="<?php echo get_option('wpsm_exclude_rss_cats'); ?>" id="exclude_rss_cats" name="exclude_rss_cats"/><br />
    <?php wp_dropdown_categories('show_count=0'); ?> <a id="wpsm_cat_exclude">add</a>
                    <div class="clear"></div>
                </div>
                <!-- END OPTIONS -->
            </div>
            <!-- END SECTION -->

    </form>
</div>
<div style="clear:both;height:20px;"></div>
    <?php
} //end wpsm_rss_page