<?php

namespace opac\controllers;

use Yii;
use common\models\Opaclogs;
use common\models\OpaclogsKeyword;
use common\models\Worksheets;
use common\models\Bookinglogs;
use common\models\Favorite;
use common\models\Collections;
use common\models\Catalogs;
use common\models\Requestcatalog;
use common\models\CollectionSearchKardeks;
use common\models\SerialArticlesSearch;
use common\models\Members;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\data\SqlDataProvider;
use yii\data\ActiveDataProvider;
use yii\web\Session;
use yii\web\Request;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\helpers\Json;
use yii\web\NotFoundHttpException;
use common\components\OpacHelpers;
$session = Yii::$app->session;
$session->open();

class PencarianSederhanaController extends \yii\web\Controller {
    public $layout = 'main-sederhana';
    public $location;
    public function actionIndex() {
        $location = Yii::$app->request->cookies->getValue('location_opac_id');
        $jmlBookMaks = Yii::$app->config->get('JumlahBookingMaksimal');
        $bookExp = Yii::$app->config->get('BookingExpired');
        $UsulanKoleksi = Yii::$app->config->get('UsulanKoleksi');
        $dateNow = new \DateTime("now");
        $noAnggota= (Yii::$app->user->isGuest ? null : \Yii::$app->user->identity->NoAnggota );
        $booking = OpacHelpers::jumlahBooking($noAnggota);

        $alert = false;
        $session = Yii::$app->session;
        $datas = $session->get('catIDmerge');
        $request = Yii::$app->request;
        $connection = Yii::$app->db;
        $url = Yii::$app->request->absoluteUrl;
        $waktu = date('m-d-Y H:i:s');
        $action = ( isset($_GET['action']) ) ? addslashes(urldecode($_GET['action'])) : "pencarianSederhana";

        $request = Yii::$app->request;
        if ($request->isAjax && $_GET['action'] === "favourite") {
            if (Yii::$app->user->isGuest) {
                return $this->redirect('../keanggotaan/site/login');
            }
            $model = new favorite;
            (int) $count = favorite::find()
                    ->where(['Member_Id' => \Yii::$app->user->identity->NoAnggota, 'Catalog_Id' => addslashes($_GET['catID'])])
                    ->count();


            if ($count == 0) {
                $model->Member_Id = \Yii::$app->user->identity->NoAnggota;
                $model->Catalog_Id = addslashes($_GET['catID']);
                //$model->CreateDate = new Expression('NOW()');
                $model->save();

                Yii::$app->getSession()->setFlash('success', [
                    'type' => 'info',
                    'duration' => 2500,
                    'icon' => 'glyphicon glyphicon-ok-sign',
                    'message' => Yii::t('app', '  Telah Di Simpan ke-dalam daftar Favorite'),
                    'title' => 'success',
                    'positonY' => Yii::$app->params['flashMessagePositionY'],
                    'positonX' => Yii::$app->params['flashMessagePositionX']
                ]);
            } else {
                Yii::$app->getSession()->setFlash('error', [
                    'type' => 'danger',
                    'delay' => 2500,
                    'icon' => 'glyphicon glyphicon-remove',
                    'message' => Yii::t('app', ' Katalog ini sudah ada di dalam daftar Favorite anda'),
                    'title' => 'Gagal',
                    'body' => 'This is a successful growling alert.',
                    'positonY' => Yii::$app->params['flashMessagePositionY'],
                    'positonX' => Yii::$app->params['flashMessagePositionX']
                ]);
            }

            return $this->renderAjax('_favorite', [
                        'catID' => addslashes($_GET['catID']),
            ]);
        }
        if ($request->isAjax && $_GET['action'] === "requestCatalog") {
            $model = new requestcatalog;
            $model->MemberID = 1;
            $model->WorksheetID = 1;
            $model->Title = 1;
            $model->Author = 1;
            $model->PublishLocation = 1;
            $model->PublishYear = 1;
            $model->Comments = 1;
            $model->save();
        }

        if (Yii::$app->request->get() && $_GET['action'] === "pencarianSederhana") {              
            $rawKeyword = isset($_GET['katakunci']) ? trim(urldecode($_GET['katakunci'])) : '';
            if (mb_strlen($rawKeyword) < 2) {
                Yii::$app->getSession()->setFlash('error', [
                    'type' => 'info',
                    'duration' => 4000,
                    'icon' => 'glyphicon glyphicon-info-sign',
                    'message' => Yii::t('app', 'Silakan masukkan kata kunci pencarian minimal 2 karakter.'),
                    'title' => 'Petunjuk Pencarian',
                    'positonY' => Yii::$app->params['flashMessagePositionY'],
                    'positonX' => Yii::$app->params['flashMessagePositionX']
                ]);
                return $this->render('resultListOpac', [
                    'countResult' => 0,
                    'dataResult' => [],
                    'totalCountResult' => 0,
                    'dataFacedAuthor' => [],
                    'dataFacedPublisher' => [],
                    'dataFacedPublishYear' => [],
                    'dataFacedPublishLocation' => [],
                    'dataFacedSubject' => [],
                    'dataFacedBahasa' => [],
                    'noAnggota' => $noAnggota,
                    'alert' => true,
                    'UsulanKoleksi' => $UsulanKoleksi,
                    'booking' => $booking,
                    'FacedAuthorMax' => Yii::$app->config->get('FacedAuthorMax'),
                    'FacedAuthorMin' => Yii::$app->config->get('FacedAuthorMin'),
                    'FacedPublisherMax' => Yii::$app->config->get('FacedPublisherMax'),
                    'FacedPublisherMin' => Yii::$app->config->get('FacedPublisherMin'),
                    'FacedPublishLocationMax' => Yii::$app->config->get('FacedPublishLocationMax'),
                    'FacedPublishLocationMin' => Yii::$app->config->get('FacedPublishLocationMin'),
                    'FacedPublishYearMax' => Yii::$app->config->get('FacedPublishYearMax'),
                    'FacedPublishYearMin' => Yii::$app->config->get('FacedPublishYearMin'),
                    'FacedSubjectMax' => Yii::$app->config->get('FacedSubjectMax'),
                    'FacedSubjectMin' => Yii::$app->config->get('FacedSubjectMin'),
                    'FacedBahasaMax' => Yii::$app->config->get('FacedBahasaMax'),
                    'FacedBahasaMin' => Yii::$app->config->get('FacedBahasaMin'),
                    'catID' => null
                ]);
            }

            $bahan = addslashes($_GET['bahan']);
            $bahan1 = $bahan;
            if ($bahan != 'Semua Jenis Bahan') {
                $tmp = worksheets::find()
                    ->where(['id' => $bahan])
                    ->one();
                if ($tmp) {
                    $bahan = $tmp['Name'];  
                }
            }

            $Keyword = $rawKeyword;
            $ruas = addslashes($_GET['ruas']);
            $dariTGL = ( isset($_GET['dariTGL']) ) ? addslashes($_GET['dariTGL']) : '';
            $sampaiTGL = ( isset($_GET['sampaiTGL']) ) ? addslashes($_GET['sampaiTGL']) : '';
            $ip = OpacHelpers::getIP();
            if (isset($_SESSION['RiwayatPencarian'])) {
                $temp = $_SESSION['RiwayatPencarian'];
                $_SESSION['RiwayatPencarian'] = array_merge($temp, array(
                    array(
                        "ip" =>  $ip,
                        "url" => $url,
                        "action" => addslashes($_GET['action']),
                        "keyword" => $ruas . " = " . $Keyword,
                        "bahan" => $bahan,
                        "time" => $waktu,
                    )
                ));
            } else {
                $temp = array(
                    array(
                        "ip" =>  $ip,
                        "url" => $url,
                        "action" => addslashes($_GET['action']),
                        "keyword" => $ruas . " = " . $Keyword,
                        "bahan" => $bahan,
                        "time" => $waktu,
                    )
                );
                $_SESSION['RiwayatPencarian'] = $temp;
            }

            $logs=[
                'user_id' => $noAnggota,
                'ip'      => $ip,
                'jenis_pencarian' => addslashes($_GET['action']),
                'keyword' => $ruas . " = " . $Keyword,
                'jenis_bahan' => $bahan,
                'url' => $url,
                'isLKD' => 0,
                'Field' => $ruas,
            ];
            try {
                OpacHelpers::opacLogs($logs);
            } catch (\Exception $eLogs) {
                Yii::warning($eLogs->getMessage(), 'opac.logs');
            }

            $page = ( isset($_GET['page']) ) ? (int)$_GET['page'] : 1;
            if ($page < 1) {
                $page = 1;
            }
            $limit = ( isset($_GET['limit']) ) ? (int)$_GET['limit'] : 10;
            if ($limit < 1) {
                $limit = 10;
            }
            $fAuthor = ( isset($_GET['fAuthor']) ) ? addslashes(urldecode($_GET['fAuthor'])) : '';
            $fPublisher = ( isset($_GET['fPublisher']) ) ? addslashes(urldecode($_GET['fPublisher'])) : '';
            $fPublishLoc = ( isset($_GET['fPublishLoc']) ) ? addslashes(urldecode($_GET['fPublishLoc'])) : '';
            $fPublishYear = ( isset($_GET['fPublishYear']) ) ? addslashes(urldecode($_GET['fPublishYear'])) : '';
            $fSubject = ( isset($_GET['fSubject']) ) ? addslashes(urldecode($_GET['fSubject'])) : '';
            $fBahasa = ( isset($_GET['fBahasa']) ) ? addslashes(urldecode($_GET['fBahasa'])) : '';

            $limitAwal = ($page - 1) * $limit;
            $KeywordParam = "%" . $Keyword . "%";
            $params = [':keyword' => $KeywordParam, ':ruas' => $ruas, ':bahan1' => $bahan1, ':fAuthor' => $fAuthor, ':fPublisher' => $fPublisher, ':fPublishLoc' => $fPublishLoc, ':fPublishYear' => $fPublishYear, ':fSubject' => $fSubject, ':fBahasa' => $fBahasa, ':dariTGL' => $dariTGL, ':sampaiTGL' => $sampaiTGL ];

            $FacedAuthorMax = Yii::$app->config->get('FacedAuthorMax');
            $FacedPublisherMax = Yii::$app->config->get('FacedPublisherMax');
            $FacedPublishLocationMax = Yii::$app->config->get('FacedPublishLocationMax');
            $FacedPublishYearMax = Yii::$app->config->get('FacedPublishYearMax');
            $FacedSubjectMax = Yii::$app->config->get('FacedSubjectMax');
            $FacedBahasaMax = Yii::$app->config->get('FacedBahasaMax');

            $FacedAuthorMin = Yii::$app->config->get('FacedAuthorMin');
            $FacedPublisherMin = Yii::$app->config->get('FacedPublisherMin');
            $FacedPublishLocationMin = Yii::$app->config->get('FacedPublishLocationMin');
            $FacedPublishYearMin = Yii::$app->config->get('FacedPublishYearMin');
            $FacedSubjectMin = Yii::$app->config->get('FacedSubjectMin');
            $FacedBahasaMin = Yii::$app->config->get('FacedBahasaMin');

            $hasilSearch = [];
            $count = 0;
            $dataFacedAuthor = [];
            $dataFacedPublisher = [];
            $dataFacedPublishLocation = [];
            $dataFacedPublishYear = [];
            $dataFacedSubject = [];
            $dataFacedBahasa = [];

            if ($page === 1) {
                try {
                    if ($location) {
                        $command = Yii::$app->db->createCommand("CALL insertTempSederhanaOpac(:keyword,:ruas,:bahan1,:fAuthor,:fPublisher,:fPublishLoc,:fPublishYear,:fSubject,:fBahasa,:dariTGL,:sampaiTGL,'',".$location." );");
                        $command->bindValues($params);
                        $command->execute();
                    } else {
                        $command = Yii::$app->db->createCommand("CALL insertTempSederhanaOpac(:keyword,:ruas,:bahan1,:fAuthor,:fPublisher,:fPublishLoc,:fPublishYear,:fSubject,:fBahasa,:dariTGL,:sampaiTGL,'',0 );");
                        $command->bindValues($params);
                        $command->execute();
                    }

                    $count = (int)Yii::$app->db->createCommand("select count(1) from tempCariOpac")->queryScalar();
                    $hasilSearch = Yii::$app->db->createCommand("select * from tempCariOpac limit 0,$limit")->queryAll();

                    $req = [
                        'fAuthor' => $fAuthor,
                        'fPublisher' => $fPublisher,
                        'fPublishLoc' => $fPublishLoc,
                        'fPublishYear' => $fPublishYear,
                        'fSubject' => $fSubject,
                        'fBahasa' => $fBahasa
                    ];
                    $dataFacedAuthor = OpacHelpers::facedGenerator(OpacHelpers::facedOpac('Author', $req), 'Author');
                    $dataFacedPublisher = OpacHelpers::facedGenerator(OpacHelpers::facedOpac('Publisher', $req), 'Publisher');
                    $dataFacedPublishLocation = OpacHelpers::facedGenerator(OpacHelpers::facedOpac('PublishLocation', $req), 'PublishLocation');
                    $dataFacedPublishYear = OpacHelpers::facedGenerator(OpacHelpers::facedOpac('PublishYear', $req), 'PublishYear');
                    $dataFacedSubject = OpacHelpers::facedGenerator(OpacHelpers::facedOpac('SUBJECT', $req), 'SUBJECT');
                    $dataFacedBahasa = OpacHelpers::facedGenerator(OpacHelpers::facedOpac('bahasa', $req), 'bahasa');

                    $_SESSION['dataFacedAuthor'] = $dataFacedAuthor;
                    $_SESSION['dataFacedPublisher'] = $dataFacedPublisher;
                    $_SESSION['dataFacedPublishLocation'] = $dataFacedPublishLocation;
                    $_SESSION['dataFacedPublishYear'] = $dataFacedPublishYear;
                    $_SESSION['dataFacedSubject'] = $dataFacedSubject;
                    $_SESSION['dataFacedBahasa'] = $dataFacedBahasa;
                    $_SESSION['countSearch'] = $count;
                } catch (\Exception $e) {
                    Yii::warning($e->getMessage(), 'opac.search');
                    $hasilSearch = $this->getDirectSearchData($KeywordParam, $ruas, $bahan1, $location, 0, $limit, $fAuthor, $fPublisher, $fPublishLoc, $fPublishYear, $fSubject, $fBahasa);
                    $count = $this->getDirectSearchCount($KeywordParam, $ruas, $bahan1, $location, $fAuthor, $fPublisher, $fPublishLoc, $fPublishYear, $fSubject, $fBahasa);
                    $_SESSION['countSearch'] = $count;
                    $_SESSION['dataFacedAuthor'] = [];
                    $_SESSION['dataFacedPublisher'] = [];
                    $_SESSION['dataFacedPublishLocation'] = [];
                    $_SESSION['dataFacedPublishYear'] = [];
                    $_SESSION['dataFacedSubject'] = [];
                    $_SESSION['dataFacedBahasa'] = [];
                }
            } else {
                $hasilSearch = $this->getDirectSearchData($KeywordParam, $ruas, $bahan1, $location, $limitAwal, $limit, $fAuthor, $fPublisher, $fPublishLoc, $fPublishYear, $fSubject, $fBahasa);
                if (!isset($_SESSION['countSearch']) || empty($_SESSION['countSearch'])) {
                    $_SESSION['countSearch'] = $this->getDirectSearchCount($KeywordParam, $ruas, $bahan1, $location, $fAuthor, $fPublisher, $fPublishLoc, $fPublishYear, $fSubject, $fBahasa);
                }
                $count = isset($_SESSION['countSearch']) ? (int)$_SESSION['countSearch'] : 0;
                $dataFacedAuthor = isset($_SESSION['dataFacedAuthor']) ? $_SESSION['dataFacedAuthor'] : [];
                $dataFacedPublisher = isset($_SESSION['dataFacedPublisher']) ? $_SESSION['dataFacedPublisher'] : [];
                $dataFacedPublishLocation = isset($_SESSION['dataFacedPublishLocation']) ? $_SESSION['dataFacedPublishLocation'] : [];
                $dataFacedPublishYear = isset($_SESSION['dataFacedPublishYear']) ? $_SESSION['dataFacedPublishYear'] : [];
                $dataFacedSubject = isset($_SESSION['dataFacedSubject']) ? $_SESSION['dataFacedSubject'] : [];
                $dataFacedBahasa = isset($_SESSION['dataFacedBahasa']) ? $_SESSION['dataFacedBahasa'] : [];
            }

            foreach ($hasilSearch as $key => $value) {
                // OpacHelpers::print__r($hasilSearch);
                $dataSearch[$key] = $value;
                $dataTagRDA        = OpacHelpers::getTaginfo($dataSearch[$key]['CatalogId'],'336,338','a');
                $jenisBahanRDA     = OpacHelpers::jenisBahanRDA($dataTagRDA);
                $jenis_bahanold    = $dataSearch[$key]['worksheet'];
                $dataSearch[$key]['worksheet'] = $jenis_bahanold." ".$jenisBahanRDA;
                $dataSearch[$key]['authOriginal'] =  array_values(array_filter(explode("|",OpacHelpers::sqlDetailOpac('PENGARANG',$dataSearch[$key]['CatalogId']))));
                $dataSearch[$key]['authModif'] = preg_replace("/\([^)]+\)/","",$dataSearch[$key]['authOriginal']);
                $dataSearch[$key]['keyword']=urldecode($_GET['katakunci']);
                $dataSearch[$key]['title'] =  OpacHelpers::highlight($dataSearch[$key]['title'],$dataSearch[$key]['keyword']);

                //replace authoriginal with highlighed string
                foreach ($dataSearch[$key]['authOriginal'] as $keys => &$values){
                    $values = OpacHelpers::highlight($values,$dataSearch[$key]['keyword']);
                }

            }

            
            



            //buat nyimpen session keranjang
            if (!isset($_SESSION['catID']) || $_SESSION['catID'] == '') {
                $_SESSION['catID'] = NULL;
            };
            if (!isset($_SESSION['catIDmerge']) || $_SESSION['catIDmerge'] == '') {
                $_SESSION['catIDmerge'] = NULL;
            };
            if (!isset($_POST['catID']) || $_POST['catID'] == '') {
                $_POST['catID'] = NULL;
            };
            if (!isset($_SESSION['catID']) || $_SESSION['catID'] == '') {
                $_SESSION['catID'] = NULL;
            };
            if (!isset($_SESSION['catIDmerge']) || $_SESSION['catIDmerge'] == '') {
                $_SESSION['catIDmerge'] = NULL;
            };
            if (!isset($_POST['catID']) || $_POST['catID'] == '') {
                $_POST['catID'] = NULL;
            };

            if (isset($_POST['action']) && $_POST['action'] == "keranjang" && isset($_POST['catID'])) {
                if (isset($_SESSION['catID'])) {

                    $temp = (is_array($_SESSION['catID']) ? $_SESSION['catID'] : array($_SESSION['catID']));
                    $duplicated = 0;
                    for ($i = 0; $i < sizeof($_POST['catID']); $i++) {
                        if (in_array($_POST['catID'][$i], $temp)) {
                            $duplicated+=1;
                        }
                    }
                    //menggabungkan catID di session dengan catID dari post//
                    $_SESSION['catID'] = array_unique(array_merge($temp, $_POST['catID']));

                    //pesan  ketika semua catalogID gagal dimasukkan ke keranjang
                    if (sizeof($_POST['catID']) == $duplicated) {
                        Yii::$app->getSession()->setFlash('error', [
                            'type' => 'danger',
                            'duration' => 3500,
                            'icon' => 'glyphicon glyphicon-ok-sign',
                            'message' => Yii::t('app', ' Katalog Gagal disimpan, Katalog sudah ada di dalam keranjang'),
                            'title' => 'Error',
                            'positonY' => Yii::$app->params['flashMessagePositionY'],
                            'positonX' => Yii::$app->params['flashMessagePositionX']
                        ]);
                        $alert = TRUE;
                    } else
                    //pesan  ketika sebagian catalogID gagal dimasukkan ke keranjang
                    if ($duplicated != 0) {
                        Yii::$app->getSession()->setFlash('success', [
                            'type' => 'info',
                            'duration' => 2500,
                            'icon' => 'glyphicon glyphicon-ok-sign',
                            'message' => Yii::t('app', (sizeof($_POST['catID']) - $duplicated) . ' Katalog berhasil disimpan di dalam keranjang ' . $duplicated . ' Katalog gagal disimpan'),
                            'title' => 'success',
                            'positonY' => Yii::$app->params['flashMessagePositionY'],
                            'positonX' => Yii::$app->params['flashMessagePositionX']
                        ]);
                        $alert = TRUE;
                    }
                    //pesan ketika semua catalogID berhasil di masukkan ke keranjang
                    else {
                        Yii::$app->getSession()->setFlash('success', [
                            'type' => 'info',
                            'duration' => 2500,
                            'icon' => 'glyphicon glyphicon-ok-sign',
                            'message' => Yii::t('app', sizeof($_POST['catID']) . ' Katalog berhasil disimpan di dalam keranjang'),
                            'title' => 'success',
                            'positonY' => Yii::$app->params['flashMessagePositionY'],
                            'positonX' => Yii::$app->params['flashMessagePositionX']
                        ]);
                        $alert = TRUE;
                    }
                } else {
                    $_SESSION['catID'] = $_POST['catID'];
                    Yii::$app->getSession()->setFlash('success', [
                        'type' => 'info',
                        'duration' => 2500,
                        'icon' => 'glyphicon glyphicon-ok-sign',
                        'message' => Yii::t('app', sizeof($_POST['catID']) . ' Katalog berhasil disimpan di dalam keranjang'),
                        'title' => 'success',
                        'positonY' => Yii::$app->params['flashMessagePositionY'],
                        'positonX' => Yii::$app->params['flashMessagePositionX']
                    ]);
                    $alert = TRUE;
                }
                $gabung = implode(",", $_SESSION['catID']);
                $_SESSION['catIDmerge'] = $gabung;
            }
            // echo "<pre>";
            // print_r($dataSearch);
            // die;

            if (!isset($dataSearch)) {
                $dataSearch = "";
            }
            return $this->render('resultListOpac', [
                'countResult' => count($hasilSearch),
                'dataResult' => $dataSearch,
                'totalCountResult' => $count,
                'dataFacedAuthor' => $dataFacedAuthor,
                'dataFacedPublisher' => $dataFacedPublisher,
                'dataFacedPublishYear' => $dataFacedPublishYear,
                'dataFacedPublishLocation' => $dataFacedPublishLocation,
                'dataFacedSubject' => $dataFacedSubject,
                'dataFacedBahasa' => $dataFacedBahasa,
                'noAnggota' => $noAnggota,
                'alert' => $alert,
                'UsulanKoleksi' => $UsulanKoleksi,
                'booking' => $booking,
                'FacedAuthorMax' => $FacedAuthorMax,
                'FacedAuthorMin' => $FacedAuthorMin,
                'FacedPublisherMax' => $FacedPublisherMax,
                'FacedPublisherMin' => $FacedPublisherMin,
                'FacedPublishLocationMax' => $FacedPublishLocationMax,
                'FacedPublishLocationMin' => $FacedPublishLocationMin,
                'FacedPublishYearMax' => $FacedPublishYearMax,
                'FacedPublishYearMin' => $FacedPublishYearMin,
                'FacedSubjectMax' => $FacedSubjectMax,
                'FacedSubjectMin' => $FacedSubjectMin,
                'FacedBahasaMax' => $FacedBahasaMax,
                'FacedBahasaMin' => $FacedBahasaMin,
                'page' => $page,
                'limit' => $limit,
                'offset' => ceil($page / $limit),
                'fAuthor' => $fAuthor,
                'fPublisher' => $fPublisher,
                'fPublishLoc' => $fPublishLoc,
                'fPublishYear' => $fPublishYear,
                'fSubject' => $fSubject,
                'fBahasa' => $fBahasa,
                'action' => $action,
                'bases' => Yii::$app->homeUrl,
            ]);
        }
        
        if (!isset($_SESSION['catID']) || $_SESSION['catID'] == '') {
            $_SESSION['catID'] = NULL;
        };
        if (!isset($_SESSION['catIDmerge']) || $_SESSION['catIDmerge'] == '') {
            $_SESSION['catIDmerge'] = NULL;
        };
        if (!isset($_POST['catID']) || $_POST['catID'] == '') {
            $_POST['catID'] = NULL;
        };

        if (isset($_POST['catID'])) {
            if (isset($_SESSION['catID'])) {
                $temp = $_SESSION['catID'];
                //menggabungkan catID di session dengan catID dari post//
                $_SESSION['catID'] = array_unique(array_merge($temp, $_POST['catID']));
            } else {
                $_SESSION['catID'] = $_POST['catID'];
            }

            $gabung = implode(",", $_SESSION['catID']);
            $_SESSION['catIDmerge'] = $gabung;
        }
        
        if ($request->isAjax && $_GET['action'] === "showCollection") {

            $catID = $_GET['catID'];
            if ($_GET['serial'] == 1) {
                $searchModel = new CollectionSearchKardeks;
                $params['CatalogId'] = $_GET['catID'];
                $dataProvider = $searchModel->search2($params);
                return $this->renderAjax('_serial', [
                            'catID' => $catID,
                            'dataProvider' => $dataProvider,
                            'searchModel' => $searchModel,
                ]);
            }


            $sqlCollectionList = "CALL showCollectionOpac(" . $catID . ");";

            $dataProviderCollectionList = new SqlDataProvider([
                'sql' => $sqlCollectionList,
                'pagination' => false,
                    //'pagination' => [ 'pageSize' => 20,],
            ]);

            $modelCollectionList = $dataProviderCollectionList->getModels();
            $countCollectionList = $dataProviderCollectionList->getCount();
            $temp = 1;
            foreach ($modelCollectionList as $value) {
                $dataCollectionList[$temp] = $value;
                $temp++;
            }
            if (!isset($dataCollectionList)) {
                $dataCollectionList = "";
            }


            return $this->renderAjax('_collectionlist', [

                        'dataProviderCollectionList' => $dataProviderCollectionList,
                        'countCollectionList' => $countCollectionList,
                        'dataCollectionList' => $dataCollectionList,
                        'noAnggota' => $noAnggota,
                        'catID' => $catID
            ]);
        }
        if ($request->isAjax && $_GET['action'] === "showArticle") {
            /*$catID = $_GET['catID'];
            $hasilSearch = Yii::$app->db->createCommand("SELECT * FROM serial_articles WHERE Catalog_id =".$catID." ")->queryAll();

            return $this->renderAjax('_articleList', [

                'hasilSearch' => $hasilSearch,
                'noAnggota' => $noAnggota,
                'catID' => $catID
            ]);*/
            $catID = $_GET['catID'];
            $searchModel = new SerialArticlesSearch;
            $params['Catalog_id'] = $_GET['catID'];
            $dataProvider = $searchModel->advancedSearchByCatalogId($params,$rules=null);
            return $this->renderAjax('_serialArticle', [
                'catID' => $catID,
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
            ]);
        }
        if ($request->isAjax && $_GET['action'] === "logDownload") {

            OpacHelpers::logsDownload($_GET['ID'],$noAnggota,'0');          
        }
        if ($request->isAjax && $_GET['action'] === "showKontenDigital") {
            $catID = $_GET['catID'];
            $sqlCollectionList = "CALL showKontenDigital(" . $catID . "); ";

            $dataProviderCollectionList = new SqlDataProvider([
                'sql' => $sqlCollectionList,
                //'pagination'=> false,
                'pagination' => [ 'pageSize' => 1,],
            ]);

            $modelCollectionList = $dataProviderCollectionList->getModels();
            $countCollectionList = $dataProviderCollectionList->getCount();
            $temp = 1;
            foreach ($modelCollectionList as $value) {
                $dataCollectionList[$temp] = $value;
                $temp++;
            }
            if (!isset($dataCollectionList)) {
                $dataCollectionList = "";
            }

            return $this->renderAjax('_kontendigitallist', [

                        'dataProviderCollectionList' => $dataProviderCollectionList,
                        'countCollectionList' => $countCollectionList,
                        'dataCollectionList' => $dataCollectionList,
                        'noAnggota' => $noAnggota,
                        'catID' => $catID,
            ]);
        }
        if ($request->isAjax && $_GET['action'] === "boooking") {

            if (Yii::$app->user->isGuest) {
                return $this->redirect('../keanggotaan/site/login');
            }
            $colID = $_GET['colID'];
            $cekBooking = OpacHelpers::cekBooking($noAnggota,$colID);       
            $noAnggota = \Yii::$app->user->identity->NoAnggota;
            $dateNow = new \DateTime("now");
            $dateAdd = new \DateTime("now");
            $bookingTime=OpacHelpers::SetBookingTime($bookExp);
            /*$tambahJam= explode(":",$bookExp);


            $dateAdd->modify("+".$tambahJam[0]." hours +".$tambahJam[1]." minutes +".$tambahJam[2]." seconds");*/

            if (!$cekBooking) {
                
                    $modelLogs = new Bookinglogs;
                    $modelLogs->memberId = $noAnggota;
                    $modelLogs->collectionId = $colID;
                    $modelLogs->bookingDate = $dateNow->format("Y-m-d H:i:sO");
                    $modelLogs->bookingExpired = $bookingTime->format("Y-m-d H:i:sO");
                    $modelLogs->save();
                    
                    $params2 = [':ID' => $colID, ':BookingMemberID' => $noAnggota, ':BookingExpiredDate' => $bookingTime->format("Y-m-d H:i:sO")];
                    $command = Yii::$app->db->createCommand("UPDATE collections SET BookingMemberID=:BookingMemberID, BookingExpiredDate=:BookingExpiredDate WHERE ID=:ID;");
                    $command->bindValues($params2);
                    $command->execute();

                    Yii::$app->getSession()->setFlash('success', [
                        'type' => 'info',
                        'duration' => 2500,
                        'icon' => 'glyphicon glyphicon-ok-sign',
                        'message' => Yii::t('app', 'Berhasil Booking'),
                        'title' => 'success',
                        'positonY' => Yii::$app->params['flashMessagePositionY'],
                        'positonX' => Yii::$app->params['flashMessagePositionX']
                    ]);
            } else {
                $pesan=implode(",", $cekBooking);
                    Yii::$app->getSession()->setFlash('error', [
                    'type' => 'danger',
                    'delay' => 3500,
                    'icon' => 'glyphicon glyphicon-remove',
                    'message' => Yii::t('app', '  Gagal Booking, '.$pesan),
                    'title' => 'Gagal',
                    'body' => 'This is a successful growling alert.',
                    'positonY' => 'top',
                    'positonX' => 'right'
                ]);
                
            }

            
            return $this->renderAjax('alert', [
                        'booking' => $booking,

            ]);
        }
        if ($request->isAjax && $_GET['action'] === "search") {
            $catID = $_GET['catID'];
            $pos  = $_GET['pos'];
            $sqlSearch = "
                SELECT CAT.id CatalogId,CAT.title kalimat2,CAT.author,CAT.publisher,CAT.PublishLocation,CAT.PublishYear,CAT.subject,CAT.CoverURL ,CAT.Worksheet_id, 
                (SELECT NAME FROM worksheets WHERE id=CAT.Worksheet_id) worksheet,
                (SELECT COUNT(1) FROM collections WHERE CATALOG_ID=CAT.ID AND STATUS_ID=1 AND (BookingExpiredDate < now() || BookingExpiredDate is null)) JML_BUKU,
                (SELECT COUNT(1) FROM collections WHERE CATALOG_ID=CAT.ID) ALL_BUKU,
                (SELECT GROUP_CONCAT(DISTINCT SUBSTR(fileURL,INSTR(fileURL, '.')+1) SEPARATOR ', ') 
                FROM catalogfiles WHERE Catalog_id = CAT.ID) KONTEN_DIGITAL
                
                FROM catalogs CAT JOIN collections col ON col.Catalog_id = CAT.ID
                 WHERE 
                   CAT.isopac=1 AND
                    CAT.ID=" . $catID . ";


                ";
            
            $dataProviderSearch = new SqlDataProvider([
                'sql' => $sqlSearch,
                'pagination' => false,
            ]);

            $modelSearch = $dataProviderSearch->getModels();
            $countSearch = $dataProviderSearch->getCount();

            $temp = 1;
            foreach ($modelSearch as $value) {
                $dataSearch[$temp] = $value;
                $dataTagRDA        = OpacHelpers::getTaginfo($dataSearch[$temp]['CatalogId'],'336,338','a');
                $jenisBahanRDA     = OpacHelpers::jenisBahanRDA($dataTagRDA);
                $jenis_bahanold    = $dataSearch[$temp]['worksheet'];
                $dataSearch[$temp]['worksheet'] = $jenis_bahanold." ".$jenisBahanRDA;
                $temp++;
            }

            $dateNow = new \DateTime("now");

            return $this->renderAjax('_search', [
                        'dataResult' => $dataSearch,
                        'booking' => $booking,
                        'i' => $pos,
            ]);
        }

        return $this->render('index');
    }

    public function actionUsulan() {
        if (Yii::$app->user->isGuest) {
            $noAnggota = $_POST['formData']['NomorAnggota'];
        } else {
            $noAnggota = \Yii::$app->user->identity->NoAnggota;
        }


        $model = new requestcatalog;
        //$model->MemberID = $noAnggota;
        //$model->WorksheetID = 1;
        $model->WorksheetID = $_POST['formData']['JenisBahan'];
        $model->Title = $_POST['formData']['Judul'];
        $model->Author = $_POST['formData']['Pengarang'];
        $model->PublishLocation = $_POST['formData']['KotaTerbit'];
        $model->Publisher = $_POST['formData']['Penerbit'];
        $model->PublishYear = $_POST['formData']['TahunTerbit'];
        $model->Comments = $_POST['formData']['Keterangan'];
        $model->save(false);


        Yii::$app->getSession()->setFlash('success', [
            'type' => 'info',
            'delay' => 2500,
            'icon' => 'glyphicon glyphicon-remove',
            'message' => Yii::t('app', '  Data Berhasil Disimpan '),
            'title' => 'Sukses',
            'body' => 'This is a successful growling alert.',
            'positonY' => Yii::$app->params['flashMessagePositionY'],
            'positonX' => Yii::$app->params['flashMessagePositionX']
        ]);
        return $this->renderAjax('_usulan', [
                        
        ]);
    }
    private function getDirectSearchData($keyword, $ruas, $bahan1, $location, $limitAwal, $limit, $fAuthor, $fPublisher, $fPublishLoc, $fPublishYear, $fSubject, $fBahasa) {
        $bindParams = [':keyword' => $keyword];

        switch ($ruas) {
            case 'Judul':
                $searchCondition = "(CAT.Title LIKE :keyword OR EXISTS (SELECT 1 FROM catalog_ruas R WHERE R.TAG IN ('240','245','246','440','740') AND R.Value LIKE :keyword AND R.CATALOGID = CAT.ID))";
                break;
            case 'Pengarang':
                $searchCondition = "(CAT.Author LIKE :keyword OR EXISTS (SELECT 1 FROM catalog_ruas R WHERE R.TAG IN ('100','110','111','700','710','711','800','810','811') AND R.Value LIKE :keyword AND R.CATALOGID = CAT.ID))";
                break;
            case 'Penerbit':
                $searchCondition = "(CAT.Publisher LIKE :keyword OR EXISTS (SELECT 1 FROM catalog_ruas R WHERE R.TAG IN ('260','264') AND R.Value LIKE :keyword AND R.CATALOGID = CAT.ID))";
                break;
            case 'Subyek':
                $searchCondition = "(CAT.Subject LIKE :keyword OR EXISTS (SELECT 1 FROM catalog_ruas R WHERE R.TAG IN ('600','610','611','650','651') AND R.Value LIKE :keyword AND R.CATALOGID = CAT.ID))";
                break;
            case 'Nomor Panggil':
                $searchCondition = "(CAT.CallNumber LIKE :keyword OR EXISTS (SELECT 1 FROM catalog_ruas R WHERE R.TAG IN ('090','084') AND R.Value LIKE :keyword AND R.CATALOGID = CAT.ID))";
                break;
            case 'ISBN':
                $searchCondition = "(CAT.ISBN LIKE :keyword OR EXISTS (SELECT 1 FROM catalog_ruas R WHERE R.TAG IN ('020') AND R.Value LIKE :keyword AND R.CATALOGID = CAT.ID))";
                break;
            case 'ISSN':
                $searchCondition = "(EXISTS (SELECT 1 FROM catalog_ruas R WHERE R.TAG IN ('022') AND R.Value LIKE :keyword AND R.CATALOGID = CAT.ID))";
                break;
            case 'ISMN':
                $searchCondition = "(EXISTS (SELECT 1 FROM catalog_ruas R WHERE R.TAG IN ('024') AND R.Value LIKE :keyword AND R.CATALOGID = CAT.ID))";
                break;
            default:
                $searchCondition = "(CAT.Title LIKE :keyword OR CAT.Author LIKE :keyword OR CAT.Publisher LIKE :keyword OR CAT.Subject LIKE :keyword OR CAT.CallNumber LIKE :keyword OR CAT.ISBN LIKE :keyword OR EXISTS (SELECT 1 FROM catalog_ruas R WHERE R.Value LIKE :keyword AND R.CATALOGID = CAT.ID))";
                break;
        }

        $locationCondition = "";
        if (!empty($location) && $location != 0) {
            $bindParams[':location'] = $location;
            $locationCondition = "AND EXISTS (SELECT 1 FROM collections col WHERE col.Catalog_id = CAT.ID AND col.Location_id = :location)";
        }

        $worksheetCondition = "1 = 1";
        if (!empty($bahan1) && strcasecmp($bahan1, 'Semua Jenis Bahan') !== 0 && is_numeric($bahan1)) {
            $worksheetCondition = "CAT.Worksheet_id = :bahan1";
            $bindParams[':bahan1'] = (int)$bahan1;
        }

        $facetCondition = "1 = 1";
        if (!empty($fAuthor)) {
            $facetCondition .= " AND CAT.Author LIKE :fAuthor";
            $bindParams[':fAuthor'] = '%' . $fAuthor . '%';
        }
        if (!empty($fPublisher)) {
            $facetCondition .= " AND CAT.Publisher LIKE :fPublisher";
            $bindParams[':fPublisher'] = '%' . $fPublisher . '%';
        }
        if (!empty($fPublishLoc)) {
            $facetCondition .= " AND CAT.PublishLocation LIKE :fPublishLoc";
            $bindParams[':fPublishLoc'] = '%' . $fPublishLoc . '%';
        }
        if (!empty($fPublishYear)) {
            $facetCondition .= " AND CAT.PublishYear LIKE :fPublishYear";
            $bindParams[':fPublishYear'] = '%' . $fPublishYear . '%';
        }
        if (!empty($fSubject)) {
            $facetCondition .= " AND CAT.Subject LIKE :fSubject";
            $bindParams[':fSubject'] = '%' . $fSubject . '%';
        }
        if (!empty($fBahasa)) {
            $facetCondition .= " AND CAT.Languages LIKE :fBahasa";
            $bindParams[':fBahasa'] = '%' . $fBahasa . '%';
        }

        $limitOffset = (int)$limitAwal;
        $limitCount = (int)$limit;

        $sql = "SELECT DISTINCT 
            CAT.id AS CatalogId,
            CAT.Title AS title,
            CAT.Author AS author,
            CAT.Publisher AS publisher,
            CAT.PublishLocation AS PublishLocation,
            CAT.PublishYear AS PublishYear,
            CAT.Subject AS subject,
            CAT.Languages AS bahasa,
            CAT.CoverURL AS CoverURL,
            CAT.Worksheet_id AS worksheet_id,
            CAT.Worksheet_id AS Worksheet_id,
            (SELECT NAME FROM worksheets WHERE id = CAT.Worksheet_id) AS worksheet,
            (SELECT ISSERIAL FROM worksheets WHERE id = CAT.Worksheet_id) AS ISSERIAL,
            (SELECT COUNT(1) FROM collections WHERE CATALOG_ID = CAT.ID AND STATUS_ID = 1 AND (BookingExpiredDate < NOW() OR BookingExpiredDate IS NULL)) AS JML_BUKU,
            (SELECT COUNT(1) FROM collections WHERE CATALOG_ID = CAT.ID) AS ALL_BUKU,
            (SELECT GROUP_CONCAT(DISTINCT SUBSTR(fileURL, INSTR(fileURL, '.') + 1) SEPARATOR ', ') FROM catalogfiles WHERE Catalog_id = CAT.ID) AS KONTEN_DIGITAL
        FROM catalogs CAT
        WHERE {$searchCondition}
          {$locationCondition}
          AND {$worksheetCondition}
          AND {$facetCondition}
          AND (CAT.isopac = 1 OR CAT.isopac IS NULL)
        LIMIT {$limitOffset}, {$limitCount}";

        $command = Yii::$app->db->createCommand($sql);
        $command->bindValues($bindParams);
        return $command->queryAll();
    }

    private function getDirectSearchCount($keyword, $ruas, $bahan1, $location, $fAuthor, $fPublisher, $fPublishLoc, $fPublishYear, $fSubject, $fBahasa) {
        $bindParams = [':keyword' => $keyword];

        switch ($ruas) {
            case 'Judul':
                $searchCondition = "(CAT.Title LIKE :keyword OR EXISTS (SELECT 1 FROM catalog_ruas R WHERE R.TAG IN ('240','245','246','440','740') AND R.Value LIKE :keyword AND R.CATALOGID = CAT.ID))";
                break;
            case 'Pengarang':
                $searchCondition = "(CAT.Author LIKE :keyword OR EXISTS (SELECT 1 FROM catalog_ruas R WHERE R.TAG IN ('100','110','111','700','710','711','800','810','811') AND R.Value LIKE :keyword AND R.CATALOGID = CAT.ID))";
                break;
            case 'Penerbit':
                $searchCondition = "(CAT.Publisher LIKE :keyword OR EXISTS (SELECT 1 FROM catalog_ruas R WHERE R.TAG IN ('260','264') AND R.Value LIKE :keyword AND R.CATALOGID = CAT.ID))";
                break;
            case 'Subyek':
                $searchCondition = "(CAT.Subject LIKE :keyword OR EXISTS (SELECT 1 FROM catalog_ruas R WHERE R.TAG IN ('600','610','611','650','651') AND R.Value LIKE :keyword AND R.CATALOGID = CAT.ID))";
                break;
            case 'Nomor Panggil':
                $searchCondition = "(CAT.CallNumber LIKE :keyword OR EXISTS (SELECT 1 FROM catalog_ruas R WHERE R.TAG IN ('090','084') AND R.Value LIKE :keyword AND R.CATALOGID = CAT.ID))";
                break;
            case 'ISBN':
                $searchCondition = "(CAT.ISBN LIKE :keyword OR EXISTS (SELECT 1 FROM catalog_ruas R WHERE R.TAG IN ('020') AND R.Value LIKE :keyword AND R.CATALOGID = CAT.ID))";
                break;
            case 'ISSN':
                $searchCondition = "(EXISTS (SELECT 1 FROM catalog_ruas R WHERE R.TAG IN ('022') AND R.Value LIKE :keyword AND R.CATALOGID = CAT.ID))";
                break;
            case 'ISMN':
                $searchCondition = "(EXISTS (SELECT 1 FROM catalog_ruas R WHERE R.TAG IN ('024') AND R.Value LIKE :keyword AND R.CATALOGID = CAT.ID))";
                break;
            default:
                $searchCondition = "(CAT.Title LIKE :keyword OR CAT.Author LIKE :keyword OR CAT.Publisher LIKE :keyword OR CAT.Subject LIKE :keyword OR CAT.CallNumber LIKE :keyword OR CAT.ISBN LIKE :keyword OR EXISTS (SELECT 1 FROM catalog_ruas R WHERE R.Value LIKE :keyword AND R.CATALOGID = CAT.ID))";
                break;
        }

        $locationCondition = "";
        if (!empty($location) && $location != 0) {
            $bindParams[':location'] = $location;
            $locationCondition = "AND EXISTS (SELECT 1 FROM collections col WHERE col.Catalog_id = CAT.ID AND col.Location_id = :location)";
        }

        $worksheetCondition = "1 = 1";
        if (!empty($bahan1) && strcasecmp($bahan1, 'Semua Jenis Bahan') !== 0 && is_numeric($bahan1)) {
            $worksheetCondition = "CAT.Worksheet_id = :bahan1";
            $bindParams[':bahan1'] = (int)$bahan1;
        }

        $facetCondition = "1 = 1";
        if (!empty($fAuthor)) {
            $facetCondition .= " AND CAT.Author LIKE :fAuthor";
            $bindParams[':fAuthor'] = '%' . $fAuthor . '%';
        }
        if (!empty($fPublisher)) {
            $facetCondition .= " AND CAT.Publisher LIKE :fPublisher";
            $bindParams[':fPublisher'] = '%' . $fPublisher . '%';
        }
        if (!empty($fPublishLoc)) {
            $facetCondition .= " AND CAT.PublishLocation LIKE :fPublishLoc";
            $bindParams[':fPublishLoc'] = '%' . $fPublishLoc . '%';
        }
        if (!empty($fPublishYear)) {
            $facetCondition .= " AND CAT.PublishYear LIKE :fPublishYear";
            $bindParams[':fPublishYear'] = '%' . $fPublishYear . '%';
        }
        if (!empty($fSubject)) {
            $facetCondition .= " AND CAT.Subject LIKE :fSubject";
            $bindParams[':fSubject'] = '%' . $fSubject . '%';
        }
        if (!empty($fBahasa)) {
            $facetCondition .= " AND CAT.Languages LIKE :fBahasa";
            $bindParams[':fBahasa'] = '%' . $fBahasa . '%';
        }

        $sql = "SELECT COUNT(DISTINCT CAT.id)
        FROM catalogs CAT
        WHERE {$searchCondition}
          {$locationCondition}
          AND {$worksheetCondition}
          AND {$facetCondition}
          AND (CAT.isopac = 1 OR CAT.isopac IS NULL)";

        $command = Yii::$app->db->createCommand($sql);
        $command->bindValues($bindParams);
        return (int)$command->queryScalar();
    }

}
