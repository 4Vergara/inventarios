<?php
defined('BASEPATH') OR exit('No direct script access allowed');
function filtro_permisos($ruta, $permisos) {
	// TODO ======================== RUTAS ========================
	$route = [];
	// ? ======== home ========
	$route['home/index'] = ['ROLE_INICIAR_SESION'];
	$route['home/inicio'] = ['ROLE_INICIAR_SESION'];
	// TODO ======================== REPORTES ========================
	// ? ======== targetas de emergencia ========
	$route['generar/targetaemergencia'] = ['ROLE_PDF_IMPRIMIR'];
	$route['generar/fichaseguridad'] = ['ROLE_PDF_IMPRIMIR'];
	$route['generar/etiquetasgaonu'] = ['ROLE_PDF_IMPRIMIR'];
	// ? ======== reportes ========
	$route['reportes/index'] = ['ROLE_REPORTE_LISTAR'];
	$route['reportes/reportproveedores'] = ['ROLE_REPORTE_PROVEEDORES'];
	$route['reportes/reportelog'] = ['ROLE_REPORTE_LOGS'];
	// TODO ======================== GENERAL ========================
	// ? ======== perfil ========
	$route['perfil/index'] = ['ROLE_USUARIO_VER_PERFIL'];
	$route['perfil/cambiarclave'] = ['ROLE_USUARIO_CAMBIAR_CLAVE'];
	$route['perfil/actualizarperfil'] = ['ROLE_USUARIO_MODIFICAR'];

	$ruta = strtolower($ruta);
	if (!empty($route[$ruta])) {
		foreach ($route[$ruta] as $propiedad) {
			if (isset($permisos->$propiedad)) {
				return true;
			}
		}
	}
	return false;
}