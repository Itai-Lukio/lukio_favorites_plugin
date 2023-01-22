(function ($) {
    $(document).ready(function () {
        $('.lukio_favorirs_options_tab').on('click', function () {
            let tab = $(this);
            if (tab.hasClass('active')) {
                return;
            }
            let new_tab_index = tab.data('tab');
            $('.lukio_favorirs_options_tab.active, .lukio_favorirs_options_tab_content.active').removeClass('active');
            $(`.lukio_favorirs_options_tab[data-tab="${new_tab_index}"], .lukio_favorirs_options_tab_content[data-tab="${new_tab_index}"]`).addClass('active');
        });

        $('#custom_button').on('change', function () {
            $('.lukio_custom_button_wrapper, .lukio_custom_images_wrapper, .button_content').toggleClass('hide_option');
        });

        $('.lukio_set_custom_images').on('click', function (e) {
            e.preventDefault();
            let btn = $(this);
            let input = btn.siblings('.lukio_process_custom_images');
            let img_prev = btn.siblings('.preview_image');
            let image_frame;
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
                let selection = image_frame.state().get('selection');
                let gallery_ids = new Array();
                let my_index = 0;
                selection.each(function (attachment) {
                    gallery_ids[my_index] = attachment['id'];
                    my_index++;
                });
                let ids = gallery_ids.join(",");
                if (ids.length === 0) return true;//if closed withput selecting an image
                input.val(ids);
                if (ids != '0') {
                    let favorite_image = input.attr('name') == 'lukio_favorites[custom_button_on]';
                    Refresh_Image(ids, img_prev, favorite_image);
                }
            });

            image_frame.on('open', function () {
                // On open, get the id from the hidden input
                // and select the appropiate images in the media manager
                let selection = image_frame.state().get('selection');
                let ids = input.val().split(',');
                ids.forEach(function (id) {
                    let attachment = wp.media.attachment(id);
                    attachment.fetch();
                    selection.add(attachment ? [attachment] : []);
                });

            });

            image_frame.open();
        });

        function Refresh_Image(the_id, preview, favorite_preview_state) {
            $.ajax({
                method: 'GET',
                url: lukio_favorites_ajax.ajax_url,
                data: {
                    action: 'lukio_favorites_get_preview_img',
                    id: the_id
                },
                success: function (response) {
                    if (response) {
                        response = JSON.parse(response);
                        if (response.success === true) {
                            preview.replaceWith(response.image);
                            $(`.lukio_favorites_button[data-lukio-fav="${favorite_preview_state ? 1 : 0}"] img`).attr('src', response.image_src);
                        }
                    }
                }
            })
        }

        // create the color picker and update the svg fill on change
        $('.lukio_color_picker').wpColorPicker({
            defaultColor: $('#lukio_default_color').val(),
            change: function (event, ui) {
                $('.lukio_favorites_button[data-lukio-fav="0"] .lukio_pre_fav,.lukio_favorites_button[data-lukio-fav="1"] .lukio_fav').css('fill', ui.color.toString());
            },
        });

        // update the button size
        $('.lukio_edit_button_size').on('change', function () {
            let input = $(this);
            let css_attr = input.attr('id') == 'button_width' ? 'width' : 'height';
            $('.lukio_favorites_button').css(css_attr, input.val() + 'px');
        });

        // change the shown svg when the select was changed
        $('.lukio_svg_picker').on('change', function () {
            let svg_index = $(this).val();
            $('.button_content_svg').addClass('hide_option');
            $(`.hide_option[data-index="${svg_index}"]`).removeClass('hide_option');
        });
    })
})(jQuery)