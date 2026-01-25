<?php
/**
 * Main template file
 *
 * @package Sparks_Theme
 */

get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">
        <div class="custom-loop-container">
            <?php if (have_posts()) : ?>
                <?php while (have_posts()) : the_post(); ?>
                    <?php
                    $post_id = get_the_ID();
                    include(get_template_directory() . '/elements/post-card.php');
                    ?>
                <?php endwhile; ?>

                <!-- Pagination -->
                <div class="pagination-container">
                    <?php
                    the_posts_pagination(array(
                        'prev_text' => __('Previous', 'sparks-theme'),
                        'next_text' => __('Next', 'sparks-theme'),
                    ));
                    ?>
                </div>

            <?php else : ?>
                <p><?php esc_html_e('No content found.', 'sparks-theme'); ?></p>
            <?php endif; ?>
        </div>
    </main>

    <?php get_sidebar(); ?>
</div><!-- #primary -->

<?php get_footer(); ?>
