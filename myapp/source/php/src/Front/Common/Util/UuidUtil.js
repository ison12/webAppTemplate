/**
 * UUIDユーティリティクラス。
 */
export default class {

    /**
     * UUID値を比較する。
     * @param {String} value1 値
     * @param {String} value2 値
     * @returns {Boolean} true 一致, flase 不一致
     */
    static equalsUuid(value1, value2) {

        value1 = castString(value1);
        value2 = castString(value2);

        const reg = /^[0-9A-F]{8}-[0-9A-F]{4}-[0-9A-F]{4}-[0-9A-F]{4}-[0-9A-F]{12}$/i;

        if (
                value1.match(reg) && value2.match(reg) &&
                value1.toUpperCase() === value2.toUpperCase()
                ) {
            // 両者がUUIDの場合は、大文字に変換して一致するかを判定する
            return true;
        }

        // 通常の比較を実行する
        return value1 === value2;
    }

}
