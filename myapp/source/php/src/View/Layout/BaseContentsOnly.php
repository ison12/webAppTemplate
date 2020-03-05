<?php

use App\Common\Util\ViewUtil;
use App\Constant\CommonConstant;
?>
<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta http-equiv="Pragma" content="no-cache">
        <meta http-equiv="Cache-Control" content="no-cache">
        <meta http-equiv="Expires" content="Mon, 26 Jul 1997 05:00:00 GMT">
        <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale = 1.0, maximum-scale = 1.0, user-scalable = no">

        <title><?= CommonConstant::APP_NAME ?></title>

        <?=
        ViewUtil::render(__DIR__ . DIRECTORY_SEPARATOR . 'Common_css.php'
                , ['__environment' => $__environment, '__baseUrl' => $__baseUrl, '__requestParams' => $__requestParams, '__user' => $__user, '__contentsViewPath' => $__contentsViewPath, '__contentsViewName' => $__contentsViewName, '__contentsData' => $__contentsData, '__isVisibleHeader' => true, '__isVisibleFooter' => true])
        ?>

    </head>

    <body class="layout-base-contents-only">
        <div id="app" v-cloak>
        </div>
        <?=
        ViewUtil::render(__DIR__ . DIRECTORY_SEPARATOR . 'Common_js.php'
                , ['__environment' => $__environment, '__baseUrl' => $__baseUrl, '__requestParams' => $__requestParams, '__user' => $__user, '__contentsViewPath' => $__contentsViewPath, '__contentsViewName' => $__contentsViewName, '__contentsData' => $__contentsData, '__isVisibleHeader' => true, '__isVisibleFooter' => true])
        ?>
    </body>

</html>