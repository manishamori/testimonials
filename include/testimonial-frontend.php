<?php

if (!defined('ABSPATH')) exit;

function enqueue_bootstrap_for_testimonials() {
    wp_enqueue_style('bootstrap-css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css');
    wp_enqueue_script('bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js', [], null, true);
}
add_action('wp_enqueue_scripts', 'enqueue_bootstrap_for_testimonials');



function testimonial_frontend_form_shortcode() {
    ob_start();

    ?>
     <div class="container my-4">
        <h2 class="mb-4 text-center">Submit Your Testimonial</h2>
        <div id="testimonial-message"></div>

        <form id="testimonial-form" class="container my-4" method="post" action="<?php echo admin_url('admin-ajax.php'); ?>">
            <div class="mb-3">
                <label for="name" class="form-label">Name *</label>
                <input type="text" name="name" class="form-control" id="name" required>
            </div>

            <div class="mb-3">
                <label for="emailID" class="form-label">Email *</label>
                <input type="email" name="emailID" class="form-control" id="emailID" required>
            </div>

            <div class="mb-3">
                <label for="testimonial_content" class="form-label">Your Testimonial *</label>
                <textarea name="testimonial_content" class="form-control" id="testimonial_content" rows="5" required></textarea>
            </div>

            <?php wp_nonce_field('submit_testimonial', 'testimonial_form_nonce'); ?>

            <div class="mb-3">
                <label for="rating" class="form-label">Rating *</label>
                <select name="rating" id="rating" class="form-select" required>
                    <option value="">Select rating</option>
                    <option value="1">★ 1</option>
                    <option value="2">★★ 2</option>
                    <option value="3">★★★ 3</option>
                    <option value="4">★★★★ 4</option>
                    <option value="5">★★★★★ 5</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Submit Testimonial</button>
        </form>
    </div>

    <?php
    
    wp_enqueue_script('testimonial-ajax', plugin_dir_url(__FILE__) . 'testimonial-ajax.js', ['jquery'], null, true);

    wp_localize_script('testimonial-ajax', 'testimonialAjax', [
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce'   => wp_create_nonce('submit_testimonial'),
    ]);

    return ob_get_clean();
}
add_shortcode('testimonial_form', 'testimonial_frontend_form_shortcode');






function testimonial_ajax_submit() {
    check_ajax_referer('submit_testimonial', 'nonce');

    $name = sanitize_text_field($_POST['name'] ?? '');
    $email = sanitize_email($_POST['emailID'] ?? '');
    $testimonial_content = sanitize_textarea_field($_POST['testimonial_content'] ?? '');
    $rating = intval($_POST['rating'] ?? 0);



    if (!$name || !$email || !$testimonial_content) {
        wp_send_json_error('Please fill in all required fields.');
    }
    if ($rating < 1 || $rating > 5) {
        wp_send_json_error('Please provide a valid rating between 1 and 5.');
    }

    $post_id = wp_insert_post([
        'post_title'  => $name,
        'post_type'   => 'testimonial',
        'post_status' => 'publish', 
    ]);

    if (!$post_id) {
        wp_send_json_error('Submission failed. Please try again.');
    }


    update_post_meta($post_id, 'testimonial_rating', $rating);
    update_post_meta($post_id, 'emailID', $email);
    update_post_meta($post_id, 'testimonial_content', $testimonial_content);
    update_post_meta($post_id, 'testimonial_status', 'pending');

    wp_send_json_success('Thank you! Your testimonial has been submitted and is pending approval.');
}
add_action('wp_ajax_submit_testimonial', 'testimonial_ajax_submit');
add_action('wp_ajax_nopriv_submit_testimonial', 'testimonial_ajax_submit');



function display_stars($rating) {
    $stars = '';
    for ($i = 1; $i <= 5; $i++) {
        $stars .= $i <= $rating ? '★' : '☆';
    }
    return '<div class="testimonial-rating text-warning fs-4">' . $stars . '</div>';
}
function testimonial_list_shortcode($atts) {
    ob_start();

    $query = new WP_Query([
        'post_type' => 'testimonial',
        'posts_per_page' => 5,
        'meta_key' => 'testimonial_status',
        'meta_value' => 'approved',
        'post_status' => 'publish',
    ]);

    if ($query->have_posts()) :
        ?>
         <h2 class="text-center my-4">What People Are Saying</h2>
        <div id="testimonialslide" class="carousel slide my-4" data-bs-ride="carousel">
            <div class="carousel-inner">
                <?php
                $i = 0;
                while ($query->have_posts()) : $query->the_post();
                    $content = get_post_meta(get_the_ID(), 'testimonial_content', true);
                    $name = esc_html(get_the_title());
                    $rating = intval(get_post_meta(get_the_ID(), 'testimonial_rating', true));
                    
                    ?>
                    <div class="carousel-item <?php echo ($i === 0) ? 'active' : ''; ?>">
                        <div class="p-3 p-md-5 bg-light text-dark text-center">
                            <blockquote class="blockquote mb-0">
                                <p class="fs-6 fs-md-5">"<?php echo $content; ?>"</p>
                                <?php echo display_stars($rating); ?>
                                <footer class="blockquote-footer mt-2"><?php echo $name; ?></footer>
                            </blockquote>
                        </div>
                    </div>
                <?php $i++; endwhile; ?>
            </div>

            <button class="carousel-control-prev position-absolute top-50 translate-middle-y bg-dark rounded-circle d-flex align-items-center justify-content-center" type="button" data-bs-target="#testimonialslide" data-bs-slide="prev" style="width: 40px; height: 40px; left: -20px;border: 2px solid #212529;">
                <span class="carousel-control-prev-icon" aria-hidden="true" style="transform: scale(1);"></span>
                <span class="visually-hidden">Previous</span>
            </button>

            <button class="carousel-control-next position-absolute top-50 translate-middle-y bg-dark rounded-circle d-flex align-items-center justify-content-center" type="button" data-bs-target="#testimonialslide" data-bs-slide="next" style="width: 40px; height: 40px; right: -20px;border: 2px solid #212529;">
                <span class="carousel-control-next-icon" aria-hidden="true" style="transform: scale(1);"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>

    <?php else :
        echo '<p class="text-center my-4">No testimonials found.</p>';
    endif;

    wp_reset_postdata();
    return ob_get_clean();
}
add_shortcode('testimonial_list', 'testimonial_list_shortcode');




