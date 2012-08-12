<?php
//set defaults
if (!get_option('wpsm_top_nav')) {
    add_option('wpsm_top_nav', 'pages');
}
if (!get_option('wpsm_custom_css')) {
    add_option('wpsm_custom_css', '');
}

function wpsm_main_page() {
    $saved = false;
    if ($_REQUEST['action'] == 'save') {

        update_option('wpsm_top_nav', strip_tags(stripslashes($_POST['top_nav'])));
        update_option('wpsm_custom_css', strip_tags(stripslashes($_POST['custom_css'])));

        $saved = true;
    }
    ?>
    <div class="wrap">
        <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
            <div id="icon-themes" class="icon32"></div> <h2><?php echo WPSM_THEME_NAME; ?> Options</h2>
            <?php if ($saved) { ?><div class="updated fade" id="message"><p><strong>Settings saved.</strong></p></div><?php } ?>
            <p>These settings are theme specific and will only apply when the current theme (<strong><?php echo WPSM_THEME_NAME; ?></strong>) is enabled.</p><br />
            <!-- START SECTION -->
            <div class="section">
                <div class="section-title">
                    <h3>Site Options</h3>
                    <input type="submit" value="Save Changes" class="button-primary" name="Submit"/>
                    <input type="hidden" name="action" value="save" />
                    <div class="clear"></div>
                </div>

                <!-- START OPTIONS -->
                <div class="option">
                    <label>Navigation Switcher</label>
                    <fieldset>
                        <legend class="screen-reader-text"><span>Top Navigation</span></legend>
                        <label title="Pages"><input type="radio" value="pages" name="top_nav" <?php
        if (get_option('wpsm_top_nav') == 'pages') {
            echo 'checked="checked"';
        }
            ?>/> Pages</label><br/>
                        <label title="Categories"><input type="radio" value="cats" name="top_nav" <?php
                                                if (get_option('wpsm_top_nav') == 'cats') {
                                                    echo 'checked="checked"';
                                                }
                                                ?>/> Categories</label>
                    </fieldset>
                    <div class="clear"></div>
                </div>
                <div class="option">
                    <label for="custom_css">Custom CSS File</label>
                    <span class="description">Put a custom CSS file name in here (e.g. blue_theme.css) relative to your theme's root directory.</span>
                    <input type="text" class="regular-text" value="<?php echo get_option('wpsm_custom_css'); ?>" id="custom_css" name="custom_css"/>
                    <div class="clear"></div>
                </div>
                <!-- END OPTIONS -->
            </div>
            <!-- END SECTION -->
    </form>
</div>
<div style="clear:both;height:20px;"></div>
    <?php
} //end wpsm_main_page