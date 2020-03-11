<?php

namespace App\Dao\Diary;

use App\Common\DB\DBConnection;
use App\Common\DB\DBFactory;
use App\Common\DB\DBRawValue;
use App\Common\Util\DateUtil;
use App\Common\Util\ValUtil;
use App\Dao\BaseDao;

/**
 * 日記DAO。
 */
class DiaryDao extends BaseDao {

    /**
     * コンストラクタ。
     * @param DBConnection $dbConnection DBコネクション
     */
    public function __construct(DBConnection $dbConnection) {
        parent::__construct($dbConnection);
    }

    /**
     * 主キーでレコードを取得する。
     * @param string $diaryId 日記 ID
     * @param bool $isLock ロック有無
     * @return array レコード
     */
    public function selectByDiaryId(string $userId, string $diaryId, bool $isLock = false): ?array {

        // SELECTクエリ
        $querySelect = DBFactory::createSelect()
                ->column(...[
                    'diary_id',
                    'user_id',
                    'title',
                    'content',
                    new DBRawValue(["DATE_FORMAT(" . 'diary_datetime' . ", '%Y/%m/%d')", 'diary_datetime']),
                    new DBRawValue(["DATE_FORMAT(" . 'create_datetime' . ", '%Y/%m/%d %H:%i:%S.%f')", 'create_datetime']),
                    'create_user_id',
                    new DBRawValue(["DATE_FORMAT(" . 'update_datetime' . ", '%Y/%m/%d %H:%i:%S.%f')", 'update_datetime']),
                    'update_user_id'
                ])
                ->from('diary')
                ->where()
                ->condition('user_id', '=', $userId)
                ->_and()
                ->condition('diary_id', '=', $diaryId);

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
        $insert->from('diary')->column(...[
              'user_id'
            , 'title'
            , 'content'
            , 'diary_datetime'
            , 'create_datetime'
            , 'create_user_id'
            , 'update_datetime'
            , 'update_user_id'
        ])->value(...[
            [
                $data['user_id'],
                $data['title'],
                $data['content'],
                $data['diary_datetime'],
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

        return (int) $this->dbConnection->lastInsertId();
    }

    /**
     * レコードを更新する。
     * @param array $data データ
     * @param string $userId ユーザーID
     * @param string $diaryId 日記 ID
     * @return int 件数
     */
    public function update(array $data, string $userId, string $diaryId): int {

        $update = DBFactory::createUpdate();
        $update
                ->from('diary')
                ->set('user_id', $data['user_id'])
                ->set('title', $data['title'])
                ->set('content', $data['content'])
                ->set('diary_datetime', $data['diary_datetime'])
                ->set('update_datetime', $data['update_datetime'])
                ->set('update_user_id', $data['update_user_id'])
                ->where()
                ->condition('user_id', '=', $userId)
                ->_and()
                ->condition('diary_id', '=', $diaryId)
        ;

        $sql = '';
        $sqlParams = [];
        $update->compile($sql, $sqlParams);

        $ret = $this->dbConnection->queryAction($sql, $sqlParams);

        return $ret;
    }

    /**
     * レコードを削除する。
     * @param string $userId ユーザーID
     * @param string $diaryId 日記 ID
     * @return int 件数
     */
    public function delete(string $userId, string $diaryId): int {

        $delete = DBFactory::createDelete();
        $delete
                ->from('diary')
                ->where()
                ->condition('user_id', '=', $userId)
                ->_and()
                ->condition('diary_id', '=', $diaryId)
        ;

        $sql = '';
        $sqlParams = [];
        $delete->compile($sql, $sqlParams);

        $ret = $this->dbConnection->queryAction($sql, $sqlParams);

        return $ret;
    }

    /**
     * 主キーでレコードを取得する。
     * @param string $userId ユーザーID
     * @param string $diaryId 日記 ID
     * @param bool $isLock ロック有無
     * @return array レコード
     */
    public function selectLinkedUserAccoutByDiaryId(string $userId, string $diaryId, bool $isLock = false): array {

        $forUpdate = '';
        if ($isLock) {
            $forUpdate = 'FOR UPDATE OF dr';
        }

        $sql = <<<EOT
SELECT
    dr.diary_id
  , dr.user_id
  , dr.title
  , dr.content
  , DATE_FORMAT(dr.diary_datetime, '%Y/%m/%d') diary_datetime
  , DATE_FORMAT(dr.create_datetime, '%Y/%m/%d %H:%i:%S.%f') create_datetime
  , dr.create_user_id
  , uc.user_account create_user_name
  , DATE_FORMAT(dr.update_datetime, '%Y/%m/%d %H:%i:%S.%f') update_datetime
  , dr.update_user_id
  , uu.user_account update_user_name
FROM
    diary dr
        LEFT JOIN user uc ON dr.create_user_id = uc.user_id
        LEFT JOIN user uu ON dr.update_user_id = uu.user_id
WHERE
    dr.diary_id = ?
AND dr.user_id = ?
{$forUpdate}
EOT;
        $sqlParams = [$userId, $diaryId];

        return $this->dbConnection->queryFetch($sql, $sqlParams);
    }

    /**
     * 設定あり項目での検索
     * @param string $userId ユーザーID
     * @param array $condition 検索条件
     * @param bool $getCount 件数取得有無
     * @return array レコードリスト。
     */
    public function selectSearchList(string $userId, array $condition, bool $getCount = false): array {

        $helper = DBFactory::createDBHelper();

        /*
         * COLUMN
         */
        $column = '';
        if ($getCount) {
            $column = '      COUNT(1) count';
        } else {
            $column = <<<EOT
    dr.diary_id
  , dr.user_id
  , dr.title
  , dr.content
  , DATE_FORMAT(dr.diary_datetime, '%Y/%m/%d') diary_datetime
  , DATE_FORMAT(dr.create_datetime, '%Y/%m/%d %H:%i:%S.%f') create_datetime
  , dr.create_user_id
  , uc.user_account create_user_name
  , DATE_FORMAT(dr.update_datetime, '%Y/%m/%d %H:%i:%S.%f') update_datetime
  , dr.update_user_id
  , uu.user_account update_user_name
EOT;
        }

        /*
         * WHERE
         */
        $where = '    dr.user_id = ?';
        $params = [$userId];

        $columnName = null;

        // タイトルで絞り込み
        $columnName = 'title';
        if (!ValUtil::isEmptyElementOfArray($condition, $columnName)) {
            $where = ValUtil::prependConcat($where, ' AND ', "dr.$columnName LIKE ? " . $helper->escapeLike());
            $params[] = '%' . $helper->escapeLikeValue($condition[$columnName]) . '%';
        }

        // 内容で絞り込み
        $columnName = 'content';
        if (!ValUtil::isEmptyElementOfArray($condition, $columnName)) {
            $where = ValUtil::prependConcat($where, ' AND ', "dr.$columnName LIKE ? " . $helper->escapeLike());
            $params[] = '%' . $helper->escapeLikeValue($condition[$columnName]) . '%';
        }

        // 日時で絞り込み
        $columnName1 = 'diaryYear';
        $columnName2 = 'diaryMonth';
        if (
                !ValUtil::isEmptyElementOfArray($condition, $columnName1) &&
                !ValUtil::isEmptyElementOfArray($condition, $columnName2)) {
            $from = DateUtil::GetStartOfTimeHms(DateUtil::createDateTime($condition[$columnName1] . '/' . $condition[$columnName2] . '/01'));
            $to = clone $from;
            $to->add(new \DateInterval('P1M'));

            if ($from !== null) {
                $where = ValUtil::prependConcat($where, ' AND ', "dr." . 'diary_datetime' . " >= ?");
                $params[] = $from->format(DateUtil::DATETIME_HYPHEN_MICRO_FORMAT_COMMON);

                $where = ValUtil::prependConcat($where, ' AND ', "dr." . 'diary_datetime' . " < ?");
                $params[] = $to->format(DateUtil::DATETIME_HYPHEN_MICRO_FORMAT_COMMON);
            }
        }

        $where = ValUtil::prependHead($where, ' WHERE ');

        /*
         * ORDER BY
         */
        $orderBy = '';
        if (!$getCount) {
            $orderBy .= "dr." . 'diary_datetime' . ' DESC, ';
            $orderBy .= "dr." . 'diary_id DESC';
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
    diary dr
        LEFT JOIN user uc ON dr.create_user_id = uc.user_id
        LEFT JOIN user uu ON dr.update_user_id = uu.user_id
{$where}
{$orderBy}
{$limit}{$offset}
EOT;

        return $this->dbConnection->queryFetch($statement, $params);
    }

    /**
     * 日付の年月ごとのデータを取得する。
     * @param string $userId ユーザーID
     * @return array レコード
     */
    public function selectGroupByDiaryYearMonth(string $userId): array {

        $sql = <<<EOT
SELECT
    DATE_FORMAT(dr.diary_datetime, '%Y/%m') diary_year_month
FROM
    diary dr
WHERE
    dr.user_id = ?
GROUP BY
    DATE_FORMAT(dr.diary_datetime, '%Y/%m')
ORDER BY
    DATE_FORMAT(dr.diary_datetime, '%Y/%m')
EOT;
        $sqlParams = [$userId];

        return $this->dbConnection->queryFetch($sql, $sqlParams);
    }

}
