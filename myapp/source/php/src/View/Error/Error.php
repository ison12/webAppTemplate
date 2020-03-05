<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title><?= $summary ?></title>
        <style>
            body {
                margin: 0;
                padding: 30px;
                font: 12px/1.5 Helvetica,Arial,Verdana,sans-serif;
            }
            h1 {
                margin: 0;
                font-size: 48px;
                font-weight: normal;
                line-height: 48px;
            }
            strong {
                display: inline-block;
                width: 65px;
            }
        </style>
    </head>
    <body>
        <h1><?= $summary ?></h1>
        <p><?= $message ?? '' ?></p>
    </body>
</html>
