<?php
define('THEME_NAMESPACE', 'your_namespace_here');
define('WP_HOME',   'http://www.yourdomain.com/path/to/wordpress/');
define('SITE_HOME', 'http://www.yourdomain.com/');
define('HOME_URI', get_bloginfo('url'));
define('CHILD_THEME_PATH', STYLESHEETPATH);
define('THEME_URI', get_stylesheet_directory_uri());
define('THEME_URI_RELATIVE', str_replace(WP_HOME, '', THEME_URI));
define('THEME_IMAGES', THEME_URI . '/images');
define('THEME_CSS', THEME_URI . '/css');
define('THEME_JS', THEME_URI . '/js');
define('THEME_FRAMEWORK_PATH', TEMPLATEPATH . '/../base_framework_theme');
define('WTF_PATH', TEMPLATEPATH . '/../base_framework_theme/wtf');
define('WTF_URI', THEME_URI . '/../base_framework_theme/wtf');
define('WTF_URI_RELATIVE', str_replace(WP_HOME, '', WTF_URI));
define('THEME_PERMALINKS', '/%year%/%monthnum%/%postname%/');
define('SITE_TITLE',   'Your Site Title Here');
define('SITE_TAGLINE', 'This is an awesome tagline');

add_theme_support('post-thumbnails');
add_action('init', THEME_NAMESPACE . '_init', 0);

if (!is_admin()) {
//    wp_register_script(THEME_NAMESPACE . '_js_file', THEME_URI . '/scripts.js');
//    wp_enqueue_script(THEME_NAMESPACE . '_js_file');

    if (function_exists('custom_posts_per_page')) {
        add_filter('pre_get_posts', 'custom_posts_per_page');
    }
}

function your_namespace_here_init()
{
    if (function_exists('setup_extra_thumbnail_sizes')) {
        setup_extra_thumbnail_sizes();
    }
} //end your_namespace_here_init

// posts per page based on custom post types
//function custom_posts_per_page($query)
//{
//    switch ($query->query_vars['post_type']) {
//        case 'post_type_name':
//            $query->query_vars['posts_per_page'] = 30;
//            break;
//        default:
//            break;
//    }
//    return $query;
//} //end custom_posts_per_page

//function setup_extra_thumbnail_sizes()
//{
//    add_image_size('image-size-name', width, height, (bool) crop);
//} //end setup_extra_thumbnail_sizes

function redirect_based_on_role()
{
    //get current user info
    global $current_user;
    get_currentuserinfo();
   
    if ($current_user->user_level == 0) {
     // User is subsriber    
     // Redirect to respective page
    } else if ($current_user->user_level > 1) {
      // User is contributor
      // Redirect to respective page
    } else if ($current_user->user_level >8) {
      // User is editor
      // Redirect to respective page
    } else {
      // No User role found
      // Get out of here
    }
} //end redirect_based_on_role
add_action("admin_init","redirect_based_on_role");