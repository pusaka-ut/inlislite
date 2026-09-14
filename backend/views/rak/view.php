<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\components\QrCodeHelper;

$this->title = 'Detail Koleksi: ' . $model->nama_rak;
$this->params['breadcrumbs'][] = ['label' => 'QR Code Rak Buku', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$targetUrl = QrCodeHelper::getPublicRakUrl($model->id);
$qrDataUri = QrCodeHelper::generateDataUri($targetUrl, 200);
?>

<div class="master-rak-view">
    <div class="row">
        <div class="col-md-4">
            <div class="box box-primary">
                <div class="box-body box-profile text-center">
                    <img src="<?= $qrDataUri ?>" alt="QR Code Rak" style="width: 170px; height: 170px; border: 2px solid #003366; padding: 4px; border-radius: 8px; margin-bottom: 12px;" />
                    <h3 class="profile-username text-center" style="font-weight: bold; color: #003366;">
                        <?= Html::encode($model->nama_rak) ?>
                    </h3>
                    <p class="text-muted text-center" style="font-family: monospace; font-size: 14px;">
                        <?= Html::encode($model->kode_rak) ?>
                    </p>

                    <ul class="list-group list-group-unbordered" style="text-align: left;">
                        <li class="list-group-item">
                            <b>Ruang / Lantai</b> <span class="pull-right text-primary font-weight-bold"><?= $model->location ? Html::encode($model->location->Name) : '-' ?></span>
                        </li>
                        <li class="list-group-item">
                            <b>Rentang DDC</b> 
                            <span class="pull-right">
                                <?= (!empty($model->range_ddc_awal) && !empty($model->range_ddc_akhir)) ? Html::encode($model->range_ddc_awal) . ' - ' . Html::encode($model->range_ddc_akhir) : '-' ?>
                            </span>
                        </li>
                        <li class="list-group-item">
                            <b>Prefix Call Number</b> <span class="pull-right"><?= $model->call_number_prefix ? Html::encode($model->call_number_prefix) : '-' ?></span>
                        </li>
                        <li class="list-group-item">
                            <b>Total Judul Buku</b> <span class="pull-right badge bg-green"><?= $model->getTotalJudul() ?> Judul</span>
                        </li>
                        <li class="list-group-item">
                            <b>Total Eksemplar Fisik</b> <span class="pull-right badge bg-blue"><?= $model->getTotalEksemplar() ?> Eksemplar</span>
                        </li>
                    </ul>

                    <div style="margin-top: 15px; display: flex; gap: 6px; justify-content: center;">
                        <?= Html::a('<i class="fa fa-print"></i> Cetak Stiker', ['print-single', 'id' => $model->id], ['class' => 'btn btn-primary btn-flat', 'target' => '_blank']) ?>
                        <?= Html::a('<i class="fa fa-external-link"></i> Buka Tampilan Pemustaka', $targetUrl, ['class' => 'btn btn-info btn-flat', 'target' => '_blank']) ?>
                        <?= Html::a('<i class="fa fa-pencil"></i> Ubah', ['update', 'id' => $model->id], ['class' => 'btn btn-warning btn-flat']) ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title" style="font-weight: bold;">
                        <i class="fa fa-book text-info"></i> Daftar Buku yang Berada di Rak Ini
                    </h3>
                </div>
                <div class="box-body table-responsive">
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'summary' => '<div class="text-muted" style="margin-bottom: 8px;">Menampilkan <b>{begin}-{end}</b> dari <b>{totalCount}</b> eksemplar buku</div>',
                        'tableOptions' => ['class' => 'table table-bordered table-striped table-hover'],
                        'columns' => [
                            ['class' => 'yii\grid\SerialColumn'],
                            [
                                'attribute' => 'NomorBarcode',
                                'label' => 'Barcode',
                                'format' => 'raw',
                                'value' => function ($col) {
                                    return '<code>' . Html::encode($col->NomorBarcode) . '</code>';
                                }
                            ],
                            [
                                'attribute' => 'CallNumber',
                                'label' => 'Nomor Panggil',
                                'format' => 'raw',
                                'value' => function ($col) {
                                    return '<b style="color: #003366;">' . Html::encode($col->CallNumber) . '</b>';
                                }
                            ],
                            [
                                'label' => 'Judul Buku & Pengarang',
                                'format' => 'raw',
                                'value' => function ($col) {
                                    if (!$col->catalog) {
                                        return '-';
                                    }
                                    return '<strong>' . Html::encode($col->catalog->Title) . '</strong><br>' .
                                        '<small class="text-muted">' . Html::encode($col->catalog->Author) . ' (' . Html::encode($col->catalog->PublishYear) . ')</small>';
                                }
                            ],
                            [
                                'label' => 'Status',
                                'format' => 'raw',
                                'value' => function ($col) {
                                    $statusName = $col->status ? $col->status->Name : 'Tersedia';
                                    if (strpos(strtolower($statusName), 'pinjam') !== false) {
                                        return '<span class="label label-danger">' . Html::encode($statusName) . '</span>';
                                    }
                                    return '<span class="label label-success">' . Html::encode($statusName) . '</span>';
                                }
                            ],
                        ],
                    ]); ?>
                </div>
            </div>
        </div>
    </div>
</div>
