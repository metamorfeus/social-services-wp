/**
 * Social Services Directory - Admin JS
 */
(function ($) {
    'use strict';

    // ── Photo Gallery ─────────────────────────────────────────────────────────

    function initPhotoGallery() {
        var $addBtn  = $('#ssd-add-photos');
        var $list    = $('#ssd-photo-list');

        if (!$addBtn.length) return;

        // Open media uploader
        $addBtn.on('click', function (e) {
            e.preventDefault();

            var frame = wp.media({
                title:    'Select Photos',
                button:   { text: 'Add to Gallery' },
                multiple: true
            });

            frame.on('select', function () {
                var attachments = frame.state().get('selection').toJSON();

                attachments.forEach(function (att) {
                    uploadPhoto(att.id);
                });
            });

            frame.open();
        });

        // Remove photo
        $(document).on('click', '.ssd-remove-photo', function (e) {
            e.preventDefault();
            var $item   = $(this).closest('.ssd-photo-item');
            var photoId = $item.data('photo-id');
            var postId  = $('#post_ID').val();

            if (!confirm('Remove this photo?')) return;

            $.post(ssdAdmin.ajaxurl, {
                action:   'ssd_remove_photo',
                nonce:    ssdAdmin.nonce,
                photo_id: photoId,
                post_id:  postId
            })
            .done(function (res) {
                if (res.success) $item.remove();
            });
        });

        // Sortable
        if ($.fn.sortable) {
            $list.sortable({
                items:  '.ssd-photo-item',
                update: function () {
                    var order = [];
                    $list.find('.ssd-photo-item').each(function (i) {
                        order.push({ id: $(this).data('photo-id'), pos: i });
                    });
                    $.post(ssdAdmin.ajaxurl, {
                        action: 'ssd_reorder_photos',
                        nonce:  ssdAdmin.nonce,
                        order:  JSON.stringify(order)
                    });
                }
            });
        }
    }

    function uploadPhoto(attachmentId) {
        var postId = $('#post_ID').val();

        $.post(ssdAdmin.ajaxurl, {
            action:        'ssd_add_photo',
            nonce:         ssdAdmin.nonce,
            post_id:       postId,
            attachment_id: attachmentId
        })
        .done(function (res) {
            if (res.success) {
                $('#ssd-photo-list p').remove(); // remove "No photos" message
                $('#ssd-photo-list').append(res.data.html);
            }
        });
    }

    // ── Import progress feedback ───────────────────────────────────────────────

    function initImportForm() {
        var $form = $('form[action*="ssd_import_csv"]');
        if (!$form.length) return;

        $form.on('submit', function () {
            var $btn = $form.find('[type="submit"]');
            $btn.val('Importing...').prop('disabled', true);
        });
    }

    // ── Init ─────────────────────────────────────────────────────────────────

    $(document).ready(function () {
        initPhotoGallery();
        initImportForm();
    });

}(jQuery));
