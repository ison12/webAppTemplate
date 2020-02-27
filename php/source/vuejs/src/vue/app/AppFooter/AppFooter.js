export default {
    data: function () {
        return {
            // コンポーネントID
            componentId: null
        };
    },
    methods: {
        /**
         * コンポーネントの変更
         */
        onChangeComponent: function () {
            // イベントを発行
            this.$emit("onChangeComponent", this.componentId);
        }
    }
}
