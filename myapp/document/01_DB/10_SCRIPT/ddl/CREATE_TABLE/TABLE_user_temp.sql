-- -----------------------------------------------------------------------------
-- テーブル：user_temp
-- 作成者　：自動生成
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `user_temp`;
CREATE TABLE `user_temp` (
      `account_regist_uri`      varchar(255)                 NOT NULL                     COMMENT 'アカウント登録URI'
    , `auth_code`               varchar(10)                  NOT NULL                     COMMENT '認証コード'
    , `user_account`            varchar(255)                 NOT NULL                     COMMENT 'ユーザーアカウント'
    , `create_datetime`         datetime                     NOT NULL DEFAULT '1000-01-01 00:00:00' COMMENT '登録日時'
    , `create_user_id`          bigint unsigned              NOT NULL DEFAULT '0'         COMMENT '登録者ID'
    , `update_datetime`         datetime                     NOT NULL DEFAULT '1000-01-01 00:00:00' COMMENT '更新日時'
    , `update_user_id`          bigint unsigned              NOT NULL DEFAULT '0'         COMMENT '更新者ID'
) ENGINE = INNODB COMMENT = 'ユーザー仮登録';
