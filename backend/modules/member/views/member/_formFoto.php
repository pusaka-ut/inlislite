<?php
use yii\helpers\Html;
use yii\helpers\Url;

$csrfParam = Yii::$app->request->csrfParam;
$csrfToken = Yii::$app->request->csrfToken;
$uploadUrl = Url::to(['/member/member/upload-foto-anggota', 'id' => $model->ID]);
$saveUrl = Url::to(['save-foto', 'id' => $model->ID]);
$currentPhotoUrl = $model->getImageUrl();
?>
<div class="row" style="margin-top: 10px;">
    <div class="col-md-5">
        <div class="box box-solid" style="border: 1px solid #e2e8f0; border-radius: 10px; padding: 20px; background: #ffffff; box-shadow: 0 2px 8px rgba(0, 43, 85, 0.05); min-height: 440px;">
            <h4 style="margin-top: 0; margin-bottom: 14px; font-weight: 700; color: #002b55;">
                <i class="glyphicon glyphicon-camera" style="color: #004080; margin-right: 6px;"></i><?= Yii::t('app', 'Ambil Foto dari Kamera') ?>
            </h4>
            <div id="frameFoto" class="img-frame text-center" style="min-height: 250px; display: flex; flex-direction: column; align-items: center; justify-content: center;">
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
                    $('#post_take_buttons button').prop('disabled', true);
                    $.ajax({
                        type: 'POST',
                        url: '{$saveUrl}',
                        data: {
                            imgBase64: dataUrl,
                            '{$csrfParam}': '{$csrfToken}'
                        },
                        dataType: 'json'
                    }).done(function() {
                        location.reload();
                    }).fail(function() {
                        $('#post_take_buttons button').prop('disabled', false);
                        swal({
                            title: '',
                            type: 'error',
                            text: '" . Yii::t('app', 'Gagal menyimpan foto dari kamera.') . "'
                        });
                    });
                };
            ", yii\web\View::POS_END);
            ?>
        </div>
    </div>
    <div class="col-md-7">
        <div class="box box-solid" style="border: 1px solid #e2e8f0; border-radius: 10px; padding: 20px; background: #ffffff; box-shadow: 0 2px 8px rgba(0, 43, 85, 0.05); min-height: 440px;">
            <h4 style="margin-top: 0; margin-bottom: 6px; font-weight: 700; color: #002b55;">
                <i class="glyphicon glyphicon-picture" style="color: #004080; margin-right: 6px;"></i><?= Yii::t('app', 'Unggah Berkas Foto Anggota') ?>
            </h4>
            <p class="text-muted" style="font-size: 12.5px; margin-bottom: 16px;">
                <?= Yii::t('app', 'Pilih file foto berformat JPG, JPEG, PNG, atau WEBP. Ukuran file maksimal 5 MB.') ?>
            </p>

            <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 16px; background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 8px;">
                <div>
                    <span id="photo_badge" class="label label-info" style="font-size: 11px; font-weight: 600; padding: 4px 10px; border-radius: 9999px;">
                        <?= Yii::t('app', 'Foto Saat Ini') ?>
                    </span>
                </div>
                <div style="margin-top: 10px; min-height: 200px; display: flex; align-items: center; justify-content: center;">
                    <img id="photo_preview_img" src="<?= Html::encode($currentPhotoUrl) ?>" alt="Foto Anggota" style="max-width: 180px; max-height: 220px; border-radius: 6px; box-shadow: 0 4px 12px rgba(0, 43, 85, 0.1); border: 2px solid #ffffff; object-fit: contain;">
                </div>
                <div id="file_info_text" style="display: none; margin-top: 10px; font-size: 12px; color: #334155; font-weight: 600;"></div>
            </div>

            <input type="file" id="member_photo_input" accept="image/jpeg,image/png,image/jpg,image/webp" style="display: none;">

            <div style="display: flex; gap: 8px; justify-content: center; align-items: center; margin-top: 16px; flex-wrap: wrap;">
                <button type="button" id="btn_choose_photo" class="btn btn-primary" style="border-radius: 6px; font-weight: 600; padding: 7px 16px;">
                    <i class="glyphicon glyphicon-folder-open" style="margin-right: 4px;"></i> <?= Yii::t('app', 'Pilih Berkas Foto') ?>
                </button>
                <button type="button" id="btn_upload_photo" class="btn btn-success" style="display: none; border-radius: 6px; font-weight: 600; padding: 7px 16px;">
                    <i class="glyphicon glyphicon-floppy-disk" style="margin-right: 4px;"></i> <?= Yii::t('app', 'Unggah & Simpan Foto') ?>
                </button>
                <button type="button" id="btn_cancel_photo" class="btn btn-default" style="display: none; border-radius: 6px; padding: 7px 14px;">
                    <i class="glyphicon glyphicon-remove" style="margin-right: 4px;"></i> <?= Yii::t('app', 'Batal') ?>
                </button>
            </div>
            <p class="text-muted text-center" style="font-size: 12px; margin-top: 14px; margin-bottom: 0; color: #64748b;">
                <i class="glyphicon glyphicon-info-sign"></i> <?= Yii::t('app', 'Pratinjau foto akan muncul langsung setelah berkas dipilih sebelum disimpan.') ?>
            </p>
        </div>
    </div>
</div>

<?php
$this->registerJs("
    var originalPhotoUrl = '" . $currentPhotoUrl . "';
    var selectedPhotoFile = null;

    $('#btn_choose_photo').on('click', function() {
        $('#member_photo_input').trigger('click');
    });

    $('#member_photo_input').on('change', function() {
        var files = this.files;
        if (!files || !files[0]) {
            return;
        }
        var file = files[0];
        if (file.size > 5242880) {
            swal({
                title: '',
                type: 'error',
                text: '" . Yii::t('app', 'Ukuran berkas melebihi batas maksimal 5 MB.') . "'
            });
            $(this).val('');
            return;
        }
        var validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
        if (validTypes.indexOf(file.type) === -1) {
            swal({
                title: '',
                type: 'error',
                text: '" . Yii::t('app', 'Format berkas tidak didukung. Harap pilih berkas JPG, PNG, atau WEBP.') . "'
            });
            $(this).val('');
            return;
        }
        selectedPhotoFile = file;
        var reader = new FileReader();
        reader.onload = function(e) {
            $('#photo_preview_img').attr('src', e.target.result);
        };
        reader.readAsDataURL(file);

        var sizeStr = file.size > 1048576 ? (file.size / 1048576).toFixed(2) + ' MB' : (file.size / 1024).toFixed(1) + ' KB';
        $('#file_info_text').html('<i class=\"glyphicon glyphicon-file\"></i> ' + file.name + ' (' + sizeStr + ')').show();
        $('#photo_badge').removeClass('label-info').addClass('label-success').text('" . Yii::t('app', 'Pratinjau Foto Baru') . "');
        $('#btn_upload_photo').show();
        $('#btn_cancel_photo').show();
        $('#btn_choose_photo').html('<i class=\"glyphicon glyphicon-refresh\"></i> " . Yii::t('app', 'Ganti Berkas') . "');
    });

    $('#btn_cancel_photo').on('click', function() {
        $('#member_photo_input').val('');
        selectedPhotoFile = null;
        $('#photo_preview_img').attr('src', originalPhotoUrl);
        $('#file_info_text').hide().empty();
        $('#photo_badge').removeClass('label-success').addClass('label-info').text('" . Yii::t('app', 'Foto Saat Ini') . "');
        $('#btn_upload_photo').hide();
        $('#btn_cancel_photo').hide();
        $('#btn_choose_photo').html('<i class=\"glyphicon glyphicon-folder-open\"></i> " . Yii::t('app', 'Pilih Berkas Foto') . "');
    });

    $('#btn_upload_photo').on('click', function() {
        if (!selectedPhotoFile) {
            return;
        }
        var formData = new FormData();
        formData.append('image', selectedPhotoFile);
        formData.append('{$csrfParam}', '{$csrfToken}');

        $('#btn_upload_photo').prop('disabled', true).html('<i class=\"fa fa-spinner fa-spin\"></i> " . Yii::t('app', 'Mengunggah...') . "');
        $('#btn_choose_photo').prop('disabled', true);
        $('#btn_cancel_photo').prop('disabled', true);

        $.ajax({
            url: '{$uploadUrl}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response && response.success) {
                    swal({
                        title: '',
                        type: 'success',
                        text: '" . Yii::t('app', 'Foto anggota berhasil diperbarui!') . "',
                        timer: 1600,
                        showConfirmButton: false
                    });
                    setTimeout(function() {
                        location.reload();
                    }, 1400);
                } else {
                    var errorMsg = response && response.error ? response.error : '" . Yii::t('app', 'Gagal mengunggah foto.') . "';
                    swal({
                        title: '',
                        type: 'error',
                        text: errorMsg
                    });
                    $('#btn_upload_photo').prop('disabled', false).html('<i class=\"glyphicon glyphicon-floppy-disk\"></i> " . Yii::t('app', 'Unggah & Simpan Foto') . "');
                    $('#btn_choose_photo').prop('disabled', false);
                    $('#btn_cancel_photo').prop('disabled', false);
                }
            },
            error: function(xhr) {
                var errorMsg = '" . Yii::t('app', 'Terjadi kesalahan pada server saat mengunggah foto.') . "';
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMsg = xhr.responseJSON.error;
                }
                swal({
                    title: '',
                    type: 'error',
                    text: errorMsg
                });
                $('#btn_upload_photo').prop('disabled', false).html('<i class=\"glyphicon glyphicon-floppy-disk\"></i> " . Yii::t('app', 'Unggah & Simpan Foto') . "');
                $('#btn_choose_photo').prop('disabled', false);
                $('#btn_cancel_photo').prop('disabled', false);
            }
        });
    });
", yii\web\View::POS_END);
?>