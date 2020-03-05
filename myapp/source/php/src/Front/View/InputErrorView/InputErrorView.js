/* global AppContext */

export default {
    // ----------------------------------------------------
    // Mixin
    // ----------------------------------------------------
    mixins: [],
    // ----------------------------------------------------
    // ローカルコンポーネント
    // ----------------------------------------------------
    components: {
    },
    // ----------------------------------------------------
    // プロパティ
    // ----------------------------------------------------
    props: {
        // ツールチップの表示対象セレクタ
        selector: {
            type: String,
            required: true
        },
        // メッセージ情報
        messageInfo: {
            type: Object,
            required: true
        },
        // メッセージ種類
        messageType: {
            type: String,
            required: false,
            default: "error"
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

            var $inputElement = $(this.$parent.$el).find(this.selector);
            if ($inputElement.length <= 0) {
                // 要素が見つからない場合
                $inputElement.removeClass(this.messageType);
                return;
            }

            // メッセージを設定する
            if (this.messageInfo.message) {
                // メッセージがある場合
                $inputElement.addClass(this.messageType);
            } else {
                // メッセージがない場合
                $inputElement.removeClass(this.messageType);
            }

        },
        /**
         * 破棄処理。
         */
        processDestroy() {

            var $inputElement = $(this.$parent.$el).find(this.selector);
            if ($inputElement.length <= 0 || !$inputElement.bstooltip) {
                // 要素が見つからない場合
                return;
            }

            $inputElement.removeClass(this.messageType);
        }
    },
    // ----------------------------------------------------
    // 計算項目
    // ----------------------------------------------------
    computed: {
        /**
         * メッセージ
         * @returns {String} メッセージ
         */
        message() {

            return this.messageInfo.message;
        },
        /**
         * 表示有無
         * @returns {Boolean} true 表示、false 非表示
         */
        isVisible() {

            this.$nextTick(() => {
                this.processInit();
            });

            if (this.messageInfo.id && this.messageInfo.message) {
                return true;
            } else {
                return false;
            }
        }
    }
}
