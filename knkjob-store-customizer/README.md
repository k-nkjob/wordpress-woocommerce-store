# KNKJOB Store Customizer v0.2.1

## What this update does
- Creates a dedicated EC home page and sets it as the WordPress front page.
- Hero section, category cards, latest products, value/tech sections.
- Breadcrumb: Home -> Shop -> Category -> Product.
- Removes Sample Page from classic/block navigation output.
- Responsive product archive styling.
- Product-card category labels and NEW badges.
- Product detail styling, shipping note, demo notice.
- Low-stock messaging.
- Custom product spec tab.
- Adds WooCommerce admin fields for Material and Shipping note.
- Cart / Checkout demo notice.
- Does not modify WooCommerce core or the parent theme.

## Update
Upload this ZIP from:
WordPress Admin -> Plugins -> Add Plugin -> Upload Plugin.

Because v0.1 is already installed, WordPress should show a screen asking whether to replace the current plugin with the uploaded version. Choose Replace current with uploaded.

## After activation/update
If the front page does not change immediately, deactivate and reactivate this plugin once. The activation hook creates `store-home` and assigns it as the front page.

## Rollback
Keep your previous v0.1 ZIP. Re-uploading the old ZIP will revert the customizer code.

## v0.2.1 cleanup
- Site title is normalized to `KNKJOB STORE`.
- Default `Sample Page / サンプルページ` is removed from dynamic block navigation too.
- Duplicate `KNKJOB STORE` navigation item is suppressed when the theme already shows the site title.
