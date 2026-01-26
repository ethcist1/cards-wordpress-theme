<?php
/**
 * functions.php
 *
 * @package Sparks_Theme
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Load helper functions
require_once get_template_directory() . '/inc/helpers.php';

/**
 * Enqueue styles and scripts.
 */
function sparks_enqueue_assets() {
    // Enqueue parent and child theme stylesheets with versioning
    wp_enqueue_style(
        'sparks-main-style',
        get_template_directory_uri() . '/style.css',
        array(),
        sparks_get_asset_version('/style.css')
    );
    wp_enqueue_style(
        'open-sans-font',
        'https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap',
        array(),
        null
    );

    // Enqueue post-card assets on specific pages
   if (is_front_page() || is_archive() || is_home() || is_category() || is_page_template('pagination-archive.php')) {
        wp_enqueue_style(
            'sparks-post-card-style',
            get_template_directory_uri() . '/elements/post-card.css',
            array(),
            sparks_get_asset_version('/elements/post-card.css')
        );
        wp_enqueue_script(
            'sparks-post-card-script',
            get_template_directory_uri() . '/elements/post-card.js',
            array('jquery'),
            sparks_get_asset_version('/elements/post-card.js'),
            true
        );

        // Localize script
        $script_data = array('themeUri' => get_template_directory_uri());
        wp_localize_script('sparks-post-card-script', 'sparksPostCard', $script_data);
    }
}
add_action('wp_enqueue_scripts', 'sparks_enqueue_assets');

/**
 * Enqueue AJAX pagination script with nonce
 */
function sparks_enqueue_ajax_pagination() {
    if (is_front_page() || is_archive() || is_home() || is_category() || is_page_template('pagination-archive.php')) {
        wp_enqueue_script(
            'sparks-ajax-pagination',
            get_template_directory_uri() . '/js/ajax-pagination.js',
            array('jquery'),
            '1.0.0',
            true
        );

        // Localize script with ajaxurl and nonce
        wp_localize_script('sparks-ajax-pagination', 'sparksAjaxPagination', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('sparks_ajax_pagination_nonce'),
        ));
    }
}
add_action('wp_enqueue_scripts', 'sparks_enqueue_ajax_pagination');

/**
 * AJAX handler for pagination
 */
function sparks_ajax_pagination_handler() {
    // Verify nonce
    if (!check_ajax_referer('sparks_ajax_pagination_nonce', 'nonce', false)) {
        wp_send_json_error(array('message' => 'Security check failed'));
        wp_die();
    }

    // Validate and sanitize input
    $paged = isset($_POST['page']) ? absint($_POST['page']) : 1;
    $query_vars = isset($_POST['query_vars']) ? json_decode(stripslashes($_POST['query_vars']), true) : array();

    if (!is_array($query_vars)) {
        $query_vars = array();
    }

    // Set up query
    $query_vars['paged'] = $paged;
    $query_vars['posts_per_page'] = isset($query_vars['posts_per_page']) ? absint($query_vars['posts_per_page']) : 10;

    $custom_query = new WP_Query($query_vars);

    ob_start();

    if ($custom_query->have_posts()) {
        while ($custom_query->have_posts()) {
            $custom_query->the_post();
            $post_id = get_the_ID();
            include(get_template_directory() . '/elements/post-card.php');
        }

        // Output pagination
        echo '<div class="pagination-container">';
        echo paginate_links(array(
            'prev_text' => __('Previous', 'sparks-theme'),
            'next_text' => __('Next', 'sparks-theme'),
            'total' => $custom_query->max_num_pages,
            'current' => $paged,
        ));
        echo '</div>';

        wp_reset_postdata();
    } else {
        echo '<p>' . esc_html__('No posts found.', 'sparks-theme') . '</p>';
    }

    $html = ob_get_clean();
    wp_send_json_success(array('html' => $html));
    wp_die();
}
add_action('wp_ajax_sparks_ajax_pagination', 'sparks_ajax_pagination_handler');
add_action('wp_ajax_nopriv_sparks_ajax_pagination', 'sparks_ajax_pagination_handler');

/**
 * Add preconnect for Google Fonts for performance
 */
function sparks_add_font_preconnect() {
    echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
    echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
}
add_action('wp_head', 'sparks_add_font_preconnect', 1);


/**
 * Sparks Theme setup
 * Consolidates all after_setup_theme functionality
 */
function sparks_theme_setup() {
    // Make theme available for translation
    load_theme_textdomain('sparks-theme', get_template_directory() . '/languages');

    // Add theme support for various features
    add_theme_support('custom-logo', array(
        'height'      => 100,
        'width'       => 400,
        'flex-height' => true,
        'flex-width'  => true,
    ));

    add_theme_support('post-thumbnails');

    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ));

    add_theme_support('title-tag');

    // Register navigation menus
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'sparks-theme'),
    ));

    // Set content width
    if (!isset($content_width)) {
        $content_width = 700;
    }
}
add_action('after_setup_theme', 'sparks_theme_setup');

/**
 * Register custom image sizes for responsive images
 */
function sparks_register_image_sizes() {
    // Card thumbnail size
    add_image_size('sparks-card-thumb', 350, 450, true);

    // Single post featured image
    add_image_size('sparks-single-featured', 700, 525, false);

    // Slider images
    add_image_size('sparks-slider', 350, 450, false);
}
add_action('after_setup_theme', 'sparks_register_image_sizes');

/**
 * Add custom image sizes to media library dropdown
 */
function sparks_custom_image_sizes($sizes) {
    return array_merge($sizes, array(
        'sparks-card-thumb' => __('Card Thumbnail', 'sparks-theme'),
        'sparks-single-featured' => __('Single Post Featured', 'sparks-theme'),
        'sparks-slider' => __('Slider Image', 'sparks-theme'),
    ));
}
add_filter('image_size_names_choose', 'sparks_custom_image_sizes');

/**
 * Register widget area
 */
function sparks_widgets_init() {
    register_sidebar(array(
        'name'          => __('Archive Sidebar', 'sparks-theme'),
        'id'            => 'archive-sidebar',
        'description'   => __('Widgets in this area will be shown on all posts and archives.', 'sparks-theme'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ));
}
add_action('widgets_init', 'sparks_widgets_init');


/**
 * Get first video shortcode from content
 * @deprecated Use sparks_get_first_video_shortcode() instead
 */
function get_first_video_shortcode($content) {
    return sparks_get_first_video_shortcode($content);
}

/**
 * Set pagination base
 */
function sparks_set_pagination_base() {
    global $wp_rewrite;
    $wp_rewrite->pagination_base = 'page';
}
add_action('init', 'sparks_set_pagination_base');


/**
 * Check for theme updates securely with caching
 */
function sparks_check_theme_update() {
    // Use transients for caching (12-hour cache)
    $transient_key = 'sparks_theme_update_check';
    $cached_data = get_transient($transient_key);

    if (false !== $cached_data) {
        return $cached_data;
    }

    // GitHub raw URL for update JSON
    $json_url = 'https://raw.githubusercontent.com/ethcist1/cards-wordpress-theme/main/sparks-update.json';

    $response = wp_remote_get($json_url, array(
        'timeout' => 10,
        'sslverify' => true, // Verify SSL certificate
    ));

    if (is_wp_error($response)) {
        // Log error and set transient to retry in 1 hour
        error_log('Sparks Theme update check failed: ' . $response->get_error_message());
        set_transient($transient_key, false, HOUR_IN_SECONDS);
        return false;
    }

    $response_code = wp_remote_retrieve_response_code($response);
    if (200 !== $response_code) {
        error_log('Sparks Theme update check returned HTTP ' . $response_code);
        set_transient($transient_key, false, HOUR_IN_SECONDS);
        return false;
    }

    $data = json_decode(wp_remote_retrieve_body($response), true);

    if (!$data || !isset($data['version'])) {
        error_log('Sparks Theme update check returned invalid JSON');
        set_transient($transient_key, false, HOUR_IN_SECONDS);
        return false;
    }

    // Cache successful result for 12 hours
    set_transient($transient_key, $data, 12 * HOUR_IN_SECONDS);

    // Check version and add admin notice if update available
    if (version_compare(wp_get_theme()->get('Version'), $data['version'], '<')) {
        add_action('admin_notices', 'sparks_theme_update_notice');
    }

    return $data;
}

/**
 * Display admin notice for theme update
 */
function sparks_theme_update_notice() {
    $screen = get_current_screen();
    if ('themes' !== $screen->base) {
        return;
    }

    $update_data = get_transient('sparks_theme_update_check');
    if (!$update_data || !isset($update_data['version'])) {
        return;
    }

    ?>
    <div class="notice notice-warning is-dismissible">
        <p>
            <?php
            printf(
                /* translators: %s: new version number */
                esc_html__('A new version (%s) of Sparks Theme is available.', 'sparks-theme'),
                esc_html($update_data['version'])
            );
            ?>
        </p>
    </div>
    <?php
}

// Only check on themes page, not every admin page
add_action('load-themes.php', 'sparks_check_theme_update');

/**
 * Push theme update info into WordPress update system
 */
function sparks_push_theme_update($transient) {
    if (empty($transient->checked)) {
        return $transient;
    }

    $theme_data = wp_get_theme();
    $theme_slug = $theme_data->get_stylesheet();
    $current_version = $theme_data->get('Version');

    // Get update info from our JSON
    $update_data = sparks_check_theme_update();

    if (!$update_data || !isset($update_data['version'])) {
        return $transient;
    }

    // If there's a new version available
    if (version_compare($current_version, $update_data['version'], '<')) {
        $transient->response[$theme_slug] = array(
            'theme'       => $theme_slug,
            'new_version' => $update_data['version'],
            'url'         => isset($update_data['url']) ? $update_data['url'] : 'https://github.com/ethcist1/cards-wordpress-theme',
            'package'     => $update_data['download_url'],
            'requires'    => isset($update_data['requires']) ? $update_data['requires'] : '5.0',
            'requires_php' => isset($update_data['requires_php']) ? $update_data['requires_php'] : '7.4',
        );
    }

    return $transient;
}
add_filter('pre_set_site_transient_update_themes', 'sparks_push_theme_update');

/**
 * Fix GitHub zip folder name during update
 * GitHub zips extract to "repo-name-branch" but WordPress expects the theme folder name
 */
function sparks_fix_github_zip_folder($source, $remote_source, $upgrader, $hook_extra) {
    global $wp_filesystem;

    // Only process theme updates
    if (!isset($hook_extra['theme'])) {
        return $source;
    }

    $theme = wp_get_theme();
    $expected_folder = $theme->get_stylesheet();

    // Check if this is our theme
    if ($hook_extra['theme'] !== $expected_folder) {
        return $source;
    }

    // GitHub extracts to something like "cards-wordpress-theme-main"
    // We need to rename it to "Sparks Theme"
    $corrected_source = trailingslashit($remote_source) . $expected_folder . '/';

    if ($source !== $corrected_source) {
        // Rename the folder
        if ($wp_filesystem->move($source, $corrected_source, true)) {
            return $corrected_source;
        }
    }

    return $source;
}
add_filter('upgrader_source_selection', 'sparks_fix_github_zip_folder', 10, 4);

/**
 * Get asset version based on file modification time
 * Falls back to theme version if file doesn't exist
 *
 * @param string $file_path Relative path to asset file
 * @return string Version string
 */
function sparks_get_asset_version($file_path) {
    $file_full_path = get_template_directory() . $file_path;

    if (file_exists($file_full_path)) {
        return filemtime($file_full_path);
    }

    // Fallback to theme version
    return wp_get_theme()->get('Version');
}

/**
 * Auto-convert video URLs to video players in post content
 */
function sparks_autoembed_video_urls($content) {
    // Pattern to match video URLs that might be wrapped in <p> tags or standalone
    // Handles URLs on their own line with optional paragraph tags
    $pattern = '/(<p[^>]*>)?\s*(https?:\/\/[^\s<>"]+\.(?:mp4|webm|ogg))\s*(<\/p>)?/i';

    $content = preg_replace_callback($pattern, function($matches) {
        $opening_tag = isset($matches[1]) ? $matches[1] : '';
        $url = $matches[2];
        $closing_tag = isset($matches[3]) ? $matches[3] : '';

        $extension = strtolower(pathinfo($url, PATHINFO_EXTENSION));
        $mime_types = array(
            'mp4' => 'video/mp4',
            'webm' => 'video/webm',
            'ogg' => 'video/ogg',
        );
        $mime_type = isset($mime_types[$extension]) ? $mime_types[$extension] : 'video/mp4';

        $video_html = sprintf(
            '<video class="wp-video" controls preload="metadata"><source src="%s" type="%s">%s</video>',
            esc_url($url),
            esc_attr($mime_type),
            esc_html__('Your browser doesn\'t support HTML5 video.', 'sparks-theme')
        );

        // Preserve paragraph tags if they existed
        if ($opening_tag && $closing_tag) {
            return $opening_tag . $video_html . $closing_tag;
        }

        return $video_html;
    }, $content);

    return $content;
}
add_filter('the_content', 'sparks_autoembed_video_urls', 12);