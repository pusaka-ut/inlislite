<?php

namespace backend\controllers;

use Yii;
use common\models\MasterRak;
use common\models\Locations;
use common\components\QrCodeHelper;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

class RakController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => MasterRak::find()->with('location'),
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ],
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate()
    {
        $model = new MasterRak();
        $locations = Locations::find()->orderBy(['Name' => SORT_ASC])->all();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Master rak ' . $model->nama_rak . ' berhasil ditambahkan.');
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
            'locations' => $locations,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $locations = Locations::find()->orderBy(['Name' => SORT_ASC])->all();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Master rak ' . $model->nama_rak . ' berhasil diperbarui.');
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
            'locations' => $locations,
        ]);
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->delete();
        Yii::$app->session->setFlash('success', 'Master rak ' . $model->nama_rak . ' berhasil dihapus.');
        return $this->redirect(['index']);
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);
        $collectionsQuery = $model->getCollectionsQuery();

        $dataProvider = new ActiveDataProvider([
            'query' => $collectionsQuery,
            'pagination' => [
                'pageSize' => 15,
            ],
        ]);

        return $this->render('view', [
            'model' => $model,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionPrintSingle($id, $baseUrl = null)
    {
        $this->layout = false;
        $model = $this->findModel($id);
        $targetUrl = QrCodeHelper::getPublicRakUrl($model->id, $baseUrl);
        $qrDataUri = QrCodeHelper::generateDataUri($targetUrl, 300);

        return $this->render('print-single', [
            'model' => $model,
            'targetUrl' => $targetUrl,
            'qrDataUri' => $qrDataUri,
            'baseUrl' => $baseUrl,
        ]);
    }

    public function actionPrintBatch($baseUrl = null)
    {
        $this->layout = false;
        $models = MasterRak::find()->with('location')->orderBy(['location_id' => SORT_ASC, 'kode_rak' => SORT_ASC])->all();
        $items = [];

        foreach ($models as $model) {
            $targetUrl = QrCodeHelper::getPublicRakUrl($model->id, $baseUrl);
            $items[] = [
                'model' => $model,
                'targetUrl' => $targetUrl,
                'qrDataUri' => QrCodeHelper::generateDataUri($targetUrl, 250),
            ];
        }

        return $this->render('print-batch', [
            'items' => $items,
            'baseUrl' => $baseUrl,
        ]);
    }

    protected function findModel($id)
    {
        if (($model = MasterRak::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Data rak tidak ditemukan.');
    }
}
