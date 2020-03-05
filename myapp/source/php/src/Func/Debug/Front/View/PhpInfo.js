/* global AppContext */

import ResizeUtil from "Front/Common/Util/ResizeUtil.js";
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
        };
    },
    // ----------------------------------------------------
    // ライフサイクルフック
    // ----------------------------------------------------
    created() {
    },
    mounted() {
        // ページロード時の処理
        this.processLoad();
        // iframeの高さを、ブラウザの高さで調整する
        ResizeUtil.adjustAutoHeightForIframe(this.$refs.phpinfo);
    },
    // ----------------------------------------------------
    // メソッド
    // ----------------------------------------------------
    methods: {
        // 初期表示時の処理
        processLoad() {

            this.getAjax().ajax({
                context: this,
                url: AppContext.baseUrl + "/debug/phpInfo/load",
                data: AppContext.requestParams
            }).done(function (data, textStatus, jqXHR) {

                // データをバインドする
                this.bindData(data);

            }).fail(() => {

            });
        },
        // VueJsのデータにdataを設定
        bindData(data) {
        }
    },
    // ----------------------------------------------------
    // 計算項目
    // ----------------------------------------------------
    computed: {
    }
}
