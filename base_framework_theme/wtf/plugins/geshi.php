<?php
require_once dirname(__FILE__) . '/geshi/geshi.php';

global $wtf_plaintext_shortcodes;
$wtf_plaintext_shortcodes[] = '\[geshi(?:[^\]]+)?\].*?\[\/geshi\]';
$wtf_plaintext_shortcodes[] = '\[sourcecode(?:[^\]]+)?\].*?\[\/sourcecode\]';

function geshi_shortcode($atts, $content = '') {
    extract(shortcode_atts(array(
        'lang'  => null,
        'force' => false,
    ), $atts));

    if (is_null($lang)) {
        return 'You must specify a language for the source code highlighting to work.';
    }

    $hash = md5('lang', $content);
    if (!$force) {
        $cached = get_transient('geshi_' . $hash);
        if (false !== $cached) {
            return $cached;
        }
    } else {
        delete_transient('geshi_' . $hash);
    }

    $geshi = new GeSHi($content, $lang);
    $content = $geshi->parse_code();

    // We don't want to store something in a permanent location, but source code
    // isn't something that changes, so cache it for a long time.
    set_transient('geshi_' . $hash, $content, 60*60*24*365);

    return $content;
} //end geshi_shortcode

add_shortcode('sourcecode', 'geshi_shortcode');
add_shortcode('geshi', 'geshi_shortcode');