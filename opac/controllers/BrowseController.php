<?php

namespace opac\controllers;

use Yii;
use common\models\Opaclogs;
use common\models\Bookinglogs;
use common\models\Favorite;
use common\models\Collections;
use common\models\Catalogs;
use common\models\CollectionSearchKardeks;
use common\models\SerialArticlesSearch;
use yii\data\SqlDataProvider;
use yii\web\Session;
use yii\web\Request;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use common\components\OpacHelpers;
use common\models\OpaclogsKeyword;
class BrowseController extends \yii\web\Controller {

    public $layout = 'main';
    public $location;

    public function actionIndex() {
        $location = Yii::$app->request->cookies->getValue('location_opac_id') ? Yii::$app->request->cookies->getValue('location_opac_id') : 0;
        $jmlBookMaks = Yii::$app->config->get('JumlahBookingMaksimal');
        $bookExp = Yii::$app->config->get('BookingExpired');
        $UsulanKoleksi = Yii::$app->config->get('UsulanKoleksi');
        $dateNow = new \DateTime("now");
        $noAnggota= (Yii::$app->user->isGuest ? null : \Yii::$app->user->identity->NoAnggota );
        $booking = OpacHelpers::jumlahBooking($noAnggota);
        $alert = FALSE;

        $request = Yii::$app->request;
        if (isset($_GET['tag']) && isset($_GET['findBy']) && isset($_GET['query']) && isset($_GET['query2'])) {

            $tag = addslashes($_GET['tag']);
            $findBy = addslashes($_GET['findBy']);
            $query = addslashes($_GET['query']);
            $query2 = addslashes($_GET['query2']);
            $session = Yii::$app->session;
            $datas = $session->get('catIDmerge');
            $connection = Yii::$app->db;
            $url = Yii::$app->request->absoluteUrl;
            $waktu = date('m-d-Y H:i:s');
            $record = "tag = " . $tag . " & findBy = " . $findBy . " & keyword1 = " . $query . " & keyword2 = " . $query2;

            switch ($tag) {
                case 'Author':
                    $tagID='Pengarang';
                    break;
                case 'Subject':
                    $tagID='Subyek';
                    break;
                case 'Publisher':
                    $tagID='Penerbit';
                    break;
                case 'PublishLocation':
                   $tagID='Tempat Terbit';
                    break;
                case 'PublishYear':
                    $tagID='Tahun Terbit';
                    break;
                case 'Alphabetical':
                    $tagID='Alphabetical';
                    break;
            }
            switch ($findBy) {
                case 'Author':
                    $findByID='Pengarang';
                    break;
                case 'Subject':
                    $findByID='Subyek';
                    break;
                case 'Publisher':
                    $findByID='Penerbit';
                    break;
                case 'PublishLocation':
                   $findByID='Tempat Terbit';
                    break;
                case 'PublishYear':
                    $findByID='Tahun Terbit';
                    break;
                case 'Alphabetical':
                    $findByID='Alphabetical';
                    break;
            }
            $record = "tag = " . $tag . " & findBy = " . $findBy . " & keyword1 = " . $query . " & keyword2 = " . $query2;
            $record2 = $findByID." = ".$query." & ".$tagID." = ".$query2;


            if (Yii::$app->request->get() && addslashes($_GET['action']) === "browse") {

                $dariTGL = ( isset($_GET['dariTGL']) ) ? addslashes($_GET['dariTGL']) : '2011-11-11';
                $sampaiTGL = ( isset($_GET['sampaiTGL']) ) ? addslashes($_GET['sampaiTGL']) : '2011-11-11';
                $ip = OpacHelpers::getIP();

                if (isset($_SESSION['RiwayatPencarian'])) {
                    $temp = $_SESSION['RiwayatPencarian'];
                    $_SESSION['RiwayatPencarian'] = array_merge($temp, array(
                        array(
                            "ip" => $ip,
                            "url" => $url,
                            "action" => addslashes($_GET['action']),
                            "keyword" => $record2,
                            "bahan" => '',
                            "time" => $waktu,
                        )
                    ));
                } else {
                    $temp = array(
                        array(
                            "ip" => $ip,
                            "url" => $url,
                            "action" => addslashes($_GET['action']),
                            "keyword" => $record2,
                            "bahan" => '',
                            "time" => $waktu,
                        )
                    );
                    $_SESSION['RiwayatPencarian'] = $temp;
                }

                if (Yii::$app->user->isGuest) {
                    $noAnggota = null;
                } else {
                    $noAnggota = \Yii::$app->user->identity->NoAnggota;
                }


                $logs=[

                    'user_id' => $noAnggota,
                    'ip'      => $ip,
                    'jenis_pencarian' => addslashes($_GET['action']),
                    'keyword' => $record2,
                    'url' => $url,
                    'isLKD' => 0,
                    'findByID' => $findByID,
                    'tagID' => $tagID,
                    'query' => $query,
                    'query2' => $query2,
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

                if ($page === 1) {
                    $hasilSearch = $this->getDirectBrowseData($tag, $findBy, $query, $query2, $location, 0, $limit, $fAuthor, $fPublisher, $fPublishLoc, $fPublishYear, $fSubject, $fBahasa);
                    $count = $this->getDirectBrowseCount($tag, $findBy, $query, $query2, $location, $fAuthor, $fPublisher, $fPublishLoc, $fPublishYear, $fSubject, $fBahasa);
                    $_SESSION['countBrowse'] = $count;
                    $facets = $this->getDirectBrowseFacets($tag, $findBy, $query, $query2, $location, $fAuthor, $fPublisher, $fPublishLoc, $fPublishYear, $fSubject, $fBahasa);
                    $dataFacedAuthor = $facets['author'];
                    $dataFacedPublisher = $facets['publisher'];
                    $dataFacedPublishLocation = $facets['publishLocation'];
                    $dataFacedPublishYear = $facets['publishYear'];
                    $dataFacedSubject = $facets['subject'];
                    $dataFacedBahasa = $facets['bahasa'];
                    $_SESSION['dataFacedAuthorBrowse'] = $dataFacedAuthor;
                    $_SESSION['dataFacedPublisherBrowse'] = $dataFacedPublisher;
                    $_SESSION['dataFacedPublishLocationBrowse'] = $dataFacedPublishLocation;
                    $_SESSION['dataFacedPublishYearBrowse'] = $dataFacedPublishYear;
                    $_SESSION['dataFacedSubjectBrowse'] = $dataFacedSubject;
                    $_SESSION['dataFacedBahasaBrowse'] = $dataFacedBahasa;
                } else {
                    $hasilSearch = $this->getDirectBrowseData($tag, $findBy, $query, $query2, $location, $limitAwal, $limit, $fAuthor, $fPublisher, $fPublishLoc, $fPublishYear, $fSubject, $fBahasa);
                    if (!isset($_SESSION['countBrowse']) || empty($_SESSION['countBrowse'])) {
                        $_SESSION['countBrowse'] = $this->getDirectBrowseCount($tag, $findBy, $query, $query2, $location, $fAuthor, $fPublisher, $fPublishLoc, $fPublishYear, $fSubject, $fBahasa);
                    }
                    $count = isset($_SESSION['countBrowse']) ? (int)$_SESSION['countBrowse'] : 0;
                    if (!isset($_SESSION['dataFacedAuthorBrowse'])) {
                        $facets = $this->getDirectBrowseFacets($tag, $findBy, $query, $query2, $location, $fAuthor, $fPublisher, $fPublishLoc, $fPublishYear, $fSubject, $fBahasa);
                        $_SESSION['dataFacedAuthorBrowse'] = $facets['author'];
                        $_SESSION['dataFacedPublisherBrowse'] = $facets['publisher'];
                        $_SESSION['dataFacedPublishLocationBrowse'] = $facets['publishLocation'];
                        $_SESSION['dataFacedPublishYearBrowse'] = $facets['publishYear'];
                        $_SESSION['dataFacedSubjectBrowse'] = $facets['subject'];
                        $_SESSION['dataFacedBahasaBrowse'] = $facets['bahasa'];
                    }
                    $dataFacedAuthor = isset($_SESSION['dataFacedAuthorBrowse']) ? $_SESSION['dataFacedAuthorBrowse'] : [];
                    $dataFacedPublisher = isset($_SESSION['dataFacedPublisherBrowse']) ? $_SESSION['dataFacedPublisherBrowse'] : [];
                    $dataFacedPublishLocation = isset($_SESSION['dataFacedPublishLocationBrowse']) ? $_SESSION['dataFacedPublishLocationBrowse'] : [];
                    $dataFacedPublishYear = isset($_SESSION['dataFacedPublishYearBrowse']) ? $_SESSION['dataFacedPublishYearBrowse'] : [];
                    $dataFacedSubject = isset($_SESSION['dataFacedSubjectBrowse']) ? $_SESSION['dataFacedSubjectBrowse'] : [];
                    $dataFacedBahasa = isset($_SESSION['dataFacedBahasaBrowse']) ? $_SESSION['dataFacedBahasaBrowse'] : [];
                }


                foreach ($hasilSearch as $key => $value) {
                    $dataSearch[$key] = $value;
                    $dataTagRDA        = OpacHelpers::getTaginfo($dataSearch[$key]['CatalogId'],'336,338','a');
                    $jenisBahanRDA     = OpacHelpers::jenisBahanRDA($dataTagRDA);
                    $jenis_bahanold    = $dataSearch[$key]['worksheet'];
                    $dataSearch[$key]['worksheet'] = $jenis_bahanold." ".$jenisBahanRDA;
                    $dataSearch[$key]['authOriginal'] =  array_values(array_filter(explode("|",OpacHelpers::sqlDetailOpac('PENGARANG',$dataSearch[$key]['CatalogId']))));
                    $dataSearch[$key]['authModif'] = preg_replace("/\([^)]+\)/","",$dataSearch[$key]['authOriginal']);


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

                if (!isset($dataSearch)) {
                    $dataSearch = "";
                }
                if (isset($_POST['actions'])) {
                    echo"<pre>";
                    print_r($_POST['action']);
                    echo"</pre>";
                }
                return $this->render('resultListOpac', [
                            'countResult' => count($hasilSearch),
                            'dataResult' => $dataSearch,
                            'totalCountResult' => $count,
                            'dataResult' => $dataSearch,
                            'totalCountResult' => $count,
                            'dataFacedAuthor' => $dataFacedAuthor,
                            'dataFacedPublisher' => $dataFacedPublisher,
                            'dataFacedPublishYear' => $dataFacedPublishYear,
                            'dataFacedPublishLocation' => $dataFacedPublishLocation,
                            'dataFacedSubject' => $dataFacedSubject,
                            'dataFacedBahasa' => $dataFacedBahasa,
                            'alert' => $alert,
                ]);
            }//end if telusur
        } //endif
        if ($request->isAjax && $_GET['action'] === "favourite") {
            if (Yii::$app->user->isGuest) {
                return $this->redirect('../keanggotaan/site/login');
            }
            $model = new favorite;
            (int) $count = favorite::find()
                    ->where(['Member_Id' => \Yii::$app->user->identity->NoAnggota, 'Catalog_Id' => $_GET['catID']])
                    ->count();

            if ($count == 0) {
                $model->Member_Id = \Yii::$app->user->identity->NoAnggota;
                $model->Catalog_Id = $_GET['catID'];
                $model->CreateDate = new Expression('NOW()');
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
        } //end if favorite
        if ($request->isAjax && $_GET['action'] === "showCollection") {

            $catID = $_GET['catID'];
            if ($_GET['serial'] == 1) {
                $searchModel = new CollectionSearchKardeks;
                $params['CatalogId'] = $catID;
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
                //'pagination'=> false,
                'pagination' => [ 'pageSize' => 20,],
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
                        'catID' => $catID,
            ]);
        }
        if ($request->isAjax && $_GET['action'] === "showArticle") {
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
            $catID = addslashes($_GET['catID']);
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

            return $this->renderPartial('_kontendigitallist', [
                        'dataProviderCollectionList' => $dataProviderCollectionList,
                        'countCollectionList' => $countCollectionList,
                        'dataCollectionList' => $dataCollectionList,
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

            $tambahJam= explode(":",$bookExp);


            $dateAdd->modify("+".$tambahJam[0]." hours +".$tambahJam[1]." minutes +".$tambahJam[2]." seconds");

            if (!$cekBooking) {

                    $modelLogs = new Bookinglogs;
                    $modelLogs->memberId = $noAnggota;
                    $modelLogs->collectionId = $colID;
                    $modelLogs->bookingDate = $dateNow->format("Y-m-d H:i:sO");
                    $modelLogs->bookingExpired = $dateAdd->format("Y-m-d H:i:sO");
                    $modelLogs->save();

                    $params2 = [':ID' => $colID, ':BookingMemberID' => $noAnggota, ':BookingExpiredDate' => $dateAdd->format("Y-m-d H:i:sO")];
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
            $catID = addslashes($_GET['catID']);
            $sqlSearch = "
		SELECT CAT.id CatalogId,CAT.title kalimat2,CAT.author,CAT.publisher,CAT.PublishLocation,CAT.PublishYear,CAT.subject,CAT.CoverURL ,CAT.Worksheet_id, 
                (SELECT NAME FROM worksheets WHERE id=CAT.Worksheet_id) worksheet,
                (SELECT COUNT(1) FROM collections WHERE CATALOG_ID=CAT.ID AND STATUS_ID=1 AND BookingExpiredDate < now()) JML_BUKU,
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

            return $this->renderAjax('_search', [
                        'dataResult' => $dataSearch,
                        'booking' => $booking,
            ]);
        }
        if ($request->isAjax && $_GET['action'] === "showBookingDetail") {

            if (Yii::$app->user->isGuest) {
                $noAnggota = null;
                return $this->renderPartial('_bookingList', ['noAnggota' => $noAnggota,]);
            } else {
                $dateNow = new \DateTime("now");
                $noAnggota = \Yii::$app->user->identity->NoAnggota;
                $booking = Collections::find()
                        ->select([
                            'collections.BookingExpiredDate',
                            'catalogs.Title',
                        ])
                        ->leftJoin('catalogs', '`catalogs`.`ID` = `collections`.`Catalog_id`')
                        ->andWhere('BookingMemberID ="' . $noAnggota.'"')
                        ->andWhere('BookingExpiredDate >  "' . $dateNow->format("Y-m-d H:i:s") . '"')
                        ->all();
                return $this->renderPartial('_bookingList', [
                            'booking' => $booking,
                            'noAnggota' => $noAnggota,
                ]);
            }
        }
        //buat nampilin browse nya
        if (isset($_GET['tag'])) {

            if (isset($_GET['findBy'])) {

                $sql = "call BrowseOpac('" . addslashes($_GET['findBy']) . "','','','',".$location.");";
                $dataProvider = new SqlDataProvider([
                    'sql' => $sql,
                    'pagination' => false,
                ]);
                $model = $dataProvider->getModels();


                if (isset($_GET['query'])) {
                    $sql2 = "call BrowseOpac('" . addslashes($_GET['findBy']) . "','" . addslashes($_GET['tag']) . "','" . addslashes($_GET['query']) . "','',".$location.");";
                    $dataProvider2 = new SqlDataProvider([
                        'sql' => $sql2,
                        'pagination' => false,
                    ]);
                    $model2 = $dataProvider2->getModels();
                    return $this->render('index', [
                                'model' => $model,
                                'model2' => $model2,
                    ]);
                }
                return $this->render('index', [
                            'model' => $model,
                ]);
            }
        }
        return $this->render('index');
    }

    private function getDirectBrowseData($tag, $findBy, $query, $query2, $location, $limitOffset, $limitCount, $fAuthor, $fPublisher, $fPublishLoc, $fPublishYear, $fSubject, $fBahasa) {
        $bindParams = [];

        $tagCondition = "1 = 1";
        if (!empty($query2)) {
            switch ($tag) {
                case 'Author':
                    $tagCondition = "CAT.Author = :query2";
                    $bindParams[':query2'] = $query2;
                    break;
                case 'Subject':
                    $tagCondition = "CAT.Subject = :query2";
                    $bindParams[':query2'] = $query2;
                    break;
                case 'Publisher':
                    $tagCondition = "CAT.Publisher = :query2";
                    $bindParams[':query2'] = $query2;
                    break;
                case 'PublishLocation':
                    $tagCondition = "CAT.PublishLocation = :query2";
                    $bindParams[':query2'] = $query2;
                    break;
                case 'PublishYear':
                    $tagCondition = "CAT.PublishYear = :query2";
                    $bindParams[':query2'] = $query2;
                    break;
            }
        }

        $findByCondition = "1 = 1";
        if (!empty($query)) {
            switch ($findBy) {
                case 'Alphabetical':
                    $findByCondition = "1 = 1";
                    break;
                case 'Author':
                    $findByCondition = "CAT.Author = :query";
                    $bindParams[':query'] = $query;
                    break;
                case 'Subject':
                    $findByCondition = "CAT.Subject = :query";
                    $bindParams[':query'] = $query;
                    break;
                case 'Publisher':
                    $findByCondition = "CAT.Publisher = :query";
                    $bindParams[':query'] = $query;
                    break;
                case 'PublishLocation':
                    $findByCondition = "CAT.PublishLocation = :query";
                    $bindParams[':query'] = $query;
                    break;
                case 'PublishYear':
                    $findByCondition = "CAT.PublishYear = :query";
                    $bindParams[':query'] = $query;
                    break;
            }
        }

        $locationCondition = "";
        if (!empty($location) && $location != 0) {
            $bindParams[':location'] = $location;
            $locationCondition = "AND EXISTS (SELECT 1 FROM collections col WHERE col.Catalog_id = CAT.ID AND col.Location_id = :location)";
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

        $offset = (int)$limitOffset;
        $countLimit = (int)$limitCount;

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
        WHERE {$tagCondition}
          AND {$findByCondition}
          {$locationCondition}
          AND {$facetCondition}
          AND (CAT.isopac = 1 OR CAT.isopac IS NULL)
        LIMIT {$offset}, {$countLimit}";

        $command = Yii::$app->db->createCommand($sql);
        if (!empty($bindParams)) {
            $command->bindValues($bindParams);
        }
        return $command->queryAll();
    }

    private function getDirectBrowseCount($tag, $findBy, $query, $query2, $location, $fAuthor, $fPublisher, $fPublishLoc, $fPublishYear, $fSubject, $fBahasa) {
        $bindParams = [];

        $tagCondition = "1 = 1";
        if (!empty($query2)) {
            switch ($tag) {
                case 'Author':
                    $tagCondition = "CAT.Author = :query2";
                    $bindParams[':query2'] = $query2;
                    break;
                case 'Subject':
                    $tagCondition = "CAT.Subject = :query2";
                    $bindParams[':query2'] = $query2;
                    break;
                case 'Publisher':
                    $tagCondition = "CAT.Publisher = :query2";
                    $bindParams[':query2'] = $query2;
                    break;
                case 'PublishLocation':
                    $tagCondition = "CAT.PublishLocation = :query2";
                    $bindParams[':query2'] = $query2;
                    break;
                case 'PublishYear':
                    $tagCondition = "CAT.PublishYear = :query2";
                    $bindParams[':query2'] = $query2;
                    break;
            }
        }

        $findByCondition = "1 = 1";
        if (!empty($query)) {
            switch ($findBy) {
                case 'Alphabetical':
                    $findByCondition = "1 = 1";
                    break;
                case 'Author':
                    $findByCondition = "CAT.Author = :query";
                    $bindParams[':query'] = $query;
                    break;
                case 'Subject':
                    $findByCondition = "CAT.Subject = :query";
                    $bindParams[':query'] = $query;
                    break;
                case 'Publisher':
                    $findByCondition = "CAT.Publisher = :query";
                    $bindParams[':query'] = $query;
                    break;
                case 'PublishLocation':
                    $findByCondition = "CAT.PublishLocation = :query";
                    $bindParams[':query'] = $query;
                    break;
                case 'PublishYear':
                    $findByCondition = "CAT.PublishYear = :query";
                    $bindParams[':query'] = $query;
                    break;
            }
        }

        $locationCondition = "";
        if (!empty($location) && $location != 0) {
            $bindParams[':location'] = $location;
            $locationCondition = "AND EXISTS (SELECT 1 FROM collections col WHERE col.Catalog_id = CAT.ID AND col.Location_id = :location)";
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
        WHERE {$tagCondition}
          AND {$findByCondition}
          {$locationCondition}
          AND {$facetCondition}
          AND (CAT.isopac = 1 OR CAT.isopac IS NULL)";

        $command = Yii::$app->db->createCommand($sql);
        if (!empty($bindParams)) {
            $command->bindValues($bindParams);
        }
        return (int)$command->queryScalar();
    }

    private function getDirectBrowseFacets($tag, $findBy, $query, $query2, $location, $fAuthor, $fPublisher, $fPublishLoc, $fPublishYear, $fSubject, $fBahasa) {
        $bindParams = [];

        $tagCondition = "1 = 1";
        if (!empty($query2)) {
            switch ($tag) {
                case 'Author':
                    $tagCondition = "CAT.Author = :query2";
                    $bindParams[':query2'] = $query2;
                    break;
                case 'Subject':
                    $tagCondition = "CAT.Subject = :query2";
                    $bindParams[':query2'] = $query2;
                    break;
                case 'Publisher':
                    $tagCondition = "CAT.Publisher = :query2";
                    $bindParams[':query2'] = $query2;
                    break;
                case 'PublishLocation':
                    $tagCondition = "CAT.PublishLocation = :query2";
                    $bindParams[':query2'] = $query2;
                    break;
                case 'PublishYear':
                    $tagCondition = "CAT.PublishYear = :query2";
                    $bindParams[':query2'] = $query2;
                    break;
            }
        }

        $findByCondition = "1 = 1";
        if (!empty($query)) {
            switch ($findBy) {
                case 'Alphabetical':
                    $findByCondition = "1 = 1";
                    break;
                case 'Author':
                    $findByCondition = "CAT.Author = :query";
                    $bindParams[':query'] = $query;
                    break;
                case 'Subject':
                    $findByCondition = "CAT.Subject = :query";
                    $bindParams[':query'] = $query;
                    break;
                case 'Publisher':
                    $findByCondition = "CAT.Publisher = :query";
                    $bindParams[':query'] = $query;
                    break;
                case 'PublishLocation':
                    $findByCondition = "CAT.PublishLocation = :query";
                    $bindParams[':query'] = $query;
                    break;
                case 'PublishYear':
                    $findByCondition = "CAT.PublishYear = :query";
                    $bindParams[':query'] = $query;
                    break;
            }
        }

        $locationCondition = "";
        if (!empty($location) && $location != 0) {
            $bindParams[':location'] = $location;
            $locationCondition = "AND EXISTS (SELECT 1 FROM collections col WHERE col.Catalog_id = CAT.ID AND col.Location_id = :location)";
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

        $baseWhere = "{$tagCondition} AND {$findByCondition} {$locationCondition} AND {$facetCondition} AND (CAT.isopac = 1 OR CAT.isopac IS NULL)";

        $maxAuthor = (int)Yii::$app->config->get('FacedAuthorMax') ?: 10;
        $maxPub = (int)Yii::$app->config->get('FacedPublisherMax') ?: 10;
        $maxLoc = (int)Yii::$app->config->get('FacedPublishLocationMax') ?: 10;
        $maxYear = (int)Yii::$app->config->get('FacedPublishYearMax') ?: 10;
        $maxSub = (int)Yii::$app->config->get('FacedSubjectMax') ?: 10;
        $maxLang = (int)Yii::$app->config->get('FacedBahasaMax') ?: 10;

        $facets = [
            'author' => [],
            'publisher' => [],
            'publishLocation' => [],
            'publishYear' => [],
            'subject' => [],
            'bahasa' => []
        ];

        try {
            $sqlAuthor = "SELECT CAT.Author AS Author, COUNT(1) AS jml FROM catalogs CAT WHERE {$baseWhere} AND CAT.Author IS NOT NULL AND CAT.Author != '' AND CAT.Author != '-' GROUP BY CAT.Author ORDER BY jml DESC LIMIT {$maxAuthor}";
            $cmdAuthor = Yii::$app->db->createCommand($sqlAuthor);
            if (!empty($bindParams)) {
                $cmdAuthor->bindValues($bindParams);
            }
            $facets['author'] = OpacHelpers::facedGenerator($cmdAuthor->queryAll(), 'Author');

            $sqlPub = "SELECT CAT.Publisher AS Publisher, COUNT(1) AS jml FROM catalogs CAT WHERE {$baseWhere} AND CAT.Publisher IS NOT NULL AND CAT.Publisher != '' AND CAT.Publisher != '-' GROUP BY CAT.Publisher ORDER BY jml DESC LIMIT {$maxPub}";
            $cmdPub = Yii::$app->db->createCommand($sqlPub);
            if (!empty($bindParams)) {
                $cmdPub->bindValues($bindParams);
            }
            $facets['publisher'] = OpacHelpers::facedGenerator($cmdPub->queryAll(), 'Publisher');

            $sqlLoc = "SELECT CAT.PublishLocation AS PublishLocation, COUNT(1) AS jml FROM catalogs CAT WHERE {$baseWhere} AND CAT.PublishLocation IS NOT NULL AND CAT.PublishLocation != '' AND CAT.PublishLocation != '-' GROUP BY CAT.PublishLocation ORDER BY jml DESC LIMIT {$maxLoc}";
            $cmdLoc = Yii::$app->db->createCommand($sqlLoc);
            if (!empty($bindParams)) {
                $cmdLoc->bindValues($bindParams);
            }
            $facets['publishLocation'] = OpacHelpers::facedGenerator($cmdLoc->queryAll(), 'PublishLocation');

            $sqlYear = "SELECT CAT.PublishYear AS PublishYear, COUNT(1) AS jml FROM catalogs CAT WHERE {$baseWhere} AND CAT.PublishYear IS NOT NULL AND CAT.PublishYear != '' AND CAT.PublishYear != '-' GROUP BY CAT.PublishYear ORDER BY jml DESC LIMIT {$maxYear}";
            $cmdYear = Yii::$app->db->createCommand($sqlYear);
            if (!empty($bindParams)) {
                $cmdYear->bindValues($bindParams);
            }
            $facets['publishYear'] = OpacHelpers::facedGenerator($cmdYear->queryAll(), 'PublishYear');

            $sqlSub = "SELECT CAT.Subject AS SUBJECT, COUNT(1) AS jml FROM catalogs CAT WHERE {$baseWhere} AND CAT.Subject IS NOT NULL AND CAT.Subject != '' AND CAT.Subject != '-' GROUP BY CAT.Subject ORDER BY jml DESC LIMIT {$maxSub}";
            $cmdSub = Yii::$app->db->createCommand($sqlSub);
            if (!empty($bindParams)) {
                $cmdSub->bindValues($bindParams);
            }
            $facets['subject'] = OpacHelpers::facedGenerator($cmdSub->queryAll(), 'SUBJECT');

            $sqlLang = "SELECT CAT.Languages AS bahasa, COUNT(1) AS jml FROM catalogs CAT WHERE {$baseWhere} AND CAT.Languages IS NOT NULL AND CAT.Languages != '' AND CAT.Languages != '-' GROUP BY CAT.Languages ORDER BY jml DESC LIMIT {$maxLang}";
            $cmdLang = Yii::$app->db->createCommand($sqlLang);
            if (!empty($bindParams)) {
                $cmdLang->bindValues($bindParams);
            }
            $facets['bahasa'] = OpacHelpers::facedGenerator($cmdLang->queryAll(), 'bahasa');
        } catch (\Exception $eFacet) {
            Yii::warning($eFacet->getMessage(), 'opac.facet.browse');
        }

        return $facets;
    }
}
