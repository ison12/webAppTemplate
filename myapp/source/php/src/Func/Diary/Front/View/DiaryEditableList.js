/* global AppContext */

import VueJsCommonMixin from "Front/Common/VueJsMixin/VueJsCommonMixin.js";
import VueJsInputErrorMixin from "Front/Common/VueJsMixin/VueJsInputErrorMixin.js";
import DatePicker from "Front/View/DatePicker/DatePicker.vue";

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
            // 検索条件
            condition: {},
            // 検索結果リスト
            list: {},
            // 年月情報
            diaryYearMonthInfo: {},
            // 対象行のインデックス
            targetRowIndex: 0
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
         * 保存処理。
         * @param {Object} rec 行情報
         * @param {Number} index インデックス
         */
        processSave(rec, index) {

            this.getAjax().ajax({
                context: this,
                url: AppContext.baseUrl + "/diary/editableList/save",
                data: {
                    condition: this.condition,
                    data: rec,
                    index: index
                }
            }).then(function (data) {

                // VueJsの更新
                this.bindDataForRow(data);

                if (!data.errors || data.errors.length <= 0) {
                    // 正常時
                    const index = this.list.findIndex(item => item.diary_id === rec.diary_id);
                    if (index !== -1) {
                        data.data.isChange = false;
                        this.list[index] = data.data;
                    }
                } else {
                    // エラー発生時
                    this.targetRowIndex = index;
                }

            }).catch(function () {

            });
        },
        /**
         * 削除処理。
         * @param {Object} rec 行情報
         * @param {Number} index インデックス
         */
        processDelete(rec, index) {

            // 日記削除処理
            const diaryDeleteProcess = () => {

                this.getAjax().ajax({
                    context: this,
                    url: AppContext.baseUrl + "/diary/editableList/delete",
                    data: {
                        condition: this.condition,
                        data: rec,
                        index: index
                    }
                }).then(function (data) {

                    // VueJsの更新
                    this.bindDataForRow(data);

                    if (!data.errors || data.errors.length <= 0) {
                        // 正常時
                        const index = this.list.findIndex(item => item.diary_id === rec.diary_id);
                        if (index !== -1) {
                            rec.isChange = false;
                            this.list.splice(index, 1);
                        }
                    } else {
                        // エラー発生時
                        this.targetRowIndex = index;
                    }

                }).catch(function () {

                });

            };

            // 確認メッセージ
            this.getConfirmComponent().showWarn(
                    "削除確認",
                    `${rec.diary_datetime} 『${rec.title}』 を削除しますがよろしいですか？`,
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
                            text: "やっぱり削除しません",
                            onClick() {
                                return true;
                            },
                            onCloseComplete() { }
                        }
                    });

        },
        /**
         * データバインド処理。
         * @param {Object} data データ
         */
        bindDataForSearch(data) {

            // 検索結果の各行を未変更状態にする（ここでプロパティを追加することでリアクティブ対象になる）
            for (let i = 0; i < data.list.length; i++) {
                if (data.list[i]) {
                    data.list[i].isChange = false;
                }
            }

            this.condition = data.condition;
            this.list = data.list;
            this.diaryYearMonthInfo = data.diaryYearMonthInfo;
            this.data = data.data;
            this.errors = data.errors;
            this.$nextTick(() => {
                this.errorsOnBoard = this.createErrorsOnBoard(this.errors);
            });
        },
        /**
         * 行のデータバインド処理。
         * @param {Object} data データ
         */
        bindDataForRow(data) {
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
         * 行の追加処理。
         */
        onAddRecord() {

            this.list.unshift({
                diary_id: null,
                user_id: null,
                title: null,
                content: null,
                diary_datetime: null,
                isChange: true
            });
        },
        /**
         * 行の保存処理。
         * @param {Object} rec 行情報
         * @param {Number} index インデックス
         */
        onSaveRecord(rec, index) {
            this.processSave(rec, index);
        },
        /**
         * 行の削除処理。
         * @param {Object} rec 行情報
         * @param {Number} index インデックス
         */
        onDeleteRecord(rec, index) {

            if (!rec.diary_id) {
                this.list.splice(index, 1);

                if (this.targetRowIndex === index) {
                    // 対象行が削除されたので対象行情報をリセットする
                    this.targetRowIndex = -1;
                }
                return;
            }

            this.processDelete(rec, index);
        },
        /**
         * 変更マークを付与する。
         * @param {Object} rec 行情報
         */
        setRowOfChangeMarking(rec) {
            rec.isChange = true;
            this.$forceUpdate();
        }
    },
    // ----------------------------------------------------
    // 計算項目
    // ----------------------------------------------------
    computed: {
        /**
         * 行数の計算
         * @returns {Function} 行数の計算処理
         */
        calcRows() {

            /**
             * 改行コードの数を数えて、行数を計算する。
             * @param {String} content コンテント
             * @returns {Number} 行数
             */
            return function (content) {

                if (!content) {
                    return 1;
                }

                let ret = content;
                ret = ret.replace(/\r\n/g, "\n");
                ret = ret.replace(/\r/g, "\n");

                return ret.split("\n").length;
            };
        },
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
