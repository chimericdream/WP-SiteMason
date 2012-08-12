<?php
// Add the function that deals with deleted users
add_action('delete_user', 'wpsm_cal_deal_with_deleted_user');

// Function to deal with events posted by a user when that user is deleted
function wpsm_cal_deal_with_deleted_user($id)
{
    global $wpdb;

    // Do the query
    $wpdb->get_results("UPDATE " . WPSM_CALENDAR_TABLE . " SET event_author=" . $wpdb->get_var("SELECT MIN(ID) FROM " . $wpdb->prefix . "users", 0, 0) . " WHERE event_author=" . mysql_escape_string($id));
} //end wpsm_cal_deal_with_deleted_user

// Function to provide time with WordPress offset, localy replaces time()
function wpsm_cal_ctwo()
{
    return (time() + (3600 * (get_option('gmt_offset'))));
} //end wpsm_cal_ctwo

// Setup comparison functions for building the calendar later
function wpsm_cal_month_comparison($month)
{
    $current_month = strtolower(date("M", wpsm_cal_ctwo()));
    if (isset($_GET['yr']) && isset($_GET['month'])) {
        if ($month == $_GET['month']) {
            return ' selected="selected"';
        }
    } elseif ($month == $current_month) {
        return ' selected="selected"';
    }
} //end wpsm_cal_month_comparison

function wpsm_cal_year_comparison($year)
{
    $current_year = strtolower(date("Y", wpsm_cal_ctwo()));
    if (isset($_GET['yr']) && isset($_GET['month'])) {
        if ($year == $_GET['yr']) {
            return ' selected="selected"';
        }
    } else if ($year == $current_year) {
        return ' selected="selected"';
    }
} //end wpsm_cal_year_comparison

// Function to compare time in event objects
function wpsm_cal_time_cmp($a, $b)
{
    if ($a->event_time == $b->event_time) {
        return 0;
    }
    return ($a->event_time < $b->event_time) ? -1 : 1;
} //end wpsm_cal_time_cmp

