<?php
if (!defined('ABSPATH')) {
    exit;
}

class FTRB_Data
{
    /**
     * Cache lifetime (24 hours).
     */
    const CACHE_TIME = 172800;

    /**
     * Get review data for a feed.
     *
     * @param int $feed_id
     * @return array|false
     */
    public static function get_feed_data($feed_id)
    {
        $feed_id = (int) $feed_id;

        if ($feed_id <= 0) {
            return false;
        }

        /*
         * Try cache first.
         */
        $cache_key = 'ftrb_feed_' . $feed_id;

        $cached = get_transient($cache_key);

        if ($cached !== false) {
            return $cached;
        }

        global $wpdb;

        $feeds_table   = $wpdb->prefix . 'sbr_feeds';
        $sources_table = $wpdb->prefix . 'sbr_sources';

        /*
         * Get feed settings.
         */
        $settings_json = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT settings
                 FROM {$feeds_table}
                 WHERE id = %d
                 LIMIT 1",
                $feed_id
            )
        );

        if (empty($settings_json)) {

            self::debug(
                'Feed not found.',
                $feed_id
            );

            return false;
        }

        $settings = json_decode($settings_json, true);

        if (
            empty($settings) ||
            empty($settings['sources']) ||
            !is_array($settings['sources'])
        ) {

            self::debug(
                'Feed has no sources.',
                $feed_id
            );

            return false;
        }

        /*
         * We only use the first source.
         */
        $source_id = reset($settings['sources']);

        if (empty($source_id)) {

            self::debug(
                'Source ID missing.',
                $feed_id
            );

            return false;
        }

        /*
         * Load source information.
         */
        $info_json = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT info
                 FROM {$sources_table}
                 WHERE account_id = %s
                 LIMIT 1",
                $source_id
            )
        );

        if (empty($info_json)) {

            self::debug(
                'Source not found.',
                $feed_id
            );

            return false;
        }

        $info = json_decode($info_json, true);

        if (!is_array($info)) {

            self::debug(
                'Invalid source JSON.',
                $feed_id
            );

            return false;
        }

        /*
         * Normalize the data.
         */
        $data = array(
            'feed_id' => $feed_id,
            'source_id' => $source_id,

            'name' => isset($info['name']) ? $info['name'] : '',

            'rating' => isset($info['rating'])
                ? (float) $info['rating']
                : 0,

            'count' => isset($info['total_rating'])
                ? (int) $info['total_rating']
                : 0,

            'url' => isset($info['review_url'])
                ? esc_url_raw($info['review_url'])
                : '',
        );

        /*
         * Cache the result.
         */
        set_transient(
            $cache_key,
            $data,
            self::CACHE_TIME
        );

        return $data;
    }

    /**
     * Clear cache.
     *
     * @param int $feed_id
     */
    public static function clear_cache($feed_id)
    {
        delete_transient(
            'ftrb_feed_' . (int) $feed_id
        );
    }

    /**
     * Debug logger.
     *
     * @param string $message
     * @param int    $feed_id
     */
    private static function debug($message, $feed_id = 0)
    {
        if (!defined('WP_DEBUG') || !WP_DEBUG) {
            return;
        }

        error_log(
            '[Fast Track Review Badge] ' .
            $message .
            ' Feed ID: ' .
            $feed_id
        );
    }
}
