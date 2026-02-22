<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route['default_controller'] = 'home';
$route['404_override'] = 'home';
$route['translate_uri_dashes'] = FALSE;

/* Facturación */
$route['facturacion'] = 'facturacion/index';
$route['facturacion/crear/(:num)'] = 'facturacion/crear/$1';
$route['facturacion/ver/(:num)'] = 'facturacion/ver/$1';
$route['facturacion/pdf/(:num)'] = 'facturacion/pdf/$1';
$route['facturacion/configuracion'] = 'facturacion/configuracion';

/* Cierre de Caja */
$route['cierre_caja'] = 'cierre_caja/index';
$route['cierre_caja/crear'] = 'cierre_caja/crear';
$route['cierre_caja/ver/(:num)'] = 'cierre_caja/ver/$1';
$route['cierre_caja/pdf/(:num)'] = 'cierre_caja/pdf/$1';

/* Reportes */
$route['reportes'] = 'reportes/index';
$route['reportes/ventas'] = 'reportes/ventas';
$route['reportes/productos'] = 'reportes/productos';
$route['reportes/clientes'] = 'reportes/clientes';
$route['reportes/financiero'] = 'reportes/financiero';
$route['reportes/pdfVentas'] = 'reportes/pdfVentas';
$route['reportes/pdfProductos'] = 'reportes/pdfProductos';
$route['reportes/pdfClientes'] = 'reportes/pdfClientes';
$route['reportes/pdfFinanciero'] = 'reportes/pdfFinanciero';
$route['reportes/kpis'] = 'reportes/kpis';
$route['reportes/comparativo'] = 'reportes/comparativo';
$route['reportes/ventasPorPeriodo'] = 'reportes/ventasPorPeriodo';
$route['reportes/ventasPorMetodoPago'] = 'reportes/ventasPorMetodoPago';
$route['reportes/ventasPorVendedor'] = 'reportes/ventasPorVendedor';
$route['reportes/facturadoVsNoFacturado'] = 'reportes/facturadoVsNoFacturado';
$route['reportes/productosMasVendidos'] = 'reportes/productosMasVendidos';
$route['reportes/productosMenosVendidos'] = 'reportes/productosMenosVendidos';
$route['reportes/productosStockBajo'] = 'reportes/productosStockBajo';
$route['reportes/productosPorVencer'] = 'reportes/productosPorVencer';
$route['reportes/rotacionInventario'] = 'reportes/rotacionInventario';
$route['reportes/clientesMasCompras'] = 'reportes/clientesMasCompras';
$route['reportes/clientesMayorFacturacion'] = 'reportes/clientesMayorFacturacion';
$route['reportes/clientesInactivos'] = 'reportes/clientesInactivos';
$route['reportes/historialCliente/(:num)'] = 'reportes/historialCliente/$1';
$route['reportes/resumenFinanciero'] = 'reportes/resumenFinanciero';
$route['reportes/cuentasPorCobrar'] = 'reportes/cuentasPorCobrar';
$route['reportes/flujoCaja'] = 'reportes/flujoCaja';
$route['reportes/ingresosPorDia'] = 'reportes/ingresosPorDia';
