/* global AppContext */

import VueJsCommonMixin from "Front/Common/VueJsMixin/VueJsCommonMixin.js";
import DatePicker from "Front/Common/Control/DatePicker.js";

export default {
    // ----------------------------------------------------
    // Mixin
    // ----------------------------------------------------
    mixins: [VueJsCommonMixin],
    // ----------------------------------------------------
    // ローカルコンポーネント
    // ----------------------------------------------------
    components: {
    },
    // ----------------------------------------------------
    // プロパティ
    // ----------------------------------------------------
    props: {
        // 日付値
        value: {
            required: true
        },
        // 読み取り有無
        readonly: {
            type: Boolean,
            required: false,
            default: false
        }
    },
    // ----------------------------------------------------
    // データ
    // ----------------------------------------------------
    data() {
        return {
        };
    },
    // ----------------------------------------------------
    // ライフサイクルフック
    // ----------------------------------------------------
    created() {
    },
    mounted() {
    },
    beforeDestroy() {
        this.processDestroy();
    },
    // ----------------------------------------------------
    // メソッド
    // ----------------------------------------------------
    methods: {
        /**
         * 初期化処理。
         */
        processInit() {

            var self = this;

            if (!this.readonly) {
                // 編集可能な場合のみ初期化する

                // DatePickerの初期化
                DatePicker.applyCalendar(this.$el, {
                    /**
                     * 日付カレンダーによる日付選択処理
                     * @param {String} dateText 日付テキスト
                     */
                    onSelect: function (dateText/*, inst*/) {
                        // 親にイベントを送信する
                        // 親のv-modelの更新
                        self.$emit('input', dateText);
                    }
                });

                return true;
            } else {
                this.processDestroy();
            }

            return false;
        },
        /**
         * 破棄処理。
         */
        processDestroy() {
            DatePicker.destroyCalendar(this.$el);
        },
        // 値が変更された場合
        onInput: function (e) {
            // 親にイベントを送信する
            // 親のv-modelの更新
            this.$emit('input', e.target.value);
        },
        // フォーカスされた場合
        onFocus: function (/*e*/) {

            // フォーカス時にカレンダーコントロールを有効にする
            if (this.processInit()) {
                this.onShowCalendar();
            }

            // 親にイベントを送信する
            this.$emit('focus');
        },
        // フォーカスが解除された場合
        onBlur: function (/*e*/) {
            // 親にイベントを送信する
            this.$emit('blur');
        },
        // カレンダーを表示する
        onShowCalendar: function (/*e*/) {
            $(this.$el).datepicker("show");
        }
    },
    // ----------------------------------------------------
    // 計算項目
    // ----------------------------------------------------
    computed: {
    }
}
