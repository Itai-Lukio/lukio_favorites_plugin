jQuery(document).ready(function ($) {
    /**
     * add or update the url query with the new param and value
     * 
     * @param {string} param url param to update
     * @param {string} newval param new value
     * @param {string} search url query
     * @returns {string} updated url query
     * 
     * @author Itai Dotan
     */
    function replace_query_param(param, newval, search) {
        let regex = new RegExp("([?;&])(" + param + "[^&;]*[;&]?)"),
            query = search.replace(regex, "$1").replace(/[?&]$/, '');

        return query + (newval ? (query.length > 0 ? "&" : "?") + param + "=" + newval : '');
    }

    // switch option tabs
    $('.lukio_favorites_options_tab').on('click', function () {
        let tab = $(this);
        if (tab.hasClass('active')) {
            return;
        }
        let new_tab_index = tab.data('tab');
        $('.lukio_favorites_options_tab.active, .lukio_favorites_options_tab_content.active').removeClass('active');
        $(`.lukio_favorites_options_tab[data-tab="${new_tab_index}"], .lukio_favorites_options_tab_content[data-tab="${new_tab_index}"]`).addClass('active');

        window.history.replaceState({}, "", window.location.pathname + replace_query_param('tab', new_tab_index, window.location.search));
    });

    // toggle between the switch 2 display options if there are any
    $('.lukio_favorites_switch_input').on('change', function () {
        $(`[data-toggle="${$(this).attr('name')}"]`).toggleClass('hide_option');
    });

    // set up the image picking
    $('.lukio_favorites_set_custom_images').on('click', function (e) {
        e.preventDefault();
        let btn = $(this),
            input = btn.siblings('.lukio_favorites_process_custom_images'),
            image_preview = btn.siblings('.lukio_favorites_custom_image_preview'),
            // Define image_frame as wp.media object
            image_frame = wp.media({
                title: btn.data('popup-title'),
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
                let favorite_image = input.attr('id').includes('_on');
                refresh_preview_images(id, image_preview, favorite_image);
            });

        image_frame.open();
    });

    /**
     * get the new preview image src and update the previews
     * 
     * @param {string} image_id id of the image to get
     * @param {jQuery} preview_image jQuery object of the preview image
     * @param {bool} preview_added true to update added image, false for not added
     * 
     * @author Itai Dotan
     */
    function refresh_preview_images(image_id, preview_image, preview_added) {
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
                        preview_image.closest('.lukio_favorites_options_tab_content').find(`.lukio_favorites_default_btn_wrapper [data-lukio-fav="${preview_added ? 1 : 0}"] img`).attr('src', response.image_src);
                    }
                }
            }
        })
    }

    // create the color picker and update the svg fill on change
    $('.lukio_favorites_color_picker').wpColorPicker({
        defaultColor: $('#lukio_default_color').val(),
        change: function (event, ui) {
            let tab = $(event.target).closest('.lukio_favorites_options_tab_content');

            tab.find('[data-lukio-fav="0"] .lukio_favorites_unmarked,[data-lukio-fav="1"] .lukio_favorites_marked').css('fill', ui.color.toString());
        },
    });

    // update the button size
    $('.lukio_favorites_edit_button_size')
        .on('change', function () {
            let input = $(this),
                css_attr = input.attr('id').includes('width') ? 'width' : 'height',
                tab = $(this).closest('.lukio_favorites_options_tab_content');

            tab.find('.preview_button').css(css_attr, input.val() + 'px');
        })
        .on('input', function () {
            $(this).trigger('change');
        })
        .on('keydown', function (e) {
            if (e.key == 'Enter') {
                e.preventDefault();
            }
        });

    $('.lukio_favorites_text_input').on('keydown', function (e) {
        if (e.key == 'Enter') {
            e.preventDefault();
        }
    })

    // change the shown svg when the select was changed
    $('.lukio_favorites_svg_picker').on('change', function () {
        let input = $(this),
            svg_index = input.val(),
            tab = $(this).closest('.lukio_favorites_options_tab_content');

        tab.find('.lukio_favorites_button_content_svg').addClass('hide_option');
        tab.find(`.lukio_favorites_button_content_svg[data-index="${svg_index}"]`).removeClass('hide_option');
    });
})