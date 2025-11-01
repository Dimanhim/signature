<?php

namespace app\controllers;

use app\components\InfoLog;
use app\models\Api;
use app\models\DocumentSearch;
use app\models\DocumentSignature;
use app\models\Tablet;
use app\models\User;
use setasign\Fpdi\PdfReader\PdfReaderException;
use Yii;
use app\models\Document;
use app\models\Template;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;

class DocumentController extends BaseController
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'className' => Document::className(),
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    public function actionIndex($tablet_id = null, $text = false)
    {
        \Yii::beginProfile('block1');
        $model = new Document(['tablet_id' => $tablet_id]);

        if($model->load(\Yii::$app->request->post())) {

            if(!$template = $model->template) {
                $model->addDocumentError('Выбранный шаблон не существует');
            }
            elseif(!$tablet = $model->tablet) {
                $model->addDocumentError('Выбранного планшета не существует');
            }

            $clinic = null;
            $patient = null;
            $appointments = $model->getAppointment();

            if($appointments and isset($appointments['clinic_id'])) {
                $clinic = $model->getClinic($appointments['clinic_id']);
            }
            if($appointments and isset($appointments['patient_id'])) {
                $patient = $model->getPatient($appointments['patient_id']);
            }
            if(!$appointments) {
                $model->addDocumentError('Визит '.$model->appointment_id.' не найден');
            }
            if(!$clinic) {
                $model->addDocumentError('Клиника не найдена');
            }
            if(!$patient) {
                $model->addDocumentError('Пациент не найден');
            }

            //\Yii::$app->infoLog->add('appointments', $appointments, '__appointments-log.txt');
            //\Yii::$app->infoLog->add('clinic', $clinic, '__clinic-log.txt');
            //\Yii::$app->infoLog->add('patient', $patient, '__patient-log.txt');

            if($appointments and $clinic and $patient) {
                $model->setAvaliablePatterns();
                $model->setContent();
                $model->setFullContent();
                if($model->checkRequiredFields()) {
                    $totalData = $model->prepareData($clinic, $patient, $appointments);
                    //\Yii::$app->infoLog->add('total-data-log', $totalData, '__total-data-log.txt');
                    $model->setResultContent($totalData);

                    if($model->save()) {
                        $model->generatePdf(true);
                        $model->cancelDocuments();
                        $btn = '<a href="/pdf/'.$model->document_name.'" class="btn btn-sm btn-primary" target="_blank">Скачать</a>';
                        if($model->hasCustomParams()) {
                            $btn .= '<a href="/document/update/?id='.$model->id.'" class="btn btn-sm btn-warning" style="margin-left: 10px;">Заполнить параметры</a>';
                        }
                        Yii::$app->session->setFlash('success', 'Документ успешно отправлен на планшет '.$btn);
                    }

                    // раскомментировать для отладки генерации документа
                    if($text) {
                        return $model->full_content;
                    }
                }
            }
        }
        \Yii::endProfile('document');

        if($model->documentErrors) {
            Yii::$app->session->setFlash('error', $model->documentErrors[0]);
        }

        return $this->render('index', [
            'model' => $model,
        ]);
    }

    public function actionList($tablet_id = null)
    {
        $searchModel = new DocumentSearch();
        $dataProvider = $searchModel->search($this->request->queryParams, $tablet_id);
        return $this->render('list', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost) {
            $customParams = [];
            foreach ($this->request->post() as $key => $value) {
                if (is_numeric($key)) {
                    $customParams[] = ['id' => $key, 'value' => $value];
                }
            }
            $model->custom_params = $customParams;
            $model->applyCustomParams();
            $model->save();
            Yii::$app->session->setFlash('success', 'Документ успешно сохранен');
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionTemplate()
    {
        $this->layout = 'blanc';
        $model = new Document();
        return $this->render('template', [
            'model' => $model,
        ]);
    }
    public function actionTemplateTest()
    {
        $this->layout = 'blanc';


        // получаем контент
        if(!$model = Document::findOne(3)) {
            $model = new Document();
        }

        // получаем массив с подписями
        $signature_1 = $this->renderPartial('_signature_1');
        $signature_2 = $this->renderPartial('_signature_2');
        $signature_3 = $this->renderPartial('_signature_3');
        $signatures = [
            'signature_1' => $signature_1,
            'signature_2' => $signature_2,
            'signature_3' => $signature_3,
            'signature_4' => $signature_2,
        ];

        $model->contentWithSignatures($signatures);

        if($model->generatePdf()) {
            $model->saveSignatures($signatures);
        }

        $this->view->registerCssFile(\Yii::getAlias('@app/web').'/css/pdf.css?v='.mt_rand(1000,10000));
        return $model->content;
    }

    public function actionCancel($id)
    {
        $model = $this->findModel($id);
        if($model->canceled == 1) {
            $model->canceled = 0;
        }
        else {
            $model->canceled = 1;
        }
        $model->save();
        return $this->redirect(Yii::$app->request->referrer);
    }


}
