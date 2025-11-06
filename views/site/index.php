<?php

/** @var yii\web\View $this */

$this->title = 'Inicio - Tutorial de Timbrado';
?>
<div class="site-index">

    <div class="p-5 mb-4 bg-light rounded-3">
        <div class="container-fluid py-5">
            <h1 class="display-4">¡Bienvenido al Tutorial de Timbrado! 🚀</h1>
            <p class="lead">
                Un proyecto demo simple y funcional para generar CFDI 4.0 con PHP, Yii2 y el SDK de MultiFacturas.
            </p>
            <hr class="my-4">
            <p>
                Navega a las secciones de <strong>Ingresos</strong>, <strong>Egresos</strong> o <strong>Pagos</strong>
                en el menú superior para comenzar a generar comprobantes en modo de prueba.
            </p>
        </div>
    </div>

    <div class="body-content">
        <div class="row">
            <div class="col-lg-8">
                <h2>¿Cuál es el objetivo de este proyecto?</h2>
                <p>
                    Este proyecto fue creado con fines didácticos. El objetivo es demostrar
                    de forma clara y directa cómo integrar el SDK de MultiFacturas en un proyecto de Yii2
                    para timbrar los principales tipos de CFDI 4.0:
                </p>
                <ul>
                    <li><strong>Facturas de Ingreso (I)</strong></li>
                    <li><strong>Notas de Crédito (Egreso - E)</strong></li>
                    <li><strong>Complementos de Pago (P)</strong></li>
                </ul>

                <h3>Filosofía del Proyecto: Simple pero Funcional</h3>
                <p>
                    Para centrarnos al 100% en el proceso de timbrado, este demo
                    <strong>NO utiliza base de datos</strong>.
                    Toda la configuración se maneja de la siguiente manera:
                </p>
                <ul>
                    <li>
                        <strong>Emisor Fijo:</strong> Los datos del Emisor (RFC, Razón Social, Régimen Fiscal, Certificados CSD)
                        se configuran directamente en el archivo <code>config/params.php</code>.
                    </li>
                    <li>
                        <strong>Receptor Dinámico:</strong> Los formularios te permitirán capturar los datos del receptor (Cliente)
                        en cada operación.
                    </li>
                    <li>
                        <strong>Productos Dinámicos:</strong> Los conceptos (productos o servicios) se capturan al momento
                        en el formulario. No hay un catálogo de productos.
                    </li>
                    <li>
                        <strong>Catálogos del SAT:</strong> Los catálogos (Uso de CFDI, Régimen Fiscal, etc.)
                        están definidos como constantes o *helpers* dentro de la aplicación para
                        poblar los menús desplegables.
                    </li>
                </ul>
            </div>

            <div class="col-lg-4">
                <h2>Requisitos de Instalación ⚙️</h2>
                <p>
                    Para poder ejecutar este proyecto en tu entorno local (ej. XAMPP),
                    necesitas asegurarte de tener lo siguiente:
                </p>
                <ol>
                    <li>
                        <strong>XAMPP</strong> (o un servidor PHP 7.4+ similar).
                    </li>
                    <li>
                        <strong>Composer</strong> (para instalar las dependencias de Yii2).
                    </li>
                    <li class="fw-bold">
                        <strong>PHP con ionCube Loader:</strong> ¡Este es el paso más importante!
                        El SDK de MultiFacturas está encriptado y PHP no puede leerlo
                        sin el <i>loader</i> de ionCube.
                    </li>
                    <li>
                        <strong>El SDK de MultiFacturas:</strong> Descargado
                        (contraseña: <code>multifacturas321</code>) y colocado en la carpeta
                        <code>lib/</code> del proyecto.
                    </li>
                    <li>
                        <strong>Configuración Local:</strong> Crear tu archivo
                        <code>config/params-local.php</code> con tus credenciales de prueba del PAC
                        y la contraseña de tus archivos <code>.cer</code> y <code>.key</code>.
                    </li>
                    <li>
                        <strong>Permisos de Escritura:</strong> La carpeta
                        <code>web/documentos/cfdis</code> debe tener permisos de escritura
                        para que el SDK pueda guardar los XML.
                    </li>
                </ol>
            </div>
        </div>
    </div>
</div>