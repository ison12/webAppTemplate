<?php

namespace Tests\App\Common\Validation;

use App\Common\Util\DateUtil;
use App\Common\Validation\Validatation;
use Tests\Common\BaseTest;

/**
 * バリデーション。
 * テストクラス。
 *
 *
 */
class ValidationTest extends BaseTest {

    /**
     * 共通処理。
     */
    public function setUp() {

    }

    /**
     * テスト内容：必須チェックのテスト。
     */
    public function testValidateRequired() {

        $validation = Validatation::getInstance();

        $errorLen = 4;
        $errors = [];
        // ↓異常パターン
        $validation->validateRequired($errors, 'test', 'テスト', null);
        $validation->validateRequired($errors, 'test', 'テスト', '');
        $validation->validateRequired($errors, 'test', 'テスト', false);
        $validation->validateRequired($errors, 'test', 'テスト', []);
        // 異常件数をチェック
        $this->assertSame($errorLen, count($errors));
        // ↓正常パターン
        $validation->validateRequired($errors, 'test', 'テスト', 'Success');
        $validation->validateRequired($errors, 'test', 'テスト', 'あいうえお');
        $validation->validateRequired($errors, 'test', 'テスト', true);
        $validation->validateRequired($errors, 'test', 'テスト', ['Success']);
        // 異常件数をチェック
        $this->assertSame($errorLen, count($errors));

        for ($index = 0; $index < $errorLen; $index++) {
            $this->assertSame('test', $errors[$index]['id']);
            $this->assertSame('テストは必須です。', $errors[$index]['message']);
        }
    }

    /**
     * テスト内容：アルファベットチェックのテスト。
     */
    public function testValidateAlphabet() {

        $validation = Validatation::getInstance();

        $errorLen = 6;
        $errors = [];
        // ↓異常パターン
        $validation->validateAlphabet($errors, 'test', 'テスト', null);
        $validation->validateAlphabet($errors, 'test', 'テスト', '');
        $validation->validateAlphabet($errors, 'test', 'テスト', 'aA1');
        $validation->validateAlphabet($errors, 'test', 'テスト', 'aAあ');
        $validation->validateAlphabet($errors, 'test', 'テスト', ';aA');
        $validation->validateAlphabet($errors, 'test', 'テスト', 'aA;');
        // 異常件数をチェック
        $this->assertSame($errorLen, count($errors));
        // ↓正常パターン
        $validation->validateAlphabet($errors, 'test', 'テスト', 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz');
        // 異常件数をチェック
        $this->assertSame($errorLen, count($errors));

        for ($index = 0; $index < $errorLen; $index++) {
            $this->assertSame('test', $errors[$index]['id']);
            $this->assertSame('テストはアルファベットで入力してください。', $errors[$index]['message']);
        }
    }

    /**
     * テスト内容：小文字のアルファベットチェックのテスト。
     */
    public function testValidateAlphabetLower() {

        $validation = Validatation::getInstance();

        $errorLen = 8;
        $errors = [];
        // ↓異常パターン
        $validation->validateAlphabetLower($errors, 'test', 'テスト', null);
        $validation->validateAlphabetLower($errors, 'test', 'テスト', '');
        $validation->validateAlphabetLower($errors, 'test', 'テスト', 'a1');
        $validation->validateAlphabetLower($errors, 'test', 'テスト', 'a;');
        $validation->validateAlphabetLower($errors, 'test', 'テスト', 'aあ');
        $validation->validateAlphabetLower($errors, 'test', 'テスト', 'AZ');
        $validation->validateAlphabetLower($errors, 'test', 'テスト', ';AZ');
        $validation->validateAlphabetLower($errors, 'test', 'テスト', 'AZ;');
        // 異常件数をチェック
        $this->assertSame($errorLen, count($errors));
        // ↓正常パターン
        $validation->validateAlphabetLower($errors, 'test', 'テスト', 'abcdefghijklmnopqrstuvwxyz');
        // 異常件数をチェック
        $this->assertSame($errorLen, count($errors));

        for ($index = 0; $index < $errorLen; $index++) {
            $this->assertSame('test', $errors[$index]['id']);
            $this->assertSame('テストは小文字のアルファベットで入力してください。', $errors[$index]['message']);
        }
    }

    /**
     * テスト内容：大文字のアルファベットチェックのテスト。
     */
    public function testValidateAlphabetUpper() {

        $validation = Validatation::getInstance();

        $errorLen = 8;
        $errors = [];
        // ↓異常パターン
        $validation->validateAlphabetUpper($errors, 'test', 'テスト', null);
        $validation->validateAlphabetUpper($errors, 'test', 'テスト', '');
        $validation->validateAlphabetUpper($errors, 'test', 'テスト', 'A1');
        $validation->validateAlphabetUpper($errors, 'test', 'テスト', 'A;');
        $validation->validateAlphabetUpper($errors, 'test', 'テスト', 'Aあ');
        $validation->validateAlphabetUpper($errors, 'test', 'テスト', 'az');
        $validation->validateAlphabetUpper($errors, 'test', 'テスト', ';az');
        $validation->validateAlphabetUpper($errors, 'test', 'テスト', 'az;');
        // 異常件数をチェック
        $this->assertSame($errorLen, count($errors));
        // ↓正常パターン
        $validation->validateAlphabetUpper($errors, 'test', 'テスト', 'ABCDEFGHIJKLMNOPQRSTUVWXYZ');
        // 異常件数をチェック
        $this->assertSame($errorLen, count($errors));

        for ($index = 0; $index < $errorLen; $index++) {
            $this->assertSame('test', $errors[$index]['id']);
            $this->assertSame('テストは大文字のアルファベットで入力してください。', $errors[$index]['message']);
        }
    }

    /**
     * テスト内容：整数チェックのテスト。
     */
    public function testValidateInteger() {

        $validation = Validatation::getInstance();

        $errorLen = 11;
        $errors = [];
        // ↓異常パターン
        $validation->validateInteger($errors, 'test', 'テスト', null);
        $validation->validateInteger($errors, 'test', 'テスト', '');
        $validation->validateInteger($errors, 'test', 'テスト', 'a');
        $validation->validateInteger($errors, 'test', 'テスト', 'A');
        $validation->validateInteger($errors, 'test', 'テスト', ';');
        $validation->validateInteger($errors, 'test', 'テスト', 'あ');
        $validation->validateInteger($errors, 'test', 'テスト', '1234567890.0');
        $validation->validateInteger($errors, 'test', 'テスト', '-1234567890');
        $validation->validateInteger($errors, 'test', 'テスト', '+1234567890');
        $validation->validateInteger($errors, 'test', 'テスト', 'a1234567890');
        $validation->validateInteger($errors, 'test', 'テスト', '1234567890b');
        // 異常件数をチェック
        $this->assertSame($errorLen, count($errors));
        // ↓正常パターン
        $validation->validateInteger($errors, 'test', 'テスト', '1234567890');
        // 異常件数をチェック
        $this->assertSame($errorLen, count($errors));

        for ($index = 0; $index < $errorLen; $index++) {
            $this->assertSame('test', $errors[$index]['id']);
            $this->assertSame('テストは整数で入力してください。', $errors[$index]['message']);
        }
    }

    /**
     * テスト内容：整数チェックのテスト。
     */
    public function testValidateSignedInteger() {

        $validation = Validatation::getInstance();

        $errorLen = 9;
        $errors = [];
        // ↓異常パターン
        $validation->validateInteger($errors, 'test', 'テスト', null, true);
        $validation->validateInteger($errors, 'test', 'テスト', '', true);
        $validation->validateInteger($errors, 'test', 'テスト', 'a', true);
        $validation->validateInteger($errors, 'test', 'テスト', 'A', true);
        $validation->validateInteger($errors, 'test', 'テスト', ';', true);
        $validation->validateInteger($errors, 'test', 'テスト', 'あ', true);
        $validation->validateInteger($errors, 'test', 'テスト', '1234567890.0', true);
        $validation->validateInteger($errors, 'test', 'テスト', 'a1234567890', true);
        $validation->validateInteger($errors, 'test', 'テスト', '1234567890b', true);
        // 異常件数をチェック
        $this->assertSame($errorLen, count($errors));
        // ↓正常パターン
        $validation->validateInteger($errors, 'test', 'テスト', '1234567890', true);
        $validation->validateInteger($errors, 'test', 'テスト', '-1234567890', true);
        $validation->validateInteger($errors, 'test', 'テスト', '+1234567890', true);
        // 異常件数をチェック
        $this->assertSame($errorLen, count($errors));

        for ($index = 0; $index < $errorLen; $index++) {
            $this->assertSame('test', $errors[$index]['id']);
            $this->assertSame('テストは符号付きの整数で入力してください。', $errors[$index]['message']);
        }
    }

    /**
     * テスト内容：数値チェックのテスト。
     */
    public function testValidateNumeric() {

        $validation = Validatation::getInstance();

        $errorLen = 12;
        $errors = [];
        // ↓異常パターン
        $validation->validateNumeric($errors, 'test', 'テスト', null, 5, 1, false);
        $validation->validateNumeric($errors, 'test', 'テスト', '', 5, 1, false);
        $validation->validateNumeric($errors, 'test', 'テスト', 'a', 5, 1, false);
        $validation->validateNumeric($errors, 'test', 'テスト', 'A', 5, 1, false);
        $validation->validateNumeric($errors, 'test', 'テスト', ';', 5, 1, false);
        $validation->validateNumeric($errors, 'test', 'テスト', 'あ', 5, 1, false);
        $validation->validateNumeric($errors, 'test', 'テスト', '123456', 5, 1, false);
        $validation->validateNumeric($errors, 'test', 'テスト', '12345.67', 5, 1, false);
        $validation->validateNumeric($errors, 'test', 'テスト', '+12345', 5, 1, false);
        $validation->validateNumeric($errors, 'test', 'テスト', '-12345', 5, 1, false);
        $validation->validateNumeric($errors, 'test', 'テスト', 'a12345.6', 5, 1, false);
        $validation->validateNumeric($errors, 'test', 'テスト', '12345.6a', 5, 1, false);
        // 異常件数をチェック
        $this->assertSame($errorLen, count($errors));
        // ↓正常パターン
        $validation->validateNumeric($errors, 'test', 'テスト', '12345', 5, 1, false);
        $validation->validateNumeric($errors, 'test', 'テスト', '12345.', 5, 1, false);
        $validation->validateNumeric($errors, 'test', 'テスト', '.6', 5, 1, false);
        $validation->validateNumeric($errors, 'test', 'テスト', '12345.6', 5, 1, false);
        // 異常件数をチェック
        $this->assertSame($errorLen, count($errors));

        for ($index = 0; $index < $errorLen; $index++) {
            $this->assertSame('test', $errors[$index]['id']);
            $this->assertSame('テストは数値で入力してください。（整数部 5桁、小数部 1桁）', $errors[$index]['message']);
        }

        $errorLen = 10;
        $errors = [];
        // ↓異常パターン
        $validation->validateNumeric($errors, 'test', 'テスト', null, 0, 0, false);
        $validation->validateNumeric($errors, 'test', 'テスト', '', 0, 0, false);
        $validation->validateNumeric($errors, 'test', 'テスト', 'a', 0, 0, false);
        $validation->validateNumeric($errors, 'test', 'テスト', 'A', 0, 0, false);
        $validation->validateNumeric($errors, 'test', 'テスト', ';', 0, 0, false);
        $validation->validateNumeric($errors, 'test', 'テスト', 'あ', 0, 0, false);
        $validation->validateNumeric($errors, 'test', 'テスト', '+12345', 0, 0, false);
        $validation->validateNumeric($errors, 'test', 'テスト', '-12345', 0, 0, false);
        $validation->validateNumeric($errors, 'test', 'テスト', 'a12345.6', 0, 0, false);
        $validation->validateNumeric($errors, 'test', 'テスト', '12345.6a', 0, 0, false);
        // 異常件数をチェック
        $this->assertSame($errorLen, count($errors));

        // ↓正常パターン
        $validation->validateNumeric($errors, 'test', 'テスト', '12345', 0, 0, false);
        $validation->validateNumeric($errors, 'test', 'テスト', '12345.', 0, 0, false);
        $validation->validateNumeric($errors, 'test', 'テスト', '.6', 0, 0, false);
        $validation->validateNumeric($errors, 'test', 'テスト', '12345.6', 0, 0, false);
        // 異常件数をチェック
        $this->assertSame($errorLen, count($errors));

        for ($index = 0; $index < $errorLen; $index++) {
            $this->assertSame('test', $errors[$index]['id']);
            $this->assertSame('テストは数値で入力してください。', $errors[$index]['message']);
        }
    }

    /**
     * テスト内容：数値チェックのテスト。
     */
    public function testValidateSignedNumeric() {

        $validation = Validatation::getInstance();

        $errorLen = 10;
        $errors = [];
        // ↓異常パターン
        $validation->validateNumeric($errors, 'test', 'テスト', null, 5, 1, true);
        $validation->validateNumeric($errors, 'test', 'テスト', '', 5, 1, true);
        $validation->validateNumeric($errors, 'test', 'テスト', 'a', 5, 1, true);
        $validation->validateNumeric($errors, 'test', 'テスト', 'A', 5, 1, true);
        $validation->validateNumeric($errors, 'test', 'テスト', ';', 5, 1, true);
        $validation->validateNumeric($errors, 'test', 'テスト', 'あ', 5, 1, true);
        $validation->validateNumeric($errors, 'test', 'テスト', '123456', 5, 1, true);
        $validation->validateNumeric($errors, 'test', 'テスト', '12345.67', 5, 1, true);
        $validation->validateNumeric($errors, 'test', 'テスト', 'a12345.6', 5, 1, true);
        $validation->validateNumeric($errors, 'test', 'テスト', '12345.6a', 5, 1, true);
        // 異常件数をチェック
        $this->assertSame($errorLen, count($errors));
        // ↓正常パターン
        $validation->validateNumeric($errors, 'test', 'テスト', '12345', 5, 1, true);
        $validation->validateNumeric($errors, 'test', 'テスト', '12345.', 5, 1, true);
        $validation->validateNumeric($errors, 'test', 'テスト', '.6', 5, 1, true);
        $validation->validateNumeric($errors, 'test', 'テスト', '12345.6', 5, 1, true);
        $validation->validateNumeric($errors, 'test', 'テスト', '+12345', 5, 1, true);
        $validation->validateNumeric($errors, 'test', 'テスト', '+12345.', 5, 1, true);
        $validation->validateNumeric($errors, 'test', 'テスト', '+.6', 5, 1, true);
        $validation->validateNumeric($errors, 'test', 'テスト', '+12345.6', 5, 1, true);
        $validation->validateNumeric($errors, 'test', 'テスト', '-12345', 5, 1, true);
        $validation->validateNumeric($errors, 'test', 'テスト', '-12345.', 5, 1, true);
        $validation->validateNumeric($errors, 'test', 'テスト', '-.6', 5, 1, true);
        $validation->validateNumeric($errors, 'test', 'テスト', '-12345.6', 5, 1, true);
        // 異常件数をチェック
        $this->assertSame($errorLen, count($errors));

        for ($index = 0; $index < $errorLen; $index++) {
            $this->assertSame('test', $errors[$index]['id']);
            $this->assertSame('テストは符号付きの数値で入力してください。（整数部 5桁、小数部 1桁）', $errors[$index]['message']);
        }

        $errorLen = 8;
        $errors = [];
        // ↓異常パターン
        $validation->validateNumeric($errors, 'test', 'テスト', null, 0, 0, true);
        $validation->validateNumeric($errors, 'test', 'テスト', '', 0, 0, true);
        $validation->validateNumeric($errors, 'test', 'テスト', 'a', 0, 0, true);
        $validation->validateNumeric($errors, 'test', 'テスト', 'A', 0, 0, true);
        $validation->validateNumeric($errors, 'test', 'テスト', ';', 0, 0, true);
        $validation->validateNumeric($errors, 'test', 'テスト', 'あ', 0, 0, true);
        $validation->validateNumeric($errors, 'test', 'テスト', 'a12345.6', 0, 0, true);
        $validation->validateNumeric($errors, 'test', 'テスト', '12345.6a', 0, 0, true);
        // 異常件数をチェック
        $this->assertSame($errorLen, count($errors));

        // ↓正常パターン
        $validation->validateNumeric($errors, 'test', 'テスト', '12345', 0, 0, true);
        $validation->validateNumeric($errors, 'test', 'テスト', '12345.', 0, 0, true);
        $validation->validateNumeric($errors, 'test', 'テスト', '.6', 0, 0, true);
        $validation->validateNumeric($errors, 'test', 'テスト', '12345.6', 0, 0, true);
        $validation->validateNumeric($errors, 'test', 'テスト', '+12345', 0, 0, true);
        $validation->validateNumeric($errors, 'test', 'テスト', '+12345.', 0, 0, true);
        $validation->validateNumeric($errors, 'test', 'テスト', '+.6', 0, 0, true);
        $validation->validateNumeric($errors, 'test', 'テスト', '+12345.6', 0, 0, true);
        $validation->validateNumeric($errors, 'test', 'テスト', '-12345', 0, 0, true);
        $validation->validateNumeric($errors, 'test', 'テスト', '-12345.', 0, 0, true);
        $validation->validateNumeric($errors, 'test', 'テスト', '-.6', 0, 0, true);
        $validation->validateNumeric($errors, 'test', 'テスト', '-12345.6', 0, 0, true);
        // 異常件数をチェック
        $this->assertSame($errorLen, count($errors));

        for ($index = 0; $index < $errorLen; $index++) {
            $this->assertSame('test', $errors[$index]['id']);
            $this->assertSame('テストは符号付きの数値で入力してください。', $errors[$index]['message']);
        }
    }

    /**
     * テスト内容：半角文字チェックのテスト。
     */
    public function testValidateHalfChar() {

        $validation = Validatation::getInstance();

        $errorLen = 6;
        $errors = [];
        // ↓異常パターン
        $validation->validateHalfChar($errors, 'test', 'テスト', null);
        $validation->validateHalfChar($errors, 'test', 'テスト', '');
        $validation->validateHalfChar($errors, 'test', 'テスト', 'あいうえお');
        $validation->validateHalfChar($errors, 'test', 'テスト', 'ｱｲｳｴｵ');
        $validation->validateHalfChar($errors, 'test', 'テスト', 'あ!');
        $validation->validateHalfChar($errors, 'test', 'テスト', '!あ');
        // 異常件数をチェック
        $this->assertSame($errorLen, count($errors));
        // ↓正常パターン
        $validation->validateHalfChar($errors, 'test', 'テスト', ' !"#$%&\'()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\]^_`abcdefghijklmnopqrstuvwxyz{|}~');
        // 異常件数をチェック
        $this->assertSame($errorLen, count($errors));

        for ($index = 0; $index < $errorLen; $index++) {
            $this->assertSame('test', $errors[$index]['id']);
            $this->assertSame('テストは半角文字で入力してください。', $errors[$index]['message']);
        }
    }

    /**
     * テスト内容：日時チェックのテスト。
     */
    public function testValidateDateTime() {

        $validation = Validatation::getInstance();

        $dateFormat = ['Y-m-d H:i:s.u'];

        $errorLen = 6;
        $errors = [];
        // ↓異常パターン
        $validation->validateDateTime($errors, 'test', 'テスト', null, $dateFormat);
        $validation->validateDateTime($errors, 'test', 'テスト', '', $dateFormat);
        $validation->validateDateTime($errors, 'test', 'テスト', 'a', $dateFormat);
        $validation->validateDateTime($errors, 'test', 'テスト', ';2018-01-01 01:02:03.123456', $dateFormat);
        $validation->validateDateTime($errors, 'test', 'テスト', '2018-01-01 01:02:03.123456;', $dateFormat);
        $validation->validateDateTime($errors, 'test', 'テスト', '2018-01-01 01:02:03.1234567', $dateFormat);
        // 異常件数をチェック
        $this->assertSame($errorLen, count($errors));
        // ↓正常パターン
        $validation->validateDateTime($errors, 'test', 'テスト', '2018-01-01 01:02:03.123456', $dateFormat);
        // 異常件数をチェック
        $this->assertSame($errorLen, count($errors));

        for ($index = 0; $index < $errorLen; $index++) {
            $this->assertSame('test', $errors[$index]['id']);
            $this->assertSame('テストは日時形式で入力してください。', $errors[$index]['message']);
        }
    }

    /**
     * テスト内容：Urlチェックのテスト。
     */
    public function testValidateUrl() {

        $validation = Validatation::getInstance();

        $errorLen = 6;
        $errors = [];
        // ↓異常パターン
        $validation->validateUrl($errors, 'test', 'テスト', null);
        $validation->validateUrl($errors, 'test', 'テスト', '');
        $validation->validateUrl($errors, 'test', 'テスト', 'a');
        $validation->validateUrl($errors, 'test', 'テスト', 'ftp://www.myapp.com');
        $validation->validateUrl($errors, 'test', 'テスト', 'あhttp://www.myapp.com');
        $validation->validateUrl($errors, 'test', 'テスト', 'http://www.myapp.comあ');
        // 異常件数をチェック
        $this->assertSame($errorLen, count($errors));
        // ↓正常パターン
        $validation->validateUrl($errors, 'test', 'テスト', 'http://www.myapp.com');
        $validation->validateUrl($errors, 'test', 'テスト', 'https://www.myapp.com');
        // 異常件数をチェック
        $this->assertSame($errorLen, count($errors));

        for ($index = 0; $index < $errorLen; $index++) {
            $this->assertSame('test', $errors[$index]['id']);
            $this->assertSame('テストはURL形式で入力してください。', $errors[$index]['message']);
        }
    }

    /**
     * テスト内容：Emailチェックのテスト。
     */
    public function testValidateEmail() {

        $validation = Validatation::getInstance();

        $errorLen = 6;
        $errors = [];
        // ↓異常パターン
        $validation->validateEmail($errors, 'test', 'テスト', null);
        $validation->validateEmail($errors, 'test', 'テスト', '');
        $validation->validateEmail($errors, 'test', 'テスト', 'a');
        $validation->validateEmail($errors, 'test', 'テスト', 'あ');
        $validation->validateEmail($errors, 'test', 'テスト', 'あmyapp@myapp.com');
        $validation->validateEmail($errors, 'test', 'テスト', 'myapp@myapp.comあ');
        // 異常件数をチェック
        $this->assertSame($errorLen, count($errors));
        // ↓正常パターン
        $validation->validateEmail($errors, 'test', 'テスト', 'myapp@myapp.com');
        $validation->validateEmail($errors, 'test', 'テスト', 'myapp-user@myapp.com');
        // 異常件数をチェック
        $this->assertSame($errorLen, count($errors));

        for ($index = 0; $index < $errorLen; $index++) {
            $this->assertSame('test', $errors[$index]['id']);
            $this->assertSame('テストはEMail形式で入力してください。', $errors[$index]['message']);
        }
    }

    /**
     * テスト内容：正規表現チェックのテスト。
     */
    public function testValidateRegExp() {

        $validation = Validatation::getInstance();

        $postPattern = '/^[0-9]{3}-[0-9]{4}$/';
        $formatMessage = '郵便番号';

        $errorLen = 6;
        $errors = [];
        // ↓異常パターン
        $validation->validateRegExp($errors, 'test', 'テスト', $formatMessage, null, $postPattern);
        $validation->validateRegExp($errors, 'test', 'テスト', $formatMessage, '', $postPattern);
        $validation->validateRegExp($errors, 'test', 'テスト', $formatMessage, '000-00000', $postPattern);
        $validation->validateRegExp($errors, 'test', 'テスト', $formatMessage, '0000-0000', $postPattern);
        $validation->validateRegExp($errors, 'test', 'テスト', $formatMessage, 'a000-0000', $postPattern);
        $validation->validateRegExp($errors, 'test', 'テスト', $formatMessage, '000-0000a', $postPattern);
        // 異常件数をチェック
        $this->assertSame($errorLen, count($errors));
        // ↓正常パターン
        $validation->validateRegExp($errors, 'test', 'テスト', $formatMessage, '012-3456', $postPattern);
        $validation->validateRegExp($errors, 'test', 'テスト', $formatMessage, '789-0123', $postPattern);
        // 異常件数をチェック
        $this->assertSame($errorLen, count($errors));

        for ($index = 0; $index < $errorLen; $index++) {
            $this->assertSame('test', $errors[$index]['id']);
            $this->assertSame('テストは郵便番号形式で入力してください。', $errors[$index]['message']);
        }
    }

    /**
     * テスト内容：桁数チェックのテスト。
     */
    public function testValidateLength() {

        $validation = Validatation::getInstance();

        $errorLen = 2;
        $errors = [];
        // ↓異常パターン
        $validation->validateLength($errors, 'test', 'テスト', 'abc', 2);
        $validation->validateLength($errors, 'test', 'テスト', 'あいう', 2);
        // 異常件数をチェック
        $this->assertSame($errorLen, count($errors));
        // ↓正常パターン
        $validation->validateLength($errors, 'test', 'テスト', null, 0);
        $validation->validateLength($errors, 'test', 'テスト', '', 0);
        $validation->validateLength($errors, 'test', 'テスト', 'ab', 2);
        $validation->validateLength($errors, 'test', 'テスト', 'あい', 2);
        // 異常件数をチェック
        $this->assertSame($errorLen, count($errors));

        for ($index = 0; $index < $errorLen; $index++) {
            $this->assertSame('test', $errors[$index]['id']);
            $this->assertSame('テストは2桁で入力してください。', $errors[$index]['message']);
        }
    }

    /**
     * テスト内容：桁数範囲チェックのテスト。
     */
    public function testValidateRangeForLength() {

        $validation = Validatation::getInstance();

        $errorLen = 4;
        $errors = [];
        // ↓異常パターン
        $validation->validateRangeForLength($errors, 'test', 'テスト', 'a', 2, 3);
        $validation->validateRangeForLength($errors, 'test', 'テスト', 'あいうえ', 2, 3);
        $validation->validateRangeForLength($errors, 'test', 'テスト', null, 2, 3);
        $validation->validateRangeForLength($errors, 'test', 'テスト', '', 2, 3);
        // 異常件数をチェック
        $this->assertSame($errorLen, count($errors));
        // ↓正常パターン
        $validation->validateRangeForLength($errors, 'test', 'テスト', 'ab', 2, 3);
        $validation->validateRangeForLength($errors, 'test', 'テスト', 'あいう', 2, 3);
        // 異常件数をチェック
        $this->assertSame($errorLen, count($errors));

        for ($index = 0; $index < $errorLen; $index++) {
            $this->assertSame('test', $errors[$index]['id']);
            $this->assertSame('テストは2桁～3桁で入力してください。', $errors[$index]['message']);
        }
    }

    /**
     * テスト内容：数値範囲チェックのテスト。
     */
    public function testValidateRangeForNumeric() {

        $validation = Validatation::getInstance();

        $errorLen = 4;
        $errors = [];
        // ↓異常パターン
        $validation->validateRangeForNumeric($errors, 'test', 'テスト', null, 1, 999);
        $validation->validateRangeForNumeric($errors, 'test', 'テスト', '', 1, 999);
        $validation->validateRangeForNumeric($errors, 'test', 'テスト', 0, 1, 999);
        $validation->validateRangeForNumeric($errors, 'test', 'テスト', 1000, 1, 999);
        // 異常件数をチェック
        $this->assertSame($errorLen, count($errors));
        // ↓正常パターン
        $validation->validateRangeForNumeric($errors, 'test', 'テスト', 1, 1, 999);
        $validation->validateRangeForNumeric($errors, 'test', 'テスト', 999, 1, 999);
        // 異常件数をチェック
        $this->assertSame($errorLen, count($errors));

        for ($index = 0; $index < $errorLen; $index++) {
            $this->assertSame('test', $errors[$index]['id']);
            $this->assertSame('テストは1～999で入力してください。', $errors[$index]['message']);
        }
    }

    /**
     * テスト内容：日付範囲チェックのテスト。
     */
    public function testValidateRangeForDateTime() {

        $validation = Validatation::getInstance();

        $dateFrom = DateUtil::createDateTime('2018-01-01 00:00:01');
        $dateTo = DateUtil::createDateTime('2018-05-03 00:00:01');
        $dateFormat = 'Y-m-d H:i:s';

        $errorLen = 3;
        $errors = [];
        // ↓異常パターン
        $validation->validateRangeForDateTime($errors, 'test', 'テスト', null, $dateFrom, $dateTo, $dateFormat);
        $validation->validateRangeForDateTime($errors, 'test', 'テスト', DateUtil::createDateTime('2018-01-01 00:00:00'), $dateFrom, $dateTo, $dateFormat);
        $validation->validateRangeForDateTime($errors, 'test', 'テスト', DateUtil::createDateTime('2018-05-03 00:00:02'), $dateFrom, $dateTo, $dateFormat);
        // 異常件数をチェック
        $this->assertSame($errorLen, count($errors));
        // ↓正常パターン
        $validation->validateRangeForDateTime($errors, 'test', 'テスト', DateUtil::createDateTime('2018-01-01 00:00:01'), $dateFrom, $dateTo, $dateFormat);
        $validation->validateRangeForDateTime($errors, 'test', 'テスト', DateUtil::createDateTime('2018-05-03 00:00:01'), $dateFrom, $dateTo, $dateFormat);
        // 異常件数をチェック
        $this->assertSame($errorLen, count($errors));

        for ($index = 0; $index < $errorLen; $index++) {
            $this->assertSame('test', $errors[$index]['id']);
            $this->assertSame('テストは2018-01-01 00:00:01～2018-05-03 00:00:01で入力してください。', $errors[$index]['message']);
        }
    }

}
