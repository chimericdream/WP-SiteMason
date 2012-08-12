<?php
add_shortcode('calendar_full', 'wpsm_cal_calendar_shortcode');
add_shortcode('calendar_mini', 'wpsm_cal_minical_shortcode');
add_shortcode('calendar_today', 'wpsm_cal_todays_shortcode');
add_shortcode('calendar_upcoming', 'wpsm_cal_upcoming_shortcode');

// Function to deal with loading the calendar into pages
function wpsm_cal_calendar_shortcode()
{
    return wpsm_cal_calendar();
} //end wpsm_cal_calendar_shortcode

// Function to show a mini calendar in pages
function wpsm_cal_minical_shortcode()
{
    return wpsm_cal_minical();
} //end wpsm_cal_minical_shortcode

// Functions to allow the widgets to be inserted into posts and pages
function wpsm_cal_upcoming_shortcode()
{
    $content = '<div class="upcoming-events">';
    $content .= wpsm_cal_upcoming_events();
    $content .= '</div>';

    return $content;
} //end wpsm_cal_upcoming_shortcode

function wpsm_cal_todays_shortcode()
{
    $content = '<div class="todays-events">';
    $content .= wpsm_cal_todays_events();
    $content .= '</div>';

    return $content;
} //end wpsm_cal_todays_shortcode