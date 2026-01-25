<article id="post-<?php echo $post_id; ?>" <?php post_class('custom-loop-post'); ?>>
    <div class="entry-content">
        <!-- Post date and title -->
        <div class="post-text">
            <div class="post-date"><?php echo get_the_date(); ?></div>
            <h2 class="entry-title"><a href="<?php echo esc_url(get_permalink()); ?>"><?php the_title(); ?></a></h2>
        </div>

        <!-- Slider wrapper with media and sharing -->
        <?php sparks_render_post_media($post_id, true); ?>
    </div>
</article>
