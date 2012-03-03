<?php
/*
  Plugin Name: Collapsing Archives
  Plugin URI: http://blog.robfelty.com/plugins/collapsing-archives
  Description: Allows users to expand and collapse archive links like Blogger.  <a href='options-general.php?page=collapsArch.php'>Options and Settings</a> | <a href='http://wordpress.org/extend/plugins/collapsing-archives/other_notes'>Manual</a> | <a href='http://wordpress.org/extend/plugins/collapsing-archives/faq'>FAQ</a> | <a href='http://forum.robfelty.com/forum/collapsing-archives'>User forum</a>
  Author: Robert Felty
  Version: 1.3.2
  Author URI: http://robfelty.com

  Copyright 2007-2010 Robert Felty

  This file is part of Collapsing Archives

  Collapsing Archives is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation; either version 2 of the License, or
  (at your option) any later version.

  Collapsing Archives is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with Collapsing Archives; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */
$url = get_settings('siteurl');

require_once dirname(__FILE__) . '/collapse-archives/admin.php';
require_once dirname(__FILE__) . '/collapse-archives/widget.php';

// LOCALIZATION
function collapsArch_load_domain() {
    //load_plugin_textdomain('collapsArch', WP_PLUGIN_DIR . "/" . basename(dirname(__FILE__)), basename(dirname(__FILE__)));
}

add_action('init', 'collapsArch_load_domain');

if (!is_admin()) {
    //wp_enqueue_script('collapsFunctions', WP_PLUGIN_URL . "/collapsing-archives/collapsFunctions.js", array('jquery'), '1.7', false);
}
//register_activation_hook(__FILE__, array('collapsArch', 'init'));

class collapsArch {
    function init() {
//        if (!get_option('collapsArchSidebarId')) {
//            add_option('collapsArchSidebarId', 'sidebar');
//        }
    } //end init

    function phpArrayToJS($array, $name, $options)
    {
        /* generates javscript code to create an array from a php array */
        print "try { $name" . "['catTest'] = 'test'; } catch (err) { $name = new Object(); }\n";
        if (!$options['expandYears'] && $options['expandMonths']) {
            $lastYear = -1;
            foreach ($array as $key => $value) {
                $parts = explode('-', $key);
                $label = $parts[0];
                $year = $parts[1];
                $moreparts = explode(':', $key);
                $widget = $moreparts[1];
                if ($year != $lastYear) {
                    if ($lastYear > 0) {
                        print "';\n";
                    }
                    print $name . "['$label-$year:$widget'] = '" . addslashes(str_replace("\n", '', $value));
                    $lastYear = $year;
                } else {
                    print addslashes(str_replace("\n", '', $value));
                }
            }
            print "';\n";
        } else {
            foreach ($array as $key => $value) {
                print $name . "['$key'] = '" . addslashes(str_replace("\n", '', $value)) . "';\n";
            }
        }
    } //end phpArrayToJS
}

global $collapsArchItems;
$collapsArchItems = array();

function list_archives($options)
{
    $archives = '';
    global $wpdb;

    $filterCategories = array();
    if (!empty($options['includeOrExcludeCategories']) && !empty($options['categoriesToFilter'])) {
        $categories = preg_split('/[,]+/', $options['categoriesToFilter']);
        $catIn = '';
        if ($options['includeOrExcludeCategories'] == 'exclude') {
            $catIn = 'NOT';
        }
        if (count($categories)) {
            foreach ($categories as $category) {
                if (empty($filterCategories)) {
                    $filterCategories = "'" . sanitize_title($category) . "'";
                } else {
                    $filterCategories .= ", '" . sanitize_title($category) . "'";
                }
            }
        }
    }
    if (empty($filterCategories)) {
        $categoryFilterQuery = '';
    } else {
        $categoryFilterQuery = "AND {$wpdb->terms}.slug {$catIn} IN ({$filterCategories})";
    }

    $filterYears = array();
    if (!empty($options['includeOrExcludeYears']) && !empty($options['yearsToFilter'])) {
        $years = preg_split('/[,]+/', $options['yearsToFilter']);
        $yearIn = '';
        if ($options['includeOrExcludeYears'] == 'exclude') {
            $yearIn = 'NOT';
        }
        if (count($years)) {
            foreach ($years as $year) {
                if (empty($filterYears)) {
                    $filterYears = "'" . $year . "'";
                } else {
                    $filterYears .= ", '" . $year . "'";
                }
            }
        }
    }
    if (empty($filterYears)) {
        $yearFilterQuery = "";
    } else {
        $yearFilterQuery = "AND YEAR({$wpdb->posts}.post_date) {$yearIn} IN ({$filterYears})";
    }

    $postquery = "SELECT {$wpdb->terms}.slug, {$wpdb->posts}.ID, "
                    . "{$wpdb->posts}.post_name, $wpdb->posts.post_title, "
                    . "{$wpdb->posts}.post_author, {$wpdb->posts}.post_date, "
                    . "YEAR({$wpdb->posts}.post_date) AS 'year', "
                    . "MONTH({$wpdb->posts}.post_date) AS 'month' "
               . "FROM {$wpdb->posts} "
               . "LEFT JOIN {$wpdb->term_relationships} "
                    . "ON {$wpdb->posts}.ID = {$wpdb->term_relationships}.object_id "
               . "LEFT JOIN {$wpdb->term_taxonomy} "
                    . "ON {$wpdb->term_taxonomy}.term_taxonomy_id = {$wpdb->term_relationships}.term_taxonomy_id "
               . "LEFT JOIN {$wpdb->terms} "
                    . "ON {$wpdb->terms}.term_id = {$wpdb->term_taxonomy}.term_id "
               . "WHERE post_date != '0000-00-00 00:00:00' "
                    . "AND post_status = 'publish'";
    if (!$options['showPages']) {
        $postquery .= " AND {$wpdb->posts}.post_type = 'post'";
    }
    $postquery .= " {$yearFilterQuery} {$categoryFilterQuery}";
    $postquery .= " GROUP BY {$wpdb->posts}.ID "
               . "ORDER BY {$wpdb->posts}.post_date {$options['sort']}";

    $allPosts = $wpdb->get_results($postquery);

    if ($options['debug'] == 1) {
        echo "<pre style='display:none' >";
        printf("MySQL server version: %s\n", mysql_get_server_info());
        echo "\ncollapsArch options:\n";
        print_r($options);
        echo "POST QUERY:\n $postquery\n";
        echo "\nPOST QUERY RESULTS\n";
        print_r($allPosts);
        echo "</pre>";
    }

    if ($allPosts) {
        // Get the counts for the year display
        $currentYear  = -1;
        $currentMonth = -1;
        foreach ($allPosts as $post) {
            if ($post->year != $currentYear) {
                $currentYear = $post->year;
            }
            if ($post->month != $currentMonth) {
                $currentMonth = $post->month;
            }
            $yearCounts[$currentYear]++;
            $monthCounts[$currentYear . $currentMonth]++;
        }

        $currentYear    = -1;
        $currentMonth   = -1;
        $yearsDisplayed = 0;
        $archives       = "\n<ul class=\"collapse-archives-list\">";
        foreach ($allPosts as $post) {
            if ($post->month != $currentMonth) {
                $prevMonth    = $currentMonth;
                $currentMonth = $post->month;
            }
            if ($post->year != $currentYear) {
                $prevYear    = $currentYear;
                $currentYear = $post->year;
                $yearID      = "collapse-archives-{$currentYear}";

                if ($monthsDisplayed > 0) {
                    $archives .= "                </ul> <!-- End posts for {$prevMonth} -->";
                    $archives .= "\n            </li> <!-- End month {$prevMonth} -->";
                }

                if ($yearsDisplayed > 0) {
                    $archives .= "\n        </ul> <!-- End months for {$prevYear} -->\n";
                    $archives .= "    </li> <!-- End {$prevYear} -->";
                }
                $archives .= "\n    <li id=\"{$yearID}\">\n        <span class=\"year-title\">";
                if ($options['linkToYearlyArchive']) {
                    $archives .= '<a href="' . get_year_link($post->year) . '">';
                }

                $yearCount   = '';
                if ($options['showYearCount']) {
                    $yearCount = ' <span class="year-count">(' . $yearCounts[$currentYear] . ')</span>';
                }
                $archives .= "{$post->year}{$yearCount}";

                if ($options['linkToYearlyArchive']) {
                    $archives .= '</a>';
                }
                $archives .= "</span>\n";
                $archives .= '        <ul class="month-list">' . "\n";

                $yearsDisplayed++;
                $monthsDisplayed = 0;
            }

            if ($post->month != $currentMonth) {
                $monthID      = "collapse-archives-{$currentYear}-{$currentMonth}";

                if ($monthsDisplayed > 0) {
                    if ($options['showIndividualPosts']) {
                        $archives .= "                </ul> <!-- End posts for {$prevMonth} -->";
                    }
                    $archives .= "\n            </li> <!-- End month {$prevMonth} -->\n";
                }
                $archives .= "            <li id=\"{$monthID}\">\n                <span class=\"month-title\">";
                if ($options['linkToMonthlyArchive']) {
                    $archives .= '<a href="' . get_month_link($post->year, $post->month) . '">';
                }

                $monthCount   = '';
                if ($options['showMonthCount']) {
                    $monthCount = ' <span class="month-count">(' . $monthCounts[$currentYear . $currentMonth] . ')</span>';
                }
                $monthName = date('M', mktime(0, 0, 0, $post->month, 1, $post->year));
                $archives .= "{$monthName}{$monthCount}";

                if ($options['linkToMonthlyArchive']) {
                    $archives .= '</a>';
                }
                $archives .= "</span>\n";

                if ($options['showIndividualPosts']) {
                    $archives .= '                <ul class="post-list">' . "\n";
                }

                $monthsDisplayed++;
            }

            if ($options['showIndividualPosts']) {
                $postTitle = htmlspecialchars(strip_tags(__($post->post_title)), ENT_QUOTES);
                if ($options['postTitleMaxLength'] != -1 && strlen($postTitle) > $options['postTitleMaxLength']) {
                    $postTitle = substr($postTitle, 0, $options['postTitleMaxLength']);
                    $postTitle .= '&hellip;';
                }
                $archives .= "                    <li><a href=\"" . get_permalink($post->ID) . "\">{$postTitle}</a></li>\n";
            }
        }
        $archives .= "\n    </li> <!-- End Year {$prevYear} -->\n";
    }

    if ($archives != '') {
        $archives .= "\n</ul> <!-- End collapsible archive list -->";
    }
    return $archives;
} //end list_archives

function collapsArch($args = '')
{
    $defaults = array(
        'title'                      => 'Archives',
        'noTitle'                    => '',
        'includeOrExcludeCategories' => 'exclude',
        'categoriesToFilter'         => '',
        'includeOrExcludeYears'      => 'exclude',
        'yearsToFilter'              => '',
        'showPages'                  => false,
        'sort'                       => 'DESC',
        'linkToYearlyArchive'        => true,
        'linkToMonthlyArchive'       => true,
        'showYearCount'              => true,
        'expandCurrentYear'          => true,
        'expandMonths'               => true,
        'expandYears'                => true,
        'expandCurrentMonth'         => true,
        'showMonthCount'             => true,
        'showPostTitle'              => true,
        'expand'                     => '0',
        'showPostDate'               => false,
        'debug'                      => '0',
        'postDateFormat'             => 'm/d',
        'postDateAppend'             => 'after',
        'animate'                    => 0,
        'postTitleMaxLength'         => -1,
        'showIndividualPosts'        => true,
    );

    $options = wp_parse_args($args, $defaults);
    if (!is_admin()) {
        if (!$options['number'] || $options['number'] == '') {
            $options['number'] = 1;
        }
        return list_archives($options);
    }
} //end collapsArch