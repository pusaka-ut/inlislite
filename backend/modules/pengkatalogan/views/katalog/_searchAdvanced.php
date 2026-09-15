<?php
/**
 * @copyright Copyright &copy; Perpustakaan Nasional RI, 2015
 * @package _form.php
 * @version 1.0.0
 * @author Henry <alvin_vna@yahoo.com>
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\widgets\Pjax;

use kartik\widgets\Select2;
use leandrogehlen\querybuilder\QueryBuilderForm;

/**
 * @var yii\web\View $this
 * @var common\models\MemberSearch $model
 * @var yii\widgets\ActiveForm $form
 */

?>

<div class="row">
    <div class="col-xs-12 col-xs-12">
<?php 

QueryBuilderForm::begin([
    'rules' => $rules,
    'options' => ['data-pjax' => true ],
    'builder' => [
        'id' => 'query-builder',
        'allowGroups' => false,
        'selectPlaceholder'=>yii::t('app','-Pilih Kriteria-'),
        'filters' => [
            //['id' => 'ID', 'label' => 'Id', 'type' => 'integer'],
            ['id' => 'Worksheet_id', 
             'label' => yii::t('app','Jenis Bahan'), 
             'type' => 'integer',
             'input'=> 'select',
             'values'=> ArrayHelper::merge(['0'=>'-Pilih Jenis Bahan-'],ArrayHelper::map(\common\models\Worksheets::find()->addSelect(['ID','(CASE WHEN Keterangan IS NULL THEN Name ELSE CONCAT(Name,\'(\',Keterangan,\')\') END) AS Name'])->orderby('NoUrut ASC')->all(),'ID','Name'))
            ],
            ['id' => 'catalogfilesCount.PunyaKontenDigital', 
             'label' => 'Konten Digital', 
             'type' => 'integer',
             'input'=> 'radio',
             'values'=> ['1'=>'Ada','0'=>'Tidak Ada']
            ],
            ['id' => 'Title', 'label' => 'Judul', 'type' => 'string'],
            ['id' => 'Author', 'label' => 'Pengarang', 'type' => 'string'],
            ['id' => 'PublishLocation', 'label' => 'Tempat Terbit', 'type' => 'string'],
            ['id' => 'Publisher', 'label' => 'Penerbit', 'type' => 'string'],
            ['id' => 'PublishYear', 'label' => 'Tahun Terbit', 'type' => 'integer', 'input'=>'text'],
            ['id' => 'Subject', 'label' => 'Subjek', 'type' => 'string'],
            ['id' => 'catalogs.CallNumber', 'label' => 'Nomor Panggil', 'type' => 'string'],
            ['id' => 'ControlNumber', 'label' => 'Control Number', 'type' => 'string'],
            ['id' => 'BIBID', 'label' => 'BIBID', 'type' => 'string'],
            ['id' => 'ISBN', 'label' => 'ISBN / ISSN', 'type' => 'string'],
            ['id' => 'Edition', 'label' => 'Edisi', 'type' => 'string'],
            ['id' => 'PhysicalDescription', 'label' => 'Deskripsi Fisik', 'type' => 'string'],
            [
                'id' => 'YEAR(catalogfiles.CreateDate)', 
                'label' => 'Tahun Upload', 
                'type' => 'integer',
                'input' => 'text'
            ],
            ['id' => 'catalogs.ID', 'label' => 'Catalog ID', 'type' => 'string'],
            ['id' => 'usercreateby.username', 'label' => 'Operator (Tambah)', 'type' => 'string'],
            ['id' => 'userupdateby.username', 'label' => 'Operator (Ubah Terakhir)', 'type' => 'string'],
            [
                'id' => 'DATE(catalogs.CreateDate)', 
                'label' => 'Tanggal Entri', 
                'type' => 'date',
                'plugin' => 'datepicker',
                'pluginConfig' => [
                    'format' => 'yyyy-mm-dd',
                    'todayBtn' => 'linked',
                    'todayHighlight' => true,
                    'autoclose' => true
                ]
            ],
            [
                'id' => 'DATE(catalogs.UpdateDate)', 
                'label' => 'Tanggal Ubah Terakhir', 
                'type' => 'date',
                'plugin' => 'datepicker',
                'pluginConfig' => [
                    'format' => 'yyyy-mm-dd',
                    'todayBtn' => 'linked',
                    'todayHighlight' => true,
                    'autoclose' => true
                ]
            ],
        ]
    ]
 ])?>
 <input type="hidden" name="for" value="<?=$for?>">
  <div class="form-group pull-right query-action-buttons" style="margin-top: 12px; margin-bottom: 14px; display: inline-flex; align-items: center; gap: 8px;">
      <?= Html::submitButton('<i class="glyphicon glyphicon-search"></i> '.Yii::t('app','Cari') , ['class' => 'btn btn-primary btn-sm btn-search-query', 'style' => 'background: linear-gradient(135deg, #002b55, #004080) !important; border-color: #002b55 !important; border-radius: 6px !important; padding: 6px 18px !important; font-weight: 600 !important; box-shadow: 0 2px 6px rgba(0,43,85,0.2) !important; margin-right: 6px !important;']); ?>
      <?= Html::a('<i class="glyphicon glyphicon-repeat"></i> '.Yii::t('app','Ulangi'), ['index'], ['class' => 'btn btn-default btn-sm btn-reset-query', 'style' => 'border-radius: 6px !important; padding: 6px 16px !important; font-weight: 600 !important; background: #ffffff !important; border: 1px solid #cbd5e1 !important; color: #475569 !important;']); ?>
  </div>
  <div class="clearfix"></div>
 <?php QueryBuilderForm::end() ?>
 </div>

</div>
