<?php

use yii\bootstrap5\Html;

$xmlUrl = $modalData['xmlUrl'] ?? null;
$pdfUrl = $modalData['pdfUrl'] ?? null;
$fileName = $modalData['fileName'] ?? null;

?>

<div id="modalResultados" class="modal-target-overlay">
    <div class="modal-target-content">
        <div class="modal-target-header">
            <h5 class="h5">CFDI Timbrado Exitosamente</h5>
            <!-- Este enlace "cierra" el modal quitando el hash de la URL -->
            <a href="#" class="close-button" aria-label="Close">&times;</a>
        </div>

        <div class="modal-target-body">
            <p>¡Tu factura se ha generado correctamente!</p>
            <!-- Solo mostramos esta sección si las variables existen -->
            <!-- Poblamos los enlaces directamente con PHP -->
            <?php if ($xmlUrl && $pdfUrl && $fileName) : ?>
                <p>
                    Archivo: <strong><?= Html::encode($fileName) ?>.*</strong>
                </p>
                <div class="d-grid gap-2">
                    <a id="btnDescargarXml" class="btn btn-primary" role="button" target="_blank"
                        href="<?= Html::encode($xmlUrl) ?>"
                        download="<?= Html::encode($fileName . '.xml') ?>">
                        Descargar XML
                    </a>
                    <a id="btnDescargarPdf" class="btn btn-secondary" role="button" target="_blank"
                        href="<?= Html::encode($pdfUrl) ?>"
                        download="<?= Html::encode($fileName . '.pdf') ?>">
                        Descargar PDF
                    </a>
                </div>
            <?php else : ?>
                <!-- Esto se mostraría si alguien navega a #modalResultados manualmente -->
                <p class="text-warning">No se encontraron los datos del CFDI para descargar.</p>
            <?php endif; ?>
        </div>

        <div class="modal-target-footer">
            <a href="#" class="btn btn-outline-secondary" role="button">Cerrar</a>
        </div>
    </div>
</div>