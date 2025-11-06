<?php

/**
 * Este helper centraliza los catálogos del SAT más comunes
 * para ser usados en los formularios del demo de timbrado.
 * * Todos los métodos devuelven un array [clave => 'clave - descripcion']
 * listo para usarse en un Html::dropDownList() de Yii2.
 */

namespace app\helpers;

class SatCatalogos
{
    /**
     * Devuelve un array con un 'prompt' (ej. "Seleccione una opción")
     * listo para ser fusionado con un catálogo.
     * @param string $texto El texto a mostrar.
     * @return array
     */
    public static function getPrompt($texto = 'Seleccione una opción...')
    {
        return ['' => $texto];
    }

    /**
     * c_FormaPago
     * Catálogo de Formas de Pago.
     */
    public static function getFormaPago($c_FormaPago = null)
    {
        $CFormaPago = [
            '01' => '01 - Efectivo',
            '02' => '02 - Cheque nominativo',
            '03' => '03 - Transferencia electrónica de fondos',
            '04' => '04 - Tarjeta de crédito',
            '05' => '05 - Monedero electrónico',
            '06' => '06 - Dinero electrónico',
            '08' => '08 - Vales de despensa',
            '15' => '15 - Condonación',
            '28' => '28 - Tarjeta de débito',
            '29' => '29 - Tarjeta de servicios',
            '99' => '99 - Por definir',
        ];

        if ($c_FormaPago) {
            return $CFormaPago[$c_FormaPago] ?? '99 - Por definir';
        }

        return $CFormaPago;
    }

    /**
     * c_MetodoPago
     * Catálogo de Métodos de Pago.
     */
    public static function getMetodoPago($c_MetodoPago = null)
    {
        $CMetodoPago = [
            'PUE' => 'PUE - Pago en una sola exhibición',
            'PPD' => 'PPD - Pago en parcialidades o diferido',
        ];

        if ($c_MetodoPago) {
            return $CMetodoPago[$c_MetodoPago] ?? 'PPD - Pago en parcialidades o diferido';
        }

        return $CMetodoPago;
    }

    /**
     * c_RegimenFiscal
     * Catálogo de Regímenes Fiscales (lista simplificada).
     */
    public static function getRegimenFiscal($c_RegimenFiscal = null)
    {
        $CRegimenFiscal = [
            // --- Personas Morales ---
            '601' => '601 - General de Ley Personas Morales',
            '603' => '603 - Agrupaciones Agrícolas, Ganaderas, Silvícolas y Pesqueras',
            '625' => '625 - Régimen de las Actividades Empresariales con ingresos a través de Plataformas Tecnológicas',
            '626' => '626 - Régimen Simplificado de Confianza', // RESICO
            // --- Personas Físicas ---
            '605' => '605 - Sueldos y Salarios e Ingresos Asimilados a Salarios',
            '606' => '606 - Arrendamiento',
            '608' => '608 - Demás ingresos',
            '611' => '611 - Ingresos por Dividendos (socios y accionistas)',
            '612' => '612 - Personas Físicas con Actividades Empresariales y Profesionales',
            '614' => '614 - Ingresos por intereses',
            '615' => '615 - Régimen de los Ingresos por obtención de Premios',
            '616' => '616 - Sin obligaciones fiscales',
        ];

        if ($c_RegimenFiscal) {
            return $CRegimenFiscal[$c_RegimenFiscal] ?? '616 - Sin obligaciones fiscales';
        }

        return $CRegimenFiscal;
    }

    /**
     * c_UsoCFDI
     * Catálogo de Uso de CFDI.
     */
    public static function getUsoCfdi($c_UsoCFDI = null)
    {
        $C_UsoCFDI = [
            'G01' => 'G01 - Adquisición de mercancías',
            'G02' => 'G02 - Devoluciones, descuentos o bonificaciones',
            'G03' => 'G03 - Gastos en general',
            'I01' => 'I01 - Construcciones',
            'I02' => 'I02 - Mobiliario y equipo de oficina por inversiones',
            'I03' => 'I03 - Equipo de transporte',
            'I04' => 'I04 - Equipo de cómputo y accesorios',
            'I05' => 'I05 - Dados, troqueles, moldes, matrices y herramental',
            'I06' => 'I06 - Comunicaciones telefónicas',
            'I07' => 'I07 - Comunicaciones satelitales',
            'I08' => 'I08 - Otra maquinaria y equipo',
            'D01' => 'D01 - Honorarios médicos, dentales y gastos hospitalarios',
            'D04' => 'D04 - Donativos',
            'D05' => 'D05 - Intereses reales efectivamente pagados por créditos hipotecarios',
            'D07' => 'D07 - Primas por seguros de gastos médicos',
            'D08' => 'D08 - Gastos de transportación escolar obligatoria',
            'S01' => 'S01 - Sin efectos fiscales',
        ];

        if ($c_UsoCFDI) {
            return $C_UsoCFDI[$c_UsoCFDI] ?? 'S01 - Sin efectos fiscales';
        }

        return $C_UsoCFDI;
    }

    /**
     * c_UsoCFDI
     * El único UsoCFDI permitido para facturas de Egreso (E) (Notas de crédito).
     */
    public static function getUsoCfdiEgreso()
    {
        return [
            'G02' => 'G02 - Devoluciones, descuentos o bonificaciones',
        ];
    }

    /**
     * c_UsoCFDI
     * El único UsoCFDI permitido para Comprobantes de Pago (P).
     */
    public static function getUsoCfdiPago()
    {
        return [
            'CP01' => 'CP01 - Pagos',
        ];
    }

    /**
     * c_ClaveUnidad
     * Catálogo de Claves de Unidad (lista simplificada).
     */
    public static function getClaveUnidad()
    {
        return [
            'E48' => 'E48 - Unidad de servicio',
            'H87' => 'H87 - Pieza',
            'KGM' => 'KGM - Kilogramo',
            'MTR' => 'MTR - Metro',
            'LTR' => 'LTR - Litro',
            'XUN' => 'XUN - Unidad',
            'XNA' => 'XNA - Sin unidad (o no aplicable)',
        ];
    }

    /**
     * c_ClaveProdServ
     * Catálogo de Claves de Producto o Servicio (lista simplificada).
     */
    public static function getClaveProdServ()
    {
        return [
            '01010101' => '01010101 - No existe en el catálogo',
            '84111506' => '84111506 - Servicios de facturación',
            '81111500' => '81111500 - Servicios de desarrollo de software',
            '43231500' => '43231500 - Equipos de cómputo',
            '60101715' => '60101715 - Libros de ideas',
        ];
    }

    /**
     * c_TipoRelacion
     * Catálogo de Tipos de Relación entre CFDI.
     */
    public static function getTipoRelacion($c_TipoRelacion = null)
    {
        $CTipoRelacion = [
            '01' => '01 - Nota de crédito de los documentos relacionados',
            '02' => '02 - Nota de débito de los documentos relacionados',
            '03' => '03 - Devolución de mercancía sobre facturas o traslados previos',
            '04' => '04 - Sustitución de los CFDI previos',
            '05' => '05 - Traslados de mercancías facturados previamente',
            '06' => '06 - Factura generada por los traslados previos',
            '07' => '07 - CFDI por aplicación de anticipo',
        ];

        if ($c_TipoRelacion) {
            return $CTipoRelacion[$c_TipoRelacion] ?? '01 - Nota de crédito de los documentos relacionados';
        }

        return $CTipoRelacion;
    }
}
