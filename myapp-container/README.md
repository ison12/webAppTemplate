# 起動方法
docker-compose up -d --build

# URL

Web：
http://localhost:10180/
https://localhost:10443/

http://localhost:10180/myapp/login

PHP MyAdmin：
http://localhost:10181/

# DockerのTIPS

## initディレクトリ内のファイル変更時に、ビルドしても反映されない問題

https://qiita.com/kondo0602/items/ab0a85fb1e731234eb1a

## docker検証時に頻繁に実行するコマンド

https://qiita.com/suin/items/19d65e191b96a0079417

## 全部削除
docker-compose down --rmi all --volumes --remove-orphans

## リビルド
docker-compose up -d --force-recreate
docker-compose up -d --build --force-recreate

## 10080を使ってはいけない
https://labor.ewigleere.net/2021/06/03/chrome-unsafe-port-10080/

## 証明書作成の自動化

https://www.code-mogu.com/2021/09/04/docker-oreore-ssl/
https://sy-base.com/myrobotics/mac/expect_shell_script/

## EntryPointやCmdを上書きする場合！

https://zenn.dev/flyingbarbarian/articles/bedd7961d74b83
