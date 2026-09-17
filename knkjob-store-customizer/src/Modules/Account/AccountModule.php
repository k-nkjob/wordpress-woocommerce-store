<?php
namespace KNKJOB\StoreCustomizer\Modules\Account;

use KNKJOB\StoreCustomizer\Core\Assets;

if (!defined('ABSPATH')) {
    exit;
}

final class AccountModule {
    public static function register() {
        add_filter('pre_option_woocommerce_enable_myaccount_registration', array(__CLASS__, 'disable_registration'));
        add_filter('pre_option_woocommerce_enable_signup_and_login_from_checkout', array(__CLASS__, 'disable_registration'));
        add_filter('woocommerce_checkout_registration_enabled', '__return_false', 99);

        add_action('wp_enqueue_scripts', array(__CLASS__, 'enqueue'));
    }

    public static function disable_registration() {
        return 'no';
    }

    public static function enqueue() {
        if (is_admin() || !is_account_page()) {
            return;
        }

        Assets::style('knkjob-sc-account', 'assets/css/account.css', array('knkjob-sc-common'));
        Assets::script('knkjob-sc-account', 'assets/js/modules/account.js');
    }
}
