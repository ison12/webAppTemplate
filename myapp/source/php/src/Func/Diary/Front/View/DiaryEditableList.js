/* global AppContext */

import VueJsCommonMixin from "Front/Common/VueJsMixin/VueJsCommonMixin.js";
import VueJsInputErrorMixin from "Front/Common/VueJsMixin/VueJsInputErrorMixin.js";

import ErrorList from "Front/View/Error/ErrorList.vue";
import InputErrorView from "Front/View/InputErrorView/InputErrorView.vue";
import DiaryEdit from "Func/Diary/Front/View/DiaryEdit.vue";

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
        'ErrorListComponent': ErrorList,
        'InputErrorViewComponent': InputErrorView,
        'DiaryEditComponent': DiaryEdit
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
            // ロード有無
            isLoad: false,
            // 検索条件
            condition: {},
            // 検索結果リスト
            list: {},
            // 年月情報
            diaryYearMonthInfo: {}
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
                url: AppContext.baseUrl + "/diary/editableList/load",
                data: AppContext.requestParams
            }).then(function (data) {

                this.isLoad = true;
                // VueJsの更新
                this.bindDataForSearch(data);

            }).catch(function () {

            });
        },
        /**
         * 検索処理。
         */
        processSearch() {

            this.getAjax().ajax({
                context: this,
                url: AppContext.baseUrl + "/diary/editableList/search",
                data: {
                    condition: this.condition
                }
            }).then(function (data) {

                // VueJsの更新
                this.bindDataForSearch(data);

            }).catch(function () {

            });

        },
        /**
         * データバインド処理。
         * @param {Object} data データ
         */
        bindDataForSearch(data) {

            this.condition = data.condition;
            this.list = data.list;
            this.diaryYearMonthInfo = data.diaryYearMonthInfo;
            this.data = data.data;
            this.errors = data.errors;
            this.$nextTick(() => {
                this.errorsOnBoard = this.createErrorsOnBoard(this.errors);
            });
        },
        // ------------------------------------
        // イベントの定義
        // ------------------------------------
        /**
         * 検索処理。
         * @param {String} year 年
         * @param {String} month 月
         */
        onSearch(year, month) {

            if (typeof year !== "undefined") {
                this.condition.diaryYear = year;
            }
            if (typeof month !== "undefined") {
                this.condition.diaryMonth = month;
            }

            this.processSearch();
        },
        /**
         * 前月の検索処理。
         */
        onSearchPrev() {

            const diaryMonthIndex = this.diaryMonthList.findIndex((element) => Number(element) === Number(this.condition.diaryMonth));
            if (diaryMonthIndex !== -1 && diaryMonthIndex - 1 >= 0) {

                // 前月の値を取得して検索する
                this.condition.diaryMonth = this.diaryMonthList[diaryMonthIndex - 1];
                this.processSearch();
            }
        },
        /**
         * 次月の検索処理。
         */
        onSearchNext() {

            const diaryMonthIndex = this.diaryMonthList.findIndex((element) => Number(element) === Number(this.condition.diaryMonth));
            if (diaryMonthIndex !== -1 && diaryMonthIndex + 1 < this.diaryMonthList.length) {

                // 次月の値を取得して検索する
                this.condition.diaryMonth = this.diaryMonthList[diaryMonthIndex + 1];
                this.processSearch();
            }
        },
        /**
         * 年変更時の処理。
         */
        onChangeDiaryYear() {

            if (this.diaryMonthList.length > 0) {
                this.condition.diaryMonth = this.diaryMonthList[0];
                this.processSearch();
            }
        },
        /**
         * 行の表示処理。
         * @param {Object} rec 行情報
         */
        onViewRecord(rec) {

            if (rec === null) {
                rec = {
                    diary_id: null,
                    user_id: null,
                    diary_datetime: null,
                    title: null,
                    content: null,
                    create_datetime: null,
                    create_user_id: null,
                    update_datetime: null,
                    update_user_id: null
                };
            }

            this.$refs.diaryEditDialog.show(rec);
        },
        /**
         * 日記編集ダイアログの閉じるイベント。
         * @param {Boolean} isRefresh 再表示有無
         */
        onCloseDiaryEdit(isRefresh) {
            if (isRefresh) {
                this.onSearch();
            }
        }
    },
    // ----------------------------------------------------
    // 計算項目
    // ----------------------------------------------------
    computed: {
        /**
         * 日付の月リストの取得
         * @returns {Array} 日付の月リスト
         */
        diaryMonthList() {

            if (
                    this.diaryYearMonthInfo &&
                    this.diaryYearMonthInfo.diaryMonthListMappedByYear &&
                    typeof this.diaryYearMonthInfo.diaryMonthListMappedByYear[this.condition.diaryYear] !== "undefined"
                    ) {
                // 現在選択中の年に紐づく月リストを取得する
                return this.diaryYearMonthInfo.diaryMonthListMappedByYear[this.condition.diaryYear];
            }

            return [];
        }
    }
}
