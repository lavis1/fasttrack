<?php
if (!defined('ABSPATH')) {
    exit;
}

class FTRB_Plugin
{
    /**
     * Singleton instance.
     *
     * @var FTRB_Plugin|null
     */
    private static $instance = null;

    /**
     * Get singleton instance.
     *
     * @return FTRB_Plugin
     */
    public static function instance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Constructor.
     */
    private function __construct()
    {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_assets'));
    }

    /**
     * Load CSS.
     */
    public function enqueue_assets()
    {
        wp_enqueue_style(
            'ftrb-badge',
            FTRB_PLUGIN_URL . 'assets/css/badge.css',
            array(),
            FTRB_VERSION
        );
    }

    /**
     * Public output function.
     *
     * @param string $value
     *
     * @return string
     */
    public function output($value)
    {
        $value = trim($value);

        if ($value === '') {
            return '';
        }

        /*
         * Expect:
         *
         * feed:1
         * feed:12
         * feed:25
         */
        if (!preg_match('/^feed:(\d+)$/i', $value, $matches)) {

            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('Fast Track Review Badge: Invalid feed value "' . $value . '"');
            }

            return '';
        }

        $feed_id = (int) $matches[1];

        if ($feed_id <= 0) {
            return '';
        }

        /*
         * Get review data.
         */
        $review = FTRB_Data::get_feed_data($feed_id);

        if (empty($review)) {
            return '';
        }

        /*
         * Render badge.
         */
        return FTRB_Renderer::render($review);
    }
}
