<?php
// Print todays events
function wpsm_cal_todays_events($cat_list = '')
{
    global $wpdb;

    // Find out if we should be displaying todays events
    $display = get_option('wpsm_cal_display_todays');

    if ($display == 'true') {
        $output = '<ul>';
        $events = wpsm_cal_grab_events(date("Y", wpsm_cal_ctwo()), date("m", wpsm_cal_ctwo()), date("d", wpsm_cal_ctwo()), 'todays', $cat_list);
        usort($events, "wpsm_cal_time_cmp");
        foreach ($events as $event) {
            if ($event->event_time == '00:00:00') {
                $time_string = ' ' . __('all day', 'wpsm_calendar');
            } else {
                $time_string = ' ' . __('at', 'wpsm_calendar') . ' ' . date(get_option('time_format'), strtotime(stripslashes($event->event_time)));
            }
            $output .= '<li>' . wpsm_cal_draw_event($event) . $time_string . '</li>';
        }
        $output .= '</ul>';
        if (count($events) != 0) {
            return $output;
        }
    }
} //end wpsm_cal_todays_events

