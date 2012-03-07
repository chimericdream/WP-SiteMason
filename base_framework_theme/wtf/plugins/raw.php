<?php
global $wtf_plaintext_shortcodes;
$wtf_plaintext_shortcodes[] = '\[raw\].*?\[\/raw\]';

function allow_raw_content($content)
{
    global $wtf_plaintext_shortcodes;
    $new_content = '';
	$pattern_full = '/(' . implode('|', $wtf_plaintext_shortcodes) . ')/is';
	$pattern_contents = '/\[(raw|geshi|sourcecode)\](.*?)\[\/\1\]/is';
	$pieces = preg_split($pattern_full, $content, -1, PREG_SPLIT_DELIM_CAPTURE);

	foreach ($pieces as $piece) {
		if (preg_match($pattern_contents, $piece, $matches)) {
			$new_content .= $matches[2];
		} else {
			$new_content .= wptexturize(wpautop($piece));
		}
	}

	return $new_content;
} //end allow_raw_content

remove_filter('the_content', 'wpautop');
remove_filter('the_content', 'wptexturize');
add_filter('the_content', 'allow_raw_content', 99);