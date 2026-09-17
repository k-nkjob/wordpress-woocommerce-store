<?php
namespace KNKJOB\StoreCustomizer\Modules\Breadcrumb;

if (!defined('ABSPATH')) {
    exit;
}

final class BreadcrumbModule {
    public static function register() {
        add_filter('woocommerce_get_breadcrumb', array(__CLASS__, 'insert_shop'), 20);
    }

    public static function insert_shop($crumbs) {
        if (!(is_product() || is_product_category() || is_product_tag())) {
            return $crumbs;
        }

        $shop_id = wc_get_page_id('shop');

        if ($shop_id <= 0) {
            return $crumbs;
        }

        $shop_url   = get_permalink($shop_id);
        $shop_label = get_the_title($shop_id);

        if (!$shop_url || !$shop_label) {
            return $crumbs;
        }

        foreach ($crumbs as $crumb) {
            if (!empty($crumb[1]) && untrailingslashit($crumb[1]) === untrailingslashit($shop_url)) {
                return $crumbs;
            }
        }

        array_splice($crumbs, 1, 0, array(array($shop_label, $shop_url)));

        return $crumbs;
    }
}
