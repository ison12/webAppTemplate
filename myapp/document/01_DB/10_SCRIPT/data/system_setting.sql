INSERT INTO `system_setting` (
    `id`
   ,`system_code`
   ,`system_name`
   ,`system_value`
   ,`create_datetime`
   ,`create_user_id`
   ,`update_datetime`
   ,`update_user_id`
   ,`delete_flag`
) VALUES (
    1
   ,'SYSTEM_URL'
   ,'システムURL'
   ,'http://localhost:5555/myapp'
   ,'1000-01-01 00:00:00'
   ,0
   ,'1000-01-01 00:00:00'
   ,0
   ,0
);

INSERT INTO `system_setting` (
    `id`
   ,`system_code`
   ,`system_name`
   ,`system_value`
   ,`create_datetime`
   ,`create_user_id`
   ,`update_datetime`
   ,`update_user_id`
   ,`delete_flag`
) VALUES (
    2
   ,'USER_TEMP_EXPIRED_MINUTES'
   ,'ユーザー仮登録有効期限（N分）'
   ,'30'
   ,'1000-01-01 00:00:00'
   ,0
   ,'1000-01-01 00:00:00'
   ,0
   ,0
);

INSERT INTO `system_setting` (
    `id`
   ,`system_code`
   ,`system_name`
   ,`system_value`
   ,`create_datetime`
   ,`create_user_id`
   ,`update_datetime`
   ,`update_user_id`
   ,`delete_flag`
) VALUES (
    3
   ,'PASSWORD_RESET_EXPIRED_MINUTES'
   ,'パスワードリセット有効期限（N分）'
   ,'30'
   ,'1000-01-01 00:00:00'
   ,0
   ,'1000-01-01 00:00:00'
   ,0
   ,0
);

INSERT INTO `system_setting` (
    `id`
   ,`system_code`
   ,`system_name`
   ,`system_value`
   ,`create_datetime`
   ,`create_user_id`
   ,`update_datetime`
   ,`update_user_id`
   ,`delete_flag`
) VALUES (
    4
   ,'SMTP_SERVER_HOST'
   ,'SMTPサーバー（ホスト）'
   ,'myapp.com'
   ,'1000-01-01 00:00:00'
   ,0
   ,'1000-01-01 00:00:00'
   ,0
   ,0
);

INSERT INTO `system_setting` (
    `id`
   ,`system_code`
   ,`system_name`
   ,`system_value`
   ,`create_datetime`
   ,`create_user_id`
   ,`update_datetime`
   ,`update_user_id`
   ,`delete_flag`
) VALUES (
    5
   ,'SMTP_SERVER_PORT'
   ,'SMTPサーバー（ポート番号）'
   ,'587'
   ,'1000-01-01 00:00:00'
   ,0
   ,'1000-01-01 00:00:00'
   ,0
   ,0
);

INSERT INTO `system_setting` (
    `id`
   ,`system_code`
   ,`system_name`
   ,`system_value`
   ,`create_datetime`
   ,`create_user_id`
   ,`update_datetime`
   ,`update_user_id`
   ,`delete_flag`
) VALUES (
    6
   ,'SMTP_SERVER_USER_ID'
   ,'SMTPサーバー（ユーザーID）'
   ,'myapp@myapp.com'
   ,'1000-01-01 00:00:00'
   ,0
   ,'1000-01-01 00:00:00'
   ,0
   ,0
);

INSERT INTO `system_setting` (
    `id`
   ,`system_code`
   ,`system_name`
   ,`system_value`
   ,`create_datetime`
   ,`create_user_id`
   ,`update_datetime`
   ,`update_user_id`
   ,`delete_flag`
) VALUES (
    7
   ,'SMTP_SERVER_PASSWORD'
   ,'SMTPサーバー（パスワード）'
   ,'passwordを入力する'
   ,'1000-01-01 00:00:00'
   ,0
   ,'1000-01-01 00:00:00'
   ,0
   ,0
);

INSERT INTO `system_setting` (
    `id`
   ,`system_code`
   ,`system_name`
   ,`system_value`
   ,`create_datetime`
   ,`create_user_id`
   ,`update_datetime`
   ,`update_user_id`
   ,`delete_flag`
) VALUES (
    8
   ,'SMTP_SERVER_SECURE'
   ,'SMTPサーバー（セキュア）'
   ,'tls'
   ,'1000-01-01 00:00:00'
   ,0
   ,'1000-01-01 00:00:00'
   ,0
   ,0
);

INSERT INTO `system_setting` (
    `id`
   ,`system_code`
   ,`system_name`
   ,`system_value`
   ,`create_datetime`
   ,`create_user_id`
   ,`update_datetime`
   ,`update_user_id`
   ,`delete_flag`
) VALUES (
    9
   ,'MAIL_FROM_ADDRESS'
   ,'メール送信時のFromアドレス'
   ,'myapp@myapp.com'
   ,'1000-01-01 00:00:00'
   ,0
   ,'1000-01-01 00:00:00'
   ,0
   ,0
);

INSERT INTO `system_setting` (
    `id`
   ,`system_code`
   ,`system_name`
   ,`system_value`
   ,`create_datetime`
   ,`create_user_id`
   ,`update_datetime`
   ,`update_user_id`
   ,`delete_flag`
) VALUES (
    10
   ,'MAIL_FROM_NAME'
   ,'メール送信時のFrom名'
   ,'MyApp'
   ,'1000-01-01 00:00:00'
   ,0
   ,'1000-01-01 00:00:00'
   ,0
   ,0
);

INSERT INTO `system_setting` (
    `id`
   ,`system_code`
   ,`system_name`
   ,`system_value`
   ,`create_datetime`
   ,`create_user_id`
   ,`update_datetime`
   ,`update_user_id`
   ,`delete_flag`
) VALUES (
    11
   ,'MAIL_TO_ADDRESS'
   ,'管理者メール送信先アドレス'
   ,'myapp@myapp.com'
   ,'1000-01-01 00:00:00'
   ,0
   ,'1000-01-01 00:00:00'
   ,0
   ,0
);

INSERT INTO `system_setting` (
    `id`
   ,`system_code`
   ,`system_name`
   ,`system_value`
   ,`create_datetime`
   ,`create_user_id`
   ,`update_datetime`
   ,`update_user_id`
   ,`delete_flag`
) VALUES (
    12
   ,'MAIL_TO_NAME'
   ,'管理者メール送信先名'
   ,'MyApp'
   ,'1000-01-01 00:00:00'
   ,0
   ,'1000-01-01 00:00:00'
   ,0
   ,0
);

INSERT INTO `system_setting` (
    `id`
   ,`system_code`
   ,`system_name`
   ,`system_value`
   ,`create_datetime`
   ,`create_user_id`
   ,`update_datetime`
   ,`update_user_id`
   ,`delete_flag`
) VALUES (
    13
   ,'MAIL_REPLY_TO_ADDRESS'
   ,'メール返信時のReplyToアドレス'
   ,'myapp@myapp.com'
   ,'1000-01-01 00:00:00'
   ,0
   ,'1000-01-01 00:00:00'
   ,0
   ,0
);

INSERT INTO `system_setting` (
    `id`
   ,`system_code`
   ,`system_name`
   ,`system_value`
   ,`create_datetime`
   ,`create_user_id`
   ,`update_datetime`
   ,`update_user_id`
   ,`delete_flag`
) VALUES (
    14
   ,'MAIL_REPLY_TO_NAME'
   ,'メール返信時のReplyTo名'
   ,'MyApp'
   ,'1000-01-01 00:00:00'
   ,0
   ,'1000-01-01 00:00:00'
   ,0
   ,0
);

INSERT INTO `system_setting` (
    `id`
   ,`system_code`
   ,`system_name`
   ,`system_value`
   ,`create_datetime`
   ,`create_user_id`
   ,`update_datetime`
   ,`update_user_id`
   ,`delete_flag`
) VALUES (
    15
   ,'USER_ACCOUNT_AUTH_LOCK_FAILED_COUNT'
   ,'アカウントロックするまでの認証失敗回数'
   ,'10'
   ,'1000-01-01 00:00:00'
   ,0
   ,'1000-01-01 00:00:00'
   ,0
   ,0
);

INSERT INTO `system_setting` (
    `id`
   ,`system_code`
   ,`system_name`
   ,`system_value`
   ,`create_datetime`
   ,`create_user_id`
   ,`update_datetime`
   ,`update_user_id`
   ,`delete_flag`
) VALUES (
    16
   ,'USER_ACCOUNT_AUTH_LOCK_RESET_TIME'
   ,'アカウントロックのリセット時間（分）'
   ,'5'
   ,'1000-01-01 00:00:00'
   ,0
   ,'1000-01-01 00:00:00'
   ,0
   ,0
);

INSERT INTO `system_setting` (
    `id`
   ,`system_code`
   ,`system_name`
   ,`system_value`
   ,`create_datetime`
   ,`create_user_id`
   ,`update_datetime`
   ,`update_user_id`
   ,`delete_flag`
) VALUES (
    17
   ,'USER_ACCOUNT_RESET_AUTH_CODE_LENGTH'
   ,'パスワードリセット時の認証コード桁数'
   ,'6'
   ,'1000-01-01 00:00:00'
   ,0
   ,'1000-01-01 00:00:00'
   ,0
   ,0
);

