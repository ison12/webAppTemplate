/* global AppContext */

import VueJsCommonMixin from "Front/Common/VueJsMixin/VueJsCommonMixin.js";
import VueJsInputErrorMixin from "Front/Common/VueJsMixin/VueJsInputErrorMixin.js";
import DatePicker from "Front/View/DatePicker/DatePicker.vue";
import InputErrorView from "Front/View/InputErrorView/InputErrorView.vue";

import ErrorList from "Front/View/Error/ErrorList.vue";

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
            isSucceededInLoad: false, // ロード成否（ロードに失敗した場合は、画面を非表示のままにする。意図せぬ操作によるエラーの副作用を防ぐため）
            isNew: null,
            data: {}
        };
    },
    // ----------------------------------------------------
    // ライフサイクルフック
    // ----------------------------------------------------
    created() {
    },
    mounted() {
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
                url: AppContext.baseUrl + "/systemSetting/edit/load",
                data: AppContext.requestParams
            }).then(function (data) {

                // VueJsの初期化
                this.bindData(data);

            }).catch(function () {

            });
        },
        /**
         * 保存処理。
         */
        processSave() {

            this.getAjax().ajax({
                context: this,
                url: AppContext.baseUrl + "/systemSetting/edit/save",
                data: {data: this.data}
            }).then(function (data) {

                this.bindDataForErrorOnly(data);

                if (data.errors.length <= 0) {
                    // 正常時
                    // 検索画面へ移動する
                    window.location.href = AppContext.baseUrl + "/systemSetting/search?previousSearch=true&selectedId=" + data.data.id;
                }

            }).catch(function () {

            });
        },
        /**
         * 削除処理。
         */
        processDelete() {

            this.getAjax().ajax({
                context: this,
                url: AppContext.baseUrl + "/systemSetting/edit/delete",
                data: {data: this.data}
            }).then(function (data) {

                this.bindDataForErrorOnly(data);

                if (data.errors.length <= 0) {
                    // 正常時
                    // 検索画面へ移動する
                    window.location.href = AppContext.baseUrl + "/systemSetting/search?previousSearch=true";
                }

            }).catch(function () {

            });
        },
        /**
         * データバインド処理。
         * @param {Object} data データ
         */
        bindData(data) {
            this.isSucceededInLoad = data.errors.length <= 0;
            this.isNew = data.isNew;
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
         * 保存処理。
         */
        onSave() {
            this.processSave();
        },
        /**
         * 削除処理。
         */
        onDelete() {
            this.processDelete();
        }
    },
    // ----------------------------------------------------
    // 計算項目
    // ----------------------------------------------------
    computed: {
    }
}
