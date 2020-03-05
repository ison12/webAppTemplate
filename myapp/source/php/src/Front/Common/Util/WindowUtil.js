import FormUtil from "./FormUtil.js";

/**
 * ウィンドウ関連ユーティリティクラス。
 */
export default class {

    /**
     * ウィンドウをオープンする。
     * @param {String} url URL
     * @param {String} name ウィンドウ名
     * @param {String} windowFeatures ウィンドウのオプション
     */
    static open(url, name, windowFeatures) {
        var ret = window.open(url, name, windowFeatures);
        return ret;
    }

    /**
     * デフォルト指定でウィンドウをオープンする。
     * @param {String} url URL
     * @param {String} name ウィンドウ名
     * @param {Object} data データ
     * @param {Boolean} isTab タブ表示有無
     */
    static openWithPost(url, name, data, isTab) {

        let ret;

        if (isTab) {
            ret = window.open("about:blank", name);
        } else {
            const windowFeatures = this.getDefaultWindowFeatures();
            ret = window.open("about:blank", name, windowFeatures);
        }

        // popupブロックによりwindowオブジェクトが取得できないことがあるためチェックする
        if (ret) {
            ret.focus();
        }

        FormUtil.post(url, data, name);

        return ret;
    }

    /**
     * デフォルトウィンドウオプションの取得
     * @returns {String} デフォルトウィンドウオプション
     */
    static getDefaultWindowFeatures() {

        const marginX = 20;
        const marginY = 60;

        const width = screen.availWidth - marginX;
        const height = screen.availHeight - marginY;

        const left = 0;//Number((screen.availWidth - width) / 2);
        const top = 0;//Number((screen.availHeight - height) / 2);

        const widthOption = "width=" + width;
        const heightOption = "height=" + height;

        const leftOption = "left=" + left;
        const topOption = "top=" + top;

        return "menubar=no,location=no,resizable=yes,scrollbars=yes,status=no," + leftOption + "," + topOption + "," + widthOption + "," + heightOption;

    }

    /**
     * スリープ処理。
     * @param {Number} waitMsec 待機時間（ミリ秒）
     */
    static sleep(waitMsec) {

        const startMsec = new Date();

        // 指定ミリ秒間だけループさせる（CPUは常にビジー状態）
        while (new Date() - startMsec < waitMsec)
            ;
    }

    /**
     * クリップボードにコピーする。
     * @param {String} value コピー文字列
     * @returns {Boolean} true コピーに成功、false コピーに失敗
     */
    static copyToClipboard(value) {

        var result = false;

        if (typeof window.clipboardData === "undefined") {
            // other browser

            var temp = document.createElement('div');
            var child = document.createElement('textarea');
            temp.appendChild(child);

            child.value = value;

            var s = temp.style;
            s.position = 'fixed';
            s.left = '-100%';

            document.body.appendChild(temp);

            window.setTimeout(function () {
                // 文字が大量にあると、うまくコピーできないので少し待機する
                child.select(temp);

                result = document.execCommand('copy');

                document.body.removeChild(temp);

                alert("コピーしました");
            }, 500);

        } else {
            // internet explorer

            window.clipboardData.setData("Text", value);
            alert("コピーしました");

            result = true;
        }

        return result;
    }
}
