(function ($) {
    $(document).ready(function () {
        $('#custom_button').on('change', function () {
            $('.lukio_custom_button_wrapper, .lukio_custom_images_wrapper').toggleClass('hide_option');
        });

        $('.set_custom_images').on('click', function (e) {
            e.preventDefault();
            let btn = $(this);
            let input = btn.siblings('.process_custom_images');
            let img_prev = btn.siblings('.preview_image');
            var image_frame;
            if (image_frame) {
                image_frame.open();
            }
            // Define image_frame as wp.media object
            image_frame = wp.media({
                title: '',
                multiple: false,
                library: {
                    type: 'image',
                }
            });

            image_frame.on('close', function () {
                // On close, get selections and save to the hidden input
                // plus other AJAX stuff to refresh the image preview
                var selection = image_frame.state().get('selection');
                var gallery_ids = new Array();
                var my_index = 0;
                selection.each(function (attachment) {
                    gallery_ids[my_index] = attachment['id'];
                    my_index++;
                });
                var ids = gallery_ids.join(",");
                if (ids.length === 0) return true;//if closed withput selecting an image
                input.val(ids);
                if (ids != '0') {
                    Refresh_Image(ids, img_prev);
                }
            });

            image_frame.on('open', function () {
                // On open, get the id from the hidden input
                // and select the appropiate images in the media manager
                var selection = image_frame.state().get('selection');
                var ids = input.val().split(',');
                ids.forEach(function (id) {
                    var attachment = wp.media.attachment(id);
                    attachment.fetch();
                    selection.add(attachment ? [attachment] : []);
                });

            });

            image_frame.open();
        });

        function Refresh_Image(the_id, preview) {
            var data = {
                action: 'lukio_favorites_get_preview_img',
                id: the_id
            };

            $.get(ajaxurl, data, function (response) {

                if (response.success === true) {
                    preview.replaceWith(response.data.image);
                }
            });
        }

        $('.lukio_color_picker').wpColorPicker({
            defaultColor: $('#lukio_default_color').val(),
            change: function (event, ui) {
                $('.lukio_favorites_button[data-lukio-fav="0"] .lukio_pre_fav,.lukio_favorites_button[data-lukio-fav="1"] .lukio_fav').css('fill', ui.color.toString());
            },
        });

    })
})(jQuery)