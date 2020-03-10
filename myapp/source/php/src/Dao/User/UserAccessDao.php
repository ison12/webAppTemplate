<?php

namespace App\Dao\User;

use App\Common\DB\DBConnection;
use App\Common\DB\DBFactory;
use App\Common\DB\DBRawValue;
use App\Dao\BaseDao;

/**
 * ユーザーDAO。
 */
class UserAccessDao extends BaseDao {

    /**
     * コンストラクタ。
     * @param DBConnection $dbConnection DBコネクション
     */
    public function __construct(DBConnection $dbConnection) {
        parent::__construct($dbConnection);
    }

    /**
     * レコードを登録する。
     * @param array $data データ
     */
    public function insert(array $data) {

        $insert = DBFactory::createInsert();
        $insert->from('user_access')->column(...[
            'user_id'
            , 'access_datetime'
            , 'auth_failed_count'
            , 'auth_failed_datetime'
            , 'create_datetime'
            , 'create_user_id'
            , 'update_datetime'
            , 'update_user_id'
        ])->value(...[
            [
                $data['user_id'],
                null,
                0,
                null,
                $data['create_datetime'],
                $data['create_user_id'],
                $data['update_datetime'],
                $data['update_user_id']
            ]
        ]);

        $sql = '';
        $sqlParams = [];
        $insert->compile($sql, $sqlParams);

        $this->dbConnection->queryAction($sql, $sqlParams);
    }

    /**
     * ユーザーアクセス情報を認証成功状態で更新する。
     * @param string $userId ユーザーID
     * @param string $updateDatetime 更新日時
     * @return int 件数
     */
    public function updateAccessForSuccessByUserId(string $userId, string $updateDatetime): int {

        $update = DBFactory::createUpdate();
        $update
                ->from('user_access')
                ->set('access_datetime', $updateDatetime)
                ->set('auth_failed_count', 0)
                ->set('update_datetime', $updateDatetime)
                ->set('update_user_id', 0)
                ->where()
                ->condition('user_id', '=', $userId)
        ;

        $sql = '';
        $sqlParams = [];
        $update->compile($sql, $sqlParams);

        return $this->dbConnection->queryAction($sql, $sqlParams);
    }

    /**
     * ユーザーアクセス情報を認証失敗状態で更新する。
     * @param string $userId ユーザーID
     * @param string $updateDatetime 更新日時
     * @param int $authFailedCount 認証失敗件数
     * @return int 件数
     */
    public function updateAccessForFailedByUserId(string $userId, string $updateDatetime, int $authFailedCount): int {

        $update = DBFactory::createUpdate();
        $update
                ->from('user_access')
                ->set('auth_failed_datetime', $updateDatetime)
                ->set('auth_failed_count', $authFailedCount)
                ->set('update_datetime', $updateDatetime)
                ->set('update_user_id', 0)
                ->where()
                ->condition('user_id', '=', $userId)
        ;

        $sql = '';
        $sqlParams = [];
        $update->compile($sql, $sqlParams);

        return $this->dbConnection->queryAction($sql, $sqlParams);
    }

    /**
     * 認証失敗情報を更新する。
     * @param array $data データ
     * @return int 件数
     */
    public function updateAccessForClearAuthFailed(array $data): int {

        $update = DBFactory::createUpdate();
        $update
                ->from('user_access')
                ->set('auth_failed_count', 0)
                ->set('auth_failed_datetime', null)
                ->set('update_datetime', $data['update_datetime'])
                ->set('update_user_id', $data['update_user_id'])
                ->where()
                ->condition('user_id', '=', $data['user_id'])
        ;

        $sql = '';
        $sqlParams = [];
        $update->compile($sql, $sqlParams);

        $ret = $this->dbConnection->queryAction($sql, $sqlParams);

        return $ret;
    }

    /**
     * レコードを削除する。
     * @param array $data データ
     * @return int 件数
     */
    public function delete(array $data): int {

        $delete = DBFactory::createDelete();
        $delete
                ->from('user_access')
                ->where()
                ->condition('user_id', '=', $data['user_id'])
        ;

        $sql = '';
        $sqlParams = [];
        $delete->compile($sql, $sqlParams);

        $ret = $this->dbConnection->queryAction($sql, $sqlParams);

        return $ret;
    }

    /**
     * ユーザーIDでユーザーアクセスレコードを取得する。
     * @param string $userId ユーザーID
     * @param bool $isLock ロック有無
     * @return array レコード
     */
    public function selectAccessByUserId(string $userId, bool $isLock = false): array {

        // SELECTクエリ
        $querySelect = DBFactory::createSelect()
                ->column(
                        'user_id'
                        , new DBRawValue(["DATE_FORMAT(" . 'access_datetime' . ", '%Y/%m/%d %H:%i:%S.%f')", 'access_datetime'])
                        , 'auth_failed_count'
                        , new DBRawValue(["DATE_FORMAT(" . 'auth_failed_datetime' . ", '%Y/%m/%d %H:%i:%S.%f')", 'auth_failed_datetime'])
                )
        ->from('user_access')
                ->where()
                ->condition('user_id', '=', $userId)
        ;

        if ($isLock) {
            $querySelect->lock();
        }

        // SELECTクエリの生成
        $sql = '';
        $sqlParams = [];
        $querySelect->compile($sql, $sqlParams);

        $ret = $this->dbConnection->queryFetch($sql, $sqlParams);
        if (count($ret) > 0) {
            return $ret[0];
        }

        return array();
    }

}
