-- -----------------------------------------------------------------------------
-- テーブル：system_setting
-- 作成者　：自動生成
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `system_setting`;
CREATE TABLE `system_setting` (
      `id`                      serial             NOT NULL                     COMMENT 'ID'
    , `system_code`             varchar(100)       NOT NULL                     COMMENT 'システムコード'
    , `system_name`             varchar(100)       NOT NULL                     COMMENT 'システム名称'
    , `system_value`            varchar(256)       NOT NULL                     COMMENT 'システム値'
    , `create_datetime`         datetime           NOT NULL DEFAULT '1000-01-01 00:00:00' COMMENT '登録日時'
    , `create_user_id`          bigint unsigned    NOT NULL DEFAULT '0'         COMMENT '登録者ID'
    , `update_datetime`         datetime           NOT NULL DEFAULT '1000-01-01 00:00:00' COMMENT '更新日時'
    , `update_user_id`          bigint unsigned    NOT NULL DEFAULT '0'         COMMENT '更新者ID'
    , `delete_flag`             boolean            NOT NULL DEFAULT '0'         COMMENT '削除フラグ'
) ENGINE = INNODB COMMENT = 'システム設定マスタ';
-- -----------------------------------------------------------------------------
-- テーブル：user
-- 作成者　：自動生成
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
      `id`                      serial             NOT NULL                     COMMENT 'ID'
    , `user_account`            varchar(255)       NOT NULL                     COMMENT 'ユーザーアカウント'
    , `password`                varchar(100)       NOT NULL                     COMMENT 'パスワード'
    , `email`                   varchar(255)       NOT NULL                     COMMENT 'e-mail'
    , `user_name`               varchar(100)       NOT NULL                     COMMENT 'ユーザー名'
    , `user_name_kana`          varchar(100)       NOT NULL                     COMMENT 'ユーザー名カナ'
    , `authority`               varchar(100)                                    COMMENT '権限'
    , `create_datetime`         datetime           NOT NULL DEFAULT '1000-01-01 00:00:00' COMMENT '登録日時'
    , `create_user_id`          bigint unsigned    NOT NULL DEFAULT '0'         COMMENT '登録者ID'
    , `update_datetime`         datetime           NOT NULL DEFAULT '1000-01-01 00:00:00' COMMENT '更新日時'
    , `update_user_id`          bigint unsigned    NOT NULL DEFAULT '0'         COMMENT '更新者ID'
    , `delete_flag`             boolean            NOT NULL DEFAULT '0'         COMMENT '削除フラグ'
) ENGINE = INNODB COMMENT = 'ユーザー';
-- -----------------------------------------------------------------------------
-- テーブル：user_access
-- 作成者　：自動生成
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `user_access`;
CREATE TABLE `user_access` (
      `user_id`                 bigint unsigned    NOT NULL                     COMMENT 'ユーザーID'
    , `access_datetime`         datetime                                        COMMENT '最終アクセス日時'
    , `auth_failed_count`       integer            NOT NULL                     COMMENT '認証失敗数'
    , `auth_failed_datetime`    datetime                                        COMMENT '認証失敗日時'
    , `create_datetime`         datetime           NOT NULL DEFAULT '1000-01-01 00:00:00' COMMENT '登録日時'
    , `create_user_id`          bigint unsigned    NOT NULL DEFAULT '0'         COMMENT '登録者ID'
    , `update_datetime`         datetime           NOT NULL DEFAULT '1000-01-01 00:00:00' COMMENT '更新日時'
    , `update_user_id`          bigint unsigned    NOT NULL DEFAULT '0'         COMMENT '更新者ID'
    , `delete_flag`             boolean            NOT NULL DEFAULT '0'         COMMENT '削除フラグ'
) ENGINE = INNODB COMMENT = 'ユーザーアクセス';
-- -----------------------------------------------------------------------------
-- テーブル：user_account_reset
-- 作成者　：自動生成
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `user_account_reset`;
CREATE TABLE `user_account_reset` (
      `account_reset_uri`       varchar(255)       NOT NULL                     COMMENT 'アカウントリセットURI'
    , `user_id`                 bigint unsigned    NOT NULL                     COMMENT 'ユーザーID'
    , `auth_code`               varchar(10)        NOT NULL                     COMMENT '認証コード'
    , `create_datetime`         datetime           NOT NULL DEFAULT '1000-01-01 00:00:00' COMMENT '登録日時'
    , `create_user_id`          bigint unsigned    NOT NULL DEFAULT '0'         COMMENT '登録者ID'
    , `update_datetime`         datetime           NOT NULL DEFAULT '1000-01-01 00:00:00' COMMENT '更新日時'
    , `update_user_id`          bigint unsigned    NOT NULL DEFAULT '0'         COMMENT '更新者ID'
    , `delete_flag`             boolean            NOT NULL DEFAULT '0'         COMMENT '削除フラグ'
) ENGINE = INNODB COMMENT = 'ユーザーアカウントリセット';
-- -----------------------------------------------------------------------------
-- テーブル：system_setting
-- 作成者　：自動生成
-- -----------------------------------------------------------------------------

-- -----------------------------------------------------------------------------
-- PK：PK_SYSTEM_SETTING
-- -----------------------------------------------------------------------------
ALTER TABLE `system_setting` ADD CONSTRAINT PK_SYSTEM_SETTING PRIMARY KEY (
      `id`
);

-- -----------------------------------------------------------------------------
-- UK：UK_SYSTEM_SETTING_system_code
-- -----------------------------------------------------------------------------
ALTER TABLE `system_setting` ADD CONSTRAINT UK_SYSTEM_SETTING_system_code UNIQUE (
      `system_code`
);



-- -----------------------------------------------------------------------------
-- テーブル：user
-- 作成者　：自動生成
-- -----------------------------------------------------------------------------

-- -----------------------------------------------------------------------------
-- PK：PK_USER
-- -----------------------------------------------------------------------------
ALTER TABLE `user` ADD CONSTRAINT PK_USER PRIMARY KEY (
      `id`
);

-- -----------------------------------------------------------------------------
-- UK：UK_USER_user_account
-- -----------------------------------------------------------------------------
ALTER TABLE `user` ADD CONSTRAINT UK_USER_user_account UNIQUE (
      `user_account`
);


-- -----------------------------------------------------------------------------
-- Index：IDX_USER_01
-- -----------------------------------------------------------------------------
ALTER TABLE `user` ADD INDEX IDX_USER_01 (
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
-- テーブル：user_account_reset
-- 作成者　：自動生成
-- -----------------------------------------------------------------------------

-- -----------------------------------------------------------------------------
-- PK：PK_USER_ACCOUNT_RESET
-- -----------------------------------------------------------------------------
ALTER TABLE `user_account_reset` ADD CONSTRAINT PK_USER_ACCOUNT_RESET PRIMARY KEY (
      `account_reset_uri`
);




