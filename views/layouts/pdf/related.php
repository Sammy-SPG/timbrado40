<?php
$uuids = $dataPDF['uuids'] ?? [];

if (!is_array($uuids)) {
    Yii::warning('ManagerPDF: $dataPDF["uuids"] no era un array en related.php. Se omite la sección.', 'pdf');
    $uuids = [];
}

if (empty($uuids)) {
    return;
}

$uuidCount = count($uuids);

$relacionValor = $dataPDF['relacion']['valor'] ?? 'Relación Desconocida';
?>

<table style="width: 100%; border-collapse: collapse;" class="tableRelacionados">
    <thead>
        <tr>
            <th colspan="2" style="border: 1px solid #ddd; padding: 5px; text-align: center;">CFDI RELACIONADOS</th>
        </tr>
        <tr>
            <th style="border: 1px solid #ddd; padding: 5px; text-align: left;">Tipo relación:</th>
            <th style="border: 1px solid #ddd; padding: 5px; text-align: left;">Lista de CFDI's relacionados:</th>
        </tr>
    </thead>
    <tbody>
        <tr style="border: 1px solid #ddd; padding: 5px; text-align: left;">
            <th rowspan="<?= $uuidCount + 1 ?>"><?= htmlspecialchars($relacionValor, ENT_QUOTES, 'UTF-8') ?></th>
        </tr>
        <?php foreach ($uuids as $uuid) : ?>
            <tr>
                <td style="border: 1px solid #ddd; padding: 5px;"><?= htmlspecialchars($uuid ?? 'UUID NO VÁLIDO', ENT_QUOTES, 'UTF-8') ?></td>
            </tr>
        <?php endforeach ?>
    </tbody>
</table>
<br>