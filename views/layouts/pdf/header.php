<?php 

$_formaPago = !empty($dataPDF['formaPago']['valor']) ? $dataPDF['formaPago']['valor'] : 'N/A';
$_metodoPago = !empty($dataPDF['metodoPago']['valor']) ? $dataPDF['metodoPago']['valor'] : 'N/A';

?>

<div style="text-align: center; font-size: 14px; font-weight: bold;">
    <div style="font-size: 12px; text-align: left;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="text-align: start; padding: 8px; width: 55%;">
                        <?= yii\helpers\Html::img('@web/images/icons8-empresa.png') ?>
                    </th>
                    <th style="padding: 8px; text-align: start;">
                        <b>EMISOR:</b><br>
                        <p style="font-weight: normal;">
                            <?= htmlspecialchars($dataPDF['emisor']['nombre'] ?? '', ENT_QUOTES, 'UTF-8') ?><br>
                            CP. 000000<br>
                            RFC: <?= htmlspecialchars($dataPDF['emisor']['rfc'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                        </p>
                    </th>
                </tr>
            </thead>
        </table>
        <table style="width: 100%; border-collapse: collapse;">
            <tbody>
                <tr>
                    <td style="width: 55%;">
                        <b>RECEPTOR:</b><br><br>
                        <?= htmlspecialchars($dataPDF['receptor']['nombre'] ?? '', ENT_QUOTES, 'UTF-8') ?><br>
                        CP. <?= htmlspecialchars($dataPDF['receptor']['domicilioFiscalReceptor'] ?? '', ENT_QUOTES, 'UTF-8') ?><br>
                        RFC: <?= htmlspecialchars($dataPDF['receptor']['rfc'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                        <br><br>
                        <p><b>Regimen Fiscal:</b> <?= htmlspecialchars($dataPDF['receptor']['regimenFiscal']['valor'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?></p>
                    </td>
                    <td style="width: 45%; font-size: small;">
                        <p><b>Comprobante Digital:</b> (<?= htmlspecialchars($dataPDF['tipoComprobante']['clave'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?>) <?= htmlspecialchars($dataPDF['tipoComprobante']['valor'] ?? 'Desconocido', ENT_QUOTES, 'UTF-8') ?></p>
                        <br>
                        <p><b>Factura:</b> <?= htmlspecialchars(($dataPDF['serie'] ?? '') . ($dataPDF['folio'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
                        <br>
                        <p><b>Fecha:</b>
                            <?php
                            $fechaValida = $dataPDF['fecha'] ?? null;
                            if ($fechaValida && strtotime($fechaValida)) {
                                echo date('d/m/Y', strtotime($fechaValida));
                            } else {
                                echo 'Fecha no válida'; // O ''
                            }
                            ?>
                        </p>
                        <br>
                        <p><b>Lugar de expedición: </b><?= htmlspecialchars($dataPDF['lugarExpedicion'] ?? '', ENT_QUOTES, 'UTF-8') ?> </p>
                        <br>
                        <p><b>Forma de Pago:</b> <?= htmlspecialchars($_formaPago ?? 'N/A', ENT_QUOTES, 'UTF-8') ?> </p>
                        <br>
                        <p><b>Metodo de Pago:</b> <?= htmlspecialchars($_metodoPago ?? 'N/A', ENT_QUOTES, 'UTF-8') ?></p>
                        <br>
                        <p><b>Uso CFDI: </b> <?= htmlspecialchars($dataPDF['receptor']['usoCFDI']['valor'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?></p>
                        <br>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <br>
</div>