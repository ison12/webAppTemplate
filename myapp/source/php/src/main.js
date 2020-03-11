// -----------------------------------------------------------------------------
// ライブラリのインポート
// -----------------------------------------------------------------------------
// JQuery
import $ from 'jquery';
// Bootstrap
import "bootstrap/dist/css/bootstrap.min.css";
import "bootstrap";

import "open-iconic/font/css/open-iconic-bootstrap.css";

$.fn.bstooltip = $.fn.tooltip;

// JQuery UI
import "jquery-ui/themes/base/base.css";
import "jquery-ui/themes/base/theme.css";
import "jquery-ui/ui/widgets/datepicker";
import "jquery-ui/ui/widgets/tooltip";

// VueJs
import Vue from 'vue';
import Vuex from 'vuex';

Vue.use(Vuex);

// アプリケーションCSS
import "main.css";

// -----------------------------------------------------------------------------
// エラーハンドラ設定
// -----------------------------------------------------------------------------
import ErrorHandler from "Front/Common/Error/ErrorHandler.js";
const errorHandler = new ErrorHandler(null, null);

// -----------------------------------------------------------------------------
// 共通で使用するコンポーネント
// -----------------------------------------------------------------------------
import AppComponent from 'Front/View/App/App.vue';
Vue.component("AppComponent", AppComponent);

// -----------------------------------------------------------------------------
// 機能別コンポーネント
// -----------------------------------------------------------------------------
import PhpInfoComponent from 'Func/Debug/Front/View/PhpInfo.vue';
Vue.component("PhpInfoComponent", PhpInfoComponent);

import LoginComponent from 'Func/Login/Front/View/Login.vue';
Vue.component("LoginComponent", LoginComponent);

import PasswordChangeRequestComponent from 'Func/Password/Front/View/PasswordChangeRequest.vue';
Vue.component("PasswordChangeRequestComponent", PasswordChangeRequestComponent);

import PasswordChangeComponent from 'Func/Password/Front/View/PasswordChange.vue';
Vue.component("PasswordChangeComponent", PasswordChangeComponent);

import UserRegistRequestComponent from 'Func/User/Front/View/UserRegistRequest.vue';
Vue.component("UserRegistRequestComponent", UserRegistRequestComponent);

import UserRegistComponent from 'Func/User/Front/View/UserRegist.vue';
Vue.component("UserRegistComponent", UserRegistComponent);

import TopComponent from 'Func/Top/Front/View/Top.vue';
Vue.component("TopComponent", TopComponent);

import SystemSettingSearchComponent from 'Func/SystemSetting/Front/View/SystemSettingSearch.vue';
Vue.component("SystemSettingSearchComponent", SystemSettingSearchComponent);

import SystemSettingEditComponent from 'Func/SystemSetting/Front/View/SystemSettingEdit.vue';
Vue.component("SystemSettingEditComponent", SystemSettingEditComponent);

import DiaryEditableListComponent from 'Func/Diary/Front/View/DiaryEditableList.vue';
Vue.component("DiaryEditableListComponent", DiaryEditableListComponent);

// -----------------------------------------------------------------------------
// Vuex Store定義
// -----------------------------------------------------------------------------
const store = new Vuex.Store({
    state: {
        // ログインユーザー
        loginUser: {}
    },
    mutations: {
        /**
         * ログインユーザーの設定
         * @param {Vuex.Store} state State
         * @param {Object} user ユーザー情報
         */
        loginUser(state, user) {
            // 状態を変更する
            state.loginUser = user;
        }
    },
    getters: {
        /**
         * ログインユーザーの取得
         * @param {Vuex.Store} state State
         * @returns {Object} ユーザー情報
         */
        loginUser(state) {
            return state.loginUser;
        }
    }
});

// -----------------------------------------------------------------------------
// ルートVueJsインスタンスを生成
// -----------------------------------------------------------------------------
const vm = new Vue({
    el: '#app',
    store, // Vuexを利用する
    render: h => h("AppComponent")
});


// -----------------------------------------------------------------------------
// Ajaxインスタンスの定義
// -----------------------------------------------------------------------------
import AppAjax from "Front/Common/Ajax/AppAjax.js";
let ajax = null;

/**
 * アプリケーションの関数定義
 */
var AppFuncs = {

    /**
     * コンテントコンポーネントの設定
     * @param {String} componentId コンポーネントID
     * @param {Boolean} isVisibleHeader ヘッダ表示有無
     * @param {Boolean} isVisibleFooter フッタ表示有無
     */
    applyContentComponent(componentId, isVisibleHeader, isVisibleFooter) {

        vm.$nextTick(function () {

            // $root要素の子はAppComponent（refs設定がないので$children[0]で決め打ちする）
            var appComponent = vm.$root.$children[0];

            // Ajaxオブジェクトを初期化する
            AppFuncs.getAjax();

            // AppComponentのdataを設定する
            appComponent.componentId = componentId;
            appComponent.isVisibleHeader = isVisibleHeader;
            appComponent.isVisibleFooter = isVisibleFooter;
        });
    },

    /**
     * アプリケーション用Ajaxオブジェクトを取得する。
     * 初回取得時に生成されたインスタンスが以後キャッシュされる。
     * @returns {AppAjax} アプリケーション用Ajax
     */
    getAjax() {

        if (ajax) {
            return ajax;
        }

        // $root要素の子はAppComponent（refs設定がないので$children[0]で決め打ちする）
        var appComponent = vm.$root.$children[0];
        var alertComponent = appComponent.$refs.alert;
        var confirmComponent = appComponent.$refs.confirm;
        var loadingComponent = appComponent.$refs.loading;

        /**
         * ロード時の処理
         * @param {Boolean} isShow true 表示時、false 非表示時
         */
        const onLoading = (isShow) => {

            const lc = loadingComponent;

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
            vm.$store.commit("loginUser", data.user);
        };

        /**
         * 通信失敗時の処理。
         * @param {Object} jqXHR
         * @param {String} textStatus
         * @param {Object} errorThrown
         * @param {Object} settings
         */
        const onFail = (jqXHR, textStatus, errorThrown, settings) => {

            const getErrorMessage = function (url, jqXHR, textStatus, errorThrown) {

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
                        errorThrown ? "例外=" + errorThrown : null,
                        jqXHR.responseText];
                }

            };

            const ac = alertComponent;
            ac.showError("通信に失敗しました。", getErrorMessage(settings.url, jqXHR, textStatus, errorThrown));
        };

        /**
         * 処理終了時の処理。
         * @param {Object|Array} dataOrJqXHR
         * @param {String} textStatus
         * @param {Object} jqXHROrErrorThrown
         */
        const onAlways = (dataOrJqXHR, textStatus, jqXHROrErrorThrown) => {

        };

        ajax = new AppAjax(onLoading, onDone, onFail, onAlways);
        return ajax;
    }

};

// -----------------------------------------------------------------------------
// グローバル変数の定義
// -----------------------------------------------------------------------------
window.$ = $; // jQueryオブジェクト
window.AppFuncs = AppFuncs;
