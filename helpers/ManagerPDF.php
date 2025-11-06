<?php

namespace app\helpers;

use Mpdf\Mpdf;
use Exception;
use SimpleXMLElement;
use Yii;

class ManagerPDF
{
    private Mpdf $mpdf;
    private ?SimpleXMLElement $xml = null;
    private ?SimpleXMLElement $cfdi = null;
    private ?SimpleXMLElement $emisor = null;
    private ?SimpleXMLElement $receptor = null;
    private ?SimpleXMLElement $tfd = null;
    private String $url_qr = "";

    /**
     * Constructor: Carga el XML y prepara mpdf.
     * @param string $xmlString Contenido del CFDI 4.0
     */
    public function __construct(string $xmlString, string $url_qr)
    {
        $rutaTemp = Yii::getAlias('@tempDir');
        $this->url_qr = $url_qr;

        if (!is_dir($rutaTemp)) {
            if (!mkdir($rutaTemp, 0775, true)) {
                throw new Exception("No se pudo crear el directorio: $rutaTemp");
            }
        }

        $this->mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 10,
            'margin_bottom' => 10,
            'tempDir' => $rutaTemp
        ]);

        libxml_use_internal_errors(true);
        $this->xml = simplexml_load_string($xmlString);

        if ($this->xml === false) {
            throw new Exception("No se pudo parsear el XML.");
        }

        $this->xml->registerXPathNamespace('cfdi', 'http://www.sat.gob.mx/cfd/4');
        $this->xml->registerXPathNamespace('tfd', 'http://www.sat.gob.mx/TimbreFiscalDigital');
        $this->xml->registerXPathNamespace('pago20', 'http://www.sat.gob.mx/Pagos20');

        $this->cfdi = $this->xml;

        $this->emisor = $this->cfdi->xpath('//cfdi:Emisor')[0] ?? null;
        $this->receptor = $this->cfdi->xpath('//cfdi:Receptor')[0] ?? null;

        $this->tfd = $this->cfdi->xpath('//tfd:TimbreFiscalDigital')[0] ?? null;
    }

    private function renderView($path, $data)
    {
        $filePath = Yii::getAlias($path) . '.php';
        if (!file_exists($filePath)) {
            throw new Exception("No se pudo encontrar el archivo en '{$filePath}'.");
        }

        return Yii::$app->view->renderFile($filePath, $data, $this);
    }

    /**
     * Método helper para obtener un atributo de un nodo SimpleXML de forma segura.
     * Si el atributo no existe, loggea un warning y retorna un string vacío.
     */
    private function getAttr($node, string $attribute): string
    {
        // Primero, validamos que el nodo principal (ej. $this->receptor) exista.
        if (!$node) {
            // El nodo padre no existe.
            Yii::warning("ManagerPDF: El nodo padre para el atributo '$attribute' no existe en el XML.", 'pdf');
            return '';
        }

        // Segundo, validamos que el atributo exista en ese nodo.
        if (!isset($node[$attribute])) {
            Yii::warning("ManagerPDF: El atributo '$attribute' no se encontró en el nodo.", 'pdf');
            return '';
        }

        return (string)$node[$attribute];
    }

    /**
     * Genera el PDF basado en el TipoDeComprobante.
     * @param string $destination 'I' (Navegador), 'F' (Archivo), 'D' (Descarga)
     * @param string $filename Nombre del archivo (si es 'F' o 'D')
     */
    public function generate(string $destination = 'I', string $filename = 'comprobante'): void
    {
        try {
            if (str_contains($filename, ".pdf")) $filename = explode('.', $filename)[0];

            $html = $this->buildStyles();
            $html .= '<body>';

            $html .= $this->buildHeader();

            $tipoComprobante = $this->getAttr($this->cfdi, 'TipoDeComprobante');

            switch ($tipoComprobante) {
                case 'I': // Ingreso
                case 'E': // Egreso
                    $html .= $this->buildCFDIsRelacionados();
                    $html .= $this->buildConceptos();
                    $html .= $this->buildTotales();
                    break;
                case 'P': // Pago
                    $html .= $this->buildCFDIsRelacionados();
                    $html .= $this->buildPagos();
                    break;
                default:
                    $html .= $this->buildCFDIsRelacionados();
                    $html .= $this->buildConceptos();
                    $html .= $this->buildTotales();
                    break;
            }

            $html .= $this->buildFooter();
            $html .= '</body>';

            $this->mpdf->WriteHTML($html);

            $filePath = $filename;
            if ($destination === 'F') {
                $filePath = Yii::getAlias('@cfdis_path') . '/' . $filename . '.pdf';
            }

            $this->mpdf->Output($filePath, $destination);
        } catch (\Exception $e) {
            Yii::error($e->getMessage() . " LINE: " . $e->getLine() . " " . $e->getFile(), 'pdf');
        }
    }

    /**
     * Define los estilos CSS para el PDF.
     */
    private function buildStyles(): string
    {
        $cssPath = Yii::getAlias('@app/web/css/pdf-cfdi.css');

        if (!file_exists($cssPath)) {
            return '<style>/* Archivo CSS no encontrado */</style>';
        }

        return '<style>' . file_get_contents($cssPath) . '</style>';
    }

    /**
     * Construye el cabezal del PDF (Logo, Folio, Fecha, etc.)
     */
    private function buildHeader(): string
    {
        $headerPath = Yii::getAlias('@app') . "/views/layouts/pdf/header";

        $tipoComprobante = [
            'I' => 'Ingreso',
            'E' => 'Egreso',
            'T' => 'Traslado',
            'P' => 'Pago',
            'N' => 'Nómina',
        ];

        $tipo = $this->getAttr($this->cfdi, 'TipoDeComprobante');

        $buildUsoCFDI = function ($usoCFDI) {
            if (empty($usoCFDI)) return [
                'clave' => '',
                'valor' => ''
            ];

            return [
                'clave' => $usoCFDI,
                'valor' => SatCatalogos::getUsoCfdi($usoCFDI)
            ];
        };

        $buildRegimenFiscal = function ($regimenFiscal) {
            if (empty($regimenFiscal)) return [
                'clave' => '',
                'valor' => ''
            ];

            return [
                'clave' => $regimenFiscal,
                'valor' => SatCatalogos::getRegimenFiscal($regimenFiscal),
            ];
        };

        $buildMetodoPago = function ($metodoPago) {
            if (empty($metodoPago)) return [
                'clave' => '',
                'valor' => ''
            ];

            return [
                'clave' => $metodoPago,
                'valor' => SatCatalogos::getMetodoPago($metodoPago)
            ];
        };

        $buildFormaPago = function ($formaPago) {
            if (empty($formaPago)) return [
                'clave' => '',
                'valor' => ''
            ];

            return [
                'clave' => $formaPago,
                'valor' => SatCatalogos::getFormaPago($formaPago)
            ];
        };

        $dataPDF = [
            'tipoComprobante' => [
                'clave' => $tipo,
                'valor' => $tipoComprobante[$tipo] ?? 'Desconocido'
            ],
            'serie' => $this->getAttr($this->cfdi, 'Serie'),
            'folio' => $this->getAttr($this->cfdi, 'Folio'),
            'fecha' => $this->getAttr($this->cfdi, 'Fecha'),
            'lugarExpedicion' => $this->getAttr($this->cfdi, 'LugarExpedicion'),
            'formaPago' => $buildFormaPago($this->getAttr($this->cfdi, 'FormaPago')),
            'metodoPago' => $buildMetodoPago($this->getAttr($this->cfdi, 'MetodoPago')),
            'emisor' => [
                'nombre' => $this->getAttr($this->emisor, 'Nombre'),
                'rfc' => $this->getAttr($this->emisor, 'Rfc'),
                'regimenFiscal' => $buildRegimenFiscal($this->getAttr($this->emisor, 'RegimenFiscal'))
            ],
            'receptor' => [
                'nombre' => $this->getAttr($this->receptor, 'Nombre'),
                'rfc' => $this->getAttr($this->receptor, 'Rfc'),
                'regimenFiscal' => $buildRegimenFiscal($this->getAttr($this->receptor, 'RegimenFiscalReceptor')),
                'domicilioFiscalReceptor' => $this->getAttr($this->receptor, 'DomicilioFiscalReceptor'),
                'usoCFDI' => $buildUsoCFDI($this->getAttr($this->receptor, 'UsoCFDI'))
            ]
        ];

        return $this->renderView($headerPath, ['dataPDF' => $dataPDF]);
    }

    /**
     * Construye los cfdis relacionados.
     */
    private function buildCFDIsRelacionados(): string
    {
        $relatedPath = Yii::getAlias('@app') . "/views/layouts/pdf/related";

        $cfdisRelacionadosNode = $this->cfdi->xpath('//cfdi:CfdiRelacionados')[0] ?? null;

        if (!$cfdisRelacionadosNode) {
            return '';
        }

        $tipoRelacionClave = $this->getAttr($cfdisRelacionadosNode, 'TipoRelacion');
        $tipoRelacionValor = SatCatalogos::getTipoRelacion($tipoRelacionClave);

        $relacionados = $cfdisRelacionadosNode->xpath('cfdi:CfdiRelacionado');
        $uuids = [];

        foreach ($relacionados as $relacionado) {
            $uuids[] = $this->getAttr($relacionado, 'UUID');
        }

        if (empty($uuids)) {
            return '';
        }

        $dataPDF = [
            'relacion' => [
                'clave' => $tipoRelacionClave,
                'valor' => $tipoRelacionValor
            ],
            'uuids' => $uuids
        ];

        return $this->renderView($relatedPath, ['dataPDF' => $dataPDF]);
    }

    /**
     * Construye la tabla de conceptos.
     */
    private function buildConceptos(): string
    {
        $conceptosPath = Yii::getAlias('@app') . "/views/layouts/pdf/conceptos";

        $conceptos = $this->cfdi->xpath('//cfdi:Concepto');

        $dataPDF = ['conceptos' => []];

        foreach ($conceptos as $concepto) {
            $dataPDF['conceptos'][] = [
                'ClaveProdServ' => $this->getAttr($concepto, 'ClaveProdServ'),
                'Cantidad' => $this->getAttr($concepto, 'Cantidad'),
                'ClaveUnidad' => $this->getAttr($concepto, 'ClaveUnidad'),
                'Descripcion' => $this->getAttr($concepto, 'Descripcion'),
                'ValorUnitario' => number_format((float)$this->getAttr($concepto, 'ValorUnitario'), 2),
                'Importe' => number_format((float)$this->getAttr($concepto, 'Importe'), 2)
            ];
        }

        return $this->renderView($conceptosPath, ['dataPDF' => $dataPDF]);
    }

    /**
     * Construye tabla de totales.
     */
    private function buildTotales(): string
    {
        $totalesPath = Yii::getAlias('@app') . "/views/layouts/pdf/totales";

        $impuestos = $this->cfdi->xpath('//cfdi:Impuestos')[0] ?? null;
        $totalImpuestos = $impuestos ? $this->getAttr($impuestos, 'TotalImpuestosTrasladados') : '0.00';

        $dataPDF = [
            'subtotal' => number_format((float)$this->getAttr($this->cfdi, 'SubTotal'), 2),
            'totalImpuestosTrasladados' => number_format((float)$totalImpuestos, 2),
            'total' => number_format((float)$this->getAttr($this->cfdi, 'Total'), 2)
        ];

        return $this->renderView($totalesPath, ['dataPDF' => $dataPDF]);
    }

    /**
     * Construye la sección de Pagos (REP 2.0).
     * Itera sobre el complemento de Pagos y sus Documentos Relacionados.
     */
    private function buildPagos(): string
    {
        $pagosPath = Yii::getAlias('@app') . "/views/layouts/pdf/pagos";

        // Usamos el namespace 'pago20' que registramos en el constructor
        $pagosNodes = $this->cfdi->xpath('//cfdi:Complemento/pago20:Pagos/pago20:Pago');

        if (empty($pagosNodes)) {
            return '<p>Error: No se encontró el complemento de pago en el XML.</p>';
        }

        $dataPDF = ['pagos' => []];

        // --- Iteramos sobre cada PAGO (usualmente solo 1) ---
        foreach ($pagosNodes as $pago) {
            $totalesNode = $pago->xpath('pago20:Totales')[0] ?? null;

            $formaPagoClave = $this->getAttr($pago, 'FormaDePagoP');
            $formaPagoValor = 'Desconocido';
            $formaPagoValor = SatCatalogos::getFormaPago($formaPagoClave);


            $pagoData = [
                'fecha' => $this->getAttr($pago, 'FechaPago'),
                'monto' => number_format((float)$this->getAttr($pago, 'Monto'), 2),
                'moneda' => $this->getAttr($pago, 'MonedaP'),
                'formaPago' => [
                    'clave' => $formaPagoClave,
                    'valor' => $formaPagoValor
                ],
                'impuestos' => [
                    'iva16_base' => number_format((float)$this->getAttr($totalesNode, 'TotalTrasladosBaseIVA16'), 2),
                    'iva16_impuesto' => number_format((float)$this->getAttr($totalesNode, 'TotalTrasladosImpuestoIVA16'), 2),
                ],
                'documentos' => []
            ];

            // --- Iteramos sobre cada DOCUMENTO RELACIONADO ---
            $doctosNodes = $pago->xpath('pago20:DoctoRelacionado');

            foreach ($doctosNodes as $docto) {
                $impuestosDRNode = $docto->xpath('pago20:ImpuestosDR/pago20:TrasladosDR/pago20:TrasladoDR')[0] ?? null;

                $pagoData['documentos'][] = [
                    'uuid' => $this->getAttr($docto, 'IdDocumento'),
                    'serie' => $this->getAttr($docto, 'Serie'),
                    'folio' => $this->getAttr($docto, 'Folio'),
                    'parcialidad' => $this->getAttr($docto, 'NumParcialidad'),
                    'moneda' => $this->getAttr($docto, 'MonedaDR'),
                    'saldoAnt' => number_format((float)$this->getAttr($docto, 'ImpSaldoAnt'), 2),
                    'pagado' => number_format((float)$this->getAttr($docto, 'ImpPagado'), 2),
                    'saldoIns' => number_format((float)$this->getAttr($docto, 'ImpSaldoInsoluto'), 2),
                    'impuestos' => [
                        'base' => number_format((float)$this->getAttr($impuestosDRNode, 'BaseDR'), 6), // Base es a 6 decimales
                        'impuesto' => number_format((float)$this->getAttr($impuestosDRNode, 'ImporteDR'), 2),
                        'tasa' => $this->getAttr($impuestosDRNode, 'TasaOCuotaDR'),
                    ]
                ];
            }

            $dataPDF['pagos'][] = $pagoData;
        }

        return $this->renderView($pagosPath, ['dataPDF' => $dataPDF]);
    }

    /**
     * Construye el pie de página (QR, Sellos).
     */
    private function buildFooter(): string
    {
        $footerPath = Yii::getAlias('@app') . "/views/layouts/pdf/footer";

        $dataPDF = [
            'uuid' => $this->getAttr($this->tfd, 'UUID'),
            'sello' => $this->getAttr($this->cfdi, 'Sello'),
            'selloSAT' => $this->getAttr($this->tfd, 'SelloSAT'),
            'fechaTimbrado' => $this->getAttr($this->tfd, 'FechaTimbrado'),
            'noCertificadoSAT' => $this->getAttr($this->tfd, 'NoCertificadoSAT'),
            'selloCFD' => $this->getAttr($this->tfd, 'SelloCFD'),
            'qr' => $this->url_qr
        ];

        return $this->renderView($footerPath, ['dataPDF' => $dataPDF]);
    }
}
