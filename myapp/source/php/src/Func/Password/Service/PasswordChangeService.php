<?php

namespace App\Func\Password\Service;

use App\Common\App\AppContext;
use App\Common\Exception\DBException;
use App\Common\Exception\ServiceException;
use App\Common\Util\DateUtil;
use App\Common\Util\UrlUtil;
use App\Common\Validation\Validatation;
use App\Dao\SystemSetting\SystemSettingDao;
use App\Dao\User\UserAccessDao;
use App\Dao\User\UserAccountResetDao;
use App\Dao\User\UserDao;
use App\Func\Base\Service\DBBaseService;
use App\Func\Common\Service\MailService;
use DateInterval;
use Psr\Http\Message\UriInterface;
use Slim\Http\Uri;

/**
 * パスワード変更サービス。
 */
class PasswordChangeService extends DBBaseService {

    /**
     * コンストラクタ。
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * パスワード変更時の入力チェックを実施する。
     * @param array $data データ
     * @return array エラーリスト
     */
    private function validateForChange(array $data): array {

        $errors = [];

        // 入力チェックを実施
        $validation = Validatation::getInstance();

        // アカウントチェック
        $itemId = 'user_account';
        $itemName = 'アカウント';
        $validation
                ->oneOfTheFollowing()
                ->validateRequired($errors, $itemId, $itemName, $data[$itemId])
                ->validateEmail($errors, $itemId, $itemName, $data[$itemId])
                ->validateLength($errors, $itemId, $itemName, $data[$itemId], 255)
                ->end()
        ;

        // 認証コードチェック
        $itemId = 'auth_code';
        $itemName = '認証コード';
        $validation
                ->oneOfTheFollowing()
                ->validateRequired($errors, $itemId, $itemName, $data[$itemId])
                ->validateLength($errors, $itemId, $itemName, $data[$itemId], 10)
                ->end()
        ;

        // パスワードチェック
        $itemId = 'password';
        $itemName = '新しいパスワード';
        $validation
                ->oneOfTheFollowing()
                ->validateRequired($errors, $itemId, $itemName, $data[$itemId])
                ->validatePassword($errors, $itemId, $itemName, $data[$itemId])
                ->validateLength($errors, $itemId, $itemName, $data[$itemId], 100)
                ->end()
        ;

        // パスワードチェック
        $itemId = 'password_confirm';
        $itemName = '新しいパスワード（確認）';
        $validation
                ->oneOfTheFollowing()
                ->validateRequired($errors, $itemId, $itemName, $data[$itemId])
                ->validateLength($errors, $itemId, $itemName, $data[$itemId], 100)
                ->end()
        ;

        if (count($errors) <= 0) {

            // パスワードと確認欄の同一性チェック
            if ($data['password'] !== $data['password_confirm']) {
                $validation->createAnyError($errors, 'password_confirm', 'validation_password_confirm', []);
            }
        }

        return $errors;
    }

    /**
     * データを検証する。
     * @param array $data データ
     * @return boolean true 有効なデータ、false 無効なデータ
     */
    public function validateData(array $data) {

        // ユーザーアカウントリセットのURI
        $accountResetUri = $data['id'] ?? null;

        $systemSettingDao = new SystemSettingDao($this->dbConnection);
        $userAccountResetDao = new UserAccountResetDao($this->dbConnection);
        $userAccountResetRecord = $this->selectUserAccountReset($systemSettingDao, $userAccountResetDao, $accountResetUri);

        if ($userAccountResetRecord === null) {
            return false;
        }

        return true;
    }

    /**
     * パスワード変更処理。
     * @param array $data データ
     * @param UriInterface $uri URI
     */
    public function change(array $data, UriInterface $uri) {

        $errors = $this->validateForChange($data);
        if (count($errors) > 0) {
            throw new ServiceException($errors);
        }

        // DBへの更新
        try {
            $this->transaction($this->dbConnection, function($dbConnection) use($data) {

                // Daoの初期化
                $systemSettingDao = new SystemSettingDao($dbConnection);
                $userDao = new UserDao($dbConnection);
                $userAccessDao = new UserAccessDao($dbConnection);
                $userAccountResetDao = new UserAccountResetDao($dbConnection);

                // アカウントを取得する
                $userAccount = $data['user_account'];
                $userRecord = $userDao->selectByUserAccount($userAccount, true);

                // アカウント存在チェック
                if ($userRecord === null) {
                    // アカウントが存在しない場合
                    $this->throwDataNotFound('ユーザー', $userAccount);
                }

                // ユーザーアカウントリセットURIの取得
                $accountResetUri = $data['id'] ?? null;

                // ユーザーアカウントリセット情報の取得
                $userAccountResetRecord = $this->selectUserAccountReset($systemSettingDao, $userAccountResetDao, $accountResetUri);
                if ($userAccountResetRecord === null || $userAccountResetRecord['user_id'] !== $userRecord['user_id']) {
                    $this->throwDataNotFound('ユーザーアカウントリセット', $accountResetUri);
                }

                // 認証コードチェック
                if ($data['auth_code'] !== $userAccountResetRecord['auth_code']) {
                    // 認証コードが不正
                    $this->throwAnyError('auth_code', 'error_invalid_auth_code', []);
                }

                // 新しいパスワードで更新する
                $this->updateUserPassword($userDao, $userRecord['user_id'], $data['password']);
                // ユーザーアクセス情報をリセットする
                $this->updateUserAccess($userAccessDao, $userRecord['user_id']);
                // ユーザーアカウントリセット情報を削除する
                $userAccountResetDao->deleteByAccountResetUri($accountResetUri);
            });

            // メールを送信する
            $this->sendMailForPasswordChange($uri, $data['user_account']);
        } catch (DBException $ex) {

            throw $ex;
        }

    }

    /**
     * ユーザーアカウントリセット情報を取得する。
     * @param SystemSettingDao $systemSettingDao システム設定Dao
     * @param UserAccountResetDao $userAccountResetDao ユーザーアカウントリセットDAO
     * @param string $accountResetUri ユーザーアカウントリセットのURI
     * @return ?array レコード
     */
    private function selectUserAccountReset(SystemSettingDao $systemSettingDao, UserAccountResetDao $userAccountResetDao, string $accountResetUri): ?array {

        // ユーザーアカウントリセットのURI
        if ($accountResetUri === null) {
            return null;
        }

        // 現在日
        $systemDate = DateUtil::getSystemDate();

        // ユーザーアカウントリセット情報を取得する
        $userAccountResetRecord = $userAccountResetDao->selectByAccountResetUri($accountResetUri);

        // レコード存在チェック
        if ($userAccountResetRecord === null) {
            // レコードが存在しない
            return null;
        }

        // パスワードリセット有効期限（N分）
        $passwordResetExpiredMinutes = (int) $systemSettingDao->getFromCacheBySystemCode('PASSWORD_RESET_EXPIRED_MINUTES')['system_value'];

        $createDatetime = DateUtil::createDateTime($userAccountResetRecord['create_datetime']);
        if ($createDatetime < $systemDate->sub(new DateInterval('PT' . $passwordResetExpiredMinutes . 'M'))) {
            // 時間切れの場合
            return null;
        }

        return $userAccountResetRecord;
    }

    /**
     * ユーザパスワード情報を更新する。
     * @param UserDao $userDao ユーザDao
     * @param string $userId ユーザID
     * @param string $password パスワード
     * @return int 更新件数
     */
    private function updateUserPassword($userDao, $userId, $password) {

        $ret = 0;

        // ユーザーアクセス情報を更新する
        $updateCount = 0;
        $updateCount = $userDao->updatePassword([
            'user_id' => $userId,
            'password' => $password,
            'update_datetime' => $this->systemDate->format(DateUtil::DATETIME_HYPHEN_FORMAT_COMMON),
            'update_user_id' => 0
        ]);

        if ($updateCount !== 1) {
            // ユーザーアクセス情報の更新に失敗
            $this->throwDataNotFound('ユーザー', $userId);
        }

        return $ret;
    }

    /**
     * ユーザアクセス情報を更新する。
     * @param UserAccessDao $userAccessDao ユーザアクセスDao
     * @param string $userId ユーザID
     * @return int 更新件数
     */
    private function updateUserAccess($userAccessDao, $userId) {

        $ret = 0;

        // ロックを実施する（ロックを取得できるまで待機する）
        $userAccessRec = $userAccessDao->selectAccessByUserId($userId, true);

        if ($userAccessRec === null) {
            // レコードが存在しない場合
            $this->throwDataNotFound('ユーザーアクセス', $userId);
        }

        // ユーザーアクセス情報を更新する
        $updateCount = 0;
        $updateCount = $userAccessDao->updateAccessForClearAuthFailed([
            'user_id' => $userId,
            'update_datetime' => $this->systemDate->format(DateUtil::DATETIME_HYPHEN_FORMAT_COMMON),
            'update_user_id' => 0
        ]);

        if ($updateCount !== 1) {
            // ユーザーアクセス情報の更新に失敗
            $this->throwDataNotFound('ユーザーアクセス', $userId);
        }

        return $ret;
    }

    /**
     * ログインURLを生成する。
     * @param Uri $uri URI
     * @param string $userAccount ユーザーアカウント
     * @return string ログインURL
     */
    private function createLoginUrl($uri, $userAccount) {

        $userAccountEnc = \urlencode($userAccount);

        $loginUrl = UrlUtil::createRootUrlWithBase($uri, AppContext::get()->getBasePath()) . "/login?user_account={$userAccountEnc}";
        return $loginUrl;
    }

    /**
     * パスワード変更リクエストのメール送信処理。
     * @param UriInterface $uri URI
     * @param string $userAccount ユーザーアカウント
     */
    private function sendMailForPasswordChange($uri, $userAccount) {

        // ログインURLを生成する
        $loginUrl = $this->createLoginUrl($uri, $userAccount);
        // ログ出力する
        $this->logger->info("PasswordChange user_account={$userAccount}");

        $mailService = new MailService($this->dbConnection);
        $mailService->send(
                'User/PasswordChange',
                [
                    'loginUrl' => $loginUrl
                ],
                [
                    ['address' => $userAccount, 'name' => $userAccount]
                ]
        );
    }

}
