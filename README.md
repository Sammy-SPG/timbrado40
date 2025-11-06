Tutorial de Timbrado CFDI 4.0 (PHP, Yii2 y MultiFacturas)
=========================================================

> Un proyecto demo simple y funcional para generar CFDI 4.0 con PHP, Yii2 y el SDK de MultiFacturas.

Este proyecto fue creado con fines didácticos. El objetivo es demostrar de forma clara y directa cómo integrar el SDK de MultiFacturas en un proyecto de Yii2 para timbrar los principales tipos de CFDI 4.0.

🚀 ¿Qué puedes hacer con este demo?
-----------------------------------

Este proyecto te permite generar comprobantes en modo de prueba para los siguientes escenarios:

*   **Facturas de Ingreso (I):** La factura estándar de venta.
    
*   **Notas de Crédito (Egreso - E):** Para aplicar devoluciones o descuentos a una factura existente.
    
*   **Complementos de Pago (P):** Para registrar el pago (total o parcial) de facturas emitidas a crédito (PPD).
    

💡 Filosofía del Proyecto: Simple pero Funcional
------------------------------------------------

Para centrarnos al 100% en el proceso de timbrado y la integración del SDK, este demo **NO utiliza base de datos**.

Toda la configuración se maneja de la siguiente manera:

*   **Emisor Fijo:** Los datos del Emisor (RFC, Razón Social, Régimen Fiscal, Certificados CSD) se configuran directamente en el archivo config/params.php.
    
*   **Receptor Dinámico:** Los formularios te permitirán capturar los datos del receptor (Cliente) en cada operación.
    
*   **Conceptos Dinámicos:** Los conceptos (productos o servicios) se capturan al momento en el formulario. No hay un catálogo de productos.
    
*   **Catálogos del SAT:** Los catálogos (Uso de CFDI, Régimen Fiscal, etc.) están definidos como _helpers_ dentro de la aplicación (app\\helpers\\SatCatalogos) para poblar los menús desplegables.
    

🛠️ Stack Tecnológico
---------------------

*   **PHP 7.4+**
    
*   **Yii2 Framework** (yiisoft/yii2)
    
*   **Yii2 Bootstrap 5** (yiisoft/yii2-bootstrap5)
    
*   **mPDF** (mpdf/mpdf) para la generación de la representación impresa (PDF).
    
*   **MultiFacturas SDK** (requiere ionCube Loader).
    

⚙️ Requisitos de Instalación
----------------------------

Para poder ejecutar este proyecto en tu entorno local (ej. XAMPP), necesitas asegurarte de tener lo siguiente:

1.  **Servidor Web:** Un entorno como XAMPP o similar con **PHP 7.4 o superior**.
    
2.  **Composer:** Para instalar las dependencias de PHP.
    
3.  **SDK de MultiFacturas:** El SDK debe estar descargado (la contraseña habitual es multifacturas321) y colocado en la carpeta lib/ del proyecto.
    
4.  **Permisos de Escritura:** La carpeta web/documentos/cfdis debe tener permisos de escritura para que el SDK pueda guardar los XML y PDF generados.
    

### ⚠️ Requisito Crítico: ionCube Loader

¡Este es el paso más importante! El SDK de MultiFacturas está encriptado y PHP no puede leerlo sin la extensión **ionCube Loader**.

*   Debes descargar el _loader_ correcto para tu versión de PHP y sistema operativo desde [el sitio oficial de ionCube](https://www.ioncube.com/loaders.php).
    
*   Debes habilitarlo en tu archivo php.ini. (Ej. zend\_extension = "C:\\xampp\\php\\ext\\php\_ioncube\_loader\_win\_7.4.dll")
    
*   Verifica que esté activo ejecutando php -v en tu terminal.
    

🏁 Pasos de Instalación
-----------------------

1.  Bashgit clone https://github.com/TU\_USUARIO/TU\_REPOSITORIO.gitcd TU\_REPOSITORIO
    
2.  **Instala el SDK:**Descarga el SDK de MultiFacturas y descomprímelo dentro de la carpeta lib/. La estructura debería quedar así: lib/SDK\_MultiFacturas\_PHP\_7\_4/.
    
3.  Bashcomposer install
    
4.  **Configura tus credenciales:**
    
    *   Crea una copia del archivo config/params-local.example.php y renuébrala a config/params-local.php.
        
    *   Edita config/params-local.php y rellena tus credenciales de prueba de MultiFacturas (usuario, contraseña) y la contraseña de tus archivos CSD (.key).
        
5.  **Configura el Emisor:**
    
    *   Edita el archivo config/params.php.
        
    *   Rellena **todos** los datos de la sección emisor con los datos de tu empresa de prueba (RFC, Razón Social, Régimen Fiscal, CP).
        
    *   Asegúrate de colocar tus archivos .cer y .key en la carpeta web/documentos/certificados/ y que los nombres coincidan con los del archivo de configuración.
        
6.  **Configura tu Host (Opcional pero recomendado):**Apunta un virtual host de Apache (ej. http://yii-timbrado.test) a la carpeta web/ del proyecto.
    
7.  **¡Listo!**Accede a tu proyecto. Navega a las secciones de **Ingresos**, **Egresos** o **Pagos** en el menú superior para comenzar a generar comprobantes.