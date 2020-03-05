/**
 * コレクション関連ユーティリティクラス。
 */
export default class {

    /**
     * 配列を任意のキーでオブジェクトに変換する。
     * @param {Array} arr 配列
     * @param {Function} onKey キーを取得するコールバック関数
     * @returns {Object} オブジェクト
     */
    static convertArrayToObject(arr, onKey) {

        let ret = {};
        for (let i = 0, ilen = arr.length; i < ilen; i++) {

            let key = onKey(arr[i]);
            ret[key] = arr[i];
        }

        return ret;
    }

    /**
     * 配列の要素を変換する。
     * @param {Array} arr 配列
     * @param {Function} onVal 値を取得するコールバック関数
     * @returns {Array} 変換後の配列
     */
    static convertElementOfArray(arr, onVal) {

        let ret = [];
        for (let i = 0, ilen = arr.length; i < ilen; i++) {

            var value = onVal(arr[i]);
            ret.push(value);
        }

        return ret;
    }

    /**
     * 配列をオプション配列（選択肢リスト）に変換する。
     * @param {Array} arr 配列
     * @param {String} targetNameStr 配列の要素の対象となる名前文字列
     * @param {String} targetValueStr 配列の要素の対象となる値文字列
     * @param {String} optionNameStr 選択肢リストの名前文字列
     * @param {String} optionValueStr 選択肢リストの値文字列
     * @returns {Array} オプション配列（選択肢リスト）
     */
    static convertArrayToOptionArray(arr
            , targetNameStr
            , targetValueStr
            , optionNameStr
            , optionValueStr) {

        let ret = [];
        for (let i = 0, ilen = arr.length; i < ilen; i++) {

            const obj = arr[i];

            const targetKey = obj;
            const targetKeyStrArr = targetNameStr.split(".");
            for (let kIndex = 0; kIndex < targetKeyStrArr.length; kIndex++) {
                if (targetKey) {
                    targetKey = targetKey[targetKeyStrArr[kIndex]];
                }
            }

            const targetVal = obj;
            const targetValStrArr = targetValueStr.split(".");
            for (let vIndex = 0; vIndex < targetValStrArr.length; vIndex++) {
                if (targetVal) {
                    targetVal = targetVal[targetValStrArr[vIndex]];
                }
            }

            const element = {};
            element[optionNameStr] = targetKey;
            element[optionValueStr] = targetVal;
            ret.push(element);
        }

        return ret;
    }

    /**
     * オブジェクトからプロパティ値を取得する。
     * @param {Object} obj 配列
     * @param {String} propertyNameStr 配列の要素の対象となるキー
     * @returns {type} プロパティ値
     */
    static getObjectProperty(obj, propertyNameStr) {

        const propertyVal = obj;
        const propertyNameArr = propertyNameStr.split(".");

        for (let kIndex = 0; kIndex < propertyNameArr.length; kIndex++) {
            propertyVal = propertyVal[propertyNameArr[kIndex]];
            if (!propertyVal) {
                return propertyVal;
            }
        }

        return propertyVal;
    }

    /**
     * オブジェクトにプロパティ値を設定する。
     * @param {Object} obj 配列
     * @param {String} propertyNameStr 配列の要素の対象となるキー
     * @param {String} value 値
     */
    static setObjectProperty(obj, propertyNameStr, value) {

        const propertyVal = obj;
        const propertyNameArr = propertyNameStr.split(".");

        for (let kIndex = 0; kIndex < propertyNameArr.length; kIndex++) {

            const temp = propertyVal[propertyNameArr[kIndex]];

            if (!temp) {
                if (kIndex >= propertyNameArr.length - 1) {
                    // 最後の要素の場合
                } else {
                    temp = {};
                    propertyVal[propertyNameArr[kIndex]] = temp;
                    propertyVal = temp;
                }
            } else {
                propertyVal = temp;
            }

            if (kIndex >= propertyNameArr.length - 1) {
                // 最後の要素の場合
                propertyVal[propertyNameArr[kIndex]] = value;
            }
        }
    }

    /**
     * 配列から任意の条件に一致するデータを取得する。
     * @param {Array} arr 配列
     * @param {Function} onFind 任意の条件で判定する関数
     * @returns {type} 配列の要素
     */
    static findElementFromArray(arr, onFind) {

        for (let i = 0; i < arr.length; i++) {

            if (onFind(arr[i])) {
                return arr[i];
            }
        }

        return null;
    }

}
