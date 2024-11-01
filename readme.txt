=== Time goes by ===
Contributors: tsato
Donate link: https://blog.gti.jp/time-goes-by
Tags: time goes by,scheduled,contents
Requires at least: 5.1
Tested up to: 6.5.3
Stable tag: 1.2.9
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
ショートコードで囲んだコンテンツを時刻等で表示を切り替えるプラグイン

== Description ==
ショートコード [tgb ][/tgb] で囲まれた部分が　start_time, end_time, config で指定された時間帯等に表示されるようになるプラグインです。
start_time だけの指定
end_time だけの指定も可能です。

[tgb start_time="20160101"][/tgb]　で囲まれた部分は 2016年１月１日を迎えると表示されます。
[tgb end_time="20161231"][/tgb] で囲まれた部分は 2016年１２月３１日になるまで表示されます。
２０１６年１２月３１日いっぱい表示したい場合は　[tgb end_time="20161231235959"][/tgb] で囲むか
[tgb end_time="20170101"][/tgb] で囲むと2017年１月１日担った瞬間表示されなくなります。

より詳細で繰り返しも可能な　config パラメータの設定ができるようにしました。
設定は　config="day:1|2|3|4|5,hour:7|8|9|10|11" のように設定します。
上記の設定は下記のように理解されます。

「毎月１〜５日　の　７〜１１時　にだけ表示される」

これに start_time, end_time も絡めて詳細に繰り返しとなる部分を設定することが可能です。
そして複数の設定を一度に行うことができます。パラメータをカンマ区切りで指定できます。
** 複数の設定は記述の順番に実行されます。ただし、任意のパラメータについては最後に実行されます。
** 規定のパラメータ、任意のパラメータ以外のパラメータは無視されます。

config パラメータは下記の設定が出来ます。
[1] hour: ２４時表記で設定します。７〜１０時という場合は　hour:7|8|9（１０時台に入ったら表示されない） というように | （vertical line）で区切るようにします。
[2] day: 毎月○日の設定をします。　day:7|8|9|10 とすると ７〜１０日というようになります。
[3] week: 毎週○曜日の設定をします。　week:sun|mon|tue とすると日〜火曜日という感じです。一応日本語での設定も受け付けるはずです。 week:火|木|土 など。
[4] month: ○月の設定をします。　month:6|7|8 とすると６〜８月という感じです。季節ごとの表示切り替えができる感じです。
[5] 任意のパラメータ これは
　　
　　add_filter('time_goes_by_judgement', '〜任意のメソッド名〜', 10, 3);
　　
　　という感じにfunctions.phpに記述し
　　任意のメソッド名にて TRUE か FALSE を返却すればそのように表示されます。
　　そもそも他の判定に依存しないように $flg を引数に入れていますので、そこまでの判定を覆すことも可能です。
　　$config には任意のパラメータ以外の上記パラメータも入ってきます。 $content にはショートコードで囲まれた部分が入ってますので
　　様々な利用方法・判定方法が考えられます。
　　apply_filters( 'time_goes_by_judgement', $flg, $configs, $content );

　　祝日に表示する　などは現在のプラグイン自体にはない機能ですが、これを使って実装すると良いでしょう。

例：
 [tgb config="month:1|2|3,day:5|15|25,hour:13|14|15"]１〜３月の　５の付く日は　タイムサービスを行っています！！！　ただいま１３時〜１６時までサービスタイム実施中！！[/tgb]


・timezone="Asia/Tokyo" のようにパラメータ指定でタイムゾーンの指定ができます。

このデフォルト値は内部設定のタイムゾーンとなっています。

このデフォルト値は管理画面から変更することが可能です。

その他、この中で利用出来るように[disp_content][disp_title]ショートコードも用意しました。
[disp_content post_id="xxx"] とすると、投稿IDがxxxのコンテンツを表示します。
[disp_title post_id="xxx"] とすると、投稿IDのタイトルを表示します。

例：
[tgb start_time="20160101" end_time="20160201"]<h2>[disp_title post_id="1234"]</h2>
[disp_content post_id="1234"][/tgb]

２０１６年１月１日から２０１６年２月１日になるまで（１月３１日の間まで）投稿ID:1234のタイトルがh2タグでその下に投稿ID:1234のコンテンツが表示されます。

・disp_content, disp_title のパラメータに slug と status を追加しました。（version 1.2.5）
　slug にはスラッグを入れると記事を特定します。（サーバー移転などpost_idが変わるような環境で効果的です。）
　status は publish, future, private, draft の中から指定できます。
　非公開の状態だったら取得したくない場合などを考慮しました。
　無指定の場合はいかなる状態でも取得してきます。

-----------------------


制作：佐藤　毅（さとう　たけし） <a href="https://gti.co.jp/" target="_blank">福岡市南区大橋 ウェブシステム開発</a> 株式会社ジーティーアイ代表

== Installation ==

e.g.

1. Upload `time-goes-by` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==
質問やご要望はSupportまたは弊社お問い合わせ（https://gti.co.jp/）へお願いします！

== Screenshots ==

== Changelog ==
= 1.2.9 =
WordPress 6.5.3 にてテスト
コードの整理
timezone の取得を wp_timezone_string() に変更

= 1.2.8 =
WordPrss 5.9 対応
PHP8 で警告が出ていた処理を修正。

= 1.2.7 =
WordPress 5.8 対応
コンテンツ呼び出しの際、表示している $post->ID と同じIDを取得すると回帰的になり
タイムエラーが発生するため同じ投稿IDのコンテンツは空文字を返すように修正。

= 1.2.6 =
WordPress 5.2.2 にてテスト

= 1.2.5 =
他のコンテンツを呼び出す場合に $post->ID ではなく slug で呼び出せるようにしました。
slug='（スラッグ）' で指定してください。
disp_excerpt で記事抜粋を表示パラメータは post_id, slug, status

= 1.2.4 =
コードの整理

= 1.2.3 =
timezoneの設定箇所が一つ足りなかったためサーバーによっては
ショートコードの１つ目で利用出来なかったため修正しました。

= 1.2 =
configパラメータ追加
WordPress動作環境で「 date_default_timezone_get() 」関数は「UTC」しか返却しなかったので
get_option('timezone_string')を取得しそこに値があれば　それを　なければ
get_option('gmt_offset') を取得しその数値を「Etc/GMT」にプラスするようにしたものを
　※例：日本だと「Etc/GMT+9」となります。
それぞれデフォルトタイムゾーンとするようにしました。

= 1.1 =
ソースコードにライセンス記述

= 1.0 =
新規作成

== Upgrade Notice ==
= 1.1 =
time-goes-by.php を time_goes_by.php にリネームしました。
以前のバージョンをご利用の方は一度プラグイン停止→削除（推奨）後、インストールし直してください。

= 1.0 =
とりあえず作りました！

== Arbitrary section ==

== A brief Markdown Example ==
