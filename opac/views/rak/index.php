<?php

use yii\helpers\Html;
use yii\helpers\Url;

$locationName = $model->location ? $model->location->Name : 'Perpustakaan UT';
$ddcRange = (!empty($model->range_ddc_awal) && !empty($model->range_ddc_akhir)) ? $model->range_ddc_awal . ' - ' . $model->range_ddc_akhir : null;
$totalCount = count($collections);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= Html::encode($model->nama_rak) ?> - Perpustakaan Universitas Terbuka</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            -webkit-tap-highlight-color: transparent;
        }
        body {
            background-color: #f4f7fa;
            color: #2c3e50;
            line-height: 1.5;
            padding-bottom: 40px;
        }
        .header-nav {
            background: linear-gradient(135deg, #002b55 0%, #004080 100%);
            color: #ffffff;
            padding: 16px 16px 20px 16px;
            border-bottom: 3px solid #ffcc00;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 4px 12px rgba(0, 43, 85, 0.15);
        }
        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }
        .brand-badge {
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            color: #ffffff;
        }
        .ut-pill {
            background: #ffcc00;
            color: #002b55;
            font-weight: 900;
            font-size: 11px;
            padding: 2px 7px;
            border-radius: 4px;
            letter-spacing: 0.5px;
        }
        .brand-title {
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 0.2px;
        }
        .opac-link {
            font-size: 12px;
            color: #ffcc00;
            text-decoration: none;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .rack-banner {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            padding: 12px 14px;
            backdrop-filter: blur(8px);
        }
        .rack-code {
            font-size: 11px;
            font-weight: 800;
            color: #ffcc00;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .rack-name {
            font-size: 18px;
            font-weight: 800;
            color: #ffffff;
            margin: 2px 0 4px 0;
            line-height: 1.3;
        }
        .rack-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            font-size: 11px;
            color: #d8e5f3;
        }
        .rack-meta-item {
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .container {
            max-width: 680px;
            margin: 0 auto;
            padding: 14px 14px 0 14px;
        }
        .search-box {
            background: #ffffff;
            border-radius: 10px;
            padding: 6px 12px;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            border: 1.5px solid #e1e8f0;
            margin-bottom: 12px;
        }
        .search-box:focus-within {
            border-color: #004080;
            box-shadow: 0 0 0 3px rgba(0, 64, 128, 0.1);
        }
        .search-box i {
            color: #8c9ba5;
            font-size: 14px;
        }
        .search-input {
            border: none;
            outline: none;
            width: 100%;
            font-size: 14px;
            color: #2c3e50;
            background: transparent;
        }
        .filters {
            display: flex;
            gap: 8px;
            overflow-x: auto;
            padding-bottom: 4px;
            margin-bottom: 14px;
            scrollbar-width: none;
        }
        .filters::-webkit-scrollbar {
            display: none;
        }
        .filter-chip {
            white-space: nowrap;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
            color: #4a5d6e;
            background: #ffffff;
            border: 1px solid #dce4ec;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: all 0.2s ease;
        }
        .filter-chip.active {
            background: #004080;
            color: #ffffff;
            border-color: #004080;
        }
        .results-count {
            font-size: 12px;
            color: #6a7c8c;
            margin-bottom: 10px;
            font-weight: 600;
        }
        .book-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .book-card {
            background: #ffffff;
            border-radius: 12px;
            padding: 14px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.04);
            border: 1px solid #e9edf2;
            display: flex;
            gap: 12px;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }
        .book-card:active {
            transform: scale(0.99);
        }
        .book-cover {
            width: 72px;
            height: 102px;
            border-radius: 6px;
            object-fit: cover;
            background: linear-gradient(135deg, #002b55 0%, #004080 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffcc00;
            font-size: 24px;
            flex-shrink: 0;
            box-shadow: 0 2px 6px rgba(0,0,0,0.12);
        }
        .book-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .book-title {
            font-size: 14px;
            font-weight: 700;
            color: #002b55;
            line-height: 1.35;
            margin-bottom: 3px;
        }
        .book-author {
            font-size: 12px;
            color: #556677;
            margin-bottom: 6px;
        }
        .call-number-box {
            background: #fff9e6;
            border: 1px solid #ffcc00;
            border-radius: 6px;
            padding: 4px 8px;
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .call-number-label {
            font-size: 9px;
            font-weight: 800;
            color: #996600;
            text-transform: uppercase;
        }
        .call-number-value {
            font-size: 13px;
            font-weight: 800;
            color: #002b55;
            font-family: monospace;
        }
        .book-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 4px;
        }
        .status-badge {
            font-size: 11px;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 4px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .status-tersedia {
            background: #e6f8ee;
            color: #0d8244;
        }
        .status-dipinjam {
            background: #feecee;
            color: #c52233;
        }
        .btn-detail {
            font-size: 11px;
            font-weight: 700;
            color: #004080;
            text-decoration: none;
            background: #edf3fa;
            padding: 4px 9px;
            border-radius: 5px;
            display: inline-flex;
            align-items: center;
            gap: 3px;
        }
        .btn-detail:hover {
            background: #dbe7f5;
        }
        .empty-state {
            background: #ffffff;
            border-radius: 12px;
            padding: 36px 20px;
            text-align: center;
            border: 1px solid #e9edf2;
            margin-top: 10px;
        }
        .empty-icon {
            font-size: 40px;
            color: #a8b8c5;
            margin-bottom: 12px;
        }
        .empty-title {
            font-size: 15px;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 4px;
        }
        .empty-desc {
            font-size: 12px;
            color: #7b8c9c;
        }
        .shelf-note {
            background: #eef4fb;
            border-left: 3px solid #004080;
            border-radius: 0 8px 8px 0;
            padding: 10px 12px;
            font-size: 11px;
            color: #3b5066;
            margin-top: 20px;
            line-height: 1.4;
        }
    </style>
</head>
<body>
    <header class="header-nav">
        <div class="header-top">
            <a href="<?= Url::to(['/opac']) ?>" class="brand-badge">
                <span class="ut-pill">UT</span>
                <span class="brand-title">Perpustakaan Universitas Terbuka</span>
            </a>
            <a href="<?= Url::to(['/opac']) ?>" class="opac-link">
                <i class="fa fa-home"></i> OPAC
            </a>
        </div>

        <div class="rack-banner">
            <div class="rack-code"><?= Html::encode($model->kode_rak) ?></div>
            <h1 class="rack-name"><?= Html::encode($model->nama_rak) ?></h1>
            <div class="rack-meta">
                <span class="rack-meta-item">
                    <i class="fa fa-map-marker" style="color:#ffcc00;"></i> <?= Html::encode($locationName) ?>
                </span>
                <?php if ($ddcRange): ?>
                    <span class="rack-meta-item">
                        <i class="fa fa-bookmark" style="color:#ffcc00;"></i> DDC <?= Html::encode($ddcRange) ?>
                    </span>
                <?php endif; ?>
                <span class="rack-meta-item">
                    <i class="fa fa-book" style="color:#ffcc00;"></i> <?= $totalAll ?> Buku Terdaftar
                </span>
            </div>
        </div>
    </header>

    <main class="container">
        <div class="search-box">
            <i class="fa fa-search"></i>
            <input type="text" id="liveSearchInput" class="search-input" placeholder="Cari judul, pengarang, atau nomor panggil di rak ini..." value="<?= Html::encode($q) ?>">
        </div>

        <div class="filters">
            <a href="<?= Url::to(['/opac/rak', 'id' => $model->id, 'q' => $q]) ?>" class="filter-chip <?= empty($status) ? 'active' : '' ?>">
                <i class="fa fa-th-large"></i> Semua Koleksi (<?= $totalAll ?>)
            </a>
            <a href="<?= Url::to(['/opac/rak', 'id' => $model->id, 'q' => $q, 'status' => 'tersedia']) ?>" class="filter-chip <?= $status === 'tersedia' ? 'active' : '' ?>">
                <i class="fa fa-check-circle text-success"></i> Tersedia di Rak
            </a>
            <a href="<?= Url::to(['/opac/rak', 'id' => $model->id, 'q' => $q, 'status' => 'dipinjam']) ?>" class="filter-chip <?= $status === 'dipinjam' ? 'active' : '' ?>">
                <i class="fa fa-clock-o text-danger"></i> Sedang Dipinjam
            </a>
        </div>

        <div class="results-count" id="resultsCount">
            Menampilkan <?= $totalCount ?> buku di rak ini
        </div>

        <div class="book-list" id="bookListContainer">
            <?php if (empty($collections)): ?>
                <div class="empty-state">
                    <div class="empty-icon"><i class="fa fa-folder-open-o"></i></div>
                    <div class="empty-title">Tidak Ada Buku Ditemukan</div>
                    <div class="empty-desc">Tidak ada koleksi yang sesuai dengan kriteria filter di rak ini.</div>
                </div>
            <?php else: ?>
                <?php foreach ($collections as $col): ?>
                    <?php 
                        $cat = $col->catalog;
                        $statusName = $col->status ? $col->status->Name : 'Tersedia';
                        $isDipinjam = (strpos(strtolower($statusName), 'pinjam') !== false);
                        $coverUrl = ($cat && !empty($cat->CoverURL)) ? $cat->CoverURL : null;
                        $catalogId = $cat ? $cat->ID : null;
                        $detailUrl = $catalogId ? Url::to(['/opac/detail-opac', 'id' => $catalogId]) : '#';
                        $title = $cat ? $cat->Title : 'Buku Tanpa Judul';
                        $author = ($cat && !empty($cat->Author)) ? $cat->Author : 'Penulis tidak terdata';
                        $year = ($cat && !empty($cat->PublishYear)) ? $cat->PublishYear : null;
                    ?>
                    <article class="book-card" data-title="<?= strtolower(Html::encode($title)) ?>" data-author="<?= strtolower(Html::encode($author)) ?>" data-call="<?= strtolower(Html::encode($col->CallNumber)) ?>" data-barcode="<?= strtolower(Html::encode($col->NomorBarcode)) ?>">
                        <?php if ($coverUrl): ?>
                            <img src="<?= Html::encode($coverUrl) ?>" alt="Cover" class="book-cover" onerror="this.outerHTML='<div class=\'book-cover\'><i class=\'fa fa-book\'></i></div>'">
                        <?php else: ?>
                            <div class="book-cover"><i class="fa fa-book"></i></div>
                        <?php endif; ?>

                        <div class="book-content">
                            <div>
                                <h2 class="book-title"><?= Html::encode($title) ?></h2>
                                <div class="book-author">
                                    <i class="fa fa-user-o"></i> <?= Html::encode($author) ?> <?= $year ? '(' . Html::encode($year) . ')' : '' ?>
                                </div>
                            </div>

                            <div>
                                <div class="call-number-box">
                                    <span class="call-number-label">No. Panggil:</span>
                                    <span class="call-number-value"><?= Html::encode($col->CallNumber) ?></span>
                                </div>

                                <div class="book-footer">
                                    <span class="status-badge <?= $isDipinjam ? 'status-dipinjam' : 'status-tersedia' ?>">
                                        <i class="fa <?= $isDipinjam ? 'fa-times-circle' : 'fa-check-circle' ?>"></i>
                                        <?= Html::encode($statusName) ?>
                                    </span>

                                    <?php if ($catalogId): ?>
                                        <a href="<?= $detailUrl ?>" class="btn-detail" target="_blank">
                                            Rincian Buku <i class="fa fa-angle-right"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="shelf-note">
            <strong><i class="fa fa-info-circle"></i> Panduan Pemustaka:</strong><br>
            Nomor panggil pada kotak kuning di atas adalah kode yang tertera pada stiker punggung buku fisik di rak ini. Jika buku berstatus "Tersedia" namun tidak berada di jajarannya, silakan hubungi pustakawan di meja layanan informasi.
        </div>
    </main>

    <script>
        const searchInput = document.getElementById('liveSearchInput');
        const cards = document.querySelectorAll('.book-card');
        const countLabel = document.getElementById('resultsCount');

        if (searchInput && cards.length > 0) {
            searchInput.addEventListener('input', function() {
                const keyword = this.value.toLowerCase().trim();
                let visibleCount = 0;

                cards.forEach(card => {
                    const title = card.getAttribute('data-title') || '';
                    const author = card.getAttribute('data-author') || '';
                    const call = card.getAttribute('data-call') || '';
                    const barcode = card.getAttribute('data-barcode') || '';

                    if (!keyword || title.includes(keyword) || author.includes(keyword) || call.includes(keyword) || barcode.includes(keyword)) {
                        card.style.display = 'flex';
                        visibleCount++;
                    } else {
                        card.style.display = 'none';
                    }
                });

                if (countLabel) {
                    countLabel.textContent = 'Menampilkan ' + visibleCount + ' dari ' + cards.length + ' buku di rak ini';
                }
            });
        }
    </script>
</body>
</html>
