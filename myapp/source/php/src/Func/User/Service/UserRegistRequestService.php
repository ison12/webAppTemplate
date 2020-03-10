<?php

namespace App\Func\User\Service;

use App\Common\Exception\DBException;
use App\Common\Exception\ServiceException;
use App\Common\Util\DateUtil;
use App\Common\Util\GuidUtil;
use App\Common\Util\RandomUtil;
use App\Common\Util\UrlUtil;
use App\Common\Validation\Validatation;
use App\Dao\SystemSetting\SystemSettingDao;
use App\Dao\User\UserDao;
use App\Dao\User\UserTempDao;
use App\Func\Base\Service\DBBaseService;
use App\Func\Common\Service\MailService;
use DateInterval;
use DateTime;
use Psr\Http\Message\UriInterface;
use Slim\Http\Uri;

/**
 * ユーザー登録リクエストサービス。
 */
class UserRegistRequestService extends DBBaseService {

    /**
     * コンストラクタ。
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * ユーザー登録リクエスト時の入力チェックを実施する。
     * @param array $data データ
     * @return array エラーリスト
     */
    private function validateForUserRegistRequest(array $data): array {

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

        return $errors;
    }

    /**
     * ユーザー登録リクエスト処理。
     * @param array $data データ
     * @param UriInterface $uri URI
     */
    public function registRequest(array $data, UriInterface $uri) {

        $errors = $this->validateForUserRegistRequest($data);
        if (count($errors) > 0) {
            throw new ServiceException($errors);
        }

        $userAccount = $data['user_account'];
        $userTempData = null;

        // DBへの更新
        try {
            // Daoの初期化
            $systemSettingDao = new SystemSettingDao($this->dbConnection);
            // ユーザー登録有効期限（N分）
            $userTempExpiredMinutes = (int) $systemSettingDao->getFromCacheBySystemCode('USER_TEMP_EXPIRED_MINUTES')['system_value'];

            $userTempData = $this->transaction($this->dbConnection, function($dbConnection) use($userAccount, $userTempExpiredMinutes) {

                // Daoの初期化
                $userDao = new UserDao($dbConnection);
                $userTempDao = new UserTempDao($dbConnection);

                // アカウントを取得する
                $userRecord = $userDao->selectByUserAccount($userAccount);

                // アカウント存在チェック
                if ($userRecord !== null) {
                    // アカウントが存在する場合は、仮登録できない
                    // （ここでエラーメッセージを通知すると、不正アクセス者にアカウントの存在が認知されるため、正常かエラーかを分からないようにする）
                    return null;
                }

                // ユーザー仮登録情報を登録する
                $userTempData = $this->insertUserTemp($userTempDao, $userAccount);

                // 現時点からの期限切れ時間を計算して古いデータを削除する
                $expiredDate = DateUtil::getSystemDate();
                $expiredDate->sub(new DateInterval('PT' . $userTempExpiredMinutes . 'M'));
                $userTempDao->deleteByExpiredDate($expiredDate);

                return $userTempData;
            });

            if ($userTempData !== null) {
                // 期限切れ時間の計算
                $expiredDate = DateUtil::createDateTime($userTempData['create_datetime']);
                $expiredDate->add(new DateInterval('PT' . $userTempExpiredMinutes . 'M'));

                // ユーザー仮登録できた場合にのみメールを送信する
                $this->sendMailForUserRegistRequest($uri, $expiredDate, $userAccount, $userTempData);
            } else {

                // ユーザーが既に登録されている場合のメール送信
                $this->sendMailForUserAlreadyRegist($uri, $userAccount);
            }
        } catch (DBException $ex) {

            throw $ex;
        }

    }

    /**
     * ユーザー仮登録処理。
     * @param UserTempDao $userTempDao ユーザー仮登録DAO
     * @param string $userAccount ユーザーアカウント
     * @return array レコード
     */
    private function insertUserTemp(UserTempDao $userTempDao, string $userAccount): array {

        $data = [];

        $data['account_regist_uri'] = GuidUtil::generateGuid();
        $data['auth_code'] = RandomUtil::generateRandomNumber(6);
        $data['user_account'] = $userAccount;
        $data['create_datetime'] = $this->systemDate->format(DateUtil::DATETIME_HYPHEN_MICRO_FORMAT_COMMON);
        $data['create_user_id'] = 0;
        $data['update_datetime'] = $this->systemDate->format(DateUtil::DATETIME_HYPHEN_MICRO_FORMAT_COMMON);
        $data['update_user_id'] = 0;

        // 登録クエリ発行
        $userTempDao->insert($data);

        return $data;
    }

    /**
     * ユーザー登録URLを生成する。
     * @param Uri $uri URI
     * @param string $userAccount ユーザーアカウント
     * @param string $id ID
     * @return string ユーザー登録URL
     */
    private function createUserRegistUrl($uri, $userAccount, $id) {

        $userAccountEnc = \urlencode($userAccount);
        $idEnc = \urlencode($id);

        $userRegistUrl = UrlUtil::createRootUrlWithBase($uri) . "/user/regist?user_account={$userAccountEnc}&id={$idEnc}";
        return $userRegistUrl;
    }

    /**
     * ログインURLを生成する。
     * @param Uri $uri URI
     * @param string $userAccount ユーザーアカウント
     * @return string ログインURL
     */
    private function createLoginUrl($uri, $userAccount) {

        $userAccountEnc = \urlencode($userAccount);

        $loginUrl = UrlUtil::createRootUrlWithBase($uri) . "/login?user_account={$userAccountEnc}";
        return $loginUrl;
    }

    /**
     * パスワード変更リクエストURLを生成する。
     * @param Uri $uri URI
     * @param string $userAccount ユーザーアカウント
     * @return string パスワード変更リクエストURL
     */
    private function createPasswordChangeRequestUrl($uri, $userAccount) {

        $userAccountEnc = \urlencode($userAccount);

        $userRegistUrl = UrlUtil::createRootUrlWithBase($uri) . "/password/changeRequest?user_account={$userAccountEnc}";
        return $userRegistUrl;
    }

    /**
     * ユーザー登録リクエストのメール送信処理。
     * @param UriInterface $uri URI
     * @param DateTime $expiredDate 有効期限切れ日時
     * @param string $userAccount ユーザーアカウント
     * @param array $userTempData ユーザーアカウントリセット情報
     */
    private function sendMailForUserRegistRequest($uri, DateTime $expiredDate, $userAccount, $userTempData) {

        // ユーザー登録URLを生成する
        $userRegistUrl = $this->createUserRegistUrl($uri, $userAccount, $userTempData['account_regist_uri']);
        // ログ出力する
        $this->logger->info("UserRegistRequest uri={$userRegistUrl}, user_account={$userAccount}");

        $mailService = new MailService($this->dbConnection);
        $mailService->send(
                'User/UserRegistRequest',
                [
                    'userRegistUrl' => $userRegistUrl,
                    'authCode' => $userTempData['auth_code'],
                    'expiredDate' => $expiredDate->format(DateUtil::DATETIME_FORMAT_COMMON),
                ],
                [
                    ['address' => $userAccount, 'name' => $userAccount]
                ]
        );
    }

    /**
     * 既にユーザーが存在する場合のメール送信処理。
     * @param UriInterface $uri URI
     * @param DateTime $expiredDate 有効期限切れ日時
     * @param string $userAccount ユーザーアカウント
     */
    private function sendMailForUserAlreadyRegist($uri, $userAccount) {

        // ログ出力する
        $this->logger->alert("UserRegistRequest user_account={$userAccount} ... userテーブルに既に存在する");

        $mailService = new MailService($this->dbConnection);
        $mailService->send(
                'User/UserAlreadyRegist',
                [
                    'userAccount' => $userAccount,
                    'loginUrl' => $this->createLoginUrl($uri, $userAccount),
                    'passwordChangeRequestUrl' => $this->createPasswordChangeRequestUrl($uri, $userAccount),
                ],
                [
                    ['address' => $userAccount, 'name' => $userAccount]
                ]
        );
    }

}
