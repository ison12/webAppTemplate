/**
 * フォーム関連ユーティリティクラス。
 */
export default class {

    /**
     * Post実行。
     * @param {String} url URL
     * @param {Object} data データ
     * @param {String} target Post先
     */
    static post(url, data, target) {

        const action = url;
        const method = 'POST';

        const form = $('form#FormUtil_post');
        if (form.length > 0) {
            form.remove();
        }
        form = $('<form>');
        form.attr('id', 'FormUtil_post');
        form.attr('action', action);
        form.attr('method', method);
        if (target) {
            form.attr('target', target);
        }
        form.css('display', 'none');

        form.appendTo($(document.body));

        if (data) {

            for (var key in data) {

                const val = data[key];
                let input;

                if (Array.isArray(val)) {

                    for (var i = 0, ilen = val.length; i < ilen; i++) {
                        input = $('<input>');
                        input.attr('type', 'hidden');
                        input.attr('name', key + "[" + i + "]");
                        input.attr('value', val[i]);
                        form.append(input);
                    }

                } else if (typeof val === 'number') {
                    input = $('<input>');
                    input.attr('type', 'hidden');
                    input.attr('name', key);
                    input.attr('value', val);
                    form.append(input);

                } else if (typeof val === 'string') {
                    input = $('<input>');
                    input.attr('type', 'hidden');
                    input.attr('name', key);
                    input.attr('value', val);
                    form.append(input);
                }
            }

        }

        form.submit();
    }

}
