<?php

namespace App\Func\User\Service;

use App\Common\Data\User;
use App\Common\Exception\ServiceException;
use App\Common\Util\DateUtil;
use App\Common\Validation\Validatation;
use App\Constants\Entity\ConstTUser;
use App\Dao\User\UserAccessDao;
use App\Dao\User\UserDao;
use App\Func\Base\Service\BaseService;
use DateTime;

/**
 * ユーザーサービス。
 */
class UserService extends BaseService {

    /**
     * コンストラクタ。
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * ランダムなパスワードを生成する。
     * @return string ランダムなパスワード
     */
    public function generatePassword(): string {

        $length = 30;
        return substr(bin2hex(random_bytes($length)), 0, $length);
    }

    /**
     * アカウント認証情報のURIを生成する。
     * @param string $account アカウント
     * @param string $password パスワード
     * @return string アカウント認証情報のURI
     */
    public function createAccountInfoUri($account, $password): string {

        return 'account=' . urlencode($account) . '&password=' . urlencode($password);
    }

    /**
     * 登録処理。
     * @param UserDao $userDao ユーザーDAO
     * @param UserAccessDao $userAccessDao ユーザーアクセスDAO
     * @param DateTime $systemDate システム日付
     * @param array $data データ
     * @param User $user ユーザー情報
     * @return array レコード
     */
    public function saveForInsert(UserDao $userDao
    , UserAccessDao $userAccessDao
    , DateTime $systemDate
    , array $data
    , User $user = null): array {

        // 登録クエリ発行
        $data[ConstTUser::CREATE_DATETIME] = $systemDate->format(DateUtil::DATETIME_HYPHEN_MICRO_FORMAT_COMMON);
        $data[ConstTUser::CREATE_USER_ID] = $user->id ?? 0;
        $data[ConstTUser::UPDATE_DATETIME] = $systemDate->format(DateUtil::DATETIME_HYPHEN_MICRO_FORMAT_COMMON);
        $data[ConstTUser::UPDATE_USER_ID] = $user->id ?? 0;
        $data[ConstTUser::DELETE_FLAG] = 0;

        $id = $userDao->insert($data);
        $data[ConstTUser::ID] = $id;

        $userAccessDao->insert($data);

        return $data;
    }

    /**
     * 更新処理。
     * @param UserDao $userDao ユーザーDAO
     * @param UserAccessDao $userAccessDao ユーザーアクセスDAO
     * @param DateTime $systemDate システム日付
     * @param array $data データ
     * @param User $user ユーザー情報
     * @return array レコード
     */
    public function saveForUpdatePassword(UserDao $userDao
    , UserAccessDao $userAccessDao
    , DateTime $systemDate
    , array $data
    , User $user = null): array {

        // 更新クエリ発行
        $data[ConstTUser::UPDATE_DATETIME] = $systemDate->format(DateUtil::DATETIME_HYPHEN_MICRO_FORMAT_COMMON);
        $data[ConstTUser::UPDATE_USER_ID] = $user->id ?? 0;

        $retUser = $userDao->updatePassword($data);
        $retUserAccess = $userAccessDao->updateAccessForClearAuthFailed($data);

        if ($retUser === 0) {
            // 更新に失敗
            $error = Validatation::createError(
                            'error_data_not_found'
                            , $this->errorMessage->get('error_data_not_found', ['%itemName%' => 'ユーザー', '%id%' => $data['id']]));
            throw new ServiceException([$error]);
        }

        if ($retUserAccess === 0) {
            // 更新に失敗
            $error = Validatation::createError(
                            'error_data_not_found'
                            , $this->errorMessage->get('error_data_not_found', ['%itemName%' => 'ユーザーアクセス', '%id%' => $data['id']]));
            throw new ServiceException([$error]);
        }

        return $data;
    }

    /**
     * 削除処理。
     * @param UserDao $userDao ユーザーDAO
     * @param UserAccessDao $userAccessDao ユーザーアクセスDAO
     * @param array $data データ
     */
    public function saveForDelete(UserDao $userDao
    , UserAccessDao $userAccessDao
    , array $data) {

        // 更新クエリ発行
        $userDao->delete($data);
        $userAccessDao->delete($data);
    }

}
