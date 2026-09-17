<?php
namespace KNKJOB\StoreCustomizer\Core;

use KNKJOB\StoreCustomizer\Modules\Shell\ShellModule;
use KNKJOB\StoreCustomizer\Modules\Home\HomeModule;
use KNKJOB\StoreCustomizer\Modules\Archive\ArchiveModule;
use KNKJOB\StoreCustomizer\Modules\Breadcrumb\BreadcrumbModule;
use KNKJOB\StoreCustomizer\Modules\Product\ProductModule;
use KNKJOB\StoreCustomizer\Modules\Cart\CartModule;
use KNKJOB\StoreCustomizer\Modules\Checkout\CheckoutModule;
use KNKJOB\StoreCustomizer\Modules\Account\AccountModule;

if (!defined('ABSPATH')) {
    exit;
}

final class Plugin {
    public static function boot() {
        ProductLabels::register();
        add_action('admin_init', array(__CLASS__, 'upgrade'));
        add_action('admin_notices', array(__CLASS__, 'activation_notice'));

        $modules = array(
            ShellModule::class,
            HomeModule::class,
            ArchiveModule::class,
            BreadcrumbModule::class,
            ProductModule::class,
            CartModule::class,
            CheckoutModule::class,
            AccountModule::class,
        );

        foreach ($modules as $module) {
            $module::register();
        }
    }

    public static function upgrade() {
        $installed = get_option('knkjob_store_customizer_version');

        if ($installed === KNKJOB_SC_VERSION) {
            return;
        }

        Activation::ensure_front_page();
        update_option('blogname', 'KNKJOB STORE');
        update_option('knkjob_store_customizer_version', KNKJOB_SC_VERSION);
    }

    public static function activation_notice() {
        if (!get_transient('knkjob_store_customizer_activated')) {
            return;
        }

        delete_transient('knkjob_store_customizer_activated');

        echo '<div class="notice notice-success is-dismissible"><p>';
        echo '<strong>KNKJOB Store Customizer:</strong> ';
        echo 'モジュール構成のECカスタマイズを有効化しました。';
        echo '</p></div>';
    }
}
