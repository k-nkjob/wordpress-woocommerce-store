<?php
namespace KNKJOB\StoreCustomizer\Core;

if (!defined('ABSPATH')) {
    exit;
}

final class Assets {
    private static function version($relative_path) {
        $full_path = KNKJOB_SC_PATH . ltrim($relative_path, '/');
        return file_exists($full_path) ? (string) filemtime($full_path) : KNKJOB_SC_VERSION;
    }

    public static function style($handle, $relative_path, $deps = array()) {
        wp_enqueue_style(
            $handle,
            KNKJOB_SC_URL . ltrim($relative_path, '/'),
            $deps,
            self::version($relative_path)
        );
    }

    public static function script($handle, $relative_path, $deps = array()) {
        wp_enqueue_script(
            $handle,
            KNKJOB_SC_URL . ltrim($relative_path, '/'),
            $deps,
            self::version($relative_path),
            true
        );

        /* Our scripts do not need to block first paint. */
        wp_script_add_data($handle, 'strategy', 'defer');
    }
}
