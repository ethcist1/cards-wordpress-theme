<?php
/**
 * The template for displaying all single pages
 */

get_header(); 

if ( have_posts() ) : 
    while ( have_posts() ) : the_post();
?>

    <div id="page" class="sparks-page">
        <div id="content" class="page-content">
            <h1 class="page-title"><?php the_title(); ?></h1>
            <div class="page-content">
                <?php the_content(); ?>
            </div><!-- .page-content -->
        </div><!-- #content -->
    </div><!-- #page -->

<?php
    endwhile;
endif;

get_footer(); // This function call gets the footer.php file content.
?>