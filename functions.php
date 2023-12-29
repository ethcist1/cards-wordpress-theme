<?php
/**
 * functions.php
 *
 * @package Sparks_Theme
 */

/**
 * Enqueue styles and scripts.
 */
function sparks_theme_enqueue_styles() {
    // Enqueue parent theme stylesheet (assuming it has one)
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');

    // Enqueue child theme stylesheet
    wp_enqueue_style('child-style', get_stylesheet_uri(), array('parent-style'));
	
	// Enqueue Open Sans Font
    wp_enqueue_style('open-sans-font', 'https://fonts.googleapis.com/css?family=Open+Sans&display=swap');
}
add_action('wp_enqueue_scripts', 'sparks_theme_enqueue_styles');


//supporting logo
function mytheme_theme_setup() {
    // Add theme support for Custom Logo
    add_theme_support('custom-logo', array(
        'height'      => 100,
        'width'       => 400,
        'flex-height' => true,
        'flex-width'  => true,
    ));
}
add_action('after_setup_theme', 'mytheme_theme_setup');


//creating the menu
function mytheme_register_menus() {
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'mytheme'),
        // You can register more menus here if needed
    ));
}
add_action('after_setup_theme', 'mytheme_register_menus');

//creating widgets area
function mytheme_widgets_init() {
    register_sidebar(array(
        'name'          => __('Archive Sidebar', 'mytheme'),
        'id'            => 'archive-sidebar',
        'description'   => __('Widgets in this area will be shown on all posts and archives.', 'mytheme'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ));
}
add_action('widgets_init', 'mytheme_widgets_init');



function get_first_video_shortcode($content) {
    preg_match('/\[video[^\]]*?mp4=[\'"]([^\'"]+)[\'"][^\]]*?\]/i', $content, $matches);
    return !empty($matches[0]) ? $matches[0] : '';
}





//function to support proper pagination on homepage
global $wp_rewrite;
$wp_rewrite->pagination_base = 'page';
