<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Cierre_caja Controller
 * 
 * Controlador para la gestión de cierres de caja
 */
class Cierre_caja extends CI_Controller {
	
	public function __construct() {
		parent::__construct();
		
		// Verificar sesión activa
		if (!isset($this->session->datosusuario) || empty($this->session->datosusuario)) {
			redirect('login');
		}
		
		$this->load->model('Cierre_caja_model');
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
	 * Listado de cierres (página principal)
	 */
	public function index() {
		$data['tipos_periodo'] = Cierre_caja_model::obtenerTiposPeriodo();
		$this->vista('cierre_caja/index', $data);
	}
	
	/**
	 * Formulario para nuevo cierre de caja
	 */
	public function crear() {
		$data['tipos_periodo'] = Cierre_caja_model::obtenerTiposPeriodo();
		$this->vista('cierre_caja/crear', $data);
	}
	
	/**
	 * Ver detalle de un cierre
	 */
	public function ver($id = null) {
		if (!$id) {
			redirect('cierre_caja');
		}
		
		$data['cierre'] = $this->Cierre_caja_model->obtenerCierreCompleto($id);
		
		if (!$data['cierre']) {
			redirect('cierre_caja');
		}
		
		$this->vista('cierre_caja/ver', $data);
	}
	
	// ==========================================
	// SECCIÓN: OPERACIONES AJAX
	// ==========================================
	
	/**
	 * Listar cierres (AJAX)
	 */
	public function listar() {
		$filtros = [
			'tipo_periodo' => $this->input->post('tipo_periodo'),
			'fecha_desde' => $this->input->post('fecha_desde'),
			'fecha_hasta' => $this->input->post('fecha_hasta'),
			'codigo' => $this->input->post('codigo')
		];
		
		$cierres = $this->Cierre_caja_model->listarCierres($filtros);
		$this->jsonResponse(['success' => true, 'data' => $cierres]);
	}
	
	/**
	 * Preview del cierre (AJAX)
	 */
	public function preview() {
		$tipoPeriodo = $this->input->post('tipo_periodo');
		$fechaReferencia = $this->input->post('fecha_referencia');
		
		if (empty($tipoPeriodo)) {
			$this->jsonResponse(['success' => false, 'message' => 'Tipo de período requerido'], 400);
			return;
		}
		
		$preview = $this->Cierre_caja_model->obtenerPreviewCierre($tipoPeriodo, $fechaReferencia);
		$this->jsonResponse(['success' => true, 'data' => $preview]);
	}
	
	/**
	 * Realizar cierre de caja (AJAX)
	 */
	public function guardar() {
		$response = ['success' => false, 'message' => 'Error al procesar la solicitud'];
		
		try {
			$tipoPeriodo = $this->input->post('tipo_periodo');
			$fechaReferencia = $this->input->post('fecha_referencia');
			
			if (empty($tipoPeriodo)) {
				$response['message'] = 'Tipo de período requerido';
				$this->jsonResponse($response, 400);
				return;
			}
			
			$datosExtra = [
				'efectivo_inicial' => $this->input->post('efectivo_inicial') ?: 0,
				'efectivo_contado' => $this->input->post('efectivo_contado'),
				'observaciones' => $this->input->post('observaciones')
			];
			
			$resultado = $this->Cierre_caja_model->realizarCierre(
				$tipoPeriodo, 
				$datosExtra, 
				$this->getUsuarioActual(),
				$fechaReferencia
			);
			
			$statusCode = $resultado['success'] ? 200 : 400;
			$this->jsonResponse($resultado, $statusCode);
			
		} catch (Exception $e) {
			log_message('error', 'Error en Cierre_caja::guardar - ' . $e->getMessage());
			$response['message'] = 'Error interno del servidor';
			$this->jsonResponse($response, 500);
		}
	}
	
	// ==========================================
	// SECCIÓN: PDF
	// ==========================================
	
	/**
	 * Generar PDF del reporte de cierre
	 */
	public function pdf($id = null) {
		if (!$id) {
			redirect('cierre_caja');
		}
		
		$data['cierre'] = $this->Cierre_caja_model->obtenerCierreCompleto($id);
		
		if (!$data['cierre']) {
			redirect('cierre_caja');
		}
		
		$this->load->view('cierre_caja/pdf', $data);
	}
}
