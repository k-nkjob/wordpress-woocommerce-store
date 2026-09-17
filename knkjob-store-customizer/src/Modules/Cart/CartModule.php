<?php
namespace KNKJOB\StoreCustomizer\Modules\Cart;

use KNKJOB\StoreCustomizer\Core\Assets;

if (!defined('ABSPATH')) {
    exit;
}

final class CartModule {
    public static function register() {
        add_action('woocommerce_before_cart', array(__CLASS__, 'notice'));
        add_action('wp_enqueue_scripts', array(__CLASS__, 'enqueue'));
        add_filter('the_title', array(__CLASS__, 'cart_title'), 20, 2);
    }

    public static function cart_title($title, $post_id) {
        if (!is_admin() && is_cart() && (int) $post_id === (int) wc_get_page_id('cart')) {
            return 'カート';
        }

        return $title;
    }

    public static function enqueue() {
        if (is_admin() || !is_cart()) {
            return;
        }

        Assets::style('knkjob-sc-cart', 'assets/css/cart.css', array('knkjob-sc-common'));
        Assets::script('knkjob-sc-row-equalizer', 'assets/js/lib/row-equalizer.js');
        Assets::script('knkjob-sc-action-labels', 'assets/js/lib/action-labels.js');
        Assets::script(
            'knkjob-sc-cart',
            'assets/js/modules/cart.js',
            array('knkjob-sc-row-equalizer', 'knkjob-sc-action-labels')
        );
    }

    public static function notice() {
        wc_print_notice(
            'ポートフォリオ用デモストアです。実際の決済・発送は行われません。',
            'notice'
        );
    }
}
