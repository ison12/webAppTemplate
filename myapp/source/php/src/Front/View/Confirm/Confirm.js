/* global AppContext */

import VueJsCommonMixin from "Front/Common/VueJsMixin/VueJsCommonMixin.js";

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
    },
    // ----------------------------------------------------
    // データ
    // ----------------------------------------------------
    data() {
        return {
            // タイトル
            caption: null,
            // メッセージ
            message: [],
            // ボタン定義
            buttonsSet: null,
            // Positive or Negativeボタンクリック中のフラグ
            posiOrNegaClicking: false,
            // 種類
            type: 'info' // info or warn or error
        };
    },
    // ----------------------------------------------------
    // ライフサイクルフック
    // ----------------------------------------------------
    created() {
    },
    mounted() {
    },
    // ----------------------------------------------------
    // メソッド
    // ----------------------------------------------------
    methods: {
        // Info 表示処理

        /**
         * 情報表示。
         * @param {String} caption キャプション
         * @param {String} message メッセージ
         * @param {Object} buttons ボタンセット
         */
        showInfo(caption, message, buttons) {
            this.show('info', caption, message, buttons, "btn btn-primary text-white");
        },
        /**
         * 警告表示。
         * @param {String} caption キャプション
         * @param {String} message メッセージ
         * @param {Object} buttons ボタンセット
         */
        showWarn(caption, message, buttons) {
            this.show('warn', caption, message, buttons, "btn btn-warning text-white");
        },
        /**
         * エラー表示。
         * @param {String} caption キャプション
         * @param {String} message メッセージ
         * @param {Object} buttons ボタンセット
         */
        showError(caption, message, buttons) {
            this.show('error', caption, message, buttons, "btn btn-danger text-white");
        },
        /**
         * 表示処理。
         *
         * buttonsの設定例）onClickでtrueを返却するか何も返却しない場合は、処理を続行する。それ以外の場合は、処理を中断する
         * -------------------------
         * {
         *     positive: {
         *       text: 'OK',
         *       onClick        () { return true; },
         *       onCloseComplete() { ... },
         *     },
         *     negative: {
         *       text: 'Cancel',
         *       onClick        () { return true; },
         *       onCloseComplete() { ... },
         *     }
         * }
         * -------------------------
         *
         * @param {String} type 表示種類
         * @param {String} caption キャプション
         * @param {String} message メッセージ
         * @param {Object} buttons ボタンセット
         */
        show(type, caption, message, buttons) {

            var self = this;
            var dialog = $(this.$el);

            this.type = type;
            this.caption = caption;
            if (Array.isArray(message)) {
                // 配列でメッセージが渡された場合
                this.message = message.concat();
            } else {
                // メッセージが文字列の場合
                this.message.splice(0, this.message.length);
                this.message.push(message);
            }

            // buttons引数省略時にデフォルト値を設定
            var buttonsSet = typeof buttons !== 'undefined' ? buttons
                    : {
                        positive: {
                            text: "はい",
                            onClick() {
                                return true;
                            },
                            onCloseComplete() { }
                        },
                        negative: {
                            text: "いいえ",
                            onClick() {
                                return true;
                            },
                            onCloseComplete() { }
                        }
                    };

            if (typeof buttonsSet.positive.text === "undefined") {
                buttonsSet.positive.text = "はい";
            }
            if (typeof buttonsSet.negative.text === "undefined") {
                buttonsSet.negative.text = "いいえ";
            }

            this.buttonsSet = buttonsSet;

            // yes or noボタン押下時 true
            this.posiOrNegaClicking = false;

            dialog.off('hidden.bs.modal');
            dialog.on('hidden.bs.modal', function (e) {

                if (!self.posiOrNegaClicking) {
                    var ret = buttonsSet.negative.onClick();
                    if (ret === undefined || ret) {
                        // コールバック関数で閉じる処理を継続すると判断された場合
                        return true;
                    } else {
                        // コールバック関数で閉じる処理をキャンセルすると判断された場合
                        self.posiOrNegaClicking = false;
                        return false;
                    }
                }

            });

            this.$nextTick(function () {
                // DOM 更新後にダイアログを表示することで一時的なちらつきをなくす
                dialog.modal('show');
            });
        },
        /**
         * 非表示処理
         */
        hide() {
            var dialog = $(this.$el);
            dialog.modal('hide');
        },
        /**
         * Positiveボタン押下。
         */
        onClickPositive() {

            this.posiOrNegaClicking = true;

            var ret = this.buttonsSet.positive.onClick();

            if (ret === undefined || ret) {
                // コールバック関数で閉じる処理を継続すると判断された場合
                this.hide();
                if (this.buttonsSet.positive.onCloseComplete)
                    this.buttonsSet.positive.onCloseComplete();
            } else {
                // コールバック関数で閉じる処理をキャンセルすると判断された場合
                this.posiOrNegaClicking = false;
            }

        },
        /**
         * Negativeボタン押下。
         */
        onClickNegative() {

            this.posiOrNegaClicking = true;

            var ret = this.buttonsSet.negative.onClick();

            if (ret === undefined || ret) {
                // コールバック関数で閉じる処理を継続すると判断された場合
                this.hide();
                if (this.buttonsSet.negative.onCloseComplete)
                    this.buttonsSet.negative.onCloseComplete();
            } else {
                // コールバック関数で閉じる処理をキャンセルすると判断された場合
                this.posiOrNegaClicking = false;
            }
        }
    },
    // ----------------------------------------------------
    // 計算項目
    // ----------------------------------------------------
    computed: {
    }
}
