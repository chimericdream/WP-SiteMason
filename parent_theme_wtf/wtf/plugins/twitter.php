<?php
require_once dirname(__FILE__) . '/twitter/admin.php';

add_action('widgets_init', 'loadWtfTwitterWidget');

function loadWtfTwitterWidget()
{
    register_widget('WTF_Twitter_Widget');
    if ((bool) get_option('wtf-twitter-use-css') == true) {
        wp_register_style('wtf-twitter-widget', WTF_URI . '/widgets/twitter/style.css');
        wp_enqueue_style('wtf-twitter-widget');
    }
} //end loadWtfTwitterWidget

class WTF_Twitter_Widget extends WP_Widget
{

    public $id_base = 'wtf-twitter-widget';
    public $name = 'Recent Tweets';

    /**
     * Constructor
     *
     * Set up the widget.
     */
    public function WTF_Twitter_Widget()
    {
        /* Widget settings. */
        $options = array(
            'classname' => $this->id_base,
            'description' => __('A simple Twitter widget to display recent tweets in the sidebar.', $this->id_base)
        );

        /* Widget control settings. */
        $controls = array(
            'id_base' => $this->id_base
        );

        parent::WP_Widget($this->id_base, __($this->name, $this->id_base), $options, $controls);
    }

    /**
     * Displays the widget settings controls on the widget panel.
     * Make use of the get_field_id() and get_field_name() function
     * when creating your form elements. This handles the confusing stuff.
     */
    public function form($instance)
    {
        $default_account = get_option('wtf-twitter-default-account', 'youraccount');
        $tweet_limit     = get_option('wtf-twitter-tweet-limit', '5');
        /* Set up some default widget settings. */
        $defaults = array(
            'title' => __($this->name, $this->id_base),
            'account' => $default_account,
            'limit' => $tweet_limit,
        );
        $instance = wp_parse_args((array) $instance, $defaults);
        ?>

        <!-- Widget Title: Text Input -->
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', $this->id_base); ?></label>
            <input id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $instance['title']; ?>" />
        </p>

        <!-- Twitter Account Name: Text Input -->
        <p>
            <label for="<?php echo $this->get_field_id('account'); ?>"><?php _e('Account:', $this->id_base); ?></label>
            <input id="<?php echo $this->get_field_id('account'); ?>" name="<?php echo $this->get_field_name('account'); ?>" value="<?php echo $instance['account']; ?>" />
        </p>

        <!-- Tweet Limit: Text Input -->
        <p>
            <label for="<?php echo $this->get_field_id('limit'); ?>"><?php _e('Tweets:', $this->id_base); ?></label>
            <input id="<?php echo $this->get_field_id('limit'); ?>" name="<?php echo $this->get_field_name('limit'); ?>" value="<?php echo $instance['limit']; ?>" />
        </p>

        <?php
    }

    /**
     * How to display the widget on the screen.
     */
    public function widget($args, $instance)
    {
        extract($args);

        /* Our variables from the widget settings. */
        $title = apply_filters('widget_title', $instance['title']);
        $account = $instance['account'];
        $limit = $instance['limit'];

        /* Before widget (defined by themes). */
        echo $before_widget;

        echo '<div class="' . $this->id_base . '">';
        if ($title) {
            echo $before_title . $title . $after_title;
        }
        echo '<p>' . __('Recent tweets for', $this->id_base) . ' <a href="https://twitter.com/#!/' . $account . '">@' . $account . '</a></p>';
        echo $this->getTwitterFeed($account, $limit);
        echo '</div>';

        /* After widget (defined by themes). */
        echo $after_widget;
    }

    /**
     * Update the widget settings.
     */
    public function update($new_instance, $old_instance)
    {
        $instance = $old_instance;

        /* Strip tags for title and name to remove HTML (important for text inputs). */
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['account'] = strip_tags($new_instance['account']);
        $instance['limit'] = strip_tags($new_instance['limit']);

        return $instance;
    }

    private function getTwitterFeed($account, $limit)
    {
        $key = 'wtf_twitter_feed' . $account;

        // Let's see if we have a cached version
        $tweetlist = get_transient($key);
        if ($tweetlist !== false) {
            return $tweetlist;
        }

        //initialize a new curl resource
        $ch = curl_init();

        //Fetch the timeline
        $url = 'http://api.twitter.com/1/statuses/user_timeline.json?screen_name=' . $account . '&count=' . $limit;
        curl_setopt($ch, CURLOPT_URL, $url);

        //do not return the header information
        curl_setopt($ch, CURLOPT_HEADER, 0);

        //Give me the data back as a string... Don't echo it.
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        //Warp 9, Engage!
        $timeline_json = curl_exec($ch);

        //Close CURL connection & free the used memory.
        curl_close($ch);

        $feed = array();
        $timeline = json_decode($timeline_json, true);
        if (isset($timeline['error'])) {
            return '<span class="error">There was an error retrieving the Twitter timeline.</span>';
        } else {
            foreach ($timeline as $key => $message) {
                $text = $message['text'];
                // Process links in the message
                $text = preg_replace(
                    '@(https?://([-\w\.]+)+(/([\w/_\.]*(\?\S+)?(#\S+)?)?)?)@',
                    '<a href="$1">$1</a>',
                    $text
                );

                // Process Twitter usernames in the message
                $text = preg_replace(
                    '/@(\w+)/',
                    '<a href="https://twitter.com/$1">@$1</a>',
                    $text
                );

                // Process hash tags in the message
                $text = preg_replace(
                    '/\s+#(\w+)/',
                    ' <a href="https://search.twitter.com/search?q=%23$1">#$1</a>',
                    $text
                );

                $time = new DateTime($message['created_at']);
                $tz = new DateTimeZone(get_option('timezone_string'));
                $time->setTimezone($tz);

                // converting feed to more clear and friendly format
                $feed[] = array(
                    'username' => $message['user']['screen_name'],
                    'id' => $message['id_str'],
                    'text' => $text,
                    'time' => $time->format('M j, Y \a\t g:i a'),
                );
            }
        }

        $tweetlist = '';
        foreach ($feed as $tweet) {
            $class = ($class == '') ? ' alt' : '';
            $tweetlist .= '<div class="tweet' . $class . '">';
            $tweetlist .= '<span class="text">' . $tweet['text'] . '</span> ';
            $tweetlist .= '<span class="time">Posted on: <a href="https://twitter.com/#!/' . $account . '/status/' . $tweet['id'] . '">' . $tweet['time'] . '</a></span>';
            $tweetlist .= '</div>';
        }

        $time_limit = 60 * (int) get_option('wtf-twitter-time-limit', '15');
        // Cache the tweet list for 15 minutes
        set_transient($key, $tweetlist, $time_limit);

        return $tweetlist;
    }
}