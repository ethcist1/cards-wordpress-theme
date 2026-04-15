<?php
/**
 * The template for displaying all single pages
 */

get_header();

if ( have_posts() ) :
    while ( have_posts() ) : the_post();
?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">
        <div id="page" class="sparks-page">
            <h1 class="page-title"><?php the_title(); ?></h1>
            <div class="page-content">
                <?php the_content(); ?>
            </div>
        </div>
    </main>

    <?php get_sidebar(); ?>
</div>

<?php
    endwhile;
endif;

get_footer();
