# KNKJOB Store Customizer v0.9.0

v0.9.0 replaces theme-dependent shell retrofitting with a plugin-owned storefront shell.

## Why

The active block theme kept adding max-width/padding wrappers around its Header/Footer.
That made Header and Home appear to use different horizontal lines even after multiple
container-width fixes.

## What changed

- Plugin-owned Header
- Plugin-owned Footer
- Theme Header/Footer hidden on customized storefront
- One `.knk-container` width for Header / Home / Shop / Product / Cart / Checkout / My Account / Footer
- Container max width: 1400px
- Responsive gutters: 24–48px
- Home hero uses the same exact container line as Header/Footer

All prior carousel, product-card, button-label, cart, demo notice and responsive fixes remain included.
