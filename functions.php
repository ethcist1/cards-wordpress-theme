<?php
/**
 * functions.php
 *
 * @package Sparks_Theme
 */

/**
 * Enqueue styles and scripts.
 */
function my_theme_enqueue_assets() {
    // Enqueue parent and child theme stylesheets
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');
    wp_enqueue_style('child-style', get_stylesheet_uri(), array('parent-style'));
    wp_enqueue_style('open-sans-font', 'https://fonts.googleapis.com/css?family=Open+Sans&display=swap');

    // Enqueue post-card assets on specific pages
   if (is_front_page() || is_archive() || is_home() || is_category() || is_page_template('pagination-archive.php')) {
        wp_enqueue_style('post-card-style', get_template_directory_uri() . '/elements/post-card.css');
        wp_enqueue_script('post-card-script', get_template_directory_uri() . '/elements/post-card.js', array('jquery'), null, true);

        // Localize script if needed
        $translation_array = array('myThemeUri' => get_template_directory_uri());
        wp_localize_script('post-card-script', 'myScriptParams', $translation_array);
    }
}
add_action('wp_enqueue_scripts', 'my_theme_enqueue_assets');


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

//suppporting featured images
function mytheme_setup() {
    // Add support for Featured Images
    add_theme_support('post-thumbnails');

}

add_action('after_setup_theme', 'mytheme_setup');


//parsing posts for videos
function get_first_video_shortcode($content) {
    preg_match('/\[video[^\]]*?mp4=[\'"]([^\'"]+)[\'"][^\]]*?\]/i', $content, $matches);
    return !empty($matches[0]) ? $matches[0] : '';
}


//function to support proper pagination on homepage
global $wp_rewrite;
$wp_rewrite->pagination_base = 'page';


//check for thmeupdates
function check_for_theme_update() {
    $json_url = 'http://sparksofanation.com/downloads/sparks-update.json'; // URL to your JSON file
    $json = wp_remote_get($json_url);

    if (is_wp_error($json)) {
        return false;
    }

    $data = json_decode(wp_remote_retrieve_body($json), true);
    if ($data && version_compare(wp_get_theme()->get('Version'), $data['version'], '<')) {
        // Logic to notify about update or auto-update the theme
    }
}
add_action('admin_init', 'check_for_theme_update');