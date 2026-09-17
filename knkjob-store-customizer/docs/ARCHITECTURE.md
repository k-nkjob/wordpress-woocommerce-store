# KNKJOB Store Customizer Architecture

The plugin is organized around page ownership so a page-specific problem is fixed in that page's module instead of by a global patch.

## Ownership

- Shell: Header / Footer / global navigation
- Home: Hero / carousel / latest products
- Archive: Shop / category / tag product grids
- Breadcrumb: WooCommerce breadcrumb hierarchy
- Product: detail page / variation stock / specs
- Cart: cart / recommendations
- Checkout: checkout
- Account: My Account / demo registration policy

## Shared libraries

Shared libraries are intentionally generic and are called explicitly by page modules.

- `row-equalizer.js`: equalizes configured fields inside one grid row
- `drag-scroll.js`: adds mouse drag scrolling to one supplied element
- `action-labels.js`: normalizes WooCommerce block action wording

## Responsive policy

Each module owns its own responsive rules. Changing Cart mobile columns must not alter Shop; changing Home carousel widths must not alter Cart.

## Performance policy

- Page modules enqueue only on their own pages.
- Custom JavaScript uses `defer`.
- Home uses one WooCommerce product query for carousel and Latest Products.
- Cart avoids a permanent body-wide MutationObserver.
- W3 Total Cache remains disabled unless WooCommerce exclusions are configured and tested.

## v0.9.0 shell ownership

Repeated width problems were traced to the active block theme's Header/Footer wrappers. Rather than continue retrofitting those wrappers, v0.9.0 changes the boundary:

- KNKJOB renders its own visible Header via `wp_body_open`.
- KNKJOB renders its own visible Footer via `wp_footer`.
- Theme Header/Footer template parts are hidden on the customized storefront.
- Header / Home / WooCommerce pages / Footer share `.knk-container`.
- `.knk-container` is the single canonical horizontal line.

This is deliberate encapsulation: Shell geometry belongs to ShellModule and does not depend on theme DOM structure.
