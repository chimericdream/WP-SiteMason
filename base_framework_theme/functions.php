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
if (!defined('WTF_PATH')) {
    define('WTF_PATH', TEMPLATEPATH . '/wtf');
}
if (!defined('WTF_URI')) {
    define('WTF_URI', THEME_URI . '/wtf');
}
if (!defined('WTF_URI_RELATIVE')) {
    define('WTF_URI_RELATIVE', str_replace(WP_HOME, '', WTF_URI));
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

require_once WTF_PATH . '/wtf/wtf.php';
if (file_exists(TEMPLATEPATH . '/inc/taxonomies.php')) {
    require_once TEMPLATEPATH . '/inc/taxonomies.php';
}
if (file_exists(TEMPLATEPATH . '/inc/post_types.php')) {
    require_once TEMPLATEPATH . '/inc/post_types.php';
}

add_theme_support('post-thumbnails');

add_action('init', THEME_NAMESPACE . '_init', 0);

// Load javascripts
if (!is_admin()) {
    wp_register_script(THEME_NAMESPACE . '_js_file', THEME_URI . '/scripts.js');
    wp_enqueue_script(THEME_NAMESPACE . '_js_file');

    if (function_exists('custom_posts_per_page')) {
        add_filter('pre_get_posts', 'custom_posts_per_page');
    }
}

function your_namespace_here_init()
{
    setup_extra_thumbnail_sizes();
}

// posts per page based on custom post types
function custom_posts_per_page($query)
{
    switch ($query->query_vars['post_type']) {
//        case 'post_type_name':
//            $query->query_vars['posts_per_page'] = 30;
//            break;
        default:
            break;
    }
    return $query;
}

function setup_extra_thumbnail_sizes()
{
//    add_image_size('image-size-name', width, height, (bool) crop);
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
}