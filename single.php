<?php
/**
 * The template for displaying all single posts
 */

get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">

        <?php
        while (have_posts()) : the_post();
            // Check for video in the content
            $post_content = get_the_content();
            $has_video = (has_shortcode($post_content, 'video') || stripos($post_content, '.mp4') !== false);
            ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class('custom-loop-post'); ?>>

                <?php
                // Show the featured image only if there's no video
                if (!$has_video && has_post_thumbnail()) {
                    echo '<div class="image-featured-image">' . get_the_post_thumbnail(null, 'large') . '</div>';
                }
                ?>

                <div class="post-content">
                    <?php the_content(); ?>
                </div>

                <div class="post-footer">
                    <h2 class="entry-title"><?php the_title(); ?></h2>
                    <div class="post-date"><?php echo get_the_date(); ?></div>
                </div>

            </article><!-- #post-<?php the_ID(); ?> -->

        <?php endwhile; // End of the loop.
        ?>

    </main><!-- #main -->
    
    <?php get_sidebar(); ?>
</div><!-- #primary -->

<?php get_footer(); ?>

