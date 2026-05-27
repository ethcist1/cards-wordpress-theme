<?php
/**
 * Helper functions for Sparks Theme
 *
 * @package Sparks_Theme
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Generate WhatsApp share button HTML
 *
 * @param int|null $post_id Post ID (defaults to current post)
 * @return string WhatsApp share button HTML
 */
function sparks_get_whatsapp_share_button($post_id = null) {
    if (null === $post_id) {
        $post_id = get_the_ID();
    }

    $post_url = urlencode(get_permalink($post_id));
    $post_title = rawurlencode(get_the_title($post_id));
    $whatsapp_url = "https://api.whatsapp.com/send?text={$post_title}:%20{$post_url}";
    $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" width="30" height="30" aria-hidden="true" focusable="false"><path fill="#ffffff" d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7.9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z"/></svg>';

    $html = sprintf(
        '<a href="%s" target="_blank" rel="noopener noreferrer" class="whatsapp-share-link" aria-label="%s">%s</a>',
        esc_url($whatsapp_url),
        esc_attr__('Share on WhatsApp', 'sparks-theme'),
        $svg
    );

    return $html;
}

/**
 * Check if post content contains video
 *
 * @param string|null $content Post content (defaults to current post)
 * @return bool True if video found
 */
function sparks_has_video($content = null) {
    if (null === $content) {
        $content = get_the_content();
    }

    // Check for video shortcode
    if (has_shortcode($content, 'video')) {
        return true;
    }

    // Check for .mp4 extension
    if (stripos($content, '.mp4') !== false) {
        return true;
    }

    // Check for YouTube/Vimeo embeds (new feature - Issue #25)
    if (preg_match('/(youtube\.com|youtu\.be|vimeo\.com)/i', $content)) {
        return true;
    }

    return false;
}

/**
 * Get all video shortcodes from content
 *
 * @param string|null $content Post content (defaults to current post)
 * @return array Array of video shortcode strings
 */
function sparks_get_video_shortcodes($content = null) {
    if (null === $content) {
        $content = get_the_content();
    }

    $videos = array();

    // Extract [video] shortcodes
    if (preg_match_all('/\[video[^\]]*?\]/i', $content, $matches)) {
        $videos = $matches[0];
    }

    return $videos;
}

/**
 * Get first video shortcode from content (legacy compatibility)
 *
 * @param string $content Post content
 * @return string First video shortcode or empty string
 */
function sparks_get_first_video_shortcode($content) {
    $videos = sparks_get_video_shortcodes($content);
    return !empty($videos) ? $videos[0] : '';
}

/**
 * Render post media (video slider or featured image)
 *
 * @param int|null $post_id Post ID (defaults to current post)
 * @param bool $include_whatsapp Whether to include WhatsApp button
 * @return void
 */
function sparks_render_post_media($post_id = null, $include_whatsapp = true) {
    if (null === $post_id) {
        $post_id = get_the_ID();
    }

    $post_content = get_post_field('post_content', $post_id);
    $videos = sparks_get_all_videos($post_content);

    echo '<div class="slider" id="slider-' . esc_attr($post_id) . '">';

    if (!empty($videos)) {
        foreach ($videos as $video_html) {
            echo '<div class="slide video-featured-image">' . $video_html . '</div>';
        }
    } elseif (has_post_thumbnail($post_id)) {
        // Render featured image with responsive attributes
        echo '<div class="slide image-featured">';
        echo get_the_post_thumbnail($post_id, 'sparks-slider', array(
            'loading' => 'lazy',
            'sizes' => '(max-width: 700px) 100vw, 350px',
        ));
        echo '</div>';
    } else {
        // Fall back to first image found in post content
        $post_content = get_post_field('post_content', $post_id);
        if (preg_match('/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $post_content, $img_match)) {
            echo '<div class="slide image-featured">';
            echo '<img src="' . esc_url($img_match[1]) . '" loading="lazy" style="width:100%;height:100%;object-fit:cover;">';
            echo '</div>';
        }
    }

    echo '</div>';

    // WhatsApp share button (outside slider, anchored to entry-content)
    if ($include_whatsapp) {
        echo sparks_get_whatsapp_share_button($post_id);
    }
}

/**
 * Check if post thumbnail has alt text
 *
 * @param int|null $post_id Post ID
 * @return bool True if alt text exists
 */
function sparks_has_image_alt_text($post_id = null) {
    if (null === $post_id) {
        $post_id = get_the_ID();
    }

    $thumbnail_id = get_post_thumbnail_id($post_id);
    if (!$thumbnail_id) {
        return false;
    }

    $alt_text = get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true);
    return !empty($alt_text);
}

/**
 * Admin notice for posts missing alt text on featured images
 */
function sparks_check_featured_image_alt() {
    $screen = get_current_screen();
    if ('post' !== $screen->post_type || 'post' !== $screen->base) {
        return;
    }

    global $post;
    if (!$post || !has_post_thumbnail($post->ID)) {
        return;
    }

    if (!sparks_has_image_alt_text($post->ID)) {
        ?>
        <div class="notice notice-warning">
            <p><?php esc_html_e('Warning: Your featured image is missing alt text. Please add descriptive alt text for accessibility.', 'sparks-theme'); ?></p>
        </div>
        <?php
    }
}
add_action('admin_notices', 'sparks_check_featured_image_alt');

/**
 * Get embedded videos (YouTube, Vimeo) from content
 *
 * @param string|null $content Post content
 * @return array Array of embedded video HTML
 */
function sparks_get_embedded_videos($content = null) {
    if (null === $content) {
        $content = get_the_content();
    }

    $videos = array();

    // Apply WordPress oEmbed to get proper embed HTML
    global $wp_embed;

    // Find YouTube URLs
    if (preg_match_all('/(https?:\/\/)?(www\.)?(youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/i', $content, $matches)) {
        foreach ($matches[0] as $url) {
            $embed_html = $wp_embed->run_shortcode('[embed]' . $url . '[/embed]');
            if ($embed_html !== $url) {
                $videos[] = $embed_html;
            }
        }
    }

    // Find Vimeo URLs
    if (preg_match_all('/(https?:\/\/)?(www\.)?vimeo\.com\/(\d+)/i', $content, $matches)) {
        foreach ($matches[0] as $url) {
            $embed_html = $wp_embed->run_shortcode('[embed]' . $url . '[/embed]');
            if ($embed_html !== $url) {
                $videos[] = $embed_html;
            }
        }
    }

    return $videos;
}

/**
 * Get self-hosted videos from content
 *
 * @param string|null $content Post content
 * @return array Array of self-hosted video HTML
 */
function sparks_get_selfhosted_videos($content = null) {
    if (null === $content) {
        $content = get_the_content();
    }

    $videos = array();

    // Find direct links to video files (.mp4, .webm, .ogg)
    if (preg_match_all('/https?:\/\/[^\s<>"]+\.(?:mp4|webm|ogg)/i', $content, $matches)) {
        foreach ($matches[0] as $video_url) {
            // Determine video type from extension
            $extension = strtolower(pathinfo($video_url, PATHINFO_EXTENSION));
            $mime_types = array(
                'mp4' => 'video/mp4',
                'webm' => 'video/webm',
                'ogg' => 'video/ogg',
            );
            $mime_type = isset($mime_types[$extension]) ? $mime_types[$extension] : 'video/mp4';

            // Generate HTML5 video tag
            $video_html = sprintf(
                '<video class="sparks-video-player" controls preload="metadata" playsinline><source src="%s" type="%s">%s</video>',
                esc_url($video_url),
                esc_attr($mime_type),
                esc_html__('Your browser doesn\'t support HTML5 video.', 'sparks-theme')
            );

            $videos[] = $video_html;
        }
    }

    return $videos;
}

/**
 * Get all videos from content (unified collection)
 * Extracts videos from [video] shortcodes, YouTube/Vimeo URLs, and self-hosted URLs
 * Returns videos in the order they appear in content
 *
 * @param string|null $content Post content
 * @return array Array of video HTML/shortcodes in content order
 */
function sparks_get_all_videos($content = null) {
    if (null === $content) {
        $content = get_the_content();
    }

    // Render blocks/shortcodes so all video formats produce plain HTML.
    $rendered = apply_filters('the_content', $content);

    $videos = array();
    $seen_urls = array();

    // Self-hosted: <video ...> tags (Gutenberg wp:video, classic player, etc.)
    if (preg_match_all('/<video[^>]*>(.*?)<\/video>/is', $rendered, $video_matches)) {
        foreach ($video_matches[0] as $video_tag) {
            // Prefer <source src> inside the tag, fall back to src attribute on <video> itself.
            $src = '';
            if (preg_match('/<source[^>]+src=["\']([^"\']+)["\']/', $video_tag, $m)) {
                $src = $m[1];
            } elseif (preg_match('/<video[^>]+src=["\']([^"\']+)["\']/', $video_tag, $m)) {
                $src = $m[1];
            }

            if (!$src || in_array($src, $seen_urls)) {
                continue;
            }

            $ext      = strtolower(pathinfo(strtok($src, '?'), PATHINFO_EXTENSION));
            $mime_map = array('mp4' => 'video/mp4', 'webm' => 'video/webm', 'ogg' => 'video/ogg', 'ogv' => 'video/ogg');
            $mime_type = isset($mime_map[$ext]) ? $mime_map[$ext] : 'video/mp4';

            $videos[]    = sprintf(
                '<video class="sparks-video-player" controls preload="metadata" playsinline><source src="%s" type="%s"></video>',
                esc_url($src),
                esc_attr($mime_type)
            );
            $seen_urls[] = $src;
        }
    }

    // Embedded: <iframe> tags (YouTube, Vimeo, etc.)
    if (preg_match_all('/<iframe[^>]+src=["\']([^"\']+)["\'][^>]*>.*?<\/iframe>/is', $rendered, $iframe_matches)) {
        foreach ($iframe_matches[0] as $index => $iframe_tag) {
            $src = $iframe_matches[1][$index];
            if (in_array($src, $seen_urls)) {
                continue;
            }
            $videos[]    = '<div class="sparks-embed-responsive">' . $iframe_tag . '</div>';
            $seen_urls[] = $src;
        }
    }

    return $videos;
}
