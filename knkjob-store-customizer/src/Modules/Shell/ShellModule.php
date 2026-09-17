<?php
namespace KNKJOB\StoreCustomizer\Modules\Shell;

use KNKJOB\StoreCustomizer\Core\Assets;

if (!defined('ABSPATH')) {
    exit;
}

final class ShellModule {
    public static function register() {
        add_action('wp_enqueue_scripts', array(__CLASS__, 'enqueue'), 99);
        add_filter('body_class', array(__CLASS__, 'body_class'));

        add_action('wp_body_open', array(__CLASS__, 'render_header'), 5);
        add_action('wp_footer', array(__CLASS__, 'render_footer'), 5);

        add_filter('wp_list_pages_excludes', array(__CLASS__, 'exclude_sample_page'));
    }

    public static function enqueue() {
        if (is_admin()) {
            return;
        }

        Assets::style('knkjob-sc-common', 'assets/css/common.css');
        Assets::style('knkjob-sc-layout', 'assets/css/layout.css', array('knkjob-sc-common'));
        Assets::style('knkjob-sc-shell', 'assets/css/shell.css', array('knkjob-sc-layout'));
    }

    public static function body_class($classes) {
        $classes[] = 'knkjob-store-customized';

        if (is_front_page()) {
            $classes[] = 'knkjob-store-home';
        }

        return $classes;
    }

    private static function sample_page_id() {
        $page = get_page_by_path('sample-page');
        return $page ? (int) $page->ID : 0;
    }

    public static function exclude_sample_page($excluded_ids) {
        $id = self::sample_page_id();

        if ($id && !in_array($id, $excluded_ids, true)) {
            $excluded_ids[] = $id;
        }

        return $excluded_ids;
    }

    private static function page_url($page_name, $fallback = '') {
        if (!function_exists('wc_get_page_permalink')) {
            return $fallback ?: home_url('/');
        }

        $url = wc_get_page_permalink($page_name);

        return $url ? $url : ($fallback ?: home_url('/'));
    }

    public static function render_header() {
        if (is_admin()) {
            return;
        }

        $home_url     = home_url('/');
        $shop_url     = self::page_url('shop', $home_url);
        $cart_url     = self::page_url('cart', $shop_url);
        $account_url  = self::page_url('myaccount', $home_url);
        $checkout_url = function_exists('wc_get_checkout_url') ? wc_get_checkout_url() : self::page_url('checkout', $cart_url);
        ?>
        <header class="knk-shell-header" data-knk-shell="header">
            <div class="knk-container knk-shell-header__inner">
                <a class="knk-shell-brand" href="<?php echo esc_url($home_url); ?>">
                    KNKJOB STORE
                </a>

                <nav class="knk-shell-nav" aria-label="Store navigation">
                    <a href="<?php echo esc_url($cart_url); ?>">カート</a>
                    <a href="<?php echo esc_url($shop_url); ?>">ショップ</a>
                    <a href="<?php echo esc_url($account_url); ?>">マイアカウント</a>
                    <a href="<?php echo esc_url($checkout_url); ?>">支払い</a>
                    <a class="knk-shell-icon-link" href="<?php echo esc_url($account_url); ?>" aria-label="アカウント">
                        <span aria-hidden="true">♙</span>
                    </a>
                    <a class="knk-shell-icon-link" href="<?php echo esc_url($cart_url); ?>" aria-label="カート">
                        <span aria-hidden="true">🛒</span>
                    </a>
                </nav>
            </div>
        </header>
        <?php
    }

    public static function render_footer() {
        if (is_admin()) {
            return;
        }

        $home_url = home_url('/');
        $shop_url = self::page_url('shop', $home_url);

        $demo_links_left = array(
            'ブログ',
            'このサイトについて',
            'よくある質問',
            '投稿者',
        );

        $demo_links_right = array(
            'イベント',
            'パターン',
            'テーマ',
        );
        ?>
        <footer class="knk-shell-footer" data-knk-shell="footer">
            <div class="knk-container knk-shell-footer__inner">
                <div class="knk-shell-footer__brand">
                    <a href="<?php echo esc_url($home_url); ?>">KNKJOB STORE</a>
                    <p>WordPress / WooCommerce EC Portfolio</p>
                </div>

                <div class="knk-shell-footer__links">
                    <div>
                        <?php foreach ($demo_links_left as $label) : ?>
                            <a href="<?php echo esc_url($home_url); ?>" title="デモサイトのためトップページへ移動します">
                                <?php echo esc_html($label); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>

                    <div>
                        <?php foreach ($demo_links_right as $label) : ?>
                            <a href="<?php echo esc_url($home_url); ?>" title="デモサイトのためトップページへ移動します">
                                <?php echo esc_html($label); ?>
                            </a>
                        <?php endforeach; ?>
                        <a href="<?php echo esc_url($shop_url); ?>">ショップ</a>
                    </div>
                </div>

                <p class="knk-shell-footer__note">
                    ※ デモサイトのため、ブログ・イベントなど未実装のリンクはトップページへ移動します。ショップは商品一覧へ移動します。
                </p>
            </div>
        </footer>
        <?php
    }
}
