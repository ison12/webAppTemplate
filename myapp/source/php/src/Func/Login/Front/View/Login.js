/* global AppContext */

import VueJsCommonMixin from "Front/Common/VueJsMixin/VueJsCommonMixin.js";
import VueJsInputErrorMixin from "Front/Common/VueJsMixin/VueJsInputErrorMixin.js";
import InputErrorView from "Front/View/InputErrorView/InputErrorView.vue";

import ErrorList from "Front/View/Error/ErrorList.vue";

export default {
    // ----------------------------------------------------
    // Mixin
    // ----------------------------------------------------
    mixins: [VueJsCommonMixin, VueJsInputErrorMixin],
    // ----------------------------------------------------
    // ローカルコンポーネント
    // ----------------------------------------------------
    components: {
        'InputErrorViewComponent': InputErrorView,
        'ErrorListComponent': ErrorList
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
            // データ
            data: {}
        };
    },
    // ----------------------------------------------------
    // ライフサイクルフック
    // ----------------------------------------------------
    created() {
    },
    mounted() {
        // ページロード時の処理
        this.processLoad();
    },
    // ----------------------------------------------------
    // メソッド
    // ----------------------------------------------------
    methods: {
        /**
         * ロード処理。
         */
        processLoad() {

            this.getAjax().ajax({
                context: this,
                url: AppContext.baseUrl + "/login/load",
                data: AppContext.requestParams
            }).done(function (data, textStatus, jqXHR) {

                var $el = $(this.$el);

                // ブラウザの自動補完により入力値が既に設定されている場合があるため、DOM要素を取得する
                var $userAccount = $el.find("[name=user_account]");
                var $password = $el.find("[name=password]");

                if (this.isEmpty(data.data.user_account)) {
                    // デフォルト値がない場合
                    // ブラウザによる入力補完を適用する
                    data.data.user_account = $userAccount.val();
                    data.data.password = $password.val();
                } else {
                    // デフォルト値がある場合
                    if (data.data.user_account === $userAccount.val()) {
                        // ブラウザによる入力補完のアカウントとデフォルト値が同じならブラウザによる入力補完を採用する
                        data.data.user_account = $userAccount.val();
                        data.data.password = $password.val();
                    }
                }

                // データをバインドする
                this.bindData(data);

            }).fail(() => {

            });
        },
        /**
         * ログイン処理。
         */
        processExec() {

            var self = this;

            this.getAjax().ajax({
                context: this,
                url: AppContext.baseUrl + "/login/exec",
                data: {data: this.data}
            }).done(function (data) {

                this.bindDataForErrorOnly(data);

                if (data.errors.length <= 0) {
                    // 正常時
                    // 検索画面へ移動する
                    window.location.href = AppContext.baseUrl + "/top";
                }

            }).fail(() => {

            });
        },
        /**
         * データバインド処理。
         * @param {Object} data データ
         */
        bindData(data) {
            this.data = data.data;
            this.errors = data.errors;
            this.$nextTick(() => {
                this.errorsOnBoard = this.createErrorsOnBoard(this.errors);
            });
        },
        /**
         * データバインド処理。
         * @param {Object} data データ
         */
        bindDataForErrorOnly(data) {
            this.errors = data.errors;
            this.$nextTick(() => {
                this.errorsOnBoard = this.createErrorsOnBoard(this.errors);
            });
        },
        // ------------------------------------
        // イベントの定義
        // ------------------------------------
        /**
         * 実行処理。
         */
        onExec() {
            this.processExec();
        }
    },
    // ----------------------------------------------------
    // 計算項目
    // ----------------------------------------------------
    computed: {
    }
}
