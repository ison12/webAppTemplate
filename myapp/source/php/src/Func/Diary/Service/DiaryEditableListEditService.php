<?php

namespace App\Func\Diary\Service;

use App\Common\Data\User;
use App\Common\DB\DBFactory;
use App\Common\Exception\DBException;
use App\Common\Exception\ServiceException;
use App\Common\Util\DateUtil;
use App\Common\Util\ValUtil;
use App\Common\Validation\Validatation;
use App\Dao\Diary\DiaryDao;
use App\Func\Base\Service\DBBaseService;

/**
 * 日記リスト編集サービス。
 */
class DiaryEditableListEditService extends DBBaseService {

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

        $itemId = 'diary_datetime';
        $itemName = '日付';
        $validation
                ->oneOfTheFollowing()
                ->validateRequired($errors, $itemId, $itemName, $data[$itemId])
                ->validateDateTime($errors, $itemId, $itemName, $data[$itemId], [DateUtil::DATETIME_YMD_FORMAT_COMMON])
                ->end()
        ;

        $itemId = 'title';
        $itemName = 'タイトル';
        $validation
                ->oneOfTheFollowing()
                ->validateRequired($errors, $itemId, $itemName, $data[$itemId])
                ->validateLength($errors, $itemId, $itemName, $data[$itemId], 100)
                ->end()
        ;

        $itemId = 'content';
        $itemName = '内容';
        $validation
                ->oneOfTheFollowing()
                ->validateRequired($errors, $itemId, $itemName, $data[$itemId])
                ->validateLength($errors, $itemId, $itemName, $data[$itemId], 1000)
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

                $diaryDao = new DiaryDao($dbConnection);

                // レコードの存在チェックを実施する
                $selectRecord = null;
                if (!ValUtil::isEmpty($data['diary_id'])) {
                    // ID値が設定されている場合、レコードを取得する
                    $selectRecord = $diaryDao->selectByDiaryId($user->user_id, $data['diary_id'], true);
                    if ($selectRecord === null) {
                        // レコードが存在しない場合
                        $error = Validatation::createError(
                                        'error_data_not_found'
                                        , $this->errorMessage->get('error_data_not_found', ['%itemName%' => '日記', '%id%' => $data['diary_id']]));
                        throw new ServiceException([$error]);
                    }
                }

                if ($selectRecord === null) {
                    // レコードが存在しない場合、新規登録する
                    $saveData = $this->saveForInsert($diaryDao, $data, $user);
                } else {
                    // 後勝ちでOKとする
                    // レコードが存在する場合、更新する
                    $saveData = $this->saveForUpdate($diaryDao, $data, $user);
                }
            });
        } catch (DBException $ex) {

            if (DBFactory::createDBHelper()->isErrorForLock($ex)) {
                // ロックエラー
                $error = Validatation::createError('error_data_lock_timeout', $this->errorMessage->get('error_data_already_update', ['%itemName%' => '日記']));
                throw new ServiceException([$error]);
            } else if (DBFactory::createDBHelper()->isErrorForTimeout($ex)) {
                // タイムアウトエラー
                $error = Validatation::createError('error_data_query_timeout', $this->errorMessage->get('error_data_query_timeout', ['%itemName%' => '日記']));
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

                $diaryDao = new DiaryDao($dbConnection);

                // レコードの存在チェックを実施する
                $selectRecord = $diaryDao->selectByDiaryId($user->user_id, $data['diary_id'], true);

                if ($selectRecord !== null) {
                    // レコードが存在する場合、削除する
                    // 後勝ちでOKとする
                    $this->saveForDelete($diaryDao, $data, $user);
                }
            });
        } catch (DBException $ex) {

            if (DBFactory::createDBHelper()->isErrorForLock($ex)) {
                // ロックエラー
                $error = Validatation::createError('error_data_lock_timeout', $this->errorMessage->get('error_data_already_update', ['%itemName%' => '日記']));
                throw new ServiceException([$error]);
            } else if (DBFactory::createDBHelper()->isErrorForTimeout($ex)) {
                // タイムアウトエラー
                $error = Validatation::createError('error_data_query_timeout', $this->errorMessage->get('error_data_already_update', ['%itemName%' => '日記']));
                throw new ServiceException([$error]);
            }

            throw $ex;
        }
    }

    /**
     * 登録処理。
     * @param DiaryDao $diaryDao 日記 DAO
     * @param array $data データ
     * @param User $user ユーザー情報
     * @return array レコード
     */
    private function saveForInsert(DiaryDao $diaryDao, array $data, User $user = null): array {

        // 登録クエリ発行
        $data['user_id'] = $user->user_id;
        $data['create_datetime'] = $this->systemDate->format(DateUtil::DATETIME_HYPHEN_MICRO_FORMAT_COMMON);
        $data['create_user_id'] = $user->user_id ?? 0;
        $data['update_datetime'] = $this->systemDate->format(DateUtil::DATETIME_HYPHEN_MICRO_FORMAT_COMMON);
        $data['update_user_id'] = $user->user_id ?? 0;

        $data['diary_id'] = $diaryDao->insert($data);

        return $data;
    }

    /**
     * 更新処理。
     * @param DiaryDao $diaryDao 日記 DAO
     * @param array $data データ
     * @param User $user ユーザー情報
     * @return array レコード
     */
    private function saveForUpdate(DiaryDao $diaryDao, array $data, User $user = null): array {

        // 更新クエリ発行
        $data['user_id'] = $user->user_id;
        $data['update_datetime'] = $this->systemDate->format(DateUtil::DATETIME_HYPHEN_MICRO_FORMAT_COMMON);
        $data['update_user_id'] = $user->user_id ?? 0;
        $diaryDao->update($data, $user->user_id, $data['diary_id']);

        return $data;
    }

    /**
     * 削除処理。
     * @param DiaryDao $diaryDao 日記 DAO
     * @param array $data データ
     * @param User $user ユーザー情報
     */
    private function saveForDelete(DiaryDao $diaryDao, array $data, User $user = null) {

        // 削除クエリ発行
        $diaryDao->delete($user->user_id, $data['diary_id']);
    }

}
