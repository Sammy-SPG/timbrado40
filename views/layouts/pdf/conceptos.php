<?php
// 1. Inicializamos el total
$totalPiezas = 0;

$conceptos = $dataPDF['conceptos'] ?? [];
?>
<table style="width: 100%; border-collapse: collapse; border: 1px solid #ddd;" class="tableconceptos">
    <thead>
        <tr>
            <th colspan="7" style="border: 1px solid #ddd; padding: 5px; text-align: center;">Conceptos</th>
        </tr>
        <tr>
            <th style="border: 1px solid #ddd; padding: 5px; text-align: left;">#</th>
            <th style="border: 1px solid #ddd; padding: 5px; text-align: left;">ClaveProdServ</th>
            <th style="border: 1px solid #ddd; padding: 5px; text-align: left;">ClaveUnidad</th>
            <th style="border: 1px solid #ddd; padding: 5px; text-align: left;">Descripción</th>
            <th style="border: 1px solid #ddd; padding: 5px; text-align: left;">Cant</th>
            <th style="border: 1px solid #ddd; padding: 5px; text-align: left;">Valor Unitario</th>
            <th style="border: 1px solid #ddd; padding: 5px; text-align: left;">Importe</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if (!is_array($conceptos)) {
            Yii::warning('ManagerPDF: $dataPDF["conceptos"] no es un array. No se pueden mostrar.', 'pdf');
            $conceptos = []; // Forzamos array vacío para que el "empty()" de abajo funcione
        }

        foreach ($conceptos as $i => $concepto) :
            if (!is_array($concepto)) {
                Yii::warning('ManagerPDF: Se encontró un item malformado en la lista de conceptos.', 'pdf');
                continue;
            }

            $cantidad = (float)($concepto['Cantidad'] ?? 0);
            $totalPiezas += $cantidad;
        ?>
            <tr>
                <td style="border: 1px solid #ddd; padding: 5px;"><?= $i + 1 ?></td>
                <td style="border: 1px solid #ddd; padding: 5px;"><?= htmlspecialchars($concepto['ClaveProdServ'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                <td style="border: 1px solid #ddd; padding: 5px;"><?= htmlspecialchars($concepto['ClaveUnidad'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                <td style="border: 1px solid #ddd; padding: 5px;"><?= htmlspecialchars($concepto['Descripcion'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                <td style="border: 1px solid #ddd; padding: 5px;"><?= htmlspecialchars($concepto['Cantidad'] ?? '0', ENT_QUOTES, 'UTF-8') ?></td>
                <td style="border: 1px solid #ddd; padding: 5px;"><?= htmlspecialchars($concepto['ValorUnitario'] ?? '0.00', ENT_QUOTES, 'UTF-8') ?></td>
                <td style="border: 1px solid #ddd; padding: 5px;"><?= htmlspecialchars($concepto['Importe'] ?? '0.00', ENT_QUOTES, 'UTF-8') ?></td>
            </tr>
        <?php endforeach; ?>

        <?php if (empty($conceptos)) : ?>
            <tr>
                <td colspan="7" style="border: 1px solid #ddd; padding: 5px; text-align: center;">No se encontraron conceptos.</td>
            </tr>
        <?php endif; ?>
    </tbody>
    <tfoot>
        <tr>
            <th colspan="4" style="text-align: right;">Total piezas:</th>
            <td style="text-align: start;"><?= number_format($totalPiezas, 3) ?></td>
        </tr>
    </tfoot>
</table>
<br>