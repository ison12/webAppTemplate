<?php

namespace App\Func\Login\Service;

use App\Common\Data\User;
use App\Common\Exception\DBException;
use App\Common\Exception\ServiceException;
use App\Common\Util\DateUtil;
use App\Common\Util\EncryptUtil;
use App\Common\Validation\Validatation;
use App\Dao\SystemSetting\SystemSettingDao;
use App\Dao\User\UserAccessDao;
use App\Dao\User\UserDao;
use App\Func\Base\Service\DBBaseService;

/**
 * ログインサービス。
 */
class LoginService extends DBBaseService {

    /**
     * コンストラクタ。
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * 認証時の入力チェックを実施する。
     * @param array $data データ
     * @return array エラーリスト
     */
    private function validateForAuth(array $data): array {

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

        // パスワードチェック
        $itemId = 'password';
        $itemName = 'パスワード';
        $validation
                ->oneOfTheFollowing()
                ->validateRequired($errors, $itemId, $itemName, $data[$itemId])
                ->validateLength($errors, $itemId, $itemName, $data[$itemId], 100)
                ->end()
        ;

        return $errors;
    }

    /**
     * 認証処理。
     * @param array $data データ
     * @param User $user ユーザー情報
     */
    public function auth(array $data, &$user) {

        $errors = $this->validateForAuth($data);
        if (count($errors) > 0) {
            throw new ServiceException($errors);
        }

        $user = null;

        $exception = null;

        // DBへの更新
        try {
            $this->transaction($this->dbConnection, function($dbConnection) use($data, &$exception, &$user) {

                /*
                 * 例外発生時にもコミットしたいので、あえて try-catch で囲み、例外発生時に例外オブジェクトを変数に保持するようにする
                 */
                try {
                    $userDao = new UserDao($dbConnection);
                    $userAccessDao = new UserAccessDao($dbConnection);
                    $systemSettingDao = new SystemSettingDao($dbConnection);

                    // アカウントを取得する
                    $userAccount = $data['user_account'];
                    // パスワードを取得する
                    $password = $data['password'];

                    $userRecord = $userDao->selectByUserAccount($userAccount, true);

                    // ログイン判定（アカウント存在チェック）
                    if ($userRecord === null || (bool) $userRecord['delete_flag']) {
                        $this->throwInvalidAuthException();
                    }
                    // ログイン判定（パスワードチェック）
                    $authSuccess = false;
                    if (EncryptUtil::equalsHash($password, $userRecord['password'])) {
                        $authSuccess = true;
                    }

                    // ユーザアクセス情報を更新する。
                    $this->updateUserAccess($systemSettingDao, $userAccessDao, $userRecord['id'], $authSuccess);

                    if (!$authSuccess) {
                        $this->throwInvalidAuthException();
                    }

                    // ユーザ情報を生成する
                    $user = $this->createUser($userRecord);
                } catch (\Exception $ex) {
                    $exception = $ex;
                }

                return $user;
            });
        } catch (DBException $ex) {

            throw $ex;
        }

        if ($exception !== null) {
            // 例外が発生している場合は、例外をスローする
            throw $exception;
        }
    }

    /**
     * ユーザ情報を生成する。
     * @param User $user ユーザ情報
     */
    private function createUser($user) {

        $userObj = new User();
        $userObj->id = $user['id'];
        $userObj->user_account = $user['user_account'];
        $userObj->user_name = $user['user_name'];
        $userObj->user_name_kana = $user['user_name_kana'];
        $userObj->authority = $user['authority'];

        return $userObj;
    }

    /**
     * ユーザアクセス情報を更新する。
     * @param SystemSettingDao $systemSettingDao システム設定Dao
     * @param UserAccessDao $userAccessDao ユーザアクセスDao
     * @param string $userId ユーザID
     * @param boolean $success 成功有無フラグ
     * @return int 更新件数
     */
    private function updateUserAccess($systemSettingDao, $userAccessDao, $userId, $success) {

        $ret = 0;

        // アカウントロックするまでの認証失敗回数
        $lockLimitCount = (int) $systemSettingDao->getFromCacheBySystemCode('USER_ACCOUNT_AUTH_LOCK_FAILED_COUNT')['system_value'];
        // アカウントロックのリセット時間（分）
        $lockResetTime = (int) $systemSettingDao->getFromCacheBySystemCode('USER_ACCOUNT_AUTH_LOCK_RESET_TIME')['system_value'];
        // ロックを実施する（ロックを取得できるまで待機する）
        $userAccessRec = $userAccessDao->selectAccessByUserId($userId, true);

        if ($userAccessRec === null) {
            // レコードが存在しない場合
            $this->throwDataNotFound('ユーザーアクセス', $userId);
        }

        // 認証失敗数のリセット判定を実施（アクセス時間のN分経過後の時間を算出し時間が経過していたらリセット）
        $authFailedCount = $userAccessRec['auth_failed_count'] === null ? 0 : (int) $userAccessRec['auth_failed_count'];
        if ($userAccessRec['auth_failed_datetime'] !== null) {
            $authFailedDatetime = strtotime("+{$lockResetTime} min {$userAccessRec['auth_failed_datetime']}");
            $nowTime = $this->systemDate->format('U');

            // 現在時間が、N分経過後の時間を超えている場合、認証失敗件数を0にする
            if ($nowTime > $authFailedDatetime) {
                $authFailedCount = 0;
            }
        }

        // 認証失敗数を設定する
        if ($success && $authFailedCount < $lockLimitCount) {
            // 認証失敗数が規定値をオーバーしておらず、ログインに成功した場合、失敗数をリセットする
            $authFailedCount = 0;
        } else {
            // 上記以外
            $authFailedCount++;
        }

        // ユーザーアクセス情報を更新する
        $updateCount = 0;
        if ($success) {
            $updateCount = $userAccessDao->updateAccessForSuccessByUserId($userId
                    , $this->systemDate->format(DateUtil::DATETIME_HYPHEN_FORMAT_COMMON));
        } else {
            $updateCount = $userAccessDao->updateAccessForFailedByUserId($userId
                    , $this->systemDate->format(DateUtil::DATETIME_HYPHEN_FORMAT_COMMON)
                    , $authFailedCount);
        }

        if ($updateCount !== 1) {
            // ユーザーアクセス情報の更新に失敗
            $this->throwDataNotFound('ユーザーアクセス', $userId);
        }

        // -----------------------------------------------------------------
        // 認証に成功していても、失敗していても、アクセスを制限するためにチェックを実施する
        // 認証失敗制限値を超えているかのチェック
        if ($authFailedCount > $lockLimitCount) {
            $this->throwInvalidAuthFailedLimitException();
        }

        return $ret;
    }

    /**
     * 不正認証例外をスローする。
     * @throws ServiceException 不正認証例外
     */
    private function throwInvalidAuthException() {

        $this->throwAnyError('error_invalid_auth', 'error_invalid_auth', []);
    }

    /**
     * 不正認証失敗数例外をスローする。
     * @throws ServiceException 不正認証失敗数例外
     */
    private function throwInvalidAuthFailedLimitException() {

        $this->throwAnyError('error_invalid_auth_failed_limit', 'error_invalid_auth_failed_limit', []);
    }

}
