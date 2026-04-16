<?php
/**
 * Blog index template
 *
 * @package Sparks Theme
 */

get_header(); ?>

<?php
$hero_image_id  = get_option('sparks_hero_image');
$hero_text      = get_option('sparks_hero_text');
$hero_subtext   = get_option('sparks_hero_subtext');
$hero_image_url = $hero_image_id ? wp_get_attachment_image_url($hero_image_id, 'full') : '';
?>

<?php if ($hero_image_url || $hero_text || $hero_subtext) : ?>
<section class="hero-section" <?php if ($hero_image_url) : ?>style="background-image: url('<?php echo esc_url($hero_image_url); ?>');"<?php endif; ?>>
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <?php if ($hero_text) : ?>
            <h1 class="hero-text"><?php echo esc_html($hero_text); ?></h1>
        <?php endif; ?>
        <?php if ($hero_subtext) : ?>
            <p class="hero-subtext"><?php echo esc_html($hero_subtext); ?></p>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">
        <div class="custom-loop-container">
            <?php
            $sparks_custom_code = get_option('sparks_custom_code', '');
            $sparks_loop_count  = 0;
            ?>
            <?php if (have_posts()) : ?>
                <?php while (have_posts()) : the_post(); ?>
                    <?php
                    $post_id = get_the_ID();
                    include(get_template_directory() . '/elements/post-card.php');
                    $sparks_loop_count++;
                    if ($sparks_loop_count === 3 && $sparks_custom_code) :
                    ?>
                        <div class="sparks-custom-inject"><?php echo do_shortcode($sparks_custom_code); ?></div>
                    <?php endif; ?>
                <?php endwhile; ?>

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
</div>

<?php get_footer(); ?>