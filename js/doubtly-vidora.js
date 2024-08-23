(function($) {
    'use strict';

    // Document ready
    $(function() {
        // Example: Add a click event to video items in the grid
        $('.video-item').on('click', function(e) {
            // Prevent default link behavior
            e.preventDefault();
            
            // Get the link URL
            var videoUrl = $(this).find('a').attr('href');
            
            // Redirect to the video page
            window.location.href = videoUrl;
        });

        // Example: Lazy load YouTube videos
        $('.wp-block-embed__wrapper').each(function() {
            var $wrapper = $(this);
            var $iframe = $wrapper.find('iframe');
            
            if ($iframe.length) {
                var src = $iframe.attr('src');
                $iframe.attr('data-src', src).removeAttr('src');
                
                $wrapper.on('click', function() {
                    $iframe.attr('src', $iframe.attr('data-src'));
                });
            }
        });
    });

})(jQuery);
