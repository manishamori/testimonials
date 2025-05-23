<?php

add_filter('rest_prepare_testimonial', 'customize_testimonial_rest_response', 10, 3);

function customize_testimonial_rest_response($response, $post, $request) {
  
    $status = get_post_meta($post->ID, 'testimonial_status', true);

   
    if ($status !== 'approved') {
        return new WP_Error('testimonial_not_approved', 'Testimonial is not approved.', ['status' => 403]);
    }

   
    $data = [
        'title' => get_the_title($post),
        'testimonial_content' => get_post_meta($post->ID, 'testimonial_content', true),
    ];

   
    $response= $data;
     
    return $response;
}
add_action('rest_testimonial_query', function ($args, $request) {
    $args['meta_query'] = [
        [
            'key' => 'testimonial_status',
            'value' => 'approved',
            'compare' => '='
        ]
    ];
    return $args;
}, 10, 2);




add_action('rest_api_init', function () {
    register_rest_route('custom/v1', '/testimonials', [
        'methods' => 'POST',
        'callback' => 'rest_testimonial_submit',
        'permission_callback' => '__return_true', 
        'args' => [
            'name' => [
                'required' => true,
                'sanitize_callback' => 'sanitize_text_field',
            ],
            'content' => [
                'required' => true,
                'sanitize_callback' => 'sanitize_textarea_field',
            ],
        ],
    ]);
});

function rest_testimonial_submit(WP_REST_Request $request) {
    $name = $request->get_param('name');
    $content = $request->get_param('content');
    $emailID = $request->get_param('emailID');
    $rating = $request->get_param('rating');

   
    if (empty($name) || !$emailID || empty($content)) {
        return new WP_Error('missing_fields', 'Please fill in all required fields.', ['status' => 422]);
    }
   
    if ($rating < 1 || $rating > 5) {
        wp_send_json_error('Please provide a valid rating between 1 and 5.');
    }
   
    $post_id = wp_insert_post([
        'post_type' => 'testimonial',
        'post_title' => $name,
        'post_status' => 'publish',
    ]);

    if (is_wp_error($post_id)) {
        return new WP_Error('insert_failed', 'Failed to submit testimonial.', ['status' => 500]);
    }

    if (isset($emailID)){ update_post_meta($post_id, 'emailID', sanitize_email($emailID));}
    if (isset($content)){  update_post_meta($post_id, 'testimonial_content', wp_kses_post($content));}
    
    if (isset($rating)) {
        $rating = intval($rating);
        if ($rating >= 1 && $rating <= 5) {
            update_post_meta($post_id, 'testimonial_rating', $rating);
        }
    }
    update_post_meta($post_id, 'testimonial_status', 'pending');
  
    return [
        'success' => true,
        'message' => 'Thank you! Your testimonial has been submitted and is pending approval.',
        'testimonial_id' => $post_id,
    ];
}

?>
