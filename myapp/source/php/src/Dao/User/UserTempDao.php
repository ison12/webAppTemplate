<?php

namespace App\Dao\User;

use App\Common\DB\DBConnection;
use App\Common\DB\DBFactory;
use App\Common\DB\DBRawValue;
use App\Common\Util\DateUtil;
use App\Dao\BaseDao;
use DateTime;

/**
 * ユーザー仮登録DAO。
 */
class UserTempDao extends BaseDao {

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
        $insert->from('user_temp')->column(...[
            'account_regist_uri',
            'auth_code',
            'user_account',
            'create_datetime',
            'create_user_id',
            'update_datetime',
            'update_user_id'
        ])->value(...[
            [
                $data['account_regist_uri'],
                $data['auth_code'],
                $data['user_account'],
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
     * レコードを削除する。
     * @param string $accountRegistUri アカウント登録URI
     * @return int 件数
     */
    public function deleteByAccountRegistUri(string $accountRegistUri): int {

        $delete = DBFactory::createDelete();
        $delete
                ->from('user_temp')
                ->where()
                ->condition('account_regist_uri', '=', $accountRegistUri)
        ;

        $sql = '';
        $sqlParams = [];
        $delete->compile($sql, $sqlParams);

        $ret = $this->dbConnection->queryAction($sql, $sqlParams);

        return $ret;
    }

    /**
     * レコードを削除する。
     * @param DateTime $expiredDate 期限切れ日時
     * @return int 件数
     */
    public function deleteByExpiredDate(DateTime $expiredDate): int {

        $delete = DBFactory::createDelete();
        $delete
                ->from('user_temp')
                ->where()
                ->condition('create_datetime', '<', $expiredDate->format(DateUtil::DATETIME_HYPHEN_MICRO_FORMAT_COMMON))
        ;

        $sql = '';
        $sqlParams = [];
        $delete->compile($sql, $sqlParams);

        $ret = $this->dbConnection->queryAction($sql, $sqlParams);

        return $ret;
    }

    /**
     * アカウント登録URIでレコードを取得する。
     * @param string $accountResetUri アカウント登録URI
     * @return array レコード
     */
    public function selectByAccountRegistUri(string $accountResetUri): array {

        // SELECTクエリ
        $querySelect = DBFactory::createSelect()
                ->column(
                        'account_regist_uri',
                        'auth_code',
                        'user_account',
                        new DBRawValue(["DATE_FORMAT(" . 'create_datetime' . ", '%Y/%m/%d %H:%i:%S.%f')", 'create_datetime'])
                )
                ->from('user_temp')
                ->where()
                ->condition('account_regist_uri', '=', $accountResetUri)
        ;

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
