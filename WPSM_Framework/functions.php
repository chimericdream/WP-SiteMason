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

require_once WPSM_PATH . '/wpsm.php';
if (defined('CHILD_THEME_PATH')) {
    if (file_exists(CHILD_THEME_PATH . '/inc/taxonomies.php')) {
        require_once CHILD_THEME_PATH . '/inc/taxonomies.php';
    }
    if (file_exists(CHILD_THEME_PATH . '/inc/post_types.php')) {
        require_once CHILD_THEME_PATH . '/inc/post_types.php';
    }
    if (file_exists(CHILD_THEME_PATH . '/inc/roles_capabilities.php')) {
        require_once CHILD_THEME_PATH . '/inc/roles_capabilities.php';
    }
    if (file_exists(CHILD_THEME_PATH . '/inc/admin_bar.php')) {
        require_once CHILD_THEME_PATH . '/inc/admin_bar.php';
        add_action('wp_before_admin_bar_render', 'build_' . THEME_NAMESPACE . '_admin_bar');
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

function is_blog ()
{
    global $post;
    $posttype = get_post_type($post);
    return ( ((is_archive()) || (is_author()) || (is_category()) || (is_home()) || (is_single()) || (is_tag())) && ( $posttype == 'post') ) ? true : false;
}