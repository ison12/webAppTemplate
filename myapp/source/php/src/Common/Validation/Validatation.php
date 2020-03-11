<?php

namespace App\Common\Validation;

use App\Common\Message\MessageManager;
use App\Common\Util\DateUtil;
use App\Common\Util\ValUtil;

/**
 * バリデーションクラス。
 *
 * 
 */
class Validatation {

    /**
     * @var Validatation
     */
    private static $instances = array();

    /**
     * インスタンスを取得する。
     * @param string $messageFilePath メッセージファイルパス
     * @return Validatation バリデーション
     */
    public static function getInstance($messageFilePath = null) {

        if ($messageFilePath === null) {
            $messageFilePath = SRC_PATH . 'Message/ValidateMessage.php';
        }

        // Make default if first instance
        if (!isset(self::$instances[$messageFilePath])) {
            $message = MessageManager::getInstance($messageFilePath);
            self::$instances[$messageFilePath] = new Validatation($message);
            return self::$instances[$messageFilePath];
        }

        return self::$instances[$messageFilePath];
    }

    /**
     * エラーを生成する。
     * @param string $id ID
     * @param string $message メッセージ
     */
    public static function createError(string $id, string $message): array {
        return ['id' => $id, 'message' => $message];
    }

    /**
     * @var MessageManager メッセージリスト
     */
    private $messages = null;

    /**
     * コンストラクタ。
     * @param MessageManager $messages メッセージリスト
     */
    public function __construct(MessageManager $messages = null) {
        $this->messages = $messages;
    }

    /**
     * 必須であるかをチェックする。
     * @param mixed $value 値
     * @return bool true 正常、false 異常
     */
    public function checkRequired($value): bool {

        return !ValUtil::isEmpty($value);
    }

    /**
     * アルファベットであるかをチェックする。
     * @param mixed $value 値
     * @return bool true 正常、false 異常
     */
    public function checkAlphabet($value): bool {

        return preg_match("/^[a-zA-Z]+$/", $value);
    }

    /**
     * カナ文字または半角文字であるかをチェックする。
     * @param mixed $value 値
     * @return bool true 正常、false 異常
     */
    public function checkKanaOrHalfChar($value): bool {

        return preg_match("/^[\x20-\x7Eァ-ヴー・　]+$/u", $value);
    }

    /**
     * 小文字アルファベットであるかをチェックする。
     * @param mixed $value 値
     * @return bool true 正常、false 異常
     */
    public function checkAlphabetLower($value): bool {

        return preg_match("/^[a-z]+$/", $value);
    }

    /**
     * 大文字アルファベットであるかをチェックする。
     * @param mixed $value 値
     * @return bool true 正常、false 異常
     */
    public function checkAlphabetUpper($value): bool {

        return preg_match("/^[A-Z]+$/", $value);
    }

    /**
     * 数値であるかをチェックする。
     * @param mixed $value 値
     * @param bool $signed trueの場合は符号あり、falseの場合は符号なし
     * @return bool true 正常、false 異常
     */
    public function checkInteger($value, bool $signed = false): bool {

        $regUnsigned = '';

        // 符号有無の正規表現
        if ($signed) {
            $regUnsigned = '[+-]?';
        }

        return preg_match("/^{$regUnsigned}\d+$/", $value);
    }

    /**
     * 数値であるかをチェックする。
     * @param mixed $value 値
     * @param int $integerPartLength 整数部分の桁数
     * @param int $fractionalPart 小数部分の桁数
     * @param bool $signed trueの場合は符号あり、falseの場合は符号なし
     * @return bool true 正常、false 異常
     */
    public function checkNumeric($value, int $integerPartLength, int $fractionalPart, bool $signed = false): bool {

        $regIntPartLen0 = '*'; // 0回以上
        $regIntPartLen1 = '+'; // 1回以上
        $regFraPartLen0 = '*'; // 0回以上
        $regFraPartLen1 = '+'; // 1回以上
        $regSigned = '';

        if ($integerPartLength > 0) {
            $regIntPartLen0 = "{0,$integerPartLength}"; // 0回以上N回の繰り返し
            $regIntPartLen1 = "{1,$integerPartLength}"; // 1回以上N回の繰り返し
        }

        if ($fractionalPart > 0) {
            $regFraPartLen1 = "{1,$fractionalPart}"; // 0回以上N回の繰り返し
            $regFraPartLen0 = "{0,$fractionalPart}"; // 1回以上N回の繰り返し
        }

        // 符号有無の正規表現
        if ($signed) {
            $regSigned = '[+-]?';
        }

        /*
         * 数値チェック
         */
        $isNumeric = preg_match('/'
                . "^{$regSigned}\d{$regIntPartLen1}$" . '|'
                . "^{$regSigned}\d{$regIntPartLen1}\.\d{$regFraPartLen0}$" . '|'
                . "^{$regSigned}\d{$regIntPartLen0}\.\d{$regFraPartLen1}$" . '/', $value);

        return $isNumeric;
    }

    /**
     * 半角文字であるかをチェックする。
     * @param mixed $value 値
     * @return bool true 正常、false 異常
     */
    public function checkHalfChar($value): bool {

        return preg_match("/^[\x20-\x7E]+$/", $value);
    }

    /**
     * 日時であるかをチェックする。
     * @param mixed $value 値
     * @param array $format フォーマット
     * @return bool true 正常、false 異常
     */
    public function checkDateTime($value, $format = null): bool {

        if (ValUtil::isEmpty($value)) {
            // 元が空の場合はOKとする
            return true;
        }

        $dateTimeObj = DateUtil::createDateTime((string) $value, $format);
        return $dateTimeObj !== null;
    }

    /**
     * Urlであるかをチェックする。
     * @param mixed $value 値
     * @return bool true 正常、false 異常
     */
    public function checkUrl($value): bool {

        return preg_match("/^http(s)?:\/\/([\w-]+\.)+[\w-]+(\/[\w- .\/?%&=]*)?$/", $value);
    }

    /**
     * Emailであるかをチェックする。
     * @param mixed $value 値
     * @return bool true 正常、false 異常
     */
    public function checkEmail($value): bool {

        // input[type=email]タグと同等のチェック
        // 参考：https://www.w3.org/TR/html5/forms.html#valid-e-mail-address
        return preg_match("/^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/", $value);
    }

    /**
     * パスワード文字であるかをチェックする。
     *
     * ・既定の長さ以上であること（$length以上）
     * ・英小文字が含まれていること
     * ・英大文字が含まれていること
     * ・数字が含まれていること
     * ・記号が含まれていること
     *
     * @param mixed $value 値
     * @param int $length 桁数
     * @return bool true 正常、false 異常
     */
    public function checkPassword($value, $length): bool {

        if (!(strlen($value) >= $length)) {
            // 既定の長さ以下の場合
            return false;
        }

        if (!(
                preg_match("/[a-z]/", $value) &&
                preg_match("/[A-Z]/", $value) &&
                preg_match("/[0-9]/", $value) &&
                preg_match("|[!-/:-@¥[-`{-~]|", $value)
                )
        ) {
            // 文字種不正
            return false;
        }

        return true;
    }

    /**
     * 任意の正規表現であるかをチェックする。
     * @param mixed $value 値
     * @param string $regExp 正規表現パターン
     * @return bool true 正常、false 異常
     */
    public function checkRegExp($value, string $regExp): bool {

        return preg_match($regExp, $value);
    }

    /**
     * 文字列の桁数をチェックする。
     * @param mixed $value 値
     * @param int $length 桁数
     * @return bool true 正常、false 異常
     */
    public function checkLength($value, int $length): bool {

        if (mb_strlen($value) <= $length) {
            return true;
        }

        return false;
    }

    /**
     * 文字列の範囲をチェックする。
     * @param mixed $value 値
     * @param int $min 最小桁数
     * @param int $max 最大桁数
     * @return bool true 正常、false 異常
     */
    public function checkRangeForLength($value, int $min, int $max): bool {

        if (mb_strlen((string) $value) >= $min && mb_strlen((string) $value) <= $max) {
            return true;
        }

        return false;
    }

    /**
     * 範囲をチェックする。
     * @param mixed $value 値
     * @param mixed $min 桁数
     * @param mixed $max 桁数
     * @return bool true 正常、false 異常
     */
    public function checkRange($value, $min, $max): bool {

        if ($value >= $min && $value <= $max) {
            return true;
        }

        return false;
    }

    /**
     * @var bool 検証メソッドが複数実行された場合に、何れかのメソッドでエラーが発生したら後続の検証は実施しないようにするためのマーキングフラグ
     */
    private $oneOfTheFollowing = false;

    /**
     * @var bool 検証メソッドが複数実行された場合に、何れかのメソッドでエラーが発生したことを示すフラグ
     */
    private $invalidate = false;

    /**
     * oneOfTheFollowingメソッドからendメソッド実行の間において
     * 検証メソッドが複数実行された場合に、何れかのメソッドでエラーが発生したら後続の検証は実施しないようにするために
     * oneOfTheFollowingメソッドからendメソッドを定義。
     * @return Validatation 自身のオブジェクト
     */
    public function oneOfTheFollowing(): Validatation {
        $this->oneOfTheFollowing = true;
        $this->invalidate = false;
        return $this;
    }

    /**
     * oneOfTheFollowingメソッドと対になるendメソッド。
     * @return Validatation 自身のオブジェクト
     */
    public function end(): Validatation {
        $this->oneOfTheFollowing = false;
        $this->invalidate = false;
        return $this;
    }

    /**
     * 必須チェック。
     * @param array $errors （戻り値）エラーリスト
     * @param string $itemId 項目ID
     * @param string $itemName 項目名称
     * @param mixed $value 値
     * @return Validatation 自身のオブジェクト
     */
    public function validateRequired(array &$errors, string $itemId, string $itemName, $value): Validatation {

        if ($this->oneOfTheFollowing && $this->invalidate) {
            // チェックせずに処理を終了
            return $this;
        }

        if (!$this->checkRequired($value)) {
            $errors[] = self::createError($itemId, $this->messages->get('validation_required', array('%itemName%' => $itemName)));
            $this->invalidate = true;
        }

        return $this;
    }

    /**
     * アルファベットチェック。
     * @param array $errors （戻り値）エラーリスト
     * @param string $itemId 項目ID
     * @param string $itemName 項目名称
     * @param mixed $value 値
     * @return Validatation 自身のオブジェクト
     */
    public function validateAlphabet(array &$errors, string $itemId, string $itemName, $value): Validatation {

        if ($this->oneOfTheFollowing && $this->invalidate) {
            // チェックせずに処理を終了
            return $this;
        }

        if (!$this->checkAlphabet($value)) {
            $errors[] = self::createError($itemId, $this->messages->get('validation_alphabet', array('%itemName%' => $itemName)));
            $this->invalidate = true;
        }

        return $this;
    }

    /**
     * 小文字アルファベットチェック。
     * @param array $errors （戻り値）エラーリスト
     * @param string $itemId 項目ID
     * @param string $itemName 項目名称
     * @param mixed $value 値
     * @return Validatation 自身のオブジェクト
     */
    public function validateAlphabetLower(array &$errors, string $itemId, string $itemName, $value): Validatation {

        if ($this->oneOfTheFollowing && $this->invalidate) {
            // チェックせずに処理を終了
            return $this;
        }

        if (!$this->checkAlphabetLower($value)) {
            $errors[] = self::createError($itemId, $this->messages->get('validation_alphabet_lower', array('%itemName%' => $itemName)));
            $this->invalidate = true;
        }

        return $this;
    }

    /**
     * 大文字アルファベットチェック。
     * @param array $errors （戻り値）エラーリスト
     * @param string $itemId 項目ID
     * @param string $itemName 項目名称
     * @param mixed $value 値
     * @return Validatation 自身のオブジェクト
     */
    public function validateAlphabetUpper(array &$errors, string $itemId, string $itemName, $value): Validatation {

        if ($this->oneOfTheFollowing && $this->invalidate) {
            // チェックせずに処理を終了
            return $this;
        }

        if (!$this->checkAlphabetUpper($value)) {
            $errors[] = self::createError($itemId, $this->messages->get('validation_alphabet_upper', array('%itemName%' => $itemName)));
            $this->invalidate = true;
        }

        return $this;
    }

    /**
     * 整数チェック。
     * @param array $errors （戻り値）エラーリスト
     * @param string $itemId 項目ID
     * @param string $itemName 項目名称
     * @param mixed $value 値
     * @param bool $signed trueの場合は符号あり、falseの場合は符号なし
     * @return Validatation 自身のオブジェクト
     */
    public function validateInteger(array &$errors, string $itemId, string $itemName, $value, bool $signed = false): Validatation {

        if ($this->oneOfTheFollowing && $this->invalidate) {
            // チェックせずに処理を終了
            return $this;
        }

        if (!$this->checkInteger($value, $signed)) {
            $key = '';
            if ($signed) {
                $key = 'validation_signed_integer';
            } else {
                $key = 'validation_integer';
            }

            $errors[] = self::createError($itemId, $this->messages->get($key, array('%itemName%' => $itemName, '%signed%' => $signed)));
            $this->invalidate = true;
        }

        return $this;
    }

    /**
     * 数値チェック。
     * @param array $errors （戻り値）エラーリスト
     * @param string $itemId 項目ID
     * @param string $itemName 項目名称
     * @param mixed $value 値
     * @param int $integerPartLength 整数部分の桁数
     * @param int $fractionalPart 小数部分の桁数
     * @param bool $signed trueの場合は符号あり、falseの場合は符号なし
     * @return Validatation 自身のオブジェクト
     */
    public function validateNumeric(array &$errors, string $itemId, string $itemName, $value, int $integerPartLength, int $fractionalPart, bool $signed = false): Validatation {

        if ($this->oneOfTheFollowing && $this->invalidate) {
            // チェックせずに処理を終了
            return $this;
        }

        if (!$this->checkNumeric($value, $integerPartLength, $fractionalPart, $signed)) {

            $key = '';
            if ($signed) {
                if ($integerPartLength > 0 && $fractionalPart > 0) {
                    $key = 'validation_signed_numeric_length';
                } else {
                    $key = 'validation_signed_numeric';
                }
            } else {
                if ($integerPartLength > 0 && $fractionalPart > 0) {
                    $key = 'validation_numeric_length';
                } else {
                    $key = 'validation_numeric';
                }
            }

            $errors[] = self::createError($itemId, $this->messages->get($key, array('%itemName%' => $itemName, '%integerPartLength%' => $integerPartLength, '%fractionalPart%' => $fractionalPart, '%signed%' => $signed)));
            $this->invalidate = true;
        }

        return $this;
    }

    /**
     * 半角文字チェック。
     * @param array $errors （戻り値）エラーリスト
     * @param string $itemId 項目ID
     * @param string $itemName 項目名称
     * @param mixed $value 値
     * @return Validatation 自身のオブジェクト
     */
    public function validateHalfChar(array &$errors, string $itemId, string $itemName, $value): Validatation {

        if ($this->oneOfTheFollowing && $this->invalidate) {
            // チェックせずに処理を終了
            return $this;
        }

        if (!$this->checkHalfChar($value)) {
            $errors[] = self::createError($itemId, $this->messages->get('validation_half_char', array('%itemName%' => $itemName)));
            $this->invalidate = true;
        }

        return $this;
    }

    /**
     * カナ文字または半角文字チェック。
     * @param array $errors （戻り値）エラーリスト
     * @param string $itemId 項目ID
     * @param string $itemName 項目名称
     * @param mixed $value 値
     * @return Validatation 自身のオブジェクト
     */
    public function validateKanaOrHalfChar(array &$errors, string $itemId, string $itemName, $value): Validatation {

        if ($this->oneOfTheFollowing && $this->invalidate) {
            // チェックせずに処理を終了
            return $this;
        }

        if (!$this->checkKanaOrHalfChar($value)) {
            $errors[] = self::createError($itemId, $this->messages->get('validation_kana_or_half_char', array('%itemName%' => $itemName)));
            $this->invalidate = true;
        }

        return $this;
    }

    /**
     * 日時チェック。
     * @param array $errors （戻り値）エラーリスト
     * @param string $itemId 項目ID
     * @param string $itemName 項目名称
     * @param mixed $value 値
     * @param array $format フォーマット
     * @return Validatation 自身のオブジェクト
     */
    public function validateDateTime(array &$errors, string $itemId, string $itemName, $value, array $format = null): Validatation {

        if ($this->oneOfTheFollowing && $this->invalidate) {
            // チェックせずに処理を終了
            return $this;
        }

        if (!$this->checkDateTime($value, $format)) {
            $errors[] = self::createError($itemId, $this->messages->get('validation_date_time', array('%itemName%' => $itemName)));
            $this->invalidate = true;
        }

        return $this;
    }

    /**
     * URLチェック。
     * @param array $errors （戻り値）エラーリスト
     * @param string $itemId 項目ID
     * @param string $itemName 項目名称
     * @param mixed $value 値
     * @return Validatation 自身のオブジェクト
     */
    public function validateUrl(array &$errors, string $itemId, string $itemName, $value): Validatation {

        if ($this->oneOfTheFollowing && $this->invalidate) {
            // チェックせずに処理を終了
            return $this;
        }

        if (!$this->checkUrl($value)) {
            $errors[] = self::createError($itemId, $this->messages->get('validation_url', array('%itemName%' => $itemName)));
            $this->invalidate = true;
        }

        return $this;
    }

    /**
     * EMailチェック。
     * @param array $errors （戻り値）エラーリスト
     * @param string $itemId 項目ID
     * @param string $itemName 項目名称
     * @param mixed $value 値
     * @return Validatation 自身のオブジェクト
     */
    public function validateEmail(array &$errors, string $itemId, string $itemName, $value): Validatation {

        if ($this->oneOfTheFollowing && $this->invalidate) {
            // チェックせずに処理を終了
            return $this;
        }

        if (!$this->checkEmail($value)) {
            $errors[] = self::createError($itemId, $this->messages->get('validation_email', array('%itemName%' => $itemName)));
            $this->invalidate = true;
        }

        return $this;
    }

    /**
     * EMailチェック。
     * @param array $errors （戻り値）エラーリスト
     * @param string $itemId 項目ID
     * @param string $itemName 項目名称
     * @param mixed $value 値
     * @return Validatation 自身のオブジェクト
     */
    public function validatePassword(array &$errors, string $itemId, string $itemName, $value): Validatation {

        $PASSWORD_LENGTH = 10;

        if ($this->oneOfTheFollowing && $this->invalidate) {
            // チェックせずに処理を終了
            return $this;
        }

        if (!$this->checkPassword($value, $PASSWORD_LENGTH)) {
            $errors[] = self::createError($itemId, $this->messages->get('validation_password', array('%itemName%' => $itemName, '%length%' => $PASSWORD_LENGTH)));
            $this->invalidate = true;
        }

        return $this;
    }

    /**
     * 正規表現チェック。
     * @param array $errors （戻り値）エラーリスト
     * @param string $itemId 項目ID
     * @param string $itemName 項目名称
     * @param string $formatMessage フォーマットメッセージ
     * @param mixed $value 値
     * @param string $regExp 正規表現パターン
     * @return Validatation 自身のオブジェクト
     */
    public function validateRegExp(array &$errors, string $itemId, string $itemName, string $formatMessage, $value, $regExp): Validatation {

        if ($this->oneOfTheFollowing && $this->invalidate) {
            // チェックせずに処理を終了
            return $this;
        }

        if (!$this->checkRegExp($value, $regExp)) {
            $errors[] = self::createError($itemId, $this->messages->get('validation_regexp', array('%itemName%' => $itemName, '%formatMessage%' => $formatMessage)));
            $this->invalidate = true;
        }

        return $this;
    }

    /**
     * 桁数チェック。
     * @param array $errors （戻り値）エラーリスト
     * @param string $itemId 項目ID
     * @param string $itemName 項目名称
     * @param mixed $value 値
     * @param int $length 桁数
     * @return Validatation 自身のオブジェクト
     */
    public function validateLength(array &$errors, string $itemId, string $itemName, $value, int $length): Validatation {

        if ($this->oneOfTheFollowing && $this->invalidate) {
            // チェックせずに処理を終了
            return $this;
        }

        if (!$this->checkLength($value, $length)) {
            $errors[] = self::createError($itemId, $this->messages->get('validation_length', array('%itemName%' => $itemName, '%length%' => $length)));
            $this->invalidate = true;
        }

        return $this;
    }

    /**
     * 桁数の範囲チェック。
     * @param array $errors （戻り値）エラーリスト
     * @param string $itemId 項目ID
     * @param string $itemName 項目名称
     * @param mixed $value 値
     * @param int $min 最小桁数
     * @param int $max 最大桁数
     * @return Validatation 自身のオブジェクト
     */
    public function validateRangeForLength(array &$errors, string $itemId, string $itemName, $value, int $min, int $max): Validatation {

        if ($this->oneOfTheFollowing && $this->invalidate) {
            // チェックせずに処理を終了
            return $this;
        }

        if (!$this->checkRangeForLength($value, $min, $max)) {
            $errors[] = self::createError($itemId, $this->messages->get('validation_range_length', array('%itemName%' => $itemName, '%min%' => $min, '%max%' => $max)));
            $this->invalidate = true;
        }

        return $this;
    }

    /**
     * 数値の範囲チェック。
     * @param array $errors （戻り値）エラーリスト
     * @param string $itemId 項目ID
     * @param string $itemName 項目名称
     * @param mixed $value 値
     * @param mixed $min 最小桁数
     * @param mixed $max 最大桁数
     * @return Validatation 自身のオブジェクト
     */
    public function validateRangeForNumeric(array &$errors, string $itemId, string $itemName, $value, $min, $max): Validatation {

        if ($this->oneOfTheFollowing && $this->invalidate) {
            // チェックせずに処理を終了
            return $this;
        }

        if (!$this->checkRange($value, $min, $max)) {
            $errors[] = self::createError($itemId, $this->messages->get('validation_range_numeric', array('%itemName%' => $itemName, '%min%' => $min, '%max%' => $max)));
            $this->invalidate = true;
        }

        return $this;
    }

    /**
     * 日付の範囲チェック。
     * @param array $errors （戻り値）エラーリスト
     * @param string $itemId 項目ID
     * @param string $itemName 項目名称
     * @param mixed $value 値
     * @param mixed $min 最小桁数
     * @param mixed $max 最大桁数
     * @param string $format フォーマット
     * @return Validatation 自身のオブジェクト
     */
    public function validateRangeForDateTime(array &$errors, string $itemId, string $itemName, $value, $min, $max, string $format): Validatation {

        if ($this->oneOfTheFollowing && $this->invalidate) {
            // チェックせずに処理を終了
            return $this;
        }

        if (!$this->checkRange($value, $min, $max)) {
            $minStr = $min->format($format);
            $maxStr = $max->format($format);
            $errors[] = self::createError($itemId, $this->messages->get('validation_range_datetime', array('%itemName%' => $itemName, '%min%' => $minStr, '%max%' => $maxStr)));
            $this->invalidate = true;
        }

        return $this;
    }

    /**
     * エラーを生成する。
     * @param array $errors （戻り値）エラーリスト
     * @param string $id ID
     * @param string $messageId メッセージID
     * @param array $messageParams メッセージパラメータリスト
     */
    public function createAnyError(array &$errors, string $id, string $messageId, array $messageParams) {
        $errors[] = ['id' => $id, 'message' => $this->messages->get($messageId, $messageParams)];
    }

}
