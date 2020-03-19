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


