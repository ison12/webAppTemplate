// VueJs
import Vue from 'vue';

/**
 * エラーハンドラクラス。
 */
export default class {

    /**
     * コンストラクタ。
     * @param {Function} onNotifyApi 通知用API
     * @param {Object} onNotifyApiParams 通知用APIパラメータ
     */
    constructor(onNotifyApi, onNotifyApiParams) {

        this.onNotifyApi = onNotifyApi;
        this.onNotifyApiParams = onNotifyApiParams;
        this.applyErrorHandler();
    }

    /**
     * エラーイベントのハンドリングを適用する
     */
    applyErrorHandler() {

        window.addEventListener("error", this.onErrorWindow.bind(this));
        window.addEventListener("unhandledrejection", this.onErrorRejection.bind(this));
        Vue.config.errorHandler = this.onErrorVueJs.bind(this);
    }

    /**
     * Windowエラーイベント
     * @param {type} event イベント
     */
    onErrorWindow(event) {

        // エラー情報を表示する
        alert("UNHANDLED ERROR：" + event.message);

        // エラー記録APIにエラー情報を伝達する
        if (this.onNotifyApi) {
            this.onNotifyApi(event, this.onNotifyApiParams);
        }

    }

    /**
     * Promiseでキャッチされない例外が発生した場合のエラーイベント
     * @param {type} event イベント
     */
    onErrorRejection(event) {

        // エラー情報を表示する
        alert("UNHANDLED PROMISE REJECTION：" + event.reason);

        // エラー記録APIにエラー情報を伝達する
        if (this.onNotifyApi) {
            this.onNotifyApi(event, this.onNotifyApiParams);
        }

    }

    /**
     * VueJsエラーイベント
     * @param {type} err エラー
     * @param {type} vm  VM
     * @param {type} info エラー情報
     */
    onErrorVueJs(err, vm, info) {

        // エラー情報を表示する
        alert("UNHANDLED Vue.js ERROR：" + err);

        // エラー記録APIにエラー情報を伝達する
        if (this.onNotifyApi) {
            this.onNotifyApi({err: err, vm: vm, info: info}, this.onNotifyApiParams);
        }

        // 上位にスローすることでブラウザのコンソールにもエラーを出力する
        throw err;
    }
}
