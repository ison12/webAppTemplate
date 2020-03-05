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
