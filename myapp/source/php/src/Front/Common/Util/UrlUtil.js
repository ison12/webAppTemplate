import StringUtil from "./StringUtil.js";

/**
 * URL関連ユーティリティクラス。
 */
export default class {

    /**
     * クエリパラメータのURIを生成する。
     * @param {Object} queries 配列
     * @returns {String} クエリパラメータのURI
     */
    static createQueryUri(queries) {

        var ret = [];

        for (const key in queries) {
            const value = queries[key];
            ret.push(key + '=' + encodeURIComponent(value));
        }

        return ret.join("&");
    }

    /**
     * URLとクエリパラメータを結合する。
     * @param {String} url URL
     * @param {String} queriesStr クエリパラメータ文字列
     * @returns {String} クエリパラメータのURI
     */
    static combineUrlAndQueryParam(url, queriesStr) {

        if (url.indexOf("?") >= 0) {
            return url + "&" + queriesStr;
        }

        return url + "?" + queriesStr;
    }

    /**
     * URLのクエリパラメータをオブジェクトで取得する。
     * @returns {Object} URLのクエリパラメータ
     */
    static getUrlQueryParam() {

        let ret = {};

        const pair = window.location.search.substring(1).split('&');

        for (let i = 0, ilen = pair.length; i < ilen; i++) {

            const pairStr = pair[i];

            if (StringUtil.isEmpty(pairStr)) {
                continue;
            }

            const keyValue = pairStr.split('=');
            ret[keyValue[0]] = keyValue[1];
        }

        return ret;
    }

}
