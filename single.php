<?php
/**
 * The template for displaying all single posts
 */

get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">

        <?php
        while (have_posts()) : the_post();
            // Check for video in the content using helper
            $has_video = sparks_has_video();
            ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class('custom-loop-post'); ?>>

                <?php
                // Show the featured image only if there's no video
                if (!$has_video && has_post_thumbnail()) {
                    echo '<div class="image-featured-image">';
                    echo get_the_post_thumbnail(null, 'sparks-single-featured', array(
                        'loading' => 'eager', // Above fold, load immediately
                        'sizes' => '(max-width: 700px) 100vw, 700px',
                    ));
                    echo '</div>';
                }
                ?>

                <div class="post-content">
                    <?php the_content(); ?>
                </div>

                <div class="post-footer">
                    <h2 class="entry-title"><?php the_title(); ?></h2>
                    <div class="post-date"><?php echo get_the_date(); ?></div>
                </div>

                <!-- Sharing icon -->
                <?php if (is_single()) : ?>
                    <div class="post-sharing">
                        <?php echo sparks_get_whatsapp_share_button(); ?>
                    </div>
                <?php endif; ?>

            </article><!-- #post-<?php the_ID(); ?> -->

        <?php endwhile; // End of the loop.
        ?>

    </main><!-- #main -->
    
    <?php get_sidebar(); ?>
</div><!-- #primary -->

<?php get_footer(); ?>

