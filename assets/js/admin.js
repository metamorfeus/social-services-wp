/**
 * Social Services Directory - Admin JS
 */
(function ($) {
    'use strict';

    // ── Photo Gallery ─────────────────────────────────────────────────────────

    function initPhotoGallery() {
        var $addBtn = $('#ssd-add-photos');
        var $list   = $('#ssd-photo-list');

        if (!$addBtn.length) return;

        $addBtn.on('click', function (e) {
            e.preventDefault();

            var frame = wp.media({
                title:    'Select Photos',
                button:   { text: 'Add to Gallery' },
                multiple: true
            });

            frame.on('select', function () {
                frame.state().get('selection').toJSON().forEach(function (att) {
                    uploadPhoto(att.id);
                });
            });

            frame.open();
        });

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
            }).done(function (res) {
                if (res.success) $item.remove();
            });
        });

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
        $.post(ssdAdmin.ajaxurl, {
            action:        'ssd_add_photo',
            nonce:         ssdAdmin.nonce,
            post_id:       $('#post_ID').val(),
            attachment_id: attachmentId
        }).done(function (res) {
            if (res.success) {
                $('#ssd-photo-list p').remove();
                $('#ssd-photo-list').append(res.data.html);
            }
        });
    }

    // ── CSV Import with progress ──────────────────────────────────────────────

    function initImportForm() {
        var $form        = $('#ssd-import-form');
        if (!$form.length) return;

        var $startBtn    = $('#ssd-start-import');
        var $formWrap    = $('#ssd-import-form-wrap');
        var $progressWrap = $('#ssd-import-progress-wrap');
        var $resultsWrap = $('#ssd-import-results-wrap');

        // Counters displayed during progress
        var totals = { created: 0, updated: 0, skipped: 0, errors: 0 };
        var allErrors = [];
        var allSkips  = [];

        $form.on('submit', function (e) {
            e.preventDefault();

            var fileInput = document.getElementById('csv_file');
            if (!fileInput.files.length) {
                alert('Please select a CSV file.');
                return;
            }

            var nonce      = $('#ssd-import-nonce').val();
            var batchSize  = parseInt($('#batch_size').val(), 10) || 50;
            var updateExisting = $('#update_existing').is(':checked') ? '1' : '';

            // Reset state
            totals     = { created: 0, updated: 0, skipped: 0, errors: 0 };
            allErrors  = [];
            allSkips   = [];
            updateCounters();

            // Hide form, show progress
            $formWrap.hide();
            $progressWrap.show();
            $resultsWrap.hide();
            setProgress(0, 0);
            setProgressText('Uploading file…');

            // Step 1: upload file and start session
            var startData = new FormData();
            startData.append('action',          'ssd_import_start');
            startData.append('nonce',           nonce);
            startData.append('csv_file',        fileInput.files[0]);
            startData.append('batch_size',      batchSize);
            startData.append('update_existing', updateExisting);

            $.ajax({
                url:         ajaxurl,
                type:        'POST',
                data:        startData,
                processData: false,
                contentType: false
            })
            .done(function (res) {
                if (!res.success) {
                    showFatalError(res.data.message || 'Upload failed.');
                    return;
                }
                var sessionId = res.data.session_id;
                var total     = res.data.total;
                setProgressText('Processing 0 of ' + total + ' rows…');
                processBatch(nonce, sessionId, 0, total);
            })
            .fail(function () {
                showFatalError('Could not contact the server. Check your network connection.');
            });
        });

        // ── Batch loop ────────────────────────────────────────────────────────

        function processBatch(nonce, sessionId, offset, total) {
            $.post(ajaxurl, {
                action:     'ssd_import_batch',
                nonce:      nonce,
                session_id: sessionId,
                offset:     offset
            })
            .done(function (res) {
                if (!res.success) {
                    showFatalError(res.data.message || 'Batch processing failed.');
                    return;
                }

                var d = res.data;

                // Accumulate totals
                totals.created += d.imported;
                totals.updated += d.updated;
                totals.skipped += d.skipped;
                totals.errors  += d.errors.length;

                allErrors = allErrors.concat(d.errors);
                allSkips  = allSkips.concat(d.skips || []);

                updateCounters();
                setProgress(d.offset, d.total);
                setProgressText(
                    'Processing ' + Math.min(d.offset, d.total) + ' of ' + d.total + ' rows…'
                );

                if (d.done) {
                    showResults(d.total);
                } else {
                    processBatch(nonce, sessionId, d.offset, d.total);
                }
            })
            .fail(function () {
                showFatalError('Network error while processing batch at offset ' + offset + '. The import may be incomplete.');
            });
        }

        // ── UI helpers ────────────────────────────────────────────────────────

        function setProgress(offset, total) {
            var pct = total > 0 ? Math.round((offset / total) * 100) : 0;
            $('#ssd-progress-bar').css('width', pct + '%').attr('aria-valuenow', pct);
        }

        function setProgressText(msg) {
            $('#ssd-progress-text').text(msg);
        }

        function updateCounters() {
            $('#cnt-created').text(totals.created);
            $('#cnt-updated').text(totals.updated);
            $('#cnt-skipped').text(totals.skipped);
            $('#cnt-errors').text(totals.errors);
        }

        function showFatalError(msg) {
            setProgressText('Import stopped: ' + msg);
            $progressWrap.find('.ssd-progress-bar-wrap').after(
                '<div class="notice notice-error inline" style="margin-top:12px;"><p>' +
                escHtml(msg) + '</p></div>'
            );
        }

        function showResults(totalProcessed) {
            setProgress(totalProcessed, totalProcessed);
            setProgressText('Import complete.');

            // Populate results table
            $('#res-total').text(totalProcessed);
            $('#res-created').text(totals.created);
            $('#res-updated').text(totals.updated);
            $('#res-skipped').text(totals.skipped);
            $('#res-errors').text(totals.errors);

            // Error list
            if (allErrors.length > 0) {
                var errorHtml = '';
                allErrors.forEach(function (e) {
                    errorHtml += '<div class="ssd-error-row">' +
                        '<span class="ssd-err-row-num">Row ' + escHtml(String(e.row)) + '</span>' +
                        (e.name ? ' <span class="ssd-err-name">' + escHtml(e.name) + '</span>' : '') +
                        ' — <span class="ssd-err-msg">' + escHtml(e.message) + '</span>' +
                        '</div>';
                });
                $('#ssd-error-list').html(errorHtml);
                $('#ssd-error-details').show();
            }

            // Skipped list
            if (allSkips.length > 0) {
                var skipHtml = '';
                allSkips.forEach(function (s) {
                    skipHtml += '<div class="ssd-error-row ssd-skip-row">' +
                        '<span class="ssd-err-row-num">Row ' + escHtml(String(s.row)) + '</span>' +
                        (s.name ? ' <span class="ssd-err-name">' + escHtml(s.name) + '</span>' : '') +
                        ' — <span class="ssd-err-msg">' + escHtml(s.message) + '</span>' +
                        '</div>';
                });
                $('#ssd-skipped-list').html(skipHtml);
                $('#ssd-skipped-details').show();
            }

            $resultsWrap.show();
        }

        function escHtml(str) {
            return $('<div>').text(str).html();
        }

        // "Import another file" resets the UI
        $('#ssd-import-again').on('click', function () {
            $form[0].reset();
            totals     = { created: 0, updated: 0, skipped: 0, errors: 0 };
            allErrors  = [];
            allSkips   = [];
            updateCounters();
            $progressWrap.find('.notice').remove();
            $('#ssd-error-details').hide();
            $('#ssd-skipped-details').hide();
            $resultsWrap.hide();
            $progressWrap.hide();
            $formWrap.show();
        });
    }

    // ── Init ─────────────────────────────────────────────────────────────────

    $(document).ready(function () {
        initPhotoGallery();
        initImportForm();
    });

}(jQuery));
