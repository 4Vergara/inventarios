<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Reportes Controller
 * 
 * Controlador para el módulo de Reportes y Estadísticas
 * Integra reportes de Ventas, Productos, Clientes y Financiero
 */
class Reportes extends CI_Controller {
	
	public function __construct() {
		parent::__construct();
		
		// Verificar sesión activa
		if (!isset($this->session->datosusuario) || empty($this->session->datosusuario)) {
			redirect('login');
		}
		
		$this->load->model('Reportes_model');
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
	// SECCIÓN: VISTAS PRINCIPALES
	// ==========================================
	
	/**
	 * Dashboard principal de reportes
	 */
	public function index() {
		$this->vista('reportes/index');
	}
	
	/**
	 * Vista de reportes de ventas
	 */
	public function ventas() {
		$this->load->model('Ventas_model');
		$data['vendedores'] = $this->Ventas_model->obtenerVendedores();
		$this->vista('reportes/ventas', $data);
	}
	
	/**
	 * Vista de reportes de productos
	 */
	public function productos() {
		$this->vista('reportes/productos');
	}
	
	/**
	 * Vista de reportes de clientes
	 */
	public function clientes() {
		$this->load->model('Clientes_model');
		$data['clientes'] = $this->Clientes_model->findAll();
		$this->vista('reportes/clientes', $data);
	}
	
	/**
	 * Vista de reportes financieros
	 */
	public function financiero() {
		$this->vista('reportes/financiero');
	}
	
	// ==========================================
	// SECCIÓN: ENDPOINTS AJAX - DASHBOARD KPIs
	// ==========================================
	
	/**
	 * Obtener KPIs generales (AJAX)
	 */
	public function kpis() {
		$fechaDesde = $this->input->get('fecha_desde');
		$fechaHasta = $this->input->get('fecha_hasta');
		
		$kpis = $this->Reportes_model->obtenerKPIs($fechaDesde, $fechaHasta);
		$this->jsonResponse(['success' => true, 'data' => $kpis]);
	}
	
	/**
	 * Comparativo entre períodos (AJAX)
	 */
	public function comparativo() {
		$fechaDesde1 = $this->input->get('fecha_desde_1') ?: date('Y-m-01');
		$fechaHasta1 = $this->input->get('fecha_hasta_1') ?: date('Y-m-d');
		$fechaDesde2 = $this->input->get('fecha_desde_2') ?: date('Y-m-01', strtotime('-1 month'));
		$fechaHasta2 = $this->input->get('fecha_hasta_2') ?: date('Y-m-t', strtotime('-1 month'));
		
		$data = $this->Reportes_model->comparativoPeriodos($fechaDesde1, $fechaHasta1, $fechaDesde2, $fechaHasta2);
		$this->jsonResponse(['success' => true, 'data' => $data]);
	}
	
	// ==========================================
	// SECCIÓN: ENDPOINTS AJAX - VENTAS
	// ==========================================
	
	/**
	 * Ventas por período (AJAX)
	 */
	public function ventasPorPeriodo() {
		$agrupacion = $this->input->get('agrupacion') ?: 'mes';
		$fechaDesde = $this->input->get('fecha_desde');
		$fechaHasta = $this->input->get('fecha_hasta');
		
		$data = $this->Reportes_model->ventasPorPeriodo($agrupacion, $fechaDesde, $fechaHasta);
		$this->jsonResponse(['success' => true, 'data' => $data]);
	}
	
	/**
	 * Ventas por método de pago (AJAX)
	 */
	public function ventasPorMetodoPago() {
		$fechaDesde = $this->input->get('fecha_desde');
		$fechaHasta = $this->input->get('fecha_hasta');
		
		$data = $this->Reportes_model->ventasPorMetodoPago($fechaDesde, $fechaHasta);
		$this->jsonResponse(['success' => true, 'data' => $data]);
	}
	
	/**
	 * Ventas por vendedor (AJAX)
	 */
	public function ventasPorVendedor() {
		$fechaDesde = $this->input->get('fecha_desde');
		$fechaHasta = $this->input->get('fecha_hasta');
		
		$data = $this->Reportes_model->ventasPorVendedor($fechaDesde, $fechaHasta);
		$this->jsonResponse(['success' => true, 'data' => $data]);
	}
	
	/**
	 * Facturado vs No Facturado (AJAX)
	 */
	public function facturadoVsNoFacturado() {
		$fechaDesde = $this->input->get('fecha_desde');
		$fechaHasta = $this->input->get('fecha_hasta');
		
		$data = $this->Reportes_model->facturadoVsNoFacturado($fechaDesde, $fechaHasta);
		$this->jsonResponse(['success' => true, 'data' => $data]);
	}
	
	// ==========================================
	// SECCIÓN: ENDPOINTS AJAX - PRODUCTOS
	// ==========================================
	
	/**
	 * Productos más vendidos (AJAX)
	 */
	public function productosMasVendidos() {
		$limite = $this->input->get('limite') ?: 10;
		$fechaDesde = $this->input->get('fecha_desde');
		$fechaHasta = $this->input->get('fecha_hasta');
		
		$data = $this->Reportes_model->productosMasVendidos($limite, $fechaDesde, $fechaHasta);
		$this->jsonResponse(['success' => true, 'data' => $data]);
	}
	
	/**
	 * Productos menos vendidos (AJAX)
	 */
	public function productosMenosVendidos() {
		$limite = $this->input->get('limite') ?: 10;
		$fechaDesde = $this->input->get('fecha_desde');
		$fechaHasta = $this->input->get('fecha_hasta');
		
		$data = $this->Reportes_model->productosMenosVendidos($limite, $fechaDesde, $fechaHasta);
		$this->jsonResponse(['success' => true, 'data' => $data]);
	}
	
	/**
	 * Productos con stock bajo (AJAX)
	 */
	public function productosStockBajo() {
		$data = $this->Reportes_model->productosStockBajo();
		$this->jsonResponse(['success' => true, 'data' => $data]);
	}
	
	/**
	 * Productos por vencer (AJAX)
	 */
	public function productosPorVencer() {
		$dias = $this->input->get('dias') ?: 30;
		$data = $this->Reportes_model->productosPorVencer($dias);
		$this->jsonResponse(['success' => true, 'data' => $data]);
	}
	
	/**
	 * Rotación de inventario (AJAX)
	 */
	public function rotacionInventario() {
		$fechaDesde = $this->input->get('fecha_desde');
		$fechaHasta = $this->input->get('fecha_hasta');
		
		$data = $this->Reportes_model->rotacionInventario($fechaDesde, $fechaHasta);
		$this->jsonResponse(['success' => true, 'data' => $data]);
	}
	
	// ==========================================
	// SECCIÓN: ENDPOINTS AJAX - CLIENTES
	// ==========================================
	
	/**
	 * Clientes con más compras (AJAX)
	 */
	public function clientesMasCompras() {
		$limite = $this->input->get('limite') ?: 10;
		$fechaDesde = $this->input->get('fecha_desde');
		$fechaHasta = $this->input->get('fecha_hasta');
		
		$data = $this->Reportes_model->clientesMasCompras($limite, $fechaDesde, $fechaHasta);
		$this->jsonResponse(['success' => true, 'data' => $data]);
	}
	
	/**
	 * Clientes con mayor facturación (AJAX)
	 */
	public function clientesMayorFacturacion() {
		$limite = $this->input->get('limite') ?: 10;
		$fechaDesde = $this->input->get('fecha_desde');
		$fechaHasta = $this->input->get('fecha_hasta');
		
		$data = $this->Reportes_model->clientesMayorFacturacion($limite, $fechaDesde, $fechaHasta);
		$this->jsonResponse(['success' => true, 'data' => $data]);
	}
	
	/**
	 * Clientes inactivos (AJAX)
	 */
	public function clientesInactivos() {
		$dias = $this->input->get('dias') ?: 30;
		$data = $this->Reportes_model->clientesInactivos($dias);
		$this->jsonResponse(['success' => true, 'data' => $data]);
	}
	
	/**
	 * Historial consolidado de un cliente (AJAX)
	 */
	public function historialCliente($id = null) {
		$id = $id ?: $this->input->get('id');
		
		if (!$id) {
			$this->jsonResponse(['success' => false, 'message' => 'ID de cliente requerido'], 400);
			return;
		}
		
		$data = $this->Reportes_model->historialCliente($id);
		$this->jsonResponse(['success' => true, 'data' => $data]);
	}
	
	// ==========================================
	// SECCIÓN: ENDPOINTS AJAX - FINANCIERO
	// ==========================================
	
	/**
	 * Resumen financiero (AJAX)
	 */
	public function resumenFinanciero() {
		$fechaDesde = $this->input->get('fecha_desde');
		$fechaHasta = $this->input->get('fecha_hasta');
		
		$data = $this->Reportes_model->resumenFinanciero($fechaDesde, $fechaHasta);
		$this->jsonResponse(['success' => true, 'data' => $data]);
	}
	
	/**
	 * Cuentas por cobrar (AJAX)
	 */
	public function cuentasPorCobrar() {
		$data = $this->Reportes_model->cuentasPorCobrar();
		$this->jsonResponse(['success' => true, 'data' => $data]);
	}
	
	/**
	 * Flujo de caja (AJAX)
	 */
	public function flujoCaja() {
		$fechaDesde = $this->input->get('fecha_desde');
		$fechaHasta = $this->input->get('fecha_hasta');
		
		$data = $this->Reportes_model->flujoCaja($fechaDesde, $fechaHasta);
		$this->jsonResponse(['success' => true, 'data' => $data]);
	}
	
	/**
	 * Ingresos por día (AJAX)
	 */
	public function ingresosPorDia() {
		$fechaDesde = $this->input->get('fecha_desde');
		$fechaHasta = $this->input->get('fecha_hasta');
		
		$data = $this->Reportes_model->ingresosPorDia($fechaDesde, $fechaHasta);
		$this->jsonResponse(['success' => true, 'data' => $data]);
	}
	
	// ==========================================
	// SECCIÓN: EXPORTACIÓN PDF
	// ==========================================
	
	/**
	 * Preparar datos comunes para PDF
	 */
	private function pdfBase($fechaDesde, $fechaHasta) {
		$this->load->model('Facturacion_model');
		return [
			'usuario' => $this->getUsuarioActual(),
			'empresa' => $this->Facturacion_model->obtenerEmisor(),
			'desde'   => $fechaDesde,
			'hasta'   => $fechaHasta
		];
	}
	
	/**
	 * PDF Reporte de Ventas
	 */
	public function pdfVentas() {
		$fechaDesde = $this->input->get('desde') ?: date('Y-m-01');
		$fechaHasta = $this->input->get('hasta') ?: date('Y-m-d');
		
		$periodo = $this->Reportes_model->ventasPorPeriodo('dia', $fechaDesde, $fechaHasta);
		$montoTotal = 0;
		$totalVentas = 0;
		foreach ($periodo as $r) {
			$montoTotal  += $r->monto_total;
			$totalVentas += $r->total_ventas;
		}
		
		$view = $this->pdfBase($fechaDesde, $fechaHasta);
		$view['tipo'] = 'ventas';
		$view['datos'] = [
			'total_ventas' => $totalVentas,
			'monto_total'  => $montoTotal,
			'promedio'     => $totalVentas > 0 ? $montoTotal / $totalVentas : 0,
			'por_periodo'  => $periodo,
			'por_metodo'   => $this->Reportes_model->ventasPorMetodoPago($fechaDesde, $fechaHasta),
			'por_vendedor' => $this->Reportes_model->ventasPorVendedor($fechaDesde, $fechaHasta)
		];
		
		$this->load->view('reportes/pdf_reporte', $view);
	}
	
	/**
	 * PDF Reporte de Productos
	 */
	public function pdfProductos() {
		$fechaDesde = $this->input->get('desde') ?: date('Y-01-01');
		$fechaHasta = $this->input->get('hasta') ?: date('Y-m-d');
		
		$view = $this->pdfBase($fechaDesde, $fechaHasta);
		$view['tipo'] = 'productos';
		$view['datos'] = [
			'mas_vendidos' => $this->Reportes_model->productosMasVendidos(20, $fechaDesde, $fechaHasta),
			'stock_bajo'   => $this->Reportes_model->productosStockBajo(),
			'por_vencer'   => $this->Reportes_model->productosPorVencer(30)
		];
		
		$this->load->view('reportes/pdf_reporte', $view);
	}
	
	/**
	 * PDF Reporte de Clientes
	 */
	public function pdfClientes() {
		$fechaDesde = $this->input->get('desde') ?: date('Y-01-01');
		$fechaHasta = $this->input->get('hasta') ?: date('Y-m-d');
		
		$view = $this->pdfBase($fechaDesde, $fechaHasta);
		$view['tipo'] = 'clientes';
		$view['datos'] = [
			'mas_compras'       => $this->Reportes_model->clientesMasCompras(20, $fechaDesde, $fechaHasta),
			'mayor_facturacion' => $this->Reportes_model->clientesMayorFacturacion(20, $fechaDesde, $fechaHasta),
			'inactivos'         => $this->Reportes_model->clientesInactivos(30)
		];
		
		$this->load->view('reportes/pdf_reporte', $view);
	}
	
	/**
	 * PDF Reporte Financiero
	 */
	public function pdfFinanciero() {
		$fechaDesde = $this->input->get('desde') ?: date('Y-m-01');
		$fechaHasta = $this->input->get('hasta') ?: date('Y-m-d');
		
		$view = $this->pdfBase($fechaDesde, $fechaHasta);
		$view['tipo'] = 'financiero';
		$view['datos'] = [
			'resumen'        => $this->Reportes_model->resumenFinanciero($fechaDesde, $fechaHasta),
			'cuentas_cobrar' => $this->Reportes_model->cuentasPorCobrar(),
			'flujo_caja'     => $this->Reportes_model->flujoCaja($fechaDesde, $fechaHasta)
		];
		
		$this->load->view('reportes/pdf_reporte', $view);
	}
}
