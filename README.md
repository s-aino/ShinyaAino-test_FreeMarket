# フリマアプリ

Docker 上で動作する Laravel 製フリマアプリです。


## 🔧 機能一覧

- 商品一覧（SOLDバッジ/ サムネイル画像）
- 商品詳細（コメント投稿 / いいね機能）
- 商品出品フォーム（画像プレビュー対応）
- 購入機能（クレジットカード / コンビニ支払い）
- プロフィール編集（画像アップロード / 住所管理）
- Fortify 認証（メール認証を含む）
- メール認証ガイドページ（外部サイト不可のため代替仕様）


## 🔧 担当コーチの許可を得て追加した拡張機能

- success ページ追加（購入完了 / 出品完了）
- 出品フォームの画像欄にプレビュー画像を表示
- コンビニ払いは Stripe を使わない独自仕様  
  （Stripe webhook が必要だがローカル環境では困難なため）
- コメント投稿時、エラー時でも同じ位置に留まる仕様へ変更  
- コメントに投稿日時を表示
- メール認証は Mailtrap のクリックが不可のため verify-guide へ誘導


## 🧾 機能の補足説明


### 1. Stripe コンビニ払いを利用しない仕様
本来の Stripe コンビニ払いは：

- 支払い受付  
- コンビニで支払い  
- Stripe が webhook で決済完了通知  

…という手順が必要ですが  
ローカル環境では webhook 公開ができないため確認が困難。

そのため本アプリでは：

- クレジットカード払い → Stripe を利用（決済完了後に注文を登録）  
- コンビニ払い → Stripe を使わずアプリ内処理で即時「購入完了」扱い  
としています。
---

### 2. 配送先の一時保存（temp address）
購入時だけ配送先を変更したいケースに対応するため、以下のように一時保存として扱います：

- 一時住所を DB（`is_temporay=true`）で保存  
- セッションでもアドレス ID を保持  
- 購入確定後は商品に紐付く配送先として利用 
---

### 3. Mailtrap についての補足（メール認証）

メール認証は **Mailtrap** を用いて動作確認しています。

ただし以下の理由で、  
**アプリ内の「認証はこちらから」ボタンから Mailtrap へ直接遷移させることはできません：**

- Mailtrap は **外部サービス**  
- ローカルアプリ → Mailtrap への **直接リンク遷移は不可**

その代わりに、本アプリでは：

➡ **メール認証ガイドページ（verify-guide）へ誘導し、  
そこで Mailtrap の確認方法を説明する構成** にしています。 

## 🏗 環境構築

 ### Docker ビルド

1. git clone https://github.com/s-aino/ShinyaAino-test_FreeMarket.git
2. DockerDesktopアプリを立ち上げる
3. docker compose up -d --build


### 🛠 Laravel環境構築

#### 1. コンテナに入る
```bash
docker compose exec php bash
```
---
#### 2. Composer インストール
```bash
composer install
```
---
#### 3. .env 作成  
.env.example をコピーし、以下の DB 設定に変更：
```env
DB_HOST=mysql
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_pass
```
---
#### 4. アプリケーションキーの作成
```bash
php artisan key:generate
```
---
#### 5. マイグレーション & シーディング
##### 初回構築・仕様更新時
```bash
php artisan migrate:fresh --seed
```
##### （データ保持が必要な場合のみ）
```bash
php artisan migrate
php artisan db:seed
```
---
#### 6.権限の修正が必要なとき
```bash
chmod -R 777 storage
chmod -R 777 bootstrap/cache
```
---

#### 7. ストレージリンクの作成（画像表示に必要）
本アプリでは商品画像を storage/app/public に保存します。
以下を実行して公開ディレクトリへのリンクを作成してください。

```bash
php artisan storage:link
```

##### Laravel のキャッシュ削除
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

---
#### 8. メール送信設定（Mailtrap を使用）

本アプリでは、ユーザー登録時のメール認証に **Mailtrap** を使用しています。  
動作確認のため、評価環境でも Mailtrap の無料アカウントを作成していただく必要があります。

##### 8-1. Mailtrap アカウントの作成

1. ブラウザで以下にアクセスします。  
   https://mailtrap.io/
2. 「Sign up」から無料アカウントを作成してください。
3. ログイン後、「Sandboxes」→ デフォルトの **My Sandbox** を開きます。

##### 8-2. Mailtrap の SMTP 情報を .env に設定

Mailtrap の画面で以下の場所を開きます。

- 上部タブ **「Integration」**
- 「SMTP」タブを選択

表示されている値を、プロジェクト直下の `.env` にコピーしてください。

```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=（Mailtrap の Username）
MAIL_PASSWORD=（Mailtrap の Password）
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"
```
※ Username / Password は Mailtrap 画面の Credentials に表示されている値をそのまま使用してください。

##### 8-3. .env を保存したあと、念のため Laravel の設定キャッシュをクリアします。
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```
---
#### 9.Stripe の設定（カード決済機能を利用する場合）

本アプリのカード決済では Stripe を使用しています。
カード払い機能を動作させるには、Stripe アカウントを作成し、API Key を .env に設定してください。

##### 9-1. Stripe アカウント作成

https://dashboard.stripe.com/register

より無料アカウントを作成してください。

##### 9-2.  API キーを取得

Stripe ダッシュボード →「開発者」→「APIキー」から以下を取得します。

公開鍵（Publishable key）

秘密鍵（Secret key）

#####  9-3. .env に入力
```bash
STRIPE_PUBLIC_KEY=pk_test_xxxxxxxxxxxxxxxxxxxxx
STRIPE_SECRET_KEY=sk_test_xxxxxxxxxxxxxxxxxxxxx
```
##### 9-4. キャッシュクリア
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

##### Stripe テストカード番号（動作確認用）

Stripe のテストモードでは、以下のカード番号が使用できます：
```bash
カード番号：　4242 4242 4242 4242
有効期限：任意（例 12/34）
CVC：任意（例 123）
```
---
### 🎉 以上で環境構築は完了です。
ブラウザでアプリを利用できる状態になりました。

- アプリURL: http://localhost
---
## 🧾 PHPUnit テスト
####   テスト環境（env.testing）

phpunit / php artisan test 実行時は、本番 DB とは別の テスト用データベース を使用します。

#####   env.testing を作成
プロジェクト直下で以下を実行します。
```bash
cp .env.example .env.testing
```
##### 　env.testingに以下の内容を記述してください。

```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel_test_db
DB_USERNAME=root
DB_PASSWORD=root
```
##### APP_KEYの生成（初回のみ）
テスト環境専用のアプリケーションキーを生成します。
```bash
php artisan key:generate --env=testing
```

##### テーブルの作成（初回のみ）
```bash
php artisan migrate --env=testing
```

##### 注意：初回は以下のような質問が表示されます
```bash
The database 'laravel_test_db' does not exist. Create it? (yes/no)
```
 **👉 yes  と入力してください。**
（yes を選ぶことで、テスト用 DB が自動作成されます）

####   テストの実行

本アプリには 16 個の自動テストが含まれています。
以下のコマンドで すべてのテストを一括実行できます。
```bash
php artisan test
```

#####  テスト内容詳細
- **PHPUnit によるテストケース一覧を Markdown 形式で整理したもの**  
[src/testcase/testcase_summary.md](src/testcase/testcase_summary.md)

## 🌐 開発環境 
- **アプリ**：http://localhost  
- **phpMyAdmin**：http://localhost:8080  

## 🧰 使用技術（実行環境）

- PHP 8.1
- Laravel 10.x
- MySQL 8.x
- Nginx（php-fpm 経由）
- Stripe API（クレジットカード決済で利用）
- Mailtrap（メール認証機能で利用）
  
## 🗂 ER 図 / 仕様書

- **ER 図（Mermaid 元ファイル）** : [docs/ER.md](docs/ER.md)
- **テーブル仕様書** : [docs/DB_SPEC.md](docs/DB_SPEC.md)
  
  （Google スプレッドシート版「テーブル仕様書」を Markdown へ書き起こしたもの）







