<?php

namespace App\Func\User\Service;

use App\Common\App\AppContext;
use App\Common\Exception\DBException;
use App\Common\Exception\ServiceException;
use App\Common\Util\DateUtil;
use App\Common\Util\UrlUtil;
use App\Common\Validation\Validatation;
use App\Constant\CommonConstant;
use App\Dao\SystemSetting\SystemSettingDao;
use App\Dao\User\UserAccessDao;
use App\Dao\User\UserDao;
use App\Dao\User\UserTempDao;
use App\Func\Base\Service\DBBaseService;
use App\Func\Common\Service\MailService;
use DateInterval;
use Psr\Http\Message\UriInterface;
use Slim\Http\Uri;

/**
 * ユーザー登録サービス。
 */
class UserRegistService extends DBBaseService {

    /**
     * コンストラクタ。
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * ユーザー登録時の入力チェックを実施する。
     * @param array $data データ
     * @return array エラーリスト
     */
    private function validateForRegist(array $data): array {

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

        // ユーザー名
        $itemId = 'user_name';
        $itemName = '名前';
        $validation
                ->oneOfTheFollowing()
                ->validateRequired($errors, $itemId, $itemName, $data[$itemId])
                ->validateLength($errors, $itemId, $itemName, $data[$itemId], 100)
                ->end()
        ;

        // ユーザー名カナ
        $itemId = 'user_name_kana';
        $itemName = '名前（カナ）';
        $validation
                ->oneOfTheFollowing()
                ->validateRequired($errors, $itemId, $itemName, $data[$itemId])
                ->validateKanaOrHalfChar($errors, $itemId, $itemName, $data[$itemId])
                ->validateLength($errors, $itemId, $itemName, $data[$itemId], 100)
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

        // アカウント登録のURI
        $userRegistUri = $data['id'] ?? null;

        $systemSettingDao = new SystemSettingDao($this->dbConnection);
        $userTempDao = new UserTempDao($this->dbConnection);
        $userTempRecord = $this->selectUserTemp($systemSettingDao, $userTempDao, $userRegistUri);

        if ($userTempRecord === null) {
            return false;
        }

        return true;
    }

    /**
     * ユーザー登録処理。
     * @param array $data データ
     * @param UriInterface $uri URI
     */
    public function regist(array $data, UriInterface $uri) {

        $errors = $this->validateForRegist($data);
        if (count($errors) > 0) {
            throw new ServiceException($errors);
        }

        // DBへの更新
        try {
            $this->transaction($this->dbConnection, function($dbConnection) use($data) {

                // Daoの初期化
                $systemSettingDao = new SystemSettingDao($dbConnection);
                $userDao = new UserDao($dbConnection);
                $userTempDao = new UserTempDao($dbConnection);
                $userAccessDao = new UserAccessDao($dbConnection);

                // アカウントを取得する
                $userAccount = $data['user_account'];
                // アカウント登録URIの取得
                $accountRegistUri = $data['id'] ?? null;

                // ユーザー仮登録情報の取得
                $userTempRecord = $this->selectUserTemp($systemSettingDao, $userTempDao, $accountRegistUri);
                if ($userTempRecord === null || $userTempRecord['user_account'] !== $userAccount) {
                    $this->throwDataNotFound('ユーザー仮登録', $accountRegistUri);
                }

                // 認証コードチェック
                if ($data['auth_code'] !== $userTempRecord['auth_code']) {
                    // 認証コードが不正
                    $this->throwAnyError('auth_code', 'error_invalid_auth_code', []);
                }

                // ユーザー情報を登録する
                $userId = $this->insertUser($userDao, $data);
                // ユーザーアクセス情報を登録
                $this->insertUserAccess($userAccessDao, $userId);
                // ユーザー仮登録情報を削除する
                $userTempDao->deleteByAccountRegistUri($accountRegistUri);
            });

            // メールを送信する
            $this->sendMailForUserRegist($uri, $data['user_account']);
        } catch (DBException $ex) {

            throw $ex;
        }

    }

    /**
     * ユーザー仮登録情報を取得する。
     * @param SystemSettingDao $systemSettingDao システム設定Dao
     * @param UserTempDao $userTempDao ユーザー仮登録DAO
     * @param string $accountRegistUri アカウント登録のURI
     * @return ?array レコード
     */
    private function selectUserTemp(SystemSettingDao $systemSettingDao, UserTempDao $userTempDao, string $accountRegistUri): ?array {

        // アカウント登録のURI
        if ($accountRegistUri === null) {
            return null;
        }

        // 現在日
        $systemDate = DateUtil::getSystemDate();

        // ユーザー仮登録情報を取得する
        $userTempRecord = $userTempDao->selectByAccountRegistUri($accountRegistUri);

        // レコード存在チェック
        if ($userTempRecord === null) {
            // レコードが存在しない
            return null;
        }

        // ユーザー登録有効期限（N分）
        $userTempExpiredMinutes = (int) $systemSettingDao->getFromCacheBySystemCode('USER_TEMP_EXPIRED_MINUTES')['system_value'];

        $createDatetime = DateUtil::createDateTime($userTempRecord['create_datetime']);
        if ($createDatetime < $systemDate->sub(new DateInterval('PT' . $userTempExpiredMinutes . 'M'))) {
            // 時間切れの場合
            return null;
        }

        return $userTempRecord;
    }

    /**
     * ユーザ情報を登録する。
     * @param UserDao $userDao ユーザDao
     * @param array $data データ
     * @return int ユーザーID
     */
    private function insertUser($userDao, $data): int {

        // ユーザー情報を登録する
        $userId = $userDao->insert([
            'user_account' => $data['user_account'],
            'password' => $data['password'],
            'email' => $data['user_account'],
            'user_name' => $data['user_name'],
            'user_name_kana' => $data['user_name_kana'],
            'authority' => CommonConstant::AUTH_NORMAL,
            'create_datetime' => $this->systemDate->format(DateUtil::DATETIME_HYPHEN_FORMAT_COMMON),
            'create_user_id' => 0,
            'update_datetime' => $this->systemDate->format(DateUtil::DATETIME_HYPHEN_FORMAT_COMMON),
            'update_user_id' => 0
        ]);

        return $userId;
    }

    /**
     * ユーザアクセス情報を登録する。
     * @param UserAccessDao $userAccessDao ユーザアクセスDao
     * @param string $userId ユーザID
     */
    private function insertUserAccess($userAccessDao, $userId) {

        // ユーザーアクセス情報を更新する
        $userAccessDao->insert([
            'user_id' => $userId,
            'create_datetime' => $this->systemDate->format(DateUtil::DATETIME_HYPHEN_FORMAT_COMMON),
            'create_user_id' => 0,
            'update_datetime' => $this->systemDate->format(DateUtil::DATETIME_HYPHEN_FORMAT_COMMON),
            'update_user_id' => 0
        ]);
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
     * ユーザー登録のメール送信処理。
     * @param UriInterface $uri URI
     * @param string $userAccount ユーザーアカウント
     */
    private function sendMailForUserRegist($uri, $userAccount) {

        // ログインURLを生成する
        $loginUrl = $this->createLoginUrl($uri, $userAccount);
        // ログ出力する
        $this->logger->info("UserRegist user_account={$userAccount}");

        $mailService = new MailService($this->dbConnection);
        $mailService->send(
                'User/UserRegist',
                [
                    'loginUrl' => $loginUrl
                ],
                [
                    ['address' => $userAccount, 'name' => $userAccount]
                ]
        );
    }

}
