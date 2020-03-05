/**
 * リサイズ関連ユーティリティクラス。
 */
export default class {

    /**
     * IFrameを自動高さ調整する。
     * @param {type} selector IFrameのセレクタ
     */
    static adjustAutoHeightForIframe(selector) {

        if (!selector) {
            selector = 'iframe';
        }

        $(selector)
                .on('load', function () {
                    try {
                        $(this).height(this.contentWindow.document.documentElement.scrollHeight);
                    } catch (e) {
                        // 例外発生時は特に何も処理しない
                    }
                })
                .trigger('load');
    }

    /**
     * 子ページからIFrameを自動高さ調整する。
     * @param {type} iframeId 親ページで設定したIFrameのID
     */
    static adjustAutoHeightForIframeAtChildPage(iframeId) {

        if (!window.parent.document) {
            return false;
        }

        $(document)
                .on('load', function () {
                    try {
                        try { // !IE
                            document.styleSheets[0].insertRule('html' + '{overflow:hidden;}', document.styleSheets[0].cssRules.length);
                        } catch (e) { // IE
                            document.styleSheets[0].addRule('html', '{overflow:hidden;}');
                        }

                        var height = document.getElementsByTagName('div')[0].offsetHeight;
                        window.parent.document.getElementById(iframeId).style.height = (height) + 'px';
                    } catch (e) {
                        // 例外発生時は特に何も処理しない
                    }
                })
                .trigger('load');
    }

    /**
     * 任意の要素の高さを調整する。
     * @param {type} selector 対象セレクタ
     * @param {type} relativeSelector 関連するセレクタ
     * @param {type} minHeight 最小の高さ
     * @param {type} adjustBottom 下部の調整値
     */
    static adjustHeight(selector, relativeSelector, minHeight, adjustBottom) {

        var calcHeight = 0;
        $(selector).filter(":visible").each(function () {

            // ウィンドウの高さを取得する
            var windowHeight = $(window).height();
            // 対象要素のオフセット位置を取得する
            var targetOffset = $(this).offset();
            // 関連要素の高さの合計を取得する
            var otherHeightSum = 0;
            $(relativeSelector).each(function () {
                otherHeightSum += $(this).outerHeight(true);
            });
            // 適用すべき高さを取得する
            calcHeight = windowHeight - targetOffset.top - otherHeightSum - adjustBottom;
            if (calcHeight < minHeight) {
                calcHeight = minHeight;
            }
        });
        $(selector).each(function () {

            $(this).height(calcHeight);
        });
    }

    /**
     * ウィンドウのリサイズに合わせて高さを自動調整する。
     * @param {type} selector 対象セレクタ
     * @param {type} relativeSelector 関連するセレクタ
     * @param {type} minHeight 最小の高さ
     * @param {type} adjustBottom 下部の調整値
     */
    static applyAutoAdjustHeight(selector, relativeSelector, minHeight, adjustBottom) {

        var resizer = function () {
            ResizeUtil.adjustHeight(selector, relativeSelector, minHeight, adjustBottom);
        };
        $(window).off("resize", resizer);
        $(window).resize(resizer);
        ResizeUtil.adjustHeight(selector, relativeSelector, minHeight, adjustBottom);
    }

}
