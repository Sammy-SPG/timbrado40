<?php

namespace app\controllers;

use app\components\MultiFacturas;
use app\helpers\ManagerPDF;
use app\models\form\IngresoForm;
use app\models\form\EgresoForm;
use app\models\form\PagoForm;
use Yii;
use yii\web\Controller;

class TimbradoController extends Controller
{
    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionIngreso()
    {
        $model = new IngresoForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $datosParaSdk = $model->getDatosParaSdk();

            $nombreArchivo = 'I_' . $model->receptorRfc . '_' . time();

            try {
                $multiFacturas = new MultiFacturas();
                $respuesta = $multiFacturas->timbrar($datosParaSdk, $nombreArchivo);

                if (!$respuesta['resultado']) {
                    throw new \Exception($respuesta['mensaje']);
                }

                $xmlContent = file_get_contents($respuesta['xml']);
                if ($xmlContent === false) {
                    throw new \Exception('No se pudo leer el XML');
                }

                $pdfManager = new ManagerPDF($xmlContent, $respuesta['codigo_png']);
                $pdfManager->generate('F', $nombreArchivo);

                $xmlUrl = Yii::getAlias('@web') . '/documentos/cfdis/' . $nombreArchivo . ".xml";
                $pdfUrl = Yii::getAlias('@web') . '/documentos/cfdis/' . $nombreArchivo . ".pdf";

                Yii::$app->session->setFlash('xmlUrl', $xmlUrl);
                Yii::$app->session->setFlash('pdfUrl', $pdfUrl);
                Yii::$app->session->setFlash('fileName', $nombreArchivo);

                return $this->redirect(['/site/ingresos', '#' => 'modalResultados']); // IMPORTANTE EL HASH PARA ABIRL EL MODAL
            } catch (\Exception $e) {
                Yii::$app->session->setFlash('error', $e->getMessage());
                return $this->render('/site/ingresos', [
                    'model' => $model,
                    'modalData' => ['xmlUrl' => null, 'pdfUrl' => null, 'fileName' => null]
                ]);
            }
        }

        Yii::$app->session->setFlash('error', 'Hubo un error al validar los datos. Revise el formulario.');
        return $this->render('/site/ingresos', [
            'model' => $model,
            'modalData' => ['xmlUrl' => null, 'pdfUrl' => null, 'fileName' => null]
        ]);
    }

    public function actionEgreso()
    {
        $model = new EgresoForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $datosParaSdk = $model->getDatosParaSdk();

            $nombreArchivo = 'E_' . $model->receptorRfc . '_' . time();

            try {
                $multiFacturas = new MultiFacturas();
                $respuesta = $multiFacturas->timbrar($datosParaSdk, $nombreArchivo);

                if (!$respuesta['resultado']) {
                    throw new \Exception($respuesta['mensaje']);
                }

                $xmlContent = file_get_contents($respuesta['xml']);
                if ($xmlContent === false) {
                    throw new \Exception('No se pudo leer el XML');
                }

                $pdfManager = new ManagerPDF($xmlContent, $respuesta['codigo_png']);
                $pdfManager->generate('F', $nombreArchivo);

                $xmlUrl = Yii::getAlias('@web') . '/documentos/cfdis/' . $nombreArchivo . ".xml";
                $pdfUrl = Yii::getAlias('@web') . '/documentos/cfdis/' . $nombreArchivo . ".pdf";

                Yii::$app->session->setFlash('xmlUrl', $xmlUrl);
                Yii::$app->session->setFlash('pdfUrl', $pdfUrl);
                Yii::$app->session->setFlash('fileName', $nombreArchivo);

                return $this->redirect(['/site/egresos', '#' => 'modalResultados']); // IMPORTANTE EL HASH PARA ABIRL EL MODAL
            } catch (\Exception $e) {
                Yii::$app->session->setFlash('error', $e->getMessage());
                return $this->render('/site/egresos', [
                    'model' => $model,
                    'modalData' => ['xmlUrl' => null, 'pdfUrl' => null, 'fileName' => null]
                ]);
            }
        }

        Yii::$app->session->setFlash('error', 'Hubo un error al validar los datos. Revise el formulario.');
        return $this->render('/site/egresos', [
            'model' => $model,
            'modalData' => ['xmlUrl' => null, 'pdfUrl' => null, 'fileName' => null]
        ]);
    }

    public function actionPago()
    {
        $model = new PagoForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $datosParaSdk = $model->getDatosParaSdk();

            $nombreArchivo = 'P_' . $model->receptorRfc . '_' . time();

            try {
                $multiFacturas = new MultiFacturas();
                $respuesta = $multiFacturas->timbrar($datosParaSdk, $nombreArchivo);

                if (!$respuesta['resultado']) {
                    throw new \Exception($respuesta['mensaje']);
                }

                $xmlContent = file_get_contents($respuesta['xml']);
                if ($xmlContent === false) {
                    throw new \Exception('No se pudo leer el XML');
                }

                $pdfManager = new ManagerPDF($xmlContent, $respuesta['codigo_png']);
                $pdfManager->generate('F', $nombreArchivo);

                $xmlUrl = Yii::getAlias('@web') . '/documentos/cfdis/' . $nombreArchivo . ".xml";
                $pdfUrl = Yii::getAlias('@web') . '/documentos/cfdis/' . $nombreArchivo . ".pdf";

                Yii::$app->session->setFlash('xmlUrl', $xmlUrl);
                Yii::$app->session->setFlash('pdfUrl', $pdfUrl);
                Yii::$app->session->setFlash('fileName', $nombreArchivo);

                return $this->redirect(['/site/pagos', '#' => 'modalResultados']); // IMPORTANTE EL HASH PARA ABIRL EL MODAL
            } catch (\Exception $e) {
                Yii::$app->session->setFlash('error', $e->getMessage());
                return $this->render('/site/pagos', [
                    'model' => $model,
                    'modalData' => ['xmlUrl' => null, 'pdfUrl' => null, 'fileName' => null]
                ]);
            }
        }

        Yii::$app->session->setFlash('error', 'Hubo un error al validar los datos. Revise el formulario.');
        return $this->render('/site/pagos', [
            'model' => $model,
            'modalData' => ['xmlUrl' => null, 'pdfUrl' => null, 'fileName' => null]
        ]);
    }
}
