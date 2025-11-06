<?php

namespace app\models\form;

use yii\base\Model;
use Yii;

/**
 * PagoForm es el modelo para el formulario de timbrado de Pagos (REP 2.0).
 * No usa base de datos.
 */
class PagoForm extends Model
{
    // --- DATOS DEL RECEPTOR (Quien nos paga) ---
    public $receptorRfc;
    public $receptorNombre;
    public $receptorDomicilioFiscal; // Código Postal
    public $receptorRegimenFiscal;

    // --- DATOS DEL PAGO (Lo que recibimos) ---
    public $fechaPago;
    public $formaDePagoP; // Catálogo SAT
    public $monto; // El monto total del pago recibido

    // --- DATOS DEL DOCUMENTO RELACIONADO (La factura que se está pagando) ---
    // Para este demo, asumimos que $monto se aplica 100% a este documento
    public $idDocumento; // El UUID de la factura de Ingreso
    public $serie; // Serie de la factura
    public $folio; // Folio de la factura
    public $numParcialidad;
    public $impSaldoAnt; // Saldo anterior de la factura
    public $doctoRelacionadoConIva; // bool: 0=No, 1=Sí (IVA 16% de la factura original)


    public function rules()
    {
        return [
            // Requeridos
            [[
                'receptorRfc',
                'receptorNombre',
                'receptorDomicilioFiscal',
                'receptorRegimenFiscal',
                'fechaPago',
                'formaDePagoP',
                'monto',
                'idDocumento',
                'numParcialidad',
                'impSaldoAnt',
                'doctoRelacionadoConIva',
            ], 'required'],

            // Formatos específicos
            ['receptorRfc', 'match', 'pattern' => '/^[A-Z&Ñ]{3,4}[0-9]{2}(0[1-9]|1[0-2])(0[1-9]|[12][0-9]|3[01])[A-Z0-9]{2}[0-9A]$/'],
            ['receptorDomicilioFiscal', 'match', 'pattern' => '/^[0-9]{5}$/'],
            ['idDocumento', 'match', 'pattern' => '/^[a-fA-F0-9]{8}-[a-fA-F0-9]{4}-[a-fA-F0-9]{4}-[a-fA-F0-9]{4}-[a-fA-F0-9]{12}$/', 'message' => 'El UUID no tiene un formato válido.'],
            [['monto', 'impSaldoAnt'], 'number'],
            ['numParcialidad', 'integer'],
            ['doctoRelacionadoConIva', 'boolean'],
            [['serie', 'folio'], 'string', 'max' => 25],
            ['fechaPago', 'safe'], // 'safe' para 'datetime-local'

            // Validación Lógica
            ['monto', 'compare', 'compareAttribute' => 'impSaldoAnt', 'operator' => '<=', 'message' => 'El monto a pagar no puede ser mayor que el saldo anterior.'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'receptorRfc' => 'RFC del Receptor',
            'receptorNombre' => 'Razón Social del Receptor',
            'receptorDomicilioFiscal' => 'Código Postal Receptor (Domicilio Fiscal)',
            'receptorRegimenFiscal' => 'Régimen Fiscal Receptor',
            'fechaPago' => 'Fecha y Hora del Pago',
            'formaDePagoP' => 'Forma en que se recibió el Pago',
            'monto' => 'Monto Total Recibido en este Pago',
            'idDocumento' => 'UUID de la Factura que se paga',
            'serie' => 'Serie (Factura que se paga)',
            'folio' => 'Folio (Factura que se paga)',
            'numParcialidad' => 'Número de Parcialidad',
            'impSaldoAnt' => 'Saldo Anterior de la Factura',
            'doctoRelacionadoConIva' => '¿La factura original tiene IVA 16%?',
        ];
    }

    /**
     * Este método transforma el modelo plano del formulario
     * al array anidado que espera el SDK de MultiFacturas para un REP 2.0.
     */
    public function getDatosParaSdk()
    {
        // 1. Calcular Saldo Insoluto
        // Asumimos que el $monto total se aplica a este documento
        $impPagado = $this->monto;
        $impSaldoInsoluto = round($this->impSaldoAnt - $impPagado, 2);

        // 2. Calcular Impuestos del Documento Relacionado
        // Estos son los impuestos "del pago", proporcionales a lo pagado.
        $totalesPago = ['MontoTotalPagos' => 0];
        $impuestosDR = null;
        $objetoImpDR = $this->doctoRelacionadoConIva ? '02' : '01'; // 02=Sí objeto, 01=No objeto

        if ($this->doctoRelacionadoConIva) {
            // El ImpPagado (monto) es el 116%. Calculamos base (100%) e impuesto (16%)
            // El REP 2.0 exige 6 decimales de precisión en la base.
            $baseDR = round($impPagado / 1.16, 6);
            $importeDR = round($baseDR * 0.16, 2); // El importe es a 2 decimales

            // Impuestos a Nivel de Documento Relacionado
            $impuestosDR = [
                'TrasladosDR' => [
                    [
                        'BaseDR' => $baseDR,
                        'ImpuestoDR' => '002', // IVA
                        'TipoFactorDR' => 'Tasa',
                        'TasaOCuotaDR' => '0.160000',
                        'ImporteDR' => $importeDR
                    ]
                ]
            ];

            // Impuestos a Nivel del Nodo "Pago" (totales)
            // $totalesPago = [
            //     'TotalTrasladosBaseIVA16' => $baseDR,
            //     'TotalTrasladosImpuestoIVA16' => $importeDR,
            //     'MontoTotalPagos' => $this->monto,
            // ];
            $totalesPago = [
                'MontoTotalPagos' => $this->monto,
            ];
        } else {
            // Si no hay IVA, el MontoTotalPagos es solo el monto.
            $totalesPago = [
                'MontoTotalPagos' => $this->monto,
            ];
        }

        // 3. Armar el array final
        $datos = [
            'complemento' => 'pagos20',
            // --- Comprobante (Root) ---
            "factura" => [
                "fecha_expedicion" => date('Y-m-d\TH:i:s', time() - 120),
                "tipocomprobante" => "P", // PAGO
                "LugarExpedicion" => Yii::$app->params['emisor']['LugarExpedicion'],
                "serie" => "P", // Serie para Pagos
                "folio" => "0001", // Folio para Pagos
                "moneda" => "XXX", // Valor Fijo para Pagos
                "subtotal" => 0,   // Valor Fijo para Pagos
                "total" => 0,      // Valor Fijo para Pagos
                "Exportacion" => "01", // 01 = No aplica
            ],

            // --- Emisor (Fijo desde params) ---
            'emisor' => [
                'rfc' => Yii::$app->params['emisor']['rfc'],
                'nombre' => Yii::$app->params['emisor']['nombre'],
                'RegimenFiscal' => Yii::$app->params['emisor']['RegimenFiscal'],
            ],

            // --- Receptor (Dinámico desde form) ---
            'receptor' => [
                'rfc' => $this->receptorRfc,
                'nombre' => $this->receptorNombre,
                'DomicilioFiscalReceptor' => $this->receptorDomicilioFiscal,
                'RegimenFiscalReceptor' => $this->receptorRegimenFiscal,
                'UsoCFDI' => 'CP01', // Valor Fijo para Pagos
            ],

            // --- Conceptos (Fijo para Pagos) ---
            'conceptos' => [
                [
                    'ClaveProdServ' => '84111506', // Servicios de facturación
                    'cantidad' => 1,
                    'ClaveUnidad' => 'ACT', // Actividad
                    'descripcion' => 'Pago',
                    'valorunitario' => 0,
                    'importe' => 0,
                    'ObjetoImp' => '01', // 01 = No objeto de impuesto
                ]
            ],

            // --- COMPLEMENTO DE PAGO ---
            'pagos20' => [
                'Totales' => $totalesPago,
                'Pagos' => [ // Array de Pagos (en este demo, solo 1)
                    [
                        'FechaPago' => $this->fechaPago,
                        'FormaDePagoP' => $this->formaDePagoP,
                        'MonedaP' => 'MXN', // Asumimos MXN
                        'TipoCambioP' => 1,
                        'Monto' => $this->monto,
                        'NomBancoOrdExt' => '0.0',
                        // 'NumOperacion' => '123456', // Opcional
                        // Documentos Relacionados (en este demo, solo 1)
                        'DoctoRelacionado' => [
                            [
                                'IdDocumento' => $this->idDocumento,
                                'Serie' => $this->serie,
                                'Folio' => $this->folio,
                                'MonedaDR' => 'MXN', // Asumimos MXN
                                'NumParcialidad' => $this->numParcialidad,
                                'ImpSaldoAnt' => $this->impSaldoAnt,
                                'ImpPagado' => $impPagado,
                                'ImpSaldoInsoluto' => $impSaldoInsoluto,
                                'ObjetoImpDR' => $objetoImpDR,
                                'EquivalenciaDR' => '1',
                                // Impuestos del Documento (calculados arriba)
                                'ImpuestosDR' => $impuestosDR,
                            ]
                        ]
                    ]
                ]
            ]
        ];

        // Limpiamos nodos nulos
        if ($impuestosDR === null) {
            unset($datos['pagos20']['Pagos'][0]['DoctoRelacionado'][0]['ImpuestosDR']);
        }

        return $datos;
    }
}
