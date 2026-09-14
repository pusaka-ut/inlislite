<?php

use yii\helpers\Html;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Massal Stiker QR Code Rak Perpustakaan UT</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        body {
            background-color: #eef2f6;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
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
        .btn-close {
            background-color: #e0e0e0;
            color: #333333;
        }
        .a4-sheet {
            width: 210mm;
            min-height: 297mm;
            background: #ffffff;
            padding: 10mm;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            grid-gap: 8mm;
            align-content: start;
        }
        .sticker-card {
            border: 1.5px dashed #003366;
            border-radius: 6px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            background: #ffffff;
            height: 80mm;
        }
        .sticker-header {
            background: #003366;
            color: #ffffff;
            padding: 6px 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 2px solid #ffcc00;
        }
        .header-title {
            font-size: 9px;
            font-weight: 800;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .sticker-body {
            padding: 8px;
            display: flex;
            flex: 1;
            align-items: center;
            gap: 10px;
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
            font-size: 10px;
            font-weight: 800;
            padding: 1px 6px;
            border-radius: 3px;
            margin-bottom: 4px;
        }
        .rack-name {
            font-size: 13px;
            font-weight: 800;
            color: #003366;
            line-height: 1.2;
            margin-bottom: 4px;
        }
        .rack-location {
            font-size: 10px;
            color: #555555;
            margin-bottom: 4px;
            font-weight: 600;
        }
        .rack-ddc {
            background: #eef4fc;
            border-left: 2.5px solid #003366;
            padding: 2px 6px;
            font-size: 10px;
            color: #003366;
            font-weight: 700;
            margin-bottom: 6px;
        }
        .scan-instruction {
            font-size: 8px;
            color: #777777;
            line-height: 1.2;
        }
        .qr-col {
            width: 34mm;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding-left: 6px;
            border-left: 1px dashed #d0d0d0;
        }
        .qr-image {
            width: 30mm;
            height: 30mm;
            display: block;
        }
        .scan-label {
            margin-top: 2px;
            font-size: 8px;
            font-weight: 800;
            color: #003366;
            text-transform: uppercase;
        }
        .sticker-footer {
            background: #f8fafc;
            border-top: 1px solid #eeeeee;
            padding: 3px 10px;
            display: flex;
            justify-content: space-between;
            font-size: 7.5px;
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
            .a4-sheet {
                box-shadow: none;
                padding: 5mm;
                margin: 0;
                width: 100%;
                page-break-after: always;
            }
            @page {
                size: A4 portrait;
                margin: 0;
            }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <button class="btn btn-print" onclick="window.print()">
            Cetak Semua Stiker (A4)
        </button>
        <button class="btn btn-close" onclick="window.close()">
            Tutup
        </button>
    </div>

    <div class="a4-sheet">
        <?php foreach ($items as $item): ?>
            <?php 
                $m = $item['model'];
                $locName = $m->location ? $m->location->Name : 'Perpustakaan UT';
                $range = (!empty($m->range_ddc_awal) && !empty($m->range_ddc_akhir)) ? $m->range_ddc_awal . ' - ' . $m->range_ddc_akhir : null;
            ?>
            <div class="sticker-card">
                <div class="sticker-header">
                    <span class="header-title">PERPUSTAKAAN UNIVERSITAS TERBUKA</span>
                    <span style="font-size: 11px; font-weight: 900; color: #ffcc00;">UT</span>
                </div>
                <div class="sticker-body">
                    <div class="info-col">
                        <div class="rack-code-badge"><?= Html::encode($m->kode_rak) ?></div>
                        <div class="rack-name"><?= Html::encode($m->nama_rak) ?></div>
                        <div class="rack-location"><?= Html::encode($locName) ?></div>
                        <?php if ($range): ?>
                            <div class="rack-ddc">DDC: <?= Html::encode($range) ?></div>
                        <?php elseif ($m->call_number_prefix): ?>
                            <div class="rack-ddc">Prefix: <?= Html::encode($m->call_number_prefix) ?>*</div>
                        <?php endif; ?>
                        <div class="scan-instruction">
                            Pindai QR Code untuk melihat seluruh buku di rak ini secara real-time.
                        </div>
                    </div>
                    <div class="qr-col">
                        <img src="<?= $item['qrDataUri'] ?>" class="qr-image" alt="QR" />
                        <div class="scan-label">Pindai QR</div>
                    </div>
                </div>
                <div class="sticker-footer">
                    <span>Perpustakaan Pusat UT</span>
                    <span><?= Html::encode($m->kode_rak) ?></span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
