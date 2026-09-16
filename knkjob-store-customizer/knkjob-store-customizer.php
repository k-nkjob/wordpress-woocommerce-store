<?php
/**
 * Plugin Name: KNKJOB Store Customizer
 * Description: Portfolio-focused WooCommerce UI/UX and PHP customizations for KNKJOB STORE.
 * Version: 0.2.1
 * Author: KNKJOB
 * License: GPL-2.0-or-later
 */

if (!defined('ABSPATH')) {
    exit;
}

define('KNKJOB_STORE_CUSTOMIZER_VERSION', '0.2.1');

function knkjob_store_prepare_front_page() {
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
            wp_update_post(array('ID' => $page_id, 'post_content' => '[knkjob_store_home]'));
        }
    }

    if (!is_wp_error($page_id) && $page_id) {
        update_option('show_on_front', 'page');
        update_option('page_on_front', (int) $page_id);
    }

    update_option('blogname', 'KNKJOB STORE');
}

register_activation_hook(__FILE__, 'knkjob_store_prepare_front_page');

add_action('admin_init', function () {
    if (get_option('knkjob_store_customizer_version') === KNKJOB_STORE_CUSTOMIZER_VERSION) {
        return;
    }

    knkjob_store_prepare_front_page();
    update_option('knkjob_store_customizer_version', KNKJOB_STORE_CUSTOMIZER_VERSION);
});

add_action('wp_enqueue_scripts', function () {
    if (is_admin()) {
        return;
    }

    wp_enqueue_style(
        'knkjob-store-customizer',
        plugin_dir_url(__FILE__) . 'assets/store-customizer.css',
        array(),
        KNKJOB_STORE_CUSTOMIZER_VERSION
    );

    wp_enqueue_script(
        'knkjob-store-customizer',
        plugin_dir_url(__FILE__) . 'assets/store-customizer.js',
        array('jquery'),
        KNKJOB_STORE_CUSTOMIZER_VERSION,
        true
    );
}, 30);

function knkjob_store_home_shortcode() {
    if (!class_exists('WooCommerce')) {
        return '<div class="knk-home-shell"><p>WooCommerceを有効化してください。</p></div>';
    }

    $shop_url = wc_get_page_permalink('shop');
    $featured = wc_get_products(array(
        'status'  => 'publish',
        'limit'   => 4,
        'orderby' => 'date',
        'order'   => 'DESC',
    ));

    $shirt_id = wc_get_product_id_by_sku('KJS-TS-001');
    $hero_product = $shirt_id ? wc_get_product($shirt_id) : null;
    if (!$hero_product && !empty($featured)) {
        $hero_product = reset($featured);
    }

    ob_start();
    ?>
    <main class="knk-home-shell">
        <section class="knk-hero" aria-labelledby="knk-hero-title">
            <div class="knk-hero__copy">
                <p class="knk-eyebrow">MINIMAL / EVERYDAY</p>
                <h1 id="knk-hero-title">Simple pieces.<br>Built for daily life.</h1>
                <p class="knk-hero__lead">Clean, functional essentials designed for a calm everyday wardrobe.</p>
                <div class="knk-hero__actions">
                    <a class="knk-btn knk-btn--dark" href="<?php echo esc_url($shop_url); ?>">Shop collection</a>
                    <?php if ($shirt_id) : ?>
                        <a class="knk-btn knk-btn--ghost" href="<?php echo esc_url(get_permalink($shirt_id)); ?>">View T-Shirt</a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="knk-hero__visual">
                <?php
                if ($hero_product instanceof WC_Product && $hero_product->get_image_id()) {
                    echo wp_get_attachment_image(
                        $hero_product->get_image_id(),
                        'large',
                        false,
                        array('class' => 'knk-hero__image', 'loading' => 'eager')
                    );
                } else {
                    echo '<div class="knk-hero__placeholder">KNKJOB STORE</div>';
                }
                ?>
            </div>
        </section>

        <section class="knk-section" aria-labelledby="knk-category-title">
            <div class="knk-section__heading">
                <div>
                    <p class="knk-eyebrow">SHOP BY CATEGORY</p>
                    <h2 id="knk-category-title">Everyday essentials</h2>
                </div>
                <a class="knk-text-link" href="<?php echo esc_url($shop_url); ?>">View all products →</a>
            </div>
            <div class="knk-category-grid">
                <?php
                $categories = array(
                    array('slug' => 'tops', 'name' => 'Tops', 'number' => '01'),
                    array('slug' => 'accessories', 'name' => 'Accessories', 'number' => '02'),
                );

                foreach ($categories as $item) :
                    $term = get_term_by('slug', $item['slug'], 'product_cat');
                    if (!$term || is_wp_error($term)) {
                        $term = get_term_by('name', $item['name'], 'product_cat');
                    }

                    $name = ($term && !is_wp_error($term)) ? $term->name : $item['name'];
                    $url  = ($term && !is_wp_error($term)) ? get_term_link($term) : $shop_url;
                    $image = '';

                    if ($term && !is_wp_error($term)) {
                        $products = wc_get_products(array(
                            'status' => 'publish',
                            'limit' => 1,
                            'category' => array($term->slug),
                        ));

                        if (!empty($products) && $products[0] instanceof WC_Product && $products[0]->get_image_id()) {
                            $image = wp_get_attachment_image(
                                $products[0]->get_image_id(),
                                'medium_large',
                                false,
                                array('class' => 'knk-category-card__image')
                            );
                        }
                    }
                    ?>
                    <a class="knk-category-card" href="<?php echo esc_url($url); ?>">
                        <span class="knk-category-card__number"><?php echo esc_html($item['number']); ?></span>
                        <div class="knk-category-card__media">
                            <?php echo $image ? $image : '<span class="knk-category-card__placeholder"></span>'; ?>
                        </div>
                        <div class="knk-category-card__body">
                            <h3><?php echo esc_html($name); ?></h3>
                            <span>Explore →</span>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="knk-section" aria-labelledby="knk-featured-title">
            <div class="knk-section__heading">
                <div>
                    <p class="knk-eyebrow">FEATURED</p>
                    <h2 id="knk-featured-title">Latest products</h2>
                </div>
            </div>
            <div class="knk-feature-grid">
                <?php foreach ($featured as $product) : ?>
                    <?php if (!$product instanceof WC_Product) { continue; } ?>
                    <article class="knk-feature-card">
                        <a class="knk-feature-card__image-link" href="<?php echo esc_url(get_permalink($product->get_id())); ?>">
                            <?php echo $product->get_image('woocommerce_thumbnail', array('class' => 'knk-feature-card__image')); ?>
                        </a>
                        <div class="knk-feature-card__body">
                            <?php
                            $terms = get_the_terms($product->get_id(), 'product_cat');
                            if (!empty($terms) && !is_wp_error($terms)) :
                            ?>
                                <p class="knk-feature-card__category"><?php echo esc_html(reset($terms)->name); ?></p>
                            <?php endif; ?>
                            <h3><a href="<?php echo esc_url(get_permalink($product->get_id())); ?>"><?php echo esc_html($product->get_name()); ?></a></h3>
                            <div class="knk-feature-card__price"><?php echo wp_kses_post($product->get_price_html()); ?></div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="knk-values" aria-label="Store values">
            <div><span>01</span><strong>Minimal design</strong><p>余白と情報設計を重視した、シンプルで見やすいEC UI。</p></div>
            <div><span>02</span><strong>Stock aware</strong><p>SKU・在庫・バリエーションをWooCommerceで管理。</p></div>
            <div><span>03</span><strong>Responsive</strong><p>PC・タブレット・スマートフォンに対応。</p></div>
        </section>

        <section class="knk-tech-note">
            <p class="knk-eyebrow">PORTFOLIO IMPLEMENTATION</p>
            <p>WordPress / WooCommerce / PHP / MySQL / JavaScript / Responsive UI / WooCommerce Hooks</p>
        </section>
    </main>
    <?php

    return ob_get_clean();
}
add_shortcode('knkjob_store_home', 'knkjob_store_home_shortcode');

add_filter('the_title', function ($title, $post_id) {
    if (!is_admin() && is_front_page()) {
        $front_id = (int) get_option('page_on_front');
        if ($front_id && (int) $post_id === $front_id && get_post_field('post_name', $post_id) === 'store-home') {
            return '';
        }
    }

    return $title;
}, 10, 2);

add_filter('woocommerce_get_breadcrumb', function ($crumbs) {
    if (!function_exists('is_product') || !(is_product() || is_product_category() || is_product_tag())) {
        return $crumbs;
    }

    $shop_id = wc_get_page_id('shop');
    if ($shop_id <= 0) {
        return $crumbs;
    }

    $shop_url = get_permalink($shop_id);
    $shop_label = get_the_title($shop_id);

    foreach ($crumbs as $crumb) {
        if (!empty($crumb[1]) && untrailingslashit($crumb[1]) === untrailingslashit($shop_url)) {
            return $crumbs;
        }
    }

    array_splice($crumbs, 1, 0, array(array($shop_label, $shop_url)));
    return $crumbs;
}, 20);

function knkjob_store_sample_page_id() {
    $page = get_page_by_path('sample-page');
    return $page ? (int) $page->ID : 0;
}

add_filter('wp_list_pages_excludes', function ($excluded_ids) {
    $id = knkjob_store_sample_page_id();
    if ($id && !in_array($id, $excluded_ids, true)) {
        $excluded_ids[] = $id;
    }
    return $excluded_ids;
});

add_filter('wp_nav_menu_objects', function ($items) {
    $id = knkjob_store_sample_page_id();

    foreach ($items as $key => $item) {
        $url = isset($item->url) ? $item->url : '';
        if (($id && (int) $item->object_id === $id) || strpos($url, '/sample-page') !== false) {
            unset($items[$key]);
        }
    }

    return $items;
});

add_filter('render_block', function ($content, $block) {
    $name = $block['blockName'] ?? '';
    if (!in_array($name, array('core/navigation', 'core/navigation-link', 'core/page-list'), true)) {
        return $content;
    }

    $content = preg_replace(
        '#<li[^>]*>.*?<a[^>]*href=["\'][^"\']*/sample-page/?["\'][^>]*>.*?</a>.*?</li>#isu',
        '',
        $content
    );
    $content = preg_replace(
        '#<li[^>]*>.*?<a[^>]*>(?:Sample Page|サンプルページ)</a>.*?</li>#isu',
        '',
        $content
    );

    return $content;
}, 50, 2);

add_action('woocommerce_before_shop_loop_item_title', function () {
    global $product;

    if (!$product instanceof WC_Product) {
        return;
    }

    $created = $product->get_date_created();
    if ($created && ((time() - $created->getTimestamp()) / DAY_IN_SECONDS) <= 45) {
        echo '<span class="knk-badge-new">NEW</span>';
    }
}, 6);

add_action('woocommerce_after_shop_loop_item_title', function () {
    global $product;

    if (!$product instanceof WC_Product) {
        return;
    }

    $terms = get_the_terms($product->get_id(), 'product_cat');
    if (!empty($terms) && !is_wp_error($terms)) {
        echo '<p class="knk-product-category">' . esc_html(reset($terms)->name) . '</p>';
    }
}, 7);

function knkjob_store_product_fallback_specs($product) {
    $sku = $product instanceof WC_Product ? $product->get_sku() : '';

    if (strpos($sku, 'KJS-TS-') === 0 || $sku === 'KJS-TS-001') {
        return array('material' => 'Cotton 100%', 'fit' => 'Oversized', 'shipping' => '通常 2〜3 営業日');
    }

    if ($sku === 'KJS-BAG-001') {
        return array('material' => 'Cotton Canvas', 'fit' => 'One Size', 'shipping' => '通常 2〜3 営業日');
    }

    return array('material' => '', 'fit' => '', 'shipping' => '通常 2〜3 営業日');
}

add_filter('woocommerce_product_tabs', function ($tabs) {
    $tabs['knk_product_specs'] = array(
        'title' => '商品仕様',
        'priority' => 25,
        'callback' => function () {
            global $product;

            if (!$product instanceof WC_Product) {
                return;
            }

            $fallback = knkjob_store_product_fallback_specs($product);
            $material = get_post_meta($product->get_id(), '_knk_material', true) ?: $fallback['material'];
            $shipping = get_post_meta($product->get_id(), '_knk_shipping_note', true) ?: $fallback['shipping'];

            echo '<div class="knk-product-specs">';
            if ($material) {
                echo '<div><span>Material</span><strong>' . esc_html($material) . '</strong></div>';
            }
            if ($fallback['fit']) {
                echo '<div><span>Fit / Size</span><strong>' . esc_html($fallback['fit']) . '</strong></div>';
            }
            if ($shipping) {
                echo '<div><span>Shipping</span><strong>' . esc_html($shipping) . '</strong></div>';
            }
            echo '</div>';
        },
    );

    return $tabs;
});

add_action('woocommerce_product_options_general_product_data', function () {
    echo '<div class="options_group">';
    woocommerce_wp_text_input(array(
        'id' => '_knk_material',
        'label' => '素材 / Material',
        'placeholder' => '例: Cotton 100%',
        'desc_tip' => true,
        'description' => '商品仕様タブに表示する素材情報。',
    ));
    woocommerce_wp_text_input(array(
        'id' => '_knk_shipping_note',
        'label' => '発送目安',
        'placeholder' => '例: 通常 2〜3 営業日',
        'desc_tip' => true,
        'description' => '商品仕様タブに表示する発送目安。',
    ));
    echo '</div>';
});

add_action('woocommerce_process_product_meta', function ($post_id) {
    if (isset($_POST['_knk_material'])) {
        update_post_meta($post_id, '_knk_material', sanitize_text_field(wp_unslash($_POST['_knk_material'])));
    }
    if (isset($_POST['_knk_shipping_note'])) {
        update_post_meta($post_id, '_knk_shipping_note', sanitize_text_field(wp_unslash($_POST['_knk_shipping_note'])));
    }
});

add_action('woocommerce_single_product_summary', function () {
    global $product;

    if (!$product instanceof WC_Product) {
        return;
    }

    if ($product->is_type('variable')) {
        echo '<p class="knk-low-stock" data-knk-low-stock hidden></p>';
        return;
    }

    if ($product->managing_stock()) {
        $qty = $product->get_stock_quantity();
        if (is_numeric($qty) && $qty > 0 && $qty <= 5) {
            echo '<p class="knk-low-stock">残り ' . esc_html((string) $qty) . ' 点</p>';
        }
    }
}, 28);

add_action('woocommerce_after_add_to_cart_form', function () {
    echo '<div class="knk-shipping-note"><span>SHIPPING</span><strong>通常 2〜3 営業日で発送</strong></div>';
    echo '<div class="knk-demo-notice" role="note"><strong>Portfolio Demo</strong><span>このストアは自主制作ポートフォリオです。実際の決済・発送は行われません。</span></div>';
}, 15);

function knkjob_store_demo_cart_notice() {
    wc_print_notice('ポートフォリオ用デモストアです。実際の決済・発送は行われません。', 'notice');
}
add_action('woocommerce_before_cart', 'knkjob_store_demo_cart_notice');
add_action('woocommerce_before_checkout_form', 'knkjob_store_demo_cart_notice', 5);

add_filter('body_class', function ($classes) {
    $classes[] = 'knkjob-store-customized';
    if (is_front_page()) {
        $classes[] = 'knkjob-store-home';
    }
    return $classes;
});
