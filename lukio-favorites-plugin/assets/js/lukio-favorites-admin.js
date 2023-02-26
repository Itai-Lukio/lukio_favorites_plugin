jQuery(document).ready(function ($) {
    // switch option tabs
    $('.lukio_favorirs_options_tab').on('click', function () {
        let tab = $(this);
        if (tab.hasClass('active')) {
            return;
        }
        let new_tab_index = tab.data('tab');
        $('.lukio_favorirs_options_tab.active, .lukio_favorirs_options_tab_content.active').removeClass('active');
        $(`.lukio_favorirs_options_tab[data-tab="${new_tab_index}"], .lukio_favorirs_options_tab_content[data-tab="${new_tab_index}"]`).addClass('active');
    });

    // switch from text button options to image button options
    $('#text_button').on('change', function () {
        $('.lukio_favorirs_text_button_wrapper, .lukio_favorirs_image_button_wrapper').toggleClass('hide_option');
    });

    // switch from svg button options to custom image button options
    $('#custom_button').on('change', function () {
        $('.lukio_favorirs_custom_button_wrapper, .lukio_favorirs_custom_images_wrapper, .lukio_favorirs_button_content').toggleClass('hide_option');
    });

    // set up the image picking
    $('.lukio_favorirs_set_custom_images').on('click', function (e) {
        e.preventDefault();
        let btn = $(this),
            input = btn.siblings('.lukio_favorirs_process_custom_images'),
            image_preview = btn.siblings('.lukio_favorirs_custom_image_preview'),
            // Define image_frame as wp.media object
            image_frame = wp.media({
                title: btn.data('popup_title'),
                multiple: false,
                library: {
                    type: 'image',
                }
            });

        image_frame
            // get the id from the input and select the appropiate image in the media manager
            .on('open', function () {
                let selection = image_frame.state().get('selection'),
                    id = input.val(),
                    attachment = wp.media.attachment(id);
                attachment.fetch();
                selection.add(attachment ? [attachment] : []);
            })
            // get the id from the media manager update the input and the preview image
            .on('close', function () {
                let selection = image_frame.state().get('selection');
                if (selection.models.length == 0) {
                    // when no image is selected
                    return;
                }

                // get the selected image id from the media manager
                let id = selection.models[0]['id'];

                if (input.val() == id) {
                    // return when the selected image wasn't changed
                    return;
                }

                input.val(id);
                let favorite_image = input.attr('id') == 'custom_button_on';
                refresh_preview_images(id, image_preview, favorite_image);
            });

        image_frame.open();
    });

    /**
     * get the new preview image src and update the previews
     * 
     * @param {string} image_id id of the image to get
     * @param {jQuery} preview_image jQuery object of the preview image
     * @param {bool} favorite_preview_state true to update added image, false for not added
     */
    function refresh_preview_images(image_id, preview_image, favorite_preview_state) {
        $.ajax({
            method: 'GET',
            url: lukio_favorites_ajax.ajax_url,
            data: {
                action: 'lukio_favorites_get_preview_img',
                id: image_id
            },
            success: function (response) {
                if (response) {
                    response = JSON.parse(response);
                    if (response.success === true) {
                        preview_image.attr('src', response.image_src);
                        $(`.lukio_favorites_button[data-lukio-fav="${favorite_preview_state ? 1 : 0}"] img`).attr('src', response.image_src);
                    }
                }
            }
        })
    }

    // create the color picker and update the svg fill on change
    $('.lukio_favorirs_color_picker').wpColorPicker({
        defaultColor: $('#lukio_default_color').val(),
        change: function (event, ui) {
            $('.lukio_favorites_button[data-lukio-fav="0"] .lukio_pre_fav,.lukio_favorites_button[data-lukio-fav="1"] .lukio_fav').css('fill', ui.color.toString());
        },
    });

    // update the button size
    $('.lukio_favorirs_edit_button_size')
        .on('change', function () {
            let input = $(this);
            let css_attr = input.attr('id') == 'button_width' ? 'width' : 'height';
            $('.lukio_favorites_button').css(css_attr, input.val() + 'px');
        })
        .on('input', function () {
            let input = $(this);
            input.val(input.val().replace(/[^0-9]/g, ""));
        })
        .on('keydown', function (e) {
            if (e.key == 'Enter') {
                e.preventDefault();
                $(this).trigger('change');
            }
        })

    // change the shown svg when the select was changed
    $('.lukio_favorirs_svg_picker').on('change', function () {
        let svg_index = $(this).val();
        $('.lukio_favorirs_button_content_svg').addClass('hide_option');
        $(`.hide_option[data-index="${svg_index}"]`).removeClass('hide_option');
    });
})