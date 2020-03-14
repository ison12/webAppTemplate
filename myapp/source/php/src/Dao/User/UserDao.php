<?php

namespace App\Dao\User;

use App\Common\DB\DBConnection;
use App\Common\DB\DBFactory;
use App\Common\DB\DBRawValue;
use App\Common\Util\EncryptUtil;
use App\Dao\BaseDao;

/**
 * ユーザーDAO。
 */
class UserDao extends BaseDao {

    /**
     * コンストラクタ。
     * @param DBConnection $dbConnection DBコネクション
     */
    public function __construct(DBConnection $dbConnection) {
        parent::__construct($dbConnection);
    }

    /**
     * 主キーでレコードを取得する。
     * @param string $userId ユーザーID
     * @param bool $isLock ロック有無
     * @return array レコード
     */
    public function selectById(string $userId, bool $isLock = false) {

        // SELECTクエリ
        $querySelect = DBFactory::createSelect()
                ->column(...[
                    'user_id',
                    'user_account',
                    'password',
                    'email',
                    'user_name',
                    'user_name_kana',
                    'authority',
                    'create_datetime',
                    'create_user_id',
                    'update_datetime',
                    'update_user_id'
                ])
                ->from('user')
                ->where()
                ->condition('user_id', '=', $userId);

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

        return null;
    }

    /**
     * レコードを登録する。
     * @param array $data データ
     */
    public function insert(array $data) {

        $insert = DBFactory::createInsert();
        $insert->from('user')->column(...[
            'user_account',
            'password',
            'email',
            'user_name',
            'user_name_kana',
            'authority',
            'create_datetime',
            'create_user_id',
            'update_datetime',
            'update_user_id'
        ])->value(...[
            [
                $data['user_account']
                , EncryptUtil::hash($data['password'])
                , $data['email']
                , $data['user_name']
                , $data['user_name_kana']
                , $data['authority']
                , $data['create_datetime']
                , $data['create_user_id']
                , $data['update_datetime']
                , $data['update_user_id']
            ]
        ]);

        $sql = '';
        $sqlParams = [];
        $insert->compile($sql, $sqlParams);

        $this->dbConnection->queryAction($sql, $sqlParams);

        return (int) $this->dbConnection->lastInsertId();
    }

    /**
     * レコードを更新する。
     * @param array $data データ
     * @return int 件数
     */
    public function update(array $data): int {

        $update = DBFactory::createUpdate();
        $update
                ->from('user')
                ->set('password', EncryptUtil::hash($data['password']))
                ->set('email', $data['email'])
                ->set('user_name', $data['user_name'])
                ->set('user_name_kana', $data['user_name_kana'])
                ->set('authority', $data['authority'])
                ->set('update_datetime', $data['update_datetime'])
                ->set('update_user_id', $data['update_user_id'])
                ->where()
                ->condition('user_id', '=', $data['user_id'])
        ;

        $sql = '';
        $sqlParams = [];
        $update->compile($sql, $sqlParams);

        $ret = $this->dbConnection->queryAction($sql, $sqlParams);
        $this->clearCache();

        return $ret;
    }

    /**
     * レコードを削除する。
     * @param string $userId ユーザーID
     * @return int 件数
     */
    public function delete($userId): int {

        $delete = DBFactory::createDelete();
        $delete
                ->from('user')
                ->where()
                ->condition('user_id', '=', $userId)
        ;

        $sql = '';
        $sqlParams = [];
        $delete->compile($sql, $sqlParams);

        $ret = $this->dbConnection->queryAction($sql, $sqlParams);

        return $ret;
    }

    /**
     * ユーザーアカウントでレコードを取得する。
     * @param string $userAccount ユーザーアカウント
     * @param bool $isLock ロック有無
     * @return array レコード
     */
    public function selectByUserAccount(string $userAccount, bool $isLock = false) {

        // SELECTクエリ
        $querySelect = DBFactory::createSelect()
                ->column(...[
                    'user_id',
                    'user_account',
                    'password',
                    'email',
                    'user_name',
                    'user_name_kana',
                    'authority',
                    new DBRawValue(["DATE_FORMAT(" . 'create_datetime' . ", '%Y/%m/%d %H:%i:%S.%f')", 'create_datetime']),
                    'create_user_id',
                    new DBRawValue(["DATE_FORMAT(" . 'update_datetime' . ", '%Y/%m/%d %H:%i:%S.%f')", 'update_datetime']),
                    'update_user_id'
                ])
                ->from('user')
                ->where()
                ->condition('user_account', '=', $userAccount);

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

        return null;
    }

    /**
     * パスワードを更新する。
     * @param array $data データ
     * @return int 件数
     */
    public function updatePassword(array $data): int {

        $update = DBFactory::createUpdate();
        $update
                ->from('user')
                ->set('password', EncryptUtil::hash($data['password']))
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

}
