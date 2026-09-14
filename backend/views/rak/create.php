<?php

use yii\helpers\Html;

$this->title = 'Tambah Master Rak Buku';
$this->params['breadcrumbs'][] = ['label' => 'QR Code Rak Buku', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="master-rak-create">
    <div class="box-header with-border" style="padding-left:0; margin-bottom: 15px;">
        <h3 class="box-title" style="font-size: 22px; font-weight: bold; color: #003366;">
            <i class="fa fa-plus-circle text-primary"></i> <?= Html::encode($this->title) ?>
        </h3>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
        'locations' => $locations,
    ]) ?>
</div>
