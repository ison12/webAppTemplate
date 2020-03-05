import CollectionUtil from "Front/Common/Util/CollectionUtil.js";

/**
 * VueJsのエラー関連のMixin。
 */
export default {
    data: function () {
        return {
            errors: [],
            errorsOnBoard: []
        };
    },
    created: function () {
    },
    methods: {
        /**
         * エラー情報を取得する。
         * @param {String} id ID
         * @returns {Object} エラー情報
         */
        getError(id) {

            if (typeof this.errorsMap[id] !== "undefined") {
                return this.errorsMap[id];
            }

            return {id: null, message: ''};
        },
        /**
         * ボードに表示するエラー情報を生成する。
         * @param {Array} errors エラー情報リスト
         * @returns {Array} エラー情報リスト
         */
        createErrorsOnBoard(errors) {

            var ret = [];
            ret = [].concat(errors);

            for (let i = 0; i < this.$children.length; i++) {
                var $c = this.$children[i];
                if (
                        !$c ||
                        !$c.$props ||
                        !$c.$props.messageInfo) {
                    continue;
                }

                var id = this.getError($c.$props.messageInfo.id).id;
                if (
                        id !== null &&
                        $c.isVisible) {

                    for (let j = 0; j < ret.length; j++) {
                        if (ret[j]["id"] === id) {
                            ret.splice(j, 1);
                            j--;
                        }
                    }

                }
            }

            if (errors.length > 0 && ret.length <= 0) {
                ret.push({id: null, message: ''});
            }

            return ret;
        }
    },
    // ----------------------------------------------------
    // 計算項目
    // ----------------------------------------------------
    computed: {
        /**
         * エラーマップを取得する。
         * @returns {Array} エラーマップ
         */
        errorsMap() {
            return CollectionUtil.convertArrayToObject(this.errors, (val) => val.id);
        }
    },
    filters: {
    }
}
