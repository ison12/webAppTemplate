/**
 * アプリケーション用Ajaxクラス。
 */
export default class {

    /**
     * コンストラクタ。
     * @param {Function} onLoading ロード処理
     * @param {Function} onDone 正常時の処理
     * @param {Function} onFail 異常時の処理
     * @param {Function} onAlways 常に実行される処理
     */
    constructor(onLoading, onDone, onFail, onAlways) {

        this.onLoading = onLoading;
        this.onDone = onDone;
        this.onFail = onFail;
        this.onAlways = onAlways;

        this.lastAjax = [];
    }

    /**
     * Ajaxを実行する。
     * @param {Object} settings 設定オプション
     */
    ajax(settings) {

        var self = this;

        const settingsDefault = {
            async: true,
            cache: false,
            ifModified: "false",
            contentType: "application/x-www-form-urlencoded; charset=UTF-8",
            dataType: "json",
            method: "POST",
            timeout: 60 * 1 * 1000,
            beforeSend(jqXHR, settings) {

                jqXHR.__id = (new Date().getTime()) + "_" + Math.random(); // 一意なIDを生成する
                self.lastAjax[jqXHR.__id] = jqXHR;

                if (self.onLoading) {
                    self.onLoading(true /* ロード開始 */);
                }
            }
        };

        const mergedSettings = Object.assign(settingsDefault, settings);
        const ajaxRet = $.ajax(mergedSettings);

        const defer = $.Deferred();

        ajaxRet.done(function (data, textStatus, jqXHR) {
            // 正常終了

            if (self.onDone) {
                self.onDone(data, textStatus, jqXHR, mergedSettings);
            }

            // defer.resovle ではなくて defer.resolveWith で
            // myAjax(...).done() 内でのthisのコンテキストを
            // 明示的に指定する
            defer.resolveWith(this, arguments);
        });

        ajaxRet.fail(function (jqXHR, textStatus, errorThrown) {
            // 異常終了

            if (self.onFail) {
                self.onFail(jqXHR, textStatus, errorThrown, mergedSettings);
            }

            // defer.resovle ではなくて defer.resolveWith で
            // myAjax(...).done() 内でのthisのコンテキストを
            // 明示的に指定する
            defer.rejectWith(this, arguments);
        });

        ajaxRet.always(function (dataOrJqXHR, textStatus, jqXHROrErrorThrown) {
            // 常に実行される処理

            // 一意なIDに対応するajaxオブジェクトを除去する
            const id = dataOrJqXHR.__id ? dataOrJqXHR.__id : jqXHROrErrorThrown.__id;
            delete self.lastAjax[id];

            if (self.onLoading) {
                self.onLoading(false /* ロード終了 */);
            }

            if (self.onAlways) {
                self.onAlways(dataOrJqXHR, textStatus, jqXHROrErrorThrown, mergedSettings);
            }
        });

        return $.extend({}, ajaxRet, defer.promise());
    }

    /**
     * 最後に実行したAjaxオブジェクトリスト。
     * @returns {Object} 最後に実行したAjaxオブジェクトリスト
     */
    getLastAjax() {
        return this.lastAjax;
    }

    /**
     * エラーメッセージを取得する。
     * @param {String} url URL
     * @param {Object} XMLHttpRequest HttpRequest
     * @param {String} textStatus ステータス
     * @param {Object} errorThrown 例外オブジェクト
     * @returns {Array}
     */
    collectErrorMessages(url, XMLHttpRequest, textStatus, errorThrown) {

        const TIMEOUT_MESSAGE = 'タイムアウトしました。再度実行してください。 ';
        const EXPECTED_MESSAGE = '予期せぬエラーが発生しました。';

        // "success"
        // "notmodified"
        // "error"
        // "timeout"
        // "abort"
        // "parsererror"

        if (textStatus === "timeout") {
            const msg = String(TIMEOUT_MESSAGE);
            return [msg,
                "アクセスURL=" + url];
        } else {
            const msg = EXPECTED_MESSAGE;
            return [msg,
                "アクセスURL=" + url,
                "ステータス=" + textStatus,
                errorThrown && errorThrown.message ? "例外=" + errorThrown.message : null,
                XMLHttpRequest.responseText];
        }

    }

}
