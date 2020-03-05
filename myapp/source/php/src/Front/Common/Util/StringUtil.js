/**
 * 文字関連ユーティリティクラス。
 */
export default class {

    /**
     * nullまたはundefinedだったらデフォルト値を返す。
     * それ以外だったらStringにキャストして返す。
     * @param {String} value 値
     * @param {String} defaultVal デフォルト値
     * @returns {String} 値
     */
    static castString(value, defaultVal) {

        if (typeof defaultVal === "undefined") {
            defaultVal = "";
        }

        if (
                value === null ||
                value === undefined ||
                typeof value === "object"
                ) {
            return defaultVal;
        } else {
            return String(value);
        }
    }

    /**
     * 空かどうかを判定する。
     * @param {String} value 値
     * @returns {Boolean} true 空文字列, flase 空文字列ではない
     */
    static isEmpty(value) {

        value = this.castString(value);

        if (value === '') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 空ではないかを判定する。
     * @param {String} value 値
     * @returns {Boolean} true 空文字列ではない, flase 空文字列
     */
    static isNotEmpty(value) {
        return !this.isEmpty(value);
    }

    /**
     *トリムする。
     * @param {String} value 値
     * @returns {String} 値
     */
    static trim(value) {

        value = this.castString(value);

        return value.trim();
    }

    /**
     * (文字)×(繰り返し数)で、文字列を生成する。
     * @param {String} c 文字
     * @param {Number} num 繰り返し数
     * @returns {String} 繰り返し文字列
     */
    static repeat(c, num) {

        let str = "";

        for (let i = 0; i < num; i++) {
            str += c;
            // 文字列生成
        }

        return str;
    }

    /**
     * パスカルケースをケバブケースに変換する。
     * @param {String} value 値
     * @returns {String} ケバブケース
     */
    static convertPascalToKebab(value) {

        if (this.isEmpty(value)) {
            return value;
        }

        let ret = "";

        for (let i = 0, length = value.length; i < length; i++) {

            if (value.substr(i, 1).toUpperCase() === value.substr(i, 1)) {

                // 大文字
                if (i === 0) {
                    // 最初の文字なら、区切り文字は付与しない
                    ret += value.substr(i, 1);
                } else if (i > 0 && value.substr(i - 1, 1).toUpperCase() === value.substr(i - 1, 1)) {
                    // 前の文字が大文字なら、区切り文字は付与しない
                    ret += value.substr(i, 1);
                } else {
                    // 小文字なので区切り文字を付与
                    ret += "-" + value.substr(i, 1);
                }

            } else {
                // 小文字
                ret += value.substr(i, 1);
            }

        }

        return ret.toLowerCase();
    }

    /**
     * 改行文字 (\r\n、\r、\n) を brタグ に変換する。
     * @param {String} value 値
     * @returns {String} 改行文字をbrタグに置換した文字列
     */
    static convertNewLineToBr(value) {

        if (this.isEmpty(value)) {
            return value;
        }

        return value.replace(/(?:\r\n|\r|\n)/g, '<br />');
    }

    /**
     * 先頭の文字を小文字に変換する。
     * @param {String} value 値
     * @returns {String} 変換値
     */
    static toLowerFirst(value) {

        if (this.isEmpty(value)) {
            return value;
        }

        return value.substr(0, 1).toLowerCase() + value.substr(1);
    }

    /**
     * 先頭の文字を大文字に変換する。
     * @param {String} value 値
     * @returns {String} 変換値
     */
    static toUpperFirst(value) {

        if (this.isEmpty(value)) {
            return value;
        }

        return value.substr(0, 1).toUpperCase() + value.substr(1);
    }

    /**
     * 任意の文字で左側を埋めた文字列を取得する。
     * @param {String} target 対象文字列
     * @param {Number} count 桁数
     * @param {String} pad 埋める文字
     * @returns {String} 左文字埋めした文字列
     */
    static padLeft(target, count, pad) {

        let pad_str = "";
        for (let i = 0; i < count; i++) {
            pad_str += pad;
        }

        var len = count * -1;
        return (pad_str + target).slice(len);
    }

    /**
     * 任意の文字で右側を埋めた文字列を取得する。
     * @param {String} target 対象文字列
     * @param {Number} count 桁数
     * @param {String} pad 埋める文字
     * @returns {String} 右文字埋めした文字列
     */
    static padRight(target, count, pad) {

        let pad_str = "";
        for (let i = 0; i < count; i++) {
            pad_str += pad;
        }

        return (target + pad_str).slice(0, count);
    }

    /**
     * 半角を全角に変換する。
     * @param value 半角が含まれた文字列
     * @returns {String} 全角に変換した文字列
     */
    static convertHalfToFull(value) {

        let ret = "";
        for (let i = 0; i < value.length; i++) {

            const code = value.charCodeAt(i);

            // 半角の範囲チェック
            if (0x0021 <= code && code <= 0x007E) {
                // ひらがなをカタカナにシフトして追加する
                ret += String.fromCharCode(code + 0xFEE0);
            } else {
                // ひらがなではないので、そのまま追加する
                ret += value.substring(i, i + 1);
            }

        }

        return ret;
    }

    /**
     * 全角を半角に変換する。
     * @param value 半角が含まれた文字列
     * @returns {String} 全角に変換した文字列
     */
    static convertFullToHalf(value) {

        let ret = "";
        for (let i = 0; i < value.length; i++) {

            const code = value.charCodeAt(i);

            // 半角の範囲チェック
            if (0xFF01 <= code && code <= 0xFF5E) {
                // ひらがなをカタカナにシフトして追加する
                ret += String.fromCharCode(code - 0xFEE0);
            } else {
                // ひらがなではないので、そのまま追加する
                ret += value.substring(i, i + 1);
            }

        }

        return ret;
    }

}
