<?php
// Used to create a hover will all a day's events in for minical
function wtf_cal_minical_draw_events($events, $day_of_week = '')
{
    // We need to sort arrays of objects by time
    usort($events, "wtf_cal_time_cmp");
    // Only show anything if there are events
    $output = '';
    if (count($events)) {
        // Setup the wrapper
        $output = '<span class="calnk"><a href="#">' . $day_of_week . '<span>';
        // Now process the events
        foreach ($events as $event) {
            if ($event->event_time == '00:00:00') {
                $the_time = 'all day';
            } else {
                $the_time = 'at ' . date(get_option('time_format'), strtotime(stripslashes($event->event_time)));
            }
            $output .= '* <strong>' . $event->event_title . '</strong> ' . $the_time . '<br />';
        }
        // The tail
        $output .= '</span></a></span>';
    } else {
        $output .= $day_of_week;
    }
    return $output;
} //end wtf_cal_minical_draw_events

function wtf_cal_minical($cat_list = '')
{
    global $wpdb;

    // Deal with the week not starting on a monday
    if (get_option('start_of_week') == 0) {
        $name_days = array(1 => __('Su', 'wtf_calendar'), __('Mo', 'wtf_calendar'), __('Tu', 'wtf_calendar'), __('We', 'wtf_calendar'), __('Th', 'wtf_calendar'), __('Fr', 'wtf_calendar'), __('Sa', 'wtf_calendar'));
    }
    // Choose Monday if anything other than Sunday is set
    else {
        $name_days = array(1 => __('Mo', 'wtf_calendar'), __('Tu', 'wtf_calendar'), __('We', 'wtf_calendar'), __('Th', 'wtf_calendar'), __('Fr', 'wtf_calendar'), __('Sa', 'wtf_calendar'), __('Su', 'wtf_calendar'));
    }

    // Carry on with the script
    $name_months = array(1 => __('January', 'wtf_calendar'), __('February', 'wtf_calendar'), __('March', 'wtf_calendar'), __('April', 'wtf_calendar'), __('May', 'wtf_calendar'), __('June', 'wtf_calendar'), __('July', '\
calendar'), __('August', 'wtf_calendar'), __('September', 'wtf_calendar'), __('October', 'wtf_calendar'), __('November', 'wtf_calendar'), __('December', 'wtf_calendar'));

    // If we don't pass arguments we want a calendar that is relevant to today
    if (empty($_GET['month']) || empty($_GET['yr'])) {
        $c_year = date("Y", wtf_cal_ctwo());
        $c_month = date("m", wtf_cal_ctwo());
        $c_day = date("d", wtf_cal_ctwo());
    }

    // Years get funny if we exceed 3000, so we use this check
    if (isset($_GET['yr'])) {
        if ($_GET['yr'] <= 3000 && $_GET['yr'] >= 0 && (int) $_GET['yr'] != 0) {
            // This is just plain nasty and all because of permalinks
            // which are no longer used, this will be cleaned up soon
            if ($_GET['month'] == 'jan' || $_GET['month'] == 'feb' || $_GET['month'] == 'mar' || $_GET['month'] == 'apr' || $_GET['month'] == 'may' || $_GET['month'] == 'jun' || $_GET['month'] == 'jul' || $_GET['month'] == 'aug' || $_GET['month'] == 'sept' || $_GET['month'] == 'oct' || $_GET['month'] == 'nov' || $_GET['month'] == 'dec') {

                // Again nasty code to map permalinks into something
                // databases can understand. This will be cleaned up
                $c_year = mysql_escape_string($_GET['yr']);
                if ($_GET['month'] == 'jan') {
                    $t_month = 1;
                } else if ($_GET['month'] == 'feb') {
                    $t_month = 2;
                } else if ($_GET['month'] == 'mar') {
                    $t_month = 3;
                } else if ($_GET['month'] == 'apr') {
                    $t_month = 4;
                } else if ($_GET['month'] == 'may') {
                    $t_month = 5;
                } else if ($_GET['month'] == 'jun') {
                    $t_month = 6;
                } else if ($_GET['month'] == 'jul') {
                    $t_month = 7;
                } else if ($_GET['month'] == 'aug') {
                    $t_month = 8;
                } else if ($_GET['month'] == 'sept') {
                    $t_month = 9;
                } else if ($_GET['month'] == 'oct') {
                    $t_month = 10;
                } else if ($_GET['month'] == 'nov') {
                    $t_month = 11;
                } else if ($_GET['month'] == 'dec') {
                    $t_month = 12;
                }
                $c_month = $t_month;
                $c_day = date("d", wtf_cal_ctwo());
            }
            // No valid month causes the calendar to default to today
            else {
                $c_year = date("Y", wtf_cal_ctwo());
                $c_month = date("m", wtf_cal_ctwo());
                $c_day = date("d", wtf_cal_ctwo());
            }
        }
    }
    // No valid year causes the calendar to default to today
    else {
        $c_year = date("Y", wtf_cal_ctwo());
        $c_month = date("m", wtf_cal_ctwo());
        $c_day = date("d", wtf_cal_ctwo());
    }

    // Fix the days of the week if week start is not on a monday
    if (get_option('start_of_week') == 0) {
        $first_weekday = date("w", mktime(0, 0, 0, $c_month, 1, $c_year));
        $first_weekday = ($first_weekday == 0 ? 1 : $first_weekday + 1);
    }
    // Otherwise assume the week starts on a Monday. Anything other
    // than Sunday or Monday is just plain odd
    else {
        $first_weekday = date("w", mktime(0, 0, 0, $c_month, 1, $c_year));
        $first_weekday = ($first_weekday == 0 ? 7 : $first_weekday);
    }

    $days_in_month = date("t", mktime(0, 0, 0, $c_month, 1, $c_year));

    // Start the table and add the header and naviagtion
    $calendar_body = '';
    $calendar_body .= '<div class="minical"><table cellspacing="1" cellpadding="0" class="calendar-table">';


    // The header of the calendar table and the links. Note calls to link functions
    $calendar_body .= '<tr>
               <td colspan="7" class="calendar-heading">
                    <table border="0" cellpadding="0" cellspacing="0" width="100%">
                        <tr>
                            <td class="calendar-prev">' . wtf_cal_prev_link($c_year, $c_month, true) . '</td>
                            <td class="calendar-month">' . $name_months[(int) $c_month] . ' ' . $c_year . '</td>
                            <td class="calendar-next">' . wtf_cal_next_link($c_year, $c_month, true) . '</td>
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
                    $calendar_body .= '        <td class="' . (date("Ymd", mktime(0, 0, 0, $c_month, $i, $c_year)) == date("Ymd", wtf_cal_ctwo()) ? 'current-day' : 'day-with-date') . $no_events_class . '"><span ' . ($ii < 7 && $ii > 1 ? '' : 'class="weekend"') . '>' . wtf_cal_minical_draw_events($grabbed_events, $i++) . '</span></td>';
                } else {
                    $grabbed_events = wtf_cal_grab_events($c_year, $c_month, $i, 'calendar', $cat_list);
                    $no_events_class = '';
                    if (!count($grabbed_events)) {
                        $no_events_class = ' no-events';
                    }
                    $calendar_body .= '        <td class="' . (date("Ymd", mktime(0, 0, 0, $c_month, $i, $c_year)) == date("Ymd", wtf_cal_ctwo()) ? 'current-day' : 'day-with-date') . $no_events_class . '"><span ' . ($ii < 6 ? '' : 'class="weekend"') . '>' . wtf_cal_minical_draw_events($grabbed_events, $i++) . '</span></td>';
                }
            } else {
                $calendar_body .= '        <td class="day-without-date">&nbsp;</td>';
            }
        }
        $calendar_body .= '</tr>';
    }
    $calendar_body .= '</table>';

    // Closing div
    $calendar_body .= '</div>';
    // Phew! After that bit of string building, spit it all out.
    // The actual printing is done by the calling function.
    return $calendar_body;
} //end wtf_cal_minical