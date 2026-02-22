####################################
SAHO Inventarios — Sistema de Gestión
####################################

Sistema integral de inventarios, ventas, facturación electrónica (DIAN) y
cierre de caja, desarrollado en **CodeIgniter 3** sobre **PHP 7.3+** y
**MySQL/MariaDB**. Orientado al mercado colombiano.

=========================
Requisitos del Servidor
=========================

- **PHP** >= 7.3 (recomendado 7.4+)
- **MySQL** >= 5.7 o **MariaDB** >= 10.3
- **XAMPP** 7.3.4 o un stack LAMP/WAMP equivalente
- Extensiones PHP: ``mysqli``, ``mbstring``, ``openssl``, ``gd``, ``zip``
- (Opcional) Extensión **Redis** para caché — descargar DLL desde
  https://pecl.php.net/package/redis/

============
Instalación
============

1. **Clonar o copiar** el proyecto dentro de la carpeta ``htdocs`` de XAMPP::

       git clone <url-del-repositorio> C:\xampp734\htdocs\inventarios

2. **Crear la base de datos** ``inventarios`` en MySQL/MariaDB.

3. **Importar las migraciones SQL** ubicadas en ``sql/``::

       mysql -u root -p inventarios < sql/migracion_facturacion_cierre_caja.sql

   Importar también cualquier otro script de estructura base que posea el
   proyecto (tablas ``ventas``, ``productos``, ``clientes``, ``usuarios``, etc.).

4. **Configurar credenciales** en ``application/config/constants.php``::

       // Entorno: DEVELOPMENT | PRODUCTION
       $enviroment = "DEVELOPMENT";

       define('IP_SERVER', 'http://localhost/inventarios/');
       define('HOSTNAME', 'localhost');
       define('USERNAME', 'root');
       define('PASSWORD', '');
       define('DATABASE', 'inventarios');

5. **Acceder** al sistema desde el navegador::

       http://localhost/inventarios/

======================
Estructura del Proyecto
======================

::

    inventarios/
    ├── index.php                  # Front-controller de CodeIgniter
    ├── readme.rst                 # Este archivo
    ├── sql/                       # Scripts de migración SQL
    ├── assets/                    # Recursos estáticos (CSS, JS, imágenes)
    │   ├── bootstrap/
    │   ├── css/main.css
    │   ├── datatables/
    │   ├── fontawesome/
    │   ├── jquery/
    │   ├── js/                    # main.js, notify.min.js, king-chart-stat.js
    │   └── sweetalert2/
    ├── application/
    │   ├── config/                # Configuración: rutas, BD, autoload, constantes
    │   ├── controllers/           # 7 controladores (módulos funcionales)
    │   ├── models/                # 9 modelos de datos
    │   ├── views/                 # Vistas organizadas por módulo + layouts
    │   ├── core/MY_Model.php      # Modelo base con CRUD genérico
    │   ├── helpers/               # Helpers de rutas y peticiones
    │   ├── libraries/             # PhpSpreadsheet (Excel), phpqrcode (QR)
    │   └── third_party/           # PhpSpreadsheet, phpqrcode, psr/autoloader
    └── system/                    # Núcleo de CodeIgniter 3 (no modificar)

========
Módulos
========

Dashboard / Catálogo Público (Home)
-------------------------------------
- Si el usuario tiene sesión activa: muestra un panel resumen con conteos de
  productos, clientes y ventas.
- Sin sesión: presenta un catálogo público de productos organizado por
  categorías.

Autenticación (Login)
-----------------------
- Inicio de sesión vía AJAX con validación de correo + contraseña
  cifrada con **SHA-256** y salt ``KEY_ALGO``.
- Creación de sesión nativa de CodeIgniter (almacenada en archivos).
- Cierre de sesión con destrucción completa.

Productos
-----------
- **CRUD completo** de productos con más de 40 campos: SKU, código de barras,
  precios (compra/venta), stock, dimensiones, información perecedera,
  vestimenta (tallas/género) y electrónica (voltaje).
- Listado con **DataTables** (paginación, búsqueda, ordenamiento).
- **Exportación a Excel** (``.xlsx``) mediante PhpSpreadsheet.
- Carga de tablas de configuración: categorías, tallas, unidades de medida,
  temperaturas, géneros, voltajes.

Clientes
----------
- **CRUD completo** con validación de unicidad de documento.
- Búsqueda por autocompletado (typeahead) por nombre o número de documento.
- Vista de detalle con **estadísticas de compra** e **historial de ventas**.
- Registro rápido desde la pantalla de ventas.
- Eliminación condicionada (solo si el cliente no tiene compras).

Ventas (Punto de Venta)
--------------------------
- Creación de ventas con carrito de productos (búsqueda por nombre, SKU o
  código de barras).
- Listado filtrable por rango de fechas, vendedor, cliente y folio.
- **Gestión de pagos**: registrar, eliminar y consultar resúmenes de pagos
  por venta (múltiples métodos de pago).
- **Cancelación** de ventas.
- **Estadísticas** de ventas por período.
- **Productos más vendidos** (Top N).
- **Impresión** de tickets/recibos.
- **Exportación a Excel** con filtros aplicados.

Facturación Electrónica (DIAN — Colombia)
-------------------------------------------
- Generación de **facturas formales** a partir de ventas, con snapshot de
  datos del cliente (razón social, NIT/CC, dirección, régimen).
- Consecutivo automático respetando prefijo y rango autorizado por la DIAN.
- Cálculo de subtotal, IVA, descuentos y total.
- **Configuración del emisor**: razón social, NIT, resolución DIAN, prefijo,
  rangos de numeración, actividad económica, logo.
- **Anulación** de facturas con motivo.
- **Estadísticas** de facturación por período.
- **Generación de PDF** para impresión/descarga.
- Prevención de doble facturación sobre la misma venta.

Cierre de Caja
----------------
- Cierres de caja por período: **diario, semanal, mensual y anual**.
- **Vista previa** (preview) del cierre antes de confirmar.
- Registro de: efectivo inicial, efectivo contado, observaciones.
- Cálculo automático de totales, desglose por método de pago, diferencia
  de caja y ventas anuladas.
- **Generación de PDF** del reporte de cierre.

======================
Esquema de Base de Datos
======================

Tablas principales del módulo de facturación y cierre de caja
(migración en ``sql/migracion_facturacion_cierre_caja.sql``):

+---------------------------+-------------------------------------------------------+
| Tabla                     | Descripción                                           |
+===========================+=======================================================+
| ``empresa_emisor``        | Datos del emisor: razón social, NIT, resolución DIAN, |
|                           | prefijo y rango de consecutivos.                      |
+---------------------------+-------------------------------------------------------+
| ``facturas``              | Encabezado de factura: número, snapshot del cliente,   |
|                           | totales (subtotal/IVA/descuentos), forma de pago,     |
|                           | estado (emitida / anulada).                           |
+---------------------------+-------------------------------------------------------+
| ``facturas_detalle``      | Líneas de la factura: snapshot del producto, cantidad, |
|                           | precio unitario, % IVA, subtotal por línea.           |
+---------------------------+-------------------------------------------------------+
| ``cierres_caja``          | Cierre de caja: tipo de período, totales de ventas,   |
|                           | desglose por método de pago, control de efectivo.     |
+---------------------------+-------------------------------------------------------+
| ``cierres_caja_detalle``  | Relación cierre ↔ ventas incluidas.                   |
+---------------------------+-------------------------------------------------------+

Tablas preexistentes referenciadas: ``ventas``, ``productos``, ``clientes``,
``usuarios``.

================
Arquitectura
================

::

    Navegador
        │
        ▼
    index.php (Front Controller)
        │
        ▼
    CI_Controller (7 controladores)
        ├── Verificación de sesión (todos excepto Home y Login)
        ├── Vistas: layouts/header + vista del módulo + layouts/footer
        └── Modelos (extienden MY_Model con CRUD genérico)
               └── MySQL vía CI Active Record / Query Builder

**Controladores** (``application/controllers/``):

- ``Home.php`` — Dashboard y catálogo público
- ``Login.php`` — Autenticación
- ``Productos.php`` — Gestión de productos
- ``Clientes.php`` — Gestión de clientes
- ``Ventas.php`` — Punto de venta y pagos
- ``Facturacion.php`` — Facturación electrónica DIAN
- ``Cierre_caja.php`` — Cierres de caja

**Modelos** (``application/models/``):

- ``Usuarios_model`` — Cuentas de usuario
- ``Productos_model`` — CRUD de productos, catálogo
- ``Clientes_model`` — CRUD de clientes, búsqueda, estadísticas
- ``Configuraciones_model`` — Tablas de lookup (categorías, tallas, etc.)
- ``Ventas_model`` — Ventas, estadísticas, cancelaciones
- ``Pagos_model`` — Registro y resumen de pagos
- ``Facturacion_model`` — Facturas DIAN, configuración del emisor
- ``Cierre_caja_model`` — Reportes de cierre de caja
- ``Login_model`` — Soporte de autenticación

=================================
Librerías y Dependencias de Terceros
=================================

- **CodeIgniter 3** — Framework PHP MVC
- **PhpSpreadsheet** — Generación de archivos Excel (``.xlsx``)
- **phpqrcode** — Generación de códigos QR
- **Bootstrap** — Framework CSS para la interfaz
- **jQuery** — Manipulación del DOM y peticiones AJAX
- **DataTables** — Tablas interactivas con paginación y búsqueda
- **SweetAlert2** — Diálogos y notificaciones elegantes
- **Font Awesome** — Iconografía
- **notify.min.js** — Notificaciones tipo toast

======================
Seguridad
======================

- Contraseñas cifradas con **AES-256-CTR** + **SHA-256** y clave de sal
  definida en ``KEY_ALGO``.
- Sesiones nativas de CodeIgniter con expiración configurable
  (``LASTACTIVITY``).
- Protección de rutas mediante verificación de sesión en cada controlador
  (excepto Home/Login).
- Sistema de permisos basado en roles (``lista_de_rutas_helper.php``).

======================
Configuración por Entorno
======================

En ``application/config/constants.php`` se define la variable
``$enviroment`` con dos posibles valores:

- ``DEVELOPMENT`` — Errores visibles, conexión local.
- ``PRODUCTION`` — Errores ocultos, credenciales de producción.

Cada entorno define: ``IP_SERVER``, ``HOSTNAME``, ``USERNAME``,
``PASSWORD`` y ``DATABASE``.

- `1 descargar ddl <https://pecl.php.net/package/redis>`_

ó

- `2 descargar ddl <https://windows.php.net/downloads/pecl/releases/redis/>`_

Put the dll in the correct folder
Wamp -> C:\wamp\bin\php\php-XXXX\ext
Laragon -> C:\laragon\bin\php\php-XXX\ext

Edit the php.ini file adding

extension=php_redis.dll

Restart server and check phpinfo(); . Now Redis should be there!
Download the CORRECT version the DDL from the following link
DLL
