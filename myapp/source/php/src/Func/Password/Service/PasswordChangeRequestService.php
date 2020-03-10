<?php

namespace App\Func\Password\Service;

use App\Common\Exception\DBException;
use App\Common\Exception\ServiceException;
use App\Common\Util\DateUtil;
use App\Common\Util\GuidUtil;
use App\Common\Util\RandomUtil;
use App\Common\Util\UrlUtil;
use App\Common\Validation\Validatation;
use App\Dao\SystemSetting\SystemSettingDao;
use App\Dao\User\UserAccountResetDao;
use App\Dao\User\UserDao;
use App\Func\Base\Service\DBBaseService;
use App\Func\Common\Service\MailService;
use DateInterval;
use Psr\Http\Message\UriInterface;
use Slim\Http\Uri;

/**
 * パスワード変更リクエストサービス。
 */
class PasswordChangeRequestService extends DBBaseService {

    /**
     * コンストラクタ。
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * パスワード変更リクエスト時の入力チェックを実施する。
     * @param array $data データ
     * @return array エラーリスト
     */
    private function validateForChangeRequest(array $data): array {

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
     * パスワード変更リクエスト処理。
     * @param array $data データ
     * @param UriInterface $uri URI
     */
    public function changeRequest(array $data, UriInterface $uri) {

        $errors = $this->validateForChangeRequest($data);
        if (count($errors) > 0) {
            throw new ServiceException($errors);
        }

        $userAccount = $data['user_account'];
        $userAccountResetData = null;

        // DBへの更新
        try {
            // Daoの初期化
            $systemSettingDao = new SystemSettingDao($this->dbConnection);
            // パスワードリセット有効期限（N分）
            $passwordResetExpiredMinutes = (int) $systemSettingDao->getFromCacheBySystemCode('PASSWORD_RESET_EXPIRED_MINUTES')['system_value'];

            $userAccountResetData = $this->transaction($this->dbConnection, function($dbConnection) use($userAccount, $passwordResetExpiredMinutes) {

                // Daoの初期化
                $userDao = new UserDao($dbConnection);
                $userAccountResetDao = new UserAccountResetDao($dbConnection);

                // アカウントを取得する
                $userRecord = $userDao->selectByUserAccount($userAccount);

                // アカウント存在チェック
                if ($userRecord === null) {
                    // アカウントが存在しない場合は何もせず処理を終了する
                    // （ここでエラーメッセージを通知すると、不正アクセス者にアカウントの存在が認知されるのを防ぐため、正常かエラーかを分からないようにする）
                    $this->logger->alert("PasswordChangeRequest {$userAccount} がuserテーブルに存在しない");
                    return null;
                }

                // ユーザーアカウントリセット情報を登録する
                $userAccountResetData = $this->insertUserAccountResetDao($userAccountResetDao, $userRecord);

                // 現時点からの期限切れ時間を計算して古いデータを削除する
                $expiredDate = DateUtil::getSystemDate();
                $expiredDate->sub(new DateInterval('PT' . $passwordResetExpiredMinutes . 'M'));
                $userAccountResetDao->deleteByExpiredDate($expiredDate);

                return $userAccountResetData;
            });

            if ($userAccountResetData !== null) {
                // 期限切れ時間の計算
                $expiredDate = DateUtil::createDateTime($userAccountResetData['create_datetime']);
                $expiredDate->add(new DateInterval('PT' . $passwordResetExpiredMinutes . 'M'));

                // アカウントリセット情報を登録できた場合にのみメールを送信する
                $this->sendMailForPasswordChangeRequest($uri, $expiredDate, $userAccount, $userAccountResetData);
            }
        } catch (DBException $ex) {

            throw $ex;
        }

    }

    /**
     * 登録処理。
     * @param UserAccountResetDao $userAccountResetDao ユーザーアカウントリセットDAO
     * @param array $userRecord ユーザーレコード
     * @return array レコード
     */
    private function insertUserAccountResetDao(UserAccountResetDao $userAccountResetDao, array $userRecord): array {

        $data = [];

        $data['account_reset_uri'] = GuidUtil::generateGuid();
        $data['user_id'] = $userRecord['user_id'];
        $data['auth_code'] = RandomUtil::generateRandomNumber(6);
        $data['create_datetime'] = $this->systemDate->format(DateUtil::DATETIME_HYPHEN_MICRO_FORMAT_COMMON);
        $data['create_user_id'] = 0;
        $data['update_datetime'] = $this->systemDate->format(DateUtil::DATETIME_HYPHEN_MICRO_FORMAT_COMMON);
        $data['update_user_id'] = 0;

        // 登録クエリ発行
        $userAccountResetDao->insert($data);

        return $data;
    }

    /**
     * パスワード変更URLを生成する。
     * @param Uri $uri URI
     * @param string $userAccount ユーザーアカウント
     * @param string $id ID
     * @return string パスワード変更URL
     */
    private function createPasswordChangeUrl($uri, $userAccount, $id) {

        $userAccountEnc = \urlencode($userAccount);
        $idEnc = \urlencode($id);

        $passwordChangeUrl = UrlUtil::createRootUrlWithBase($uri) . "/password/change?user_account={$userAccountEnc}&id={$idEnc}";
        return $passwordChangeUrl;
    }

    /**
     * パスワード変更リクエストのメール送信処理。
     * @param UriInterface $uri URI
     * @param \DateTime $expiredDate 有効期限切れ日時
     * @param string $userAccount ユーザーアカウント
     * @param array $userAccountResetData ユーザーアカウントリセット情報
     */
    private function sendMailForPasswordChangeRequest($uri, \DateTime $expiredDate, $userAccount, $userAccountResetData) {

        // パスワード変更URLを生成する
        $passwordChangeUrl = $this->createPasswordChangeUrl($uri, $userAccount, $userAccountResetData['account_reset_uri']);
        // ログ出力する
        $this->logger->info("PasswordChangeRequest uri={$passwordChangeUrl}, user_account={$userAccount}");

        $mailService = new MailService($this->dbConnection);
        $mailService->send(
                'User/PasswordChangeRequest',
                [
                    'passwordChangeUrl' => $passwordChangeUrl,
                    'authCode' => $userAccountResetData['auth_code'],
                    'expiredDate' => $expiredDate->format(DateUtil::DATETIME_FORMAT_COMMON),
                ],
                [
                    ['address' => $userAccount, 'name' => $userAccount]
                ]
        );
    }

}
