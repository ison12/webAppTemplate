import AppContent from '../AppContent/AppContent.vue';
import AppHeader from '../AppHeader/AppHeader.vue';
import AppFooter from '../AppFooter/AppFooter.vue';

export default {
    components: {
        'AppContentComponent': AppContent,
        'AppHeaderComponent': AppHeader,
        'AppFooterComponent': AppFooter
    },
    props: {
    },
    data: function () {
        return {
            // コンポーネントID
            componentId: null
        };
    },
    methods: {
        /**
         * コンポーネント変更処理。
         * @param {type} componentId コンポーネントID
         */
        onChangeComponent: function (componentId) {
            window.AppFuncs.applyContent(componentId);
        }
    }
}