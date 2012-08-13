<?php
if (!defined('THEME_NAMESPACE')) {
    define('THEME_NAMESPACE', 'your_namespace_here');
}
if (!defined('WP_HOME')) {
    define('WP_HOME',   'http://www.yourdomain.com/path/to/wordpress/');
}
if (!defined('SITE_HOME')) {
    define('SITE_HOME', 'http://www.yourdomain.com/');
}
if (!defined('HOME_URI')) {
    define('HOME_URI', get_bloginfo('url'));
}
if (!defined('THEME_URI')) {
    define('THEME_URI', get_stylesheet_directory_uri());
}
if (!defined('THEME_URI_RELATIVE')) {
    define('THEME_URI_RELATIVE', str_replace(WP_HOME, '', THEME_URI));
}
if (!defined('THEME_IMAGES')) {
    define('THEME_IMAGES', THEME_URI . '/images');
}
if (!defined('THEME_CSS')) {
    define('THEME_CSS', THEME_URI . '/css');
}
if (!defined('THEME_JS')) {
    define('THEME_JS', THEME_URI . '/js');
}
if (!defined('WPSM_PATH')) {
    define('WPSM_PATH', TEMPLATEPATH . '/wpsm');
}
if (!defined('WPSM_URI')) {
    define('WPSM_URI', THEME_URI . '/wpsm');
}
if (!defined('WPSM_URI_RELATIVE')) {
    define('WPSM_URI_RELATIVE', str_replace(WP_HOME, '', WPSM_URI));
}
if (!defined('THEME_PERMALINKS')) {
    define('THEME_PERMALINKS', '/%year%/%monthnum%/%postname%/');
}
if (!defined('SITE_TITLE')) {
    define('SITE_TITLE',   'Your Site Title Here');
}
if (!defined('SITE_TAGLINE')) {
    define('SITE_TAGLINE', 'This is an awesome tagline');
}

define('WPSM_THEME_NAME', SITE_TITLE . ' Theme');

require_once WPSM_PATH . '/admin.php';
require_once WPSM_PATH . '/functions.php';
require_once WPSM_PATH . '/plugins.php';
require_once WPSM_PATH . '/utils.php';

add_action('init', 'wpsm_init', 0);
add_action('after_setup_theme', 'wpsm_setup');
add_action('admin_menu', 'wpsm_admin');
remove_action('wp_head', 'wp_generator');

// Add RSS links to <head> section
add_theme_support('automatic-feed-links');

if (!is_admin()) {
    // Pull jQuery from Google CDN instead of local install; fallback to local if needed
    wp_deregister_script('jquery');

    $url = 'https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js';
    $test_url = @fopen($url, 'r');
    if ($test_url !== false) {
        wp_register_script('jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js', false, '1.7.2');
    } else {  
        wp_register_script('jquery', WPSM_URI . '/../js/jquery-1.7.2.min.js', false, '1.7.2');
    }    
    
    wp_enqueue_script('jquery');

    remove_action('wp_head', 'wp_print_scripts');
    remove_action('wp_head', 'wp_print_head_scripts', 9);
    remove_action('wp_head', 'wp_enqueue_scripts', 1);
    add_action('wp_footer', 'wp_print_scripts', 5);
    add_action('wp_footer', 'wp_enqueue_scripts', 5);
    add_action('wp_footer', 'wp_print_head_scripts', 5);
}

function wpsm_init() {
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'wlwmanifest_link');

    $taxonomies = 'build_' . THEME_NAMESPACE . '_taxonomies';
    if (function_exists($taxonomies)) {
        $taxonomies();
    }
    $post_types = 'build_' . THEME_NAMESPACE . '_post_types';
    if (function_exists($post_types)) {
        $post_types();
    }
} //end wpsm_init

function wpsm_setup() {
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
        wpsm_change_permalinks();

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
} //end wpsm_setup

function wpsm_change_permalinks() {
    global $wp_rewrite;
    $wp_rewrite->set_permalink_structure(THEME_PERMALINKS);
    $wp_rewrite->flush_rules();
} //end wpsm_change_permalinks

if (defined('THEME_PATH')) {
    if (file_exists(THEME_PATH . '/inc/taxonomies.php')) {
        require_once THEME_PATH . '/inc/taxonomies.php';
    }
    if (file_exists(THEME_PATH . '/inc/post_types.php')) {
        require_once THEME_PATH . '/inc/post_types.php';
    }
}

function checkActivePage($url, $whichPage)
{
    if ($whichPage == 'home' && $url == '/') {
        return ' class="active"';
    }

    if (strpos($url, $whichPage) !== false) {
        return ' class="active"';
    }
    return '';
} //end checkActivePage