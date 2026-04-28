<aside id="secondary" class="widget-area">

    <?php if ( is_active_sidebar( 'sidebar-top' ) ) : ?>
        <div class="sidebar-zone sidebar-top">
            <?php dynamic_sidebar( 'sidebar-top' ); ?>
        </div>
    <?php endif; ?>

    <div class="sidebar-zone sidebar-popular-posts">
        <h2 class="widget-title"><?php esc_html_e( 'Popular Posts', 'sparks-theme' ); ?></h2>

        <?php
        global $wp_query;
        $main_posts_per_page = (int) get_option( 'posts_per_page', 10 );
        $popular_query = new WP_Query( array(
            'post_type'      => 'post',
            'post_status'    => 'publish',
            'posts_per_page' => 8,
            'orderby'        => 'date',
            'order'          => 'DESC',
            'offset'         => $main_posts_per_page,
        ) );

        if ( $popular_query->have_posts() ) :
            while ( $popular_query->have_posts() ) :
                $popular_query->the_post();
                $thumb_url = get_the_post_thumbnail_url( get_the_ID(), 'medium' );

                // Fallback: first image in post content
                if ( ! $thumb_url ) {
                    $content = get_post_field( 'post_content', get_the_ID() );
                    if ( preg_match( '/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $content, $img_match ) ) {
                        $thumb_url = $img_match[1];
                    }
                }
        ?>
            <a class="popular-post-item" href="<?php the_permalink(); ?>" aria-label="<?php the_title_attribute(); ?>">
                <?php if ( $thumb_url ) : ?>
                    <div class="popular-post-image" style="background-image: url('<?php echo esc_url( $thumb_url ); ?>');">
                        <span class="popular-post-title"><?php the_title(); ?></span>
                    </div>
                <?php else : ?>
                    <div class="popular-post-image popular-post-no-thumb">
                        <span class="popular-post-title"><?php the_title(); ?></span>
                    </div>
                <?php endif; ?>
            </a>
        <?php
            endwhile;
            wp_reset_postdata();
        endif;
        ?>
    </div>

    <?php if ( is_active_sidebar( 'sidebar-bottom' ) ) : ?>
        <div class="sidebar-zone sidebar-bottom">
            <?php dynamic_sidebar( 'sidebar-bottom' ); ?>
        </div>
    <?php endif; ?>

</aside>
