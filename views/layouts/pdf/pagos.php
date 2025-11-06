<?php
/* @var $this \yii\web\View */
/* @var $dataPDF array */

// 1. Validación de seguridad principal
$pagos = $dataPDF['pagos'] ?? [];

if (!is_array($pagos) || empty($pagos)) {
    Yii::warning('ManagerPDF: $dataPDF["pagos"] no es un array o está vacío.', 'pdf');
    echo '<p style="text-align: center; color: red;">No se encontraron datos del complemento de pago.</p>';
    return; // Salir si no hay nada que mostrar
}
?>

<?php foreach ($pagos as $i => $pago) : ?>

    <div class="pago-section" style="border: 1px solid #aaa; padding: 10px; margin-bottom: 15px;">
        <h3 style="text-align: center; margin-top: 0;">Complemento de Pago</h3>

        <table style="width: 100%; border-collapse: collapse; font-size: small; margin-bottom: 10px;">
            <tbody>
                <tr>
                    <td style="width: 50%;">
                        <b>Fecha de Pago:</b> <?= htmlspecialchars($pago['fecha'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?><br>
                        <b>Moneda del Pago:</b> <?= htmlspecialchars($pago['moneda'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?>
                    </td>
                    <td style="width: 50%;">
                        <b>Forma de Pago:</b><?= htmlspecialchars($pago['formaPago']['valor'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?><br>
                        <b>Monto Total del Pago:</b> $ <?= htmlspecialchars($pago['monto'] ?? '0.00', ENT_QUOTES, 'UTF-8') ?>
                    </td>
                </tr>
            </tbody>
        </table>

        <?php $iva16 = (float)($pago['impuestos']['iva16_impuesto'] ?? 0); ?>
        <?php if ($iva16 > 0): ?>
            <table style="width: 100%; border-collapse: collapse; font-size: small; margin-bottom: 10px;">
                <thead>
                    <tr style="background-color: #eee;">
                        <th colspan="2" style="padding: 5px; text-align: left;">Totales de Impuestos del Pago</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="width: 50%; padding: 5px;">
                            <b>Base IVA 16%:</b> $ <?= htmlspecialchars($pago['impuestos']['iva16_base'] ?? '0.00', ENT_QUOTES, 'UTF-8') ?>
                        </td>
                        <td style="width: 50%; padding: 5px;">
                            <b>Impuesto IVA 16%:</b> $ <?= htmlspecialchars($pago['impuestos']['iva16_impuesto'] ?? '0.00', ENT_QUOTES, 'UTF-8') ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        <?php endif; ?>

        <h4 style="margin-bottom: 5px;">Documentos Relacionados</h4>
        <table style="width: 100%; border-collapse: collapse; font-size: 10px;" class="tableConceptos">
            <thead>
                <tr style="background-color: #eee;">
                    <th style="border: 1px solid #ddd; padding: 4px;">UUID</th>
                    <th style="border: 1px solid #ddd; padding: 4px;">Serie-Folio</th>
                    <th style="border: 1px solid #ddd; padding: 4px;">Parc.</th>
                    <th style="border: 1px solid #ddd; padding: 4px;">Mon.</th>
                    <th style="border: 1px solid #ddd; padding: 4px;">Saldo Ant.</th>
                    <th style="border: 1px solid #ddd; padding: 4px;">Pagado</th>
                    <th style="border: 1px solid #ddd; padding: 4px;">Saldo Ins.</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $documentos = $pago['documentos'] ?? [];
                if (!is_array($documentos)) $documentos = [];
                ?>
                <?php foreach ($documentos as $docto) : ?>
                    <tr>
                        <td style="border: 1px solid #ddd; padding: 4px;"><?= htmlspecialchars($docto['uuid'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?></td>
                        <td style="border: 1px solid #ddd; padding: 4px;"><?= htmlspecialchars(($docto['serie'] ?? '') . '-' . ($docto['folio'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                        <td style="border: 1px solid #ddd; padding: 4px;"><?= htmlspecialchars($docto['parcialidad'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?></td>
                        <td style="border: 1px solid #ddd; padding: 4px;"><?= htmlspecialchars($docto['moneda'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?></td>
                        <td style="border: 1px solid #ddd; padding: 4px; text-align: right;">$ <?= htmlspecialchars($docto['saldoAnt'] ?? '0.00', ENT_QUOTES, 'UTF-8') ?></td>
                        <td style="border: 1px solid #ddd; padding: 4px; text-align: right;">$ <?= htmlspecialchars($docto['pagado'] ?? '0.00', ENT_QUOTES, 'UTF-8') ?></td>
                        <td style="border: 1px solid #ddd; padding: 4px; text-align: right;">$ <?= htmlspecialchars($docto['saldoIns'] ?? '0.00', ENT_QUOTES, 'UTF-8') ?></td>
                    </tr>
                <?php endforeach; ?>

                <?php if (empty($documentos)) : ?>
                    <tr>
                        <td colspan="7" style="border: 1px solid #ddd; padding: 5px; text-align: center;">No se encontraron documentos relacionados.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

    </div> <?php endforeach; ?>