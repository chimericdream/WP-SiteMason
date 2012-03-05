<?php
/*
  Plugin Name: Calendar
  Plugin URI: http://www.kieranoshea.com
  Description: This plugin allows you to display a calendar of all your events and appointments as a page on your site.
  Author: Kieran O'Shea
  Author URI: http://www.kieranoshea.com
  Version: 1.3.1
 */

/*  Copyright 2008  Kieran O'Shea  (email : kieran@kieranoshea.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation; either version 2 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

require_once dirname(__FILE__) . '/calendar/utils.php';
require_once dirname(__FILE__) . '/calendar/admin.php';
require_once dirname(__FILE__) . '/calendar/minical.php';
require_once dirname(__FILE__) . '/calendar/today.php';
require_once dirname(__FILE__) . '/calendar/upcoming.php';
require_once dirname(__FILE__) . '/calendar/shortcodes.php';
require_once dirname(__FILE__) . '/calendar/widgets.php';

// Define the tables used in Calendar
global $wpdb;
define('WTF_CALENDAR_TABLE', $wpdb->prefix . 'wtf_calendar');
define('WTF_CALENDAR_CATEGORIES_TABLE', $wpdb->prefix . 'wtf_calendar_categories');

// Check ensure calendar is installed and install it if not - required for
// the successful operation of most functions called from this point on
wtf_cal_check_calendar();

// Function to check what version of Calendar is installed and install if needed
function wtf_cal_check_calendar()
{
    // Checks to make sure Calendar is installed, if not it adds the default
    // database tables and populates them with test data. If it is, then the
    // version is checked through various means and if it is not up to date
    // then it is upgraded.
    // Lets see if this is first run and create us a table if it is!
    global $wpdb;

    // Assume this is not a new install until we prove otherwise
    $new_install = false;

    $wp_calendar_exists = false;

    // Determine the calendar version
    $tables = $wpdb->get_results("show tables");
    foreach ($tables as $table) {
        foreach ($table as $value) {
            if ($value == WTF_CALENDAR_TABLE) {
                $wp_calendar_exists = true;
            }
        }
    }

    if (!$wp_calendar_exists) {
        $new_install = true;
    }

    // Now we've determined what the current install is or isn't
    // we perform operations according to the findings
    if ($new_install == true) {
        $sql = "CREATE TABLE " . WTF_CALENDAR_TABLE . " (
                event_id INT(11) NOT NULL AUTO_INCREMENT,
                event_begin DATE NOT NULL,
                event_end DATE NOT NULL,
                event_title VARCHAR(30) NOT NULL,
                event_desc TEXT NOT NULL,
                event_time TIME,
                event_recur CHAR(1),
                event_repeats INT(3),
                event_author BIGINT(20) UNSIGNED,
                event_category BIGINT(20) UNSIGNED NOT NULL DEFAULT 1,
                event_link TEXT,
                PRIMARY KEY (event_id)
        )";
        $wpdb->get_results($sql);

        $sql = "CREATE TABLE " . WTF_CALENDAR_CATEGORIES_TABLE . " (
            category_id INT(11) NOT NULL AUTO_INCREMENT,
            category_name VARCHAR(30) NOT NULL ,
            category_colour VARCHAR(30) NOT NULL ,
            PRIMARY KEY (category_id)
        )";
        $wpdb->get_results($sql);
        $sql = "INSERT INTO " . WTF_CALENDAR_CATEGORIES_TABLE . " SET category_id=1, category_name='General', category_colour='#F6F79B'";
        $wpdb->get_results($sql);
    }

    if(!get_option('wtf_cal_can_manage_events')) add_option('wtf_cal_can_manage_events', 'edit_posts');
    if(!get_option('wtf_cal_display_author')) add_option('wtf_cal_display_author', 'false');
    if(!get_option('wtf_cal_display_jump')) add_option('wtf_cal_display_jump', 'false');
    if(!get_option('wtf_cal_display_todays')) add_option('wtf_cal_display_todays', 'true');
    if(!get_option('wtf_cal_display_upcoming')) add_option('wtf_cal_display_upcoming', 'true');
    if(!get_option('wtf_cal_display_upcoming_days')) add_option('wtf_cal_display_upcoming_days', '7');
    if(!get_option('wtf_cal_enable_categories')) add_option('wtf_cal_enable_categories', 'false');
} //end wtf_cal_check_calendar

// Function to indicate the number of the day passed, eg. 1st or 2nd Sunday
function wtf_cal_np_of_day($date)
{
    $instance = 0;
    $dom = date('j', strtotime($date));
    if (($dom - 7) <= 0) {
        $instance = 1;
    } else if (($dom - 7) > 0 && ($dom - 7) <= 7) {
        $instance = 2;
    } else if (($dom - 7) > 7 && ($dom - 7) <= 14) {
        $instance = 3;
    } else if (($dom - 7) > 14 && ($dom - 7) <= 21) {
        $instance = 4;
    } else if (($dom - 7) > 21 && ($dom - 7) < 28) {
        $instance = 5;
    }
    return $instance;
} //end wtf_cal_np_of_day

// Function to provide date of the nth day passed (eg. 2nd Sunday)
function wtf_cal_dt_of_sun($date, $instance, $day)
{
    $plan = array();
    $plan['Mon'] = 1;
    $plan['Tue'] = 2;
    $plan['Wed'] = 3;
    $plan['Thu'] = 4;
    $plan['Fri'] = 5;
    $plan['Sat'] = 6;
    $plan['Sun'] = 7;
    $proper_date = date('Y-m-d', strtotime($date));
    $begin_month = substr($proper_date, 0, 8) . '01';
    $offset = $plan[date('D', strtotime($begin_month))];
    $result_day = 0;
    $recon = 0;
    if (($day - ($offset)) < 0) {
        $recon = 7;
    }
    if ($instance == 1) {
        $result_day = $day - ($offset - 1) + $recon;
    } else if ($instance == 2) {
        $result_day = $day - ($offset - 1) + $recon + 7;
    } else if ($instance == 3) {
        $result_day = $day - ($offset - 1) + $recon + 14;
    } else if ($instance == 4) {
        $result_day = $day - ($offset - 1) + $recon + 21;
    } else if ($instance == 5) {
        $result_day = $day - ($offset - 1) + $recon + 28;
    }
    return substr($proper_date, 0, 8) . $result_day;
} //end wtf_cal_dt_of_sun

// Function to return a prefix which will allow the correct
// placement of arguments into the query string.
function wtf_cal_permalink_prefix()
{
    // Get the permalink structure from WordPress
    if (is_home()) {
        $p_link = get_bloginfo('url');
        if ($p_link[strlen($p_link) - 1] != '/') {
            $p_link = $p_link . '/';
        }
    } else {
        $p_link = get_permalink();
    }

    // Based on the structure, append the appropriate ending
    if (!(strstr($p_link, '?'))) {
        $link_part = $p_link . '?';
    } else {
        $link_part = $p_link . '&';
    }

    return $link_part;
} //end wtf_cal_permalink_prefix

// Configure the "Next" link in the calendar
function wtf_cal_next_link($cur_year, $cur_month, $minical = false)
{
    $mod_rewrite_months = array(1 => 'jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sept', 'oct', 'nov', 'dec');
    $next_year = $cur_year + 1;

    if ($cur_month == 12) {
        if ($minical) {
            $rlink = '';
        } else {
            $rlink = __('Next', 'wtf_calendar');
        }
        return '<a href="' . wtf_cal_permalink_prefix() . 'month=jan&amp;yr=' . $next_year . '">' . $rlink . ' &raquo;</a>';
    } else {
        $next_month = $cur_month + 1;
        $month = $mod_rewrite_months[$next_month];
        if ($minical) {
            $rlink = '';
        } else {
            $rlink = __('Next', 'wtf_calendar');
        }
        return '<a href="' . wtf_cal_permalink_prefix() . 'month=' . $month . '&amp;yr=' . $cur_year . '">' . $rlink . ' &raquo;</a>';
    }
} //end wtf_cal_next_link

// Configure the "Previous" link in the calendar
function wtf_cal_prev_link($cur_year, $cur_month, $minical = false)
{
    $mod_rewrite_months = array(1 => 'jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sept', 'oct', 'nov', 'dec');
    $last_year = $cur_year - 1;

    if ($cur_month == 1) {
        if ($minical) {
            $llink = '';
        } else {
            $llink = __('Prev', 'wtf_calendar');
        }
        return '<a href="' . wtf_cal_permalink_prefix() . 'month=dec&amp;yr=' . $last_year . '">&laquo; ' . $llink . '</a>';
    } else {
        $next_month = $cur_month - 1;
        $month = $mod_rewrite_months[$next_month];
        if ($minical) {
            $llink = '';
        } else {
            $llink = __('Prev', 'wtf_calendar');
        }
        return '<a href="' . wtf_cal_permalink_prefix() . 'month=' . $month . '&amp;yr=' . $cur_year . '">&laquo; ' . $llink . '</a>';
    }
} //end wtf_cal_prev_link

// Used to draw multiple events
function wtf_cal_draw_events($events)
{
    // We need to sort arrays of objects by time
    usort($events, "wtf_cal_time_cmp");
    $output = '';
    // Now process the events
    foreach ($events as $event) {
        $output .= '* ' . wtf_cal_draw_event($event) . '<br />';
    }
    return $output;
} //end wtf_cal_draw_events

// Used to draw an event to the screen
function wtf_cal_draw_event($event)
{
    global $wpdb;

    // Before we do anything we want to know if we
    // should display the author and/or show categories.
    // We check for this later
    $display_author = get_option('wtf_cal_display_author');
    $show_cat = get_option('wtf_cal_enable_categories');
    $style = '';
    if ($show_cat == 'true') {
        $sql = "SELECT * FROM " . WTF_CALENDAR_CATEGORIES_TABLE . " WHERE category_id=" . mysql_escape_string($event->event_category);
        $cat_details = $wpdb->get_row($sql);
        $style = 'style="background-color:' . stripslashes($cat_details->category_colour) . ';"';
    }

    $header_details = '<span class="event-title" ' . $style . '>' . stripslashes($event->event_title) . '</span><br />
<span class="event-title-break"></span><br />';
    if ($event->event_time != "00:00:00") {
        $header_details .= '<strong>' . __('Time', 'wtf_calendar') . ':</strong> ' . date(get_option('time_format'), strtotime(stripslashes($event->event_time))) . '<br />';
    }
    if ($display_author == 'true') {
        $e = get_userdata(stripslashes($event->event_author));
        $header_details .= '<strong>' . __('Posted by', 'wtf_calendar') . ':</strong> ' . $e->display_name . '<br />';
    }
    if ($display_author == 'true' || $event->event_time != "00:00:00") {
        $header_details .= '<span class="event-content-break"></span><br />';
    }
    if ($event->event_link != '') {
        $linky = stripslashes($event->event_link);
    } else {
        $linky = '#';
    }

    $details = '<span class="calnk"><a href="' . $linky . '" ' . $style . '>' . stripslashes($event->event_title) . '<span ' . $style . '>' . $header_details . '' . stripslashes($event->event_desc) . '</span></a></span>';

    return $details;
} //end wtf_cal_draw_event

// Grab all events for the requested date from calendar
function wtf_cal_grab_events($y, $m, $d, $typing, $cat_list = '')
{
    global $wpdb;

    $arr_events = array();

    // Get the date format right
    $date = $y . '-' . $m . '-' . $d;
    $datetime = strtotime($date);

    // Format the category list
    if ($cat_list == '') {
        $cat_sql = '';
    } else {
        $cat_sql = 'AND event_category in (' . $cat_list . ')';
    }

    // The collated SQL code
    $sql = "
        SELECT a.*,'Normal' AS type
            FROM " . WTF_CALENDAR_TABLE . " AS a
            WHERE a.event_begin <= '$date'
                AND a.event_end >= '$date'
                AND a.event_recur = 'S' " . $cat_sql . "
        UNION ALL
            SELECT b.*,'Yearly' AS type
                FROM " . WTF_CALENDAR_TABLE . " AS b
            WHERE b.event_recur = 'Y'
                AND EXTRACT(YEAR FROM '$date') >= EXTRACT(YEAR FROM b.event_begin)
            AND b.event_repeats = 0 " . $cat_sql . "
        UNION ALL
            SELECT c.*,'Yearly' AS type
            FROM " . WTF_CALENDAR_TABLE . " AS c
            WHERE c.event_recur = 'Y'
                AND EXTRACT(YEAR FROM '$date') >= EXTRACT(YEAR FROM c.event_begin)
                AND c.event_repeats != 0
                AND (EXTRACT(YEAR FROM '$date')-EXTRACT(YEAR FROM c.event_begin)) <= c.event_repeats " . $cat_sql . "
        UNION ALL
            SELECT d.*,'Monthly' AS type
            FROM " . WTF_CALENDAR_TABLE . " AS d
            WHERE d.event_recur = 'M'
                AND EXTRACT(YEAR FROM '$date') >= EXTRACT(YEAR FROM d.event_begin)
                AND d.event_repeats = 0 " . $cat_sql . "
        UNION ALL
            SELECT e.*,'Monthly' AS type
            FROM " . WTF_CALENDAR_TABLE . " AS e
            WHERE e.event_recur = 'M'
                AND EXTRACT(YEAR FROM '$date') >= EXTRACT(YEAR FROM e.event_begin)
                AND e.event_repeats != 0
                AND (PERIOD_DIFF(EXTRACT(YEAR_MONTH FROM '$date'),EXTRACT(YEAR_MONTH FROM e.event_begin))) <= e.event_repeats " . $cat_sql . "
        UNION ALL
            SELECT f.*,'MonthSun' AS type
            FROM " . WTF_CALENDAR_TABLE . " AS f
            WHERE f.event_recur = 'U'
                AND EXTRACT(YEAR FROM '$date') >= EXTRACT(YEAR FROM f.event_begin)
                AND f.event_repeats = 0 " . $cat_sql . "
        UNION ALL
            SELECT g.*,'MonthSun' AS type
            FROM " . WTF_CALENDAR_TABLE . " AS g
            WHERE g.event_recur = 'U'
                AND EXTRACT(YEAR FROM '$date') >= EXTRACT(YEAR FROM g.event_begin)
                AND g.event_repeats != 0
                AND (PERIOD_DIFF(EXTRACT(YEAR_MONTH FROM '$date'),EXTRACT(YEAR_MONTH FROM g.event_begin))) <= g.event_repeats " . $cat_sql . "
        UNION ALL
            SELECT h.*,'Weekly' AS type
            FROM " . WTF_CALENDAR_TABLE . " AS h
            WHERE h.event_recur = 'W'
                AND '$date' >= h.event_begin
                AND h.event_repeats = 0 " . $cat_sql . "
        UNION ALL
            SELECT i.*,'Weekly' AS type
            FROM " . WTF_CALENDAR_TABLE . " AS i
            WHERE i.event_recur = 'W'
                AND '$date' >= i.event_begin
                AND i.event_repeats != 0
                AND (i.event_repeats*7) >= (TO_DAYS('$date') - TO_DAYS(i.event_end)) " . $cat_sql . "
        ORDER BY event_id";

    // Run the collated code
    $events = $wpdb->get_results($sql);
    if (!empty($events)) {
        foreach ($events as $event) {
            if ($event->type == 'Normal') {
                array_push($arr_events, $event);
            } else if ($event->type == 'Yearly') {
                // This is going to get complex so lets setup what we would place in for
                // an event so we can drop it in with ease
                // Technically we don't care about the years, but we need to find out if the
                // event spans the turn of a year so we can deal with it appropriately.
                $event_begin = strtotime($event->event_begin);
                $event_end = strtotime($event->event_end);
                $year_begin = date('Y', $event_begin);
                $year_end = date('Y', $event_end);

                if ($year_begin == $year_end) {
                    if (date('m-d', $event_begin) <= date('m-d', $datetime) &&
                            date('m-d', $event_end) >= date('m-d', $datetime)) {
                        array_push($arr_events, $event);
                    }
                } else if ($year_begin < $year_end) {
                    if (date('m-d', $event_begin) <= date('m-d', $datetime) ||
                            date('m-d', $event_end) >= date('m-d', $datetime)) {
                        array_push($arr_events, $event);
                    }
                }
            } else if ($event->type == 'Monthly') {
                // This is going to get complex so lets setup what we would place in for
                // an event so we can drop it in with ease
                // Technically we don't care about the years or months, but we need to find out if the
                // event spans the turn of a year or month so we can deal with it appropriately.
                $month_begin = date('m', $event_begin);
                $month_end = date('m', $event_end);

                if (($month_begin == $month_end) && ($event_begin <= $datetime)) {
                    if (date('d', $event_begin) <= date('d', $datetime) &&
                            date('d', $event_end) >= date('d', $datetime)) {
                        array_push($arr_events, $event);
                    }
                } else if (($month_begin < $month_end) && ($event_begin <= $datetime)) {
                    if (($event->event_begin <= date('Y-m-d', $datetime)) && (date('d', $event_begin) <= date('d', $datetime) ||
                            date('d', $event_end) >= date('d', $datetime))) {
                        array_push($arr_events, $event);
                    }
                }
            } else if ($event->type == 'MonthSun') {
                // This used to be complex but writing the dt_of_sun() function helped loads!
                // Technically we don't care about the years or months, but we need to find out if the
                // event spans the turn of a year or month so we can deal with it appropriately.
                $month_begin = date('m', $event_begin);
                $month_end = date('m', $event_end);

                // Setup some variables and get some values
                $dow = date('w', $event_begin);
                if ($dow == 0) {
                    $dow = 7;
                }
                $start_ent_this = wtf_cal_dt_of_sun($date, wtf_cal_np_of_day($event->event_begin), $dow);
                $start_ent_prev = wtf_cal_dt_of_sun(date('Y-m-d', strtotime($date . '-1 month')), wtf_cal_np_of_day($event->event_begin), $dow);
                $len_ent = $event_end - $event_begin;

                // The grunt work
                if (($month_begin == $month_end) && ($event_begin <= $datetime)) {
                    // The checks
                    if ($event_begin <= $datetime && $event_end >= $datetime) { // Handle the first occurance
                        array_push($arr_events, $event);
                    } else if (strtotime($start_ent_this) <= $datetime && $datetime <= strtotime($start_ent_this) + $len_ent) { // Now remaining items
                        array_push($arr_events, $event);
                    }
                } else if (($month_begin < $month_end) && ($event_begin <= $datetime)) {
                    // The checks
                    if ($event_begin <= $datetime && $event_end >= $datetime) { // Handle the first occurance
                        array_push($arr_events, $event);
                    } else if (strtotime($start_ent_prev) <= $datetime && $datetime <= strtotime($start_ent_prev) + $len_ent) { // Remaining items from prev month
                        array_push($arr_events, $event);
                    } else if (strtotime($start_ent_this) <= $datetime && $datetime <= strtotime($start_ent_this) + $len_ent) { // Remaining items starting this month
                        array_push($arr_events, $event);
                    }
                }
            } else if ($event->type == 'Weekly') {
                // This is going to get complex so lets setup what we would place in for
                // an event so we can drop it in with ease
                // Now we are going to check to see what day the original event
                // fell on and see if the current date is both after it and on
                // the correct day. If it is, display the event!
                $day_start_event = date('D', $event_begin);
                $day_end_event = date('D', $event_end);
                $current_day = date('D', $datetime);

                $plan = array();
                $plan['Mon'] = 1;
                $plan['Tue'] = 2;
                $plan['Wed'] = 3;
                $plan['Thu'] = 4;
                $plan['Fri'] = 5;
                $plan['Sat'] = 6;
                $plan['Sun'] = 7;

                if ($plan[$day_start_event] > $plan[$day_end_event]) {
                    if (($plan[$day_start_event] <= $plan[$current_day]) || ($plan[$current_day] <= $plan[$day_end_event])) {
                        array_push($arr_events, $event);
                    }
                } else if (($plan[$day_start_event] < $plan[$day_end_event]) || ($plan[$day_start_event] == $plan[$day_end_event])) {
                    if (($plan[$day_start_event] <= $plan[$current_day]) && ($plan[$current_day] <= $plan[$day_end_event])) {
                        array_push($arr_events, $event);
                    }
                }
            }
        }
    }

    return $arr_events;
} //end wtf_cal_grab_events

// Actually do the printing of the calendar
// Compared to searching for and displaying events
// this bit is really rather easy!
function wtf_cal_calendar($cat_list = '')
{
    $content = get_transient('wtf-cal');
    if (false !== $content) {
        return $content;
    }
    global $wpdb;

    // Deal with the week not starting on a monday
    if (get_option('start_of_week') == 0) {
        $name_days = array(
            1 => __('Sunday', 'wtf_calendar'),
            2 => __('Monday', 'wtf_calendar'),
            3 => __('Tuesday', 'wtf_calendar'),
            4 => __('Wednesday', 'wtf_calendar'),
            5 => __('Thursday', 'wtf_calendar'),
            6 => __('Friday', 'wtf_calendar'),
            7 => __('Saturday', 'wtf_calendar')
        );
    }
    // Choose Monday if anything other than Sunday is set
    else {
        $name_days = array(
            1 => __('Monday', 'wtf_calendar'),
            2 => __('Tuesday', 'wtf_calendar'),
            3 => __('Wednesday', 'wtf_calendar'),
            4 => __('Thursday', 'wtf_calendar'),
            5 => __('Friday', 'wtf_calendar'),
            6 => __('Saturday', 'wtf_calendar'),
            7 => __('Sunday', 'wtf_calendar')
        );
    }

    // Carry on with the script
    $name_months = array(
        1 => __('January', 'wtf_calendar'),
        2 => __('February', 'wtf_calendar'),
        3 => __('March', 'wtf_calendar'),
        4 => __('April', 'wtf_calendar'),
        5 => __('May', 'wtf_calendar'),
        6 => __('June', 'wtf_calendar'),
        7 => __('July', 'wtf_calendar'),
        8 => __('August', 'wtf_calendar'),
        9 => __('September', 'wtf_calendar'),
        10 => __('October', 'wtf_calendar'),
        11 => __('November', 'wtf_calendar'),
        12 => __('December', 'wtf_calendar')
    );

    // If we don't pass arguments we want a calendar that is relevant to today
    if (empty($_GET['month']) || empty($_GET['yr'])) {
        $c_year = date("Y", wtf_cal_ctwo());
        $c_month = date("m", wtf_cal_ctwo());
        $c_day = date("d", wtf_cal_ctwo());
    }

    // Years get funny if we exceed 3000, so we use this check
    if (isset($_GET['yr'])) {
        if ($_GET['yr'] <= 3000 && $_GET['yr'] >= 0 && (int) $_GET['yr'] != 0) {
            $getmonth = $_GET['month'];
            $getmontharr = array(
                'jan' => 1,
                'feb' => 2,
                'mar' => 3,
                'apr' => 4,
                'may' => 5,
                'jun' => 6,
                'jul' => 7,
                'aug' => 8,
                'sep' => 9,
                'oct' => 10,
                'nov' => 11,
                'dec' => 12,
            );

            if (array_key_exists($getmonth, $getmontharr)) {
                $c_month = $getmontharr[$getmonth];
                $c_year = mysql_escape_string($_GET['yr']);
                $c_day = date("d", wtf_cal_ctwo());
            } else {
                // No valid month causes the calendar to default to today
                $c_year = date("Y", wtf_cal_ctwo());
                $c_month = date("m", wtf_cal_ctwo());
                $c_day = date("d", wtf_cal_ctwo());
            }
        }
    } else {
        // No valid year causes the calendar to default to today
        $c_year = date("Y", wtf_cal_ctwo());
        $c_month = date("m", wtf_cal_ctwo());
        $c_day = date("d", wtf_cal_ctwo());
    }

    // Fix the days of the week if week start is not on a monday
    if (get_option('start_of_week') == 0) {
        $first_weekday = date("w", mktime(0, 0, 0, $c_month, 1, $c_year));
        $first_weekday = ($first_weekday == 0 ? 1 : $first_weekday + 1);
    } else {
        // Otherwise assume the week starts on a Monday. Anything other
        // than Sunday or Monday is just plain odd
        $first_weekday = date("w", mktime(0, 0, 0, $c_month, 1, $c_year));
        $first_weekday = ($first_weekday == 0 ? 7 : $first_weekday);
    }

    $days_in_month = date("t", mktime(0, 0, 0, $c_month, 1, $c_year));

    // Start the table and add the header and naviagtion
    $calendar_body = '';
    $calendar_body .= '<table cellspacing="1" cellpadding="0" class="calendar-table">';

    // We want to know if we should display the date switcher
    $date_switcher = get_option('wtf_cal_display_jump');

    if ($date_switcher == 'true') {
        $calendar_body .= '<tr>
        <td colspan="7" class="calendar-date-switcher">
            <form method="get" action="' . htmlspecialchars($_SERVER['REQUEST_URI']) . '">';
        $qsa = array();
        parse_str($_SERVER['QUERY_STRING'], $qsa);
        foreach ($qsa as $name => $argument) {
            if ($name != 'month' && $name != 'yr') {
                $calendar_body .= '<input type="hidden" name="' . strip_tags($name) . '" value="' . strip_tags($argument) . '" />';
            }
        }

        // We build the months in the switcher
        $calendar_body .= '
            ' . __('Month', 'wtf_calendar') . ': <select name="month">
            <option value="jan"' . wtf_cal_month_comparison('jan') . '>' . __('January', 'wtf_calendar') . '</option>
            <option value="feb"' . wtf_cal_month_comparison('feb') . '>' . __('February', 'wtf_calendar') . '</option>
            <option value="mar"' . wtf_cal_month_comparison('mar') . '>' . __('March', 'wtf_calendar') . '</option>
            <option value="apr"' . wtf_cal_month_comparison('apr') . '>' . __('April', 'wtf_calendar') . '</option>
            <option value="may"' . wtf_cal_month_comparison('may') . '>' . __('May', 'wtf_calendar') . '</option>
            <option value="jun"' . wtf_cal_month_comparison('jun') . '>' . __('June', 'wtf_calendar') . '</option>
            <option value="jul"' . wtf_cal_month_comparison('jul') . '>' . __('July', 'wtf_calendar') . '</option>
            <option value="aug"' . wtf_cal_month_comparison('aug') . '>' . __('August', 'wtf_calendar') . '</option>
            <option value="sept"' . wtf_cal_month_comparison('sept') . '>' . __('September', 'wtf_calendar') . '</option>
            <option value="oct"' . wtf_cal_month_comparison('oct') . '>' . __('October', 'wtf_calendar') . '</option>
            <option value="nov"' . wtf_cal_month_comparison('nov') . '>' . __('November', 'wtf_calendar') . '</option>
            <option value="dec"' . wtf_cal_month_comparison('dec') . '>' . __('December', 'wtf_calendar') . '</option>
            </select>
            ' . __('Year', 'wtf_calendar') . ': <select name="yr">';

        // The year builder is string mania. If you can make sense of this, you know your PHP!

        $past = 30;
        $future = 30;
        $fut = 1;
        $f = '';
        $p = '';
        while ($past > 0) {
            $p .= '            <option value="';
            $p .= date("Y", wtf_cal_ctwo()) - $past;
            $p .= '"' . wtf_cal_year_comparison(date("Y", wtf_cal_ctwo()) - $past) . '>';
            $p .= date("Y", wtf_cal_ctwo()) - $past . '</option>';
            $past = $past - 1;
        }
        while ($fut < $future) {
            $f .= '            <option value="';
            $f .= date("Y", wtf_cal_ctwo()) + $fut;
            $f .= '"' . wtf_cal_year_comparison(date("Y", wtf_cal_ctwo()) + $fut) . '>';
            $f .= date("Y", wtf_cal_ctwo()) + $fut . '</option>';
            $fut = $fut + 1;
        }
        $calendar_body .= $p;
        $calendar_body .= '            <option value="' . date("Y", wtf_cal_ctwo()) . '"' . wtf_cal_year_comparison(date("Y", wtf_cal_ctwo())) . '>' . date("Y", wtf_cal_ctwo()) . '</option>';
        $calendar_body .= $f;
        $calendar_body .= '</select>
            <input type="submit" value="' . __('Go', 'wtf_calendar') . '" />
            </form>
        </td>
</tr>';
    }

    // The header of the calendar table and the links. Note calls to link functions
    $calendar_body .= '<tr>
                <td colspan="7" class="calendar-heading">
                    <table border="0" cellpadding="0" cellspacing="0" width="100%">
                    <tr>
                    <td class="calendar-prev">' . wtf_cal_prev_link($c_year, $c_month) . '</td>
                    <td class="calendar-month">' . $name_months[(int) $c_month] . ' ' . $c_year . '</td>
                    <td class="calendar-next">' . wtf_cal_next_link($c_year, $c_month) . '</td>
                    </tr>
                    </table>
                </td>
</tr>';

    // Print the headings of the days of the week
    $calendar_body .= '<tr>';
    for ($i = 1; $i <= 7; $i++) {
        // Colours need to be different if the starting day of the week is different
        if (get_option('start_of_week') == 0) {
            $calendar_body .= '        <td class="' . ($i < 7 && $i > 1 ? 'normal-day-heading' : 'weekend-heading') . '">' . $name_days[$i] . '</td>';
        } else {
            $calendar_body .= '        <td class="' . ($i < 6 ? 'normal-day-heading' : 'weekend-heading') . '">' . $name_days[$i] . '</td>';
        }
    }
    $calendar_body .= '</tr>';
    $go = FALSE;
    for ($i = 1; $i <= $days_in_month;) {
        $calendar_body .= '<tr>';
        for ($ii = 1; $ii <= 7; $ii++) {
            if ($ii == $first_weekday && $i == 1) {
                $go = TRUE;
            } elseif ($i > $days_in_month) {
                $go = FALSE;
            }
            if ($go) {
                // Colours again, this time for the day numbers
                if (get_option('start_of_week') == 0) {
                    // This bit of code is for styles believe it or not.
                    $grabbed_events = wtf_cal_grab_events($c_year, $c_month, $i, 'calendar', $cat_list);
                    $no_events_class = '';
                    if (!count($grabbed_events)) {
                        $no_events_class = ' no-events';
                    }
                    $calendar_body .= '        <td class="' . (date("Ymd", mktime(0, 0, 0, $c_month, $i, $c_year)) == date("Ymd", wtf_cal_ctwo()) ? 'current-day' : 'day-with-date') . $no_events_class . '"><span ' . ($ii < 7 && $ii > 1 ? '' : 'class="weekend"') . '>' . $i++ . '</span><span class="event"><br />' . wtf_cal_draw_events($grabbed_events) . '</span></td>';
                } else {
                    $grabbed_events = wtf_cal_grab_events($c_year, $c_month, $i, 'calendar', $cat_list);
                    $no_events_class = '';
                    if (!count($grabbed_events)) {
                        $no_events_class = ' no-events';
                    }
                    $calendar_body .= '        <td class="' . (date("Ymd", mktime(0, 0, 0, $c_month, $i, $c_year)) == date("Ymd", wtf_cal_ctwo()) ? 'current-day' : 'day-with-date') . $no_events_class . '"><span ' . ($ii < 6 ? '' : 'class="weekend"') . '>' . $i++ . '</span><span class="event"><br />' . wtf_cal_draw_events($grabbed_events) . '</span></td>';
                }
            } else {
                $calendar_body .= '        <td class="day-without-date">&nbsp;</td>';
            }
        }
        $calendar_body .= '</tr>';
    }
    $calendar_body .= '</table>';

    $show_cat = get_option('wtf_cal_enable_categories');

    if ($show_cat == 'true') {
        $sql = "SELECT * FROM " . WTF_CALENDAR_CATEGORIES_TABLE . " ORDER BY category_name ASC";
        $cat_details = $wpdb->get_results($sql);
        $calendar_body .= '<table class="cat-key">
<tr><td colspan="2" class="cat-key-cell"><strong>' . __('Category Key', 'wtf_calendar') . '</strong></td></tr>';
        foreach ($cat_details as $cat_detail) {
            $calendar_body .= '<tr><td class="cat-key-cell"></td>
<td class="cat-key-cell">&nbsp;' . $cat_detail->category_name . '</td></tr>';
        }
        $calendar_body .= '</table>';
    }

    set_transient('wtf-cal', $calendar_body, 60*60*24);

    // Phew! After that bit of string building, spit it all out.
    // The actual printing is done by the calling function.
    return $calendar_body;
} //end wtf_cal_calendar