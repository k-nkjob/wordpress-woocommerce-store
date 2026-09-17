<?php
namespace KNKJOB\StoreCustomizer\Modules\Archive;

use KNKJOB\StoreCustomizer\Core\Assets;

if (!defined('ABSPATH')) {
    exit;
}

final class ArchiveModule {
    public static function register() {
        add_action('woocommerce_after_shop_loop_item_title', array(__CLASS__, 'category_label'), 7);
        add_action('wp_enqueue_scripts', array(__CLASS__, 'enqueue'));
    }

    public static function enqueue() {
        if (is_admin() || !(is_shop() || is_product_category() || is_product_tag())) {
            return;
        }

        Assets::style('knkjob-sc-archive', 'assets/css/archive.css', array('knkjob-sc-common'));
        Assets::script('knkjob-sc-row-equalizer', 'assets/js/lib/row-equalizer.js');
        Assets::script('knkjob-sc-action-labels', 'assets/js/lib/action-labels.js');
        Assets::script(
            'knkjob-sc-archive',
            'assets/js/modules/archive.js',
            array('knkjob-sc-row-equalizer', 'knkjob-sc-action-labels')
        );
    }

    public static function category_label() {
        global $product;

        if (!$product instanceof \WC_Product) {
            return;
        }

        $terms = get_the_terms($product->get_id(), 'product_cat');

        if (empty($terms) || is_wp_error($terms)) {
            return;
        }

        echo '<p class="knk-product-category">' . esc_html(reset($terms)->name) . '</p>';
    }
}
