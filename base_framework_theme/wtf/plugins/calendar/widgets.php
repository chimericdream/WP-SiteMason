<?php
// Add the widgets if we are using version 2.8
add_action('widgets_init', 'wpsm_cal_widget_init_calendar_today');
add_action('widgets_init', 'wpsm_cal_widget_init_calendar_upcoming');
add_action('widgets_init', 'wpsm_cal_widget_init_events_calendar');

// The widget to show the mini calendar
function wpsm_cal_widget_init_events_calendar()
{
    // Check for required functions
    if (!function_exists('wp_register_sidebar_widget'))
        return;

    function widget_events_calendar($args)
    {
        extract($args);
        $the_title = stripslashes(get_option('events_calendar_widget_title'));
        $the_cats = stripslashes(get_option('events_calendar_widget_cats'));
        $widget_title = empty($the_title) ? __('Events Calendar', 'wpsm_calendar') : $the_title;
        $the_events = wpsm_cal_minical($the_cats);
        if ($the_events != '') {
            echo $before_widget;
            echo $before_title . $widget_title . $after_title;
            echo '<br />' . $the_events;
            echo $after_widget;
        }
    }

    function widget_events_calendar_control()
    {
        $widget_title = stripslashes(get_option('events_calendar_widget_title'));
        $widget_cats = stripslashes(get_option('events_calendar_widget_cats'));
        if (isset($_POST['events_calendar_widget_title']) || isset($_POST['events_calendar_widget_cats'])) {
            update_option('events_calendar_widget_title', strip_tags($_POST['events_calendar_widget_title']));
            update_option('events_calendar_widget_cats', strip_tags($_POST['events_calendar_widget_cats']));
        }
        ?>
        <p>
            <label for="events_calendar_widget_title"><?php _e('Title', 'calendar'); ?>:<br />
                <input class="widefat" type="text" id="events_calendar_widget_title" name="events_calendar_widget_title" value="<?php echo $widget_title; ?>"/></label>
            <label for="events_calendar_widget_cats"><?php _e('Comma separated category id list', 'calendar'); ?>:<br />
                <input class="widefat" type="text" id="events_calendar_widget_cats" name="events_calendar_widget_cats" value="<?php echo $widget_cats; ?>"/></label>
        </p>
        <?php
    }

    wp_register_sidebar_widget('events_calendar', __('Events Calendar', 'wpsm_calendar'), 'widget_events_calendar', array('description' => 'A calendar of your events'));
    wp_register_widget_control('events_calendar', 'events_calendar', 'widget_events_calendar_control');
} //end wpsm_cal_widget_init_events_calendar

// The widget to show todays events in the sidebar
function wpsm_cal_widget_init_calendar_today()
{
    // Check for required functions
    if (!function_exists('wp_register_sidebar_widget'))
        return;

    function widget_calendar_today($args)
    {
        extract($args);
        $the_title = stripslashes(get_option('calendar_today_widget_title'));
        $the_cats = stripslashes(get_option('calendar_today_widget_cats'));
        $widget_title = empty($the_title) ? __('Today\'s Events', 'calendar') : $the_title;
        $the_events = wpsm_cal_todays_events($the_cats);
        if ($the_events != '') {
            echo $before_widget;
            echo $before_title . $widget_title . $after_title;
            echo $the_events;
            echo $after_widget;
        }
    }

    function widget_calendar_today_control()
    {
        $widget_title = stripslashes(get_option('calendar_today_widget_title'));
        $widget_cats = stripslashes(get_option('calendar_today_widget_cats'));
        if (isset($_POST['calendar_today_widget_title']) || isset($_POST['calendar_today_widget_cats'])) {
            update_option('calendar_today_widget_title', strip_tags($_POST['calendar_today_widget_title']));
            update_option('calendar_today_widget_cats', strip_tags($_POST['calendar_today_widget_cats']));
        }
        ?>
        <p>
            <label for="calendar_today_widget_title"><?php _e('Title', 'calendar'); ?>:<br />
                <input class="widefat" type="text" id="calendar_today_widget_title" name="calendar_today_widget_title" value="<?php echo $widget_title; ?>"/></label>
            <label for="calendar_today_widget_cats"><?php _e('Comma separated category id list', 'calendar'); ?>:<br />
                <input class="widefat" type="text" id="calendar_today_widget_cats" name="calendar_today_widget_cats" value="<?php echo $widget_cats; ?>"/></label>
        </p>
        <?php
    }

    wp_register_sidebar_widget('todays_events_calendar', __('Today\'s Events', 'calendar'), 'widget_calendar_today', array('description' => 'A list of your events today'));
    wp_register_widget_control('todays_events_calendar', 'todays_events_calendar', 'widget_calendar_today_control');
} //end wpsm_cal_widget_init_calendar_today

// The widget to show upcoming events in the sidebar
function wpsm_cal_widget_init_calendar_upcoming()
{
    // Check for required functions
    if (!function_exists('wp_register_sidebar_widget'))
        return;

    function widget_calendar_upcoming($args)
    {
        extract($args);
        $the_title = stripslashes(get_option('calendar_upcoming_widget_title'));
        $the_cats = stripslashes(get_option('calendar_upcoming_widget_cats'));
        $widget_title = empty($the_title) ? __('Upcoming Events', 'wpsm_calendar') : $the_title;
        $the_events = wpsm_cal_upcoming_events($the_cats);
        if ($the_events != '') {
            echo $before_widget;
            echo $before_title . $widget_title . $after_title;
            echo $the_events;
            echo $after_widget;
        }
    }

    function widget_calendar_upcoming_control()
    {
        $widget_title = stripslashes(get_option('calendar_upcoming_widget_title'));
        $widget_cats = stripslashes(get_option('calendar_upcoming_widget_cats'));
        if (isset($_POST['calendar_upcoming_widget_title']) || isset($_POST['calendar_upcoming_widget_cats'])) {
            update_option('calendar_upcoming_widget_title', strip_tags($_POST['calendar_upcoming_widget_title']));
            update_option('calendar_upcoming_widget_cats', strip_tags($_POST['calendar_upcoming_widget_cats']));
        }
        ?>
        <p>
            <label for="calendar_upcoming_widget_title"><?php _e('Title', 'calendar'); ?>:<br />
                <input class="widefat" type="text" id="calendar_upcoming_widget_title" name="calendar_upcoming_widget_title" value="<?php echo $widget_title; ?>"/></label>
            <label for="calendar_upcoming_widget_cats"><?php _e('Comma separated category id list', 'calendar'); ?>:<br />
                <input class="widefat" type="text" id="calendar_upcoming_widget_cats" name="calendar_upcoming_widget_cats" value="<?php echo $widget_cats; ?>"/></label>
        </p>
        <?php
    }

    wp_register_sidebar_widget('upcoming_events_calendar', __('Upcoming Events', 'wpsm_calendar'), 'widget_calendar_upcoming', array('description' => 'A list of your upcoming events'));
    wp_register_widget_control('upcoming_events_calendar', 'upcoming_events_calendar', 'widget_calendar_upcoming_control');
} //end wpsm_cal_widget_init_calendar_upcoming