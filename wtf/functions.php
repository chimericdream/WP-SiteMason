<?php

/**********************************/
/*  WTF Options Methods
/**********************************/
function wtf_rss_url()
{
	echo get_option('wtf_rss_url');
}

function wtf_rss_email()
{
	echo get_option('wtf_rss_email');
}

function wtf_tracking_code()
{
	echo get_option('wtf_tracking_code');
}

function wtf_navigation($page_options = 'title_li=', $cat_options = 'show_count=0&title_li=')
{
	if(get_option('wtf_top_nav') == 'cats'){
		wp_list_categories($cat_options);
	} else {
		wp_list_pages($page_options); 
	}
}

function wtf_custom_css()
{
	if(get_option('wtf_custom_css') != ''){
		echo '<link rel="stylesheet" href="'. get_bloginfo('template_url') .'/'. get_option('wtf_custom_css') .'" type="text/css" media="screen" />';
	}
}

function wtf_exclude_rss_cats($query) 
{
	if ($query->is_feed && get_option('wtf_exclude_rss_cats') != '') {
		$query->set('cat', get_option('wtf_exclude_rss_cats'));
	}

	return $query;
}
add_filter('pre_get_posts','wtf_exclude_rss_cats');

/****************************************/
/*  WTF Extended Functionality Methods
/****************************************/
function wtf_breadcrumbs()
{
	global $wp_query;
 
	if ( !is_home() ){
 
		// Start the UL
		echo '<ul class="breadcrumbs">';
		// Add the Home link
		echo '<li><a href="'. get_settings('home') .'">'. get_bloginfo('name') .'</a></li>';
 
		if ( is_category() ) 
		{
			$catTitle = single_cat_title( "", false );
			$cat = get_cat_ID( $catTitle );
			echo "<li> &raquo; ". get_category_parents( $cat, TRUE, " &raquo; " ) ."</li>";
		}
		elseif ( is_archive() && !is_category() ) 
		{
			echo "<li> &raquo; Archives</li>";
		}
		elseif ( is_search() ) {
 
			echo "<li> &raquo; Search Results</li>";
		}
		elseif ( is_404() ) 
		{
			echo "<li> &raquo; 404 Not Found</li>";
		}
		elseif ( is_single() ) 
		{
			$category = get_the_category();
			$category_id = get_cat_ID( $category[0]->cat_name );
 
			echo '<li> &raquo; '. get_category_parents( $category_id, TRUE, " &raquo; " );
			echo the_title('','', FALSE) ."</li>";
		}
		elseif ( is_page() ) 
		{
			$post = $wp_query->get_queried_object();
 
			if ( $post->post_parent == 0 ){
 
				echo "<li> &raquo; ".the_title('','', FALSE)."</li>";
 
			} else {
				$title = the_title('','', FALSE);
				$ancestors = array_reverse( get_post_ancestors( $post->ID ) );
				array_push($ancestors, $post->ID);
				
				foreach ( $ancestors as $ancestor ){
					if( $ancestor != end($ancestors) ){
						echo '<li> &raquo; <a href="'. get_permalink($ancestor) .'">'. strip_tags( apply_filters( 'single_post_title', get_the_title( $ancestor ) ) ) .'</a></li>';
					} else {
						echo '<li> &raquo; '. strip_tags( apply_filters( 'single_post_title', get_the_title( $ancestor ) ) ) .'</li>';
					}
				}
			}
		}
 
		// End the UL
		echo "</ul>";
	}
}

function wtf_popular_posts($showposts = 5)
{
	global $wpdb;
	
	echo '<ul class="popular_posts">';
	$result = $wpdb->get_results("SELECT comment_count,ID,post_title FROM $wpdb->posts ORDER BY comment_count DESC LIMIT 0 , ". $showposts);
	foreach ($result as $post) {
		setup_postdata($post);
		$postid = $post->ID;
		$title = $post->post_title;
		$commentcount = $post->comment_count;
		if ($commentcount != 0) {
			echo '<li><a href="'. get_permalink($postid) .'" title="'. $title .'">'. $title .'</a></li>';
		} 
	} 
	echo '</ul>';
}

//for use in the loop and only works with tags
function wtf_related_posts($showposts = 5)
{
	global $post;
	$tags = wp_get_post_tags($post->ID);
	if($tags) {
		$first_tag = $tags[0]->term_id;
		$args=array(
			'tag__in' => array($first_tag),
			'post__not_in' => array($post->ID),
			'showposts'=>$showposts,
			'caller_get_posts'=>1
		);
		
		$my_query = new WP_Query($args);
		if($my_query->have_posts()) {
			echo '<ul class="related_posts">';
			while ($my_query->have_posts()) { 
				$my_query->the_post();
				echo '<li><a href="'. get_permalink() .'" title="Permanent Link to '. the_title_attribute('echo=0') .'">'. get_the_title() .'</a></li>';
			}
			echo '</ul>';
		}
	}
}

function wtf_future_posts($showposts = 5, $date_format = 'jS F Y')
{
	query_posts('showposts='. $showposts .'&post_status=future'); 
	if ( have_posts() ){
		echo '<ul class="future_posts">';
		while ( have_posts() ){
			the_post(); 

			echo '<li>'. get_the_title() .' <span class="future_date">'. get_the_time($date_format) .'</span></li>';
		}
		echo '</ul>';
	}
}

function wtf_pings_count($post_id) 
{
	global $wpdb;
	$count = "SELECT COUNT(*) FROM $wpdb->comments WHERE (comment_type = 'pingback' OR comment_type = 'trackback') AND comment_post_ID = '$post_id'";
	return $wpdb->get_var($count);
}

function wtf_tiny_url($url) 
{
	$dataUrl = 'http://tinyurl.com/api-create.php?url=' . $url;
	$tinyurl = wtf_api_call($dataUrl);
	if($tinyurl != false){
		return $tinyurl;
	} else {
		return $url;
	}
}

function wtf_feedburner_count($feedburner_id) 
{
	$url = "https://feedburner.google.com/api/awareness/1.0/GetFeedData?uri=". $feedburner_id;
	$data = wtf_api_call($url);
	if($data != false){
		try {
			$xml = new SimpleXMLElement($data);
			$count = $xml->feed->entry['circulation'];
		} catch (Exception $e) {
			return '0';
		}
		return $count;
	}
	return '0';
}

function wtf_latest_tweet($twitter_id)
{
	$url = "http://search.twitter.com/search.atom?q=from:" . $twitter_id . "&rpp=1";
	$feed = wtf_api_call($url);

	$stepOne = explode("<content type=\"html\">", $feed);
	$stepTwo = explode("</content>", $stepOne[1]);
	$tweet = $stepTwo[0];

	return htmlspecialchars_decode($tweet);
}

//cURL helper method
function wtf_api_call($url) 
{
	if (function_exists('curl_init')) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $url);
		$data = curl_exec($ch);
		$error = curl_error($ch);
		curl_close($ch);
		if($error){
		    return $error;
		}
		return $data;
	} else {
		//cURL disabled on server
		return false;
	}
}

?>