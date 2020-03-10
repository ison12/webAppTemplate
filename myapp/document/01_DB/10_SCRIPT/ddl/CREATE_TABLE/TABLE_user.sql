-- -----------------------------------------------------------------------------
-- テーブル：user
-- 作成者　：自動生成
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
      `user_id`                 serial PRIMARY KEY           NOT NULL                     COMMENT 'ユーザーID'
    , `user_account`            varchar(255)                 NOT NULL                     COMMENT 'ユーザーアカウント'
    , `password`                varchar(100)                 NOT NULL                     COMMENT 'パスワード'
    , `email`                   varchar(255)                 NOT NULL                     COMMENT 'e-mail'
    , `user_name`               varchar(100)                 NOT NULL                     COMMENT 'ユーザー名'
    , `user_name_kana`          varchar(100)                 NOT NULL                     COMMENT 'ユーザー名カナ'
    , `authority`               varchar(100)                                              COMMENT '権限'
    , `create_datetime`         datetime                     NOT NULL DEFAULT '1000-01-01 00:00:00' COMMENT '登録日時'
    , `create_user_id`          bigint unsigned              NOT NULL DEFAULT '0'         COMMENT '登録者ID'
    , `update_datetime`         datetime                     NOT NULL DEFAULT '1000-01-01 00:00:00' COMMENT '更新日時'
    , `update_user_id`          bigint unsigned              NOT NULL DEFAULT '0'         COMMENT '更新者ID'
) ENGINE = INNODB COMMENT = 'ユーザー';
