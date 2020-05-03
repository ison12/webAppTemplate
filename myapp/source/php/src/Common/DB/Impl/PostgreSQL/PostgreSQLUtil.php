<?php

namespace App\Common\DB\Impl\PostgreSQL;

/**
 * PostgreSQLユーティリティ。
 */
class PostgreSQLUtil {

    /**
     * シリアル値を調整する。
     * @param string $tableName テーブル名
     * @param string $columnName カラム名
     * @return int シリアル値
     */
    public static function adjustSerialValue(string $tableName, string $columnName): int {

        // SQL
        $sql = <<<EOT
SELECT SETVAL('{$tableName}_{$columnName}_seq', (SELECT CASE WHEN max({$columnName}) >= 1
                                                                       THEN max({$columnName})
                                                                       ELSE 1 END
                                                   FROM {$tableName})) AS value
EOT;
        // パラメータ配列
        $sqlParams = [];

        $recs = $this->dbConnection->queryFetch($sql, $sqlParams);

        if (count($recs) >= 1) {
            return $recs[0]['value'];
        }

        return 1;
    }

}
