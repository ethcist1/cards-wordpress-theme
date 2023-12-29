<?php
/**
 * Template Name: Testing Pagination 
 */

get_header();

// Control the number of posts per page
$posts_per_page = 10; // You can adjust this number as needed

// Define custom query arguments
$args = array(
    'post_type'      => 'post',  // Specify the post type
    'posts_per_page' => $posts_per_page,
    'paged'          => get_query_var('paged') ? get_query_var('paged') : 1,  // For pagination
);

// Custom query
$custom_query = new WP_Query($args);

if ($custom_query->have_posts()) :
    echo '<div class="custom-loop-container">';

    while ($custom_query->have_posts()) : $custom_query->the_post();
    ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class('custom-loop-post'); ?>>
            <div class="entry-content">
                <h2 class="entry-title"><?php the_title(); ?></h2>

                <?php
                // Display the featured image
                if (has_post_thumbnail()) {
                    echo '<div class="video-featured-image">' . get_the_post_thumbnail(null, 'large') . '</div>';
                }
                ?>

                <div class="post-content"><?php the_content(); ?></div>
            </div>
        </article>
    <?php
    endwhile;

    echo '</div>'; // Close the custom-loop-container

    // Pagination
    echo '<div class="pagination-container">';
    echo paginate_links(array(
        'prev_text' => __('Previous', 'textdomain'),
        'next_text' => __('Next', 'textdomain'),
        'total'     => $custom_query->max_num_pages,
    ));
    echo '</div>';

    wp_reset_postdata(); // Reset the post data to the main query
else :
    // If no posts are found
    echo 'No posts found';

endif;

get_footer();
?>
