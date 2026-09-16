# KNKJOB STORE — WordPress / WooCommerce EC Portfolio

WordPress / WooCommerce を使って構築した自主制作ECサイトです。  
WooCommerce標準機能だけで終わらせず、実際の操作で見つけた導線・表示上の課題を、専用PHPプラグインで改善しています。

## Live Demo

https://knkjob-store.infinityfreeapp.com/

> ポートフォリオ用デモストアです。実際の決済・発送は行いません。

## 実装内容

- シンプル商品
- バリエーション商品（Size: S / M / L、Color: Black / White）
- バリエーション別SKU
- 在庫管理
- 商品カテゴリ
- カート
- 商品画像切替
- レスポンシブ対応
- LocalWPによるローカル開発
- InfinityFreeへの公開

## 独自PHPカスタマイズ

`knkjob-store-customizer/` が自主制作した独自プラグインです。

### パンくず改善

変更前:

`ホーム → Tops → 商品`

変更後:

`ホーム → ショップ → Tops → 商品`

WooCommerce の `woocommerce_get_breadcrumb` filter を使用しています。

### ECトップページ

専用トップページを生成し、Hero、カテゴリ、最新商品、ストア情報、使用技術を表示します。

### 商品詳細・管理画面

- 商品仕様タブ
- 素材
- 発送目安
- 在庫5点以下の残数表示
- デモストア表示
- 商品編集画面へのMaterial / 発送目安フィールド追加

## 技術

WordPress / WooCommerce / PHP / JavaScript / CSS / MySQL / WooCommerce Hooks / LocalWP / InfinityFree

## 開発時に解決した問題

WooCommerce CSV移行時、InfinityFree上の商品画像URLが `Forbidden` となり、親商品・6バリエーションのImportに失敗しました。

Import履歴から原因を特定し、画像列のみImport対象外として再実行。

結果:
- バリエーション親商品: 1件Import
- バリエーション: 6件Import
- 既存SKUの商品: 1件Skip

画像はローカル環境で再設定しました。

## ポートフォリオとしての目的

WordPressを導入しただけではなく、WooCommerce設定、既存機能の理解、問題発見、PHP Hookによる改修、エラー原因の切り分け、ローカル/公開環境での確認まで行っています。

## 注意

- 架空の商品を使用した自主制作です。
- 商品画像はポートフォリオ用のオリジナル生成画像です。
- 実際の販売・決済・発送は行っていません。
- WordPress本体 / WooCommerce本体は含みません。
- パスワードやDB認証情報などの秘密情報は含みません。

## License

Custom plugin source: GPL-2.0-or-later
