<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Ventas Controller
 * 
 * Controlador para la gestión completa de ventas/pedidos
 * Incluye CRUD de ventas y gestión de pagos
 */
class Ventas extends CI_Controller {
	
	public function __construct() {
		parent::__construct();
		
		// Verificar sesión activa
		if (!isset($this->session->datosusuario) || empty($this->session->datosusuario)) {
			redirect('login');
		}
		
		$this->load->model('Ventas_model');
		$this->load->model('Pagos_model');
		$this->load->model('Productos_model');
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
	
	/**
	 * Respuesta JSON estandarizada
	 * 
	 * @param array $data
	 * @param int $statusCode
	 */
	private function jsonResponse($data, $statusCode = 200) {
		$this->output
			->set_status_header($statusCode)
			->set_content_type('application/json')
			->set_output(json_encode($data));
	}
	
	// ==========================================
	// SECCIÓN: VENTAS
	// ==========================================
	
	/**
	 * Listado de ventas (página principal)
	 */
	public function index() {
		$this->vista('ventas/index');
	}
	
	/**
	 * Formulario para crear nueva venta
	 */
	public function crear() {
		$data['clientes'] = $this->Ventas_model->obtenerClientes();
		$data['vendedores'] = $this->Ventas_model->obtenerVendedores();
		$data['metodos_pago'] = Pagos_model::obtenerMetodosPago();
		$this->vista('ventas/crear', $data);
	}
	
	/**
	 * Ver detalle de una venta
	 * 
	 * @param int $id
	 */
	public function ver($id = null) {
		if (!$id) {
			redirect('ventas');
		}
		
		$data['venta'] = $this->Ventas_model->obtenerVentaCompleta($id);
		
		if (!$data['venta']) {
			redirect('ventas');
		}
		
		$data['metodos_pago'] = Pagos_model::obtenerMetodosPago();
		$this->vista('ventas/ver', $data);
	}
	
	/**
	 * Listar ventas para DataTable (AJAX)
	 */
	public function listar() {
		$filtros = [
			'fecha_desde' => $this->input->post('fecha_desde'),
			'fecha_hasta' => $this->input->post('fecha_hasta'),
			'id_vendedor' => $this->input->post('id_vendedor'),
			'id_cliente' => $this->input->post('id_cliente'),
			'folio' => $this->input->post('folio')
		];
		
		$ventas = $this->Ventas_model->listarVentas($filtros);
		
		$this->jsonResponse([
			'success' => true,
			'data' => $ventas
		]);
	}
	
	/**
	 * Guardar nueva venta (AJAX)
	 */
	public function guardar() {
		$response = ['success' => false, 'message' => 'Error al procesar la solicitud'];
		
		try {
			$ventaData = [
				'id_cliente' => $this->input->post('id_cliente'),
				'id_vendedor' => $this->input->post('id_vendedor')
			];
			
			// Validar datos requeridos
			if (empty($ventaData['id_cliente']) || empty($ventaData['id_vendedor'])) {
				$response['message'] = 'Cliente y vendedor son requeridos';
				$this->jsonResponse($response, 400);
				return;
			}
			
			// Obtener productos del carrito
			$productosJson = $this->input->post('productos');
			$productos = json_decode($productosJson, true);
			
			if (empty($productos)) {
				$response['message'] = 'Debe agregar al menos un producto';
				$this->jsonResponse($response, 400);
				return;
			}
			
			// Preparar detalles
			$detalles = [];
			foreach ($productos as $prod) {
				$detalles[] = [
					'id_producto' => $prod['id'],
					'cantidad' => $prod['cantidad'],
					'precio_unitario' => $prod['precio'],
					'descuento' => $prod['descuento'] ?? 0
				];
			}
			
			// Crear la venta
			$resultado = $this->Ventas_model->crearVenta(
				$ventaData, 
				$detalles, 
				$this->getUsuarioActual()
			);
			
			$statusCode = $resultado['success'] ? 200 : 400;
			$this->jsonResponse($resultado, $statusCode);
			
		} catch (Exception $e) {
			log_message('error', 'Error en Ventas::guardar - ' . $e->getMessage());
			$response['message'] = 'Error interno del servidor';
			$this->jsonResponse($response, 500);
		}
	}
	
	/**
	 * Cancelar una venta (AJAX)
	 */
	public function cancelar() {
		$id = $this->input->post('id');
		
		if (!$id) {
			$this->jsonResponse(['success' => false, 'message' => 'ID de venta requerido'], 400);
			return;
		}
		
		$resultado = $this->Ventas_model->cancelarVenta($id, $this->getUsuarioActual());
		
		$statusCode = $resultado['success'] ? 200 : 400;
		$this->jsonResponse($resultado, $statusCode);
	}
	
	/**
	 * Buscar productos para el formulario de venta (AJAX)
	 */
	public function buscarProductos() {
		$termino = $this->input->post('termino');
		
		if (strlen($termino) < 2) {
			$this->jsonResponse(['success' => true, 'data' => []]);
			return;
		}
		
		$productos = $this->db->select('id, sku, nombre, marca, precio_venta, stock_actual, imagen_principal_url, porcentaje_impuesto')
			->from('productos')
			->group_start()
				->like('nombre', $termino)
				->or_like('sku', $termino)
				->or_like('codigo_barras', $termino)
			->group_end()
			->where('estado', 'activo')
			->where('stock_actual >', 0)
			->limit(15)
			->get()
			->result();
		
		$this->jsonResponse(['success' => true, 'data' => $productos]);
	}
	
	/**
	 * Obtener producto por ID o código de barras (AJAX)
	 */
	public function obtenerProducto() {
		$codigo = $this->input->post('codigo');
		
		$producto = $this->db->select('id, sku, nombre, marca, precio_venta, stock_actual, imagen_principal_url, porcentaje_impuesto')
			->from('productos')
			->group_start()
				->where('id', $codigo)
				->or_where('sku', $codigo)
				->or_where('codigo_barras', $codigo)
			->group_end()
			->where('estado', 'activo')
			->get()
			->row();
		
		if ($producto) {
			$this->jsonResponse(['success' => true, 'data' => $producto]);
		} else {
			$this->jsonResponse(['success' => false, 'message' => 'Producto no encontrado'], 404);
		}
	}
	
	/**
	 * Obtener estadísticas de ventas (AJAX)
	 */
	public function estadisticas() {
		$periodo = $this->input->get('periodo') ?? 'mes';
		$stats = $this->Ventas_model->obtenerEstadisticas($periodo);
		
		$this->jsonResponse(['success' => true, 'data' => $stats]);
	}
	
	/**
	 * Obtener clientes para select (AJAX)
	 */
	public function obtenerClientes() {
		$clientes = $this->Ventas_model->obtenerClientes();
		$this->jsonResponse(['success' => true, 'data' => $clientes]);
	}
	
	// ==========================================
	// SECCIÓN: PAGOS
	// ==========================================
	
	/**
	 * Registrar nuevo pago (AJAX)
	 */
	public function registrarPago() {
		$response = ['success' => false, 'message' => 'Error al procesar el pago'];
		
		try {
			$pagoData = [
				'id_venta' => $this->input->post('id_venta'),
				'monto' => $this->input->post('monto'),
				'metodo_pago' => $this->input->post('metodo_pago'),
				'referencia' => $this->input->post('referencia'),
				'fecha_pago' => $this->input->post('fecha_pago') ?: date('Y-m-d H:i:s')
			];
			
			// Validaciones
			if (empty($pagoData['id_venta'])) {
				$response['message'] = 'ID de venta requerido';
				$this->jsonResponse($response, 400);
				return;
			}
			
			if (empty($pagoData['monto']) || $pagoData['monto'] <= 0) {
				$response['message'] = 'El monto debe ser mayor a cero';
				$this->jsonResponse($response, 400);
				return;
			}
			
			if (empty($pagoData['metodo_pago'])) {
				$response['message'] = 'Método de pago requerido';
				$this->jsonResponse($response, 400);
				return;
			}
			
			$resultado = $this->Pagos_model->registrarPago($pagoData, $this->getUsuarioActual());
			
			$statusCode = $resultado['success'] ? 200 : 400;
			$this->jsonResponse($resultado, $statusCode);
			
		} catch (Exception $e) {
			log_message('error', 'Error en Ventas::registrarPago - ' . $e->getMessage());
			$response['message'] = 'Error interno del servidor';
			$this->jsonResponse($response, 500);
		}
	}
	
	/**
	 * Eliminar pago (AJAX)
	 */
	public function eliminarPago() {
		$idPago = $this->input->post('id');
		
		if (!$idPago) {
			$this->jsonResponse(['success' => false, 'message' => 'ID de pago requerido'], 400);
			return;
		}
		
		$resultado = $this->Pagos_model->eliminarPago($idPago, $this->getUsuarioActual());
		
		$statusCode = $resultado['success'] ? 200 : 400;
		$this->jsonResponse($resultado, $statusCode);
	}
	
	/**
	 * Obtener resumen de pagos de una venta (AJAX)
	 */
	public function resumenPagos($idVenta = null) {
		$idVenta = $idVenta ?: $this->input->post('id_venta');
		
		if (!$idVenta) {
			$this->jsonResponse(['success' => false, 'message' => 'ID de venta requerido'], 400);
			return;
		}
		
		$resumen = $this->Pagos_model->obtenerResumenPagos($idVenta);
		
		if ($resumen) {
			$this->jsonResponse(['success' => true, 'data' => $resumen]);
		} else {
			$this->jsonResponse(['success' => false, 'message' => 'Venta no encontrada'], 404);
		}
	}
	
	/**
	 * Listar pagos (AJAX)
	 */
	public function listarPagos() {
		$filtros = [
			'fecha_desde' => $this->input->post('fecha_desde'),
			'fecha_hasta' => $this->input->post('fecha_hasta'),
			'metodo_pago' => $this->input->post('metodo_pago'),
			'id_venta' => $this->input->post('id_venta')
		];
		
		$pagos = $this->Pagos_model->listarPagos($filtros);
		
		$this->jsonResponse(['success' => true, 'data' => $pagos]);
	}
	
	// ==========================================
	// SECCIÓN: REPORTES Y UTILIDADES
	// ==========================================
	
	/**
	 * Productos más vendidos (AJAX)
	 */
	public function productosMasVendidos() {
		$limite = $this->input->get('limite') ?? 10;
		$productos = $this->Ventas_model->productosMasVendidos($limite);
		
		$this->jsonResponse(['success' => true, 'data' => $productos]);
	}
	
	/**
	 * Imprimir ticket/factura de venta
	 * 
	 * @param int $id
	 */
	public function imprimir($id = null) {
		if (!$id) {
			redirect('ventas');
		}
		
		$data['venta'] = $this->Ventas_model->obtenerVentaCompleta($id);
		
		if (!$data['venta']) {
			redirect('ventas');
		}
		
		$this->load->view('ventas/imprimir', $data);
	}
	
	/**
	 * Exportar ventas a Excel
	 */
	public function exportar() {
		$this->load->library('Excelphp');
		
		$filtros = [
			'fecha_desde' => $this->input->get('fecha_desde'),
			'fecha_hasta' => $this->input->get('fecha_hasta')
		];
		
		$ventas = $this->Ventas_model->listarVentas($filtros);
		
		// Crear archivo Excel
		$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		
		// Encabezados
		$headers = ['Folio', 'Fecha', 'Cliente', 'Vendedor', 'Subtotal', 'Impuestos', 'Descuentos', 'Total', 'Pagado', 'Saldo'];
		$col = 'A';
		foreach ($headers as $header) {
			$sheet->setCellValue($col . '1', $header);
			$sheet->getStyle($col . '1')->getFont()->setBold(true);
			$col++;
		}
		
		// Datos
		$row = 2;
		foreach ($ventas as $venta) {
			$saldo = $venta->total_final - ($venta->total_pagado ?? 0);
			
			$sheet->setCellValue('A' . $row, $venta->folio_factura);
			$sheet->setCellValue('B' . $row, $venta->fecha_venta);
			$sheet->setCellValue('C' . $row, $venta->cliente_nombre);
			$sheet->setCellValue('D' . $row, $venta->vendedor_nombre);
			$sheet->setCellValue('E' . $row, $venta->subtotal);
			$sheet->setCellValue('F' . $row, $venta->total_impuestos);
			$sheet->setCellValue('G' . $row, $venta->total_descuentos);
			$sheet->setCellValue('H' . $row, $venta->total_final);
			$sheet->setCellValue('I' . $row, $venta->total_pagado ?? 0);
			$sheet->setCellValue('J' . $row, $saldo);
			$row++;
		}
		
		// Descargar
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="ventas_' . date('Y-m-d') . '.xlsx"');
		header('Cache-Control: max-age=0');
		
		$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
		$writer->save('php://output');
		exit;
	}
}
