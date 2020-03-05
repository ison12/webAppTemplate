import StringUtil from "./StringUtil.js";

/**
 * 日付関連ユーティリティクラス。
 */
export default class {

    /**
     * 日時を解析してオブジェクトに変換する。
     * yyyy/MM/dd HH:mm:ss.fff
     *
     * @param {String} str 日付文字列
     * @returns {Date} 日付オブジェクト
     */
    static parseYmdHmsF(str) {

        var d = null;

        if (str === null || str === undefined || str === '') {
            return d;
        }

        str = str.replace(/T/g, ' ');

        var splitYmdHmsF = str.split(' ');
        if (splitYmdHmsF.length < 2) {
            return d;
        }
        var strYmd = splitYmdHmsF[0];
        var strHmsF = splitYmdHmsF[1];

        var splitYmd = strYmd.replace(/-/g, '/').split('/');
        if (splitYmd.length < 3) {
            return d;
        }
        var strY = splitYmd[0];
        var strM = splitYmd[1];
        var strD = splitYmd[2];

        strHmsF = strHmsF.substr(0, 15); // タイムゾーン、例として（+09:00）のような値をカットする

        var splitHmsF = strHmsF.replace(/\./g, ':').split(':');
        if (splitHmsF.length < 3) {
            return d;
        }

        var strTimeH = 0;
        var strTimeM = 0;
        var strTimeS = 0;
        var strTimeF = 0;
        if (splitHmsF.length >= 4) {
            strTimeH = splitHmsF[0];
            strTimeM = splitHmsF[1];
            strTimeS = splitHmsF[2];
            strTimeF = splitHmsF[3].substr(0, 3);
        } else {
            strTimeH = splitHmsF[0];
            strTimeM = splitHmsF[1];
            strTimeS = splitHmsF[2];
            strTimeF = 0;
        }

        d = new Date(strY
                , Number(strM) - 1
                , strD
                , strTimeH
                , strTimeM
                , strTimeS
                , strTimeF);

        return d;
    }

    /**
     * 日時を解析してオブジェクトに変換する。
     * yyyy/MM/dd HH:mm:ss.fff
     *
     * @param {String} str 日付文字列
     * @returns {Date} 日付オブジェクト
     */
    static parseYmdHms(str) {

        var d = null;

        if (str === null || str === undefined || str === '') {
            return d;
        }

        str = str.replace(/T/g, ' ');

        var splitYmdHms = str.split(' ');
        if (splitYmdHms.length < 2) {
            return d;
        }
        var strYmd = splitYmdHms[0];
        var strHmsF = splitYmdHms[1];

        var splitYmd = strYmd.replace(/-/g, '/').split('/');
        if (splitYmd.length < 3) {
            return d;
        }
        var strY = splitYmd[0];
        var strM = splitYmd[1];
        var strD = splitYmd[2];

        var splitHmsF = strHmsF.replace(/\./g, ':').split(':');
        if (splitYmd.length < 3) {
            return d;
        }
        var strTimeH = splitHmsF[0];
        var strTimeM = splitHmsF[1];
        var strTimeS = splitHmsF[2];

        d = new Date(strY
                , Number(strM) - 1
                , strD
                , strTimeH
                , strTimeM
                , strTimeS);

        return d;
    }

    /**
     * 日時を解析してオブジェクトに変換する。
     * yyyy/MM/dd
     *
     * @param {String} str 日付文字列
     * @returns {Date} 日付オブジェクト
     */
    static parseYmd(str) {

        var d = null;

        if (str === null || str === undefined || str === '') {
            return d;
        }

        var strConv = str.replace(/-/g, '/').split('/');
        if (strConv.length >= 3) {

            d = new Date(strConv[0]
                    , Number(strConv[1]) - 1
                    , strConv[2]);

        }

        return d;
    }

    /*
     * 日付オブジェクトをフォーマットして文字列に変換する。
     * @param {Date} param 日付オブジェクト
     * @param {String} format 日付文字列フォーマット
     * @returns {String} 日付文字列
     */
    static toString(param, format) {

        var weekChars = ["日", "月", "火", "水", "木", "金", "土"];

        var dt = null;
        var yyyy = "";
        var dd = "";
        var d = "";
        var mm = "";
        var m = "";
        var hh = "";
        var h = "";
        var nn = "";
        var n = "";
        var ss = "";
        var s = "";
        var ff = "";

        if (!param) {
            return param;
        }

        dt = param;

        // 年
        yyyy = dt.getFullYear().toString();
        format = format.replace("yyyy", yyyy);
        format = format.replace("yy", yyyy.substr(2, 2));
        // 月
        mm = (dt.getMonth() + 1).toString();
        if (mm.length === 1) {
            mm = "0" + mm;
        }
        m = (dt.getMonth() + 1).toString();
        format = format.replace("mm", mm);
        format = format.replace("m", m);
        // 日
        dd = dt.getDate().toString();
        if (dd.length === 1) {
            dd = "0" + dd;
        }
        d = dt.getDate().toString();
        format = format.replace("dd", dd);
        format = format.replace("d", d);
        // 時
        hh = dt.getHours().toString();
        if (hh.length === 1) {
            hh = "0" + hh;
        }
        h = dt.getHours().toString();
        format = format.replace("hh", hh);
        format = format.replace("h", h);
        // 分
        nn = dt.getMinutes().toString();
        if (nn.length === 1) {
            nn = "0" + nn;
        }
        n = dt.getMinutes().toString();
        format = format.replace("nn", nn);
        format = format.replace("n", n);
        // 秒
        ss = dt.getSeconds().toString();
        if (ss.length === 1) {
            ss = "0" + ss;
        }
        s = dt.getSeconds().toString();
        format = format.replace("ss", ss);
        format = format.replace("s", s);
        // ミリ秒
        ff = dt.getMilliseconds().toString();
        format = format.replace("z", StringUtil.padLeft(ff, 3, "0"));

        // 曜日
        var wDay = dt.getDay();
        format = format.replace("D", weekChars[wDay]);

        return format;
    }

}
