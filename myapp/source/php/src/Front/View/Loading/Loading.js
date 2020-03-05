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
        lockStartDuration: {
            type: Number,
            required: false,
            default: 0
        },
        lockEndDuration: {
            type: Number,
            required: false,
            default: 0
        },
        opacity: {
            type: Number,
            required: false,
            default: 0.8
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
    // ----------------------------------------------------
    // メソッド
    // ----------------------------------------------------
    methods: {
        /**
         * 表示処理
         */
        show() {

            var view = $(this.$el);
            var lockElement = view;
            var lockElementInner = view.children("div:first");

            // Get the window size.
            var ww = window.innerWidth ? window.innerWidth : $(window).width();
            var wh = window.innerHeight ? window.innerHeight : $(window).height();

            // Get the inner div of lock element.
            var ew = $(lockElementInner).width();
            var eh = $(lockElementInner).height();

            // Calc cneter position.
            lockElementInner.css({
                top: (wh / 2 - eh / 2) + "px",
                left: (ww / 2 - ew / 2) + "px"
            });

            lockElement.css({
                left: 0,
                height: this.getHeight(),
                opacity: 0
            }).show().animate({
                opacity: this.opacity
            }, this.lockStartDuration);

        },
        /**
         * 非表示処理
         * @returns {undefined}
         */
        hide() {

            $(this.$el).animate({
                opacity: 0
            }, this.lockEndDuration, function () {
                $(this.$el).hide();
            }.bind(this));
        },
        /**
         * 表示領域の高さを取得する。
         * @returns {Number}
         */
        getHeight() {
            var a = document.documentElement.clientHeight;
            var b = document.documentElement.scrollHeight;
            return Math.max(a, b) + 20; // 20 is horizontal scrollbar height.
        }
    },
    // ----------------------------------------------------
    // 計算項目
    // ----------------------------------------------------
    computed: {
    }
}
