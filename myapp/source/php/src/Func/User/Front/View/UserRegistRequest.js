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
            data: {},
            // ユーザー登録リクエスト処理の完了
            isRegistRequestComplete: false
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
                url: AppContext.baseUrl + "/user/registRequest/load",
                data: AppContext.requestParams
            }).then(function (data) {

                // データをバインドする
                this.bindData(data);

            }).catch(function () {

            });
        },
        /**
         * 仮登録処理。
         */
        processExec() {

            this.getAjax().ajax({
                context: this,
                url: AppContext.baseUrl + "/user/registRequest/exec",
                data: {data: this.data}
            }).then(function (data) {

                this.bindDataForErrorOnly(data);

                if (data.errors.length <= 0) {
                    // 正常時
                    // 完了情報を表示
                    this.isRegistRequestComplete = true;
                }

            }).catch(function () {

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
