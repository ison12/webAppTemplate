<?php

namespace App\Dao\SystemSetting;

use App\Cache\DBCacheManager;
use App\Common\DB\DBConnection;
use App\Common\DB\DBFactory;
use App\Common\DB\DBRawValue;
use App\Common\Util\DateUtil;
use App\Common\Util\ValUtil;
use App\Dao\BaseDao;

/**
 * システム設定DAO。
 */
class SystemSettingDao extends BaseDao {

    /**
     * コンストラクタ。
     * @param DBConnection $dbConnection DBコネクション
     */
    public function __construct(DBConnection $dbConnection) {
        parent::__construct($dbConnection);
    }

    /**
     * システムコードでレコードマップを取得する。
     * @return array レコードマップ
     */
    public function getFromCache(): array {

        $cacheKey = 'system_setting';
        $cache = DBCacheManager::getInstance();

        if ($cache->has($cacheKey)) {
            $values = $cache->get($cacheKey);
            return $values;
        }

        // SELECTクエリ
        $querySelect = DBFactory::createSelect()
                ->column(...[
                    'id',
                    'system_code',
                    'system_name',
                    'system_value',
                    new DBRawValue(["DATE_FORMAT(" . 'create_datetime' . ", '%Y/%m/%d %H:%i:%S.%f')", 'create_datetime']),
                    'create_user_id',
                    new DBRawValue(["DATE_FORMAT(" . 'update_datetime' . ", '%Y/%m/%d %H:%i:%S.%f')", 'update_datetime']),
                    'update_user_id',
                    'delete_flag',
                ])
                ->from('system_setting')
                ->orderByAsc('system_code')
        ;

        // SELECTクエリの生成
        $sql = '';
        $sqlParams = [];
        $querySelect->compile($sql, $sqlParams);
        // SELECT実行
        $ret = $this->dbConnection->queryFetch($sql, $sqlParams);

        $retMap = [];
        foreach ($ret as $value) {
            $retMap[$value['system_code']] = $value;
        }

        $cache->set($cacheKey, $retMap);

        return $retMap;
    }

    /**
     * システムコードでレコードを取得（キャッシュから）する。
     * @param string $systemCode システムコード
     * @return array レコード
     */
    public function getFromCacheBySystemCode(string $systemCode): array {
        $map = $this->getFromCache();
        return $map[$systemCode] ?? array();
    }

    /**
     * キャッシュをクリアする。
     */
    public function clearCache() {
        $cacheKey = 'system_setting';
        $cache = DBCacheManager::getInstance();
        $cache->delete($cacheKey);
    }

    /**
     * 主キーでレコードを取得する。
     * @param string $id ID
     * @param bool $isDelete 論理削除対象有無(true：対象）
     * @param bool $isLock ロック有無
     * @return array レコード
     */
    public function selectById(string $id, bool $isDelete = false, bool $isLock = false): array {

        // SELECTクエリ
        $querySelect = DBFactory::createSelect()
                ->column(...[
                    'id',
                    'system_code',
                    'system_name',
                    'system_value',
                    new DBRawValue(["DATE_FORMAT(" . 'create_datetime' . ", '%Y/%m/%d %H:%i:%S.%f')", 'create_datetime']),
                    'create_user_id',
                    new DBRawValue(["DATE_FORMAT(" . 'update_datetime' . ", '%Y/%m/%d %H:%i:%S.%f')", 'update_datetime']),
                    'update_user_id',
                    'delete_flag',
                ])
                ->from('system_setting')
                ->where()
                ->condition('id', '=', $id)
                ->_and()
                ->condition('delete_flag', '=', (int) $isDelete);

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
     * @return mixed ID
     */
    public function insert(array $data) {

        $insert = DBFactory::createInsert();
        $insert->from('system_setting')->column(...[
            'system_code'
            , 'system_name'
            , 'system_value'
            , 'create_datetime'
            , 'create_user_id'
            , 'update_datetime'
            , 'update_user_id'
            , 'delete_flag'
        ])->value(...[
            [
                $data['system_code'],
                $data['system_name'],
                $data['system_value'],
                $data['create_datetime'],
                $data['create_user_id'],
                $data['update_datetime'],
                $data['update_user_id'],
                $data['delete_flag']
            ]
        ]);

        $sql = '';
        $sqlParams = [];
        $insert->compile($sql, $sqlParams);

        $this->dbConnection->queryAction($sql, $sqlParams);
        $this->clearCache();

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
                ->from('system_setting')
                ->set('system_code', $data['system_code'])
                ->set('system_name', $data['system_name'])
                ->set('system_value', $data['system_value'])
                ->set('update_datetime', $data['update_datetime'])
                ->set('update_user_id', $data['update_user_id'])
                ->where()
                ->condition('id', '=', $data['id'])
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
     * @param array $data データ
     * @return int 件数
     */
    public function delete(array $data): int {

        $delete = DBFactory::createDelete();
        $delete
                ->from('system_setting')
                ->where()
                ->condition('id', '=', $data['id'])
        ;

        $sql = '';
        $sqlParams = [];
        $delete->compile($sql, $sqlParams);

        $ret = $this->dbConnection->queryAction($sql, $sqlParams);
        $this->clearCache();

        return $ret;
    }

    /**
     * 主キーでレコードを取得する。
     * @param string $id ID
     * @param bool $isLock ロック有無
     * @return array レコード
     */
    public function selectLinkedUserAccoutById(string $id, bool $isLock = false): array {

        $forUpdate = '';
        if ($isLock) {
            $forUpdate = 'FOR UPDATE OF ss';
        }

        $sql = <<<EOT
SELECT
    ss.id
  , ss.system_code
  , ss.system_name
  , ss.system_value
  , DATE_FORMAT(ss.create_datetime, '%Y/%m/%d %H:%i:%S.%f') create_datetime
  , ss.create_user_id
  , uc.user_account create_user_name
  , DATE_FORMAT(ss.update_datetime, '%Y/%m/%d %H:%i:%S.%f') update_datetime
  , ss.update_user_id
  , uu.user_account update_user_name
  , ss.delete_flag
FROM
    system_setting ss
        LEFT JOIN user uc ON ss.create_user_id = uc.id AND uc.delete_flag = 0
        LEFT JOIN user uu ON ss.update_user_id = uu.id AND uu.delete_flag = 0
WHERE
    ss.id = ?
AND ss.delete_flag = 0
{$forUpdate}
EOT;
        $sqlParams = [$id];

        return $this->dbConnection->queryFetch($sql, $sqlParams);
    }

    /**
     * 設定あり項目での検索
     * @param array $condition 検索条件
     * @param bool $getCount 件数取得有無
     * @return array レコードリスト。
     */
    public function selectSearchList(array $condition, bool $getCount = false): array {

        $helper = DBFactory::createDBHelper();

        /*
         * COLUMN
         */
        $column = '';
        if ($getCount) {
            $column = '      COUNT(1) count';
        } else {
            $column = <<<EOT
      ss.id
    , ss.system_code
    , ss.system_name
    , ss.system_value
    , ss.create_datetime
    , ss.create_user_id
    , uc.user_account create_user_name
    , ss.update_datetime
    , ss.update_user_id
    , uu.user_account update_user_name
    , ss.delete_flag
EOT;
        }

        /*
         * WHERE
         */
        $where = '';
        $params = [];

        $columnName = null;

        // idで絞り込み
        $columnName = 'id';
        if (!ValUtil::isEmptyElementOfArray($condition, $columnName)) {
            $where = ValUtil::prependConcat($where, ' AND ', "ss.$columnName = ?");
            $params[] = $condition[$columnName];
        }

        // システム設定コードで絞り込み
        $columnName = 'system_code';
        if (!ValUtil::isEmptyElementOfArray($condition, $columnName)) {
            $where = ValUtil::prependConcat($where, ' AND ', "ss.$columnName LIKE ? " . $helper->escapeLike());
            $params[] = '%' . $helper->escapeLikeValue($condition[$columnName]) . '%';
        }

        // システム設定名で絞り込み
        $columnName = 'system_name';
        if (!ValUtil::isEmptyElementOfArray($condition, $columnName)) {
            $where = ValUtil::prependConcat($where, ' AND ', "ss.$columnName LIKE ? " . $helper->escapeLike());
            $params[] = '%' . $helper->escapeLikeValue($condition[$columnName]) . '%';
        }

        // システム設定値で絞り込み
        $columnName = 'system_value';
        if (!ValUtil::isEmptyElementOfArray($condition, $columnName)) {
            $where = ValUtil::prependConcat($where, ' AND ', "ss.$columnName LIKE ? " . $helper->escapeLike());
            $params[] = '%' . $helper->escapeLikeValue($condition[$columnName]) . '%';
        }

        // 登録者で絞り込み
        $columnName = 'create_user_name';
        if (!ValUtil::isEmptyElementOfArray($condition, $columnName)) {
            $where = ValUtil::prependConcat($where, ' AND ', "uc." . 'user_account' . " LIKE ? " . $helper->escapeLike());
            $params[] = '%' . $helper->escapeLikeValue($condition[$columnName]) . '%';
        }

        // 更新者で絞り込み
        $columnName = 'update_user_name';
        if (!ValUtil::isEmptyElementOfArray($condition, $columnName)) {
            $where = ValUtil::prependConcat($where, ' AND ', "uu." . 'user_account' . " LIKE ? " . $helper->escapeLike());
            $params[] = '%' . $helper->escapeLikeValue($condition[$columnName]) . '%';
        }

        // 登録日時で絞り込み
        $columnName = 'create_datetime_from';
        if (!ValUtil::isEmptyElementOfArray($condition, $columnName)) {
            $from = DateUtil::GetStartOfTimeHms(DateUtil::createDateTime($condition[$columnName]));

            if ($from !== null) {
                $where = ValUtil::prependConcat($where, ' AND ', "ss." . 'create_datetime' . " >= ?");
                $params[] = $from->format(DateUtil::DATETIME_HYPHEN_MICRO_FORMAT_COMMON);
            }
        }
        $columnName = 'create_datetime_to';
        if (!ValUtil::isEmptyElementOfArray($condition, $columnName)) {
            $to = DateUtil::GetEndOfTimeHms(DateUtil::createDateTime($condition[$columnName]));

            if ($to !== null) {
                $where = ValUtil::prependConcat($where, ' AND ', "ss." . 'create_datetime' . " <= ?");
                $params[] = $to->format(DateUtil::DATETIME_HYPHEN_MICRO_FORMAT_COMMON);
            }
        }

        // 更新日時で絞り込み
        $columnName = 'update_datetime_from';
        if (!ValUtil::isEmptyElementOfArray($condition, $columnName)) {
            $from = DateUtil::GetStartOfTimeHms(DateUtil::createDateTime($condition[$columnName]));

            if ($from !== null) {
                $where = ValUtil::prependConcat($where, ' AND ', "ss." . 'update_datetime' . " >= ?");
                $params[] = $from->format(DateUtil::DATETIME_HYPHEN_MICRO_FORMAT_COMMON);
            }
        }
        $columnName = 'update_datetime_to';
        if (!ValUtil::isEmptyElementOfArray($condition, $columnName)) {
            $to = DateUtil::GetEndOfTimeHms(DateUtil::createDateTime($condition[$columnName]));

            if ($to !== null) {
                $where = ValUtil::prependConcat($where, ' AND ', "ss." . 'update_datetime' . " <= ?");
                $params[] = $to->format(DateUtil::DATETIME_HYPHEN_MICRO_FORMAT_COMMON);
            }
        }

        $where = ValUtil::prependConcat($where, ' AND ', "ss." . 'delete_flag' . " = 0");

        $where = ValUtil::prependHead($where, ' WHERE ');

        /*
         * ORDER BY
         */
        $orderBy = '';
        if (!$getCount) {
            $orderBy = "ss." . 'id';
            $orderBy = ValUtil::prependHead($orderBy, 'ORDER BY ');
        }

        /*
         * LIMIT / OFFSET
         */
        $limit = '';
        $offset = '';
        if (!$getCount) {
            if (
                    !ValUtil::isEmptyElementOfArray($condition, 'limit') &&
                    !ValUtil::isEmptyElementOfArray($condition, 'offset')
            ) {
                $limit = ValUtil::prependHead($condition['limit'], ' LIMIT ');
                $offset = ValUtil::prependHead($condition['offset'], ' OFFSET ');
            }
        }

        $statement = <<<EOT
SELECT
{$column}
FROM
    system_setting ss
        LEFT JOIN user uc ON ss.create_user_id = uc.id AND uc.delete_flag = 0
        LEFT JOIN user uu ON ss.update_user_id = uu.id AND uu.delete_flag = 0
{$where}
{$orderBy}
{$limit}{$offset}
EOT;

        return $this->dbConnection->queryFetch($statement, $params);
    }

}
