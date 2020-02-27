// ライブラリのインポート
import Vue from 'vue';

// アプリケーションコンポーネント
import AppComponent from './vue/app/App/App.vue';

// 機能別コンポーネント
import FuncDogComponent from './vue/func/Dog/Dog.vue';
import FuncCatComponent from './vue/func/Cat/Cat.vue';

// コンポーネント登録
Vue.component("DogComponent", FuncDogComponent);
Vue.component("CatComponent", FuncCatComponent);

// ルートのVueJsインスタンスを生成
let vm = new Vue({
    el: '#app',
    render: h => h(AppComponent)
});

/**
 * アプリケーションの関数定義
 */
var AppFuncs = {

    /**
     * コンテントコンポーネントの設定
     * @param {String} componentId コンポーネントID
     */
    applyContent: function (componentId) {

        vm.$nextTick(function () {
            // $root要素の子要素はAppコンポーネントになるので、そちらのデータにコンポーネントIDを引き渡す
            vm.$root.$children[0].componentId = componentId;
        });
    }

};

window.AppFuncs = AppFuncs;
