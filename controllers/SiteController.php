<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\form\EgresoForm;
use app\models\form\IngresoForm;
use app\models\form\PagoForm;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionIngresos()
    {
        $model = new IngresoForm();

        $modalData = [
            'xmlUrl' => Yii::$app->session->getFlash('xmlUrl'),
            'pdfUrl' => Yii::$app->session->getFlash('pdfUrl'),
            'fileName' => Yii::$app->session->getFlash('fileName'),
        ];

        return $this->render('ingresos', [
            'model' => $model,
            'modalData' => $modalData
        ]);
    }

    public function actionEgresos()
    {
        $model = new EgresoForm();

        $modalData = [
            'xmlUrl' => Yii::$app->session->getFlash('xmlUrl'),
            'pdfUrl' => Yii::$app->session->getFlash('pdfUrl'),
            'fileName' => Yii::$app->session->getFlash('fileName'),
        ];

        return $this->render('egresos', [
            'model' => $model,
            'modalData' => $modalData
        ]);
    }

    public function actionPagos()
    {
        $model = new PagoForm();

        $modalData = [
            'xmlUrl' => Yii::$app->session->getFlash('xmlUrl'),
            'pdfUrl' => Yii::$app->session->getFlash('pdfUrl'),
            'fileName' => Yii::$app->session->getFlash('fileName'),
        ];

        return $this->render('pagos', [
            'model' => $model,
            'modalData' => $modalData
        ]);
    }
}
