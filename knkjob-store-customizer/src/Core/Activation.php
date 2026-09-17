<?php
namespace KNKJOB\StoreCustomizer\Core;

if (!defined('ABSPATH')) {
    exit;
}

final class Activation {
    public static function activate() {
        self::ensure_front_page();
        update_option('blogname', 'KNKJOB STORE');
        update_option('knkjob_store_customizer_version', KNKJOB_SC_VERSION);
        set_transient('knkjob_store_customizer_activated', 1, 60);
    }

    public static function ensure_front_page() {
        $page = get_page_by_path('store-home');

        if (!$page) {
            $page_id = wp_insert_post(array(
                'post_title'   => 'KNKJOB STORE',
                'post_name'    => 'store-home',
                'post_content' => '[knkjob_store_home]',
                'post_status'  => 'publish',
                'post_type'    => 'page',
            ));
        } else {
            $page_id = (int) $page->ID;

            if (trim((string) $page->post_content) !== '[knkjob_store_home]') {
                wp_update_post(array(
                    'ID'           => $page_id,
                    'post_content' => '[knkjob_store_home]',
                ));
            }
        }

        if (!is_wp_error($page_id) && $page_id) {
            update_option('show_on_front', 'page');
            update_option('page_on_front', (int) $page_id);
        }
    }
}
