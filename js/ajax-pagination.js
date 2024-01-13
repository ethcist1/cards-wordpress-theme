jQuery(document).ready(function($) {
    $(document).on('click', '.pagination-container a', function(e) {
        e.preventDefault();
        var page = $(this).attr('href').split('page=')[1];
        var container = $('.custom-loop-container');
        var queryVars = container.data('query');

        $.ajax({
            url: ajaxpagination.ajaxurl,
            type: 'post',
            data: {
                action: 'ajax_pagination',
                query_vars: queryVars,
                page: page
            },
            success: function(html) {
                container.html(html);
            }
        });
    });
});