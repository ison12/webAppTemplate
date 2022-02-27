USE myapp;

INSERT INTO `user` (
    `user_id`
   ,`user_account`
   ,`password`
   ,`email`
   ,`user_name`
   ,`user_name_kana`
   ,`authority`
   ,`create_datetime`
   ,`create_user_id`
   ,`update_datetime`
   ,`update_user_id`
) VALUES (
    1
   ,'myapp@any.domain.com.test'
   ,'$2y$10$Sp9mz16qruXF3bwByS8hPOgLTNOlxwsBmAf0.dCy75x8hzDO9h8xK'
   ,'myapp@any.domain.com.test'
   ,'MyApp 管理者'
   ,'マイアップ カンリシャ'
   ,'admin'
   ,'1000-01-01 00:00:00'
   ,0
   ,'2020-03-04 18:02:01'
   ,0
);

