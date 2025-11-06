<div style="font-size: small;">
    <p style="font-size: medium;">"Este documento es una representaci&oacute;n impresa de un CFDI"</p>
    <div><b>Folio fiscal: </b><?= htmlspecialchars($dataPDF['uuid'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?></div>
    <div><b>Fecha y hora de certificaci&oacute;n: </b><?= htmlspecialchars($dataPDF['fechaTimbrado'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?></div>
    <div><b>N&uacute;mero de serie del Certificado de Sello Digital: </b><?= htmlspecialchars($dataPDF['noCertificadoSAT'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?></div>
</div>

<br>

<div style="font-size: small;">
    <?php
    $qrCodeSrc = $dataPDF['qr'] ?? null;

    if (!empty($qrCodeSrc)) {
        echo '<img style="width: 200px; float: left; margin-right: 10px;" src="' . htmlspecialchars($qrCodeSrc, ENT_QUOTES, 'UTF-8') . '" />';
    } else {
        Yii::warning('ManagerPDF: No se proporcionó la URL/data del QR para el footer.', 'pdf');
    }
    ?>

    <div style="overflow-wrap: break-word; word-wrap: break-word;">
        <b>Sello digital del CFDI:</b><br>
        <?= htmlspecialchars($dataPDF['sello'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?><br><br>

        <b>Sello digital del SAT:</b><br>
        <?= htmlspecialchars($dataPDF['selloSAT'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?><br><br>

        <b>Cadena Original del complemento de certificación digital del SAT:</b><br>
        <?= htmlspecialchars($dataPDF['selloCFD'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?><br>
    </div>
</div>