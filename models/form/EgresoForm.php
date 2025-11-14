<?php

namespace app\models\form;

use yii\base\Model;

/**
 * EgresoForm es el modelo para el formulario de timbrado de Egresos (Notas de Crédito).
 * No usa base de datos.
 */
class EgresoForm extends Model
{
    // --- DATOS DEL COMPROBANTE ---
    public $formaPago;
    public $metodoPago;


    // --- DATOS DEL RECEPTOR ---
    public $receptorRfc;
    public $receptorNombre;
    public $receptorDomicilioFiscal; // Código Postal
    public $receptorRegimenFiscal;
    public $usoCfdi;

    // --- CFDI RELACIONADO ---
    // Un Egreso (Nota de Crédito) DEBE ir relacionado a un Ingreso previo.
    // Para este demo, solo permitiremos 1 UUID.
    public $tipoRelacion;
    public $cfdiRelacionadoUuid;

    // --- DATOS DEL CONCEPTO (SIMPLE) ---
    // Para este demo, solo permitiremos 1 concepto
    public $conceptoClaveProdServ;
    public $conceptoClaveUnidad;
    public $conceptoCantidad;
    public $conceptoDescripcion;
    public $conceptoValorUnitario;
    public $conceptoObjetoImp; // 01=No objeto, 02=Sí objeto
    public $conceptoConIva; // 0=No, 1=Sí (IVA 16%)


    public function rules()
    {
        return [
            [[
                'formaPago',
                'metodoPago',
                'receptorRfc',
                'receptorNombre',
                'receptorDomicilioFiscal',
                'receptorRegimenFiscal',
                'usoCfdi',
                'tipoRelacion',
                'cfdiRelacionadoUuid',
                'conceptoClaveProdServ',
                'conceptoClaveUnidad',
                'conceptoCantidad',
                'conceptoDescripcion',
                'conceptoValorUnitario',
                'conceptoObjetoImp',
                'conceptoConIva'
            ], 'required'],
            ['receptorRfc', 'match', 'pattern' => '/^[A-Z&Ñ]{3,4}[0-9]{2}(0[1-9]|1[0-2])(0[1-9]|[12][0-9]|3[01])[A-Z0-9]{2}[0-9A]$/'],
            ['receptorDomicilioFiscal', 'match', 'pattern' => '/^[0-9]{5}$/'],
            ['cfdiRelacionadoUuid', 'match', 'pattern' => '/^[a-fA-F0-9]{8}-[a-fA-F0-9]{4}-[a-fA-F0-9]{4}-[a-fA-F0-9]{4}-[a-fA-F0-9]{12}$/', 'message' => 'El UUID debe tener el formato XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX'],
            [['conceptoCantidad', 'conceptoValorUnitario'], 'number'],
            ['conceptoConIva', 'boolean'],
            [['receptorNombre', 'conceptoDescripcion'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'formaPago' => 'Forma de Pago',
            'metodoPago' => 'Método de Pago',
            'receptorRfc' => 'RFC del Receptor',
            'receptorNombre' => 'Razón Social del Receptor',
            'receptorDomicilioFiscal' => 'Código Postal Receptor (Domicilio Fiscal)',
            'receptorRegimenFiscal' => 'Régimen Fiscal Receptor',
            'usoCfdi' => 'Uso de CFDI',
            'tipoRelacion' => 'Tipo de Relación',
            'cfdiRelacionadoUuid' => 'UUID del CFDI Relacionado',
            'conceptoClaveProdServ' => 'Clave Producto/Servicio',
            'conceptoClaveUnidad' => 'Clave de Unidad',
            'conceptoCantidad' => 'Cantidad',
            'conceptoDescripcion' => 'Descripción',
            'conceptoValorUnitario' => 'Valor Unitario',
            'conceptoObjetoImp' => 'Objeto de Impuesto',
            'conceptoConIva' => '¿Aplica IVA 16%?',
        ];
    }

    /**
     * Este método transforma el modelo plano del formulario
     * al array anidado que espera el SDK de MultiFacturas.
     */
    public function getDatosParaSdk()
    {
        $subtotal = round($this->conceptoCantidad * $this->conceptoValorUnitario, 2);
        $totalIva = 0;
        $impuestosConcepto = null;
        $impuestosGlobales = null;

        if ($this->conceptoConIva == 1) $totalIva = round($subtotal * 0.16, 2);

        if ($this->conceptoObjetoImp == '02') {
            $impuestosConcepto = [
                'Traslados' => [
                    [
                        'Base' => $subtotal,
                        'Impuesto' => "0" . $this->conceptoObjetoImp,
                        'TipoFactor' => 'Tasa',
                        'TasaOCuota' => $this->conceptoConIva == 1 ? '0.160000' : '0.000000',
                        'Importe' => $totalIva
                    ]
                ]
            ];

            $impuestosGlobales = [
                'TotalImpuestosTrasladados' => $totalIva,
                'translados' => [
                    [
                        'Base' => $subtotal,
                        'Impuesto' => "0" . $this->conceptoObjetoImp,
                        'TipoFactor' => 'Tasa',
                        'TasaOCuota' => $this->conceptoConIva == 1 ? '0.160000' : '0.000000',
                        'Importe' => $totalIva
                    ]
                ]
            ];
        }

        $total = $subtotal + $totalIva;

        $datos = [
            "factura" => [
                "fecha_expedicion" => date('Y-m-d\TH:i:s', time() - 120),
                "folio" => "0002",
                "forma_pago" => '15',
                "metodo_pago" => 'PUE',
                "tipocomprobante" => "E", // <--- CAMBIO IMPORTANTE: Egreso
                "LugarExpedicion" => \Yii::$app->params['emisor']['LugarExpedicion'],
                "serie" => "NC", // Nota de Crédito
                "tipocambio" => 1,
                "Exportacion" => "01", // 01 = No aplica
                "moneda" => "MXN",
                "subtotal" => $subtotal, // En Egresos, el subtotal y total se ponen en POSITIVO
                "total" => $total,
            ],

            // --- Emisor (Fijo desde params) ---
            'emisor' => [
                'rfc' => \Yii::$app->params['emisor']['rfc'],
                'nombre' => \Yii::$app->params['emisor']['nombre'],
                'RegimenFiscal' => \Yii::$app->params['emisor']['RegimenFiscal'],
            ],

            // --- Receptor (Dinámico desde form) ---
            'receptor' => [
                'rfc' => $this->receptorRfc,
                'nombre' => $this->receptorNombre,
                'DomicilioFiscalReceptor' => $this->receptorDomicilioFiscal,
                'RegimenFiscalReceptor' => $this->receptorRegimenFiscal,
                'UsoCFDI' => 'G02', // <--- CAMBIO IMPORTANTE: Fijo para Egresos
            ],

            // --- CFDI Relacionados (NUEVO Y OBLIGATORIO) ---
            'CfdisRelacionados' => [
                'TipoRelacion' => $this->tipoRelacion,
                'UUID' => [$this->cfdiRelacionadoUuid]
            ],

            // --- Conceptos (Dinámico desde form) ---
            'conceptos' => [
                [
                    'ClaveProdServ' => $this->conceptoClaveProdServ,
                    'cantidad' => $this->conceptoCantidad,
                    'ClaveUnidad' => $this->conceptoClaveUnidad,
                    'descripcion' => $this->conceptoDescripcion,
                    'valorunitario' => $this->conceptoValorUnitario,
                    'importe' => $subtotal,
                    'ObjetoImp' => $this->conceptoObjetoImp,
                ]
            ],
        ];

        if ($impuestosConcepto) {
            $datos['conceptos'][0]['Impuestos'] = $impuestosConcepto;
        }

        if ($impuestosGlobales) {
            $datos['impuestos'] = $impuestosGlobales;
        }

        return $datos;
    }
}
