# 説明
MyAppは、簡易なCRUD機能などを備えたWebアプリケーションのひな型です。  

本アプリケーションは、Webアプリケーションの学習に役立てることを目的に作成されたもので
アプリケーション単体で、何かに役立つというものではありません。

# MyAppのアーキテクチャ

クライアントサイド
- JavaScript ES6
- CSS
- HTML5
- VueJs
- jQuery, jQuery UI
- Bootstrap
- NodeJS（Webpack自動ビルドに使用）
- NPM
- Webpack

サーバーサイド
- PHP
- MySQL

# 環境構築手順

## githubからファイルをクローンする
以下のURLをクローンする。クローン先のローカルディレクトリを以降 [ルートパス] とする。
https://github.com/ison12/webAppTemplate.git

## インストールするソフトウェア
1. Xampp PHP7.2+
1. MySQL 5.7+
1. Composer 最新
1. NodeJs 最新
1. NetBeans 11

## 各種ソフトのインストール
### Xamppのインストール
ApacheとPHP（必須）をインストールする。
その他はお好みで。

### MySQLのインストール
デフォルトインストールでOK。  
MySQLのrootパスワードは後で利用するので、メモしておくこと。

### Composerのインストール
デフォルトインストールでOK。  

### NodeJSのインストール
デフォルトインストールでOK。  

### NetBeansのインストール
デフォルトインストールでOK。  

## 各種ソフトの設定
### Apache (Xampp)の設定
本設定では、80番を5555、443番を5556に設定。

[Xamppインストールパス]\apache\conf\httpd.conf

    …
    Listen 80 → Listen 5555
    ServerName localhost:80 → ServerName localhost:5555
    …
    <IfModule alias_module>
    …
        Alias /myapp "[ルートパス]/myapp/source/php/public"
        <Directory "[ルートパス]/myapp/source/php/public">
            AllowOverride All
            Require all granted
        </Directory>
    …
    </IfModule>
    …

### PHPの設定
[Xamppインストールパス]\php\php.ini

    … pdo mysqlのコメントアウトを外す
    extension=pdo_mysql
    …

### DBの作成

envファイルを環境に合わせて修正する。  
[ルートパス]/myapp/document/01_DB/10_SCRIPT/bin/env.bat

    rem mysqlコマンド
    set mysql="C:\Program Files\MySQL\MySQL Server 8.0\bin\mysql.exe"
    rem mysqlダンプコマンド
    set mysqldump="C:\Program Files\MySQL\MySQL Server 8.0\bin\mysqldump.exe"
    rem ホスト名
    set host=127.0.0.1
    rem データベース名
    set database=myapp
    rem ユーザー名
    set user=root
    rem パスワード
    set password=password
    rem オプション
    set option=--default-character-set=utf8mb4
    ...

以下のバッチを順次実行する。

    [ルートパス]/myapp/document/01_DB/10_SCRIPT/00_create_db.bat
    [ルートパス]/myapp/document/01_DB/10_SCRIPT/01_import_data.bat

### NetBeansの起動と設定

プロジェクトを開く。

    [ルートパス]/myapp/source/php

Composerで依存ライブラリをインストール。  
プロジェクトエクスプローラ上でプロジェクトを右クリックして、Composer＞インストール（dev）を選択する。

NPMで依存ライブラリをインストール。  
プロジェクトエクスプローラ上でプロジェクトを右クリックして、npmインストールを選択する。

## Webアプリの起動

Xampp Control Panelを起動し、Startボタンをクリックする。

ログインページにアクセスする。    
    http://localhost:5555/myapp/login

# 画面例

<img src="https://github.com/ison12/webAppTemplate/blob/master/myapp/document/90_IMAGE/01_login.png?raw=true" width="700px">

<img src="https://github.com/ison12/webAppTemplate/blob/master/myapp/document/90_IMAGE/02_password_change.png?raw=true" width="700px">

<img src="https://github.com/ison12/webAppTemplate/blob/master/myapp/document/90_IMAGE/02_password_change_2.png?raw=true" width="700px">

<img src="https://github.com/ison12/webAppTemplate/blob/master/myapp/document/90_IMAGE/03_top.png?raw=true" width="700px">

<img src="https://github.com/ison12/webAppTemplate/blob/master/myapp/document/90_IMAGE/04_system_setting_search.png?raw=true" width="700px">

<img src="https://github.com/ison12/webAppTemplate/blob/master/myapp/document/90_IMAGE/05_system_setting_edit.png?raw=true" width="700px">
