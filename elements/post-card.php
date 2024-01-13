<article id="post-<?php echo $post_id; ?>" <?php post_class('custom-loop-post'); ?>>
    <div class="entry-content">
        <!-- Post date and title -->
        <div class="post-text">
            <div class="post-date"><?php echo get_the_date(); ?></div>
            <h2 class="entry-title"><a href="<?php echo esc_url(get_permalink()); ?>"><?php the_title(); ?></a></h2>
        </div>

        <!-- Slider wrapper -->
        <div class="slider" id="slider-<?php echo $post_id; ?>">
            <?php
            // Get the post's content
            $post_content = get_the_content();

            // Try to extract all video shortcodes
            $has_videos = preg_match_all('/\[video.*?\]/', $post_content, $matches);

            // If videos are found, create a slide for each video
            if ($has_videos) {
                foreach ($matches[0] as $video_shortcode) {
                    echo '<div class="slide video-featured-image">';
                    echo do_shortcode($video_shortcode);
                    echo '</div>';
                }
            } elseif (has_post_thumbnail()) {
                // If no videos are found, but there is a featured image
                echo '<div class="slide image-featured">' . get_the_post_thumbnail(null, 'large') . '</div>';
            }
            ?>
        </div>
    </div>
</article>
