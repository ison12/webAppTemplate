-- -----------------------------------------------------------------------------
-- テーブル：system_setting
-- 作成者　：自動生成
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `system_setting`;
CREATE TABLE `system_setting` (
      `system_code`             varchar(100)                 NOT NULL                     COMMENT 'システムコード'
    , `system_name`             varchar(100)                 NOT NULL                     COMMENT 'システム名称'
    , `system_value`            varchar(256)                 NOT NULL                     COMMENT 'システム値'
    , `create_datetime`         datetime                     NOT NULL DEFAULT '1000-01-01 00:00:00' COMMENT '登録日時'
    , `create_user_id`          bigint unsigned              NOT NULL DEFAULT '0'         COMMENT '登録者ID'
    , `update_datetime`         datetime                     NOT NULL DEFAULT '1000-01-01 00:00:00' COMMENT '更新日時'
    , `update_user_id`          bigint unsigned              NOT NULL DEFAULT '0'         COMMENT '更新者ID'
) ENGINE = INNODB COMMENT = 'システム設定マスタ';
-- -----------------------------------------------------------------------------
-- テーブル：todo
-- 作成者　：自動生成
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `todo`;
CREATE TABLE `todo` (
      `todo_id`                 serial PRIMARY KEY           NOT NULL                     COMMENT 'TODO ID'
    , `user_id`                 bigint unsigned              NOT NULL                     COMMENT 'ユーザーID'
    , `title`                   varchar(100)                 NOT NULL                     COMMENT 'タイトル'
    , `content`                 varchar(500)                 NOT NULL                     COMMENT '内容'
    , `create_datetime`         datetime                     NOT NULL DEFAULT '1000-01-01 00:00:00' COMMENT '登録日時'
    , `create_user_id`          bigint unsigned              NOT NULL DEFAULT '0'         COMMENT '登録者ID'
    , `update_datetime`         datetime                     NOT NULL DEFAULT '1000-01-01 00:00:00' COMMENT '更新日時'
    , `update_user_id`          bigint unsigned              NOT NULL DEFAULT '0'         COMMENT '更新者ID'
) ENGINE = INNODB COMMENT = 'TODO';
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
-- -----------------------------------------------------------------------------
-- テーブル：user_access
-- 作成者　：自動生成
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `user_access`;
CREATE TABLE `user_access` (
      `user_id`                 bigint unsigned              NOT NULL                     COMMENT 'ユーザーID'
    , `access_datetime`         datetime                                                  COMMENT '最終アクセス日時'
    , `auth_failed_count`       integer                      NOT NULL                     COMMENT '認証失敗数'
    , `auth_failed_datetime`    datetime                                                  COMMENT '認証失敗日時'
    , `create_datetime`         datetime                     NOT NULL DEFAULT '1000-01-01 00:00:00' COMMENT '登録日時'
    , `create_user_id`          bigint unsigned              NOT NULL DEFAULT '0'         COMMENT '登録者ID'
    , `update_datetime`         datetime                     NOT NULL DEFAULT '1000-01-01 00:00:00' COMMENT '更新日時'
    , `update_user_id`          bigint unsigned              NOT NULL DEFAULT '0'         COMMENT '更新者ID'
) ENGINE = INNODB COMMENT = 'ユーザーアクセス';
-- -----------------------------------------------------------------------------
-- テーブル：user_account_reset
-- 作成者　：自動生成
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `user_account_reset`;
CREATE TABLE `user_account_reset` (
      `account_reset_uri`       varchar(255)                 NOT NULL                     COMMENT 'アカウントリセットURI'
    , `user_id`                 bigint unsigned              NOT NULL                     COMMENT 'ユーザーID'
    , `auth_code`               varchar(10)                  NOT NULL                     COMMENT '認証コード'
    , `create_datetime`         datetime                     NOT NULL DEFAULT '1000-01-01 00:00:00' COMMENT '登録日時'
    , `create_user_id`          bigint unsigned              NOT NULL DEFAULT '0'         COMMENT '登録者ID'
    , `update_datetime`         datetime                     NOT NULL DEFAULT '1000-01-01 00:00:00' COMMENT '更新日時'
    , `update_user_id`          bigint unsigned              NOT NULL DEFAULT '0'         COMMENT '更新者ID'
) ENGINE = INNODB COMMENT = 'ユーザーアカウントリセット';
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
-- -----------------------------------------------------------------------------
-- テーブル：system_setting
-- 作成者　：自動生成
-- -----------------------------------------------------------------------------

-- -----------------------------------------------------------------------------
-- PK：PK_SYSTEM_SETTING
-- -----------------------------------------------------------------------------
ALTER TABLE `system_setting` ADD CONSTRAINT PK_SYSTEM_SETTING PRIMARY KEY (
      `system_code`
);




-- -----------------------------------------------------------------------------
-- テーブル：todo
-- 作成者　：自動生成
-- -----------------------------------------------------------------------------

-- -----------------------------------------------------------------------------
-- FK：FK_TODO_user_id
-- -----------------------------------------------------------------------------
ALTER TABLE `todo` ADD CONSTRAINT FOREIGN KEY FK_TODO_user_id (
      `user_id`
) REFERENCES user (
      `user_id`
) ON DELETE CASCADE ON UPDATE CASCADE;


-- -----------------------------------------------------------------------------
-- テーブル：user
-- 作成者　：自動生成
-- -----------------------------------------------------------------------------
-- -----------------------------------------------------------------------------
-- UK：UK_USER_user_account
-- -----------------------------------------------------------------------------
ALTER TABLE `user` ADD CONSTRAINT UK_USER_user_account UNIQUE (
      `user_account`
);



-- -----------------------------------------------------------------------------
-- テーブル：user_access
-- 作成者　：自動生成
-- -----------------------------------------------------------------------------

-- -----------------------------------------------------------------------------
-- PK：PK_USER_ACCESS
-- -----------------------------------------------------------------------------
ALTER TABLE `user_access` ADD CONSTRAINT PK_USER_ACCESS PRIMARY KEY (
      `user_id`
);


-- -----------------------------------------------------------------------------
-- FK：FK_USER_ACCESS_user_id
-- -----------------------------------------------------------------------------
ALTER TABLE `user_access` ADD CONSTRAINT FOREIGN KEY FK_USER_ACCESS_user_id (
      `user_id`
) REFERENCES user (
      `user_id`
) ON DELETE CASCADE ON UPDATE CASCADE;


-- -----------------------------------------------------------------------------
-- テーブル：user_account_reset
-- 作成者　：自動生成
-- -----------------------------------------------------------------------------

-- -----------------------------------------------------------------------------
-- PK：PK_USER_ACCOUNT_RESET
-- -----------------------------------------------------------------------------
ALTER TABLE `user_account_reset` ADD CONSTRAINT PK_USER_ACCOUNT_RESET PRIMARY KEY (
      `account_reset_uri`
);


-- -----------------------------------------------------------------------------
-- FK：FK_USER_ACCOUNT_RESET_user_id
-- -----------------------------------------------------------------------------
ALTER TABLE `user_account_reset` ADD CONSTRAINT FOREIGN KEY FK_USER_ACCOUNT_RESET_user_id (
      `user_id`
) REFERENCES user (
      `user_id`
) ON DELETE CASCADE ON UPDATE CASCADE;


-- -----------------------------------------------------------------------------
-- テーブル：user_temp
-- 作成者　：自動生成
-- -----------------------------------------------------------------------------

-- -----------------------------------------------------------------------------
-- PK：PK_USER_TEMP
-- -----------------------------------------------------------------------------
ALTER TABLE `user_temp` ADD CONSTRAINT PK_USER_TEMP PRIMARY KEY (
      `account_regist_uri`
);




