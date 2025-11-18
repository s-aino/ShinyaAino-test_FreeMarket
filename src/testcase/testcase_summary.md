# PHPUnit テスト実施サマリー

##  実施目的
本アプリケーション（Laravel製フリマアプリ）における  
主要機能の動作確認を自動化することを目的として、  
**PHPUnit** により以下の機能テストを実施した。  

対象範囲は会員登録・ログイン・ログアウトを含む、  
ユーザー・商品・購入関連の全機能。

---

##  実施環境
| 項目       | 内容                |
| ---------- | ------------------- |
| Laravel    | 10.x（Fortify使用） |
| PHP        | 8.1.33              |
| PHPUnit    | 10.5.58             |
| MySQL      | 8.0                 |
| Docker構成 | php / mysql / nginx |

---

##  テスト結果概要

| No  | 機能               | テスト概要                                                                                                                                           | テスト名 / 結果                                                        |
| --- | ------------------ | ---------------------------------------------------------------------------------------------------------------------------------------------------- | ---------------------------------------------------------------------- |
| 01  | 会員登録           | 必須項目未入力時のバリデーションと登録成功を確認                                                                                                     | [UserRegisterTest](../test_results/phpunit_user.png)                   |
| 02  | ログイン           | 認証情報の成功・失敗パターンを確認                                                                                                                   | [loginTest](../test_results/phpunit_login.png)                         |
| 03  | ログアウト         | 認証解除・未ログイン制御を確認                                                                                                                       | [logoutTest](../test_results/phpunit_logout.png)                       |
| 04  | 商品一覧           | 全商品の表示・出品者除外・SOLD表示                                                                                                                   | [ItemListTest](../test_results/phpunit_itemlist.png)                   |
| 05  | マイリスト         | いいね商品表示・SOLDラベル表示・未ログイン時の空表示                                                                                                 | [MylistTest](../test_results/phpunit_mylist.png)                       |
| 06  | 商品検索           | 商品名で部分一致検索できる／検索キーワードがマイリストでも保持される                                                                                 | [searchTest](../test_results/phpunit_search.png)                       |
| 07  | 商品詳細情報取得   | 商品詳細ページで、商品画像・価格・ブランド名・カテゴリ・状態・コメントなど全情報の表示を確認                                                         | [ItemDetailTest](../test_results/phpunit_itemdetail.png)               |
| 08  | いいね機能         | ログインユーザーによる「いいね登録」「色変化」「いいね解除」を確認                                                                                   | [LikeTest](../test_results/phpunit_like.png)                           |
| 09  | コメント送信機能   | ログイン済み送信・未ログイン拒否・バリデーション（空欄／255字超）を確認                                                                              | [CommentTest](../test_results/phpunit_comment.png)                     |
| 10  | 商品購入機能       | 「購入する」ボタン押下で購入が完了し、購入済商品のSOLD表示およびプロフィールへの反映を確認                                                           | [PurchaseTest](../test_results/phpunit_purchase.png)                   |
| 11  | 支払い方法選択機能 | 「コンビニ払い／カード支払い」の選択表示・hidden反映・バリデーション動作を確認                                                                       | [PaymentMethodTest](../test_results/phpunit_paymentmethod.png)         |
| 12  | 配送先変更機能     | 住所変更画面表示・住所登録・反映動作の3パターンを確認                                                                                                | [AddressChangeTest](../test_results/phpunit_addresschange.png)         |
| 13  | ユーザー情報取得   | マイページにて、プロフィール画像・ユーザー名・出品／購入した商品一覧が正しく取得・表示を確認                                                         | [UserInfoTest](../test_results/phpunit_userinfo.png)                   |
| 14  | ユーザー情報変更   | 変更項目が初期値として過去設定されていること（プロフィール画像、ユーザー名、郵便番号、住所）を確認                                                   | [UserProfileUpdateTest](../test_results/phpunit_userprofileupdate.png) |
| 15  | 出品商品情報登録   | 商品出品画面にて情報を保存（カテゴリ、商品の状態、商品名、ブランド名、商品の説明、販売価格）                                                         | [ItemRegisterTest](../test_results/phpunit_itemregister.png)           |
| 16  | メール認証機能     | 会員登録後に認証メールが送信され、「認証はこちらから」ボタンでガイドページへ遷移し、Mailtrapでの認証完了後にプロフィール設定画面へ移動する流れを確認 | [MailVerificationTest](../test_results/phpunit_mailverification.png)   |

---

##  補足資料
| 項目                   | 内容                               |
| ---------------------- | ---------------------------------- |
| 結果スクリーンショット | `/test_results/*.png`              |
| 使用データベース       | `demo_test`（phpunit.xmlにて指定） |

---

