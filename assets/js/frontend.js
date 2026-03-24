/**
 * Social Services Directory - Frontend JS
 */
(function ($) {
    'use strict';

    var SSD = {

        init: function () {
            this.bindFilters();
            this.bindViewToggle();
            this.bindSort();
            this.bindLoadMore();
            this.bindFavorites();
            this.bindReviews();
        },

        // ── Filtering ────────────────────────────────────────────────────────

        bindFilters: function () {
            var $form = $('.ssd-filters-form');
            if (!$form.length) return;

            // Initialise Select2 on filter dropdowns if available
            if ($.fn.select2) {
                $form.find('select.ssd-filter-select').select2({ width: '100%' });
            }

            $form.on('submit', function (e) {
                e.preventDefault();
                SSD.applyFilters();
            });

            // Collapse / expand filters panel
            $('.ssd-toggle-filters').on('click', function () {
                $('.ssd-filters-content').slideToggle(200);
            });
        },

        applyFilters: function () {
            var $form     = $('.ssd-filters-form');
            var $search   = $('.ssd-search-form');
            var $container = $('#ssd-directory');

            var data = {
                action:       'ssd_filter_providers',
                nonce:        ssdAjax.nonce,
                municipality: $form.find('[name="municipality"]').val() || '',
                service:      $form.find('[name="service"]').val() || '',
                target_group: $form.find('[name="target_group"]').val() || '',
                min_rating:   $form.find('[name="rating"]').val() || 0,
                search:       $search.find('[name="search"]').val() || '',
                orderby:      $container.data('orderby') || 'title',
                order:        $container.data('order') || 'ASC',
                paged:        1
            };

            SSD.showLoading();

            $.post(ssdAjax.ajaxurl, data)
                .done(function (res) {
                    if (res.success) {
                        $('.ssd-providers-grid').html(res.data.html);
                        SSD.updateResultsCount(res.data.found);
                        SSD.updateLoadMore(res.data.current_page, res.data.max_pages);
                        $container.data('paged', 1)
                                  .data('max-pages', res.data.max_pages);
                    } else {
                        $('.ssd-providers-grid').html(
                            '<div class="ssd-no-results"><p>' + res.data.message + '</p></div>'
                        );
                    }
                })
                .fail(function () {
                    alert(ssdAjax.strings.error);
                })
                .always(function () {
                    SSD.hideLoading();
                });
        },

        // ── View toggle (grid / list) ────────────────────────────────────────

        bindViewToggle: function () {
            $(document).on('click', '.ssd-view-toggle', function () {
                var view = $(this).data('view');
                $('.ssd-view-toggle').removeClass('active');
                $(this).addClass('active');
                $('.ssd-providers-container').attr('data-view', view);
                localStorage.setItem('ssd_view', view);
            });

            // Restore saved view
            var saved = localStorage.getItem('ssd_view');
            if (saved) {
                $('.ssd-view-toggle[data-view="' + saved + '"]').trigger('click');
            }
        },

        // ── Sort ─────────────────────────────────────────────────────────────

        bindSort: function () {
            $(document).on('change', '.ssd-sort-select', function () {
                var val    = $(this).val().split('-');
                var field  = val[0];
                var order  = val[1] ? val[1].toUpperCase() : 'ASC';
                var map    = { name: 'title', rating: 'meta_value_num', newest: 'date' };
                var orderby = map[field] || 'title';
                $('#ssd-directory').data('orderby', orderby).data('order', order);
                SSD.applyFilters();
            });
        },

        // ── Load more ────────────────────────────────────────────────────────

        bindLoadMore: function () {
            $(document).on('click', '#ssd-load-more', function () {
                var $btn  = $(this);
                var $dir  = $('#ssd-directory');
                var paged = parseInt($dir.data('paged') || 1) + 1;

                $btn.prop('disabled', true).text(ssdAjax.strings.loading);

                $.post(ssdAjax.ajaxurl, {
                    action: 'ssd_load_more',
                    nonce:  ssdAjax.nonce,
                    paged:  paged
                })
                .done(function (res) {
                    if (res.success) {
                        $('.ssd-providers-grid').append(res.data.html);
                        $dir.data('paged', paged);
                        if (!res.data.has_more) {
                            $btn.hide();
                        } else {
                            $btn.prop('disabled', false)
                                .text($btn.data('original-text'));
                        }
                    }
                })
                .fail(function () {
                    $btn.prop('disabled', false)
                        .text($btn.data('original-text'));
                });
            });

            // Store original button text
            var $btn = $('#ssd-load-more');
            if ($btn.length) $btn.data('original-text', $btn.text());
        },

        // ── Favourites ────────────────────────────────────────────────────────

        bindFavorites: function () {
            $(document).on('click', '.ssd-favorite-btn', function (e) {
                e.preventDefault();
                var $btn = $(this);
                var id   = $btn.data('provider-id');

                $.post(ssdAjax.ajaxurl, {
                    action:      'ssd_toggle_favorite',
                    nonce:       ssdAjax.nonce,
                    provider_id: id
                })
                .done(function (res) {
                    if (res.success) {
                        $btn.toggleClass('active', res.data.action === 'added');
                    }
                });
            });
        },

        // ── Reviews ───────────────────────────────────────────────────────────

        bindReviews: function () {
            // Open review modal
            $(document).on('click', '.ssd-write-review-btn', function () {
                var id = $(this).data('provider-id');
                SSD.openReviewModal(id);
            });

            // Star rating — click to select
            $(document).on('click', '.ssd-star-input', function () {
                var val = parseInt($(this).data('value'), 10);
                var labels = ['', '1 — Много лошо', '2 — Лошо', '3 — Средно', '4 — Добро', '5 — Отлично'];
                $('#ssd-review-modal input[name="rating"]').val(val);
                $('.ssd-star-input').each(function () {
                    $(this).toggleClass('selected', parseInt($(this).data('value'), 10) <= val);
                });
                $('.ssd-star-label').text(labels[val] || '');
            });

            // Star rating — hover preview
            $(document).on('mouseenter', '.ssd-star-input', function () {
                var val = parseInt($(this).data('value'), 10);
                $('.ssd-star-input').each(function () {
                    $(this).toggleClass('hovered', parseInt($(this).data('value'), 10) <= val);
                });
            }).on('mouseleave', '.ssd-star-input', function () {
                $('.ssd-star-input').removeClass('hovered');
            });

            // Submit review
            $(document).on('submit', '#ssd-review-form', function (e) {
                e.preventDefault();
                var $form = $(this);

                $.post(ssdAjax.ajaxurl, {
                    action:      'ssd_submit_review',
                    nonce:       ssdAjax.nonce,
                    provider_id: $form.find('[name="provider_id"]').val(),
                    rating:      $form.find('[name="rating"]').val(),
                    title:       $form.find('[name="title"]').val(),
                    review_text: $form.find('[name="review_text"]').val()
                })
                .done(function (res) {
                    if (res.success) {
                        $('#ssd-review-form').hide();
                        $('.ssd-review-notice-success').text(res.data.message).show();
                        setTimeout(function () {
                            $('#ssd-review-modal, #ssd-review-modal-overlay').fadeOut();
                        }, 2500);
                    } else {
                        $('.ssd-review-notice-error').text(res.data.message).show();
                    }
                });
            });

            // Close modal
            $(document).on('click', '.ssd-modal-close, #ssd-review-modal-overlay', function () {
                $('#ssd-review-modal').hide();
                $('#ssd-review-modal-overlay').hide();
            });
        },

        openReviewModal: function (providerId) {
            if (!$('#ssd-review-modal').length) {
                $('body').append(
                    '<div id="ssd-review-modal-overlay" class="ssd-modal-overlay"></div>' +
                    '<div id="ssd-review-modal" class="ssd-review-modal-box">' +
                        '<span class="ssd-modal-close">&times;</span>' +
                        '<h3 class="ssd-modal-title">Напишете отзив</h3>' +
                        '<div class="ssd-review-notice ssd-review-notice-error" style="display:none"></div>' +
                        '<div class="ssd-review-notice ssd-review-notice-success" style="display:none"></div>' +
                        '<form id="ssd-review-form">' +
                            '<input type="hidden" name="provider_id" value="">' +
                            '<input type="hidden" name="rating" value="0">' +
                            '<div class="ssd-modal-field">' +
                                '<label class="ssd-modal-label">Оценка <span class="ssd-required">*</span></label>' +
                                '<div class="ssd-star-row" role="group" aria-label="Рейтинг">' +
                                    '<span class="ssd-star-input" data-value="1" title="1 звезда">&#9733;</span>' +
                                    '<span class="ssd-star-input" data-value="2" title="2 звезди">&#9733;</span>' +
                                    '<span class="ssd-star-input" data-value="3" title="3 звезди">&#9733;</span>' +
                                    '<span class="ssd-star-input" data-value="4" title="4 звезди">&#9733;</span>' +
                                    '<span class="ssd-star-input" data-value="5" title="5 звезди">&#9733;</span>' +
                                    '<span class="ssd-star-label"></span>' +
                                '</div>' +
                            '</div>' +
                            '<div class="ssd-modal-field">' +
                                '<label class="ssd-modal-label">Заглавие <span class="ssd-required">*</span></label>' +
                                '<input type="text" name="title" class="ssd-modal-input" required>' +
                            '</div>' +
                            '<div class="ssd-modal-field">' +
                                '<label class="ssd-modal-label">Текст на отзива <span class="ssd-required">*</span></label>' +
                                '<textarea name="review_text" rows="4" class="ssd-modal-textarea" required></textarea>' +
                            '</div>' +
                            '<button type="submit" class="button button-primary ssd-modal-submit">Изпрати</button>' +
                        '</form>' +
                    '</div>'
                );
            }
            $('#ssd-review-modal input[name="provider_id"]').val(providerId);
            $('#ssd-review-modal input[name="rating"]').val(0);
            $('.ssd-star-input').removeClass('selected hovered');
            $('.ssd-star-label').text('');
            $('#ssd-review-form').show();
            $('.ssd-review-notice').hide().text('');
            $('#ssd-review-modal, #ssd-review-modal-overlay').show();
        },

        // ── Helpers ───────────────────────────────────────────────────────────

        showLoading: function () {
            if (!$('#ssd-loading-overlay').length) {
                $('.ssd-providers-container').append(
                    '<div id="ssd-loading-overlay" style="position:absolute;inset:0;background:rgba(255,255,255,.7);display:flex;align-items:center;justify-content:center;z-index:10">' +
                    '<span>' + ssdAjax.strings.loading + '</span></div>'
                );
                $('.ssd-providers-container').css('position', 'relative');
            }
        },

        hideLoading: function () {
            $('#ssd-loading-overlay').remove();
        },

        updateResultsCount: function (count) {
            $('.ssd-results-count strong:first').text(count);
            $('.ssd-results-count strong:last').text(count);
        },

        updateLoadMore: function (page, maxPages) {
            var $btn = $('#ssd-load-more');
            if (!$btn.length) return;
            $btn.toggle(page < maxPages).prop('disabled', false);
        }
    };

    $(document).ready(function () {
        if (typeof ssdAjax !== 'undefined') {
            SSD.init();
        }
    });

}(jQuery));
