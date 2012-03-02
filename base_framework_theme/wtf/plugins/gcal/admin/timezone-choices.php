<?php 
function gce_get_timezone_choices($tzstring = ''){
    $content = '<select name="gce_options[timezone]">';
    $content .= wp_timezone_choice($tzstring);
    $content .= '</select>';
	return $content;
}