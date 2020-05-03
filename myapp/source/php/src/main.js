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
// ルートVueJsインスタンス定義
// -----------------------------------------------------------------------------
var vm = null;

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
     * VuexStoreを生成する。
     * @returns {Vuex.Store} VuexStore
     */
    createVmStore() {

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

        return store;
    },

    /**
     * VueJsインスタンスを生成する。
     * @returns {Vue} VueJsインスタンス
     */
    createVm() {

        const store = AppFuncs.createVmStore();

        const vm = new Vue({
            el: '#app',
            store, // Vuexを利用する
            render: h => h("AppComponent")
        });

        return vm;

    },

    /**
     * コンテントコンポーネントの設定
     * @param {String} componentPath コンポーネントパス
     * @param {String} componentId コンポーネントID
     * @param {Boolean} isVisibleHeader ヘッダ表示有無
     * @param {Boolean} isVisibleFooter フッタ表示有無
     */
    applyContentComponent(componentPath, componentId, isVisibleHeader, isVisibleFooter) {

        // 動的インポートを実施
        //
        // componentPathを以下のルールで変換
        // 例）ログインの場合
        //    /Func/Login/Front/View/Login
        //     ↓
        //   ./Func/Login/Front/View/Login.vue
        const componentLoadPromise = import("./" + componentPath.replace(/^\//, "") + ".vue");

        componentLoadPromise.then(function (value) {
            // 動的インポート完了

            // VueのComponentのグローバル登録は、Vueインスタンス生成前に実施する必要あり
            Vue.component(componentId, value.default /* import関数の戻り値に default があるので、そちらを使用する（defaultエクスポート定義を読み込むという意味になる） */);
            // Vueインスタンスの生成
            vm = AppFuncs.createVm();

            // レンダリングを実行
            vm.$nextTick(function () {

                // $root要素の子はAppComponent（refs設定がないので$children[0]で決め打ちする）
                const appComponent = vm.$root.$children[0];

                // Ajaxオブジェクトを初期化する
                AppFuncs.getAjax();

                // AppComponentを初期化する
                appComponent.componentId = componentId;
                appComponent.isVisibleHeader = isVisibleHeader;
                appComponent.isVisibleFooter = isVisibleFooter;
            });
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

                    if (jqXHR.responseJSON) {
                        var errorObj = jqXHR.responseJSON;
                        if (
                                errorObj.type &&
                                errorObj.summary &&
                                errorObj.message) {
                            return [errorObj.summary,
                                errorObj.message];
                        }
                    }

                    let body = jqXHR.responseText;
                    if (jqXHR.responseJSON) {

                        var errorObj = jqXHR.responseJSON;
                        if (
                                errorObj.type &&
                                errorObj.summary &&
                                errorObj.message) {
                            return [errorObj.summary,
                                "アクセスURL=" + url,
                                errorObj.message];
                            return;
                        }

                        body = JSON.stringify(jqXHR.responseJSON, null, '  ');
                    }

                    const msg = EXPECTED_MESSAGE;
                    return [msg,
                        "アクセスURL=" + url,
                        "ステータス=" + textStatus,
                        errorThrown ? "例外=" + errorThrown : null,
                        body];
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
