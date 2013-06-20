<?php
function build_your_namespace_here_post_types()
{
//    register_post_type(
//        'post_type_name',
//        array(
//            'labels' => array(
//                'name' => 'Things',
//                'singular_name' => 'Thing',
//                'add_new' => 'Add New Thing',
//                'all_items' => 'All Things',
//                'add_new_item' => 'Add New Thing',
//                'edit_item' => 'Edit Thing',
//                'new_item' => '',
//                'view_item' => 'View',
//                'search_items' => '',
//                'not_found' => '',
//                'not_found_in_trash' => '',
//                'parent_item' => null,
//                'parent_item_colon' => null,
//                'menu_name' => 'Things',
//            ),
//            'description' => '',
//            'public' => true,
//            'publicly_queryable' => true,
//            'exclude_from_search' => false,
//            'show_ui' => true,
//            'show_in_menu' => true,
//            'menu_position' => 5,
//            'menu_icon' => null, //@todo: change this to a custom icon
//            'capability_type' => 'page',
//            'supports' => array(
//                'title',
//                'editor',
//                'author',
//                'thumbnail',
//                'revisions',
//            ),
//            'register_meta_box_cb' => 'add_post_type_name_meta_boxes',
//            'has_archive' => true,
//            'rewrite' => array(
//                'slug' => 'things',
//                'with_front' => false,
//            )
//        )
//    );
}

//function add_post_type_name_meta_boxes()
//{
//    add_meta_box(
//        'post_type_name_metabox', 'Member Information', 'post_type_name_html_box', 'post_type_name', 'advanced', 'high'
//    );
//}
//
//function post_type_name_html_box($post)
//{
//    // Use nonce for verification
//    wp_nonce_field(plugin_basename(__FILE__), 'post_type_name_meta_nonce');
//
//    $field1 = get_post_meta($post->ID, 'post_type_name_field1', true);
//    echo '<label for="post_type_name_field1">Select example</label><br />';
//    echo '<select id="post_type_name_field1" name="post_type_name_field1">';
//    echo '<option value="opt1"' . (($field1 == 'opt1') ? 'selected="selected"' : '') . '>opt1</option>';
//    echo '<option value="opt2"' . (($field1 == 'opt2') ? 'selected="selected"' : '') . '>opt2</option>';
//    echo '<option value="opt3"' . (($field1 == 'opt3') ? 'selected="selected"' : '') . '>opt3</option>';
//    echo '<option value="opt4"' . (($field1 == 'opt4') ? 'selected="selected"' : '') . '>opt4</option>';
//    echo '</select><br /><br />';
//
//    $field2 = get_post_meta($post->ID, 'post_type_name_field2', true);
//    echo '<label for="post_type_name_field2">Text example</label><br />';
//    echo '<input type="text" id="post_type_name_field2" name="post_type_name_field2" value="' . $field2 . '" size="25" /><br /><br />';
//}
//
//add_action('save_post', 'post_type_name_save_postdata');
//
//function post_type_name_save_postdata($post_id)
//{
//    // verify if this is an auto save routine.
//    // If it is our form has not been submitted, so we dont want to do anything
//    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
//        return;
//    }
//
//    // verify this came from the our screen and with proper authorization,
//    // because save_post can be triggered at other times
//    if (!wp_verify_nonce($_POST['post_type_name_meta_nonce'], plugin_basename(__FILE__))) {
//        return;
//    }
//
//    // Check permissions
//    if (!current_user_can('edit_page', $post_id)) {
//        return;
//    }
//
//    $actual_post_id = $_POST['ID'];
//
//    // OK, we're authenticated: we need to find and save the data
//    $field1 = $_POST['field1'];
//    $field2 = $_POST['field2'];
//    update_post_meta($actual_post_id, 'post_type_name_field1', $field1);
//    update_post_meta($actual_post_id, 'post_type_name_field2', $field2);
//}