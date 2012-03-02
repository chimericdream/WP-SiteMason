<?php

/*
  Wordpress Theme Framework
  Version: 1.2
  Author: Gilbert Pellegrom
  Date: February 2010
  URL: http://wtf.dev7studios.com/

  ==== VERSION HISTROY ====
  v1.0     - Release Version
  v1.1     - Bug Fix: Fixed page breadcrumbs ordering of li tags.
  v1.2    - Design Update.
  ========================
 */

//Edit this to customize the theme name in the options menu
define('WTF_THEME_NAME', SITE_TITLE . ' Theme');
add_action('init', 'wtf_init', 0);
add_action('after_setup_theme', 'wtf_setup');

add_action('admin_menu', 'wtf_admin');

require_once dirname(__FILE__) . '/admin.php';
require_once dirname(__FILE__) . '/functions.php';
require_once dirname(__FILE__) . '/widgets.php';

// Add RSS links to <head> section
add_theme_support('automatic-feed-links');

if (!current_user_can('manage_options')) {
    add_action('wp_dashboard_setup', 'wtf_remove_dashboard_widgets');
}

function wtf_remove_dashboard_widgets()
{
    global $wp_meta_boxes;

    unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_plugins']);
    unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_drafts']);
    unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']);
    unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_secondary']);
}

//end wtf_remove_dashboard_widgets

remove_action('wp_head', 'wp_generator');

if (!is_admin()) {
    // Pull jQuery from Google CDN instead of local install
    wp_deregister_script('jquery');
    $jqprotocol = is_ssl() ? 'https' : 'http';
    wp_register_script('jquery', ("{$jqprotocol}://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"), false);
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
}

//end wtf_init
// Clean up the <head>
function wtf_remove_head_links()
{
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'wlwmanifest_link');
}

//end wtf_remove_head_links

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
                'post_type' => 'page',
                'post_content' => '',
                'post_parent' => 0,
                'post_author' => $user_ID,
                'post_status' => 'publish',
                'comment_status' => 'closed',
                'post_name' => 'home',
                'post_title' => 'Home',
            ),
            'blog' => array(
                'post_type' => 'page',
                'post_content' => '',
                'post_parent' => 0,
                'post_author' => $user_ID,
                'post_status' => 'publish',
                'comment_status' => 'closed',
                'post_name' => 'blog',
                'post_title' => 'Blog',
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
            'blogname' => SITE_TITLE,
            'blogdescription' => SITE_TAGLINE,
            'siteurl' => WP_HOME,
            'home' => SITE_HOME,
            'default_role' => 'subscriber',
            'timezone_string' => 'America/Chicago',
            'date_format' => 'F j, Y',
            'time_format' => 'g:i a',
            'start_of_week' => '1', // 0-6 = Sun-Sat
            'avatar_default' => 'mystery',
            'avatar_rating' => 'G',
            'comments_per_page' => 20,
            'show_on_front' => 'page',
            'page_on_front' => $pages['home']['post_id'],
            'page_for_posts' => $pages['blog']['post_id'],
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
}

//end wtf_setup

function wtf_change_permalinks()
{
    global $wp_rewrite;
    $wp_rewrite->set_permalink_structure(THEME_PERMALINKS);
    $wp_rewrite->flush_rules();
}

//end wtf_change_permalinks

function wtf_htaccess_optimization($rules)
{
    $smart_optimizer = <<<EOD
\n# BEGIN Smart Optimizer Code
<IfModule mod_expires.c>
    <FilesMatch "\.(gif|jpg|jpeg|png|swf|css|js|html?|xml|txt|ico)$">
        ExpiresActive On
        ExpiresDefault "access plus 10 years"
    </FilesMatch>
</IfModule>
<IfModule mod_rewrite.c>
    RewriteEngine On
    
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} !^.*wp-admin.*$
EOD;
    $smart_optimizer .= "\n" . '    RewriteRule ^(.*\.(js|css))$ ' . WTF_URI_RELATIVE . '/smartoptimizer/?$1' . "\n";
    $smart_optimizer .= <<<EOD

    <IfModule mod_expires.c>
        RewriteCond %{REQUEST_FILENAME} -f
        RewriteCond %{REQUEST_URI} !^.*wp-admin.*$
EOD;

    $smart_optimizer .= "\n" . '        RewriteRule ^(.*\.(js|css|html?|xml|txt))$ ' . WTF_URI_RELATIVE . '/smartoptimizer/?$1' . "\n";
    $smart_optimizer .= <<<EOD
    </IfModule>

    <IfModule !mod_expires.c>
        RewriteCond %{REQUEST_FILENAME} -f
        RewriteCond %{REQUEST_URI} !^.*wp-admin.*$
EOD;

    $smart_optimizer .= "\n" . '        RewriteRule ^(.*\.(gif|jpg|jpeg|png|swf|css|js|html?|xml|txt|ico))$ ' . WTF_URI_RELATIVE . '/smartoptimizer/?$1' . "\n";
    $smart_optimizer .= <<<EOD
    </IfModule>
</IfModule>
<FilesMatch "\.(gif|jpg|jpeg|png|swf|css|js|html?|xml|txt|ico)$">
    FileETag none
</FilesMatch>
# END Smart Optimizer Code\n
EOD;

    return $rules . $smart_optimizer;
}

add_filter('mod_rewrite_rules', 'wtf_htaccess_optimization');