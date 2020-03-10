<?= \App\Constant\CommonConstant::APP_NAME ?> にてユーザーの仮登録が行われようとしました。

しかし、既にアカウント <?= $userAccount ?> が存在しているため、仮登録できませんでした。
以下の何れかの操作を行ってください。

1. パスワードを覚えている場合は、ログインする。
<?= $loginUrl ?>


2. パスワードを忘れた場合はパスワード変更を行う。
<?= $passwordChangeRequestUrl ?>

