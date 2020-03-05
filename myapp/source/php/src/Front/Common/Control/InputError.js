/**
 * 入力エラー関連クラス。
 */
export default class {

    /**
     * errorsの内容に対応する要素をツールチップ情報としてバインドする。
     * バインドできなかったエラーについては、戻り値として返却する。
     *
     * 例）errorsの内容 … "対象要素のname属性"と"id"が一致した場合に message をツールチップとしてバインドする
     * let errors = [
     *     {id: 'item1', message: 'item1は必須です'},
     *     {id: 'item2', message: 'item2は半角数値形式で入力してください'},
     *     ...
     * ];
     *
     * @param {Array} errors エラーリスト
     * @param {String} targetSelector ツールチップとしてバインドする対象要素
     * @returns {Array} バインドできなかったエラーリスト
     */
    static bindTooltip(errors, targetSelector) {

        const errorsNotBindItems = [];
        const $target = $(targetSelector);

        // エラー対象になりうるすべての要素のエラー情報をクリアする
        $target.removeClass('error'); // エラースタイル
        $target.bstooltip('dispose'); // ツールチップ

        for (let i = 0, max = errors.length; i < max; i++) {

            // エラーが発生している項目を特定して、エラー情報を適用する
            const error = errors[i];
            const errorId = error.id;
            const $element = $target.filter('[name=' + errorId + ']');

            if ($element.length > 0) {

                $.each($element, function (index, val) {
                    if ($(val).prop("tagName") === "LABEL") {
                        $(val).addClass("error");
                    } else {
                        $(val).attr("title", error.message);
                        $(val).addClass("error");
                    }
                });

                // エラーと判定された要素のみ、ツールチップを適用する
                $element.bstooltip({
                    container: 'body',
                    template: '<div class="tooltip error" role="tooltip"><div class="arrow"></div><div class="tooltip-outer"><span class="oi oi-warning"></span> <span class="tooltip-inner"></span></div></div>'}
                );

            } else {
                // エラーが発生している項目が見つからないため、項目に紐付かないエラーとして追加する
                errorsNotBindItems.push(error);
            }
        }

        return errorsNotBindItems;
    }

}
