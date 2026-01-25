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
    $icon_url = get_template_directory_uri() . '/elements/WhatsApp.png';

    $html = sprintf(
        '<a href="%s" target="_blank" rel="noopener noreferrer" class="whatsapp-share-link" aria-label="%s"><img src="%s" alt="%s" class="whatsapp-share" width="30" height="30"></a>',
        esc_url($whatsapp_url),
        esc_attr__('Share on WhatsApp', 'sparks-theme'),
        esc_url($icon_url),
        esc_attr__('WhatsApp icon', 'sparks-theme')
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
        // Render video slides
        foreach ($videos as $video_shortcode) {
            echo '<div class="slide video-featured-image">';
            echo do_shortcode($video_shortcode);
            echo '</div>';
        }
    } elseif (has_post_thumbnail($post_id)) {
        // Render featured image with responsive attributes
        echo '<div class="slide image-featured">';
        echo get_the_post_thumbnail($post_id, 'sparks-slider', array(
            'loading' => 'lazy',
            'sizes' => '(max-width: 700px) 100vw, 350px',
        ));
        echo '</div>';
    }

    // WhatsApp share button
    if ($include_whatsapp) {
        echo sparks_get_whatsapp_share_button($post_id);
    }

    echo '</div>';
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
                '<video class="wp-video" controls preload="metadata"><source src="%s" type="%s">%s</video>',
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

    $video_items = array();
    $processed_urls = array();

    // Extract [video] shortcodes with positions
    if (preg_match_all('/\[video[^\]]*?\]/i', $content, $matches, PREG_OFFSET_CAPTURE)) {
        foreach ($matches[0] as $match) {
            $video_items[] = array(
                'position' => $match[1],
                'content' => $match[0],
                'type' => 'shortcode',
            );

            // Extract URL from shortcode to track for deduplication
            if (preg_match('/src=["\']([^"\']+)["\']/', $match[0], $url_match)) {
                $processed_urls[] = $url_match[1];
            }
        }
    }

    // Extract YouTube URLs with positions
    global $wp_embed;
    if (preg_match_all('/(https?:\/\/)?(www\.)?(youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/i', $content, $matches, PREG_OFFSET_CAPTURE)) {
        foreach ($matches[0] as $match) {
            $url = $match[0];
            $position = $match[1];

            // Skip if already processed
            if (in_array($url, $processed_urls)) {
                continue;
            }

            // Check for duplicates within YouTube matches
            $video_id = $matches[4][array_search($match, $matches[0])][0];
            $is_duplicate = false;
            foreach ($processed_urls as $processed_url) {
                if (strpos($processed_url, $video_id) !== false) {
                    $is_duplicate = true;
                    break;
                }
            }

            if (!$is_duplicate) {
                $embed_html = $wp_embed->run_shortcode('[embed]' . $url . '[/embed]');
                if ($embed_html !== $url) {
                    $video_items[] = array(
                        'position' => $position,
                        'content' => $embed_html,
                        'type' => 'youtube',
                    );
                    $processed_urls[] = $url;
                }
            }
        }
    }

    // Extract Vimeo URLs with positions
    if (preg_match_all('/(https?:\/\/)?(www\.)?vimeo\.com\/(\d+)/i', $content, $matches, PREG_OFFSET_CAPTURE)) {
        foreach ($matches[0] as $match) {
            $url = $match[0];
            $position = $match[1];

            // Skip if already processed
            if (in_array($url, $processed_urls)) {
                continue;
            }

            $embed_html = $wp_embed->run_shortcode('[embed]' . $url . '[/embed]');
            if ($embed_html !== $url) {
                $video_items[] = array(
                    'position' => $position,
                    'content' => $embed_html,
                    'type' => 'vimeo',
                );
                $processed_urls[] = $url;
            }
        }
    }

    // Extract self-hosted video URLs with positions
    if (preg_match_all('/https?:\/\/[^\s<>"]+\.(?:mp4|webm|ogg)/i', $content, $matches, PREG_OFFSET_CAPTURE)) {
        foreach ($matches[0] as $match) {
            $url = $match[0];
            $position = $match[1];

            // Skip if already processed
            if (in_array($url, $processed_urls)) {
                continue;
            }

            // Determine video type from extension
            $extension = strtolower(pathinfo($url, PATHINFO_EXTENSION));
            $mime_types = array(
                'mp4' => 'video/mp4',
                'webm' => 'video/webm',
                'ogg' => 'video/ogg',
            );
            $mime_type = isset($mime_types[$extension]) ? $mime_types[$extension] : 'video/mp4';

            // Generate HTML5 video tag
            $video_html = sprintf(
                '<video class="wp-video" controls preload="metadata" style="max-width: 100%%;"><source src="%s" type="%s">%s</video>',
                esc_url($url),
                esc_attr($mime_type),
                esc_html__('Your browser doesn\'t support HTML5 video.', 'sparks-theme')
            );

            $video_items[] = array(
                'position' => $position,
                'content' => $video_html,
                'type' => 'selfhosted',
            );
            $processed_urls[] = $url;
        }
    }

    // Sort by position to maintain content order
    usort($video_items, function($a, $b) {
        return $a['position'] - $b['position'];
    });

    // Extract just the content (HTML/shortcodes) for return
    $videos = array();
    foreach ($video_items as $item) {
        $videos[] = $item['content'];
    }

    return $videos;
}
