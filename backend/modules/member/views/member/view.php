<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\datecontrol\DateControl;
use yii\helpers\Url;

$this->title = $model->Fullname ? $model->Fullname : $model->MemberNo;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Members'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="members-view">
    <p>
        <?= Html::a('<i class="glyphicon glyphicon-arrow-left"></i> ' . Yii::t('app', 'Kembali'), ['index'], ['class' => 'btn btn-warning btn-sm', 'style' => 'border-radius: 6px;']) ?>
        <?= Html::a('<i class="glyphicon glyphicon-pencil"></i> ' . Yii::t('app', 'Koreksi'), ['update', 'id' => $model->ID], ['class' => 'btn btn-primary btn-sm', 'style' => 'border-radius: 6px;']) ?>
        <?= Html::a('<i class="glyphicon glyphicon-trash"></i> ' . Yii::t('app', 'Hapus'), ['delete', 'id' => $model->ID], [
            'class' => 'btn btn-danger btn-sm',
            'style' => 'border-radius: 6px;',
            'data' => [
                'confirm' => Yii::t('app', 'Apakah Anda yakin ingin menghapus item ini?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>



    <?= DetailView::widget([
            'model' => $model,
            
        ],
        ['attributes' => [
            'MemberNo',
            'Fullname',
            'PlaceOfBirth',
            [
                        'attribute'=>'DateOfBirth',
                        'format'=>['datetime',(isset(Yii::$app->modules['datecontrol']['displaySettings']['datetime'])) ? Yii::$app->modules['datecontrol']['displaySettings']['datetime'] : 'd-m-Y H:i:s A'],
                        'type'=>DetailView::INPUT_WIDGET,
                        'widgetOptions'=> [
                            'class'=>DateControl::classname(),
                            'type'=>DateControl::FORMAT_DATETIME
                        ]
                    ],
            'Address',
            'AddressNow',
            'Phone',
            'InstitutionName',
            'InstitutionAddress',
            'InstitutionPhone',
            'IdentityType_id',
            'IdentityNo',
            'EducationLevel_id',
            'Religion',
            'Sex_id',
            'MaritalStatus',
            'Job_id',
            [
                        'attribute'=>'RegisterDate',
                        'format'=>['datetime',(isset(Yii::$app->modules['datecontrol']['displaySettings']['datetime'])) ? Yii::$app->modules['datecontrol']['displaySettings']['datetime'] : 'd-m-Y H:i:s A'],
                        'type'=>DetailView::INPUT_WIDGET,
                        'widgetOptions'=> [
                            'class'=>DateControl::classname(),
                            'type'=>DateControl::FORMAT_DATETIME
                        ]
                    ],
            [
                        'attribute'=>'EndDate',
                        'format'=>['datetime',(isset(Yii::$app->modules['datecontrol']['displaySettings']['datetime'])) ? Yii::$app->modules['datecontrol']['displaySettings']['datetime'] : 'd-m-Y H:i:s A'],
                        'type'=>DetailView::INPUT_WIDGET,
                        'widgetOptions'=> [
                            'class'=>DateControl::classname(),
                            'type'=>DateControl::FORMAT_DATETIME
                        ]
                    ],
            'BarCode',
            'PicPath',
            'MotherMaidenName',
            'Email:email',
            'JenisPermohonan',
            'JenisPermohonanName',
            'JenisAnggota_id',
            'JenisAnggotaName',
            'StatusAnggota',
            'StatusAnggotaName',
            'Handphone',
            'ParentName',
            'ParentAddress',
            'ParentPhone',
            'ParentHandphone',
            'Nationality',
            'LoanReturnLateCount',
            'Branch_id',
            'User_id',
            'AlamatDomisili',
            'RT',
            'RW',
            'Kelurahan',
            'Kecamatan',
            'Kota',
            'KodePos',
            'NoHp',
            'NamaDarurat',
            'TelpDarurat',
            'AlamatDarurat',
            'StatusHubunganDarurat',
            'City',
            'Province',
            'CityNow',
            'ProvinceNow',
            'JobNameDetail',
            'Kelas_id',
            'tahunAjaran',
            'Agama_id',
            'MasaBerlaku_id',
            'Jurusan_id',
            'Fakultas_id',
            'UnitKerja_id',
        ],
       
    ]) ?>

</div>
