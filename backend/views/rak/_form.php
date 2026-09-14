<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

$locationItems = ArrayHelper::map($locations, 'ID', 'Name');
?>

<div class="master-rak-form box box-primary">
    <div class="box-body">
        <?php $form = ActiveForm::begin(); ?>

        <div class="row">
            <div class="col-md-4">
                <?= $form->field($model, 'kode_rak')->textInput(['maxlength' => true, 'placeholder' => 'Contoh: RAK-L2-01']) ?>
            </div>
            <div class="col-md-8">
                <?= $form->field($model, 'nama_rak')->textInput(['maxlength' => true, 'placeholder' => 'Contoh: Rak 01 - Ilmu Sosial & Ekonomi']) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'location_id')->dropDownList($locationItems, ['prompt' => '-- Pilih Ruangan / Lantai --']) ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, 'range_ddc_awal')->textInput(['maxlength' => true, 'placeholder' => 'Contoh: 330']) ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, 'range_ddc_akhir')->textInput(['maxlength' => true, 'placeholder' => 'Contoh: 339.99']) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'call_number_prefix')->textInput(['maxlength' => true, 'placeholder' => 'Opsional. Contoh: FP untuk Fiksi, UT untuk Buku Materi Pokok']) ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'keterangan')->textarea(['rows' => 2, 'placeholder' => 'Keterangan letak rak fisik (misal: Baris 1 Sebelah Kanan Jendela)']) ?>
            </div>
        </div>

        <div class="form-group" style="margin-top: 15px;">
            <?= Html::submitButton($model->isNewRecord ? '<i class="fa fa-save"></i> Simpan Rak' : '<i class="fa fa-save"></i> Perbarui Rak', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            <?= Html::a('<i class="fa fa-arrow-left"></i> Kembali', ['index'], ['class' => 'btn btn-default']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>
