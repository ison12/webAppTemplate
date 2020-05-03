<?php

use App\Common\Util\ViewUtil;
?>

<?php /* グローバル関連の定義 */ ?>
<script type="text/javascript">

    // アプリケーションコンテキストの定義
    var AppContext = Object.freeze({
        name: 'Myapp',
        environment: '<?= $__environment ?>',
        baseUrl: '<?= $__baseUrl ?>',
        requestParams: <?= json_encode($__requestParams) ?>,
        user: <?= json_encode($__user) ?>,
        contentsData: <?= json_encode($__contentsData) ?>
    });

</script>

<?php /* バンドルファイルの読み込み */ ?>
<?php /* ※本ファイルの読み込み後に、main.jsで定義したグローバル変数などが読み込みできるようになる */ ?>
<script src="<?= $__baseUrl . ViewUtil::getUrlWithFileTimestamp('/dist/bundle.js') ?>" ></script>

<?php /* コンテンツコンポーネントの適用 */ ?>
<script type="text/javascript">

    // コンテンツのコンポーネントIDの取得
    var contentsComponentPath = "<?= $__contentsViewPath ?>";
    var contentsComponentId = "<?= $__contentsViewName ?>";
    if (contentsComponentId && contentsComponentId !== "") {
        contentsComponentId += "Component";
    }

    // コンテンツコンポーネントの適用
    window.AppFuncs.applyContentComponent(contentsComponentPath, contentsComponentId, <?= $__isVisibleHeader ?>, <?= $__isVisibleFooter ?>);

</script>
