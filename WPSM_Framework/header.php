<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <?php
    echo '    <meta http-equiv="Content-Type" content="' . get_bloginfo('html_type') . '; charset=' . get_bloginfo('charset') . '" />';
    if (is_search()) {
        echo '    <meta name="robots" content="noindex, nofollow" />';
    }
    echo '    <title>';
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
    if ($paged>1) {
        echo ' - page '. $paged;
    }
    echo "</title>\n";
    echo '    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />';
    echo '    <link rel="stylesheet" href="' . get_bloginfo('stylesheet_url') . '" type="text/css" media="screen" />';
    echo '    <link rel="stylesheet" href="' . get_bloginfo('template_directory') . '/css/print.css" type="text/css" media="print" />';
    echo '    <!--[If IE 6]>';
    echo '        <link rel="stylesheet" href="' . get_bloginfo('template_directory') . '/css/ie6.css" type="text/css" media="screen" />';
    echo '    <![endif]-->';
    echo '    <!--[If IE 7]>';
    echo '        <link rel="stylesheet" href="' . get_bloginfo('template_directory') . '/css/ie7.css" type="text/css" media="screen" />';
    echo '    <![endif]-->';
    echo '    <!--[If IE 8]>';
    echo '        <link rel="stylesheet" href="' . get_bloginfo('template_directory') . '/css/ie8.css" type="text/css" media="screen" />';
    echo '    <![endif]-->';
    echo '    <!--[If IE 9]>';
    echo '        <link rel="stylesheet" href="' . get_bloginfo('template_directory') . '/css/ie9.css" type="text/css" media="screen" />';
    echo '    <![endif]-->';
    echo '    <link rel="pingback" href="' . get_bloginfo('pingback_url') . '" />';

    if (is_singular()) {
        wp_enqueue_script('comment-reply');
    }
    wp_head();
    ?>
</head>
<body <?php body_class(); ?>>
    <header role="page-head">
        <h1><a href="<?php echo get_option('home'); ?>/"><?php bloginfo('name'); ?></a></h1>
        <div class="description"><?php bloginfo('description'); ?></div>
    </header>