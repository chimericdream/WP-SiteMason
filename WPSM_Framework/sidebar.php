<aside role="sidebar">
<?php
if (!function_exists('dynamic_sidebar') || !dynamic_sidebar('Sidebar Widgets')) {
    // All this stuff in here only shows up if you DON'T have any widgets active in this zone
    get_search_form();
    wp_list_pages('title_li=<h2>Pages</h2>');
    echo '    <h2>Archives</h2>';
    echo '    <ul>';
    wp_get_archives('type=monthly');
    echo '    </ul>';
    echo '    <h2>Categories</h2>';
    echo '    <ul>';
    wp_list_categories('show_count=1&title_li=');
    echo '    </ul>';
    wp_list_bookmarks();
    echo '    <h2>Meta</h2>';
    echo '    <ul>';
    wp_register();
    echo '        <li>' . wp_loginout('', false) . '</li>';
    echo '        <li><a href="http://wordpress.org/" title="Powered by WordPress, state-of-the-art semantic personal publishing platform.">WordPress</a></li>';
    wp_meta();
    echo '    </ul>';
    echo '    <h2>Subscribe</h2>';
    echo '    <ul>';
    echo '        <li><a href="' . get_bloginfo('rss2_url') . '">Entries (RSS)</a></li>';
    echo '        <li><a href="' . get_bloginfo('comments_rss2_url') . '">Comments (RSS)</a></li>';
    echo '    </ul>';
}
?>
</aside>