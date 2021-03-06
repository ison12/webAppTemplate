/* global AppContext */

import VueJsCommonMixin from "Front/Common/VueJsMixin/VueJsCommonMixin.js";
import Error from "./Error.vue";

export default {
    // ----------------------------------------------------
    // Mixin
    // ----------------------------------------------------
    mixins: [VueJsCommonMixin],
    // ----------------------------------------------------
    // ローカルコンポーネント
    // ----------------------------------------------------
    components: {
        'ErrorComponent': Error,
    },
    // ----------------------------------------------------
    // プロパティ
    // ----------------------------------------------------
    props: {
        // エラーリスト
        errors: {
            type: Array,
            required: true,
            validator: function (value) {
                return true;
            }
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
    },
    // ----------------------------------------------------
    // 計算項目
    // ----------------------------------------------------
    computed: {
        errorsIsNotEmpty() {
            var ret = [].concat(this.errors);
            for (let i = 0; i < ret.length; i++) {
                if (!ret[i].id) {
                    ret.splice(i, 1);
                    i--;
                }
            }

            return ret;
        }
    }
}
