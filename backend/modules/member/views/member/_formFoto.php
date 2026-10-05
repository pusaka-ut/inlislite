<?php
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\widgets\FileInput;

?>
<div class="row" style="margin-top: 10px;">
    <div class="col-md-5">
        <div class="box box-solid" style="border: 1px solid #e2e8f0; border-radius: 10px; padding: 18px; background: #ffffff; box-shadow: 0 2px 8px rgba(0, 43, 85, 0.05);">
            <h4 style="margin-top: 0; margin-bottom: 14px; font-weight: 700; color: #002b55;">
                <i class="glyphicon glyphicon-camera" style="color: #004080; margin-right: 6px;"></i><?= Yii::t('app', 'Ambil Foto dari Kamera') ?>
            </h4>
            <div id="frameFoto" class="img-frame text-center" style="min-height: 240px; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                <video id="inlis_camera" width="320" height="240" autoplay style="border-radius: 8px; border: 1px solid #cbd5e1; max-width: 100%; height: auto; background: #000000;"></video>
                <canvas id="snapshot" width="320" height="240" style="display: none; border-radius: 8px; border: 1px solid #cbd5e1; max-width: 100%; height: auto;"></canvas>

                <div id="camera_fallback_notice" style="display: none; width: 100%;">
                    <div class="alert alert-info text-left" style="border-radius: 8px; font-size: 13px; line-height: 1.5; margin-bottom: 0;">
                        <i class="glyphicon glyphicon-info-sign" style="font-size: 16px; margin-right: 6px;"></i>
                        <?= Yii::t('app', 'Akses kamera langsung membutuhkan koneksi aman (HTTPS). Untuk koneksi jaringan intranet, silakan gunakan fitur Unggah Berkas Foto di sebelah kanan.') ?>
                    </div>
                </div>

                <div id="pre_take_buttons" style="margin-top: 14px; width: 100%;">
                    <button class="btn btn-primary btn-block" type="button" onClick="preview_snapshot()" style="border-radius: 6px; font-weight: 600;">
                        <span class="glyphicon glyphicon-camera"></span> <?= Yii::t('app', 'Ambil Foto') ?>
                    </button>
                </div>
                <div id="post_take_buttons" style="display: none; margin-top: 14px; width: 100%;">
                    <button class="btn btn-default" type="button" onClick="cancel_preview()" style="border-radius: 6px; margin-right: 6px;">
                        <span class="glyphicon glyphicon-repeat"></span> <?= Yii::t('app', 'Ulangi Foto') ?>
                    </button>
                    <button class="btn btn-success" type="button" onClick="save_photo()" style="border-radius: 6px; font-weight: 600;">
                        <span class="glyphicon glyphicon-save"></span> <?= Yii::t('app', 'Simpan Foto') ?>
                    </button>
                </div>
            </div>
            <?php
            $saveUrl = Url::to(['save-foto', 'id' => $model->ID]);
            $this->registerJs("
                var videoTracks;
                var player = document.getElementById('inlis_camera');
                var snapshotCanvas = document.getElementById('snapshot');
                var preButtons = document.getElementById('pre_take_buttons');
                var postButtons = document.getElementById('post_take_buttons');
                var fallbackNotice = document.getElementById('camera_fallback_notice');

                if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                    navigator.mediaDevices.getUserMedia({video: true}).then(function(stream) {
                        player.srcObject = stream;
                        videoTracks = stream.getVideoTracks();
                    }).catch(function(err) {
                        if (player) player.style.display = 'none';
                        if (preButtons) preButtons.style.display = 'none';
                        if (fallbackNotice) fallbackNotice.style.display = 'block';
                    });
                } else {
                    if (player) player.style.display = 'none';
                    if (preButtons) preButtons.style.display = 'none';
                    if (fallbackNotice) fallbackNotice.style.display = 'block';
                }

                window.preview_snapshot = function() {
                    if (!player || !snapshotCanvas) return;
                    var context = snapshotCanvas.getContext('2d');
                    context.drawImage(player, 0, 0, snapshotCanvas.width, snapshotCanvas.height);
                    $('#snapshot').show();
                    $('#inlis_camera').hide();
                    preButtons.style.display = 'none';
                    postButtons.style.display = 'block';
                    if (videoTracks) {
                        videoTracks.forEach(function(track) { track.stop(); });
                    }
                };

                window.cancel_preview = function() {
                    $('#inlis_camera').show();
                    $('#snapshot').hide();
                    preButtons.style.display = 'block';
                    postButtons.style.display = 'none';
                    if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                        navigator.mediaDevices.getUserMedia({video: true}).then(function(stream) {
                            player.srcObject = stream;
                            videoTracks = stream.getVideoTracks();
                        });
                    }
                };

                window.save_photo = function() {
                    if (!snapshotCanvas) return;
                    var dataUrl = snapshotCanvas.toDataURL('image/jpeg');
                    $.ajax({
                        type: 'POST',
                        url: '{$saveUrl}',
                        data: { imgBase64: dataUrl }
                    }).done(function() {
                        location.reload();
                    });
                };
            ", yii\web\View::POS_END);
            ?>
        </div>
    </div>
    <div class="col-md-7">
        <div class="box box-solid" style="border: 1px solid #e2e8f0; border-radius: 10px; padding: 18px; background: #ffffff; box-shadow: 0 2px 8px rgba(0, 43, 85, 0.05);">
            <h4 style="margin-top: 0; margin-bottom: 6px; font-weight: 700; color: #002b55;">
                <i class="glyphicon glyphicon-picture" style="color: #004080; margin-right: 6px;"></i><?= Yii::t('app', 'Unggah Berkas Foto Anggota') ?>
            </h4>
            <p class="text-muted" style="font-size: 12.5px; margin-bottom: 16px;">
                <?= Yii::t('app', 'Pilih file foto berformat JPG, JPEG, PNG, atau WEBP. Ukuran file maksimal 5 MB.') ?>
            </p>
            <?php echo FileInput::widget([
                'name' => 'image',
                'options' => [
                    'id' => 'member_photo_upload',
                    'accept' => 'image/*'
                ],
                'pluginOptions' => [
                    'showPreview' => true,
                    'showCaption' => true,
                    'showRemove' => true,
                    'showUpload' => true,
                    'browseLabel' => Yii::t('app', 'Pilih Foto'),
                    'browseClass' => 'btn btn-primary',
                    'browseIcon' => '<i class="glyphicon glyphicon-folder-open"></i> ',
                    'removeLabel' => Yii::t('app', 'Batal'),
                    'removeClass' => 'btn btn-default',
                    'uploadLabel' => Yii::t('app', 'Unggah Foto'),
                    'uploadClass' => 'btn btn-success',
                    'uploadIcon' => '<i class="glyphicon glyphicon-upload"></i> ',
                    'uploadUrl' => Url::to(['/member/member/upload-foto-anggota', 'id' => $model->ID]),
                    'allowedFileExtensions' => ['jpg', 'jpeg', 'png', 'webp'],
                    'msgInvalidFileExtension' => Yii::t('app', 'Format berkas "{name}" tidak didukung. Hanya berkas bertipe "{extensions}" yang diperbolehkan.'),
                    'maxFileSize' => 5120,
                    'initialPreviewAsData' => true
                ],
                'pluginEvents' => [
                    'fileuploaded' => 'function(event, data, previewId, index) { location.reload(); }',
                ]
            ]); ?>
        </div>
    </div>
</div>