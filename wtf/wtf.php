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

require_once dirname(__FILE__) .'/config.php';
require_once dirname(__FILE__) .'/functions.php';
require_once dirname(__FILE__) .'/plugins.php';

add_action('init', 'wtf_init', 0);
add_action('after_setup_theme', 'wtf_setup');

add_action('admin_menu', 'wtf_admin');

// Add RSS links to <head> section
add_theme_support('automatic-feed-links');

if (!current_user_can('manage_options')) {
    add_action('wp_dashboard_setup', 'wtf_remove_dashboard_widgets');
}

function wtf_remove_dashboard_widgets() {
	global $wp_meta_boxes;

	unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_plugins']);
	unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_drafts']);
	unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']);
	unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_secondary']);
} //end wtf_remove_dashboard_widgets

remove_action('wp_head', 'wp_generator');

if (!is_admin()) {
    // Pull jQuery from Google CDN instead of local install
    wp_deregister_script('jquery');
    wp_register_script('jquery', ("http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"), false);
    wp_enqueue_script('jquery');

    remove_action('wp_head', 'wp_print_scripts');
    remove_action('wp_head', 'wp_print_head_scripts', 9);
    remove_action('wp_head', 'wp_enqueue_scripts', 1);
    add_action('wp_footer', 'wp_print_scripts', 5);
    add_action('wp_footer', 'wp_enqueue_scripts', 5);
    add_action('wp_footer', 'wp_print_head_scripts', 5);
}


if (function_exists('register_sidebar')) {
    register_sidebar(array(
        'name' => 'Sidebar Widgets',
        'id' => 'sidebar-widgets',
        'description' => 'These are widgets for the sidebar.',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h2>',
        'after_title' => '</h2>'
    ));
}

function wtf_init()
{
    wtf_remove_head_links();
    $taxonomies = 'build_' . THEME_NAMESPACE . '_taxonomies';
    if (function_exists($taxonomies)) {
        $taxonomies();
    }
    $post_types = 'build_' . THEME_NAMESPACE . '_post_types';
    if (function_exists($post_types)) {
        $post_types();
    }
} //end wtf_init

// Clean up the <head>
function wtf_remove_head_links()
{
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'wlwmanifest_link');
} //end wtf_remove_head_links

function wtf_setup()
{
    // First we check to see if our default theme settings have been applied.
    $the_theme_status = get_option('theme_setup_status');

    // If the theme has not yet been used we want to run our default settings.
    if ($the_theme_status !== '1') {
        $errors = false;

        // Delete dummy post, page and comment.
        wp_delete_post(1, true);
        wp_delete_post(2, true);
        wp_delete_comment(1);

        // Add default home and blog pages
        global $user_ID;
        $pages = array(
            'home' => array(
                'post_type'      => 'page',
                'post_content'   => '',
                'post_parent'    => 0,
                'post_author'    => $user_ID,
                'post_status'    => 'publish',
                'comment_status' => 'closed',
                'post_name'      => 'home',
                'post_title'     => 'Home',
            ),
            'blog' => array(
                'post_type'      => 'page',
                'post_content'   => '',
                'post_parent'    => 0,
                'post_author'    => $user_ID,
                'post_status'    => 'publish',
                'comment_status' => 'closed',
                'post_name'      => 'blog',
                'post_title'     => 'Blog',
            ),
        );
        foreach ($pages as &$page) {
            $pageid = wp_insert_post($page);
            if ($pageid != 0) {
                $page['post_id'] = $pageid;
            } else {
                $errors = true;
            }
        }

        // Setup Default WordPress settings
        $core_settings = array(
            'blogname'          => SITE_TITLE,
            'blogdescription'   => SITE_TAGLINE,
            'siteurl'           => WP_HOME,
            'home'              => SITE_HOME,
            'default_role'      => 'subscriber',
            'timezone_string'   => 'America/Chicago',
            'date_format'       => 'F j, Y',
            'time_format'       => 'g:i a',
            'start_of_week'     => '1', // 0-6 = Sun-Sat
            'avatar_default'    => 'mystery',
            'avatar_rating'     => 'G',
            'comments_per_page' => 20,
            'show_on_front'     => 'page',
            'page_on_front'     => $pages['home']['post_id'],
            'page_for_posts'    => $pages['blog']['post_id'],
        );
        foreach ($core_settings as $k => $v) {
            update_option($k, $v);
        }

        // Set the permalink structure to our desired default
        wtf_change_permalinks();

        // Once done, we register our setting to make sure we don't duplicate everytime we activate.
        update_option('theme_setup_status', '1');

        // Lets let the admin know whats going on.
        $msg = '
		<div class="updated">
			<p>The ' . get_option('current_theme') . 'theme has changed your WordPress default <a href="' . admin_url('options-general.php') . '" title="See Settings">settings</a> and deleted default posts & comments.</p>
		</div>';
        add_action('admin_notices', $c = create_function('', 'echo "' . addcslashes($msg, '"') . '";'));

        if ($errors) {
            $msg = '
            <div class="error">
                <p>There were errors while setting up the ' . get_option('current_theme') . 'theme. Please verify your settings are correct.</p>
            </div>';
            add_action('admin_notices', $c = create_function('', 'echo "' . addcslashes($msg, '"') . '";'));
        }
    } elseif ($the_theme_status === '1' and isset($_GET['activated'])) {
        // Else if we are re-activing the theme
        $msg = '
		<div class="updated">
			<p>The ' . get_option('current_theme') . ' theme was successfully re-activated.</p>
		</div>';
        add_action('admin_notices', $c = create_function('', 'echo "' . addcslashes($msg, '"') . '";'));
    }
} //end wtf_setup

function wtf_change_permalinks()
{
    global $wp_rewrite;
    $wp_rewrite->set_permalink_structure(THEME_PERMALINKS);
    $wp_rewrite->flush_rules();
} //end wtf_change_permalinks

function wtf_admin()
{
	$plugin_page = add_menu_page(WTF_THEME_NAME . " Options", WTF_THEME_NAME . " Options", 'edit_themes', basename(__FILE__), 'wtf_page');
	add_action('admin_head-' . $plugin_page, 'wtf_header');

	//set defaults
	if(!get_option('wtf_top_nav')) add_option('wtf_top_nav', 'pages');
	if(!get_option('wtf_rss_url')) add_option('wtf_rss_url', '');
	if(!get_option('wtf_rss_email')) add_option('wtf_rss_email', '');
	if(!get_option('wtf_tracking_code')) add_option('wtf_tracking_code', '');
	if(!get_option('wtf_custom_css')) add_option('wtf_custom_css', '');
	if(!get_option('wtf_exclude_rss_cats')) add_option('wtf_exclude_rss_cats', '');
} //end wtf_admin

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
} //end wtf_header

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
				<fieldset>
                    <legend class="screen-reader-text"><span>Top Navigation</span></legend>
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
} //end wtf_page