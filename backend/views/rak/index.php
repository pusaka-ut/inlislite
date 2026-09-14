<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\components\QrCodeHelper;

$this->title = 'Manajemen & QR Code Rak Buku';
$this->params['breadcrumbs'][] = $this->title;

$currentBaseUrl = Yii::$app->request->get('baseUrl', 'http://172.30.14.94/inlislite3');
?>

<div class="master-rak-index box box-info">
    <div class="box-header with-border" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; padding: 15px;">
        <div>
            <h3 class="box-title" style="font-size: 20px; font-weight: bold; color: #003366;">
                <i class="fa fa-qrcode text-primary"></i> Navigasi & Stiker QR Code Rak Fisik
            </h3>
            <p class="text-muted" style="margin: 5px 0 0 0; font-size: 13px;">
                Kelola daftar rak fisik di setiap lantai perpustakaan, cetak label stiker QR code, dan pantau buku di setiap rak.
            </p>
        </div>
        <div style="margin-top: 10px;">
            <?= Html::a('<i class="fa fa-plus"></i> Tambah Rak Baru', ['create'], ['class' => 'btn btn-success btn-flat']) ?>
            <?= Html::a('<i class="fa fa-print"></i> Cetak Semua Stiker (Batch A4)', ['print-batch', 'baseUrl' => $currentBaseUrl], [
                'class' => 'btn btn-primary btn-flat',
                'target' => '_blank',
                'title' => 'Cetak seluruh stiker rak dalam format lembaran A4 siap gunting'
            ]) ?>
        </div>
    </div>

    <div class="box-body" style="background-color: #fcfcfc; border-bottom: 1px solid #eee; padding: 15px;">
        <form method="get" class="form-inline" style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
            <label style="font-weight: 600; color: #444;">
                <i class="fa fa-globe text-info"></i> Target Base URL untuk QR Code:
            </label>
            <input type="text" name="baseUrl" class="form-control" value="<?= Html::encode($currentBaseUrl) ?>" style="min-width: 320px;" placeholder="Contoh: http://172.30.14.94/inlislite3" />
            <button type="submit" class="btn btn-default btn-flat">
                <i class="fa fa-refresh"></i> Terapkan URL
            </button>
            <span class="text-muted" style="font-size: 12px;">(Digunakan pada stiker agar smartphone pemustaka membuka alamat yang tepat)</span>
        </form>
    </div>

    <div class="box-body table-responsive">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'summary' => '<div class="text-muted" style="margin-bottom: 10px;">Menampilkan <b>{begin}-{end}</b> dari <b>{totalCount}</b> rak perpustakaan</div>',
            'tableOptions' => ['class' => 'table table-bordered table-hover table-striped'],
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                [
                    'attribute' => 'kode_rak',
                    'format' => 'raw',
                    'value' => function ($model) {
                        return '<span class="label label-primary" style="font-size: 13px; font-family: monospace;">' . Html::encode($model->kode_rak) . '</span>';
                    }
                ],
                [
                    'attribute' => 'nama_rak',
                    'format' => 'raw',
                    'value' => function ($model) {
                        return '<strong>' . Html::encode($model->nama_rak) . '</strong>' .
                            ($model->keterangan ? '<br><small class="text-muted"><i class="fa fa-info-circle"></i> ' . Html::encode($model->keterangan) . '</small>' : '');
                    }
                ],
                [
                    'attribute' => 'location_id',
                    'value' => function ($model) {
                        return $model->location ? $model->location->Name : '-';
                    }
                ],
                [
                    'label' => 'Cakupan Buku',
                    'format' => 'raw',
                    'value' => function ($model) {
                        $parts = [];
                        if (!empty($model->range_ddc_awal) && !empty($model->range_ddc_akhir)) {
                            $parts[] = '<span class="badge bg-green">DDC ' . Html::encode($model->range_ddc_awal) . ' - ' . Html::encode($model->range_ddc_akhir) . '</span>';
                        }
                        if (!empty($model->call_number_prefix)) {
                            $parts[] = '<span class="badge bg-yellow">Prefix ' . Html::encode($model->call_number_prefix) . '*</span>';
                        }
                        return empty($parts) ? '<span class="text-muted">Semua di Lantai</span>' : implode(' ', $parts);
                    }
                ],
                [
                    'label' => 'Total Koleksi',
                    'format' => 'raw',
                    'value' => function ($model) {
                        $judul = $model->getTotalJudul();
                        $eksemplar = $model->getTotalEksemplar();
                        return '<span class="text-success font-weight-bold"><b>' . $judul . '</b> Judul</span><br><small class="text-muted">' . $eksemplar . ' Eksemplar</small>';
                    }
                ],
                [
                    'label' => 'QR Code',
                    'format' => 'raw',
                    'contentOptions' => ['style' => 'text-align: center; width: 100px;'],
                    'value' => function ($model) use ($currentBaseUrl) {
                        $url = QrCodeHelper::getPublicRakUrl($model->id, $currentBaseUrl);
                        $dataUri = QrCodeHelper::generateDataUri($url, 120);
                        return Html::a(
                            '<img src="' . $dataUri . '" style="width: 55px; height: 55px; border: 1px solid #ddd; padding: 2px; background: #fff;" alt="QR" />',
                            ['print-single', 'id' => $model->id, 'baseUrl' => $currentBaseUrl],
                            ['target' => '_blank', 'title' => 'Klik untuk cetak stiker rak ini']
                        );
                    }
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'header' => 'Aksi Pustakawan',
                    'contentOptions' => ['style' => 'width: 170px; text-align: center; white-space: nowrap;'],
                    'template' => '{print} {view} {update} {delete}',
                    'buttons' => [
                        'print' => function ($url, $model) use ($currentBaseUrl) {
                            return Html::a('<i class="fa fa-print"></i>', ['print-single', 'id' => $model->id, 'baseUrl' => $currentBaseUrl], [
                                'class' => 'btn btn-xs btn-primary btn-flat',
                                'target' => '_blank',
                                'title' => 'Cetak Stiker Label Rak'
                            ]);
                        },
                        'view' => function ($url, $model) {
                            return Html::a('<i class="fa fa-eye"></i>', ['view', 'id' => $model->id], [
                                'class' => 'btn btn-xs btn-info btn-flat',
                                'title' => 'Lihat Daftar Buku di Rak'
                            ]);
                        },
                        'update' => function ($url, $model) {
                            return Html::a('<i class="fa fa-pencil"></i>', ['update', 'id' => $model->id], [
                                'class' => 'btn btn-xs btn-warning btn-flat',
                                'title' => 'Ubah Konfigurasi Rak'
                            ]);
                        },
                        'delete' => function ($url, $model) {
                            return Html::a('<i class="fa fa-trash"></i>', ['delete', 'id' => $model->id], [
                                'class' => 'btn btn-xs btn-danger btn-flat',
                                'title' => 'Hapus Rak',
                                'data' => [
                                    'confirm' => 'Apakah Anda yakin ingin menghapus rak ' . $model->nama_rak . '?',
                                    'method' => 'post',
                                ],
                            ]);
                        }
                    ]
                ],
            ],
        ]); ?>
    </div>
</div>
