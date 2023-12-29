<?php
/*
Template Name: Single Post Template
*/

get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">

        <?php
        // Custom query to retrieve the single post
        $post_id = get_the_ID();
        $custom_query = new WP_Query(array(
            'post_type' => 'post',  // Specify the post type
            'p' => $post_id,  // Display the specific post
        ));

        if ($custom_query->have_posts()) :
            echo '<div class="custom-loop-container">';

            while ($custom_query->have_posts()) : $custom_query->the_post();
                ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class('custom-loop-post'); ?>>
                    <div class="entry-content">
                        <?php
                        // Display the featured image if available
                        if (has_post_thumbnail()) {
                            echo '<div class="featured-image">' . get_the_post_thumbnail() . '</div>';
                        }
                        ?>

                        <h2 class="entry-title"><?php the_title(); ?></h2>
                        <div class="post-content"><?php the_content(); ?></div>
                    </div>
                </article>
            <?php
            endwhile;

            echo '</div>'; // Close the custom-loop-container

            wp_reset_postdata(); // Reset the post data to the main query
        else :
            // If no posts are found
            echo 'No posts found';

        endif;
        ?>

    </main><!-- #main -->
</div><!-- #primary -->

<?php get_footer(); ?>