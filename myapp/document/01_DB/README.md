# 説明

## 00_TABLE_DEFINITION

テーブル定義所を管理するディレクトリ。  
テーブルごとにxlsxファイルで管理。

## 10_SCRIPT/00_create_db.bat  

DBを生成するバッチファイル。詳細は以下の通り。

- データベース生成
- データベースユーザー生成

## 10_SCRIPT/01_import_data.bat

10_SCRIPT/dataフォルダ内のSQLファイルを実行するバッチファイル。  
投入すべきマスタデータが増えた場合にこちらにファイルを追加する。  

バッチファイルを実行すると、以下の順序でクエリが発行される。
1. 拡張子がsqlのファイルが順次実行される。
1. truncate [対象ファイルのテーブル名]が実行
1. [対象ファイル]の内容が実行

## 90_TOOL_DDL_CREATOR

テーブル定義が変更になったり、増えた場合に、以下のマクロを使用して、DDLファイルを生成する。  
テーブル定義_スクリプト生成.xlsm