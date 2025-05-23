<?php /* 
Plugin Name: Testimonials
Plugin URI: Testimonials
Description: testimTestimonialsonials
Version: 1.0 
Author: manisha mori
Author URI: manishamcald@gmail.com
 
*/

if (!defined('ABSPATH')) exit;

require_once plugin_dir_path(__FILE__) . 'include/testimonial-frontend.php';
require_once plugin_dir_path(__FILE__) . 'include/rest-api.php';
function register_testimonial() {
    register_post_type('testimonial', [
        'labels' => [
            'name' => 'Testimonials',
            'singular_name' => 'Testimonial',
            
        ],
        'public' => false,
        'show_ui' => true,
        'supports' => ['title'],
        'capability_type' => 'post',
        'publicly_queryable' => true, 
        'show_in_rest' => true,   
    ]);
}
add_action('init', 'register_testimonial');

add_action('add_meta_boxes', 'testimonial_meta');
function testimonial_meta() {
    add_meta_box('testimonial_fields', 'Testimonial Details', 'testimonial_field', 'testimonial', 'normal', 'high');
}
function testimonial_field($post) {
   
    $emailID = get_post_meta($post->ID, 'emailID', true);
    $content = get_post_meta($post->ID, 'testimonial_content', true);
    $status = get_post_meta($post->ID, 'testimonial_status', true);
    $rating = get_post_meta($post->ID, 'testimonial_rating', true);
    ?>
  
    <p><label>Email : <input type="email" name="emailID" value="<?php echo $emailID; ?>"></label></p>
    <p><label>Testimonial Content:<br>
        <textarea name="testimonial_content" rows="5" cols="50"><?php echo $content; ?></textarea></label></p>
    <p><label>Status:
        <select name="testimonial_status">
            <option value="pending" <?php selected($status, 'pending'); ?>>Pending</option>
            <option value="approved" <?php selected($status, 'approved'); ?>>Approved</option>
            <option value="rejected" <?php selected($status, 'rejected'); ?>>Rejected</option>
        </select>
    </label></p>
    <p><label>Rating:
        <select name="testimonial_rating">
            <option value="1" <?php selected($rating, '1'); ?>>★ 1</option>
            <option value="2" <?php selected($rating, '2'); ?>>★★ 2</option>
            <option value="3" <?php selected($rating, '3'); ?>>★★★ 3</option>
            <option value="4" <?php selected($rating, '4'); ?>>★★★★ 4</option>
            <option value="5" <?php selected($rating, '5'); ?>>★★★★★ 5</option>
        </select>
    </label></p>
    <?php
}

// Save Meta Fields
function save_testimonial($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (isset($_POST['emailID'])){ update_post_meta($post_id, 'emailID', sanitize_email($_POST['emailID']));}
    if (array_key_exists('testimonial_content', $_POST)) {
        update_post_meta($post_id, 'testimonial_content', sanitize_textarea_field($_POST['testimonial_content']));
    }
    if (isset($_POST['testimonial_status'])){ update_post_meta($post_id, 'testimonial_status', sanitize_text_field($_POST['testimonial_status'])); }
    if (isset($_POST['testimonial_rating'])) {
        $rating = intval($_POST['testimonial_rating']);
        if ($rating >= 1 && $rating <= 5) {
            update_post_meta($post_id, 'testimonial_rating', $rating);
        }
    }
}
add_action('save_post', 'save_testimonial');


// Add custom columns to the testimonial list table
function testimonial_column($columns) {
    $columns['emailID'] = 'Email';
    $columns['testimonial_status'] = 'Status';
    return $columns;
}
add_filter('manage_testimonial_posts_columns', 'testimonial_column');

// Render the values for the custom columns
function testimonial_custom_column_content($column, $post_id) {
    switch ($column) {
        case 'emailID':
            echo esc_html(get_post_meta($post_id, 'emailID', true));
            break;
        case 'testimonial_status':
            $status = get_post_meta($post_id, 'testimonial_status', true);
            echo esc_html(ucfirst($status));
            echo "<div class='hidden testimonial-status' data-id='{$post_id}' data-status='{$status}'></div>";
            break;
    }
}
add_action('manage_testimonial_posts_custom_column', 'testimonial_custom_column_content', 10, 2);






//email notification to admin

function send_email_on_testimonial_submit($post_id, $post, $update) {
    if ($post->post_type != 'testimonial' || $update) return;
    
  
    $email = get_post_meta($post_id, 'emailID', true);
    $content = get_post_meta($post_id, 'testimonial_content', true);
    $to=get_option('admin_email');

    $message = "A new testimonial has been submitted:\n\n
                Name: $post->post_title \n
                Email: $email\n
                Content:\n$content";
    wp_mail($to, 'New Testimonial Submitted', $message);
}
add_action('wp_insert_post', 'send_email_on_testimonial_submit', 10, 3);


//quick edit status
function testimonial_quick_edit($column_name, $post_type) {
    if ($column_name !== 'testimonial_status' || $post_type !== 'testimonial') return;

    ?>
    <fieldset class="inline-edit-col-right">
        <div class="inline-edit-col">
            <label class="inline-edit-group">
                <span class="title">Testimonial Status</span>
                <select name="testimonial_status">
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                </select>
            </label>
        </div>
    </fieldset>
    <?php
}
add_action('quick_edit_custom_box', 'testimonial_quick_edit', 10, 2);



add_action('admin_footer', function() {
    global $typenow;
    if ($typenow !== 'testimonial') return;
    ?>
    <script>
    jQuery(document).ready(function($) {
        let $qe = inlineEditPost.edit;

        inlineEditPost.edit = function(postId) {
            $qe.apply(this, arguments);

            let id = typeof(postId) === 'object' ? $(postId).closest('tr').attr('id').replace("post-", "") : postId;

            let status = $('.testimonial-status[data-id="' + id + '"]').data('status');

            if (status) {
                $('select[name="testimonial_status"]', '.inline-edit-row').val(status);
            }
        };
    });
    </script>
    <?php
});









 

    