<?php
define('THEME_NAMESPACE', 'your_namespace_here');
define('WP_HOME',   'http://www.yourdomain.com/path/to/wordpress/');
define('SITE_HOME', 'http://www.yourdomain.com/');
define('HOME_URI', get_bloginfo('url'));
define('THEME_URI', get_stylesheet_directory_uri());
define('THEME_URI_RELATIVE', str_replace(WP_HOME, '', THEME_URI));
define('THEME_IMAGES', THEME_URI . '/images');
define('THEME_CSS', THEME_URI . '/css');
define('THEME_JS', THEME_URI . '/js');
define('WTF_PATH', TEMPLATEPATH . '/wtf');
define('WTF_URI', THEME_URI . '/wtf');
define('WTF_URI_RELATIVE', str_replace(WP_HOME, '', WTF_URI));
define('THEME_PERMALINKS', '/%year%/%monthnum%/%postname%/');
define('SITE_TITLE',   'Your Site Title Here');
define('SITE_TAGLINE', 'This is an awesome tagline');

require_once TEMPLATEPATH . '/wtf/wtf.php';
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

    add_filter('pre_get_posts', 'custom_posts_per_page');
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