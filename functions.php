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
}
add_action('wp_enqueue_scripts', 'sparks_theme_enqueue_styles');


// Function to get the first video shortcode from the content
function get_first_video_shortcode($content)
{
    preg_match('/\[video[^\]]*?mp4=[\'"]([^\'"]+)[\'"][^\]]*?\]/i', $content, $matches);
    return !empty($matches[0]) ? $matches[0] : '';
}

//viewing featured images
add_theme_support('post-thumbnails');
