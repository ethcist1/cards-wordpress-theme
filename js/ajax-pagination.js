jQuery(document).ready(function($) {
    $(document).on('click', '.pagination-container a', function(e) {
        e.preventDefault();

        // Extract page number from URL
        var href = $(this).attr('href');
        var page = 1;

        // Try different URL patterns
        if (href.indexOf('page=') !== -1) {
            page = href.split('page=')[1];
        } else if (href.indexOf('/page/') !== -1) {
            var matches = href.match(/\/page\/(\d+)/);
            if (matches && matches[1]) {
                page = matches[1];
            }
        }

        var container = $('.custom-loop-container');
        var queryVars = container.data('query');

        // Add loading indicator
        container.addClass('loading').css('opacity', '0.5');

        $.ajax({
            url: sparksAjaxPagination.ajaxurl, // Now properly defined
            type: 'post',
            data: {
                action: 'sparks_ajax_pagination',
                nonce: sparksAjaxPagination.nonce, // Security nonce
                query_vars: queryVars,
                page: page
            },
            success: function(response) {
                if (response.success && response.data.html) {
                    container.html(response.data.html);

                    // Scroll to top of container
                    $('html, body').animate({
                        scrollTop: container.offset().top - 100
                    }, 300);
                } else {
                    console.error('AJAX pagination error:', response);
                    alert('Failed to load posts. Please refresh the page.');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', status, error);
                alert('Failed to load posts. Please refresh the page.');
            },
            complete: function() {
                container.removeClass('loading').css('opacity', '1');
            }
        });
    });
});
