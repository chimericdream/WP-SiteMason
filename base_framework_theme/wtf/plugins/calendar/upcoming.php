<?php
// Print upcoming events
function wpsm_cal_upcoming_events($cat_list = '')
{
    global $wpdb;

    // Find out if we should be displaying upcoming events
    $display = get_option('wpsm_cal_display_upcoming');

    if ($display == 'true') {
        // Get number of days we should go into the future
        $future_days = get_option('wpsm_cal_display_upcoming_days');
        $day_count = 1;

        $output = '';
        while ($day_count < $future_days + 1) {
            list($y, $m, $d) = explode("-", date("Y-m-d", mktime($day_count * 24, 0, 0, date("m", wpsm_cal_ctwo()), date("d", wpsm_cal_ctwo()), date("Y", wpsm_cal_ctwo()))));
            $events = wpsm_cal_grab_events($y, $m, $d, 'upcoming', $cat_list);
            usort($events, "wpsm_cal_time_cmp");
            if (count($events) != 0) {
                $output .= '<li>' . date_i18n(get_option('date_format'), mktime($day_count * 24, 0, 0, date("m", wpsm_cal_ctwo()), date("d", wpsm_cal_ctwo()), date("Y", wpsm_cal_ctwo()))) . '<ul>';
            }
            foreach ($events as $event) {
                if ($event->event_time == '00:00:00') {
                    $time_string = ' ' . __('all day', 'wpsm_calendar');
                } else {
                    $time_string = ' ' . __('at', 'wpsm_calendar') . ' ' . date(get_option('time_format'), strtotime(stripslashes($event->event_time)));
                }
                $output .= '<li>' . wpsm_cal_draw_event($event) . $time_string . '</li>';
            }
            if (count($events) != 0) {
                $output .= '</ul></li>';
            }
            $day_count = $day_count + 1;
        }

        if ($output != '') {
            $visual = '<ul>';
            $visual .= $output;
            $visual .= '</ul>';
            return $visual;
        }
    }
} //end wpsm_cal_upcoming_events

