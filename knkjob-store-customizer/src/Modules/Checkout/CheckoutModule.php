<?php
namespace KNKJOB\StoreCustomizer\Modules\Checkout;

use KNKJOB\StoreCustomizer\Core\Assets;

if (!defined('ABSPATH')) {
    exit;
}

final class CheckoutModule {
    public static function register() {
        add_action('woocommerce_before_checkout_form', array(__CLASS__, 'notice'), 5);
        add_action('wp_enqueue_scripts', array(__CLASS__, 'enqueue'));
    }

    public static function enqueue() {
        if (is_admin() || !is_checkout()) {
            return;
        }

        Assets::style('knkjob-sc-checkout', 'assets/css/checkout.css', array('knkjob-sc-common'));
    }

    public static function notice() {
        wc_print_notice(
            'ポートフォリオ用デモストアです。実際の決済・発送は行われません。',
            'notice'
        );
    }
}
