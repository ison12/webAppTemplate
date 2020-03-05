/**
 * 文字関連ユーティリティクラス。
 */
export default class {

    /**
     * nullまたはundefinedだったらデフォルト値を返す。
     * それ以外だったらNumberにキャストして返す。
     * @param {String} value 値
     * @param {Number} defaultVal 値
     * @returns {Number} 値
     */
    static castNumber(value, defaultVal) {

        if (typeof defaultVal === "undefined") {
            defaultVal = 0;
        }

        if (
                value === null ||
                value === undefined ||
                value === "") {
            return defaultVal;
        } else {
            return Number(value);
        }
    }

}
