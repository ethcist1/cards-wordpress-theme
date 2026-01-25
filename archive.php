<?php
/**
 * Template Name: Cards Archive - depriciatd
 */

get_header();

// Control the number of posts per page
$posts_per_page = 10; // You can adjust this number as needed

// Determine the correct page number
if (is_front_page() && is_home()) {
    // Default homepage
    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
} else {
    // Everything else
    $paged = (get_query_var('page')) ? get_query_var('page') : 1;
}

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
                <?php while ($custom_query->have_posts()) : $custom_query->the_post(); ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class('custom-loop-post'); ?>>
                        <div class="entry-content">
                            <?php
                            $post_content = get_the_content();
                            $has_video = (has_shortcode($post_content, 'video') || stripos($post_content, '.mp4') !== false);

							// Display the post date
                            echo '<div class="post-text"><div class="post-date">' . get_the_date() . '</div>';
							
                            echo '<h2 class="entry-title"><a  href="' . esc_url(get_permalink()) . '">' . get_the_title() . '</a></h2></div>';
							
							 
                            
                                                  
							
                            if ($has_video) {
                                $first_video_shortcode = get_first_video_shortcode($post_content);

                                if (!empty($first_video_shortcode)) {
                                    echo '<div class="video-featured-image">' . do_shortcode($first_video_shortcode) . '</div>';
                                }
                            } elseif (has_post_thumbnail()) {
                                echo '<div class="video-featured-image">' . get_the_post_thumbnail(null, 'large') . '</div>';
                            }
                            ?>
                        </div>
                    </article>
                <?php endwhile; ?>
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
            <p>No posts found</p>
        <?php endif; ?>
    </main>

    <?php get_sidebar(); ?>
</div>

<?php get_footer(); ?>