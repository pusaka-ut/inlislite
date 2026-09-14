<?php

namespace opac\controllers;

use Yii;
use common\models\MasterRak;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class RakController extends Controller
{
    public $layout = false;

    public function actionIndex($id, $q = null, $status = null)
    {
        $model = MasterRak::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('Data rak tidak ditemukan.');
        }

        $query = $model->getCollectionsQuery()
            ->with(['catalog', 'status'])
            ->orderBy(['collections.CallNumber' => SORT_ASC]);

        if (!empty($q)) {
            $query->andWhere([
                'or',
                ['like', 'catalogs.Title', $q],
                ['like', 'catalogs.Author', $q],
                ['like', 'collections.CallNumber', $q],
                ['like', 'collections.NomorBarcode', $q]
            ]);
        }

        $allCollections = $query->all();
        $filtered = [];

        foreach ($allCollections as $item) {
            $statusName = $item->status ? strtolower($item->status->Name) : 'tersedia';
            $isDipinjam = (strpos($statusName, 'pinjam') !== false);

            if ($status === 'tersedia' && $isDipinjam) {
                continue;
            }
            if ($status === 'dipinjam' && !$isDipinjam) {
                continue;
            }

            $filtered[] = $item;
        }

        return $this->render('index', [
            'model' => $model,
            'collections' => $filtered,
            'totalAll' => count($allCollections),
            'q' => $q,
            'status' => $status,
        ]);
    }
}
