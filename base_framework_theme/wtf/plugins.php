<?php
if ((bool) get_option('wtf-plugins-google-calendar') == true) {
    require_once dirname(__FILE__) .'/plugins/gcal.php';
}
if ((bool) get_option('wtf-plugins-wp-calendar') == true) {
    require_once dirname(__FILE__) .'/plugins/calendar.php';
}
if ((bool) get_option('wtf-plugins-collapse-arch') == true) {
    require_once dirname(__FILE__) .'/plugins/collapse-archives.php';
}
if ((bool) get_option('wtf-plugins-twitter') == true) {
    require_once dirname(__FILE__) .'/plugins/twitter.php';
}