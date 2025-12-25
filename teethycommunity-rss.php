<?php
/**
 * Plugin Name: TeethyCommunity RSS
 * Plugin URI: https://teethy.org
 * Description: RSS feed for FluentCommunity posts with correct slugs, space filtering, and Facebook/X-friendly formatting.
 * Version: 1.4
 * Author: Teethy
 * Version: 1.1
 * Author: teethy
 * Author URI: https://teethy.org
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

if (!defined('ABSPATH')) exit;

add_action('init', function () {

    if (!isset($_GET['teethycommunity_rss'])) {
        return;
    }

    global $wpdb;

    header('Content-Type: application/rss+xml; charset=UTF-8');

    $posts_table  = $wpdb->prefix . 'fcom_posts';
    $spaces_table = $wpdb->prefix . 'fcom_spaces';

    /**
     * ----------------------------------------------------
     * Space filtering (?space=slug OR ?spaces=a,b,c)
     * ----------------------------------------------------
     */
    $space_slugs = [];

    if (!empty($_GET['spaces'])) {
        $space_slugs = array_map(
            'sanitize_title',
            explode(',', $_GET['spaces'])
        );
    } elseif (!empty($_GET['space'])) {
        $space_slugs[] = sanitize_title($_GET['space']);
    }

    $space_where = '';
    if (!empty($space_slugs)) {
        $placeholders = implode(',', array_fill(0, count($space_slugs), '%s'));
        $space_where = $wpdb->prepare(
            "AND s.slug IN ($placeholders)",
            ...$space_slugs
        );
    }

    /**
     * ----------------------------------------------------
     * Query FluentCommunity posts
     * ----------------------------------------------------
     */
    $posts = $wpdb->get_results("
        SELECT 
            p.title,
            p.slug AS post_slug,
            p.message_rendered,
            p.created_at,
            s.slug AS space_slug
        FROM $posts_table p
        INNER JOIN $spaces_table s ON p.space_id = s.id
        WHERE p.status = 'published'
          AND p.privacy = 'public'
          $space_where
        ORDER BY p.created_at DESC
        LIMIT 100
    ");

    /**
     * ----------------------------------------------------
     * RSS OUTPUT
     * ----------------------------------------------------
     */
    echo '<?xml version="1.0" encoding="UTF-8"?>';
    echo '<rss version="2.0"
        xmlns:content="http://purl.org/rss/1.0/modules/content/"
        xmlns:dc="http://purl.org/dc/elements/1.1/"
    >';

    echo '<channel>';
    echo '<title>TeethyCommunity RSS</title>';
    echo '<link>' . esc_url(home_url()) . '</link>';
    echo '<description>FluentCommunity public posts feed</description>';
    echo '<language>en</language>';

    if (!$posts) {
        echo '<item>';
        echo '<title>No posts found</title>';
        echo '<description>No public posts available</description>';
        echo '</item>';
    }

    foreach ($posts as $post) {

        $url = home_url(
            '/space/' . $post->space_slug . '/post/' . $post->post_slug
        );

        /**
         * ------------------------------------------------
         * TEXT VERSION → <description> (Facebook preview)
         * ------------------------------------------------
         */
        $text_description = html_entity_decode(
            wp_strip_all_tags($post->message_rendered),
            ENT_QUOTES,
            'UTF-8'
        );

        // Normalize whitespace
        $text_description = preg_replace('/\s+/u', ' ', trim($text_description));

        // Trim length for social previews
        $text_description = wp_trim_words($text_description, 25, '…');

        if (empty($text_description)) {
            $text_description = $post->title;
        }

        /**
         * ------------------------------------------------
         * HTML VERSION → <content:encoded>
         * ------------------------------------------------
         */
        $html_description = wpautop($post->message_rendered);
        $html_description = wp_kses_post($html_description);

        /**
         * ------------------------------------------------
         * ITEM OUTPUT
         * ------------------------------------------------
         */
        echo '<item>';

        echo '<title><![CDATA[' . $post->title . ']]></title>';
        echo '<link><![CDATA[' . esc_url($url) . ']]></link>';
        echo '<guid isPermaLink="true"><![CDATA[' . esc_url($url) . ']]></guid>';
        echo '<pubDate>' . date(DATE_RSS, strtotime($post->created_at)) . '</pubDate>';

        // Plain text for Facebook/X
        echo '<description><![CDATA[' . esc_html($text_description) . ']]></description>';

        // Proper HTML content
        echo '<content:encoded><![CDATA[' . $html_description . ']]></content:encoded>';

        echo '</item>';
    }

    echo '</channel>';
    echo '</rss>';

    exit;
});
