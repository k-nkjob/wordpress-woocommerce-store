<?php
namespace KNKJOB\StoreCustomizer\Core;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Store-wide product action wording.
 *
 * This is intentionally a cross-cutting service: every page that exposes
 * a WooCommerce product action should use the same short vocabulary.
 */
final class ProductLabels {
    public static function register() {
        add_filter('woocommerce_product_add_to_cart_text', array(__CLASS__, 'loop_label'), 99, 2);
        add_filter('woocommerce_product_single_add_to_cart_text', array(__CLASS__, 'single_label'), 99);
    }

    public static function loop_label($text, $product) {
        if (!$product instanceof \WC_Product) {
            return $text;
        }

        if (!$product->is_purchasable() || !$product->is_in_stock()) {
            return '続きを読む';
        }

        if ($product->is_type('variable')) {
            return 'オプションを選択';
        }

        if ($product->is_type('external')) {
            return '続きを読む';
        }

        return 'カートに追加';
    }

    public static function single_label($text) {
        return 'カートに追加';
    }
}
