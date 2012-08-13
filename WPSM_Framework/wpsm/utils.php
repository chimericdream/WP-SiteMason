<?php
require_once dirname(__FILE__) . '/utils/smartoptimizer.php';

if (!current_user_can('manage_options')) {
    add_action('wp_dashboard_setup', 'wpsm_remove_dashboard_widgets');
}

function wpsm_remove_dashboard_widgets() {
    global $wp_meta_boxes;

    unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_plugins']);
    unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_drafts']);
    unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']);
    unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_secondary']);
} //end wpsm_remove_dashboard_widgets

if (function_exists('register_sidebar')) {
    register_sidebar(array(
        'name' => 'Sidebar Widgets',
        'id' => 'sidebar-widgets',
        'description' => 'These are widgets for the sidebar.',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h2>',
        'after_title' => '</h2>'
    ));
}

function wpsm_rss_url() {
    echo get_option('wpsm_rss_url');
} //end wpsm_rss_url

function wpsm_rss_email() {
    echo get_option('wpsm_rss_email');
} //end wpsm_rss_email

function wpsm_tracking_code() {
    echo get_option('wpsm_tracking_code');
} //end wpsm_tracking_code

function wpsm_navigation($page_options = 'title_li=', $cat_options = 'show_count=0&title_li=') {
    if (get_option('wpsm_top_nav') == 'cats') {
        wp_list_categories($cat_options);
    } else {
        wp_list_pages($page_options);
    }
} //end wpsm_navigation

function wpsm_custom_css() {
    if (get_option('wpsm_custom_css') != '') {
        echo '<link rel="stylesheet" href="' . get_bloginfo('template_url') . '/' . get_option('wpsm_custom_css') . '" type="text/css" media="screen" />';
    }
} //end wpsm_custom_css

function wpsm_exclude_rss_cats($query) {
    if ($query->is_feed && get_option('wpsm_exclude_rss_cats') != '') {
        $query->set('cat', get_option('wpsm_exclude_rss_cats'));
    }

    return $query;
} //end wpsm_exclude_rss_cats

add_filter('pre_get_posts', 'wpsm_exclude_rss_cats');

function wpsm_breadcrumbs() {
    global $wp_query;

    if (!is_home() && !is_front_page()) {
        // Start the UL
        echo '<ul class="breadcrumbs">';
        // Add the Home link
        echo '<li><a href="' . get_settings('home') . '">' . get_bloginfo('name') . '</a></li>';

        $post_type = get_post_type();

        if (is_category()) {
            $catTitle = single_cat_title('', false);
            $cat = get_cat_ID($catTitle);
            echo '<li>' . get_category_parents($cat, false, ' ') . '</li>';
        } elseif (is_post_type_archive()) {
            echo '<li>' . post_type_archive_title('', false) . '</li>';
        } elseif (is_archive() && !is_category()) {
            echo '<li>Archives</li>';
        } elseif (is_search()) {
            echo '<li>Search Results</li>';
        } elseif (is_404()) {
            echo '<li>404 Not Found</li>';
        } elseif (is_single() && !($post_type == false || $post_type == 'page' || $post_type == 'post')) {
            $label = get_post_type_object($post_type);
            echo '<li><a href="' . get_post_type_archive_link($post_type) . '">' . $label->labels->name . '</a></li>';
            echo '<li>' . the_title('', '', false) . '</li>';
        } elseif (is_single()) {
            $category = get_the_category();
            $category_id = get_cat_ID($category[0]->cat_name);
            echo '<li>' . get_category_parents($category_id, TRUE, ' ');
            echo the_title('', '', FALSE) . '</li>';
        } elseif (is_page()) {
            $post = $wp_query->get_queried_object();
            if ($post->post_parent == 0) {
                echo '<li>' . the_title('', '', FALSE) . '</li>';
            } else {
                $title = the_title('', '', FALSE);
                $ancestors = array_reverse(get_post_ancestors($post->ID));
                array_push($ancestors, $post->ID);
                foreach ($ancestors as $ancestor) {
                    if ($ancestor != end($ancestors)) {
                        echo '<li><a href="' . get_permalink($ancestor) . '">' . strip_tags(apply_filters('single_post_title', get_the_title($ancestor))) . '</a></li>';
                    } else {
                        echo '<li>' . strip_tags(apply_filters('single_post_title', get_the_title($ancestor))) . '</li>';
                    }
                }
            }
        }

        // End the UL
        echo '</ul>';
    }
} //end wpsm_breadcrumbs

function wpsm_popular_posts($showposts = 5) {
    global $wpdb;

    echo '<ul class="popular_posts">';
    $result = $wpdb->get_results("SELECT comment_count,ID,post_title FROM $wpdb->posts ORDER BY comment_count DESC LIMIT 0 , " . $showposts);
    foreach ($result as $post) {
        setup_postdata($post);
        $postid = $post->ID;
        $title = $post->post_title;
        $commentcount = $post->comment_count;
        if ($commentcount != 0) {
            echo '<li><a href="' . get_permalink($postid) . '" title="' . $title . '">' . $title . '</a></li>';
        }
    }
    echo '</ul>';
} //end wpsm_popular_posts

//for use in the loop and only works with tags
function wpsm_related_posts($showposts = 5) {
    global $post;
    $tags = wp_get_post_tags($post->ID);
    if ($tags) {
        $first_tag = $tags[0]->term_id;
        $args = array(
            'tag__in' => array($first_tag),
            'post__not_in' => array($post->ID),
            'showposts' => $showposts,
            'caller_get_posts' => 1
        );

        $my_query = new WP_Query($args);
        if ($my_query->have_posts()) {
            echo '<ul class="related_posts">';
            while ($my_query->have_posts()) {
                $my_query->the_post();
                echo '<li><a href="' . get_permalink() . '" title="Permanent Link to ' . the_title_attribute('echo=0') . '">' . get_the_title() . '</a></li>';
            }
            echo '</ul>';
        }
    }
} //end wpsm_related_posts

function wpsm_future_posts($showposts = 5, $date_format = 'jS F Y') {
    query_posts('showposts=' . $showposts . '&post_status=future');
    if (have_posts()) {
        echo '<ul class="future_posts">';
        while (have_posts()) {
            the_post();

            echo '<li>' . get_the_title() . ' <span class="future_date">' . get_the_time($date_format) . '</span></li>';
        }
        echo '</ul>';
    }
} //end wpsm_future_posts

function wpsm_pings_count($post_id) {
    global $wpdb;
    $count = "SELECT COUNT(*) FROM $wpdb->comments WHERE (comment_type = 'pingback' OR comment_type = 'trackback') AND comment_post_ID = '$post_id'";
    return $wpdb->get_var($count);
} //end wpsm_pings_count

function wpsm_tiny_url($url) {
    $dataUrl = 'http://tinyurl.com/api-create.php?url=' . $url;
    $tinyurl = wpsm_api_call($dataUrl);
    if ($tinyurl != false) {
        return $tinyurl;
    } else {
        return $url;
    }
} //end wpsm_tiny_url

function wpsm_feedburner_count($feedburner_id) {
    $url = "https://feedburner.google.com/api/awareness/1.0/GetFeedData?uri=" . $feedburner_id;
    $data = wpsm_api_call($url);
    if ($data != false) {
        try
        {
            $xml = new SimpleXMLElement($data);
            $count = $xml->feed->entry['circulation'];
        } catch (Exception $e)
        {
            return '0';
        }
        return $count;
    }
    return '0';
} //end wpsm_feedburner_count

function wpsm_get_attachment_extension($attachment) {
    if (!is_object($attachment) || $attachment->post_type != 'attachment') {
        return null;
    }

    if (!preg_match('/\./', $attachment->guid)) {
        $ext = '';
    } else {
        $ext = preg_replace('/^.*\./', '', $attachment->guid);
    }

    return $ext;
} //end wpsm_get_attachment_extension

//cURL helper method
function wpsm_api_call($url) {
    if (function_exists('curl_init')) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        $data = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);
        if ($error) {
            return $error;
        }
        return $data;
    } else {
        //cURL disabled on server
        return false;
    }
} //end wpsm_api_call