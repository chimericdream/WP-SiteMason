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
global $collapsArchVersion;
$collapsArchVersion = '1.3';

// LOCALIZATION
function collapsArch_load_domain() {
    load_plugin_textdomain('collapsArch', WP_PLUGIN_DIR . "/" . basename(dirname(__FILE__)), basename(dirname(__FILE__)));
}

add_action('init', 'collapsArch_load_domain');

if (!is_admin()) {
    //wp_enqueue_script('collapsFunctions', WP_PLUGIN_URL . "/collapsing-archives/collapsFunctions.js", array('jquery'), '1.7', false);
    add_action('wp_head', array('collapsArch', 'get_head'));
} else {
    // call upgrade function if current version is lower than actual version
    $dbversion = get_option('collapsArchVersion');
    if (!$dbversion || $collapsArchVersion != $dbversion) {
        collapsArch::init();
    }
}
add_action('admin_menu', array('collapsArch', 'setup'));
register_activation_hook(__FILE__, array('collapsArch', 'init'));

class collapsArch {
    function init() {
        global $collapsArchVersion;
        $style = "#sidebar span.collapsing.archives {border:0; padding:0; margin:0; cursor:pointer;}\n";
        $style .= "#sidebar span.monthCount, span.yearCount {text-decoration:none; color:#333}\n";
        $style .= "#sidebar li.collapsing.archives a.self {font-weight:bold}\n";
        $style .= "#sidebar ul.collapsing.archives.list ul.collapsing.archives.list:before {content:'';}\n";
        $style .= "#sidebar ul.collapsing.archives.list li.collapsing.archives:before {content:'';}\n";
        $style .= "#sidebar ul.collapsing.archives.list li.collapsing.archives {list-style-type:none}\n";
        $style .= "#sidebar ul.collapsing.archives.list li {margin:0 0 0 .8em; text-indent:-1em;}\n";
        $style .= "#sidebar ul.collapsing.archives.list li.collapsing.archives.item:before {content: '\\\\00BB \\\\00A0' !important;}\n";
        $style .= "#sidebar ul.collapsing.archives.list li.collapsing.archives .sym {font-size:1.2em; font-family:Monaco, 'Andale Mono', 'FreeMono', 'Courier new', 'Courier', monospace; cursor:pointer; padding-right:5px;}\n";

        $default = $style;

        $block = "#sidebar li.collapsing.archives a {display:block; text-decoration:none; margin:0; padding:0;}\n";
        $block .= "#sidebar li.collapsing.archives a:hover {background:#CCC; text-decoration:none;}\n";
        $block .= "#sidebar span.collapsing.archives {border:0; padding:0; margin:0; cursor:pointer;}\n";
        $block .= "#sidebar li.collapsing.archives a.self {background:#CCC; font-weight:bold}\n";
        $block .= "#sidebar ul.collapsing.archives.list ul.collapsing.archives.list:before, #sidebar ul.collapsing.archives.list li.collapsing.archives:before, #sidebar ul.collapsing.archives.list li.collapsing.archives.item:before {content:'';}\n";
        $block .= "#sidebar ul.collapsing.archives.list li.collapsing.archives {list-style-type:none;}\n";
        $block .= "#sidebar ul.collapsing.archives.list li.collapsItem {}\n";
        $block .= "#sidebar ul.collapsing.archives.list li.collapsing.archives .sym {font-size:1.2em; font-family:Monaco, 'Andale Mono', 'FreeMono', 'Courier new', 'Courier', monospace; float:left; padding-right:5px; cursor:pointer;}\n";

        $noArrows = "#sidebar span.collapsing.archives {border:0; padding:0; margin:0; cursor:pointer;}\n";
        $noArrows .= "#sidebar span.monthCount, span.yearCount {text-decoration:none; color:#333}\n";
        $noArrows .= "#sidebar li.collapsing.archives a.self {font-weight:bold}\n";
        $noArrows .= "#sidebar ul.collapsing.archives.list li {margin:0 0 0 .8em; text-indent:-1em;}\n";
        $noArrows .= "#sidebar ul.collapsing.archives.list ul.collapsing.archives.list:before, #sidebar ul.collapsing.archives.list li.collapsing.archives:before, #sidebar ul.collapsing.archives.list li.collapsing.archives.item:before {content:'';}\n";
        $noArrows .= "#sidebar ul.collapsing.archives.list li.collapsing.archives {list-style-type:none}\n";
        $noArrows .= "#sidebar ul.collapsing.archives.list li.collapsing.archives .sym {font-size:1.2em; font-family:Monaco, 'Andale Mono', 'FreeMono', 'Courier new', 'Courier', monospace; cursor:pointer; padding-right:5px;}\n";
        $selected = 'default';
        $custom = get_option('collapsing.archivesStyle');

        $dbversion = get_option('collapsArchVersion');
        if ($collapsArchVersion != $dbversion && $selected != 'custom') {
            $style = $defaultStyles[$selected];
            update_option('collapsArchStyle', $style);
            update_option('collapsArchVersion', $collapsArchVersion);
        }
        $defaultStyles = compact('selected', 'default', 'block', 'noArrows', 'custom');
        if (function_exists('add_option')) {
            update_option('collapsArchOrigStyle', $style);
            update_option('collapsArchDefaultStyles', $defaultStyles);
        }
        if (!get_option('collapsArchStyle')) {
            add_option('collapsArchStyle', $style);
        }
        if (!get_option('collapsArchSidebarId')) {
            add_option('collapsArchSidebarId', 'sidebar');
        }
        if (!get_option('collapsArchVersion')) {
            add_option('collapsArchVersion', $collapsArchVersion);
        }
    }

    function setup()
    {
        if (function_exists('add_options_page') && current_user_can('manage_options')) {
            add_options_page(__('Collapsing Archives', 'collapsArch'), __('Collapsing Archives', 'collapsArch'), 1, basename(__FILE__), array('collapsArch', 'ui'));
        }
    }

    function ui()
    {
        check_admin_referer();

        $options = get_option('collapsArchOptions');
        $widgetOn = 0;
        $number = '%i%';
        if (empty($options)) {
            $number = '-1';
        } elseif (!isset($options['%i%']['title']) ||
                count($options) > 1) {
            $widgetOn = 1;
        }

        if (isset($_POST['resetOptions'])) {
            if (isset($_POST['reset'])) {
                delete_option('collapsArchOptions');
                $widgetOn = 0;
                $number = '-1';
            }
        } elseif (isset($_POST['infoUpdate'])) {
            $style = $_POST['collapsArchStyle'];
            $defaultStyles = get_option('collapsArchDefaultStyles');
            $selectedStyle = $_POST['collapsArchSelectedStyle'];
            $defaultStyles['selected'] = $selectedStyle;
            $defaultStyles['custom'] = $_POST['collapsArchStyle'];

            update_option('collapsArchStyle', $style);
            update_option('collapsArchSidebarId', $_POST['collapsArchSidebarId']);
            update_option('collapsArchDefaultStyles', $defaultStyles);

            if ($widgetOn == 0) {
                $title = strip_tags(stripslashes($new_instance['title']));

                $archSortOrder = ($new_instance['archSortOrder'] == 'ASC') ? 'ASC' : 'DESC';
                $showPosts = ($new_instance['showPosts'] == 'yes') ? true : false;
                $linkToArch = ($new_instance['linkToArch'] == 'yes') ? true : false;
                $showPostCount = (isset($new_instance['showPostCount'])) ? true : false;
                $showArchives = (isset($new_instance['showArchives'])) ? true : false;
                $showYearCount = (isset($new_instance['showYearCount'])) ? true : false;
                $expandCurrentYear = (isset($new_instance['expandCurrentYear'])) ? true : false;
                $expand = $new_instance['expand'];
                $customExpand = $new_instance['customExpand'];
                $customCollapse = $new_instance['customCollapse'];
                $noTitle = $new_instance['noTitle'];
                $includeOrExcludeYears = $new_instance['includeOrExcludeYears'];
                $includeOrExcludeCategories = $new_instance['includeOrExcludeCategories'];

                $expandYears = (isset($new_instance['expandYears'])) ? true : false;
                $showMonthCount = (isset($new_instance['showMonthCount'])) ? true : false;
                $expandMonths = (isset($new_instance['expandMonths'])) ? true : false;
                $showPostTitle = (isset($new_instance['showPostTitle'])) ? true : false;
                $animate = (!isset($new_instance['animate'])) ? 0 : 1;
                $debug = (isset($new_instance['debug'])) ? true : false;
                $showPostDate = (isset($new_instance['showPostDate'])) ? true : false;
                $postDateFormat = addslashes($new_instance['postDateFormat']);
                $postDateAppend = ($new_instance['postDateAppend'] == 'before') ? 'before' : 'after';
                $expandCurrentMonth = (isset($new_instance['expandCurrentMonth'])) ? true : false;
                $yearsToFilter = addslashes($new_instance['yearsToFilter']);
                $postTitleLength = addslashes($new_instance['postTitleLength']);
                $categoriesToFilter = addslashes($new_instance['categoriesToFilter']);
                $defaultExpand = addslashes($new_instance['defaultExpand']);
                $instance = compact(
                        'title',
                        'showPostCount',
                        'includeOrExcludeCategories',
                        'categoriesToFilter',
                        'includeOrExcludeYears',
                        'yearsToFilter',
                        'archSortOrder',
                        'showPosts',
                        'showPages',
                        'linkToArch',
                        'debug',
                        'showYearCount',
                        'expandCurrentYear',
                        'expandMonths',
                        'expandYears',
                        'expandCurrentMonth',
                        'showMonthCount',
                        'showPostTitle',
                        'expand',
                        'noTitle',
                        'customExpand',
                        'customCollapse',
                        'postDateAppend',
                        'showPostDate',
                        'postDateFormat',
                        'animate',
                        'postTitleLength'
                );
            }
        }
        if (-1 == $number) {
            $title = __('Archives', 'collapsArch');
            $text = '';
            $showPostCount = 'yes';
            $archSortOrder = 'DESC';
            $defaultExpand = '';
            $number = '%i%';
            $expand = '1';
            $customExpand = '';
            $customCollapse = '';
            $noTitle = '';
            $includeOrExcludeCategories = 'include';
            $includeOrExcludeYears = 'include';
            $categoriesToFilter = '';
            $yearsToFilter = '';
            $postTitleLength = '';
            $showPosts = 'yes';
            $linkToArch = 'yes';
            $showArchives = 'no';
            $expandCurrentYear = 'yes';
            $showYearCount = 'yes';
            $expandCurrentMonth = 'yes';
            $expandMonths = 'yes';
            $showMonthCount = 'yes';
            $showMonths = 'yes';
            $showPostTitle = 'yes';
            $showPostDate = 'no';
            $postDateFormat = 'm/d';
            $animate = 1;
            $debug = 0;
        } else {
            $title = attribute_escape($options[$number]['title']);
            $showPostCount = $options[$number]['showPostCount'];
            $expand = $options[$number]['expand'];
            $customExpand = $options[$number]['customExpand'];
            $customCollapse = $options[$number]['customCollapse'];
            $categoriesToFilter = $options[$number]['categoriesToFilter'];
            $yearsToFilter = $options[$number]['yearsToFilter'];
            $postTitleLength = $options[$number]['postTitleLength'];
            $includeOrExcludeCategories = $options[$number]['includeOrExcludeCategories'];
            $includeOrExcludeYears = $options[$number]['includeOrExcludeYears'];
            $archSortOrder = $options[$number]['archSortOrder'];
            $defaultExpand = $options[$number]['defaultExpand'];
            $showPosts = $options[$number]['showPosts'];
            $showArchives = $options[$number]['showArchives'];
            $linkToArch = $options[$number]['linkToArch'];
            $showYearCount = $options[$number]['showYearCount'];
            $expandCurrentYear = $options[$number]['expandCurrentYear'];
            $showMonthCount = $options[$number]['showMonthCount'];
            $showMonths = $options[$number]['showMonths'];
            $expandMonths = $options[$number]['expandMonths'];
            $expandCurrentMonth = $options[$number]['expandCurrentMonth'];
            $showPostTitle = $options[$number]['showPostTitle'];
            $showPostDate = $options[$number]['showPostDate'];
            $postDateFormat = $options[$number]['postDateFormat'];
            $animate = $options[$number]['animate'];
            $debug = $options[$number]['debug'];
            $noTitle = $options[$number]['noTitle'];
        }
        ?>
        <div class=wrap>
            <form method="post">
                <h2><? _e('Collapsing Archives Options', 'collapsArch'); ?></h2>
                <fieldset name="Collapsing Archives Options">
                    <p>
                        <?php _e('Id of the sidebar where collapsing pages appears:', 'collapsArch'); ?>
                        <input id='collapsArchSidebarId' name='collapsArchSidebarId' type='text' size='20' value="<?php echo get_option('collapsArchSidebarId'); ?>" onchange='changeStyle("collapsArchStylePreview","collapsArchStyle", "collapsArchDefaultStyles", "collapsArchSelectedStyle", false);' />
                        <table>
                            <tr>
                                <td>
                                    <input type='hidden' id='collapsArchCurrentStyle' value="<?php echo stripslashes(get_option('collapsArchStyle')); ?>" />
                                    <input type='hidden' id='collapsArchSelectedStyle' name='collapsArchSelectedStyle' />
                                    <label for="collapsArchStyle"><?php _e('Select style:', 'collapsArch'); ?></label>
                                </td>
                                <td>
                                    <select name='collapsArchDefaultStyles' id='collapsArchDefaultStyles' onchange='changeStyle("collapsArchStylePreview","collapsArchStyle", "collapsArchDefaultStyles", "collapsArchSelectedStyle", false);' />
                                    <?php
                                        $url = get_settings('siteurl') . '/wp-content/plugins/collapsing-archives';
                                        $styleOptions = get_option('collapsArchDefaultStyles');
                                        //print_r($styleOptions);
                                        $selected = $styleOptions['selected'];
                                        foreach ($styleOptions as $key => $value) {
                                            if ($key != 'selected') {
                                                if ($key == $selected) {
                                                    $select = ' selected=selected ';
                                                } else {
                                                    $select = ' ';
                                                }
                                                echo '<option' . $select . 'value="' . stripslashes($value) . '" >' . $key . '</option>';
                                            }
                                        }
                                    ?>
                                    </select>
                                </td>
                                <td><?php _e('Preview', 'collapsArch'); ?><br />
                                    <img style='border:1px solid' id='collapsArchStylePreview' alt='preview'/>
                                </td>
                            </tr>
                        </table>
                        <?php _e('You may also customize your style below if you wish', 'collapsArch'); ?><br />
                        <input type='button' value='<?php _e('restore current style', 'collapsArch'); ?>' onclick='restoreStyle();' /><br />
                        <textarea onchange='changeStyle("collapsArchStylePreview","collapsArchStyle", "collapsArchDefaultStyles", "collapsArchSelectedStyle", true);' cols='78' rows='10' id="collapsArchStyle"name="collapsArchStyle"><?php echo stripslashes(get_option('collapsArchStyle')) ?></textarea>
                    </p>
                    <script type='text/javascript'>
                        function changeStyle(preview,template,select,selected,custom) {
                            var preview = document.getElementById(preview);
                            var pageStyles = document.getElementById(select);
                            var selectedStyle;
                            var hiddenStyle=document.getElementById(selected);
                            var pageStyle = document.getElementById(template);
                            if (custom==true) {
                                selectedStyle=pageStyles.options[pageStyles.options.length-1];
                                selectedStyle.value=pageStyle.value;
                                selectedStyle.selected=true;
                            } else {
                                for(i=0; i<pageStyles.options.length; i++) {
                                    if (pageStyles.options[i].selected == true) {
                                        selectedStyle=pageStyles.options[i];
                                    }
                                }
                            }
                            hiddenStyle.value=selectedStyle.innerHTML
                            preview.src='<?php echo $url ?>/img/'+selectedStyle.innerHTML+'.png';
                            var sidebarId=document.getElementById('collapsArchSidebarId').value;
                            var theStyle = selectedStyle.value.replace(/#[a-zA-Z]+\s/g, '#'+sidebarId + ' ');
                            pageStyle.value=theStyle
                        }

                        function restoreStyle() {
                            var defaultStyle = document.getElementById('collapsArchCurrentStyle').value;
                            var pageStyle = document.getElementById('collapsArchStyle');
                            pageStyle.value=defaultStyle;
                        }
                        changeStyle('collapsArchStylePreview','collapsArchStyle', 'collapsArchDefaultStyles', 'collapsArchSelectedStyle', false);
                    </script>
                </fieldset>
                <div class="submit">
                    <input type="submit" name="infoUpdate" value="<?php _e('Update options', 'collapsArch'); ?> &raquo;" />
                </div>
            </form>
        </div>
        <?php
    }

    function get_head()
    {
        $style = stripslashes(get_option('collapsArchStyle'));
        echo "<style type='text/css'>{$style}</style>\n";
    }

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
    }

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
            if ($post->year != $currentYear) {
                $currentYear     = $post->year;
                $yearID          = "collapse-archives-{$currentYear}";

                if ($monthsDisplayed > 0) {
                    $archives .= "                </ul> <!-- End posts for {$allPosts[$i - 1]->month} -->";
                    $archives .= "\n            </li> <!-- End month {$allPosts[$i - 1]->month} -->";
                }

                if ($yearsDisplayed > 0) {
                    $archives .= "\n        </ul> <!-- End months for {$allPosts[$i - 1]->year} -->\n";
                    $archives .= "    </li> <!-- End {$allPosts[$i - 1]->year} -->";
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
                $currentMonth = $post->month;
                $monthID      = "collapse-archives-{$currentYear}-{$currentMonth}";

                if ($monthsDisplayed > 0) {
                    if ($options['showIndividualPosts']) {
                        $archives .= "                </ul> <!-- End posts for {$allPosts[$i - 1]->month} -->";
                    }
                    $archives .= "\n            </li> <!-- End month {$allPosts[$i - 1]->month} -->\n";
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
        $archives .= "\n    </li> <!-- End Year {$allPosts[$i - 1]->year} -->\n";
    }

    if ($archives != '') {
        $archives .= "\n</ul> <!-- End collapsible archive list -->";
    }
    return $archives;
}

function collapsArch($args = '')
{
    $defaults = array(
        'title'                      => __('Archives', 'collapsArch'),
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
        $archives = list_archives($options);
        echo $archives;
    }
}

class collapsArchWidget extends WP_Widget
{
    function collapsArchWidget()
    {
        $widget_ops = array(
            'classname' => 'widget_collapsarch',
            'description' => 'Collapsible archives listing'
        );
        $control_ops = array(
            'width' => '400',
            'height' => '400'
        );
        $this->WP_Widget('collapsarch', 'Collapsing Archives', $widget_ops, $control_ops);
    }

    function widget($args, $instance)
    {
        extract($args, EXTR_SKIP);

        $title = empty($instance['title']) ? '&nbsp;' : apply_filters('widget_title', $instance['title']);
        echo $before_widget . $before_title . $title . $after_title;
        $instance['number'] = $this->get_field_id('top');
        $instance['number'] = preg_replace('/[a-zA-Z-]/', '', $instance['number']);
        echo "<ul id='" . $this->get_field_id('top') . "' class='collapsing archives list'>\n";
        if (function_exists('collapsArch')) {
            collapsArch($instance);
        } else {
            wp_list_archives();
        }
        echo "</ul>\n";
        echo $after_widget;
    }

    function update($new_instance, $old_instance)
    {
        $instance = $old_instance;

        $title = strip_tags(stripslashes($new_instance['title']));

        $archSortOrder = ($new_instance['archSortOrder'] == 'ASC') ? 'ASC' : 'DESC';
        $showPosts = ($new_instance['showPosts'] == 'yes') ? true : false;
        $linkToArch = ($new_instance['linkToArch'] == 'yes') ? true : false;
        $showPostCount = (isset($new_instance['showPostCount'])) ? true : false;
        $showArchives = (isset($new_instance['showArchives'])) ? true : false;
        $showYearCount = (isset($new_instance['showYearCount'])) ? true : false;
        $expandCurrentYear = (isset($new_instance['expandCurrentYear'])) ? true : false;
        $expand = $new_instance['expand'];
        $customExpand = $new_instance['customExpand'];
        $customCollapse = $new_instance['customCollapse'];
        $noTitle = $new_instance['noTitle'];
        $includeOrExcludeYears = $new_instance['includeOrExcludeYears'];
        $includeOrExcludeCategories = $new_instance['includeOrExcludeCategories'];

        $expandYears = (isset($new_instance['expandYears'])) ? true : false;
        $showMonthCount = (isset($new_instance['showMonthCount'])) ? true : false;
        $expandMonths = (isset($new_instance['expandMonths'])) ? true : false;
        $showPostTitle = (isset($new_instance['showPostTitle'])) ? true : false;
        $animate = (!isset($new_instance['animate'])) ? 0 : 1;
        $debug = (isset($new_instance['debug'])) ? true : false;
        $showPostDate = (isset($new_instance['showPostDate'])) ? true : false;
        $postDateFormat = addslashes($new_instance['postDateFormat']);
        $postDateAppend = ($new_instance['postDateAppend'] == 'before') ? 'before' : 'after';
        $expandCurrentMonth = (isset($new_instance['expandCurrentMonth'])) ? true : false;
        $yearsToFilter = addslashes($new_instance['yearsToFilter']);
        $postTitleLength = addslashes($new_instance['postTitleLength']);
        $categoriesToFilter = addslashes($new_instance['categoriesToFilter']);
        $defaultExpand = addslashes($new_instance['defaultExpand']);
        $instance = compact(
                'title',
                'showPostCount',
                'includeOrExcludeCategories',
                'categoriesToFilter',
                'includeOrExcludeYears',
                'yearsToFilter',
                'archSortOrder',
                'showPosts',
                'showPages',
                'linkToArch',
                'debug',
                'showYearCount',
                'expandCurrentYear',
                'expandMonths',
                'expandYears',
                'expandCurrentMonth',
                'showMonthCount',
                'showPostTitle',
                'expand',
                'noTitle',
                'customExpand',
                'customCollapse',
                'postDateAppend',
                'showPostDate',
                'postDateFormat',
                'animate',
                'postTitleLength'
        );

        return $instance;
    }

    function form($instance)
    {
        $defaults = array(
            'title' => __('Archives', 'collapsArch'),
            'noTitle' => '',
            'includeOrExcludeCategories' => 'exclude',
            'categoriesToFilter' => '',
            'includeOrExcludeYears' => 'exclude',
            'yearsToFilter' => '',
            'showPages' => false,
            'sort' => 'DESC',
            'linkToArch' => true,
            'showYearCount' => true,
            'expandCurrentYear' => true,
            'expandMonths' => true,
            'expandYears' => true,
            'expandCurrentMonth' => true,
            'showMonthCount' => true,
            'showPostTitle' => true,
            'expand' => '0',
            'showPostDate' => false,
            'debug' => '0',
            'postDateFormat' => 'm/d',
            'postDateAppend' => 'after',
            'animate' => 0,
            'postTitleLength' => ''
        );

        $options = wp_parse_args($instance, $defaults);
        extract($options);
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>">Title:
                <input id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo attribute_escape($title); ?>" />
            </label>
        </p>
        <p>
            <input type="checkbox" name="<?php echo $this->get_field_name('showPostCount'); ?>" <?php if ($showPostCount == 'yes') echo 'checked'; ?> id="<?php echo $this->get_field_id('collapsArch'); ?>"></input>
            <label for="collapsArchShowPostCount"><?php _e('Show Post Count', 'collapsArch'); ?> </label>
            <input type="checkbox" name="<?php echo $this->get_field_name('showPages'); ?>" <?php if ($showPages == 'yes') echo 'checked'; ?> id="<?php echo $this->get_field_id('showPages'); ?>"></input>
            <label for="collapsArchShowPages"><?php _e('Show Pages as well as posts', 'collapsArch'); ?> </label>
        </p>
        <p><?php _e('Display archives in', 'collapsArch'); ?>
            <select name="<?php echo $this->get_field_name('sort'); ?>">
                <option <?php if ($sort == 'ASC') echo 'selected'; ?> id="<?php echo $this->get_field_id('sort'); ?>" value='ASC'><?php _e('Chronological order', 'collapsArch'); ?></option>
                <option <?php if ($sort == 'DESC') echo 'selected'; ?> id="<?php echo $this->get_field_id('sort'); ?>" value='DESC'><?php _e('Reverse Chronological order', 'collapsArch'); ?></option>
            </select>
        </p>
        <p><?php _e('Expanding and collapse characters:', 'collapsArch'); ?><br />
            <strong>html:</strong>
            <input type="radio" name="<?php echo $this->get_field_name('expand'); ?>" <?php if ($expand == 0) echo 'checked'; ?> id="expand0" value='0'></input>
            <label for="expand0">&#9658;&nbsp;&#9660;</label>
            <input type="radio" name="<?php echo $this->get_field_name('expand'); ?>" <?php if ($expand == 1) echo 'checked'; ?> id="expand1" value='1'></input>
            <label for="expand1">+&nbsp;&mdash;</label>
            <input type="radio" name="<?php echo $this->get_field_name('expand'); ?>" <?php if ($expand == 2) echo 'checked'; ?> id="expand2" value='2'></input>
            <label for="expand2">[+]&nbsp;[&mdash;]</label>&nbsp;&nbsp;
            <input type="radio" name="<?php echo $this->get_field_name('expand'); ?>" <?php if ($expand == 4) echo 'checked'; ?> id="expand4" value='4'></input>
            <label for="expand4">custom</label>
            <?php _e('expand:', 'collapsArch'); ?>
            <input type="text" size='1' name="<?php echo $this->get_field_name('customExpand'); ?>" value="<?php echo $customExpand ?>" id="<?php echo $this->get_field_id('customExpand'); ?>"></input>
            <?php _e('collapse:', 'collapsArch'); ?>
            <input type="text" size='1' name="<?php echo $this->get_field_name('customCollapse'); ?>" value="<?php echo $customCollapse ?>" id="<?php echo $this->get_field_id('customCollapse'); ?>"></input>
            <?php _e('<strong>images:</strong>', 'collapsArch'); ?>
            <input type="radio" name="<?php echo $this->get_field_name('expand'); ?>" <?php if ($expand == 3) echo 'checked'; ?> id="expand0" value='3'></input>
            <label for="expand3"><img src='<?php echo get_settings('siteurl') . "/wp-content/plugins/collapsArch/" ?>img/collapse.gif' />&nbsp;<img src='<?php echo get_settings('siteurl') . "/wp-content/plugins/collapsArch/" ?>img/expand.gif' /></label>
        </p>
        <p>
            <select name="<?php echo $this->get_field_name('inExcludePage'); ?>">
                <option <?php if ($inExcludePage == 'include') echo 'selected'; ?> id="<?php echo $this->get_field_id(''); ?>" value='include'><?php _e('Include', 'collapsArch'); ?></option>
                <option <?php if ($inExcludePage == 'exclude') echo 'selected'; ?> id="<?php echo $this->get_field_id(''); ?>" value='exclude'><?php _e('Exclude', 'collapsArch'); ?></option>
            </select>
            <?php _e('these years (separated by commas):', 'collapsArch'); ?><br />
            <input type="text" name="<?php echo $this->get_field_name('yearsToFilter'); ?>" value="<?php echo $yearsToFilter ?>" id="<?php echo $this->get_field_id('yearsToFilter'); ?>"></input>
        </p>
        <p>
            <select name="<?php echo $this->get_field_name('includeOrExcludeCategories'); ?>">
                <option <?php if ($includeOrExcludeCategories == 'include') echo 'selected'; ?> id="<?php echo $this->get_field_id('inExcludeCatInclude') ?>" value='include'>Include</option>
                <option <?php if ($includeOrExcludeCategories == 'exclude') echo 'selected'; ?> id="<?php echo $this->get_field_id('inExcludeCatExclude') ?>" value='exclude'>Exclude</option>
            </select>
            <?php _e('these categories (input slug or ID separated by commas):', 'collapsArch') ?><br />
            <input type="text" name="<?php echo $this->get_field_name('categoriesToFilter'); ?>" value="<?php echo $categoriesToFilter ?>" id="<?php echo $this->get_field_id('categoriesToFilter') ?>"</input>
        </p>
        <p><?php _e('Clicking on year/month', 'collapsArch'); ?>:<br />
            <input type="radio" name="<?php echo $this->get_field_name('linkToArch'); ?>" <?php if ($linkToArch) echo 'checked'; ?> id="<?php echo $this->get_field_id('collapsArch'); ?>" value='yes'></input>
            <label for="collapsArch-linkToArchYes"><?php _e('Links to archive', 'collapsArch'); ?></label>
            <input type="radio" name="<?php echo $this->get_field_name('linkToArch'); ?>" <?php if (!$linkToArch) echo 'checked'; ?> id="<?php echo $this->get_field_id('collapsArch'); ?>" value='no'></input>
            <label for="linkToArchNo"><?php _e('Expands list', 'collapsArch'); ?></label>
        </p>
        <p>
            <input type="checkbox" name="<?php echo $this->get_field_name('expandCurrentYear'); ?>" <?php if ($expandCurrentYear) echo 'checked'; ?> id="<?php echo $this->get_field_id('expandCurrentYear'); ?>"></input>
            <label for="expandCurrentYear"><?php _e('Leave Current Year Expanded by Default', 'collapsArch'); ?></label>
        </p>
        <p>
            <input type="checkbox" name="<?php echo $this->get_field_name('showYearCount'); ?>" <?php if ($showYearCount) echo 'checked'; ?> id="<?php echo $this->get_field_id(''); ?>"></input>
            <label for="showYearCount"><?php _e('Show Post Count in Year Links', 'collapsArch'); ?></label>
        </p>
        <p>
            <input type="checkbox" name="<?php echo $this->get_field_name('expandYears'); ?>" <?php if ($expandYears) echo 'checked'; ?> id="<?php echo $this->get_field_id('expandYears'); ?>"></input>
            <label for="expandYears"><?php _e('Show Month Link', 'collapsArch'); ?></label>
        </p>
        <p>
            &nbsp;&nbsp;<input type="checkbox" name="<?php echo $this->get_field_name('showMonthCount'); ?>" <?php if ($showMonthCount == 'yes') echo 'checked'; ?> id="<?php echo $this->get_field_id('showMonthCount'); ?>"></input>
            <label for="showMonthCount"><?php _e('Show Post Count in Month Links', 'collapsArch'); ?></label><br />
            &nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="<?php echo $this->get_field_name('expandMonths'); ?>" <?php if ($expandMonths) echo 'checked'; ?> id="<?php echo $this->get_field_id('expandMonths'); ?>"></input>
            <label for="expandMonths"><?php _e('Month Links should expand to show Posts', 'collapsArch'); ?></label><br />
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="<?php echo $this->get_field_name('expandCurrentMonth'); ?>" <? if ($expandCurrentMonth) echo 'checked'; ?> id="<?php echo $this->get_field_id('expandCurrentMonth'); ?>"></input>
            <label for="expandCurrentMonth"><?php _e('Leave Current Month Expanded by Default', 'collapsArch'); ?></label>
        </p>
        <p>
            <input type="checkbox" name="<?php echo $this->get_field_name('showPostTitle'); ?>" <?php if ($showPostTitle) echo 'checked'; ?> id="<?php echo $this->get_field_id('showPostTitle'); ?>"></input>
            <label for="showPostTitle"><?php _e('Show Post Title', 'collapsArch'); ?></label>
            | <?php _e('Truncate Post Title to', 'collapsArch'); ?>
            <input type="text" size='3' name="<?php echo $this->get_field_name('postTitleLength'); ?>" id="<?php echo $this->get_field_id('postTitleLength'); ?>" value="<?php echo $postTitleLength; ?>"></input>
            <label for="postTitleLength"> <?php _e('characters', 'collapsArch'); ?></label>
        </p>
        <p>
            <input type="checkbox" name="<?php echo $this->get_field_name('showPostDate'); ?>" <?php if ($showPostDate) echo 'checked'; ?> id="<?php echo $this->get_field_id('showPostDate'); ?>"></input>
            <label for="showPostDate"><?php _e('Show Post Date', 'collapsArch'); ?></label> |
            <select name="<?php echo $this->get_field_name('postDateAppend'); ?>">
                <option <?php if ($postDateAppend == 'before') echo 'selected'; ?> id="<?php echo $this->get_field_id('postDateAppendBefore') ?>" value='before'><?php _e('Before post title', 'collapsArch') ?></option>
                <option <?php if ($postDateAppend == 'after') echo 'selected'; ?> id="<?php echo $this->get_field_id('postDateAppendAfter') ?>" value='after'><?php _e('After post title', 'collapsArch') ?></option>
            </select>
            <label for="postDateFormat"><a href='http://php.net/date' title='information about date formatting syntax' target='_blank'><?php _e('as', 'collapsArch'); ?></a>:</label>
            <input type="text" size='8' name="<?php echo $this->get_field_name('postDateFormat'); ?>" value="<?php echo $postDateFormat; ?>" id="<?php echo $this->get_field_id('postDateFormat'); ?>"></input>
        </p>
        <p>
            <input type="checkbox" name="<?php echo $this->get_field_name('animate'); ?>" <?php if ($animate == 1) echo 'checked'; ?> id="<?php echo $this->get_field_id(''); ?>"></input>
            <label for="animate"><?php _e('Animate collapsing and expanding', 'collapsArch'); ?></label>
        </p>
        <p>
            <input type="checkbox" name="<?php echo $this->get_field_name('debug'); ?>" <?php if ($debug == '1') echo 'checked'; ?> id="<?php echo $this->get_field_id('collapsArch'); ?>"></input>
            <label for="collapsArchDebug"><?php _e('Show debugging information (shows up as a hidden pre right after the title)', 'collapsArch'); ?></label>
        </p>
        <p>Style can be set from the <a href='options-general.php?page=collapsArch.php'>options page</a></p>
        <?php
    }
}

function registerCollapsArchWidget()
{
    register_widget('collapsArchWidget');
}

add_action('widgets_init', 'registerCollapsArchWidget');