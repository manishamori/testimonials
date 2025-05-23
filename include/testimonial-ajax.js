jQuery(document).ready(function($) {
    $('#testimonial-form').on('submit', function(e) {
        e.preventDefault();

        let $form = $(this);
        let $msg = $('#testimonial-message');
        $msg.removeClass().text('');


        $.ajax({
            url: testimonialAjax.ajaxurl,
            method: 'POST',
            data: {
                action: 'submit_testimonial',
                nonce: testimonialAjax.nonce,
                name: $form.find('input[name="name"]').val(),
                emailID: $form.find('input[name="emailID"]').val(),
                testimonial_content: $form.find('textarea[name="testimonial_content"]').val(),
                rating: $form.find('select[name="rating"]').val()
            },
            success: function(response) {
                if (response.success) {
                    $msg.addClass('alert alert-success').text(response.data);
                    $form[0].reset();
                } else {
                    $msg.addClass('alert alert-danger').text(response.data);
                }
            },
            error: function() {
                $msg.addClass('alert alert-danger').text('An unexpected error occurred.');
            }
        });
    });
});
