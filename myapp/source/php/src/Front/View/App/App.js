import VueJsCommonMixin from "Front/Common/VueJsMixin/VueJsCommonMixin.js";

import Alert from 'Front/View/Alert/Alert.vue';
import Confirm from 'Front/View/Confirm/Confirm.vue';
import Loading from 'Front/View/Loading/Loading.vue';

import AppContent from 'Front/View/App/AppContent/AppContent.vue';
import AppHeader from 'Front/View/App/AppHeader/AppHeader.vue';
import AppFooter from 'Front/View/App/AppFooter/AppFooter.vue';

export default {
    // ----------------------------------------------------
    // Mixin
    // ----------------------------------------------------
    mixins: [VueJsCommonMixin],
    // ----------------------------------------------------
    // ローカルコンポーネント
    // ----------------------------------------------------
    components: {
        'AlertComponent': Alert,
        'ConfirmComponent': Confirm,
        'LoadingComponent': Loading,
        'AppContentComponent': AppContent,
        'AppHeaderComponent': AppHeader,
        'AppFooterComponent': AppFooter
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
            // ヘッダの表示有無
            isVisibleHeader: true,
            // フッタの表示有無
            isVisibleFooter: true,
            // コンポーネントID
            componentId: null
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
    }
}