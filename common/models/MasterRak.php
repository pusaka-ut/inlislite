<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

class MasterRak extends ActiveRecord
{
    public static function tableName()
    {
        return 'master_rak';
    }

    public function rules()
    {
        return [
            [['kode_rak', 'nama_rak', 'location_id'], 'required'],
            [['location_id'], 'integer'],
            [['keterangan'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['kode_rak', 'call_number_prefix'], 'string', 'max' => 50],
            [['nama_rak'], 'string', 'max' => 255],
            [['range_ddc_awal', 'range_ddc_akhir'], 'string', 'max' => 20],
            [['kode_rak'], 'unique'],
            [['location_id'], 'exist', 'skipOnError' => true, 'targetClass' => Locations::className(), 'targetAttribute' => ['location_id' => 'ID']]
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'kode_rak' => 'Kode Rak',
            'nama_rak' => 'Nama Rak',
            'location_id' => 'Lokasi / Ruang / Lantai',
            'range_ddc_awal' => 'Rentang DDC Awal',
            'range_ddc_akhir' => 'Rentang DDC Akhir',
            'call_number_prefix' => 'Prefix Nomor Panggil',
            'keterangan' => 'Keterangan',
            'created_at' => 'Waktu Dibuat',
            'updated_at' => 'Waktu Diperbarui'
        ];
    }

    public function getLocation()
    {
        return $this->hasOne(Locations::className(), ['ID' => 'location_id']);
    }

    public function getCollectionsQuery()
    {
        $query = Collections::find()
            ->innerJoinWith('catalog')
            ->where(['collections.Location_id' => $this->location_id]);

        $hasDdcRange = (!empty($this->range_ddc_awal) && !empty($this->range_ddc_akhir));
        $hasPrefix = !empty($this->call_number_prefix);

        if ($hasDdcRange && $hasPrefix) {
            $query->andWhere([
                'or',
                [
                    'and',
                    ['>=', 'catalogs.DeweyNo', $this->range_ddc_awal],
                    ['<=', 'catalogs.DeweyNo', $this->range_ddc_akhir]
                ],
                ['like', 'collections.CallNumber', $this->call_number_prefix . '%', false]
            ]);
        } elseif ($hasDdcRange) {
            $query->andWhere(['>=', 'catalogs.DeweyNo', $this->range_ddc_awal]);
            $query->andWhere(['<=', 'catalogs.DeweyNo', $this->range_ddc_akhir]);
        } elseif ($hasPrefix) {
            $query->andWhere(['like', 'collections.CallNumber', $this->call_number_prefix . '%', false]);
        }

        return $query;
    }

    public function getTotalJudul()
    {
        return (int) $this->getCollectionsQuery()->select('collections.Catalog_id')->distinct()->count();
    }

    public function getTotalEksemplar()
    {
        return (int) $this->getCollectionsQuery()->count();
    }
}
