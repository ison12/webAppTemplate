/**
 * VueJsのページ関連のMixin。
 */
export default {
    data: function () {
        return {
        };
    },
    created: function () {
    },
    methods: {
        /*
         * ページ番号を計算する。
         * @param {Number} currentPageNum 現在のページ番号
         * @param {Number} pageSize ページサイズ
         * @param {String} targetPageNum 移動したい対象のページ番号
         * @returns {Number} 計算したページ番号
         */
        calcPageNum: function (currentPage, pageSize, targetPageNum) {

            var ret = currentPage;

            switch (targetPageNum) {
                case 'f':
                    // 先頭
                    if (currentPage <= 1) {
                        break;
                    }
                    ret = 1;
                    break;
                case 'p':
                    // 一つ前
                    if (currentPage <= 1) {
                        break;
                    }
                    ret = currentPage - 1;
                    break;
                case 'n':
                    // 一つ次
                    if (currentPage >= pageSize) {
                        break;
                    }
                    ret = currentPage + 1;
                    break;
                case 'l':
                    // 最後
                    if (currentPage >= pageSize) {
                        break;
                    }
                    ret = pageSize;
                    break;
                case 'x':
                    // 省略表記
                    break;
                default:
                    // 指定のページ番号
                    ret = Number(targetPageNum);
                    break;
            }

            return ret;
        }
    }
}
