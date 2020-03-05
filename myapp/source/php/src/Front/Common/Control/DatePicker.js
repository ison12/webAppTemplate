/**
 * 日付コントロール関連クラス。
 */
export default class {

    /**
     * カレンダー補助コントロールを適用する。
     * @param {Object|String} selector セレクタ
     * @param {Object} option オプション
     */
    static applyCalendar(selector, option) {

        // カレンダーを日本語化
        $(selector).datepicker({
            regional: "ja"
            , constrainInput: true
            , showAnim: null /* fadeIn */
            , duration: ""
            , dateFormat: "yy/mm/dd"
            , monthNames: ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12']
            , monthNamesShort: ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12']
            , dayNames: ['日曜日', '月曜日', '火曜日', '水曜日', '木曜日', '金曜日', '土曜日']
            , dayNamesMin: ['日', '月', '火', '水', '木', '金', '土']
            , dayNamesShort: ['日', '月', '火', '水', '木', '金', '土']
            , changeYear: true
            , changeMonth: true
            , yearRange: 'c-99:c+10'
            , isRTL: false
                    //, yearSuffix: '年'
            , showMonthAfterYear: true
            , onSelect: option.onSelect
        });
    }

    /**
     * カレンダー補助コントロールを解除する。
     * @param {Object|String} selector セレクタ
     */
    static destroyCalendar(selector) {

        $(selector).datepicker("destroy");
    }
}
