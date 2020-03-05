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
