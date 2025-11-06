<?php

use app\helpers\SatCatalogos;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

/** @var yii\web\View $this */
/** @var app\models\form\EgresoForm $model */
/** @var array $modalData */

$this->title = 'Timbrar Factura de Egreso (E) (Nota de Crédito)';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-egresos">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        Complete el siguiente formulario para generar un CFDI 4.0 de Egreso (Nota de Crédito).
        Los datos del Emisor se tomarán de la configuración global.
    </p>

    <div class="row">
        <div class="col-lg-12">

            <?php $form = ActiveForm::begin([
                'id' => 'formCFDI',
                'action' => ['timbrado/egreso'],
                'method' => 'post',
            ]); ?>

            <div class="card shadow-sm mb-3">
                <div class="card-header">
                    <h4 class="mb-0">1. Datos del Receptor (Cliente)</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <?= $form->field($model, 'receptorRfc') ?>
                        </div>
                        <div class="col-md-8">
                            <?= $form->field($model, 'receptorNombre') ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <?= $form->field($model, 'receptorDomicilioFiscal')
                                ->textInput(['maxlength' => 5])
                                ->hint('El Código Postal del receptor.') ?>
                        </div>
                        <div class="col-md-8">
                            <?= $form->field($model, 'receptorRegimenFiscal')->dropDownList(
                                SatCatalogos::getPrompt() + SatCatalogos::getRegimenFiscal()
                            ) ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-3">
                <div class="card-header">
                    <h4 class="mb-0">2. Datos del Comprobante</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <?= $form->field($model, 'usoCfdi')->dropDownList(
                                SatCatalogos::getPrompt() + SatCatalogos::getUsoCfdi()
                            ) ?>
                        </div>
                        <div class="col-md-4">
                            <?= $form->field($model, 'formaPago')->dropDownList(
                                SatCatalogos::getPrompt() + SatCatalogos::getFormaPago()
                            ) ?>
                        </div>
                        <div class="col-md-4">
                            <?= $form->field($model, 'metodoPago')->dropDownList(
                                SatCatalogos::getPrompt('Seleccione método...') + SatCatalogos::getMetodoPago()
                            ) ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-3">
                <div class="card-header">
                    <h4 class="mb-0">3. Datos del CFDI Relacionado</h4>
                    <small>Factura (UUID) a la que se aplica esta nota de crédito.</small>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <?= $form->field($model, 'tipoRelacion')->dropDownList(
                                SatCatalogos::getPrompt() + SatCatalogos::getTipoRelacion()
                            ) ?>
                        </div>
                        <div class="col-md-8">
                            <?= $form->field($model, 'cfdiRelacionadoUuid')->textInput([
                                'placeholder' => 'XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX'
                            ])->hint('El UUID de la factura original.') ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-3">
                <div class="card-header">
                    <h4 class="mb-0">4. Concepto (Simple)</h4>
                    <small>Descripción del motivo del egreso (ej. "Devolución", "Descuento").</small>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <?= $form->field($model, 'conceptoDescripcion') ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($model, 'conceptoClaveProdServ')->dropDownList(
                                SatCatalogos::getPrompt() + SatCatalogos::getClaveProdServ()
                            ) ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'conceptoClaveUnidad')->dropDownList(
                                SatCatalogos::getPrompt() + SatCatalogos::getClaveUnidad()
                            ) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <?= $form->field($model, 'conceptoCantidad')->textInput(['type' => 'number', 'step' => 'any']) ?>
                        </div>
                        <div class="col-md-3">
                            <?= $form->field($model, 'conceptoValorUnitario')->textInput(['type' => 'number', 'step' => 'any']) ?>
                        </div>
                        <div class="col-md-3">
                            <?= $form->field($model, 'conceptoObjetoImp')->dropDownList(
                                SatCatalogos::getPrompt() + [
                                    '01' => '01 - No objeto de impuesto',
                                    '02' => '02 - Sí objeto de impuesto',
                                ]
                            ) ?>
                        </div>
                        <div class="col-md-3">
                            <?= $form->field($model, 'conceptoConIva')->checkbox() ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group text-center">
                <?= Html::submitButton('Timbrar CFDI de Egreso', ['class' => 'btn btn-primary btn-lg']) ?>
            </div>

            <?php ActiveForm::end(); ?>

        </div>
    </div>

    <?= \Yii::$app->view->render('@app/views/components/modal', ['modalData' => $modalData]); ?>
</div>