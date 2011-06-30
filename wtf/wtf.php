<?php
/*
Wordpress Theme Framework
Version: 1.2
Author: Gilbert Pellegrom
Date: February 2010
URL: http://wtf.dev7studios.com/

==== VERSION HISTROY ====
v1.0 	- Release Version
v1.1 	- Bug Fix: Fixed page breadcrumbs ordering of li tags.
v1.2	- Design Update.
========================
*/

require_once (dirname(__FILE__) .'/config.php');
require_once (dirname(__FILE__) .'/functions.php');

add_action('admin_menu', 'wtf_admin');

function wtf_admin() 
{
	$plugin_page = add_menu_page(WTF_THEME_NAME." Options", WTF_THEME_NAME." Options", 'edit_themes', basename(__FILE__), 'wtf_page');
	add_action( 'admin_head-'. $plugin_page, 'wtf_header' );

	//set defaults
	if(!get_option('wtf_top_nav')) add_option('wtf_top_nav', 'pages');
	if(!get_option('wtf_rss_url')) add_option('wtf_rss_url', '');
	if(!get_option('wtf_rss_email')) add_option('wtf_rss_email', '');
	if(!get_option('wtf_tracking_code')) add_option('wtf_tracking_code', '');
	if(!get_option('wtf_custom_css')) add_option('wtf_custom_css', '');
	if(!get_option('wtf_exclude_rss_cats')) add_option('wtf_exclude_rss_cats', '');
}

function wtf_header()
{	
	?>
	<link rel="stylesheet" href="<?php bloginfo('template_url'); ?>/wtf/styles/style.css" type="text/css" media="screen" />
	<script type="text/javascript">
	jQuery(function($){
		$('#wtf_cat_exclude').bind('click', function(){
			var id = $('#cat option:selected').val();
			var list = $('#exclude_rss_cats').val();
			if(list == ''){
				list += '-' + id;
			} else {
				list += ',-' + id;
			}
			$('#exclude_rss_cats').val(list);
		});
		
		$('#footer-left').append(' | <a href="http://wtf.dev7studios.com/" target="_blank">WTF Documentation</a>');
	});
	</script>
	<?php
}

function wtf_page()
{	
	$saved = false;
	if($_REQUEST['action'] == 'save') {
		
		update_option('wtf_top_nav', strip_tags(stripslashes($_POST['top_nav'])));
		update_option('wtf_rss_url', strip_tags(stripslashes($_POST['rss_url'])));
		update_option('wtf_rss_email', strip_tags(stripslashes($_POST['email_url'])));
		update_option('wtf_tracking_code', strip_tags(stripslashes($_POST['tracking_code'])));
		update_option('wtf_custom_css', strip_tags(stripslashes($_POST['custom_css'])));
		update_option('wtf_exclude_rss_cats', strip_tags(stripslashes($_POST['exclude_rss_cats'])));
	
		$saved = true;
	}
?>
<div class="wrap">
	<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
		<div id="icon-themes" class="icon32"></div> <h2><?php echo WTF_THEME_NAME; ?> Options</h2>

		<?php if($saved){ ?><div class="updated fade" id="message"><p><strong>Settings saved.</strong></p></div><?php } ?>				
		
		<p>These settings are theme specific and will only apply when the current theme (<strong><?php echo WTF_THEME_NAME; ?></strong>) is enabled.</p><br />
		
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
				<label for="rss_url">RSS URL</label>
				<span class="description">Put your Feedburner (or other) custom feed URL in here.</span>
				<input type="text" class="regular-text" value="<?php echo get_option('wtf_rss_url'); ?>" id="rss_url" name="rss_url"/>
				<div class="clear"></div>
			</div>
			
			<div class="option">
				<label for="email_url">Subscribe by Email URL</label>
				<span class="description">Put your email subscription URL in here.</span>
				<input type="text" class="regular-text" value="<?php echo get_option('wtf_rss_email'); ?>" id="email_url" name="email_url"/>
				<div class="clear"></div>
			</div>
			
			<div class="option">
				<label for="tracking_code">Tracking Code</label>
				<span class="description">Paste your Google Analytics (or other) tracking code in here.</span>
				<textarea cols="50" rows="5" id="tracking_code" name="tracking_code"><?php echo get_option('wtf_tracking_code'); ?></textarea>
				<div class="clear"></div>
			</div>
			
			<div class="option">
				<label>Navigation Switcher</label>
				<fieldset><legend class="screen-reader-text"><span>Top Navigation</span></legend>
				<label title="Pages"><input type="radio" value="pages" name="top_nav" <?php if(get_option('wtf_top_nav') == 'pages'){ echo 'checked="checked"'; } ?>/> Pages</label><br/>
				<label title="Categories"><input type="radio" value="cats" name="top_nav" <?php if(get_option('wtf_top_nav') == 'cats'){ echo 'checked="checked"'; } ?>/> Categories</label>
				</fieldset>
				<div class="clear"></div>
			</div>
			
			<div class="option">
				<label for="custom_css">Custom CSS File</label>
				<span class="description">Put a custom CSS file name in here (e.g. blue_theme.css) relative to your theme's root directory.</span>
				<input type="text" class="regular-text" value="<?php echo get_option('wtf_custom_css'); ?>" id="custom_css" name="custom_css"/>
				<div class="clear"></div>
			</div>
			<!-- END OPTIONS -->
			
		</div>
		<!-- END SECTION -->
		
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
				<label for="exclude_rss_cats">Exclude Categories from RSS Feed</label>
				<span class="description">Comma separated list of Category ID's prefixed with a minus "-" (e.g. -3,-6,-11).</span>
				<input type="text" class="regular-text" value="<?php echo get_option('wtf_exclude_rss_cats'); ?>" id="exclude_rss_cats" name="exclude_rss_cats"/><br />
				<?php wp_dropdown_categories('show_count=0'); ?> <a id="wtf_cat_exclude">add</a>
				<div class="clear"></div>
			</div>
			<!-- END OPTIONS -->
			
		</div>
		<!-- END SECTION -->
	
	</form>
</div>

<div style="clear:both;height:20px;"></div>
 
<?php
}
?>