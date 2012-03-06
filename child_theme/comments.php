<?php
if (!empty($_SERVER['SCRIPT_FILENAME']) && 'comments.php' == basename($_SERVER['SCRIPT_FILENAME'])) {
    die ('Please do not load this page directly. Thanks!');
}

if (post_password_required()) {
    echo 'This post is password protected. Enter the password to view comments.';
    return;
}

if (have_comments() || comments_open()) {
    echo '<section role="comments">';
}

if (have_comments()) {
    echo '    <h3>';
    comments_number('No Responses', 'One Response', '% Responses' );
    echo '</h3>';
    echo '    <nav role="comment-nav">';
    echo '        <span class="next">';
    previous_comments_link();
    echo '</span>';
    echo '        <span class="prev">';
    next_comments_link();
    echo '</span>';
    echo '    </nav>';
    echo '    <ol class="commentlist">';
    wp_list_comments();
    echo '    </ol>';
    echo '    <nav role="comment-nav">';
    echo '        <span class="next">';
    previous_comments_link();
    echo '</span>';
    echo '        <span class="prev">';
    next_comments_link();
    echo '</span>';
    echo '    </nav>';
} else {
    if (comments_open()) {
        // Comments are open but there are no comments.
        echo '    <h3>';
        comments_number('No Responses', 'One Response', '% Responses' );
        echo '</h3>';
    } else {
        // Comments are closed and there are no comments
        // echo '    <p>Comments are closed.</p>';
    }
}
if (comments_open()) {
    echo '    <section id="comment-form">';
    echo '        <h4>';
    comment_form_title('Leave a Reply', 'Leave a Reply to %s');
    echo '</h4>';
    echo '        <span class="cancel-comment-reply"><?php cancel_comment_reply_link(); ?></span>';

    if (get_option('comment_registration') && !is_user_logged_in()) {
        echo '        <p>You must be <a href="' . wp_login_url(get_permalink()) . '">logged in</a> to post a comment.</p>';
    } else {
        echo '        <form action="' . get_option('siteurl') . '/wp-comments-post.php" method="post">';
        echo '            <fieldset>';
        if (is_user_logged_in()) {
            echo '                    <p>Logged in as <a href="' . get_option('siteurl') . '/wp-admin/profile.php">' . $user_identity . '</a>. <a href="' . wp_logout_url(get_permalink()) . '" title="Log out of this account">Log out &raquo;</a></p>';
        } else {
            echo '                    <label for="author">Name';
            if ($req) {
                echo " (required)";
            }
            echo '</label>';
            echo '                    <input type="text" name="author" id="author" value="' . esc_attr($comment_author) . '" size="22" tabindex="1"';
            if ($req) {
                echo ' required="required"';
            }
            echo ' />';
            echo '                    <label for="email">Mail (will not be published)';
            if ($req) {
                echo " (required)";
            }
            echo '</label>';
            echo '                    <input type="email" name="email" id="email" value="' . esc_attr($comment_author_email) . '" size="22" tabindex="2"';
            if ($req) {
                echo ' required="required"';
            }
            echo ' />';
            echo '                    <label for="url">Website</label>';
            echo '                    <input type="url" name="url" id="url" value="' . esc_attr($comment_author_url) . '" size="22" tabindex="3" />';
        }
        //echo '                <p>You can use these tags: <code>' . allowed_tags() . '</code></p>';
        echo '                <textarea name="comment" id="comment" cols="58" rows="10" tabindex="4"></textarea>';
        echo '                <input name="submit" type="submit" id="submit" tabindex="5" value="Submit Comment" />';
        comment_id_fields();
        do_action('comment_form', $post->ID);
        echo '            </fieldset>';
        echo '        </form>';
    } // If registration required and not logged in
    echo '    </section>';
}

if (have_comments() || comments_open()) {
    echo '</section>';
}