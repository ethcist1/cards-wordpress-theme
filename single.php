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

                <div class="post-header">
                    <h2 class="entry-title"><?php the_title(); ?></h2>
                    <div class="post-date"><?php echo get_the_date(); ?></div>
                </div>

                <?php
                // Show the featured image only if there's no video
                if ( ! $has_video ) {
                    if ( has_post_thumbnail() ) {
                        echo get_the_post_thumbnail( null, 'sparks-single-featured', array(
                            'class' => 'full-width-featured-image',
                            'loading' => 'eager',
                            'sizes' => '100vw',
                        ) );
                    } else {
                        // Fallback: use first image found in post content
                        $post_content = get_the_content();
                        if ( preg_match( '/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $post_content, $img_match ) ) {
                            echo '<img src="' . esc_url( $img_match[1] ) . '" class="full-width-featured-image" loading="eager" style="width:100%;height:auto;">';
                        }
                    }
                }
                ?>

                <div class="post-content">
                    <?php the_content(); ?>
                </div>

                <!-- Sharing icon -->
                <?php if (is_single()) : ?>
                    <div class="post-sharing">
                        <?php echo sparks_get_whatsapp_share_button(); ?>
                    </div>
                <?php endif; ?>

            </article><!-- #post-<?php the_ID(); ?> -->

            <?php $sparks_custom_code = get_option('sparks_custom_code', ''); ?>
            <?php if ($sparks_custom_code) : ?>
                <div class="sparks-custom-inject"><?php echo do_shortcode($sparks_custom_code); ?></div>
            <?php endif; ?>

            <?php if ( comments_open() || get_comments_number() ) : ?>
                <?php comments_template(); ?>
            <?php endif; ?>

        <?php endwhile; // End of the loop.
        ?>

    </main><!-- #main -->
    
    <?php get_sidebar(); ?>
</div><!-- #primary -->

<?php get_footer(); ?>

