/* global AppContext */

import VueJsCommonMixin from "Front/Common/VueJsMixin/VueJsCommonMixin.js";

export default {
    // ----------------------------------------------------
    // Mixin
    // ----------------------------------------------------
    mixins: [VueJsCommonMixin],
    // ----------------------------------------------------
    // ローカルコンポーネント
    // ----------------------------------------------------
    components: {
    },
    // ----------------------------------------------------
    // プロパティ
    // ----------------------------------------------------
    props: {
    },
    // ----------------------------------------------------
    // データ
    // ----------------------------------------------------
    data() {
        return {
        };
    },
    // ----------------------------------------------------
    // ライフサイクルフック
    // ----------------------------------------------------
    created() {
    },
    mounted() {
    },
    // ----------------------------------------------------
    // メソッド
    // ----------------------------------------------------
    methods: {
        // ------------------------------------
        // イベントの定義
        // ------------------------------------
        /**
         * DBキャッシュクリア処理
         */
        onDebugDBCacheClear() {

            this.getAjax().ajax({
                context: this,
                url: AppContext.baseUrl + "/debug/cacheClear/dbCacheClear",
                data: AppContext.$requestParams
            }).done(function (data, textStatus, jqXHR) {

                if (data.errors === null || data.errors.length <= 0) {
                    this.getAlertComponent().showInfo(
                            "DBキャッシュクリア成功",
                            "正常にDBのキャッシュがクリアされました。"
                            );
                } else {
                    this.getAlertComponent().showError(
                            "DBキャッシュクリア失敗",
                            data.errors
                            );
                }

            }).catch(function () {

            });

            return false;
        },
        /**
         * ユーザーの削除処理
         */
        onDeleteUser() {

            // ユーザー退会処理
            const userDeleteProcess = () => {

                this.getAjax().ajax({
                    context: this,
                    url: AppContext.baseUrl + "/user/delete/exec",
                    data: AppContext.$requestParams
                }).done(function (data, textStatus, jqXHR) {

                    if (data.errors === null || data.errors.length <= 0) {
                        this.getAlertComponent().showInfo(
                                "退会完了",
                                "退会が完了しました。",
                                {
                                    close: {
                                        text: "閉じる",
                                        onClick() {
                                            return true;
                                        },
                                        onCloseComplete() {
                                            window.location.href = AppContext.baseUrl + "/logout";
                                        }
                                    }
                                }
                        );
                    } else {
                        this.getAlertComponent().showError(
                                "退会失敗",
                                data.error
                                );
                    }

                }).catch(function () {

                });
            };

            // 確認メッセージ
            this.getConfirmComponent().showWarn(
                    "退会確認",
                    AppContext.name + "を退会します。今後、本サイトのサービスが使用できなくなりますが問題ありませんか？",
                    {
                        positive: {
                            text: "退会する",
                            onClick() {
                                userDeleteProcess();
                                return true;
                            },
                            onCloseComplete() { }
                        },
                        negative: {
                            text: "やっぱり退会しません",
                            onClick() {
                                return true;
                            },
                            onCloseComplete() { }
                        }
                    });


            return false;
        }
    },
    // ----------------------------------------------------
    // 計算項目
    // ----------------------------------------------------
    computed: {
    }
}
