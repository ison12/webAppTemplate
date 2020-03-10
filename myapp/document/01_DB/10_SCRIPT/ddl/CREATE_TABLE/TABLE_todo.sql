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
