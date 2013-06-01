<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta http-equiv="Content-Type" content="<?php echo get_bloginfo('html_type'); ?>; charset=<?php echo get_bloginfo('charset'); ?>" />
    <?php if (is_search()) : ?>
        <meta name="robots" content="noindex, nofollow" />
    <?php endif; ?>
    <title><?php
    if (function_exists('is_tag') && is_tag()) {
        single_tag_title("Tag Archive for &quot;");
        echo '&quot; - ';
    } elseif (is_archive()) {
        wp_title('');
        echo ' Archive - ';
    } elseif (is_search()) {
        echo 'Search for &quot;' . wp_specialchars($s) . '&quot; - ';
    } elseif (!(is_404()) && (is_single()) || (is_page() && !is_front_page())) {
        wp_title('');
        echo ' - ';
    } elseif (is_404()) {
        echo 'Not Found - ';
    }
    if (is_home()) {
        //Blog front page
        bloginfo('name');
        echo ' - ';
        bloginfo('description');
    } else {
        bloginfo('name');
    }
    if ($paged > 1) {
        echo ' - page '. $paged;
    }
    ?></title>
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
    <link rel="pingback" href="<?php echo get_bloginfo('pingback_url'); ?>" />

    <?php if (is_singular()) : ?>
        <?php wp_enqueue_script('comment-reply'); ?>
    <?php endif; ?>
    <?php wp_head(); ?>
</head>
    <body <?php body_class(); ?>>
        <div class="header">
            <h1><a href="<?php echo get_option('home'); ?>/"><?php bloginfo('name'); ?></a></h1>
            <div class="description"><?php bloginfo('description'); ?></div>
        </div>
        <div class="sidebar">
        <?php if (!function_exists('dynamic_sidebar') || !dynamic_sidebar('Sidebar Widgets')) : ?>
            <!-- All this stuff in here only shows up if you DON'T have any widgets active in this zone -->
            <?php get_search_form(); ?>
            <?php wp_list_pages('title_li=<h2>Pages</h2>'); ?>
            <h2>Archives</h2>';
            <ul>';
                <?php wp_get_archives('type=monthly'); ?>
            </ul>
            <h2>Categories</h2>';
            <ul>
                <?php wp_list_categories('show_count=1&title_li='); ?>
            </ul>
            <?php wp_list_bookmarks(); ?>
            <h2>Meta</h2>
            <ul>
                <?php wp_register(); ?>
                <li><?php echo wp_loginout('', false); ?></li>
                <li><a href="http://wordpress.org/" title="Powered by WordPress, state-of-the-art semantic personal publishing platform.">WordPress</a></li>
                <?php wp_meta(); ?>
            </ul>
            <h2>Subscribe</h2>
            <ul>
                <li><a href="<?php echo get_bloginfo('rss2_url'); ?>">Entries (RSS)</a></li>
                <li><a href="<?php echo get_bloginfo('comments_rss2_url'); ?>">Comments (RSS)</a></li>
            </ul>
        <?php endif; ?>
        </div>
        <div class="footer">
            &copy;<?php echo date("Y"); echo " "; bloginfo('name'); ?>
            <?php wp_footer(); ?>
        </div>
    </body>
</html>