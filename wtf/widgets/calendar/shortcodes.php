<?php
add_shortcode('calendar_full', 'wtf_cal_calendar_shortcode');
add_shortcode('calendar_mini', 'wtf_cal_minical_shortcode');
add_shortcode('calendar_today', 'wtf_cal_todays_shortcode');
add_shortcode('calendar_upcoming', 'wtf_cal_upcoming_shortcode');

// Function to deal with loading the calendar into pages
function wtf_cal_calendar_shortcode()
{
    return wtf_cal_calendar();
} //end wtf_cal_calendar_shortcode

// Function to show a mini calendar in pages
function wtf_cal_minical_shortcode()
{
    return wtf_cal_minical();
} //end wtf_cal_minical_shortcode

// Functions to allow the widgets to be inserted into posts and pages
function wtf_cal_upcoming_shortcode()
{
    $content = '<div class="upcoming-events">';
    $content .= wtf_cal_upcoming_events();
    $content .= '</div>';

    return $content;
} //end wtf_cal_upcoming_shortcode

function wtf_cal_todays_shortcode()
{
    $content = '<div class="todays-events">';
    $content .= wtf_cal_todays_events();
    $content .= '</div>';

    return $content;
} //end wtf_cal_todays_shortcode