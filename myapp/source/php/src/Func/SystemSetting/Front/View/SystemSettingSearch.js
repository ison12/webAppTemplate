/* global AppContext */

import VueJsCommonMixin from "Front/Common/VueJsMixin/VueJsCommonMixin.js";
import VueJsPagerMixin from "Front/Common/VueJsMixin/VueJsPagerMixin.js";
import DatePicker from "Front/View/DatePicker/DatePicker.vue";

import ErrorList from "Front/View/Error/ErrorList.vue";
import Pager from "Front/View/Pager/Pager.vue";

export default {
    // ----------------------------------------------------
    // Mixin
    // ----------------------------------------------------
    mixins: [
        VueJsCommonMixin,
        VueJsPagerMixin
    ],
    // ----------------------------------------------------
    // ローカルコンポーネント
    // ----------------------------------------------------
    components: {
        'DatePickerComponent': DatePicker,
        'ErrorListComponent': ErrorList,
        'PagerComponent': Pager
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
            // 検索条件
            condition: {},
            // ページ情報
            page: {},
            // 検索結果リスト
            list: {},
            previousSearch: null,
            // 選択行ID
            selectedId: null,
            // エラー情報
            errors: [],
            errorsOnBoard: []
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
        // 初期化処理
        processInit() {

        },
        /**
         * ロード処理。
         */
        processLoad() {

            this.getAjax().ajax({
                context: this,
                url: AppContext.baseUrl + "/systemSetting/search/load",
                data: AppContext.requestParams
            }).then(function (data) {

                // VueJsの更新
                this.bindData(data);

            }).catch(function () {

            });
        },
        /**
         * 検索処理。
         */
        processSearch() {

            this.getAjax().ajax({
                context: this,
                url: AppContext.baseUrl + "/systemSetting/search/search",
                data: {
                    condition: this.condition,
                    page: this.page
                }
            }).then(function (data) {

                // VueJsの更新
                this.bindData(data);

            }).catch(function () {

            });

        },
        /**
         * データバインド処理。
         * @param {Object} data データ
         */
        bindData(data) {
            this.condition = data.condition;
            this.page = data.page;
            this.list = data.list;
            this.previousSearch = data.previousSearch;
            this.selectedId = data.selectedId;
            this.data = data.data;
            this.errors = data.errors;
        },
        // ------------------------------------
        // イベントの定義
        // ------------------------------------
        /**
         * 検索処理。
         */
        onSearch() {
            this.page.currentPage = 1;
            this.processSearch();
        },
        /**
         * ページ番号による検索処理（ページ移動）。
         * @argument {Object} page ページ情報
         */
        onSearchByPager(page) {
            // ページを計算する
            this.page.currentPage = this.calcPageNum(
                    Number(this.page.currentPage),
                    Number(this.page.pageSize),
                    page.pageNum);

            this.processSearch();
        }
    },
    // ----------------------------------------------------
    // 計算項目
    // ----------------------------------------------------
    computed: {
    }
}
