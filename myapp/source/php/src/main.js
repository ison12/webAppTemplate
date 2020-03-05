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

import TopComponent from 'Func/Top/Front/View/Top.vue';
Vue.component("TopComponent", TopComponent);

import SystemSettingSearchComponent from 'Func/SystemSetting/Front/View/SystemSettingSearch.vue';
Vue.component("SystemSettingSearchComponent", SystemSettingSearchComponent);

import SystemSettingEditComponent from 'Func/SystemSetting/Front/View/SystemSettingEdit.vue';
Vue.component("SystemSettingEditComponent", SystemSettingEditComponent);

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
    applyContent(componentId, isVisibleHeader, isVisibleFooter) {

        vm.$nextTick(function () {

            // $root要素の子はAppComponent（refs設定がないので$children[0]で決め打ちする）
            var appComponent = vm.$root.$children[0];

            // AppComponentのdataを設定する
            appComponent.componentId = componentId;
            appComponent.isVisibleHeader = isVisibleHeader;
            appComponent.isVisibleFooter = isVisibleFooter;
        });
    }

};

// -----------------------------------------------------------------------------
// グローバル変数の定義
// -----------------------------------------------------------------------------
window.$ = $; // jQueryオブジェクト
window.AppFuncs = AppFuncs;
