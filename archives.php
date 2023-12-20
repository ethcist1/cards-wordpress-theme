<?php
/**
 * Template Name: Cards Archive
 */


get_header();

// Control the number of posts per page
$posts_per_page = 10; // You can adjust this number as needed

// Custom query to retrieve posts
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$custom_query = new WP_Query(array(
    'post_type'      => 'post',  // Specify the post type
    'posts_per_page' => $posts_per_page,
    'paged'          => $paged,  // For pagination
));

if ($custom_query->have_posts()) :
    echo '<div class="custom-loop-container">';

    while ($custom_query->have_posts()) : $custom_query->the_post();
    ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class('custom-loop-post'); ?>>
            <div class="entry-content">
                <?php
                // Check if the post content contains video shortcode or .mp4 suffix
                $post_content = get_the_content();
                $has_video = (has_shortcode($post_content, 'video') || stripos($post_content, '.mp4') !== false);

                // Always display the article title
                echo '<h2 class="entry-title">' . get_the_title() . '</h2>';

                // Display the video or the featured image based on condition
                if ($has_video) {
                    // Extract the first video shortcode from the content
                    $first_video_shortcode = get_first_video_shortcode($post_content);

                    if (!empty($first_video_shortcode)) {
                        // Display the first video
                        echo '<div class="video-featured-image">' . do_shortcode($first_video_shortcode) . '</div>';
                    }
                } elseif (has_post_thumbnail()) {
                    // Display the featured image
                    echo '<div class="video-featured-image">' . get_the_post_thumbnail(null, 'large') . '</div>';
                }
                ?>
            </div>
        </article>
    <?php
    endwhile;

    echo '</div>'; // Close the custom-loop-container

    // Pagination
    $total_pages = $custom_query->max_num_pages;
    the_posts_pagination(array(
        'prev_text' => __('Previous', 'textdomain'),
        'next_text' => __('Next', 'textdomain'),
        'total'     => $total_pages,
    ));

    wp_reset_postdata(); // Reset the post data to the main query
else :
    // If no posts are found
    echo 'No posts found';

endif;

get_footer();

?>
