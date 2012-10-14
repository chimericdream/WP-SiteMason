<?php
require_once dirname(__FILE__) . '/admin/analytics.php';
require_once dirname(__FILE__) . '/admin/main.php';
require_once dirname(__FILE__) . '/admin/rss.php';
require_once dirname(__FILE__) . '/admin/plugins.php';
require_once dirname(__FILE__) . '/admin/shortcodes.php';
require_once dirname(__FILE__) . '/admin/utilities.php';
require_once dirname(__FILE__) . '/admin/widgets.php';

function wpsm_admin() {
    $main_page = add_menu_page(
        WPSM_THEME_NAME,
        WPSM_THEME_NAME,
        'edit_themes',
        'theme-admin',
        'wpsm_main_page'
    );

    $main_page         = add_submenu_page('theme-admin', 'Theme Options', 'Theme Options', 'edit_themes', 'theme-admin', 'wpsm_main_page');
    $analytics_subpage = add_submenu_page('theme-admin', 'Google Analytics', 'Google Analytics', 'edit_themes', 'theme-admin-analytics', 'wpsm_analytics_page');
    $rss_subpage       = add_submenu_page('theme-admin', 'RSS Settings', 'RSS Settings', 'edit_themes', 'theme-admin-rss', 'wpsm_rss_page');
    $plugin_subpage    = add_submenu_page('theme-admin', 'Plugins', 'Plugins', 'edit_themes', 'theme-admin-plugins', 'wpsm_plugin_page');
    $shortcode_subpage = add_submenu_page('theme-admin', 'Shortcodes', 'Shortcodes', 'edit_themes', 'theme-admin-shortcodes', 'wpsm_shortcode_page');
    $utility_subpage   = add_submenu_page('theme-admin', 'Utilities', 'Utilities', 'edit_themes', 'theme-admin-utilities', 'wpsm_utility_page');
    $widget_subpage    = add_submenu_page('theme-admin', 'Widgets', 'Widgets', 'edit_themes', 'theme-admin-widgets', 'wpsm_widget_page');

    add_action('admin_head-' . $main_page,         'wpsm_header');
    add_action('admin_head-' . $plugin_subpage,    'wpsm_header');
    add_action('admin_head-' . $shortcode_subpage, 'wpsm_header');
    add_action('admin_head-' . $utility_subpage,   'wpsm_header');
    add_action('admin_head-' . $widget_subpage,    'wpsm_header');
    add_action('admin_head-' . $rss_subpage,       'wpsm_header');
    add_action('admin_head-' . $analytics_subpage, 'wpsm_header');

    $plugin_page = add_menu_page(
        WPSM_THEME_NAME . ' Plugins',
        WPSM_THEME_NAME . ' Plugins',
        'manage_options',
        'theme-plugins',
        'wpsm_plugin_main_page'
    );
    $plugin_page = add_submenu_page('theme-plugins', 'About the plugins', 'About the plugins', 'manage_options', 'theme-plugins', 'wpsm_plugin_main_page');
    add_action('admin_head-' . $plugin_page,    'wpsm_header');

    $shortcode_page = add_menu_page(
        WPSM_THEME_NAME . ' Shortcodes',
        WPSM_THEME_NAME . ' Shortcodes',
        'manage_options',
        'theme-shortcodes',
        'wpsm_shortcode_main_page'
    );
    $shortcode_page = add_submenu_page('theme-shortcodes', 'About the shortcodes', 'About the shortcodes', 'manage_options', 'theme-shortcodes', 'wpsm_shortcode_main_page');
    add_action('admin_head-' . $shortcode_page,    'wpsm_header');

    $utility_page = add_menu_page(
        WPSM_THEME_NAME . ' Utilities',
        WPSM_THEME_NAME . ' Utilities',
        'manage_options',
        'theme-utilities',
        'wpsm_utility_main_page'
    );
    $utility_page = add_submenu_page('theme-utilities', 'About the utilities', 'About the utilities', 'manage_options', 'theme-utilities', 'wpsm_utility_main_page');
    add_action('admin_head-' . $utility_page,    'wpsm_header');

    $widget_page = add_menu_page(
        WPSM_THEME_NAME . ' Widgets',
        WPSM_THEME_NAME . ' Widgets',
        'manage_options',
        'theme-widgets',
        'wpsm_widget_main_page'
    );
    $widget_page = add_submenu_page('theme-widgets', 'About the widgets', 'About the widgets', 'manage_options', 'theme-widgets', 'wpsm_widget_main_page');
    add_action('admin_head-' . $widget_page,    'wpsm_header');
} //end wpsm_admin

function wpsm_header() {
    ?>
    <link rel="stylesheet" href="<?php bloginfo('template_url'); ?>/wpsm/styles/style.css" type="text/css" media="screen" />
    <script type="text/javascript">
        jQuery(function($){
            $('#wpsm_cat_exclude').bind('click', function(){
                var id = $('#cat option:selected').val();
                var list = $('#exclude_rss_cats').val();
                if(list == ''){
                    list += '-' + id;
                } else {
                    list += ',-' + id;
                }
                $('#exclude_rss_cats').val(list);
            });
        });
    </script>
    <?php
} //end wpsm_header