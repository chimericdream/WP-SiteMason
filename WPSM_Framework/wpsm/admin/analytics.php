<?php
//set defaults
if (!get_option('wpsm_tracking_code')) {
    add_option('wpsm_tracking_code', '');
}

function wpsm_analytics_page() {
    $saved = false;
    if ($_REQUEST['action'] == 'save') {

        update_option('wpsm_tracking_code', strip_tags(stripslashes($_POST['tracking_code'])));

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
                    <h3>Analytics Options</h3>
                    <input type="submit" value="Save Changes" class="button-primary" name="Submit"/>
                    <input type="hidden" name="action" value="save" />
                    <div class="clear"></div>
                </div>

                <!-- START OPTIONS -->
                <div class="option">
                    <label for="tracking_code">Tracking Code</label>
                    <span class="description">Paste your Google Analytics (or other) tracking code in here.</span>
                    <textarea cols="50" rows="5" id="tracking_code" name="tracking_code"><?php echo get_option('wpsm_tracking_code'); ?></textarea>
                    <div class="clear"></div>
                </div>
                <!-- END OPTIONS -->
            </div>
            <!-- END SECTION -->

    </form>
</div>
<div style="clear:both;height:20px;"></div>
    <?php
} //end wpsm_analytics_page