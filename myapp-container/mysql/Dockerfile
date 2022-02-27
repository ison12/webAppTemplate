FROM mysql:8.0.28

# volumesでbindすると777になるので、手動でファイルを追加して適切な権限にする
ADD ./conf/my.cnf /etc/mysql/conf.d/my.cnf
RUN chmod 644 /etc/mysql/conf.d/my.cnf
