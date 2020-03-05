import StringUtil from "./StringUtil.js";

/**
 * ファイル関連ユーティリティクラス。
 */
export default class {

    /**
     * 対象値から拡張子を取得する(文字列の末尾に位置する . より後ろにある文字列)。
     *
     * @param {String} value 対象値
     * @returns {String} 拡張子
     */
    static getFileExt(value) {

        if (StringUtil.isEmpty(value)) {
            return value;
        }

        const splitVal = value.split('.');
        if (1 >= splitVal.length) {
            return value;
        }

        return splitVal[splitVal.length - 1].toLowerCase();
    }

}
