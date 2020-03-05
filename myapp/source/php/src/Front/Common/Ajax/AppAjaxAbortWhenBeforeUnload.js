import AppAjax from "./AppAjax.js";

/**
 * Ajax通信時をページ移動前に通信を停止させるイベントクラス。
 */
export default class {

    /**
     * コンストラクタ。
     * @param {AppAjax} appAjax ロード処理
     */
    constructor(appAjax) {

        this.appAjax = appAjax;

        applyBeforeUnload();
    }

    /**
     * 最後に実行したAjaxオブジェクトリスト。
     * @returns {Object} 最後に実行したAjaxオブジェクトリスト
     */
    applyBeforeUnload() {

        window.addEventListener('beforeunload', (event) => {

            const lastAjax = this.appAjax.getLastAjax();

            for (var key in lastAjax) {
                lastAjax[key].abort();
            }
        });
    }

}
