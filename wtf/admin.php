<?php
require_once dirname(__FILE__) . '/admin/analytics.php';
require_once dirname(__FILE__) . '/admin/main.php';
require_once dirname(__FILE__) . '/admin/rss.php';
require_once dirname(__FILE__) . '/admin/plugins.php';

function wtf_admin() {
    $main_page = add_menu_page(
        WTF_THEME_NAME,
        WTF_THEME_NAME,
        'edit_themes',
        'theme-admin',
        'wtf_main_page'
    );

    $main_page      = add_submenu_page('theme-admin', 'Theme Options', 'Theme Options', 'edit_themes', 'theme-admin', 'wtf_main_page');
    $analytics_page = add_submenu_page('theme-admin', 'Google Analytics', 'Google Analytics', 'edit_themes', 'theme-admin-analytics', 'wtf_analytics_page');
    $rss_page       = add_submenu_page('theme-admin', 'RSS Settings', 'RSS Settings', 'edit_themes', 'theme-admin-rss', 'wtf_rss_page');
    $plugin_page    = add_submenu_page('theme-admin', 'Plugins', 'Plugins', 'edit_themes', 'theme-admin-plugins', 'wtf_plugin_page');
//    $analytics_page = add_submenu_page(
//        'theme-admin',
//        'Google Analytics',
//        'Google Analytics',
//        'edit_themes',
//        'theme-admin-analytics',
//        'wtf_analytics_page'
//    );
//
    add_action('admin_head-' . $main_page,      'wtf_header');
    add_action('admin_head-' . $plugin_page,    'wtf_header');
    add_action('admin_head-' . $rss_page,       'wtf_header');
    add_action('admin_head-' . $analytics_page, 'wtf_header');

    $widget_page = add_menu_page(
        WTF_THEME_NAME . ' Widgets',
        WTF_THEME_NAME . ' Widgets',
        'edit_themes',
        'theme-widgets',
        'wtf_widget_main_page'
    );
    add_action('admin_head-' . $widget_page,    'wtf_header');

    //set defaults
    if (!get_option('wtf_top_nav')) {
        add_option('wtf_top_nav', 'pages');
    }
    if (!get_option('wtf_rss_url')) {
        add_option('wtf_rss_url', '');
    }
    if (!get_option('wtf_rss_email')) {
        add_option('wtf_rss_email', '');
    }
    if (!get_option('wtf_tracking_code')) {
        add_option('wtf_tracking_code', '');
    }
    if (!get_option('wtf_custom_css')) {
        add_option('wtf_custom_css', '');
    }
    if (!get_option('wtf_exclude_rss_cats')) {
        add_option('wtf_exclude_rss_cats', '');
    }
}

//end wtf_admin

function wtf_header() {
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