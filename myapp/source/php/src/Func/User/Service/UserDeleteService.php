<?php

namespace App\Func\User\Service;

use App\Common\Data\User;
use App\Common\Exception\DBException;
use App\Dao\User\UserDao;
use App\Func\Base\Service\DBBaseService;
use App\Func\Common\Service\MailService;
use Psr\Http\Message\UriInterface;

/**
 * ユーザー削除サービス。
 */
class UserDeleteService extends DBBaseService {

    /**
     * コンストラクタ。
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * ユーザー削除処理。
     * @param array $data データ
     * @param User $user ユーザー情報
     * @param UriInterface $uri URI
     */
    public function delete(array $data, $user, UriInterface $uri) {

        // DBへの更新
        try {
            $this->transaction($this->dbConnection, function($dbConnection) use($data, $user) {

                // Daoの初期化
                $userDao = new UserDao($dbConnection);
                $userDao->delete($user->user_id);
            });

            // メールを送信する
            $this->sendMailForUserDelete($uri, $user);
        } catch (DBException $ex) {

            throw $ex;
        }

    }

    /**
     * ユーザー削除のメール送信処理。
     * @param UriInterface $uri URI
     * @param User $user ユーザー情報
     */
    private function sendMailForUserDelete($uri, $user) {

        // ログ出力する
        $this->logger->info("UserRegist user_account={$user->user_account}");

        $mailService = new MailService($this->dbConnection);
        $mailService->send(
                'User/UserDelete',
                [
                    'userAccount' => $user->user_account
                ],
                [
                    ['address' => $user->user_account, 'name' => $user->user_account]
                ]
        );
    }

}
