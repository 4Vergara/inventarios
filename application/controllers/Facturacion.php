<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Facturacion Controller
 * 
 * Controlador para la gestión de facturación
 * Cumple con requisitos DIAN Colombia
 */
class Facturacion extends CI_Controller {
	
	public function __construct() {
		parent::__construct();
		
		// Verificar sesión activa
		if (!isset($this->session->datosusuario) || empty($this->session->datosusuario)) {
			redirect('login');
		}
		
		$this->load->model('Facturacion_model');
		$this->load->model('Ventas_model');
	}
	
	/**
	 * Obtener el nombre del usuario actual
	 */
	private function getUsuarioActual() {
		return isset($this->session->datosusuario->nombre_completo) 
			? $this->session->datosusuario->nombre_completo 
			: 'Sistema';
	}
	
	/**
	 * Respuesta JSON estandarizada
	 */
	private function jsonResponse($data, $statusCode = 200) {
		$this->output
			->set_status_header($statusCode)
			->set_content_type('application/json')
			->set_output(json_encode($data));
	}
	
	// ==========================================
	// SECCIÓN: VISTAS
	// ==========================================
	
	/**
	 * Listado de facturas (página principal)
	 */
	public function index() {
		$this->vista('facturacion/index');
	}
	
	/**
	 * Formulario para generar nueva factura
	 */
	public function crear($idVenta = null) {
		$data = [];
		$data['emisor'] = $this->Facturacion_model->obtenerEmisor();
		
		if ($idVenta) {
			$data['venta'] = $this->Ventas_model->obtenerVentaCompleta($idVenta);
			if (!$data['venta']) {
				redirect('facturacion');
			}
			// Verificar si ya tiene factura
			$facturaExistente = $this->Facturacion_model->obtenerFacturaPorVenta($idVenta);
			if ($facturaExistente) {
				redirect('facturacion/ver/' . $facturaExistente->id);
			}
		}
		
		$data['ventas_sin_factura'] = $this->Facturacion_model->obtenerVentasSinFactura();
		$this->vista('facturacion/crear', $data);
	}
	
	/**
	 * Ver detalle de factura
	 */
	public function ver($id = null) {
		if (!$id) {
			redirect('facturacion');
		}
		
		$data['factura'] = $this->Facturacion_model->obtenerFacturaCompleta($id);
		
		if (!$data['factura']) {
			redirect('facturacion');
		}
		
		$this->vista('facturacion/ver', $data);
	}
	
	/**
	 * Configuración del emisor
	 */
	public function configuracion() {
		$data['emisor'] = $this->Facturacion_model->obtenerEmisor();
		$this->vista('facturacion/configuracion', $data);
	}
	
	// ==========================================
	// SECCIÓN: OPERACIONES AJAX
	// ==========================================
	
	/**
	 * Listar facturas (AJAX)
	 */
	public function listar() {
		$filtros = [
			'fecha_desde' => $this->input->post('fecha_desde'),
			'fecha_hasta' => $this->input->post('fecha_hasta'),
			'estado' => $this->input->post('estado'),
			'numero_factura' => $this->input->post('numero_factura'),
			'cliente' => $this->input->post('cliente')
		];
		
		$facturas = $this->Facturacion_model->listarFacturas($filtros);
		$this->jsonResponse(['success' => true, 'data' => $facturas]);
	}
	
	/**
	 * Guardar nueva factura (AJAX)
	 */
	public function guardar() {
		$response = ['success' => false, 'message' => 'Error al procesar la solicitud'];
		
		try {
			$idVenta = $this->input->post('id_venta');
			
			if (empty($idVenta)) {
				$response['message'] = 'Debe seleccionar una venta';
				$this->jsonResponse($response, 400);
				return;
			}
			
			$datosExtra = [
				'cliente_razon_social' => $this->input->post('cliente_razon_social'),
				'cliente_nit_cc' => $this->input->post('cliente_nit_cc'),
				'cliente_direccion' => $this->input->post('cliente_direccion'),
				'cliente_correo' => $this->input->post('cliente_correo'),
				'cliente_telefono' => $this->input->post('cliente_telefono'),
				'fecha_vencimiento' => $this->input->post('fecha_vencimiento'),
				'observaciones' => $this->input->post('observaciones')
			];
			
			// Validar datos requeridos del cliente
			if (empty($datosExtra['cliente_razon_social'])) {
				$response['message'] = 'La razón social del cliente es requerida';
				$this->jsonResponse($response, 400);
				return;
			}
			
			if (empty($datosExtra['cliente_nit_cc'])) {
				$response['message'] = 'El NIT/CC del cliente es requerido';
				$this->jsonResponse($response, 400);
				return;
			}
			
			$resultado = $this->Facturacion_model->generarFactura(
				$idVenta, 
				$datosExtra, 
				$this->getUsuarioActual()
			);
			
			$statusCode = $resultado['success'] ? 200 : 400;
			$this->jsonResponse($resultado, $statusCode);
			
		} catch (Exception $e) {
			log_message('error', 'Error en Facturacion::guardar - ' . $e->getMessage());
			$response['message'] = 'Error interno del servidor';
			$this->jsonResponse($response, 500);
		}
	}
	
	/**
	 * Anular factura (AJAX)
	 */
	public function anular() {
		$id = $this->input->post('id');
		$motivo = $this->input->post('motivo');
		
		if (!$id) {
			$this->jsonResponse(['success' => false, 'message' => 'ID de factura requerido'], 400);
			return;
		}
		
		$resultado = $this->Facturacion_model->anularFactura($id, $motivo, $this->getUsuarioActual());
		$statusCode = $resultado['success'] ? 200 : 400;
		$this->jsonResponse($resultado, $statusCode);
	}
	
	/**
	 * Obtener datos de venta para facturar (AJAX)
	 */
	public function obtenerVenta() {
		$idVenta = $this->input->post('id_venta');
		
		if (!$idVenta) {
			$this->jsonResponse(['success' => false, 'message' => 'ID de venta requerido'], 400);
			return;
		}
		
		$venta = $this->Ventas_model->obtenerVentaCompleta($idVenta);
		
		if (!$venta) {
			$this->jsonResponse(['success' => false, 'message' => 'Venta no encontrada'], 404);
			return;
		}
		
		$this->jsonResponse(['success' => true, 'data' => $venta]);
	}
	
	/**
	 * Estadísticas de facturación (AJAX)
	 */
	public function estadisticas() {
		$periodo = $this->input->get('periodo') ?? 'mes';
		$stats = $this->Facturacion_model->obtenerEstadisticas($periodo);
		$this->jsonResponse(['success' => true, 'data' => $stats]);
	}
	
	/**
	 * Guardar configuración del emisor (AJAX)
	 */
	public function guardarConfiguracion() {
		$data = [
			'razon_social' => $this->input->post('razon_social'),
			'nit' => $this->input->post('nit'),
			'direccion' => $this->input->post('direccion'),
			'ciudad' => $this->input->post('ciudad'),
			'departamento' => $this->input->post('departamento'),
			'telefono' => $this->input->post('telefono'),
			'correo' => $this->input->post('correo'),
			'regimen' => $this->input->post('regimen'),
			'resolucion_dian' => $this->input->post('resolucion_dian'),
			'fecha_resolucion' => $this->input->post('fecha_resolucion'),
			'prefijo_factura' => $this->input->post('prefijo_factura'),
			'rango_desde' => $this->input->post('rango_desde'),
			'rango_hasta' => $this->input->post('rango_hasta'),
			'actividad_economica' => $this->input->post('actividad_economica')
		];
		
		// Validar datos requeridos
		if (empty($data['razon_social']) || empty($data['nit'])) {
			$this->jsonResponse(['success' => false, 'message' => 'Razón social y NIT son requeridos'], 400);
			return;
		}
		
		$resultado = $this->Facturacion_model->actualizarEmisor($data, $this->getUsuarioActual());
		
		if ($resultado) {
			$this->jsonResponse(['success' => true, 'message' => 'Configuración actualizada correctamente']);
		} else {
			$this->jsonResponse(['success' => false, 'message' => 'Error al actualizar'], 500);
		}
	}
	
	// ==========================================
	// SECCIÓN: PDF
	// ==========================================
	
	/**
	 * Generar PDF de factura formal
	 */
	public function pdf($id = null) {
		if (!$id) {
			redirect('facturacion');
		}
		
		$data['factura'] = $this->Facturacion_model->obtenerFacturaCompleta($id);
		
		if (!$data['factura']) {
			redirect('facturacion');
		}
		
		// Vista HTML que se imprime/descarga como PDF desde el navegador
		$this->load->view('facturacion/pdf', $data);
	}
}
