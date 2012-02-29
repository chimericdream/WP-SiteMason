<?php
// Print todays events
function wtf_cal_todays_events($cat_list = '')
{
    global $wpdb;

    // Find out if we should be displaying todays events
    $display = get_option('wtf_cal_display_todays');

    if ($display == 'true') {
        $output = '<ul>';
        $events = wtf_cal_grab_events(date("Y", wtf_cal_ctwo()), date("m", wtf_cal_ctwo()), date("d", wtf_cal_ctwo()), 'todays', $cat_list);
        usort($events, "wtf_cal_time_cmp");
        foreach ($events as $event) {
            if ($event->event_time == '00:00:00') {
                $time_string = ' ' . __('all day', 'wtf_calendar');
            } else {
                $time_string = ' ' . __('at', 'wtf_calendar') . ' ' . date(get_option('time_format'), strtotime(stripslashes($event->event_time)));
            }
            $output .= '<li>' . wtf_cal_draw_event($event) . $time_string . '</li>';
        }
        $output .= '</ul>';
        if (count($events) != 0) {
            return $output;
        }
    }
} //end wtf_cal_todays_events

