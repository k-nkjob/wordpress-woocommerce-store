<?php
namespace KNKJOB\StoreCustomizer\Modules\Home;

use KNKJOB\StoreCustomizer\Core\Assets;

if (!defined('ABSPATH')) {
    exit;
}

final class HomeModule {
    public static function register() {
        add_shortcode('knkjob_store_home', array(__CLASS__, 'shortcode'));
        add_filter('the_title', array(__CLASS__, 'hide_front_title'), 10, 2);
        add_action('wp_enqueue_scripts', array(__CLASS__, 'enqueue'));
    }

    public static function enqueue() {
        if (is_admin() || !is_front_page()) {
            return;
        }

        Assets::style('knkjob-sc-home', 'assets/css/home.css', array('knkjob-sc-common'));
        Assets::script('knkjob-sc-row-equalizer', 'assets/js/lib/row-equalizer.js');
        Assets::script('knkjob-sc-drag-scroll', 'assets/js/lib/drag-scroll.js');
        Assets::script(
            'knkjob-sc-home',
            'assets/js/modules/home.js',
            array('knkjob-sc-row-equalizer', 'knkjob-sc-drag-scroll')
        );
    }

    public static function hide_front_title($title, $post_id) {
        if (!is_admin() && is_front_page()) {
            $front_id = (int) get_option('page_on_front');

            if (
                $front_id &&
                (int) $post_id === $front_id &&
                get_post_field('post_name', $post_id) === 'store-home'
            ) {
                return '';
            }
        }

        return $title;
    }

    private static function carousel_taxonomy() {
        /**
         * Change this filter to `product_tag` later if the store begins
         * using WooCommerce tags as the primary carousel grouping.
         */
        return apply_filters('knkjob_store_home_carousel_taxonomy', 'product_cat');
    }

    private static function carousel_terms($taxonomy) {
        $args = array(
            'taxonomy'   => $taxonomy,
            'hide_empty' => true,
        );

        if ($taxonomy === 'product_cat') {
            $default_cat = (int) get_option('default_product_cat');

            if ($default_cat > 0) {
                $args['exclude'] = array($default_cat);
            }
        }

        $terms = get_terms($args);

        return is_wp_error($terms) ? array() : $terms;
    }

    public static function shortcode() {
        if (!class_exists('WooCommerce')) {
            return '<div class="knk-container knk-home-shell"><p>WooCommerceを有効化してください。</p></div>';
        }

        $shop_url = wc_get_page_permalink('shop');

        $taxonomy          = self::carousel_taxonomy();
        $carousel_terms    = self::carousel_terms($taxonomy);
        $carousel_products = wc_get_products(array(
            'status'  => 'publish',
            'limit'   => 24,
            'orderby' => 'date',
            'order'   => 'DESC',
        ));

        /* One product query powers both carousel and Latest Products. */
        $featured = array_slice($carousel_products, 0, 4);

        $shirt_id = wc_get_product_id_by_sku('KJS-TS-001');

        ob_start();
        ?>
        <div class="knk-container knk-home-shell">
            <section class="knk-hero" aria-labelledby="knk-hero-title">
                <div class="knk-hero__copy">
                    <p class="knk-eyebrow">MINIMAL / EVERYDAY</p>
                    <h1 id="knk-hero-title">Simple pieces.<br>Built for daily life.</h1>
                    <p class="knk-hero__lead">
                        Clean, functional essentials designed for a calm everyday wardrobe.
                    </p>
                    <div class="knk-hero__actions">
                        <a class="knk-btn knk-btn--dark" href="<?php echo esc_url($shop_url); ?>">Shop collection</a>
                        <?php if ($shirt_id) : ?>
                            <a class="knk-btn knk-btn--ghost" href="<?php echo esc_url(get_permalink($shirt_id)); ?>">View T-Shirt</a>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="knk-hero__visual">
                    <?php
                    $hero_product = $shirt_id ? wc_get_product($shirt_id) : null;

                    if (!$hero_product && !empty($featured)) {
                        $hero_product = reset($featured);
                    }

                    if ($hero_product instanceof \WC_Product && $hero_product->get_image_id()) {
                        echo wp_get_attachment_image(
                            $hero_product->get_image_id(),
                            'large',
                            false,
                            array(
                                'class'         => 'knk-hero__image',
                                'loading'       => 'eager',
                                'fetchpriority' => 'high',
                                'decoding'      => 'async',
                            )
                        );
                    } else {
                        echo '<div class="knk-hero__placeholder">KNKJOB STORE</div>';
                    }
                    ?>
                </div>
            </section>

            <section class="knk-section knk-category-carousel-section" aria-labelledby="knk-category-title">
                <div class="knk-section__heading">
                    <div>
                        <p class="knk-eyebrow">SHOP BY CATEGORY</p>
                        <h2 id="knk-category-title">Everyday essentials</h2>
                    </div>
                    <a class="knk-text-link" href="<?php echo esc_url($shop_url); ?>">View all products →</a>
                </div>

                <div class="knk-carousel-toolbar">
                    <div class="knk-carousel-filters" role="tablist" aria-label="Product groups">
                        <button
                            class="knk-carousel-filter is-active"
                            type="button"
                            data-knk-category="all"
                            aria-selected="true"
                        >All</button>

                        <?php foreach ($carousel_terms as $term) : ?>
                            <button
                                class="knk-carousel-filter"
                                type="button"
                                data-knk-category="<?php echo esc_attr($term->slug); ?>"
                                aria-selected="false"
                            >
                                <?php echo esc_html($term->name); ?>
                            </button>
                        <?php endforeach; ?>
                    </div>

                    <div class="knk-carousel-controls" aria-label="Carousel controls">
                        <button type="button" class="knk-carousel-arrow" data-knk-carousel-prev aria-label="Previous products">←</button>
                        <button type="button" class="knk-carousel-arrow" data-knk-carousel-next aria-label="Next products">→</button>
                    </div>
                </div>

                <div class="knk-product-carousel" data-knk-product-carousel>
                    <div class="knk-product-carousel__track" data-knk-drag-scroll>
                        <?php foreach ($carousel_products as $product) : ?>
                            <?php
                            if (!$product instanceof \WC_Product) {
                                continue;
                            }

                            $terms = get_the_terms($product->get_id(), $taxonomy);
                            $slugs = array();
                            $group_name = '';

                            if (!empty($terms) && !is_wp_error($terms)) {
                                foreach ($terms as $term) {
                                    $slugs[] = $term->slug;
                                }

                                $group_name = reset($terms)->name;
                            }
                            ?>
                            <article
                                class="knk-carousel-card"
                                data-knk-product-card
                                data-knk-categories="<?php echo esc_attr(implode(' ', $slugs)); ?>"
                            >
                                <a class="knk-carousel-card__image-link" href="<?php echo esc_url(get_permalink($product->get_id())); ?>">
                                    <?php echo $product->get_image('woocommerce_thumbnail', array('class' => 'knk-carousel-card__image')); ?>
                                </a>

                                <div class="knk-carousel-card__body">
                                    <?php if ($group_name) : ?>
                                        <p class="knk-carousel-card__category"><?php echo esc_html($group_name); ?></p>
                                    <?php endif; ?>

                                    <h3>
                                        <a href="<?php echo esc_url(get_permalink($product->get_id())); ?>">
                                            <?php echo esc_html($product->get_name()); ?>
                                        </a>
                                    </h3>

                                    <div class="knk-carousel-card__price">
                                        <?php echo wp_kses_post($product->get_price_html()); ?>
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>

            <section class="knk-section" aria-labelledby="knk-featured-title">
                <div class="knk-section__heading">
                    <div>
                        <p class="knk-eyebrow">FEATURED</p>
                        <h2 id="knk-featured-title">Latest products</h2>
                    </div>
                </div>

                <div class="knk-feature-grid" data-knk-home-feature-grid>
                    <?php if (!empty($featured)) : ?>
                        <?php foreach ($featured as $product) : ?>
                            <?php if (!$product instanceof \WC_Product) continue; ?>
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

                                    <h3>
                                        <a href="<?php echo esc_url(get_permalink($product->get_id())); ?>">
                                            <?php echo esc_html($product->get_name()); ?>
                                        </a>
                                    </h3>

                                    <div class="knk-feature-card__price">
                                        <?php echo wp_kses_post($product->get_price_html()); ?>
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </section>

            <section class="knk-values" aria-label="Store values">
                <div>
                    <span>01</span>
                    <strong>Minimal design</strong>
                    <p>余白と情報設計を重視した、シンプルで見やすいEC UI。</p>
                </div>
                <div>
                    <span>02</span>
                    <strong>Stock aware</strong>
                    <p>SKU・在庫・バリエーションをWooCommerceで管理。</p>
                </div>
                <div>
                    <span>03</span>
                    <strong>Responsive</strong>
                    <p>PC・タブレット・スマートフォンに対応。</p>
                </div>
            </section>

            <section class="knk-tech-note">
                <p class="knk-eyebrow">PORTFOLIO IMPLEMENTATION</p>
                <p>WordPress / WooCommerce / PHP / MySQL / JavaScript / Responsive UI / WooCommerce Hooks</p>
            </section>
        </div>
        <?php

        return ob_get_clean();
    }
}
