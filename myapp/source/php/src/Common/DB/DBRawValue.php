<?php

namespace App\Common\DB;

/**
 * DBの生値。
 */
class DBRawValue {

    /**
     * @var string 値
     */
    public $value;

    /**
     * コンストラクタ。
     * SELECTのカラム句指定の場合は、$valueは、string or arrayが指定可能。
     * arrayの場合は、0番目が取得したい項目、1番目が別名。
     * @param mixed $value 値
     */
    public function __construct($value) {
        $this->value = $value;
    }

}
