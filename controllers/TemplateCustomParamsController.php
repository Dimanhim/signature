<?php

namespace app\controllers;

use app\models\Template;
use app\models\TemplateCustomParams;
use app\models\TemplateSearch;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use Yii;

/**
 * IndustryController implements the CRUD actions for Industry model.
 */
class TemplateCustomParamsController extends BaseController
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'className' => TemplateCustomParams::className(),
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
                'access' => [
                    'class' => AccessControl::className(),
                    'rules' => [
                        [
                            'allow' => true,
                            'roles' => ['admin'],
                        ],
                    ]
                ],
            ]
        );
    }

    /**
     * Creates a new TemplateCustomParams model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate($template_id)
    {
        $model = new TemplateCustomParams();
        $model->template_id = $template_id;

        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {
                if ($model->save()) {
                    return $this->redirect(['template/update?id=' . $template_id]);
                }
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Industry model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate()
    {
        $id = Yii::$app->request->get('id');
        $template_id = Yii::$app->request->get('template_id');

        $model = $this->findModel($id);
        $model->template_id = $template_id;

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['template/update?id=' . $template_id]);
        }

        return $this->render('update', [
            'model' => $model
        ]);
    }
}
