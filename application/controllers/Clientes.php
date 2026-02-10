<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Clientes Controller
 * 
 * Controlador para la gestión de clientes
 * Incluye registro, listado e historial de compras
 */
class Clientes extends CI_Controller {
	
	public function __construct() {
		parent::__construct();
		
		// Verificar sesión activa
		if (!isset($this->session->datosusuario) || empty($this->session->datosusuario)) {
			redirect('login');
		}
		
		$this->load->model('Clientes_model');
		$this->load->model('Ventas_model');
	}
	
	/**
	 * Obtener el nombre del usuario actual
	 * 
	 * @return string
	 */
	private function getUsuarioActual() {
		return isset($this->session->datosusuario->nombre_completo) 
			? $this->session->datosusuario->nombre_completo 
			: 'Sistema';
	}
	
	// ==========================================
	// SECCIÓN: VISTAS
	// ==========================================
	
	/**
	 * Listado de clientes (página principal)
	 */
	public function index() {
		$this->vista('clientes/index');
	}
	
	/**
	 * Ver detalle e historial de un cliente
	 * 
	 * @param int $id
	 */
	public function ver($id = null) {
		if (!$id) {
			redirect('clientes');
		}
		
		$data['cliente'] = $this->Clientes_model->find($id);
		
		if (!$data['cliente']) {
			redirect('clientes');
		}
		
		$data['estadisticas'] = $this->Clientes_model->obtenerEstadisticas($id);
		$this->vista('clientes/ver', $data);
	}
	
	// ==========================================
	// SECCIÓN: AJAX - LISTAR
	// ==========================================
	
	/**
	 * Listar clientes para DataTable (AJAX)
	 */
	public function listar() {
		$clientes = $this->Clientes_model->listarConEstadisticas();
		
		$this->json([
			'success' => true,
			'data' => $clientes
		]);
	}
	
	/**
	 * Obtener historial de compras de un cliente (AJAX)
	 * 
	 * @param int $id
	 */
	public function historial($id = null) {
		if (!$id) {
			$this->json(['success' => false, 'message' => 'ID de cliente requerido']);
			return;
		}
		
		$compras = $this->Clientes_model->obtenerHistorialCompras($id);
		
		$this->json([
			'success' => true,
			'data' => $compras
		]);
	}
	
	/**
	 * Buscar clientes (AJAX) - para uso en ventas
	 */
	public function buscar() {
		$termino = $this->input->post('termino');
		
		if (strlen($termino) < 2) {
			$this->json(['success' => true, 'data' => []]);
			return;
		}
		
		$clientes = $this->Clientes_model->buscar($termino);
		
		$this->json([
			'success' => true,
			'data' => $clientes
		]);
	}
	
	// ==========================================
	// SECCIÓN: CRUD
	// ==========================================
	
	/**
	 * Guardar cliente (crear o actualizar) - AJAX
	 */
	public function guardar() {
		$response = ['success' => false, 'message' => 'Error al procesar la solicitud'];
		
		$id = $this->input->post('id');
		$nombre = trim($this->input->post('nombre_completo'));
		$documento = trim($this->input->post('numero_documento'));
		$correo = trim($this->input->post('correo_electronico'));
		
		// Validaciones
		if (empty($nombre)) {
			$this->json(['success' => false, 'message' => 'El nombre es requerido']);
			return;
		}
		
		if (empty($documento)) {
			$this->json(['success' => false, 'message' => 'El número de documento es requerido']);
			return;
		}
		
		// Verificar si el documento ya existe
		if ($this->Clientes_model->documentoExiste($documento, $id)) {
			$this->json(['success' => false, 'message' => 'Ya existe un cliente con este número de documento']);
			return;
		}
		
		$usuario = $this->getUsuarioActual();
		
		$data = [
			'nombre_completo' => $nombre,
			'numero_documento' => $documento,
			'correo_electronico' => $correo ?: null
		];
		
		try {
			if ($id) {
				// Actualizar
				$data['actualizado_por'] = $usuario;
				$data['fec_actualizacion'] = date('Y-m-d H:i:s');
				$result = $this->Clientes_model->update($id, $data);
				
				if ($result) {
					$response = [
						'success' => true, 
						'message' => 'Cliente actualizado correctamente',
						'id' => $id
					];
				}
			} else {
				// Crear
				$data['creado_por'] = $usuario;
				$nuevoId = $this->Clientes_model->save($data);
				
				if ($nuevoId) {
					$cliente = $this->Clientes_model->find($nuevoId);
					$response = [
						'success' => true, 
						'message' => 'Cliente registrado correctamente',
						'id' => $nuevoId,
						'cliente' => $cliente
					];
				}
			}
		} catch (Exception $e) {
			log_message('error', 'Error al guardar cliente: ' . $e->getMessage());
			$response['message'] = 'Error al guardar el cliente';
		}
		
		$this->json($response, $response['success'] ? 200 : 500);
	}
	
	/**
	 * Eliminar cliente - AJAX
	 * 
	 * @param int $id
	 */
	public function eliminar($id = null) {
		if (!$id) {
			$this->json(['success' => false, 'message' => 'ID de cliente requerido']);
			return;
		}
		
		// Verificar si tiene compras asociadas
		$stats = $this->Clientes_model->obtenerEstadisticas($id);
		if ($stats->total_compras > 0) {
			$this->json([
				'success' => false, 
				'message' => 'No se puede eliminar el cliente porque tiene compras asociadas'
			]);
			return;
		}
		
		try {
			$result = $this->db->where('id', $id)->delete('clientes');
			
			if ($result) {
				$this->json(['success' => true, 'message' => 'Cliente eliminado correctamente']);
			} else {
				$this->json(['success' => false, 'message' => 'Error al eliminar el cliente']);
			}
		} catch (Exception $e) {
			log_message('error', 'Error al eliminar cliente: ' . $e->getMessage());
			$this->json(['success' => false, 'message' => 'Error al eliminar el cliente']);
		}
	}
	
	/**
	 * Obtener datos de un cliente - AJAX
	 * 
	 * @param int $id
	 */
	public function obtener($id = null) {
		if (!$id) {
			$this->json(['success' => false, 'message' => 'ID de cliente requerido']);
			return;
		}
		
		$cliente = $this->Clientes_model->find($id);
		
		if ($cliente) {
			$this->json(['success' => true, 'data' => $cliente]);
		} else {
			$this->json(['success' => false, 'message' => 'Cliente no encontrado']);
		}
	}
	
	/**
	 * Registro rápido de cliente (desde ventas) - AJAX
	 */
	public function registroRapido() {
		$nombre = trim($this->input->post('nombre_completo'));
		$documento = trim($this->input->post('numero_documento'));
		
		// Validaciones básicas
		if (empty($nombre) || empty($documento)) {
			$this->json([
				'success' => false, 
				'message' => 'Nombre y número de documento son requeridos'
			]);
			return;
		}
		
		// Verificar si ya existe
		$clienteExistente = $this->Clientes_model->buscarPorDocumento($documento);
		if ($clienteExistente) {
			$this->json([
				'success' => true, 
				'message' => 'Cliente encontrado',
				'cliente' => $clienteExistente,
				'existente' => true
			]);
			return;
		}
		
		// Crear nuevo cliente
		$data = [
			'nombre_completo' => $nombre,
			'numero_documento' => $documento,
			'creado_por' => $this->getUsuarioActual()
		];
		
		$nuevoId = $this->Clientes_model->save($data);
		
		if ($nuevoId) {
			$cliente = $this->Clientes_model->find($nuevoId);
			$this->json([
				'success' => true, 
				'message' => 'Cliente registrado correctamente',
				'cliente' => $cliente,
				'existente' => false
			]);
		} else {
			$this->json(['success' => false, 'message' => 'Error al registrar el cliente']);
		}
	}
}
