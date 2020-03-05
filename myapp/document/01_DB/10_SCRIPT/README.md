■00_create_db.bat
	以下を実行するバッチファイル
	・データベース生成
	・データベースユーザー生成

■01_import_data.bat
	.\dataフォルダ内のSQLファイルを実行するバッチファイル

	投入すべきマスタデータが増えた場合にこちらにファイルを追加する。

	例として、system_settingテーブルへのマスタデータを投入する「system_setting.sql」が保存されている場合、以下の順序でクエリが発行される。
	1.「truncate system_setting」が実行
	2. 対象ファイルの内容が実行
