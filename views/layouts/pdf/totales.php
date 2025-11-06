<table style="width: 100%; border-collapse: collapse;">
    <tbody>
        <tr>
            <td style="width: 60%; max-width: 60%;">
                <div>
                    <b>Observaciones:</b> <br><br>
                    <?php
                    // NOTA: Este texto es estático (hardcoded).
                    // htmlspecialchars($dataPDF['observaciones'] ?? '', ENT_QUOTES, 'UTF-8');
                    ?>
                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Dolorum voluptate omnis nemo hic tempore natus assumenda consequuntur voluptates quos inventore.</p>
                </div>
            </td>
            <td style="width: 40%; max-width: 40%;">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="text-align: right; font-weight: bold; padding: 5px;">Subtotal:</td>
                        <td style="text-align: right; padding: 5px;">$ <?= htmlspecialchars($dataPDF['subtotal'] ?? '0.00', ENT_QUOTES, 'UTF-8') ?></td>
                    </tr>
                    <tr>
                        <td style="text-align: right; font-weight: bold; padding: 5px;">Impuestos:</td>
                        <td style="text-align: right; padding: 5px;">$ <?= htmlspecialchars($dataPDF['totalImpuestosTrasladados'] ?? '0.00', ENT_QUOTES, 'UTF-8') ?></td>
                    </tr>
                    <tr>
                        <td style="text-align: right; font-weight: bold; padding: 5px;">Total:</td>
                        <td style="text-align: right; padding: 5px;">$ <?= htmlspecialchars($dataPDF['total'] ?? '0.00', ENT_QUOTES, 'UTF-8') ?></td>
                    </tr>
                </table>
            </td>
        </tr>
    </tbody>
</table>