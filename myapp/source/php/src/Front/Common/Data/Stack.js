/**
 * スタッククラス。
 */
export default class {

    /**
     * コンストラクタ。
     */
    constructor() {
        this.list = [];
    }

    /**
     * スタックに追加する。
     * @param {Any} element 要素
     */
    push(element) {
        this.list.push(element);
    }

    /**
     * スタックから要素を取り除く。
     * @returns {Any} 要素
     */
    pop() {
        return this.list.pop();
    }

    /**
     * スタックから要素を取得する。
     * @returns {Any} 要素
     */
    peak() {

        if (this.list.length <= 0) {
            return null;
        }

        return this.list[this.list.length - 1];
    }

    /**
     * スタックから全ての要素を削除する。
     */
    clear() {
        this.list.splice(0, this.list.length);
    }

    /**
     * スタックの頂点からsizeの数だけ要素を残し、満たない要素は全て削除する。
     * @param {Number} size サイズ
     */
    leftFromTop(size) {

        var delCount = this.list.length - size;
        if (delCount <= 0) {
            // 消せるものがないので終了する
            return false;
        }

        this.list.splice(0, delCount);
        return true;
    }

    /**
     * スタックのサイズを取得する。
     * @returns {Number} スタックのサイズ
     */
    length() {
        return this.list.length;
    }

}
