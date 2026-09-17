<?php
namespace KNKJOB\StoreCustomizer\Modules\Product;

use KNKJOB\StoreCustomizer\Core\Assets;

if (!defined('ABSPATH')) {
    exit;
}

final class ProductModule {
    public static function register() {
        add_action('wp_enqueue_scripts', array(__CLASS__, 'enqueue'));

        add_filter('woocommerce_product_tabs', array(__CLASS__, 'tabs'));
        add_action('woocommerce_product_options_general_product_data', array(__CLASS__, 'admin_fields'));
        add_action('woocommerce_process_product_meta', array(__CLASS__, 'save_admin_fields'));

        add_action('woocommerce_single_product_summary', array(__CLASS__, 'low_stock'), 28);
        add_action('woocommerce_after_add_to_cart_form', array(__CLASS__, 'after_cart_form'), 15);
    }

    public static function enqueue() {
        if (is_admin() || !is_product()) {
            return;
        }

        Assets::style('knkjob-sc-product', 'assets/css/product.css', array('knkjob-sc-common'));
        Assets::script('knkjob-sc-product', 'assets/js/modules/product.js', array('jquery'));
    }

    private static function fallback_specs($product) {
        $sku = $product instanceof \WC_Product ? $product->get_sku() : '';

        if (strpos($sku, 'KJS-TS-') === 0 || $sku === 'KJS-TS-001') {
            return array(
                'material' => 'Cotton 100%',
                'fit'      => 'Oversized',
                'shipping' => '通常 2〜3 営業日',
            );
        }

        if ($sku === 'KJS-BAG-001') {
            return array(
                'material' => 'Cotton Canvas',
                'fit'      => 'One Size',
                'shipping' => '通常 2〜3 営業日',
            );
        }

        return array(
            'material' => '',
            'fit'      => '',
            'shipping' => '通常 2〜3 営業日',
        );
    }

    public static function tabs($tabs) {
        $tabs['knk_product_specs'] = array(
            'title'    => '商品仕様',
            'priority' => 25,
            'callback' => array(__CLASS__, 'render_specs'),
        );

        return $tabs;
    }

    public static function render_specs() {
        global $product;

        if (!$product instanceof \WC_Product) {
            return;
        }

        $fallback = self::fallback_specs($product);
        $material = get_post_meta($product->get_id(), '_knk_material', true);
        $shipping = get_post_meta($product->get_id(), '_knk_shipping_note', true);

        if (!$material) {
            $material = $fallback['material'];
        }

        if (!$shipping) {
            $shipping = $fallback['shipping'];
        }

        echo '<div class="knk-product-specs">';

        if ($material) {
            echo '<div><span>Material</span><strong>' . esc_html($material) . '</strong></div>';
        }

        if (!empty($fallback['fit'])) {
            echo '<div><span>Fit / Size</span><strong>' . esc_html($fallback['fit']) . '</strong></div>';
        }

        if ($shipping) {
            echo '<div><span>Shipping</span><strong>' . esc_html($shipping) . '</strong></div>';
        }

        echo '</div>';
    }

    public static function admin_fields() {
        echo '<div class="options_group">';

        woocommerce_wp_text_input(array(
            'id'          => '_knk_material',
            'label'       => '素材 / Material',
            'placeholder' => '例: Cotton 100%',
            'desc_tip'    => true,
            'description' => '商品仕様タブに表示する素材情報。',
        ));

        woocommerce_wp_text_input(array(
            'id'          => '_knk_shipping_note',
            'label'       => '発送目安',
            'placeholder' => '例: 通常 2〜3 営業日',
            'desc_tip'    => true,
            'description' => '商品仕様タブに表示する発送目安。',
        ));

        echo '</div>';
    }

    public static function save_admin_fields($post_id) {
        if (isset($_POST['_knk_material'])) {
            update_post_meta(
                $post_id,
                '_knk_material',
                sanitize_text_field(wp_unslash($_POST['_knk_material']))
            );
        }

        if (isset($_POST['_knk_shipping_note'])) {
            update_post_meta(
                $post_id,
                '_knk_shipping_note',
                sanitize_text_field(wp_unslash($_POST['_knk_shipping_note']))
            );
        }
    }

    public static function low_stock() {
        global $product;

        if (!$product instanceof \WC_Product) {
            return;
        }

        if ($product->is_type('variable')) {
            echo '<p class="knk-low-stock" data-knk-low-stock hidden></p>';
            return;
        }

        if (!$product->managing_stock()) {
            return;
        }

        $qty = $product->get_stock_quantity();

        if (is_numeric($qty) && $qty > 0 && $qty <= 5) {
            echo '<p class="knk-low-stock">残り ' . esc_html((string) $qty) . ' 点</p>';
        }
    }

    public static function after_cart_form() {
        echo '<div class="knk-shipping-note">';
        echo '<span>SHIPPING</span><strong>通常 2〜3 営業日で発送</strong>';
        echo '</div>';

        echo '<div class="knk-demo-notice" role="note">';
        echo '<strong>Portfolio Demo</strong>';
        echo '<span>このストアは自主制作ポートフォリオです。実際の決済・発送は行われません。</span>';
        echo '</div>';
    }
}
