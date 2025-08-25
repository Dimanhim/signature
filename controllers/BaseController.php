<?php

namespace app\controllers;

use Yii;
use himiklab\sortablegrid\SortableGridAction;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class BaseController extends Controller
{
    /**
     * @param $action
     * @return bool
     * @throws \yii\web\BadRequestHttpException
     */
    public function beforeAction($action)
    {
        if(($model = $this->getModel()) && ($modelName = $model::modelName())) {
            $this->view->title = $modelName;
        }

        return parent::beforeAction($action);
    }

    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        //'delete' => ['POST'],
                    ],

                ],
                'access' => [
                    'class' => AccessControl::className(),
                    'rules' => [
                        [
                            'allow' => true,
                            'roles' => ['@'],
                        ],
                    ]
                ],
            ],

        );
    }

    /**
     * @return array
     */
    public function actions()
    {
        return [
            'sort' => [
                'class' => SortableGridAction::className(),
                'modelName' => $this->behaviors()['className'],
            ],
        ];
    }

    /**
     * Deletes an existing ClinicType model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if($model->delete()) {
            Yii::$app->session->setFlash('success', 'Запись удалена успешно');
        }
        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Finds the ClinicType model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return ClinicType the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function findModel($id)
    {
        if(array_key_exists('className', $this->behaviors()) && ($model = $this->getModel())) {
            if(($findModel = $model::findOne(['id' => $id])) !== null) {
                return $findModel;
            }
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function getModel()
    {
        $behaviors = $this->behaviors();
        if(array_key_exists('className', $this->behaviors())) {
            return $behaviors['className'];
        }
        return false;
    }
}
