<?php

use yii\helpers\Html;

$this->title = 'Ubah Master Rak: ' . $model->nama_rak;
$this->params['breadcrumbs'][] = ['label' => 'QR Code Rak Buku', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->nama_rak, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Ubah';
?>
<div class="master-rak-update">
    <div class="box-header with-border" style="padding-left:0; margin-bottom: 15px;">
        <h3 class="box-title" style="font-size: 22px; font-weight: bold; color: #003366;">
            <i class="fa fa-pencil text-warning"></i> <?= Html::encode($this->title) ?>
        </h3>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
        'locations' => $locations,
    ]) ?>
</div>
