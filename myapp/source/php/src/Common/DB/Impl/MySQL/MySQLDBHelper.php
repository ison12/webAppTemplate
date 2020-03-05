<?php

namespace App\Common\DB\Impl\MySQL;

use App\Common\DB\DBHelper;
use App\Common\DB\DBRawValue;
use App\Common\Exception\DBException;

/**
 * DBヘルパー。MySQL実装。
 */
class MySQLDBHelper implements DBHelper {

    /**
     * オーバーライド
     */
    public function ecnloseDBObject($value): string {

        return '`' . str_replace('`', '``', $value) . '`';
    }

    /**
     * オーバーライド
     */
    public function getQueryExpression($op, $value, &$params): string {

        if ($value === null) {
            // 値が null の場合
            if ($op === '<>' || $op === '!=') {
                // 否定の場合
                return 'IS NOT NULL';
            } elseif ($op === '=') {
                // 肯定の場合
                return 'IS NULL';
            }
        } else {
            // 値が not null の場合
            if (strtolower($op) === 'like' || strtolower($op) === 'not like') {
                $params[] = $value;
                return "$op ? " . $this->escapeLike();
            } else if (is_array($value)) {
                // 配列の場合はIN関数が指定されたとみなす

                foreach ($value as $v) {
                    $params[] = $v;
                }

                $valueArr = [];
                for ($i = 0, $count = count($value); $i < $count; $i++) {
                    $valueArr[] = '?';
                }
                $valueStr = implode(', ', $valueArr);
                return "$op ($valueStr)";
            } else if ($value instanceof DBRawValue) {
                // 関数などを直接実行する
                return "$op {$value->value}";
            } else {
                $params[] = $value;

                // 通常パターン
                return "$op ?";
            }
        }
    }

    /**
     * オーバーライド
     */
    public function getQueryValue($value, &$params): string {

        if ($value instanceof DBRawValue) {
            // 関数などを直接実行する
            return $value->value;
        } else if ($value instanceof \DateTime) {
            $params[] = $value->format('Y-m-d H:i:s.u');
            return '?';
        } else {
            // 通常パターン
            $params[] = $value;
            return '?';
        }
    }

    /**
     * オーバーライド
     */
    public function escapeLike(string $escape = '!'): string {
        return "ESCAPE '{$escape}'";
    }

    /**
     * オーバーライド
     */
    public function escapeLikeValue(string $value, string $escape = '!'): string {
        return preg_replace('/(?=[' . $escape . '_%])/', $escape, $value);
    }

    /**
     * オーバーライド
     */
    public function isErrorForLock(DBException $ex): bool {
        return 1027 === $ex->getDriverState() || 3572 === $ex->getDriverState();
    }

    /**
     * オーバーライド
     */
    public function isErrorForTimeout(DBException $ex): bool {
        return 1205 === $ex->getDriverState();
    }

    /**
     * オーバーライド
     */
    public function isErrorForDuplicate(DBException $ex): bool {
        return 1062 === $ex->getDriverState();
    }

}
