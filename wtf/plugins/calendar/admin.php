<?php
// Create a master category for Calendar and its sub-pages
add_action('admin_menu', 'wtf_cal_calendar_menu');

// Function to deal with adding the calendar menus
function wtf_cal_calendar_menu()
{
    global $wpdb;

    // Use the database to *potentially* override the above if allowed
    $allowed_group = (get_option('wtf_cal_can_manage_events')) ? 
        get_option('wtf_cal_can_manage_events') : 
        'manage_options';

    // Add the admin panel pages for Calendar. Use permissions pulled from above
    if (function_exists('add_menu_page')) {
        add_menu_page(__('Calendar', 'wtf_calendar'), __('Calendar', 'wtf_calendar'), $allowed_group, 'calendar', 'wtf_cal_edit_calendar');
    }
    if (function_exists('add_submenu_page')) {
        add_submenu_page('calendar', __('Manage Calendar', 'wtf_calendar'), __('Manage Calendar', 'wtf_calendar'), $allowed_group, 'calendar', 'wtf_cal_edit_calendar');
        add_action("admin_head", 'wtf_cal_calendar_add_javascript');
        // Note only admin can change calendar options
        add_submenu_page('calendar', __('Manage Categories', 'wtf_calendar'), __('Manage Categories', 'wtf_calendar'), 'manage_options', 'calendar-categories', 'wtf_cal_manage_categories');
        add_submenu_page('calendar', __('Calendar Config', 'wtf_calendar'), __('Calendar Options', 'wtf_calendar'), 'manage_options', 'calendar-config', 'wtf_cal_edit_calendar_config');
    }
} //end wtf_cal_calendar_menu

// Function to add the javascript to the admin header
function wtf_cal_calendar_add_javascript()
{
    echo '<script type="text/javascript" src="';
    bloginfo('template_url');
    echo '/wtf/widgets/calendar/javascript.js"></script><script type="text/javascript">document.write(getCalendarStyles());</script>';
} //end wtf_cal_calendar_add_javascript

// Display the admin configuration page
function wtf_cal_edit_calendar_config()
{
    global $wpdb;

    if (isset($_POST['permissions'])) {
        if ($_POST['permissions'] == 'subscriber') {
            $new_perms = 'read';
        } else if ($_POST['permissions'] == 'contributor') {
            $new_perms = 'edit_posts';
        } else if ($_POST['permissions'] == 'author') {
            $new_perms = 'publish_posts';
        } else if ($_POST['permissions'] == 'editor') {
            $new_perms = 'moderate_comments';
        } else if ($_POST['permissions'] == 'admin') {
            $new_perms = 'manage_options';
        } else {
            $new_perms = 'manage_options';
        }

        $display_upcoming_days = $_POST['display_upcoming_days'];

        if (mysql_escape_string($_POST['display_author']) == 'on') {
            $disp_author = 'true';
        } else {
            $disp_author = 'false';
        }

        if (mysql_escape_string($_POST['display_jump']) == 'on') {
            $disp_jump = 'true';
        } else {
            $disp_jump = 'false';
        }

        if (mysql_escape_string($_POST['display_todays']) == 'on') {
            $disp_todays = 'true';
        } else {
            $disp_todays = 'false';
        }

        if (mysql_escape_string($_POST['display_upcoming']) == 'on') {
            $disp_upcoming = 'true';
        } else {
            $disp_upcoming = 'false';
        }

        if (mysql_escape_string($_POST['enable_categories']) == 'on') {
            $enable_categories = 'true';
        } else {
            $enable_categories = 'false';
        }

        update_option('wtf_cal_can_manage_events', $new_perms);
        update_option('wtf_cal_display_author', $disp_author);
        update_option('wtf_cal_display_jump', $disp_jump);
        update_option('wtf_cal_display_todays', $disp_todays);
        update_option('wtf_cal_display_upcoming', $disp_upcoming);
        update_option('wtf_cal_display_upcoming_days', $display_upcoming_days);
        update_option('wtf_cal_enable_categories', $enable_categories);

        echo "<div class=\"updated\"><p><strong>" . __('Settings saved', 'wtf_calendar') . ".</strong></p></div>";
    }

    // Pull the values out of the database that we need for the form
    $allowed_group = get_option('wtf_cal_can_manage_events');
    $disp_author = get_option('wtf_cal_display_author');
    $disp_jump = get_option('wtf_cal_display_jump');
    $disp_todays = get_option('wtf_cal_display_todays');
    $disp_upcoming = get_option('wtf_cal_display_upcoming');
    $upcoming_days = get_option('wtf_cal_display_upcoming_days');
    $enable_categories = get_option('wtf_cal_enable_categories');

    $subscriber_selected = $contributor_selected = $author_selected = 
    $editor_selected     = $admin_selected       = '';
    if ($allowed_group == 'read') {
        $subscriber_selected = ' selected="selected"';
    } else if ($allowed_group == 'edit_posts') {
        $contributor_selected = ' selected="selected"';
    } else if ($allowed_group == 'publish_posts') {
        $author_selected = ' selected="selected"';
    } else if ($allowed_group == 'moderate_comments') {
        $editor_selected = ' selected="selected"';
    } else if ($allowed_group == 'manage_options') {
        $admin_selected = ' selected="selected"';
    }

    // Now we render the form
    ?>
    <style type="text/css">
        <!--
        .error {
            background: lightcoral;
            border: 1px solid #e64f69;
            margin: 1em 5% 10px;
            padding: 0 1em 0 1em;
        }

        .center {
            text-align: center;
        }
        .right {
            text-align: right;
        }
        .left {
            text-align: left;
        }
        .top {
            vertical-align: top;
        }
        .bold {
            font-weight: bold;
        }
        .private {
            color: #e64f69;
        }
        //-->
    </style>

    <div class="wrap">
        <h2><?php _e('Calendar Options', 'calendar'); ?></h2>
        <form name="quoteform" id="quoteform" class="wrap" method="post" action="<?php echo bloginfo('wpurl'); ?>/wp-admin/admin.php?page=calendar-config">
            <div id="linkadvanceddiv" class="postbox">
                <div class="inside">
                    <table cellpadding="5" cellspacing="5">
                        <tr>
                            <td><legend><?php _e('Choose the lowest user group that may manage events', 'calendar'); ?></legend></td>
                            <td>
                                <select name="permissions">
                                    <option value="subscriber"<?php echo $subscriber_selected ?>><?php _e('Subscriber', 'calendar') ?></option>
                                    <option value="contributor"<?php echo $contributor_selected ?>><?php _e('Contributor', 'calendar') ?></option>
                                    <option value="author"<?php echo $author_selected ?>><?php _e('Author', 'calendar') ?></option>
                                    <option value="editor"<?php echo $editor_selected ?>><?php _e('Editor', 'calendar') ?></option>
                                    <option value="admin"<?php echo $admin_selected ?>><?php _e('Administrator', 'calendar') ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td><legend><?php _e('Do you want to display the author name on events?', 'calendar'); ?></legend></td>
                            <td>
                                <select name="display_author">
                                    <option value="on"<?php echo ($disp_author == 'on') ? ' selected="selected"' : ''; ?>><?php _e('Yes', 'calendar') ?></option>
                                    <option value="off"<?php echo ($disp_author == 'off') ? ' selected="selected"' : ''; ?>><?php _e('No', 'calendar') ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td><legend><?php _e('Display a jumpbox for changing month and year quickly?', 'calendar'); ?></legend></td>
                            <td>
                                <select name="display_jump">
                                    <option value="on"<?php echo ($disp_jump == 'on') ? ' selected="selected"' : ''; ?>><?php _e('Yes', 'calendar') ?></option>
                                    <option value="off"<?php echo ($disp_jump == 'on') ? ' selected="selected"' : ''; ?>><?php _e('No', 'calendar') ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td><legend><?php _e('Display todays events?', 'calendar'); ?></legend></td>
                            <td>
                                <select name="display_todays">
                                    <option value="on"<?php echo ($disp_todays == 'on') ? ' selected="selected"' : ''; ?>><?php _e('Yes', 'calendar') ?></option>
                                    <option value="off"<?php echo ($disp_todays == 'off') ? ' selected="selected"' : ''; ?>><?php _e('No', 'calendar') ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td><legend><?php _e('Display upcoming events?', 'calendar'); ?></legend></td>
                            <td>
                                <select name="display_upcoming">
                                    <option value="on"<?php echo ($disp_upcoming == 'on') ? ' selected="selected"' : ''; ?>><?php _e('Yes', 'calendar') ?></option>
                                    <option value="off"<?php echo ($disp_upcoming == 'off') ? ' selected="selected"' : ''; ?>><?php _e('No', 'calendar') ?></option>
                                </select>
                                <?php _e('for', 'calendar'); ?>
                                <input type="text" name="display_upcoming_days" value="<?php echo $upcoming_days ?>" size="1" maxlength="2" />
                                <?php _e('days into the future', 'calendar'); ?>
                            </td>
                        </tr>
                        <tr>
                            <td><legend><?php _e('Enable event categories?', 'calendar'); ?></legend></td>
                        <td>    <select name="enable_categories">
                                <option value="on"<?php echo ($enable_categories == 'on') ? ' selected="selected"' : ''; ?>><?php _e('Yes', 'calendar') ?></option>
                                <option value="off"<?php echo ($enable_categories == 'off') ? ' selected="selected"' : ''; ?>><?php _e('No', 'calendar') ?></option>
                            </select>
                        </td>
                        </tr>
                    </table>
                </div>
                <div>&nbsp;</div>
            </div>
            <input type="submit" name="save" class="button bold" value="<?php _e('Save', 'calendar'); ?> &raquo;" />
        </form>
    </div>
    <?php
} //end wtf_cal_edit_calendar_config

// Used on the manage events admin page to display a list of events
function wtf_cal_events_display_list()
{
    global $wpdb;

    $events = $wpdb->get_results("SELECT * FROM " . WTF_CALENDAR_TABLE . " ORDER BY event_begin DESC");

    if (!empty($events)) {
        ?>
        <table class="widefat page fixed" cellpadding="3" cellspacing="3">
            <thead>
                <tr>
                    <th class="manage-column" scope="col"><?php _e('ID', 'calendar') ?></th>
                    <th class="manage-column" scope="col"><?php _e('Title', 'calendar') ?></th>
                    <th class="manage-column" scope="col"><?php _e('Start Date', 'calendar') ?></th>
                    <th class="manage-column" scope="col"><?php _e('End Date', 'calendar') ?></th>
                    <th class="manage-column" scope="col"><?php _e('Time', 'calendar') ?></th>
                    <th class="manage-column" scope="col"><?php _e('Recurs', 'calendar') ?></th>
                    <th class="manage-column" scope="col"><?php _e('Repeats', 'calendar') ?></th>
                    <th class="manage-column" scope="col"><?php _e('Author', 'calendar') ?></th>
                    <th class="manage-column" scope="col"><?php _e('Category', 'calendar') ?></th>
                    <th class="manage-column" scope="col"><?php _e('Edit', 'calendar') ?></th>
                    <th class="manage-column" scope="col"><?php _e('Delete', 'calendar') ?></th>
                </tr>
            </thead>
        <?php
        $class = '';
        foreach ($events as $event) {
            $class = ($class == 'alternate') ? '' : 'alternate';
            ?>
                <tr class="<?php echo $class; ?>">
                    <th scope="row"><?php echo stripslashes($event->event_id); ?></th>
                    <td><?php echo stripslashes($event->event_title); ?></td>
                    <td><?php echo stripslashes($event->event_begin); ?></td>
                    <td><?php echo stripslashes($event->event_end); ?></td>
                    <td><?php if ($event->event_time == '00:00:00') {
                echo __('N/A', 'wtf_calendar');
            } else {
                echo stripslashes($event->event_time);
            } ?></td>
                    <td>
            <?php
            // Interpret the DB values into something human readable
            if ($event->event_recur == 'S') {
                echo __('Never', 'wtf_calendar');
            } else if ($event->event_recur == 'W') {
                echo __('Weekly', 'wtf_calendar');
            } else if ($event->event_recur == 'M') {
                echo __('Monthly (date)', 'wtf_calendar');
            } else if ($event->event_recur == 'U') {
                echo __('Monthly (day)', 'wtf_calendar');
            } else if ($event->event_recur == 'Y') {
                echo __('Yearly', 'wtf_calendar');
            }
            ?>
                    </td>
                    <td>
            <?php
            // Interpret the DB values into something human readable
            if ($event->event_recur == 'S') {
                echo __('N/A', 'wtf_calendar');
            } else if ($event->event_repeats == 0) {
                echo __('Forever', 'wtf_calendar');
            } else if ($event->event_repeats > 0) {
                echo stripslashes($event->event_repeats) . ' ' . __('Times', 'wtf_calendar');
            }
            ?>
                    </td>
                    <td><?php $e = get_userdata($event->event_author);
            echo $e->display_name; ?></td>
                        <?php
                        $sql = "SELECT * FROM " . WTF_CALENDAR_CATEGORIES_TABLE . " WHERE category_id=" . mysql_escape_string($event->event_category);
                        $this_cat = $wpdb->get_row($sql);
                        ?>
                    <td><?php echo stripslashes($this_cat->category_name); ?></td>
                        <?php unset($this_cat); ?>
                    <td><a href="<?php echo bloginfo('wpurl') ?>/wp-admin/admin.php?page=calendar&amp;action=edit&amp;event_id=<?php echo stripslashes($event->event_id); ?>" class='edit'><?php echo __('Edit', 'wtf_calendar'); ?></a></td>
                    <td><a href="<?php echo bloginfo('wpurl') ?>/wp-admin/admin.php?page=calendar&amp;action=delete&amp;event_id=<?php echo stripslashes($event->event_id); ?>" class="delete" onclick="return confirm('<?php _e('Are you sure you want to delete this event?', 'calendar'); ?>')"><?php echo __('Delete', 'wtf_calendar'); ?></a></td>
                </tr>
            <?php
        }
        ?>
        </table>
                <?php
            } else {
                ?>
        <p><?php _e("There are no events in the database!", 'calendar') ?></p>
        <?php
    }
} //end wtf_cal_events_display_list

// The event edit form for the manage events admin page
function wtf_cal_events_edit_form($mode = 'add', $event_id = false)
{
    global $wpdb, $users_entries;
    $data = false;

    if ($event_id !== false) {
        if (intval($event_id) != $event_id) {
            echo "<div class=\"error\"><p>" . __('Bad Monkey! No banana!', 'wtf_calendar') . "</p></div>";
            return;
        } else {
            $data = $wpdb->get_results("SELECT * FROM " . WTF_CALENDAR_TABLE . " WHERE event_id='" . mysql_escape_string($event_id) . "' LIMIT 1");
            if (empty($data)) {
                echo "<div class=\"error\"><p>" . __("An event with that ID couldn't be found", 'calendar') . "</p></div>";
                return;
            }
            $data = $data[0];
        }
        // Recover users entries if they exist; in other words if editing an event went wrong
        if (!empty($users_entries)) {
            $data = $users_entries;
        }
    }
    // Deal with possibility that form was submitted but not saved due to error - recover user's entries here
    else {
        $data = $users_entries;
    }
    ?>
    <div id="pop_up_cal"></div>
    <form name="quoteform" id="quoteform" class="wrap" method="post" action="<?php echo bloginfo('wpurl'); ?>/wp-admin/admin.php?page=calendar">
        <input type="hidden" name="action" value="<?php echo $mode; ?>">
        <input type="hidden" name="event_id" value="<?php echo stripslashes($event_id); ?>">

        <div id="linkadvanceddiv" class="postbox">
            <div class="inside">
                <table cellpadding="5" cellspacing="5">
                    <tr>
                        <td><legend><?php _e('Event Title', 'calendar'); ?></legend></td>
                    <td><input type="text" name="event_title" class="input" size="40" maxlength="30"
                               value="<?php if (!empty($data)) echo htmlspecialchars(stripslashes($data->event_title)); ?>" /></td>
                    </tr>
                    <tr>
                        <td><legend><?php _e('Event Description', 'calendar'); ?></legend></td>
                    <td><textarea name="event_desc" class="input" rows="5" cols="50"><?php if (!empty($data)) echo htmlspecialchars(stripslashes($data->event_desc)); ?></textarea></td>
                    </tr>
                    <tr>
                        <td><legend><?php _e('Event Category', 'calendar'); ?></legend></td>
                    <td>	 <select name="event_category">
    <?php
    // Grab all the categories and list them
    $sql = "SELECT * FROM " . WTF_CALENDAR_CATEGORIES_TABLE;
    $cats = $wpdb->get_results($sql);
    foreach ($cats as $cat) {
        echo '<option value="' . stripslashes($cat->category_id) . '"';
        if (!empty($data)) {
            if ($data->event_category == $cat->category_id) {
                echo 'selected="selected"';
            }
        }
        echo '>' . stripslashes($cat->category_name) . '</option>';
    }
    ?>
                        </select>
                    </td>
                    </tr>
                    <tr>
                        <td><legend><?php _e('Event Link (Optional)', 'calendar'); ?></legend></td>
                    <td><input type="text" name="event_link" class="input" size="40" value="<?php if (!empty($data)) echo htmlspecialchars(stripslashes($data->event_link)); ?>" /></td>
                    </tr>
                    <tr>
                        <td><legend><?php _e('Start Date', 'calendar'); ?></legend></td>
                    <td>        <script type="text/javascript">
                        var cal_begin = new CalendarPopup('pop_up_cal');
                        cal_begin.setWeekStartDay(<?php echo get_option('start_of_week'); ?>);
                        function unifydates() {
                            document.forms['quoteform'].event_end.value = document.forms['quoteform'].event_begin.value;
                        }
                        </script>
                        <input type="text" name="event_begin" class="input" size="12"
                               value="<?php
                        if (!empty($data)) {
                            echo htmlspecialchars(stripslashes($data->event_begin));
                        } else {
                            echo date("Y-m-d", wtf_cal_ctwo());
                        }
    ?>" /> <a href="#" onClick="cal_begin.select(document.forms['quoteform'].event_begin,'event_begin_anchor','yyyy-MM-dd'); return false;" name="event_begin_anchor" id="event_begin_anchor"><?php _e('Select Date', 'calendar'); ?></a>
                    </td>
                    </tr>
                    <tr>
                        <td><legend><?php _e('End Date', 'calendar'); ?></legend></td>
                    <td>    <script type="text/javascript">
                        function check_and_print() {
                            unifydates();
                            var cal_end = new CalendarPopup('pop_up_cal');
                            cal_end.setWeekStartDay(<?php echo get_option('start_of_week'); ?>);
                            var newDate = new Date();
                            newDate.setFullYear(document.forms['quoteform'].event_begin.value.split('-')[0],document.forms['quoteform'].event_begin.value.split('-')[1]-1,document.forms['quoteform'].event_begin.value.split('-')[2]);
                            newDate.setDate(newDate.getDate()-1);
                            cal_end.addDisabledDates(null, formatDate(newDate, "yyyy-MM-dd"));
                            cal_end.select(document.forms['quoteform'].event_end,'event_end_anchor','yyyy-MM-dd');
                        }
                        </script>
                        <input type="text" name="event_end" class="input" size="12"
                               value="<?php
                        if (!empty($data)) {
                            echo htmlspecialchars(stripslashes($data->event_end));
                        } else {
                            echo date("Y-m-d", wtf_cal_ctwo());
                        }
    ?>" />  <a href="#" onClick="check_and_print(); return false;" name="event_end_anchor" id="event_end_anchor"><?php _e('Select Date', 'calendar'); ?></a>
                    </td>
                    </tr>
                    <tr>
                        <td><legend><?php _e('Time (hh:mm)', 'calendar'); ?></legend></td>
                    <td>	<input type="text" name="event_time" class="input" size=12
                                value="<?php
                           if (!empty($data)) {
                               if ($data->event_time == "00:00:00") {
                                   echo '';
                               } else {
                                   echo date("H:i", strtotime(htmlspecialchars(stripslashes($data->event_time))));
                               }
                           } else {
                               echo date("H:i", wtf_cal_ctwo());
                           }
                           ?>" /> <?php _e('Optional, set blank if not required.', 'calendar'); ?> <?php _e('Current time difference from GMT is ', 'calendar');
                           echo get_option('gmt_offset');
                           _e(' hour(s)', 'calendar'); ?>
                    </td>
                    </tr>
                    <tr>
                        <td><legend><?php _e('Recurring Events', 'calendar'); ?></legend></td>
                    <td>	<?php
                           if (isset($data)) {
                               if ($data->event_repeats != NULL) {
                                   $repeats = $data->event_repeats;
                               } else {
                                   $repeats = 0;
                               }
                           } else {
                               $repeats = 0;
                           }

                           $selected_s = '';
                           $selected_w = '';
                           $selected_m = '';
                           $selected_y = '';
                           $selected_u = '';
                           if (isset($data)) {
                               if ($data->event_recur == "S") {
                                   $selected_s = 'selected="selected"';
                               } else if ($data->event_recur == "W") {
                                   $selected_w = 'selected="selected"';
                               } else if ($data->event_recur == "M") {
                                   $selected_m = 'selected="selected"';
                               } else if ($data->event_recur == "Y") {
                                   $selected_y = 'selected="selected"';
                               } else if ($data->event_recur == "U") {
                                   $selected_u = 'selected="selected"';
                               }
                           }
    ?>
                                <?php _e('Repeats for', 'calendar'); ?>
                        <input type="text" name="event_repeats" class="input" size="1" value="<?php echo $repeats; ?>" />
                        <select name="event_recur" class="input">
                            <option class="input" <?php echo $selected_s; ?> value="S"><?php _e('None') ?></option>
                            <option class="input" <?php echo $selected_w; ?> value="W"><?php _e('Weeks') ?></option>
                            <option class="input" <?php echo $selected_m; ?> value="M"><?php _e('Months (date)') ?></option>
                            <option class="input" <?php echo $selected_u; ?> value="U"><?php _e('Months (day)') ?></option>
                            <option class="input" <?php echo $selected_y; ?> value="Y"><?php _e('Years') ?></option>
                        </select><br />
                        <?php _e('Entering 0 means forever. Where the recurrance interval is left at none, the event will not reoccur.', 'calendar'); ?>
                    </td>
                    </tr>
                </table>
            </div>
            <div>&nbsp;</div>
        </div>
        <input type="submit" name="save" class="button bold" value="<?php _e('Save', 'calendar'); ?> &raquo;" />
    </form>
    <?php
} //end wtf_cal_events_edit_form

// The actual function called to render the manage events page and
// to deal with posts
function wtf_cal_edit_calendar()
{
    global $current_user, $wpdb, $users_entries;
    ?>
    <style type="text/css">
        <!--
        .error {
            background: lightcoral;
            border: 1px solid #e64f69;
            margin: 1em 5% 10px;
            padding: 0 1em 0 1em;
        }

        .center {
            text-align: center;
        }
        .right { text-align: right;
        }
        .left {
            text-align: left;
        }
        .top {
            vertical-align: top;
        }
        .bold {
            font-weight: bold;
        }
        .private {
            color: #e64f69;
        }
        //-->
    </style>

    <?php
// First some quick cleaning up
    $edit = $create = $save = $delete = false;

// Make sure we are collecting the variables we need to select years and months
    $action = !empty($_REQUEST['action']) ? $_REQUEST['action'] : '';
    $event_id = !empty($_REQUEST['event_id']) ? $_REQUEST['event_id'] : '';

// Deal with adding an event to the database
    if ($action == 'add') {
        $title = !empty($_REQUEST['event_title']) ? $_REQUEST['event_title'] : '';
        $desc = !empty($_REQUEST['event_desc']) ? $_REQUEST['event_desc'] : '';
        $begin = !empty($_REQUEST['event_begin']) ? $_REQUEST['event_begin'] : '';
        $end = !empty($_REQUEST['event_end']) ? $_REQUEST['event_end'] : '';
        $time = !empty($_REQUEST['event_time']) ? $_REQUEST['event_time'] : '';
        $recur = !empty($_REQUEST['event_recur']) ? $_REQUEST['event_recur'] : '';
        $repeats = !empty($_REQUEST['event_repeats']) ? $_REQUEST['event_repeats'] : '';
        $category = !empty($_REQUEST['event_category']) ? $_REQUEST['event_category'] : '';
        $linky = !empty($_REQUEST['event_link']) ? $_REQUEST['event_link'] : '';

        // Perform some validation on the submitted dates - this checks for valid years and months
        $date_format_one = '/^([0-9]{4})-([0][1-9])-([0-3][0-9])$/';
        $date_format_two = '/^([0-9]{4})-([1][0-2])-([0-3][0-9])$/';
        if ((preg_match($date_format_one, $begin) || preg_match($date_format_two, $begin)) && (preg_match($date_format_one, $end) || preg_match($date_format_two, $end))) {
            // We know we have a valid year and month and valid integers for days so now we do a final check on the date
            $begin_split = explode('-', $begin);
            $begin_y = $begin_split[0];
            $begin_m = $begin_split[1];
            $begin_d = $begin_split[2];
            $end_split = explode('-', $end);
            $end_y = $end_split[0];
            $end_m = $end_split[1];
            $end_d = $end_split[2];
            if (checkdate($begin_m, $begin_d, $begin_y) && checkdate($end_m, $end_d, $end_y)) {
                // Ok, now we know we have valid dates, we want to make sure that they are either equal or that the end date is later than the start date
                if (strtotime($end) >= strtotime($begin)) {
                    $start_date_ok = 1;
                    $end_date_ok = 1;
                } else {
                    ?>
                    <div class="error"><p><strong><?php _e('Error', 'calendar'); ?>:</strong> <?php _e('Your event end date must be either after or the same as your event begin date', 'calendar'); ?></p></div>
                    <?php
                }
            } else {
                ?>
                <div class="error"><p><strong><?php _e('Error', 'calendar'); ?>:</strong> <?php _e('Your date formatting is correct but one or more of your dates is invalid. Check for number of days in month and leap year related errors.', 'calendar'); ?></p></div>
                <?php
            }
        } else {
            ?>
            <div class="error"><p><strong><?php _e('Error', 'calendar'); ?>:</strong> <?php _e('Both start and end dates must be entered and be in the format YYYY-MM-DD', 'calendar'); ?></p></div>
            <?php
        }
        // We check for a valid time, or an empty one
        $time_format_one = '/^([0-1][0-9]):([0-5][0-9])$/';
        $time_format_two = '/^([2][0-3]):([0-5][0-9])$/';
        if (preg_match($time_format_one, $time) || preg_match($time_format_two, $time) || $time == '') {
            $time_ok = 1;
            if ($time == '') {
                $time_to_use = '00:00:00';
            } else if ($time == '00:00') {
                $time_to_use = '00:00:01';
            } else {
                $time_to_use = $time;
            }
        } else {
            ?>
            <div class="error"><p><strong><?php _e('Error', 'calendar'); ?>:</strong> <?php _e('The time field must either be blank or be entered in the format hh:mm', 'calendar'); ?></p></div>
            <?php
        }
        // We check to make sure the URL is alright
        if (preg_match('/^(http)(s?)(:)\/\//', $linky) || $linky == '') {
            $url_ok = 1;
        } else {
            ?>
            <div class="error"><p><strong><?php _e('Error', 'calendar'); ?>:</strong> <?php _e('The URL entered must either be prefixed with http:// or be completely blank', 'calendar'); ?></p></div>
            <?php
        }
        // The title must be at least one character in length and no more than 30
        if (preg_match('/^.{1,30}$/', $title)) {
            $title_ok = 1;
        } else {
            ?>
            <div class="error"><p><strong><?php _e('Error', 'calendar'); ?>:</strong> <?php _e('The event title must be between 1 and 30 characters in length', 'calendar'); ?></p></div>
            <?php
        }
        // We run some checks on recurrance
        $repeats = (int) $repeats;
        if (($repeats == 0 && $recur == 'S') || (($repeats >= 0) && ($recur == 'W' || $recur == 'M' || $recur == 'Y' || $recur == 'U'))) {
            $recurring_ok = 1;
        } else {
            ?>
            <div class="error"><p><strong><?php _e('Error', 'calendar'); ?>:</strong> <?php _e('The repetition value must be 0 unless a type of recurrance is selected in which case the repetition value must be 0 or higher', 'calendar'); ?></p></div>
            <?php
        }
        if (isset($start_date_ok) && isset($end_date_ok) && isset($time_ok) && isset($url_ok) && isset($title_ok) && isset($recurring_ok)) {
            $sql = "INSERT INTO " . WTF_CALENDAR_TABLE . " SET event_title='" . mysql_escape_string($title)
                    . "', event_desc='" . mysql_escape_string($desc) . "', event_begin='" . mysql_escape_string($begin)
                    . "', event_end='" . mysql_escape_string($end) . "', event_time='" . mysql_escape_string($time_to_use) . "', event_recur='" . mysql_escape_string($recur) . "', event_repeats='" . mysql_escape_string($repeats) . "', event_author=" . $current_user->ID . ", event_category=" . mysql_escape_string($category) . ", event_link='" . mysql_escape_string($linky) . "'";

            $wpdb->get_results($sql);

            $sql = "SELECT event_id FROM " . WTF_CALENDAR_TABLE . " WHERE event_title='" . mysql_escape_string($title) . "'"
                    . " AND event_desc='" . mysql_escape_string($desc) . "' AND event_begin='" . mysql_escape_string($begin) . "' AND event_end='" . mysql_escape_string($end) . "' AND event_recur='" . mysql_escape_string($recur) . "' AND event_repeats='" . mysql_escape_string($repeats) . "' LIMIT 1";
            $result = $wpdb->get_results($sql);

            if (empty($result) || empty($result[0]->event_id)) {
                ?>
                <div class="error"><p><strong><?php _e('Error', 'calendar'); ?>:</strong> <?php _e('An event with the details you submitted could not be found in the database. This may indicate a problem with your database or the way in which it is configured.', 'calendar'); ?></p></div>
                <?php
            } else {
                ?>
                <div class="updated"><p><?php _e('Event added. It will now show in your calendar.', 'calendar'); ?></p></div>
                <?php
            }
        } else {
            // The form is going to be rejected due to field validation issues, so we preserve the users entries here
            $users_entries->event_title = $title;
            $users_entries->event_desc = $desc;
            $users_entries->event_begin = $begin;
            $users_entries->event_end = $end;
            $users_entries->event_time = $time;
            $users_entries->event_recur = $recur;
            $users_entries->event_repeats = $repeats;
            $users_entries->event_category = $category;
            $users_entries->event_link = $linky;
        }
    }
// Permit saving of events that have been edited
    elseif ($action == 'edit_save') {
        $title = !empty($_REQUEST['event_title']) ? $_REQUEST['event_title'] : '';
        $desc = !empty($_REQUEST['event_desc']) ? $_REQUEST['event_desc'] : '';
        $begin = !empty($_REQUEST['event_begin']) ? $_REQUEST['event_begin'] : '';
        $end = !empty($_REQUEST['event_end']) ? $_REQUEST['event_end'] : '';
        $time = !empty($_REQUEST['event_time']) ? $_REQUEST['event_time'] : '';
        $recur = !empty($_REQUEST['event_recur']) ? $_REQUEST['event_recur'] : '';
        $repeats = !empty($_REQUEST['event_repeats']) ? $_REQUEST['event_repeats'] : '';
        $category = !empty($_REQUEST['event_category']) ? $_REQUEST['event_category'] : '';
        $linky = !empty($_REQUEST['event_link']) ? $_REQUEST['event_link'] : '';

        if (empty($event_id)) {
            ?>
            <div class="error"><p><strong><?php _e('Failure', 'calendar'); ?>:</strong> <?php _e("You can't update an event if you haven't submitted an event id", 'calendar'); ?></p></div>
            <?php
        } else {
            // Perform some validation on the submitted dates - this checks for valid years and months
            $date_format_one = '/^([0-9]{4})-([0][1-9])-([0-3][0-9])$/';
            $date_format_two = '/^([0-9]{4})-([1][0-2])-([0-3][0-9])$/';
            if ((preg_match($date_format_one, $begin) || preg_match($date_format_two, $begin)) && (preg_match($date_format_one, $end) || preg_match($date_format_two, $end))) {
                // We know we have a valid year and month and valid integers for days so now we do a final check on the date
                $begin_split = explode('-', $begin);
                $begin_y = $begin_split[0];
                $begin_m = $begin_split[1];
                $begin_d = $begin_split[2];
                $end_split = explode('-', $end);
                $end_y = $end_split[0];
                $end_m = $end_split[1];
                $end_d = $end_split[2];
                if (checkdate($begin_m, $begin_d, $begin_y) && checkdate($end_m, $end_d, $end_y)) {
                    // Ok, now we know we have valid dates, we want to make sure that they are either equal or that the end date is later than the start date
                    if (strtotime($end) >= strtotime($begin)) {
                        $start_date_ok = 1;
                        $end_date_ok = 1;
                    } else {
                        ?>
                        <div class="error"><p><strong><?php _e('Error', 'calendar'); ?>:</strong> <?php _e('Your event end date must be either after or the same as your event begin date', 'calendar'); ?></p></div>
                        <?php
                    }
                } else {
                    ?>
                    <div class="error"><p><strong><?php _e('Error', 'calendar'); ?>:</strong> <?php _e('Your date formatting is correct but one or more of your dates is invalid. Check for number of days in month and leap year related errors.', 'calendar'); ?></p></div>
                    <?php
                }
            } else {
                ?>
                <div class="error"><p><strong><?php _e('Error', 'calendar'); ?>:</strong> <?php _e('Both start and end dates must be entered and be in the format YYYY-MM-DD', 'calendar'); ?></p></div>
                <?php
            }
            // We check for a valid time, or an empty one
            $time_format_one = '/^([0-1][0-9]):([0-5][0-9])$/';
            $time_format_two = '/^([2][0-3]):([0-5][0-9])$/';
            if (preg_match($time_format_one, $time) || preg_match($time_format_two, $time) || $time == '') {
                $time_ok = 1;
                if ($time == '') {
                    $time_to_use = '00:00:00';
                } else if ($time == '00:00') {
                    $time_to_use = '00:00:01';
                } else {
                    $time_to_use = $time;
                }
            } else {
                ?>
                <div class="error"><p><strong><?php _e('Error', 'calendar'); ?>:</strong> <?php _e('The time field must either be blank or be entered in the format hh:mm', 'calendar'); ?></p></div>
                <?php
            }
            // We check to make sure the URL is alright
            if (preg_match('/^(http)(s?)(:)\/\//', $linky) || $linky == '') {
                $url_ok = 1;
            } else {
                ?>
                <div class="error"><p><strong><?php _e('Error', 'calendar'); ?>:</strong> <?php _e('The URL entered must either be prefixed with http:// or be completely blank', 'calendar'); ?></p></div>
                <?php
            }
            // The title must be at least one character in length and no more than 30
            if (preg_match('/^.{1,30}$/', $title)) {
                $title_ok = 1;
            } else {
                ?>
                <div class="error"><p><strong><?php _e('Error', 'calendar'); ?>:</strong> <?php _e('The event title must be between 1 and 30 characters in length', 'calendar'); ?></p></div>
                <?php
            }
            // We run some checks on recurrance
            $repeats = (int) $repeats;
            if (($repeats == 0 && $recur == 'S') || (($repeats >= 0) && ($recur == 'W' || $recur == 'M' || $recur == 'Y' || $recur == 'U'))) {
                $recurring_ok = 1;
            } else {
                ?>
                <div class="error"><p><strong><?php _e('Error', 'calendar'); ?>:</strong> <?php _e('The repetition value must be 0 unless a type of recurrance is selected in which case the repetition value must be 0 or higher', 'calendar'); ?></p></div>
                <?php
            }
            if (isset($start_date_ok) && isset($end_date_ok) && isset($time_ok) && isset($url_ok) && isset($title_ok) && isset($recurring_ok)) {
                $sql = "UPDATE " . WTF_CALENDAR_TABLE . " SET event_title='" . mysql_escape_string($title)
                        . "', event_desc='" . mysql_escape_string($desc) . "', event_begin='" . mysql_escape_string($begin)
                        . "', event_end='" . mysql_escape_string($end) . "', event_time='" . mysql_escape_string($time_to_use) . "', event_recur='" . mysql_escape_string($recur) . "', event_repeats='" . mysql_escape_string($repeats) . "', event_author=" . $current_user->ID . ", event_category=" . mysql_escape_string($category) . ", event_link='" . mysql_escape_string($linky) . "' WHERE event_id='" . mysql_escape_string($event_id) . "'";

                $wpdb->get_results($sql);

                $sql = "SELECT event_id FROM " . WTF_CALENDAR_TABLE . " WHERE event_title='" . mysql_escape_string($title) . "'"
                        . " AND event_desc='" . mysql_escape_string($desc) . "' AND event_begin='" . mysql_escape_string($begin) . "' AND event_end='" . mysql_escape_string($end) . "' AND event_recur='" . mysql_escape_string($recur) . "' AND event_repeats='" . mysql_escape_string($repeats) . "' LIMIT 1";
                $result = $wpdb->get_results($sql);

                if (empty($result) || empty($result[0]->event_id)) {
                    ?>
                    <div class="error"><p><strong><?php _e('Failure', 'calendar'); ?>:</strong> <?php _e('The database failed to return data to indicate the event has been updated sucessfully. This may indicate a problem with your database or the way in which it is configured.', 'calendar'); ?></p></div>
                    <?php
                } else {
                    ?>
                    <div class="updated"><p><?php _e('Event updated successfully', 'calendar'); ?></p></div>
                    <?php
                }
            } else {
                // The form is going to be rejected due to field validation issues, so we preserve the users entries here
                $users_entries->event_title = $title;
                $users_entries->event_desc = $desc;
                $users_entries->event_begin = $begin;
                $users_entries->event_end = $end;
                $users_entries->event_time = $time;
                $users_entries->event_recur = $recur;
                $users_entries->event_repeats = $repeats;
                $users_entries->event_category = $category;
                $users_entries->event_link = $linky;
                $error_with_saving = 1;
            }
        }
    }
// Deal with deleting an event from the database
    elseif ($action == 'delete') {
        if (empty($event_id)) {
            ?>
            <div class="error"><p><strong><?php _e('Error', 'calendar'); ?>:</strong> <?php _e("You can't delete an event if you haven't submitted an event id", 'calendar'); ?></p></div>
            <?php
        } else {
            $sql = "DELETE FROM " . WTF_CALENDAR_TABLE . " WHERE event_id='" . mysql_escape_string($event_id) . "'";
            $wpdb->get_results($sql);

            $sql = "SELECT event_id FROM " . WTF_CALENDAR_TABLE . " WHERE event_id='" . mysql_escape_string($event_id) . "'";
            $result = $wpdb->get_results($sql);

            if (empty($result) || empty($result[0]->event_id)) {
                ?>
                <div class="updated"><p><?php _e('Event deleted successfully', 'calendar'); ?></p></div>
                <?php
            } else {
                ?>
                <div class="error"><p><strong><?php _e('Error', 'calendar'); ?>:</strong> <?php _e('Despite issuing a request to delete, the event still remains in the database. Please investigate.', 'calendar'); ?></p></div>
                <?php
            }
        }
    }

// Now follows a little bit of code that pulls in the main
// components of this page; the edit form and the list of events
    ?>

    <div class="wrap">
    <?php
    if ($action == 'edit' || ($action == 'edit_save' && isset($error_with_saving))) {
        ?>
            <h2><?php _e('Edit Event', 'calendar'); ?></h2>
        <?php
        if (empty($event_id)) {
            echo "<div class=\"error\"><p>" . __("You must provide an event id in order to edit it", 'calendar') . "</p></div>";
        } else {
            wtf_cal_events_edit_form('edit_save', $event_id);
        }
    } else {
        ?>
            <h2><?php _e('Add Event', 'calendar'); ?></h2>
        <?php wtf_cal_events_edit_form(); ?>

            <h2><?php _e('Manage Events', 'calendar'); ?></h2>
        <?php
        wtf_cal_events_display_list();
    }
    ?>
    </div>

    <?php
} //end wtf_cal_edit_calendar

// Function to handle the management of categories
function wtf_cal_manage_categories()
{
    global $wpdb;
    ?>
    <style type="text/css">
        <!--
        .error {
            background: lightcoral;
            border: 1px solid #e64f69;
            margin: 1em 5% 10px;
            padding: 0 1em 0 1em;
        }

        .center {
            text-align: center;
        }
        .right {
            text-align: right;
        }
        .left {
            text-align: left;
        }
        .top {
            vertical-align: top;
        }
        .bold {
            font-weight: bold;
        }
        .private {
            color: #e64f69;
        }
        //-->

    </style>
    <?php
    // We do some checking to see what we're doing
    if (isset($_POST['mode']) && $_POST['mode'] == 'add') {
        // Proceed with the save
        $sql = "INSERT INTO " . WTF_CALENDAR_CATEGORIES_TABLE . " SET category_name='" . mysql_escape_string($_POST['category_name']) . "', category_colour='" . mysql_escape_string($_POST['category_colour']) . "'";
        $wpdb->get_results($sql);
        echo "<div class=\"updated\"><p><strong>" . __('Category added successfully', 'wtf_calendar') . "</strong></p></div>";
    } else if (isset($_GET['mode']) && isset($_GET['category_id']) && $_GET['mode'] == 'delete') {
        $sql = "DELETE FROM " . WTF_CALENDAR_CATEGORIES_TABLE . " WHERE category_id=" . mysql_escape_string($_GET['category_id']);
        $wpdb->get_results($sql);
        $sql = "UPDATE " . WTF_CALENDAR_TABLE . " SET event_category=1 WHERE event_category=" . mysql_escape_string($_GET['category_id']);
        $wpdb->get_results($sql);
        echo "<div class=\"updated\"><p><strong>" . __('Category deleted successfully', 'wtf_calendar') . "</strong></p></div>";
    } else if (isset($_GET['mode']) && isset($_GET['category_id']) && $_GET['mode'] == 'edit' && !isset($_POST['mode'])) {
        $sql = "SELECT * FROM " . WTF_CALENDAR_CATEGORIES_TABLE . " WHERE category_id=" . intval(mysql_escape_string($_GET['category_id']));
        $cur_cat = $wpdb->get_row($sql);
        ?>
        <div class="wrap">
            <h2><?php _e('Edit Category', 'calendar'); ?></h2>
            <form name="catform" id="catform" class="wrap" method="post" action="<?php echo bloginfo('wpurl'); ?>/wp-admin/admin.php?page=calendar-categories">
                <input type="hidden" name="mode" value="edit" />
                <input type="hidden" name="category_id" value="<?php echo stripslashes($cur_cat->category_id) ?>" />
                <div id="linkadvanceddiv" class="postbox">
                    <div class="inside">
                        <table cellpadding="5" cellspacing="5">
                            <tr>
                                <td><legend><?php _e('Category Name', 'calendar'); ?>:</legend></td>
                            <td><input type="text" name="category_name" class="input" size="30" maxlength="30" value="<?php echo stripslashes($cur_cat->category_name) ?>" /></td>
                            </tr>
                            <tr>
                                <td><legend><?php _e('Category Colour (Hex format)', 'calendar'); ?>:</legend></td>
                            <td><input type="text" name="category_colour" class="input" size="10" maxlength="7" value="<?php echo stripslashes($cur_cat->category_colour) ?>" /></td>
                            </tr>
                        </table>
                    </div>
                    <div>&nbsp;</div>
                </div>
                <input type="submit" name="save" class="button bold" value="<?php _e('Save', 'calendar'); ?> &raquo;" />
            </form>
        </div>
        <?php
    } else if (isset($_POST['mode']) && isset($_POST['category_id']) && isset($_POST['category_name']) && isset($_POST['category_colour']) && $_POST['mode'] == 'edit') {
        // Proceed with the save
        $sql = "UPDATE " . WTF_CALENDAR_CATEGORIES_TABLE . " SET category_name='" . mysql_escape_string($_POST['category_name']) . "', category_colour='" . mysql_escape_string($_POST['category_colour']) . "' WHERE category_id=" . mysql_escape_string($_POST['category_id']);
        $wpdb->get_results($sql);
        echo "<div class=\"updated\"><p><strong>" . __('Category edited successfully', 'wtf_calendar') . "</strong></p></div>";
    }

    $get_mode = 0;
    $post_mode = 0;
    if (isset($_GET['mode'])) {
        if ($_GET['mode'] == 'edit') {
            $get_mode = 1;
        }
    }
    if (isset($_POST['mode'])) {
        if ($_POST['mode'] == 'edit') {
            $post_mode = 1;
        }
    }
    if ($get_mode != 1 || $post_mode == 1) {
        ?>

        <div class="wrap">
            <h2><?php _e('Add Category', 'calendar'); ?></h2>
            <form name="catform" id="catform" class="wrap" method="post" action="<?php echo bloginfo('wpurl'); ?>/wp-admin/admin.php?page=calendar-categories">
                <input type="hidden" name="mode" value="add" />
                <input type="hidden" name="category_id" value="">
                <div id="linkadvanceddiv" class="postbox">
                    <div class="inside">
                        <table cellspacing="5" cellpadding="5">
                            <tr>
                                <td><legend><?php _e('Category Name', 'calendar'); ?>:</legend></td>
                            <td><input type="text" name="category_name" class="input" size="30" maxlength="30" value="" /></td>
                            </tr>
                            <tr>
                                <td><legend><?php _e('Category Colour (Hex format)', 'calendar'); ?>:</legend></td>
                            <td><input type="text" name="category_colour" class="input" size="10" maxlength="7" value="" /></td>
                            </tr>
                        </table>
                    </div>
                    <div>&nbsp;</div>
                </div>
                <input type="submit" name="save" class="button bold" value="<?php _e('Save', 'calendar'); ?> &raquo;" />
            </form>
            <h2><?php _e('Manage Categories', 'calendar'); ?></h2>
        <?php
        // We pull the categories from the database
        $categories = $wpdb->get_results("SELECT * FROM " . WTF_CALENDAR_CATEGORIES_TABLE . " ORDER BY category_id ASC");

        if (!empty($categories)) {
            ?>
                <table class="widefat page fixed" width="50%" cellpadding="3" cellspacing="3">
                    <thead>
                        <tr>
                            <th class="manage-column" scope="col"><?php _e('ID', 'calendar') ?></th>
                            <th class="manage-column" scope="col"><?php _e('Category Name', 'calendar') ?></th>
                            <th class="manage-column" scope="col"><?php _e('Category Colour', 'calendar') ?></th>
                            <th class="manage-column" scope="col"><?php _e('Edit', 'calendar') ?></th>
                            <th class="manage-column" scope="col"><?php _e('Delete', 'calendar') ?></th>
                        </tr>
                    </thead>
            <?php
            $class = '';
            foreach ($categories as $category) {
                $class = ($class == 'alternate') ? '' : 'alternate';
                ?>
                        <tr class="<?php echo $class; ?>">
                            <th scope="row"><?php echo stripslashes($category->category_id); ?></th>
                            <td><?php echo stripslashes($category->category_name); ?></td>
                            <td>&nbsp;</td>
                            <td><a href="<?php echo bloginfo('wpurl') ?>/wp-admin/admin.php?page=calendar-categories&amp;mode=edit&amp;category_id=<?php echo stripslashes($category->category_id); ?>" class='edit'><?php echo __('Edit', 'wtf_calendar'); ?></a></td>
                <?php
                if ($category->category_id == 1) {
                    echo '<td>' . __('N/A', 'wtf_calendar') . '</td>';
                } else {
                    ?>
                                <td><a href="<?php echo bloginfo('wpurl') ?>/wp-admin/admin.php?page=calendar-categories&amp;mode=delete&amp;category_id=<?php echo stripslashes($category->category_id); ?>" class="delete" onclick="return confirm('<?php echo __('Are you sure you want to delete this category?', 'wtf_calendar'); ?>')"><?php echo __('Delete', 'wtf_calendar'); ?></a></td>
                    <?php
                }
                ?>
                        </tr>
                <?php
            }
            ?>
                </table>
            <?php
        } else {
            echo '<p>' . __('There are no categories in the database - something has gone wrong!', 'wtf_calendar') . '</p>';
        }
        ?>
        </div>

        <?php
    }
} //end wtf_cal_manage_categories

