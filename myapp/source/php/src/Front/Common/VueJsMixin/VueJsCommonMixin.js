/* global AppContext */

import StringUtil from "Front/Common/Util/StringUtil.js";
import DateUtil from "Front/Common/Util/DateUtil.js";
import AppAjax from "Front/Common/Ajax/AppAjax.js";

/**
 * VueJsの共通のMixin。
 */
export default {
    data: function () {
        return {
            // アプリケーションコンテキスト
            AppContext: AppContext,
            // Ajaxインスタンス
            ajax: null
        };
    },
    created: function () {
    },
    methods: {
        /**
         * nullまたはundefinedだったらデフォルト値を返す。
         * それ以外だったらStringにキャストして返す。
         * @param {String} value 値
         * @param {String} defaultVal デフォルト値
         * @returns {String} 値
         */
        castString(value, defaultVal) {
            return StringUtil.castString(value, defaultVal);
        },
        /**
         * 空かどうかをチェックする。
         * @param {type} value 値
         * @returns {Boolean} true 空、false 空ではない
         */
        isEmpty(value) {
            return StringUtil.isEmpty(value);
        },
        /**
         * 空ではないかをチェックする。
         * @param {type} value 値
         * @returns {Boolean} true 空ではない、false 空
         */
        isNotEmpty(value) {
            return StringUtil.isNotEmpty(value);
        },
        /**
         * AppComponentを取得。
         * @returns {AppComponent} AppComponent
         */
        getAppComponent() {
            return this.$root.$children[0];
        },
        /**
         * AlertComponentを取得。
         * @returns {AlertComponent} AlertComponent
         */
        getAlertComponent() {
            return this.getAppComponent().$refs.alert;
        },
        /**
         * ConfirmComponentを取得。
         * @returns {ConfirmComponent} ConfirmComponent
         */
        getConfirmComponent() {
            return this.getAppComponent().$refs.confirm;
        },
        /**
         * LoadingComponentを取得。
         * @returns {LoadingComponent} LoadingComponent
         */
        getLoadingComponent() {
            return this.getAppComponent().$refs.loading;
        },
        /**
         * Ajaxインスタンスを取得する。
         * @returns {AppAjax} Ajaxインスタンス
         */
        getAjax: function () {

            if (this.ajax) {
                return this.ajax;
            }

            /**
             * ロード時の処理
             * @param {Boolean} isShow true 表示時、false 非表示時
             */
            const onLoading = (isShow) => {

                const lc = this.getLoadingComponent();

                if (isShow) {
                    lc.show();
                } else {
                    lc.hide();
                }
            };

            /**
             * 通信成功時の処理。
             * @param {Object|Array} data
             * @param {String} textStatus
             * @param {Object} jqXHR
             */
            const onDone = (data, textStatus, jqXHR) => {
                this.$store.commit("loginUser", data.user);
            };

            /**
             * 通信失敗時の処理。
             * @param {Object} jqXHR
             * @param {String} textStatus
             * @param {Object} errorThrown
             * @param {Object} settings
             */
            const onFail = (jqXHR, textStatus, errorThrown, settings) => {
                const ac = this.getAlertComponent();
                ac.showError("通信に失敗しました。", this.collectErrorMessages(settings.url, jqXHR, textStatus, errorThrown));
            };

            /**
             * 処理終了時の処理。
             * @param {Object|Array} dataOrJqXHR
             * @param {String} textStatus
             * @param {Object} jqXHROrErrorThrown
             */
            const onAlways = (dataOrJqXHR, textStatus, jqXHROrErrorThrown) => {

            };

            this.ajax = new AppAjax(onLoading, onDone, onFail, onAlways);
            return this.ajax;
        },
        /**
         * エラーメッセージを取得する。
         * @param {String} url URL
         * @param {Object} XMLHttpRequest HttpRequest
         * @param {String} textStatus ステータス
         * @param {Object} errorThrown 例外オブジェクト
         * @returns {Array} エラーメッセージリスト
         */
        collectErrorMessages(url, XMLHttpRequest, textStatus, errorThrown) {

            const TIMEOUT_MESSAGE = 'タイムアウトしました。再度実行してください。 ';
            const EXPECTED_MESSAGE = '予期せぬエラーが発生しました。';

            // textStatusについて
            // -----------------------------------
            // "success"
            // "notmodified"
            // "error"
            // "timeout"
            // "abort"
            // "parsererror"

            if (textStatus === "timeout") {
                const msg = String(TIMEOUT_MESSAGE);
                return [msg,
                    "アクセスURL=" + url];
            } else {
                const msg = EXPECTED_MESSAGE;
                return [msg,
                    "アクセスURL=" + url,
                    "ステータス=" + textStatus,
                    errorThrown && errorThrown.message ? "例外=" + errorThrown.message : null,
                    XMLHttpRequest.responseText];
            }

        },
        /**
         * 改行コード値をbrタグに変換する処理。
         * @param {String} val 値
         * @returns {String} 変換後文字列
         */
        convertNewLineToBr: function (val) {
            return StringUtil.convertNewLineToBr(val);
        },
        // エラー情報の更新
        updateErrorInfo: function (view) {
            // （エラーが有る場合は）エラー情報の更新

            //var errorsOnBoard = InputError.bindTooltip(this.errors, $(view).find("label,input,select"));
            //return errorsOnBoard;

            return [];
        }
    },
    filters: {
        formatDateYmd: function (value) {

            if (!value) {
                return "";
            }

            var dateObj = DateUtil.parseYmdHmsF(value);
            var dateStr = DateUtil.toString(dateObj, 'yyyy/mm/dd');

            return dateStr;
        },
        formatDateYmdHns: function (value) {

            if (!value) {
                return "";
            }

            var dateObj = DateUtil.parseYmdHmsF(value);
            var dateStr = DateUtil.toString(dateObj, 'yyyy/mm/dd hh:nn:ss');

            return dateStr;
        },
        formatDatemdHn: function (value) {

            if (!value) {
                return "";
            }

            var dateObj = DateUtil.parseYmdHmsF(value);
            var dateStr = DateUtil.toString(dateObj, 'mm/dd hh:nn');

            return dateStr;
        }
    }
}
