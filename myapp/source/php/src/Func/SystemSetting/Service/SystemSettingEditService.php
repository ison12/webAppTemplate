<?php

namespace App\Func\SystemSetting\Service;

use App\Common\Data\User;
use App\Common\DB\DBFactory;
use App\Common\Exception\DBException;
use App\Common\Exception\ServiceException;
use App\Common\Util\DateUtil;
use App\Common\Util\ValUtil;
use App\Common\Validation\Validatation;
use App\Dao\SystemSetting\SystemSettingDao;
use App\Func\Base\Service\DBBaseService;

/**
 * システム設定編集サービス。
 */
class SystemSettingEditService extends DBBaseService {

    /**
     * コンストラクタ。
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * 保存時の入力チェックを実施する。
     * @param array $data データ
     * @return array エラーリスト
     */
    private function validateForSave(array $data): array {

        $errors = [];

        // 入力チェックを実施
        $validation = Validatation::getInstance();

        // コードチェック
        $itemId = 'system_code';
        $itemName = 'コード';
        $validation
                ->oneOfTheFollowing()
                ->validateRequired($errors, $itemId, $itemName, $data[$itemId])
                ->validateHalfChar($errors, $itemId, $itemName, $data[$itemId])
                ->validateLength($errors, $itemId, $itemName, $data[$itemId], 100)
                ->end()
        ;

        // 名称チェック
        $itemId = 'system_name';
        $itemName = '名称';
        $validation
                ->oneOfTheFollowing()
                ->validateRequired($errors, $itemId, $itemName, $data[$itemId])
                ->validateLength($errors, $itemId, $itemName, $data[$itemId], 100)
                ->end()
        ;

        // 値チェック
        $itemId = 'system_value';
        $itemName = '値';
        $validation
                ->oneOfTheFollowing()
                ->validateRequired($errors, $itemId, $itemName, $data[$itemId])
                ->validateLength($errors, $itemId, $itemName, $data[$itemId], 200)
                ->end()
        ;

        return $errors;
    }

    /**
     * 保存処理。
     * @param array $data データ
     * @param User $user ユーザー情報
     * @return array レコード
     */
    public function save(array $data, User $user = null): array {

        $saveData = [];

        $errors = $this->validateForSave($data);
        if (count($errors) > 0) {
            throw new ServiceException($errors);
        }

        // DBへの更新
        try {
            $this->transaction($this->dbConnection, function($dbConnection) use($data, &$saveData, $user) {

                $systemDao = new SystemSettingDao($dbConnection);

                // レコードの存在チェックを実施する
                $selectRecord = null;
                if (!ValUtil::isEmpty($data['system_code_last'])) {
                    // ID値が設定されている場合、レコードを取得する
                    $selectRecord = $systemDao->selectBySystemCode($data['system_code_last'], true);
                    if ($selectRecord === null) {
                        // レコードが存在しない場合
                        $error = Validatation::createError(
                                        'error_data_not_found'
                                        , $this->errorMessage->get('error_data_not_found', ['%itemName%' => 'システム設定', '%id%' => $data['system_code_last']]));
                        throw new ServiceException([$error]);
                    }
                }

                if ($selectRecord === null) {
                    // レコードが存在しない場合、新規登録する
                    $saveData = $this->saveForInsert($systemDao, $data, $user);
                } else {

                    if ($selectRecord['update_datetime'] !== $data['update_datetime']) {
                        // タイムスタンプが異なる場合は、他の操作で既に更新されたとみなす
                        $error = Validatation::createError('error_data_already_update', $this->errorMessage->get('error_data_already_update', ['%itemName%' => 'システム設定']));
                        throw new ServiceException([$error]);
                    }

                    // レコードが存在する場合、更新する
                    $saveData = $this->saveForUpdate($systemDao, $data, $user);
                }
            });
        } catch (DBException $ex) {

            if (DBFactory::createDBHelper()->isErrorForLock($ex)) {
                // ロックエラー
                $error = Validatation::createError('error_data_lock_timeout', $this->errorMessage->get('error_data_already_update', ['%itemName%' => 'システム設定']));
                throw new ServiceException([$error]);
            } else if (DBFactory::createDBHelper()->isErrorForTimeout($ex)) {
                // タイムアウトエラー
                $error = Validatation::createError('error_data_query_timeout', $this->errorMessage->get('error_data_query_timeout', ['%itemName%' => 'システム設定']));
                throw new ServiceException([$error]);
            } else if (DBFactory::createDBHelper()->isErrorForDuplicate($ex)) {
                // 重複エラー
                $error = Validatation::createError('error_data_duplicate', $this->errorMessage->get('error_data_duplicate', ['%itemName%' => 'システム設定']));
                throw new ServiceException([$error]);
            }

            throw $ex;
        }

        return $saveData;
    }

    /**
     * 削除処理。
     * @param array $data データ
     * @param User $user ユーザー情報
     */
    public function delete(array $data, User $user = null) {

        // DBへの更新
        try {
            $this->transaction($this->dbConnection, function($dbConnection) use($data, $user) {

                $systemDao = new SystemSettingDao($dbConnection);

                // レコードの存在チェックを実施する
                $selectRecord = $systemDao->selectBySystemCode($data['system_code'], true);

                if ($selectRecord !== null) {
                    // レコードが存在する場合、削除する

                    if ($selectRecord['update_datetime'] !== $data['update_datetime']) {
                        // タイムスタンプが異なる場合は、他の操作で既に更新されたとみなす
                        $error = Validatation::createError('error_data_already_update', $this->errorMessage->get('error_data_already_update', ['%itemName%' => 'システム設定']));
                        throw new ServiceException([$error]);
                    }

                    // 更新クエリ発行
                    $this->saveForDelete($systemDao, $data, $user);
                }
            });
        } catch (DBException $ex) {

            if (DBFactory::createDBHelper()->isErrorForLock($ex)) {
                // ロックエラー
                $error = Validatation::createError('error_data_lock_timeout', $this->errorMessage->get('error_data_already_update', ['%itemName%' => 'システム設定']));
                throw new ServiceException([$error]);
            } else if (DBFactory::createDBHelper()->isErrorForTimeout($ex)) {
                // タイムアウトエラー
                $error = Validatation::createError('error_data_query_timeout', $this->errorMessage->get('error_data_already_update', ['%itemName%' => 'システム設定']));
                throw new ServiceException([$error]);
            }

            throw $ex;
        }
    }

    /**
     * 登録処理。
     * @param SystemSettingDao $systemDao システム設定DAO
     * @param array $data データ
     * @param User $user ユーザー情報
     * @return array レコード
     */
    private function saveForInsert(SystemSettingDao $systemDao, array $data, User $user = null): array {

        // 登録クエリ発行
        $data['create_datetime'] = $this->systemDate->format(DateUtil::DATETIME_HYPHEN_MICRO_FORMAT_COMMON);
        $data['create_user_id'] = $user->user_id ?? 0;
        $data['update_datetime'] = $this->systemDate->format(DateUtil::DATETIME_HYPHEN_MICRO_FORMAT_COMMON);
        $data['update_user_id'] = $user->user_id ?? 0;

        $systemDao->insert($data);

        return $data;
    }

    /**
     * 更新処理。
     * @param SystemSettingDao $systemDao システム設定DAO
     * @param array $data データ
     * @param User $user ユーザー情報
     * @return array レコード
     */
    private function saveForUpdate(SystemSettingDao $systemDao, array $data, User $user = null): array {

        // 更新クエリ発行
        $data['update_datetime'] = $this->systemDate->format(DateUtil::DATETIME_HYPHEN_MICRO_FORMAT_COMMON);
        $data['update_user_id'] = $user->user_id ?? 0;
        $systemDao->update($data, $data['system_code_last']);

        return $data;
    }

    /**
     * 削除処理。
     * @param SystemSettingDao $systemDao システム設定DAO
     * @param array $data データ
     * @param User $user ユーザー情報
     */
    private function saveForDelete(SystemSettingDao $systemDao, array $data, User $user = null) {

        // 削除クエリ発行
        $systemDao->delete($data['system_code_last']);
    }

}
