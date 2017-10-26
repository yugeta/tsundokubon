MYNT-Studio (Framework)
==
- ver 2.1.0
- Date 2017.09.01
- Author KojiYugeta


# 概要
- ストレージサーバーに溜め込んでいる書籍のスキャンデータをブラウザで閲覧可能にするシステム


# Server environment
- Linux
		Ubuntu
		Debian
		CentOS
		* 書籍ファイル名に日本語を使う場合は、lang=jaを必要とする
		
- Nginx (apache) + PHP(5.6以上)
		* PHPはmb,gdはインストールしておくこと。

# How-to install
1. Githubよりソースコードのダウンロード
$ git clone https://github.com/yugeta/tsundokubon.git

2. サーバーモジュールのインストール
- ImageMagick
	- debian系
	$ apt-get -y install imagemagick
	- centos系
	$ yum -y install imagemagick
	
- unar
	- debian系
	$ apt-get -y install unar
	- centos系
	$ yum -y install unar

- xpdf
	- debian系
	$ apt-get -y install xpdf
	- centos系
	$ yum -y install xpdf

3. 書籍フォルダの指定
	# 下記ファイルの内部をJSON形式で記述
	$ vi data/book/config/unarshelf.json
	* デフォルトではdir:"..."となっている箇所を書籍の置かれているフルパスに書き換えてください。
	* この時にNginxやApacheでアクセスできる権限を割り当てる。

## file-nest
/
├ data/
│　├ config/
│　├ page/
│　└ blog/
│
├ design/
│　├ ##1/
│　├ ##2/
│　└ ##3/
│　
├ plugin/
│　├ ##1/
│　├ ##2/
│　└ ##3/
│　
├ lib/
│　├ css/
│　├ html/
│　├ js/
│　└ php/
│　
├ system/
│　├ config/
│　├ css/
│　├ design/
│　├ html/
│　├ img/
│　├ js/
│　└ php/
│　
├ book.php
├ index.php
├ upload.php
├ system.php
└ README.md





==
# 機能追加要望
- Book閲覧時の閲覧ログ
	一定期間毎に、ゴミデータをバキュームする仕組みが必用？

- 書籍毎にコメント、評価、閲覧日（閲覧開始日と最終閲覧日、閲覧回数など）読書記録を残せる
	ページ毎の栞コメントと、本全体のコメント、評価（☆☆☆☆★）

- BOOKアーカイブ関連
		→その際に元の圧縮データを書き換える事も必用。

- サムネイル機能（Book-archive時と、archive後の処理）
	書籍一覧表示の際にサムネイルアイコンを表示すると、選別しやすくなる。

- 各BOOKをAmazonとヒモ付け、アフェリエイトできる仕組み構築（片端読書感想文サイト「かたっぱしどくしょサイト」）
	→Amazon評価の表示
- BOOK検索機能（本の名称、作者名、出版日時、出版社）
- book-archive処理のやり直し機能（キャッシュ削除機能）
- Book-Archiveできない時の分析機能（Archiveデータのファイル名、内容リスト表示）

- Book-view機能（スマホ対応）カルーセル対応
- ページ先読み、ブラウザキャッシュ機能
- 気に入ったページを保存機能（ページ番号と、内部座標を取得）
	- お気に入りページ（ページ全体と、お気に入り箇所の座標）
- ページ表示の際に、BOOKプログレスバーを表示（閲覧課程の視覚化）
- 公衆網（パケット）利用の時用に、容量削減機能（表示ページを常に縮小）※拡大ボタンを付けることで、詳細表示にも対応（大(1024),中(640),小(320)）
- 書籍の表示入れ替え機能（自動検出は初回ページのみ）
	- WEBサイトから取得(URL登録)
	- 別ページを登録（ページ番号を登録）
	- 別ページ且つ、部分（見開きの場合対応）切り出し
- PDFのテキスト取得機能
	- これにより、書籍の閲覧パケット利用が少なく済む
	- 文字ご認識の為に、画像出力も必用
	- ご認識の時は、修正対応も必用（PDF上書き？）
- 元データ修正（削除）機能
	- 元データで不要の物は、ゴミ箱に入れる機能
	- 切り戻しも可能にする必用がある。
	- ファイル名変更で済ませる機能も必用
	- ページ順番が違う場合（アーカイブロジックの見直し？）
	- 元データの内部ファイル名の修正
	- ページ抜け、データ破損、などの対応→issueリストの作成（BOOK名、ページ数、内容などの記載）
- 本表示
	- 見開き対応（タブレット横画面対応）

