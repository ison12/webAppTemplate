FROM httpd:2.4.52

RUN apt-get update \
    && apt-get install -y openssl \
    && apt-get install -y expect

WORKDIR /usr/local/apache2/conf/

# メイン設定ファイル
COPY ./conf/httpd.conf ./
# SSL設定ファイル
COPY ./conf/extra/httpd-ssl.conf ./extra

# SSL証明書の作成
COPY conf/openssl.exp ./
RUN openssl genrsa 2048 > server.key
RUN expect openssl.exp
RUN openssl x509 -days 36500 -req -signkey server.key < server.csr > server.crt

COPY "./entrypoint.sh" "/usr/local/bin/ext-entrypoint.sh"
RUN chmod +x "/usr/local/bin/ext-entrypoint.sh"
ENTRYPOINT "/usr/local/bin/ext-entrypoint.sh"
