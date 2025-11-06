<?php

use app\helpers\SatCatalogos;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

/** @var yii\web\View $this */
/** @var app\models\form\PagoForm $model */
/** @var array $modalData */

$this->title = 'Timbrar Complemento de Pago (P)';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-pagos">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        Complete el siguiente formulario para generar un CFDI 4.0 de Pago (REP 2.0).
    </p>

    <div class="row">
        <div class="col-lg-12">

            <?php $form = ActiveForm::begin([
                'id' => 'formCFDI',
                'action' => ['timbrado/pago'],
                'method' => 'post',
            ]); ?>

            <div class="card shadow-sm mb-3">
                <div class="card-header">
                    <h4 class="mb-0">1. Datos del Receptor (Cliente que paga)</h4>
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
                    <h4 class="mb-0">2. Datos del Pago Recibido</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <?php
                            $fechaActual = date('Y-m-d\TH:i:s');
                            $model->fechaPago = $fechaActual;
                            ?>
                            <?= $form->field($model, 'fechaPago')->textInput(['type' => 'datetime-local']) ?>
                        </div>
                        <div class="col-md-4">
                            <?= $form->field($model, 'formaDePagoP')->dropDownList(
                                SatCatalogos::getPrompt() + SatCatalogos::getFormaPago()
                            ) ?>
                        </div>
                        <div class="col-md-4">
                            <?= $form->field($model, 'monto')->textInput(['type' => 'number', 'step' => 'any']) ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-3">
                <div class="card-header">
                    <h4 class="mb-0">3. Documento Relacionado (Factura que se paga)</h4>
                    <small>Para este demo, se asume que el monto total se aplica a una sola factura.</small>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <?= $form->field($model, 'idDocumento')->textInput(['placeholder' => 'XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX']) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <?= $form->field($model, 'serie') ?>
                        </div>
                        <div class="col-md-3">
                            <?= $form->field($model, 'folio') ?>
                        </div>
                        <div class="col-md-3">
                            <?= $form->field($model, 'numParcialidad')->textInput(['type' => 'number']) ?>
                        </div>
                        <div class="col-md-3">
                            <?= $form->field($model, 'impSaldoAnt')->textInput(['type' => 'number', 'step' => 'any']) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <?= $form->field($model, 'doctoRelacionadoConIva')->checkbox() ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group text-center">
                <?= Html::submitButton('Timbrar CFDI de Pago', ['class' => 'btn btn-primary btn-lg']) ?>
            </div>

            <?php ActiveForm::end(); ?>

        </div>
    </div>

    <?= $this->render('@app/views/components/modal', ['modalData' => $modalData]); ?>
</div>