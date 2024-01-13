<?php
/**
 * Template Name: Paginated Cards
 */

get_header();

// Control the number of posts per page
$posts_per_page = 10; // You can adjust this number as needed

// Determine the correct page number
$paged = get_query_var('paged') ? get_query_var('paged') : (get_query_var('page') ? get_query_var('page') : 1);


// Custom query with the correct 'paged' parameter
$custom_query = new WP_Query(array(
    'post_type'      => 'post',
    'posts_per_page' => $posts_per_page,
    'paged'          => $paged,
));

?>


<div id="primary" class="content-area">
    <main id="main" class="site-main">
        <?php if ($custom_query->have_posts()) : ?>
            <div class="custom-loop-container">
                       <?php while ($custom_query->have_posts()) : $custom_query->the_post(); 
            $post_id = get_the_ID(); // Pass the current post ID to the component
            include(get_template_directory() . '/elements/post-card.php'); // Include the post card component
        endwhile; ?>
                
            </div> <!-- Close the custom-loop-container -->

            <!-- Pagination -->
            <div class="pagination-container">
                <?php
                echo paginate_links(array(
                    'prev_text' => __('Previous', 'textdomain'),
                    'next_text' => __('Next', 'textdomain'),
                    'total'     => $custom_query->max_num_pages,
                    'current'   => $paged,
                ));
                ?>
            </div>

            <?php wp_reset_postdata(); ?>
        <?php else : ?>
            <p>No posts found.</p>
        <?php endif; ?>
    </main>
    <?php get_sidebar(); ?>
</div><!-- #primary -->


<?php get_footer(); ?>


