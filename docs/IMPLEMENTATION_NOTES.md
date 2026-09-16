# Implementation Notes

## Breadcrumb issue

商品詳細で `Home -> Category -> Product` となり、Shopへ戻る導線が弱かったため、WooCommerce filterを使って `Home -> Shop -> Category -> Product` に変更しました。

## CSV migration issue

WooCommerce CSV Import履歴から、リモート画像URLの取得が `Forbidden` になっていることを確認しました。

画像列をImport対象外にして再実行し、親商品1件と6バリエーションのImportに成功。画像はローカルで再設定しました。

## Core modification policy

WordPress本体、WooCommerce本体、親テーマは直接変更していません。独自プラグインのHooks / FiltersとCSS / JavaScriptで拡張しています。
