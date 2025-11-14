<?php

namespace app\components;

use yii\base\Component;
use yii\base\Exception;
use Yii;

error_reporting(0);

require_once Yii::getAlias('@sdk_multifacturas');

class MultiFacturas extends Component
{
    private $datosBase = [];

    public function init()
    {
        parent::init();

        $this->datosBase['version_cfdi'] = '4.0';
        $this->datosBase['validacion_local'] = 'NO';

        $produccion = Yii::$app->params['pac.produccion'] ? 'SI' : 'NO';

        $this->datosBase['PAC'] = [
            'produccion' => $produccion,
            'usuario' => Yii::$app->params['pac.user'],
            'pass' => Yii::$app->params['pac.pass'],
        ];

        $this->datosBase['conf'] = [
            'cer' => Yii::getAlias('@cer'),
            'key' => Yii::getAlias('@key'),
            'pass' => Yii::$app->params['conf.pass']
        ];
    }

    /**
     * Método principal para timbrar un CFDI.
     * @param array $datosEspecificos Los datos únicos de esta factura (receptor, conceptos, etc.)
     * @param string $nombreArchivo El nombre base para los archivos (Ej: "F-123")
     * @return array La respuesta del SDK
     * @throws Exception Si el SDK no funciona o falta la función
     */
    public function timbrar(array $datosEspecificos, $nombreArchivo)
    {
        $datosCompletos = array_merge_recursive($this->datosBase, $datosEspecificos);

        $rutaGuardado = Yii::getAlias('@cfdis_path');

        if (!is_dir($rutaGuardado)) {
            if (!mkdir($rutaGuardado, 0775, true)) {
                throw new Exception("No se pudo crear el directorio: $rutaGuardado");
            }
        }

        $datosCompletos['cfdi'] = $rutaGuardado . '/' . $nombreArchivo . '.xml';
        $datosCompletos['xml_debug'] = $rutaGuardado . '/' . $nombreArchivo . '_debug.xml';

        if (!function_exists('mf_genera_cfdi4')) {
            error_reporting(E_ALL); // Restaurar reporting
            throw new Exception("La función 'fnGenerarCfdi' del SDK de MultiFacturas no existe o no se cargó.");
        }

        $respuesta = mf_genera_cfdi4($datosCompletos);

        error_reporting(E_ALL);

        if (file_exists($datosCompletos['xml_debug'])) {
            unlink($datosCompletos['xml_debug']);
        }

        if (isset($respuesta['codigo_mf_numero']) && $respuesta['codigo_mf_numero'] != '0' && !isset($respuesta['cfdi'])) {
            $message = '';
            if (isset($respuesta['mensaje_original_pac_json'])) {
                $message = json_decode($respuesta['mensaje_original_pac_json']);
                $message_info = $message->message;
            }

            if (!empty($message) && $message->messageDetail != null) $message_info = $message->messageDetail;

            return [
                'resultado' => false,
                'mensaje' => $message_info ?? $respuesta['codigo_mf_texto']
            ];
        }

        return [
            'resultado' => true,
            'mensaje' => 'Timbrado exitoso',
            'xml' => $respuesta['archivo_xml'],
            'codigo_png' => $respuesta['archivo_png'],
            'uuid' => $respuesta['uuid'],
            'cadena' => $respuesta['representacion_impresa_cadena'],
            'certificado_no' => $respuesta['representacion_impresa_certificado_no'],
            'fecha_timbrado' => htmlentities($respuesta['representacion_impresa_fecha_timbrado']),
            'sello' => htmlentities($respuesta['representacion_impresa_sello']),
            'sello_sat' => htmlentities($respuesta['representacion_impresa_selloSAT']),
            'certificado_sat' => htmlentities($respuesta['representacion_impresa_certificadoSAT'])
        ];
    }
}
