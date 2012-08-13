<?php
//Redirect to the main plugin options page if form has been submitted
if (isset($_GET['action'])) {
    if ('add' == $_GET['action'] && isset($_GET['updated'])) {
        wp_redirect(admin_url('admin.php?page=theme-plugins-gcal&updated=added'));
    }
}

add_settings_section('gce_add', 'Add a Feed', 'gce_add_main_text', 'add_feed');
add_settings_field('gce_add_id_field', 'Feed ID', 'gce_add_id_field', 'add_feed', 'gce_add');
add_settings_field('gce_add_title_field', 'Feed Title', 'gce_add_title_field', 'add_feed', 'gce_add');
add_settings_field('gce_add_url_field', 'Feed URL', 'gce_add_url_field', 'add_feed', 'gce_add');
add_settings_field('gce_add_retrieve_from_field', 'Retrieve events from', 'gce_add_retrieve_from_field', 'add_feed', 'gce_add');
add_settings_field('gce_add_retrieve_until_field', 'Retrieve events until', 'gce_add_retrieve_until_field', 'add_feed', 'gce_add');
add_settings_field('gce_add_max_events_field', 'Maximum number of events to retrieve', 'gce_add_max_events_field', 'add_feed', 'gce_add');
add_settings_field('gce_add_date_format_field', 'Date format', 'gce_add_date_format_field', 'add_feed', 'gce_add');
add_settings_field('gce_add_time_format_field', 'Time format', 'gce_add_time_format_field', 'add_feed', 'gce_add');
add_settings_field('gce_add_timezone_field', 'Timezone adjustment', 'gce_add_timezone_field', 'add_feed', 'gce_add');
add_settings_field('gce_add_cache_duration_field', 'Cache duration', 'gce_add_cache_duration_field', 'add_feed', 'gce_add');
add_settings_field('gce_add_multiple_field', 'Show multiple day events on each day?', 'gce_add_multiple_field', 'add_feed', 'gce_add');

add_settings_section('gce_add_display', 'Display Options', 'gce_add_display_main_text', 'add_display');
add_settings_field('gce_add_use_builder_field', 'Select display customization method', 'gce_add_use_builder_field', 'add_display', 'gce_add_display');

add_settings_section('gce_add_builder', __('Event Display Builder'), 'gce_add_builder_main_text', 'add_builder');
add_settings_field('gce_add_builder_field', 'Event display builder HTML and shortcodes', 'gce_add_builder_field', 'add_builder', 'gce_add_builder');

add_settings_section('gce_add_simple_display', __('Simple Display Options'), 'gce_add_simple_display_main_text', 'add_simple_display');
add_settings_field('gce_add_display_start_field', 'Display start time / date?', 'gce_add_display_start_field', 'add_simple_display', 'gce_add_simple_display');
add_settings_field('gce_add_display_end_field', 'Display end time / date?', 'gce_add_display_end_field', 'add_simple_display', 'gce_add_simple_display');
add_settings_field('gce_add_display_separator_field', 'Separator text / characters', 'gce_add_display_separator_field', 'add_simple_display', 'gce_add_simple_display');
add_settings_field('gce_add_display_location_field', 'Display location?', 'gce_add_display_location_field', 'add_simple_display', 'gce_add_simple_display');
add_settings_field('gce_add_display_desc_field', 'Display description?', 'gce_add_display_desc_field', 'add_simple_display', 'gce_add_simple_display');
add_settings_field('gce_add_display_link_field', 'Display link to event?', 'gce_add_display_link_field', 'add_simple_display', 'gce_add_simple_display');

//Main text
function gce_add_main_text() {
    ?>
    <p>Enter the feed details below, then click the Add Feed button.</p>
    <?php
}

//ID
function gce_add_id_field() {
    $options = get_option(GCE_OPTIONS_NAME);
    $id = 1;
    if (!empty($options)) { //If there are no saved feeds
        //Go to last saved feed
        end($options);
        //Set id to last feed id + 1
        $id = key($options) + 1;
    }
    ?>
    <input type="text" disabled="disabled" value="<?php echo $id; ?>" size="3" />
    <input type="hidden" name="gce_options[id]" value="<?php echo $id; ?>" />
    <?php
}

//Title
function gce_add_title_field() {
    ?>
    <span class="description">Anything you like. 'Upcoming Club Events', for example.</span>
    <br />
    <input type="text" name="gce_options[title]" size="50" />
    <?php
}

//URL
function gce_add_url_field() {
    ?>
    <span class="description">This will probably be something like: <code>http://www.google.com/calendar/feeds/your-email@gmail.com/public/basic</code>.</span>
    <br />
    <span class="description">or: <code>http://www.google.com/calendar/feeds/your-email@gmail.com/private-d65741b037h695ff274247f90746b2ty/basic</code>.</span>
    <br />
    <input type="text" name="gce_options[url]" size="100" class="required" />
    <?php
}

//Retrieve events from
function gce_add_retrieve_from_field() {
    ?>
    <span class="description">
        The point in time at which to start retrieving events. Use the text-box to specify an additional offset from you chosen start point. The offset should be provided in seconds (3600 = 1 hour, 86400 = 1 day) and can be negative. If you have selected the 'Specific date / time' option, enter a
        <a href="http://www.timestampgenerator.com" target="_blank">UNIX timestamp</a>
        in the text-box.
    </span>
    <br />
    <select name="gce_options[retrieve_from]">
        <option value="now">Now</option>
        <option value="today" selected="selected">00:00 today</option>
        <option value="week">Start of current week</option>
        <option value="month-start">Start of current month</option>
        <option value="month-end">End of current month</option>
        <option value="any">The beginning of time</option>
        <option value="date">Specific date / time</option>
    </select>
    <input type="text" name="gce_options[retrieve_from_value]" value="0" />
    <?php
}

//Retrieve events until
function gce_add_retrieve_until_field() {
    ?>
    <span class="description">The point in time at which to stop retrieving events. The instructions for the above option also apply here.</span>
    <br />
    <select name="gce_options[retrieve_until]">
        <option value="now">Now</option>
        <option value="today">00:00 today</option>
        <option value="week">Start of current week</option>
        <option value="month-start">Start of current month</option>
        <option value="month-end">End of current month</option>
        <option value="any" selected="selected">The end of time</option>
        <option value="date">Specific date / time</option>

    </select>
    <input type="text" name="gce_options[retrieve_until_value]" value="0" />
    <?php
}

//Max events
function gce_add_max_events_field() {
    ?>
    <span class="description">Set this to a few more than you actually want to display (due to caching and timezone issues). The exact number to display can be configured per shortcode / widget.</span>
    <br />
    <input type="text" name="gce_options[max_events]" value="25" size="3" />
    <?php
}

//Date format
function gce_add_date_format_field() {
    ?>
    <span class="description">In <a href="http://php.net/manual/en/function.date.php" target="_blank">PHP date format</a>. Leave this blank if you\'d rather stick with the default format for your blog.</span>
    <br />
    <input type="text" name="gce_options[date_format]" />
    <?php
}

//Time format
function gce_add_time_format_field() {
    ?>
    <span class="description">In <a href="http://php.net/manual/en/function.date.php" target="_blank">PHP date format</a>. Again, leave this blank to stick with the default.</span>
    <br />
    <input type="text" name="gce_options[time_format]" />
    <?php
}

//Timezone offset
function gce_add_timezone_field() {
    require_once GCE_DIRECTORY . '/admin/timezone-choices.php';
    $tzstring = get_option('timezone_string');
    $timezone_list = gce_get_timezone_choices($tzstring);
    ?>
    <span class="description">If you are having problems with dates and times displaying in the wrong timezone, select a city in your required timezone here.</span>
    <br />
    <?php echo $timezone_list; ?>
    <?php
}

//Cache duration
function gce_add_cache_duration_field() {
    ?>
    <span class="description">The length of time, in seconds, to cache the feed (43200 = 12 hours). If this feed changes regularly, you may want to reduce the cache duration.</span>
    <br />
    <input type="text" name="gce_options[cache_duration]" value="43200" />
    <?php
}

//Multiple day events
function gce_add_multiple_field() {
    ?>
    <span class="description">Show events that span multiple days on each day that they span, rather than just the first day.</span>
    <br />
    <input type="checkbox" name="gce_options[multiple_day]" value="true" />
    <br /><br />
    <?php
}

//Display options
function gce_add_display_main_text() {
    ?>
    <p>These settings control what information will be displayed for this feed in the tooltip (for grids), or in a list.</p>
    <?php
}

function gce_add_use_builder_field() {
    ?>
    <span class="description">It is recommended that you use the event display builder option, as it provides much more flexibility than the simple display options. The event display builder can do everything the simple display options can, plus lots more!</span>
    <br />
    <select name="gce_options[use_builder]">
        <option value="true" selected="selected">Event display builder</option>
        <option value="false">Simple display options</option>
    </select>
    <?php
}

//Event display builder
function gce_add_builder_main_text() {
    ?>
    <p class="gce-event-builder">
        Use the event display builder to customize how event information will be displayed in the grid tooltips and in lists. Use HTML and the shortcodes (explained below) to display the information you require. A basic example display format is provided as a starting point. For more information, take a look at the
        <a href="http://www.rhanney.co.uk/plugins/google-calendar-events/event-display-builder" target="_blank">event display builder guide</a>
    </p>
    <?php
}

function gce_add_builder_field() {
    ?>
    <textarea name="gce_options[builder]" rows="10" cols="80">
        &lt;div class="gce-list-event gce-tooltip-event"&gt;[event-title]&lt;/div&gt;
        &lt;div&gt;&lt;span&gt;Starts:&lt;/span&gt; [start-time]&lt;/div&gt;
        &lt;div&gt;&lt;span&gt;Ends:&lt;/span&gt; [end-date] - [end-time]&lt;/div&gt;
        [if-location]&lt;div&gt;&lt;span&gt;Location:&lt;/span&gt; [location]&lt;/div&gt;[/if-location]
        [if-description]&lt;div&gt;&lt;span&gt;Description:&lt;/span&gt; [description]&lt;/div&gt;[/if-description]
        &lt;div&gt;[link newwindow="true"]More details...[/link]&lt;/div&gt;
    </textarea>
    <br />
    <p style="margin-top:20px;">
        (More information on all of the below shortcodes and attributes, and working examples, can be found in the
        <a href="http://www.rhanney.co.uk/plugins/google-calendar-events/event-display-builder" target="_blank">event display builder guide</a>)
    </p>
    <h4>Event information shortcodes:</h4>
    <ul>
        <li><code>[event-title]</code><span class="description"> - The event title (possible attributes: <code>html</code>, <code>markdown</code>)</span></li>
        <li><code>[start-time]</code><span class="description"> - The event start time. Will use the time format specified in the above settings</span></li>
        <li><code>[start-date]</code><span class="description"> - The event start date. Will use the date format specified in the above settings</span></li>
        <li><code>[start-custom]</code><span class="description"> - The event start date / time. Will use the format specified in the <code>format</code> attribute (possible attributes: <code>format</code>)</span></li>
        <li><code>[start-human]</code><span class="description"> - The difference between the start time of the event and the time now, in human-readable format, such as '1 hour', '4 days', '15 mins' (possible attributes: <code>precision</code>)</span></li>
        <li><code>[end-time]</code><span class="description"> - The event end time. Will use the time format specified in the above settings</span></li>
        <li><code>[end-date]</code><span class="description"> - The event end date. Will use the date format specified in the above settings</span></li>
        <li><code>[end-custom]</code><span class="description"> - The event end date / time. Will use the format specified in the <code>format</code> attribute (possible attributes: <code>format</code>)</span></li>
        <li><code>[end-human]</code><span class="description"> - The difference between the end time of the event and the time now, in human-readable format (possible attributes: <code>precision</code>)</span></li>
        <li><code>[location]</code><span class="description"> - The event location (possible attributes: <code>html</code>, <code>markdown</code>)</span></li>
        <li><code>[maps-link]&hellip;[/maps-link]</code><span class="description"> - Anything between the opening and closing shortcode tags (inlcuding further shortcodes) will be linked to Google Maps, using the event location as a search parameter (possible attributes: <code>newwindow</code>)</span></li>
        <li><code>[description]</code><span class="description"> - The event description (possible attributes: <code>html</code>, <code>markdown</code>, <code>limit</code>)</span></li>
        <li><code>[link]&hellip;[/link]</code><span class="description"> - Anything between the opening and closing shortcode tags (inlcuding further shortcodes) will be linked to the Google Calendar page for the event (possible attributes: <code>newwindow</code>)</span></li>
        <li><code>[url]</code><span class="description"> - The raw URL to the Google Calendar page for the event</span></li>
        <li><code>[length]</code><span class="description"> - The length of the event, in human-readable format (possible attributes: <code>precision</code>)</span></li>
        <li><code>[event-num]</code><span class="description"> - The position of the event in the current list, or the position of the event in the current month (for grids)</span></li>
        <li><code>[event-id]</code><span class="description"> - The event UID (a unique identifier assigned to the event by Google)</span></li>
    </ul>
    <h4>Feed information shortcodes:</h4>
    <ul>
        <li><code>[feed-title]</code><span class="description"> - The title of the feed from which the event comes</span></li>
        <li><code>[feed-id]</code><span class="description"> - The ID of the feed from which the event comes</span></li>
        <li><code>[cal-id]</code><span class="description"> - The calendar ID (a unique identifier assigned to the calendar by Google)</span></li>
    </ul>
    <h4>Conditional shortcodes:</h4>
    <p class="description" style="margin-bottom:18px;">Anything entered between the opening and closing tags of each of the following shortcodes will only be displayed if its condition (below) is met.</p>
    <ul>
        <li><code>[if-all-day]&hellip;[/if-all-day]</code><span class="description"> - The event is an all-day event</span></li>
        <li><code>[if-not-all-day]&hellip;[/if-not-all-day]</code><span class="description"> - The event is not an all-day event</span></li>
        <li><code>[if-title]&hellip;[/if-title]</code><span class="description"> - The event has a title</span></li>
        <li><code>[if-description]&hellip;[/if-description]</code><span class="description"> - The event has a description</span></li>
        <li><code>[if-location]&hellip;[/if-location]</code><span class="description"> - The event has a location</span></li>
        <li><code>[if-tooltip]&hellip;[/if-tooltip]</code><span class="description"> - The event is to be displayed in a tooltip (not a list)</span></li>
        <li><code>[if-list]&hellip;[/if-list]</code><span class="description"> - The event is to be displayed in a list (not a tooltip)</span></li>
        <li><code>[if-now]&hellip;[/if-now]</code><span class="description"> - The event is taking place now (after the start time, but before the end time)</span></li>
        <li><code>[if-not-now]&hellip;[/if-not-now]</code><span class="description"> - The event is not taking place now (may have ended or not yet started)</span></li>
        <li><code>[if-started]&hellip;[/if-started]</code><span class="description"> - The event has started (even if it has also ended)</span></li>
        <li><code>[if-not-started]&hellip;[/if-not-started]</code><span class="description"> - The event has not started</span></li>
        <li><code>[if-ended]&hellip;[/if-ended]</code><span class="description"> - The event has ended</span></li>
        <li><code>[if-not-ended]&hellip;[/if-not-ended]</code><span class="description"> - The event has not ended (even if it hasn\'t started)</span></li>
        <li><code>[if-first]&hellip;[/if-first]</code><span class="description"> - The event is the first of the day</span></li>
        <li><code>[if-not-first]&hellip;[/if-not-first]</code><span class="description"> - The event is not the first of the day</span></li>
        <li><code>[if-multi-day]&hellip;[/if-multi-day]</code><span class="description"> - The event spans multiple days</span></li>
        <li><code>[if-single-day]&hellip;[/if-single-day]</code><span class="description"> - The event does not span multiple days</span></li>
    </ul>
    <h4>Attributes:</h4>
    <p class="description" style="margin-bottom:18px;">The possible attributes mentioned above are explained here:</p>
    <ul>
        <li><code>html</code><span class="description"> - Whether or not to parse HTML that has been entered in the relevant field. Can be <code>true</code> or <code>false</code></span></li>
        <li><code>markdown</code><span class="description"> - Whether or not to parse <a href="http://daringfireball.net/projects/markdown" target="_blank">Markdown</a> that has been entered in the relevant field. <a href="http://michelf.com/projects/php-markdown" target="_blank">PHP Markdown</a> must be installed for this to work. Can be <code>true</code> or <code>false</code></span></li>
        <li><code>limit</code><span class="description"> - The word limit for the field. Should be specified as a positive integer</span></li>
        <li><code>format</code><span class="description"> - The date / time format to use. Should specified as a <a href="http://php.net/manual/en/function.date.php" target="_blank">PHP date format</a> string</span></li>
        <li><code>newwindow</code><span class="description"> - Whether or not the link should open in a new window / tab. Can be <code>true</code> or <code>false</code></span></li>
        <li><code>precision</code><span class="description"> - How precise to be when displaying a time difference in human-readable format. Should be specified as a positive integer</span></li>
        <li><code>offset</code><span class="description"> - An offset (in seconds) to apply to start / end times before display. Should be specified as a (positive or negative) integer</span></li>
        <li><code>autolink</code><span class="description"> - Whether or not to automatically convert URLs in the description to links. Can be <code>true</code> or <code>false</code></span></li>
    </ul>
    <?php
}

//Simple display options
function gce_add_simple_display_main_text() {
    ?>
    <p class="gce-simple-display-options">You can use some HTML in the text fields, but ensure it is valid or things might go wonky. Text fields can be empty too.</p>
    <?php
}

function gce_add_display_start_field() {
    ?>
    <span class="description">Select how to display the start date / time.</span>
    <br />
    <select name="gce_options[display_start]">
        <option value="none">Don't display start time or date</option>
        <option value="time" selected="selected">Display start time</option>
        <option value="date">Display start date</option>
        <option value="time-date">Display start time and date (in that order)</option>
        <option value="date-time">Display start date and time (in that order)</option>
    </select>
    <br /><br />
    <span class="description">Text to display before the start time.</span>
    <br />
    <input type="text" name="gce_options[display_start_text]" value="Starts:" />
    <?php
}

function gce_add_display_end_field() {
    ?>
    <span class="description">Select how to display the end date / time.</span>
    <br />
    <select name="gce_options[display_end]">
        <option value="none">Don't display end time or date</option>
        <option value="time">Display end time</option>
        <option value="date">Display end date</option>
        <option value="time-date" selected="selected">Display end time and date (in that order)</option>
        <option value="date-time">Display end date and time (in that order)</option>
    </select>
    <br /><br />
    <span class="description">Text to display before the end time.</span>
    <br />
    <input type="text" name="gce_options[display_end_text]" value="Ends:" />
    <?php
}

function gce_add_display_separator_field() {
    ?>
    <span class="description">If you have chosen to display both the time and date above, enter the text / characters to display between the time and date here (including any spaces).</span>
    <br />
    <input type="text" name="gce_options[display_separator]" value=", " />
    <?php
}

function gce_add_display_location_field() {
    ?>
    <input type="checkbox" name="gce_options[display_location]" value="on" />
    <span class="description">Show the location of events?</span>
    <br /><br />
    <span class="description">Text to display before the location.</span>
    <br />
    <input type="text" name="gce_options[display_location_text]" value="Location:" />
    <?php
}

function gce_add_display_desc_field() {
    ?>
    <input type="checkbox" name="gce_options[display_desc]" value="on" />
    <span class="description">Show the description of events? (URLs in the description will be made into links).</span>
    <br /><br />
    <span class="description">Text to display before the description.</span>
    <br />
    <input type="text" name="gce_options[display_desc_text]" value="Description:" />
    <br /><br />
    <span class="description">Maximum number of words to show from description. Leave blank for no limit.</span>
    <br />
    <input type="text" name="gce_options[display_desc_limit]" size="3" />
    <?php
}

function gce_add_display_link_field() {
    ?>
    <input type="checkbox" name="gce_options[display_link]" value="on" checked="checked" />
    <span class="description">Show a link to the Google Calendar page for an event?</span>
    <br />
    <input type="checkbox" name="gce_options[display_link_target]" value="on" />
    <span class="description">Links open in a new window / tab?</span>
    <br /><br />
    <span class="description">The link text to be displayed.</span>
    <br />
    <input type="text" name="gce_options[display_link_text]" value="More details" />
    <?php
}