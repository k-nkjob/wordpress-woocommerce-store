<?php
/**
 * Plugin Name: KNKJOB Store Customizer
 * Description: Modular WooCommerce portfolio customizations for KNKJOB STORE.
 * Version: 0.9.0
 * Author: KNKJOB
 * License: GPL-2.0-or-later
 */

if (!defined('ABSPATH')) {
    exit;
}

define('KNKJOB_SC_VERSION', '0.9.0');
define('KNKJOB_SC_FILE', __FILE__);
define('KNKJOB_SC_PATH', plugin_dir_path(__FILE__));
define('KNKJOB_SC_URL', plugin_dir_url(__FILE__));

$knkjob_sc_files = array(
    'src/Core/Assets.php',
    'src/Core/Activation.php',
    'src/Core/Plugin.php',
    'src/Core/ProductLabels.php',
    'src/Modules/Shell/ShellModule.php',
    'src/Modules/Home/HomeModule.php',
    'src/Modules/Archive/ArchiveModule.php',
    'src/Modules/Breadcrumb/BreadcrumbModule.php',
    'src/Modules/Product/ProductModule.php',
    'src/Modules/Cart/CartModule.php',
    'src/Modules/Checkout/CheckoutModule.php',
    'src/Modules/Account/AccountModule.php',
);

foreach ($knkjob_sc_files as $knkjob_sc_file) {
    require_once KNKJOB_SC_PATH . $knkjob_sc_file;
}

register_activation_hook(
    KNKJOB_SC_FILE,
    array('KNKJOB\\StoreCustomizer\\Core\\Activation', 'activate')
);

\KNKJOB\StoreCustomizer\Core\Plugin::boot();
