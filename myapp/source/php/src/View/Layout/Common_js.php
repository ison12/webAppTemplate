<?php

use App\Common\Util\ViewUtil;
?>

<?php /* グローバル関連の定義 */ ?>
<script type="text/javascript">

    // アプリケーションコンテキストの定義
    var AppContext = Object.freeze({
        name: 'My App',
        environment: '<?= $__environment ?>',
        baseUrl: '<?= $__baseUrl ?>',
        requestParams: <?= json_encode($__requestParams) ?>,
        user: <?= json_encode($__user) ?>,
        contentsData: <?= json_encode($__contentsData) ?>
    });

</script>

<?php /* バンドルファイルの読み込み */ ?>
<script src="<?= $__baseUrl . ViewUtil::getUrlWithFileTimestamp('/dist/bundle.js') ?>" ></script>

<?php /* VueJsの初期化 */ ?>
<script type="text/javascript">
    var componentId = "<?= $__contentsViewName ?>";
    if (componentId && componentId !== "") {
        componentId += "Component";
    }

    window.AppFuncs.applyContent(componentId, <?= $__isVisibleHeader ?>, <?= $__isVisibleFooter ?>);
</script>
