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
