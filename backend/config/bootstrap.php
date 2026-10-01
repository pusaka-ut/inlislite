<?php

ini_set('memory_limit','2G');
ini_set('max_execution_time', 3000);
ini_set('session.gc_maxlifetime', 21600);

\Yii::$container->set('kartik\grid\GridView', [
    'export' => [
        'target' => \kartik\grid\GridView::TARGET_SELF,
        'showConfirmAlert' => false,
    ],
]);