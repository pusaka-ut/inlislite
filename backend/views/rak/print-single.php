<?php

use yii\helpers\Html;

$locationName = $model->location ? $model->location->Name : 'Perpustakaan Pusat UT';
$ddcRange = (!empty($model->range_ddc_awal) && !empty($model->range_ddc_akhir)) ? $model->range_ddc_awal . ' - ' . $model->range_ddc_akhir : null;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Stiker QR Code Rak - <?= Html::encode($model->kode_rak) ?></title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        body {
            background-color: #f4f6f9;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
        }
        .toolbar {
            margin-bottom: 20px;
            display: flex;
            gap: 12px;
            align-items: center;
        }
        .btn {
            display: inline-block;
            padding: 8px 18px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            border-radius: 4px;
            cursor: pointer;
            border: none;
        }
        .btn-print {
            background-color: #003366;
            color: #ffffff;
        }
        .btn-print:hover {
            background-color: #002244;
        }
        .btn-close {
            background-color: #e0e0e0;
            color: #333333;
        }
        .sticker-card {
            width: 140mm;
            min-height: 85mm;
            background: #ffffff;
            border: 2px solid #003366;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            position: relative;
        }
        .sticker-header {
            background: linear-gradient(135deg, #003366 0%, #004c99 100%);
            color: #ffffff;
            padding: 8px 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 3px solid #ffcc00;
        }
        .header-title {
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .header-sub {
            font-size: 9px;
            opacity: 0.9;
        }
        .sticker-body {
            padding: 12px;
            display: flex;
            flex: 1;
            align-items: center;
            gap: 14px;
        }
        .info-col {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .rack-code-badge {
            display: inline-block;
            align-self: flex-start;
            background-color: #ffcc00;
            color: #003366;
            font-size: 11px;
            font-weight: 800;
            padding: 2px 8px;
            border-radius: 4px;
            margin-bottom: 6px;
            letter-spacing: 0.5px;
        }
        .rack-name {
            font-size: 16px;
            font-weight: 800;
            color: #003366;
            line-height: 1.25;
            margin-bottom: 6px;
        }
        .rack-location {
            font-size: 11px;
            color: #555555;
            margin-bottom: 6px;
            font-weight: 600;
        }
        .rack-ddc {
            background: #eef4fc;
            border-left: 3px solid #003366;
            padding: 4px 8px;
            font-size: 11px;
            color: #003366;
            font-weight: 700;
            margin-bottom: 8px;
            border-radius: 0 4px 4px 0;
        }
        .scan-instruction {
            font-size: 9px;
            color: #777777;
            line-height: 1.3;
        }
        .qr-col {
            width: 44mm;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding-left: 10px;
            border-left: 1px dashed #d0d0d0;
        }
        .qr-image {
            width: 38mm;
            height: 38mm;
            display: block;
        }
        .scan-label {
            margin-top: 4px;
            font-size: 9px;
            font-weight: 800;
            color: #003366;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .sticker-footer {
            background: #f8fafc;
            border-top: 1px solid #eeeeee;
            padding: 4px 14px;
            display: flex;
            justify-content: space-between;
            font-size: 8px;
            color: #888888;
        }
        @media print {
            body {
                background: none;
                padding: 0;
            }
            .toolbar {
                display: none !important;
            }
            .sticker-card {
                box-shadow: none;
                page-break-inside: avoid;
                margin: 0;
                border: 1.5px solid #003366;
            }
            @page {
                size: auto;
                margin: 5mm;
            }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <button class="btn btn-print" onclick="window.print()">
            Cetak Label Stiker Ini
        </button>
        <button class="btn btn-close" onclick="window.close()">
            Tutup
        </button>
    </div>

    <div class="sticker-card">
        <div class="sticker-header">
            <div>
                <div class="header-title">PERPUSTAKAAN UNIVERSITAS TERBUKA</div>
                <div class="header-sub">Sistem Otomasi & Navigasi Rak Fisik Koleksi</div>
            </div>
            <div style="font-size: 14px; font-weight: 900; color: #ffcc00; letter-spacing: 1px;">UT</div>
        </div>

        <div class="sticker-body">
            <div class="info-col">
                <div class="rack-code-badge"><?= Html::encode($model->kode_rak) ?></div>
                <div class="rack-name"><?= Html::encode($model->nama_rak) ?></div>
                <div class="rack-location"><?= Html::encode($locationName) ?></div>
                <?php if ($ddcRange): ?>
                    <div class="rack-ddc">Klasifikasi DDC: <?= Html::encode($ddcRange) ?></div>
                <?php elseif ($model->call_number_prefix): ?>
                    <div class="rack-ddc">Prefix Koleksi: <?= Html::encode($model->call_number_prefix) ?>*</div>
                <?php endif; ?>
                <div class="scan-instruction">
                    Arahkan kamera smartphone ke QR Code untuk melihat daftar buku lengkap beserta ketersediaannya di rak ini.
                </div>
            </div>

            <div class="qr-col">
                <img src="<?= $qrDataUri ?>" class="qr-image" alt="QR Code Rak" />
                <div class="scan-label">Pindai di Sini</div>
            </div>
        </div>

        <div class="sticker-footer">
            <span>Perpustakaan Pusat UT - Gedung Perpustakaan Lantai 1-4</span>
            <span><?= Html::encode($model->kode_rak) ?></span>
        </div>
    </div>
</body>
</html>
