/* global AppContext */

import VueJsCommonMixin from "Front/Common/VueJsMixin/VueJsCommonMixin.js";
import VueJsInputErrorMixin from "Front/Common/VueJsMixin/VueJsInputErrorMixin.js";

import DatePicker from "Front/View/DatePicker/DatePicker.vue";
import ErrorList from "Front/View/Error/ErrorList.vue";
import InputErrorView from "Front/View/InputErrorView/InputErrorView.vue";

export default {
    // ----------------------------------------------------
    // Mixin
    // ----------------------------------------------------
    mixins: [
        VueJsCommonMixin,
        VueJsInputErrorMixin
    ],
    // ----------------------------------------------------
    // ローカルコンポーネント
    // ----------------------------------------------------
    components: {
        'DatePickerComponent': DatePicker,
        'ErrorListComponent': ErrorList,
        'InputErrorViewComponent': InputErrorView
    },
    // ----------------------------------------------------
    // プロパティ
    // ----------------------------------------------------
    props: {
    },
    // ----------------------------------------------------
    // データ
    // ----------------------------------------------------
    data() {
        return {
            // 新規作成有無
            isNew: false,
            // 編集有無
            isEditStarting: false,
            // 閉じるボタンクリック中のフラグ
            clickingClose: false,
            // データ
            data: {
            }
        };
    },
    watch: {
    },
    // ----------------------------------------------------
    // ライフサイクルフック
    // ----------------------------------------------------
    created() {
    },
    mounted() {
    },
    // ----------------------------------------------------
    // メソッド
    // ----------------------------------------------------
    methods: {
        /**
         * 表示処理。
         * @param {Object} data データ
         */
        show(data) {

            var dialog = $(this.$el);

            if (!data.diary_id) {
                this.isNew = true;
                this.isEditStarting = true;
            } else {
                this.isNew = false;
                this.isEditStarting = false;
            }

            this.data = $.extend(true, {}, data);

            this.errors = [];
            this.errorsOnBoard = [];

            // 非表示イベントの定義
            dialog.off('hidden.bs.modal');
            dialog.on('hidden.bs.modal', function (e) {
                // 非表示時に実施したい処理を記述
            });

            this.$nextTick(function () {
                // DOM 更新後にダイアログを表示することで一時的なちらつきをなくす
                dialog.modal('show');
            });
        },
        /**
         * 非表示処理
         */
        hide() {
            var dialog = $(this.$el);
            dialog.modal('hide');
        },
        /**
         * 保存処理。
         * @param {Function} onSuccess 正常終了時のコールバック関数
         */
        processSave(onSuccess) {

            this.getAjax().ajax({
                context: this,
                url: AppContext.baseUrl + "/diary/editableList/save",
                data: {
                    condition: this.condition,
                    data: this.data
                }
            }).then(function (data) {

                // VueJsの更新
                this.bindData(data);

                if (!data.errors || data.errors.length <= 0) {
                    if (onSuccess) {
                        onSuccess();
                    }
                }

            }).catch(function () {

            });
        },
        /**
         * 削除処理。
         * @param {Function} onSuccess 正常終了時のコールバック関数
         */
        processDelete(onSuccess) {

            // 日記削除処理
            const diaryDeleteProcess = () => {

                this.getAjax().ajax({
                    context: this,
                    url: AppContext.baseUrl + "/diary/editableList/delete",
                    data: {
                        condition: this.condition,
                        data: this.data
                    }
                }).then(function (data) {

                    // VueJsの更新
                    this.bindData(data);

                    if (!data.errors || data.errors.length <= 0) {
                        if (onSuccess) {
                            onSuccess();
                        }
                    }

                }).catch(function () {

                });

            };

            // 確認メッセージ
            this.getConfirmComponent().showWarn(
                    "削除確認",
                    `${this.data.diary_datetime}\n『${this.data.title}』 を削除しますがよろしいですか？`,
                    {
                        positive: {
                            text: "削除する",
                            onClick() {
                                diaryDeleteProcess();
                                return true;
                            },
                            onCloseComplete() { }
                        },
                        negative: {
                            text: "キャンセル",
                            onClick() {
                                return true;
                            },
                            onCloseComplete() { }
                        }
                    });

        },
        /**
         * 行のデータバインド処理。
         * @param {Object} data データ
         */
        bindData(data) {
            this.errors = data.errors;
            this.$nextTick(() => {
                this.errorsOnBoard = this.createErrorsOnBoard(this.errors);
            });
        },
        /**
         * 編集開始
         */
        onClickEditStart() {
            this.isEditStarting = true;
        },
        /**
         * 編集終了
         */
        onClickEditEnd() {
            this.isEditStarting = false;
        },
        /**
         * 保存処理
         */
        onClickSave() {

            this.processSave(() => {
                this.isEditStarting = false;
                this.$emit("onClose", true /* 再表示有無 */);
                this.hide();
            });
        },
        /**
         * 削除処理
         */
        onClickDelete() {

            this.processDelete(() => {
                this.isEditStarting = false;
                this.$emit("onClose", true /* 再表示有無 */);
                this.hide();
            });
        },
        /**
         * キャンセル処理
         */
        onClickCancel() {
            this.isEditStarting = false;
            this.$emit("onClose");
            this.hide();
        }
    },
    // ----------------------------------------------------
    // 計算項目
    // ----------------------------------------------------
    computed: {
    }
}
