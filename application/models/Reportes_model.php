<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Reportes_model
 * 
 * Modelo para el módulo de Reportes y Estadísticas
 * Consultas SQL optimizadas para reportes de ventas, productos, clientes y financiero
 */
class Reportes_model extends MY_Model {
	
	public $table = 'ventas';
	public $table_id = 'id';
	
	public function __construct() {
		parent::__construct();
	}
	
	// ==========================================
	// SECCIÓN: REPORTES DE VENTAS
	// ==========================================
	
	/**
	 * Obtener ventas agrupadas por período
	 * 
	 * @param string $agrupacion 'dia', 'semana', 'mes', 'anio'
	 * @param string $fechaDesde
	 * @param string $fechaHasta
	 * @return array
	 */
	public function ventasPorPeriodo($agrupacion = 'mes', $fechaDesde = null, $fechaHasta = null) {
		$fechaDesde = $fechaDesde ?: date('Y-01-01');
		$fechaHasta = $fechaHasta ?: date('Y-m-d');
		
		switch ($agrupacion) {
			case 'dia':
				$selectFecha = "DATE(v.fecha_venta) AS periodo, DATE_FORMAT(v.fecha_venta, '%d/%m/%Y') AS periodo_label";
				$groupBy = "DATE(v.fecha_venta)";
				break;
			case 'semana':
				$selectFecha = "YEARWEEK(v.fecha_venta, 1) AS periodo, CONCAT('Sem ', WEEK(v.fecha_venta, 1), ' - ', YEAR(v.fecha_venta)) AS periodo_label";
				$groupBy = "YEARWEEK(v.fecha_venta, 1)";
				break;
			case 'mes':
				$selectFecha = "DATE_FORMAT(v.fecha_venta, '%Y-%m') AS periodo, DATE_FORMAT(v.fecha_venta, '%M %Y') AS periodo_label";
				$groupBy = "DATE_FORMAT(v.fecha_venta, '%Y-%m')";
				break;
			case 'anio':
				$selectFecha = "YEAR(v.fecha_venta) AS periodo, YEAR(v.fecha_venta) AS periodo_label";
				$groupBy = "YEAR(v.fecha_venta)";
				break;
			default:
				$selectFecha = "DATE_FORMAT(v.fecha_venta, '%Y-%m') AS periodo, DATE_FORMAT(v.fecha_venta, '%M %Y') AS periodo_label";
				$groupBy = "DATE_FORMAT(v.fecha_venta, '%Y-%m')";
		}
		
		return $this->db->select("{$selectFecha},
				COUNT(*) AS total_ventas,
				COALESCE(SUM(v.subtotal), 0) AS subtotal,
				COALESCE(SUM(v.total_impuestos), 0) AS total_impuestos,
				COALESCE(SUM(v.total_descuentos), 0) AS total_descuentos,
				COALESCE(SUM(v.total_final), 0) AS monto_total", FALSE)
			->from('ventas v')
			->where('DATE(v.fecha_venta) >=', $fechaDesde)
			->where('DATE(v.fecha_venta) <=', $fechaHasta)
			->group_by($groupBy, FALSE)
			->order_by('periodo', 'ASC')
			->get()
			->result();
	}
	
	/**
	 * Ventas por método de pago
	 * 
	 * @param string $fechaDesde
	 * @param string $fechaHasta
	 * @return array
	 */
	public function ventasPorMetodoPago($fechaDesde = null, $fechaHasta = null) {
		$fechaDesde = $fechaDesde ?: date('Y-m-01');
		$fechaHasta = $fechaHasta ?: date('Y-m-d');
		
		return $this->db->select('p.metodo_pago,
				COUNT(DISTINCT p.id) AS total_ventas,
				COALESCE(SUM(p.monto), 0) AS monto_total')
			->from('pagos p')
			->join('ventas v', 'p.id_venta = v.id')
			->where('DATE(v.fecha_venta) >=', $fechaDesde)
			->where('DATE(v.fecha_venta) <=', $fechaHasta)
			->group_by('p.metodo_pago')
			->order_by('monto_total', 'DESC')
			->get()
			->result();
	}
	
	/**
	 * Ventas por vendedor
	 * 
	 * @param string $fechaDesde
	 * @param string $fechaHasta
	 * @return array
	 */
	public function ventasPorVendedor($fechaDesde = null, $fechaHasta = null) {
		$fechaDesde = $fechaDesde ?: date('Y-m-01');
		$fechaHasta = $fechaHasta ?: date('Y-m-d');
		
		return $this->db->select('u.id_usuario, u.nombre_completo AS vendedor,
				COUNT(v.id) AS total_ventas,
				COALESCE(SUM(v.total_final), 0) AS monto_total,
				COALESCE(AVG(v.total_final), 0) AS promedio_venta')
			->from('ventas v')
			->join('usuarios u', 'v.id_vendedor = u.id_usuario', 'left')
			->where('DATE(v.fecha_venta) >=', $fechaDesde)
			->where('DATE(v.fecha_venta) <=', $fechaHasta)
			->group_by('u.id_usuario')
			->order_by('monto_total', 'DESC')
			->get()
			->result();
	}
	
	/**
	 * Comparativo facturado vs no facturado
	 * 
	 * @param string $fechaDesde
	 * @param string $fechaHasta
	 * @return object
	 */
	public function facturadoVsNoFacturado($fechaDesde = null, $fechaHasta = null) {
		$fechaDesde = $fechaDesde ?: date('Y-m-01');
		$fechaHasta = $fechaHasta ?: date('Y-m-d');
		
		$result = new stdClass();
		
		// Total ventas en período
		$total = $this->db->select('COUNT(*) AS total_ventas, COALESCE(SUM(total_final), 0) AS monto_total')
			->from('ventas')
			->where('DATE(fecha_venta) >=', $fechaDesde)
			->where('DATE(fecha_venta) <=', $fechaHasta)
			->get()
			->row();
		
		$result->total_ventas = $total->total_ventas;
		$result->monto_total = $total->monto_total;
		
		// Ventas facturadas
		$facturadas = $this->db->select('COUNT(DISTINCT v.id) AS ventas_facturadas, COALESCE(SUM(f.total_final), 0) AS monto_facturado')
			->from('ventas v')
			->join('facturas f', 'v.id = f.id_venta AND f.estado = "emitida"', 'inner')
			->where('DATE(v.fecha_venta) >=', $fechaDesde)
			->where('DATE(v.fecha_venta) <=', $fechaHasta)
			->get()
			->row();
		
		$result->facturado_cantidad = $facturadas->ventas_facturadas;
		$result->facturado_monto = $facturadas->monto_facturado;
		$result->no_facturado_cantidad = $result->total_ventas - $result->facturado_cantidad;
		$result->no_facturado_monto = $result->monto_total - $result->facturado_monto;
		
		return $result;
	}
	
	// ==========================================
	// SECCIÓN: REPORTES DE PRODUCTOS
	// ==========================================
	
	/**
	 * Productos más vendidos
	 * 
	 * @param int $limite
	 * @param string $fechaDesde
	 * @param string $fechaHasta
	 * @return array
	 */
	public function productosMasVendidos($limite = 10, $fechaDesde = null, $fechaHasta = null) {
		$fechaDesde = $fechaDesde ?: date('Y-01-01');
		$fechaHasta = $fechaHasta ?: date('Y-m-d');
		
		return $this->db->select('vd.id_producto, vd.nombre_historico AS nombre, vd.sku_historico AS codigo,
				vd.categoria_historica AS categoria,
				SUM(vd.cantidad) AS total_vendido,
				SUM(vd.subtotal_linea) AS total_ingresos,
				SUM(vd.cantidad * vd.costo_unitario_historico) AS total_costo,
				(SUM(vd.subtotal_linea) - SUM(vd.cantidad * vd.costo_unitario_historico)) AS utilidad,
				p.stock_actual AS stock, p.imagen_principal_url')
			->from('ventas_detalle vd')
			->join('ventas v', 'vd.id_venta = v.id')
			->join('productos p', 'vd.id_producto = p.id', 'left')
			->where('DATE(v.fecha_venta) >=', $fechaDesde)
			->where('DATE(v.fecha_venta) <=', $fechaHasta)
			->group_by('vd.id_producto')
			->order_by('total_vendido', 'DESC')
			->limit($limite)
			->get()
			->result();
	}
	
	/**
	 * Productos menos vendidos (con al menos 1 venta)
	 * 
	 * @param int $limite
	 * @param string $fechaDesde
	 * @param string $fechaHasta
	 * @return array
	 */
	public function productosMenosVendidos($limite = 10, $fechaDesde = null, $fechaHasta = null) {
		$fechaDesde = $fechaDesde ?: date('Y-01-01');
		$fechaHasta = $fechaHasta ?: date('Y-m-d');
		
		return $this->db->select('vd.id_producto, vd.nombre_historico AS nombre, vd.sku_historico AS codigo,
				vd.categoria_historica AS categoria,
				SUM(vd.cantidad) AS total_vendido,
				SUM(vd.subtotal_linea) AS total_ingresos,
				p.stock_actual AS stock, p.imagen_principal_url')
			->from('ventas_detalle vd')
			->join('ventas v', 'vd.id_venta = v.id')
			->join('productos p', 'vd.id_producto = p.id', 'left')
			->where('DATE(v.fecha_venta) >=', $fechaDesde)
			->where('DATE(v.fecha_venta) <=', $fechaHasta)
			->group_by('vd.id_producto')
			->order_by('total_vendido', 'ASC')
			->limit($limite)
			->get()
			->result();
	}
	
	/**
	 * Productos con stock bajo (stock_actual <= stock_minimo)
	 * 
	 * @return array
	 */
	public function productosStockBajo() {
		return $this->db->select('p.id, p.sku AS codigo, p.nombre, p.marca, p.stock_actual AS stock, p.stock_minimo,
				p.precio_costo AS precio_compra, p.precio_venta, p.imagen_principal_url, c.nombre AS categoria')
			->from('productos p')
			->join('configuraciones c', 'p.id_categoria = c.id', 'left')
			->where('p.stock_actual <= p.stock_minimo')
			->where('p.estado', 'activo')
			->order_by('p.stock_actual', 'ASC')
			->get()
			->result();
	}
	
	/**
	 * Productos por vencer en los próximos N días
	 * 
	 * @param int $dias
	 * @return array
	 */
	public function productosPorVencer($dias = 30) {
		$fechaLimite = date('Y-m-d', strtotime("+{$dias} days"));
		
		return $this->db->select('p.id, p.sku AS codigo, p.nombre, p.marca, p.stock_actual AS stock,
				p.fecha_vencimiento, p.precio_venta, p.imagen_principal_url,
				c.nombre AS categoria,
				DATEDIFF(p.fecha_vencimiento, CURDATE()) AS dias_restantes')
			->from('productos p')
			->join('configuraciones c', 'p.id_categoria = c.id', 'left')
			->where('p.es_perecedero', 1)
			->where('p.fecha_vencimiento IS NOT NULL')
			->where('p.fecha_vencimiento <=', $fechaLimite)
			->where('p.estado', 'activo')
			->order_by('p.fecha_vencimiento', 'ASC')
			->get()
			->result();
	}
	
	/**
	 * Rotación de inventario
	 * Calcula: (Costo de ventas / Inventario promedio) para cada producto
	 * 
	 * @param string $fechaDesde
	 * @param string $fechaHasta
	 * @return array
	 */
	public function rotacionInventario($fechaDesde = null, $fechaHasta = null) {
		$fechaDesde = $fechaDesde ?: date('Y-01-01');
		$fechaHasta = $fechaHasta ?: date('Y-m-d');
		
		return $this->db->select('p.id, p.sku AS codigo, p.nombre, p.marca, p.stock_actual AS stock, p.precio_costo,
				c.nombre AS categoria,
				COALESCE(SUM(vd.cantidad), 0) AS total_vendido,
				COALESCE(SUM(vd.cantidad * vd.costo_unitario_historico), 0) AS costo_ventas,
				(p.stock_actual * p.precio_costo) AS valor_inventario_actual,
				CASE 
					WHEN (p.stock_actual * p.precio_costo) > 0 
					THEN ROUND(COALESCE(SUM(vd.cantidad * vd.costo_unitario_historico), 0) / (p.stock_actual * p.precio_costo), 2)
					ELSE 0 
				END AS rotacion')
			->from('productos p')
			->join('configuraciones c', 'p.id_categoria = c.id', 'left')
			->join('ventas_detalle vd', 'p.id = vd.id_producto', 'left')
			->join('ventas v', 'vd.id_venta = v.id AND DATE(v.fecha_venta) >= "' . $fechaDesde . '" AND DATE(v.fecha_venta) <= "' . $fechaHasta . '"', 'left')
			->where('p.estado', 'activo')
			->group_by('p.id')
			->order_by('rotacion', 'DESC')
			->get()
			->result();
	}
	
	// ==========================================
	// SECCIÓN: REPORTES DE CLIENTES
	// ==========================================
	
	/**
	 * Clientes con más compras
	 * 
	 * @param int $limite
	 * @param string $fechaDesde
	 * @param string $fechaHasta
	 * @return array
	 */
	public function clientesMasCompras($limite = 10, $fechaDesde = null, $fechaHasta = null) {
		$fechaDesde = $fechaDesde ?: date('Y-01-01');
		$fechaHasta = $fechaHasta ?: date('Y-m-d');
		
		return $this->db->select('c.id, c.nombre_completo AS cliente, c.numero_documento, c.correo_electronico,
				COUNT(v.id) AS total_compras,
				COALESCE(SUM(v.total_final), 0) AS monto_total,
				COALESCE(AVG(v.total_final), 0) AS promedio_compra,
				MAX(v.fecha_venta) AS ultima_compra')
			->from('clientes c')
			->join('ventas v', 'c.id = v.id_cliente AND DATE(v.fecha_venta) >= "' . $fechaDesde . '" AND DATE(v.fecha_venta) <= "' . $fechaHasta . '"')
			->group_by('c.id')
			->order_by('total_compras', 'DESC')
			->limit($limite)
			->get()
			->result();
	}
	
	/**
	 * Clientes con mayor facturación
	 * 
	 * @param int $limite
	 * @param string $fechaDesde
	 * @param string $fechaHasta
	 * @return array
	 */
	public function clientesMayorFacturacion($limite = 10, $fechaDesde = null, $fechaHasta = null) {
		$fechaDesde = $fechaDesde ?: date('Y-01-01');
		$fechaHasta = $fechaHasta ?: date('Y-m-d');
		
		return $this->db->select('c.id, c.nombre_completo AS cliente, c.numero_documento,
				COUNT(v.id) AS total_facturas,
				COALESCE(SUM(v.total_final), 0) AS total_facturado,
				MAX(v.fecha_venta) AS ultima_compra')
			->from('clientes c')
			->join('ventas v', 'c.id = v.id_cliente AND DATE(v.fecha_venta) >= "' . $fechaDesde . '" AND DATE(v.fecha_venta) <= "' . $fechaHasta . '"')
			->group_by('c.id')
			->order_by('total_facturado', 'DESC')
			->limit($limite)
			->get()
			->result();
	}
	
	/**
	 * Clientes inactivos (sin compras en los últimos N días)
	 * 
	 * @param int $dias
	 * @return array
	 */
	public function clientesInactivos($dias = 30) {
		$fechaLimite = date('Y-m-d', strtotime("-{$dias} days"));
		
		return $this->db->select('c.id, c.nombre_completo AS cliente, c.numero_documento, c.correo_electronico AS correo,
				c.fec_creacion,
				(SELECT MAX(v.fecha_venta) FROM ventas v WHERE v.id_cliente = c.id) AS ultima_compra,
				(SELECT COUNT(*) FROM ventas v2 WHERE v2.id_cliente = c.id) AS total_compras,
				(SELECT COALESCE(SUM(v3.total_final), 0) FROM ventas v3 WHERE v3.id_cliente = c.id) AS monto_historico,
				DATEDIFF(CURDATE(), COALESCE((SELECT MAX(v4.fecha_venta) FROM ventas v4 WHERE v4.id_cliente = c.id), c.fec_creacion)) AS dias_inactivo')
			->from('clientes c')
			->where("c.id NOT IN (SELECT DISTINCT v.id_cliente FROM ventas v WHERE DATE(v.fecha_venta) >= '{$fechaLimite}')", NULL, FALSE)
			->order_by('c.nombre_completo', 'ASC')
			->get()
			->result();
	}
	
	/**
	 * Historial consolidado de un cliente
	 * 
	 * @param int $idCliente
	 * @return object
	 */
	public function historialCliente($idCliente) {
		$resultado = new stdClass();
		
		// Datos del cliente
		$resultado->cliente = $this->db->from('clientes')
			->where('id', $idCliente)
			->get()
			->row();
		
		// Estadísticas generales
		$stats = $this->db->select('COUNT(*) AS total_compras,
				COALESCE(SUM(total_final), 0) AS monto_total,
				COALESCE(AVG(total_final), 0) AS promedio_compra,
				MIN(fecha_venta) AS primera_compra,
				MAX(fecha_venta) AS ultima_compra')
			->from('ventas')
			->where('id_cliente', $idCliente)
			->get()
			->row();
		
		$resultado->estadisticas = $stats;
		$resultado->resumen = $stats;
		
		// Ventas individuales
		$resultado->ventas = $this->db->select('v.id, v.folio_factura, v.estado, v.fecha_venta, v.subtotal, v.total_impuestos AS impuesto,
				v.total_descuentos, v.total_final AS total, v.fec_creacion,
				(SELECT COUNT(*) FROM ventas_detalle vd WHERE vd.id_venta = v.id) AS total_productos')
			->from('ventas v')
			->where('v.id_cliente', $idCliente)
			->order_by('v.fecha_venta', 'DESC')
			->get()
			->result();
		
		// Compras por mes
		$resultado->compras_por_mes = $this->db->select("DATE_FORMAT(fecha_venta, '%Y-%m') AS periodo,
				DATE_FORMAT(fecha_venta, '%M %Y') AS periodo_label,
				COUNT(*) AS total_compras,
				SUM(total_final) AS monto_total", FALSE)
			->from('ventas')
			->where('id_cliente', $idCliente)
			->group_by("DATE_FORMAT(fecha_venta, '%Y-%m')", FALSE)
			->order_by('periodo', 'ASC')
			->get()
			->result();
		
		// Productos comprados
		$resultado->productos_comprados = $this->db->select('vd.nombre_historico AS nombre, vd.sku_historico AS sku,
				SUM(vd.cantidad) AS total_cantidad,
				SUM(vd.subtotal_linea) AS total_gastado')
			->from('ventas_detalle vd')
			->join('ventas v', 'vd.id_venta = v.id')
			->where('v.id_cliente', $idCliente)
			->group_by('vd.id_producto')
			->order_by('total_gastado', 'DESC')
			->get()
			->result();
		
		return $resultado;
	}
	
	// ==========================================
	// SECCIÓN: REPORTES FINANCIEROS
	// ==========================================
	
	/**
	 * Resumen financiero general
	 * 
	 * @param string $fechaDesde
	 * @param string $fechaHasta
	 * @return object
	 */
	public function resumenFinanciero($fechaDesde = null, $fechaHasta = null) {
		$fechaDesde = $fechaDesde ?: date('Y-m-01');
		$fechaHasta = $fechaHasta ?: date('Y-m-d');
		
		$resultado = new stdClass();
		
		// Ingresos por ventas
		$ventas = $this->db->select('COUNT(*) AS total_ventas,
				COALESCE(SUM(subtotal), 0) AS subtotal,
				COALESCE(SUM(total_impuestos), 0) AS total_impuestos,
				COALESCE(SUM(total_descuentos), 0) AS total_descuentos,
				COALESCE(SUM(total_final), 0) AS total_ingresos')
			->from('ventas')
			->where('DATE(fecha_venta) >=', $fechaDesde)
			->where('DATE(fecha_venta) <=', $fechaHasta)
			->get()
			->row();
		
		$resultado->total_ventas = $ventas->total_ventas;
		$resultado->subtotal = $ventas->subtotal;
		$resultado->total_impuestos = $ventas->total_impuestos;
		$resultado->total_descuentos = $ventas->total_descuentos;
		$resultado->ingresos_totales = $ventas->total_ingresos;
		
		// Total cobrado (pagos recibidos)
		$pagos = $this->db->select('COALESCE(SUM(p.monto), 0) AS total_cobrado')
			->from('pagos p')
			->join('ventas v', 'p.id_venta = v.id')
			->where('DATE(v.fecha_venta) >=', $fechaDesde)
			->where('DATE(v.fecha_venta) <=', $fechaHasta)
			->get()
			->row();
		
		$resultado->total_cobrado = $pagos->total_cobrado;
		
		// Cuentas por cobrar
		$resultado->cuentas_por_cobrar = $resultado->ingresos_totales - $resultado->total_cobrado;
		
		// Costo de lo vendido
		$costos = $this->db->select('COALESCE(SUM(vd.cantidad * vd.costo_unitario_historico), 0) AS costo_ventas')
			->from('ventas_detalle vd')
			->join('ventas v', 'vd.id_venta = v.id')
			->where('DATE(v.fecha_venta) >=', $fechaDesde)
			->where('DATE(v.fecha_venta) <=', $fechaHasta)
			->get()
			->row();
		
		$resultado->costo_ventas = $costos->costo_ventas;
		$resultado->utilidad_bruta = $resultado->subtotal - $resultado->costo_ventas;
		$resultado->margen_utilidad = $resultado->subtotal > 0 
			? round(($resultado->utilidad_bruta / $resultado->subtotal) * 100, 2) 
			: 0;
		
		// Desglose por método de pago
		$resultado->por_metodo = $this->db->select('p.metodo_pago,
				COUNT(*) AS total_transacciones,
				COALESCE(SUM(p.monto), 0) AS monto_total')
			->from('pagos p')
			->join('ventas v', 'p.id_venta = v.id')
			->where('DATE(v.fecha_venta) >=', $fechaDesde)
			->where('DATE(v.fecha_venta) <=', $fechaHasta)
			->group_by('p.metodo_pago')
			->order_by('monto_total', 'DESC')
			->get()
			->result();
		
		return $resultado;
	}
	
	/**
	 * Cuentas por cobrar detalladas
	 * 
	 * @return array
	 */
	public function cuentasPorCobrar() {
		return $this->db->select('v.id AS id_venta, v.folio_factura, v.fecha_venta, v.total_final AS total_venta,
				c.nombre_completo AS cliente, c.numero_documento,
				COALESCE(SUM(p.monto), 0) AS total_pagado,
				(v.total_final - COALESCE(SUM(p.monto), 0)) AS saldo_pendiente,
				DATEDIFF(CURDATE(), DATE(v.fecha_venta)) AS dias_transcurridos')
			->from('ventas v')
			->join('clientes c', 'v.id_cliente = c.id', 'left')
			->join('pagos p', 'v.id = p.id_venta', 'left')
			->group_by('v.id')
			->having('saldo_pendiente > 0')
			->order_by('dias_transcurridos', 'DESC')
			->get()
			->result();
	}
	
	/**
	 * Flujo de caja resumido por período
	 * 
	 * @param string $fechaDesde
	 * @param string $fechaHasta
	 * @return array
	 */
	public function flujoCaja($fechaDesde = null, $fechaHasta = null) {
		$fechaDesde = $fechaDesde ?: date('Y-m-01');
		$fechaHasta = $fechaHasta ?: date('Y-m-d');
		
		return $this->db->select("cc.id, cc.codigo_cierre,
				cc.fecha_inicio AS fecha_cierre_raw,
				DATE_FORMAT(cc.fecha_inicio, '%d/%m/%Y') AS fecha_cierre,
				cc.cerrado_por AS usuario,
				cc.efectivo_inicial AS monto_apertura,
				cc.monto_total_vendido AS total_ventas,
				cc.efectivo_esperado AS monto_cierre,
				cc.efectivo_contado, cc.diferencia_caja,
				cc.total_efectivo, cc.total_tarjeta_credito, cc.total_transferencia", FALSE)
			->from('cierres_caja cc')
			->where('DATE(cc.fecha_inicio) >=', $fechaDesde)
			->where('DATE(cc.fecha_fin) <=', $fechaHasta)
			->order_by('cc.fecha_inicio', 'DESC')
			->get()
			->result();
	}
	
	/**
	 * Ingresos agrupados por día (para gráfica de flujo)
	 * 
	 * @param string $fechaDesde
	 * @param string $fechaHasta
	 * @return array
	 */
	public function ingresosPorDia($fechaDesde = null, $fechaHasta = null) {
		$fechaDesde = $fechaDesde ?: date('Y-m-01');
		$fechaHasta = $fechaHasta ?: date('Y-m-d');
		
		return $this->db->select("DATE(p.fecha_pago) AS fecha_raw,
				DATE_FORMAT(p.fecha_pago, '%d/%m') AS fecha,
				COALESCE(SUM(p.monto), 0) AS monto_total", FALSE)
			->from('pagos p')
			->where('DATE(p.fecha_pago) >=', $fechaDesde)
			->where('DATE(p.fecha_pago) <=', $fechaHasta)
			->group_by('DATE(p.fecha_pago)', FALSE)
			->order_by('fecha_raw', 'ASC')
			->get()
			->result();
	}
	
	// ==========================================
	// SECCIÓN: KPIs DEL DASHBOARD
	// ==========================================
	
	/**
	 * Obtener KPIs generales para el dashboard de reportes
	 * 
	 * @param string $fechaDesde
	 * @param string $fechaHasta
	 * @return object
	 */
	public function obtenerKPIs($fechaDesde = null, $fechaHasta = null) {
		$fechaDesde = $fechaDesde ?: date('Y-m-01');
		$fechaHasta = $fechaHasta ?: date('Y-m-d');
		
		$kpis = new stdClass();
		
		// Ventas del período
		$ventas = $this->db->select('COUNT(*) AS total_ventas, 
				COALESCE(SUM(total_final), 0) AS monto_total,
				COALESCE(AVG(total_final), 0) AS promedio_venta')
			->from('ventas')
			->where('DATE(fecha_venta) >=', $fechaDesde)
			->where('DATE(fecha_venta) <=', $fechaHasta)
			->get()
			->row();
		
		$kpis->total_ventas = $ventas->total_ventas;
		$kpis->monto_total = $ventas->monto_total;
		$kpis->promedio_venta = round($ventas->promedio_venta, 2);
		
		// Total cobrado
		$cobrado = $this->db->select('COALESCE(SUM(p.monto), 0) AS total_cobrado')
			->from('pagos p')
			->join('ventas v', 'p.id_venta = v.id')
			->where('DATE(v.fecha_venta) >=', $fechaDesde)
			->where('DATE(v.fecha_venta) <=', $fechaHasta)
			->get()
			->row();
		$kpis->total_cobrado = $cobrado->total_cobrado;
		$kpis->total_por_cobrar = $kpis->monto_total - $kpis->total_cobrado;
		
		// Productos con stock bajo
		$kpis->productos_stock_bajo = $this->db->from('productos')
			->where('stock_actual <= stock_minimo')
			->where('estado', 'activo')
			->count_all_results();
		
		// Total clientes activos en período
		$kpis->clientes_activos = $this->db->select('COUNT(DISTINCT id_cliente) AS total')
			->from('ventas')
			->where('DATE(fecha_venta) >=', $fechaDesde)
			->where('DATE(fecha_venta) <=', $fechaHasta)
			->get()
			->row()
			->total;
		
		// Facturas emitidas
		$facturacion = $this->db->select('COUNT(*) AS total_facturas, 
				COALESCE(SUM(total_final), 0) AS monto_facturado')
			->from('facturas')
			->where('estado', 'emitida')
			->where('DATE(fecha_factura) >=', $fechaDesde)
			->where('DATE(fecha_factura) <=', $fechaHasta)
			->get()
			->row();
		$kpis->total_facturas = $facturacion->total_facturas;
		$kpis->monto_facturado = $facturacion->monto_facturado;
		
		// Valor del inventario
		$inventario = $this->db->select('COALESCE(SUM(precio_costo * stock_actual), 0) AS valor_costo,
				COALESCE(SUM(precio_venta * stock_actual), 0) AS valor_venta')
			->from('productos')
			->where('estado', 'activo')
			->get()
			->row();
		$kpis->valor_inventario = $inventario->valor_costo;
		$kpis->valor_inventario_venta = $inventario->valor_venta;
		
		return $kpis;
	}
	
	/**
	 * Comparativo entre dos períodos
	 * 
	 * @param string $fechaDesde1 Período actual inicio
	 * @param string $fechaHasta1 Período actual fin
	 * @param string $fechaDesde2 Período anterior inicio
	 * @param string $fechaHasta2 Período anterior fin
	 * @return object
	 */
	public function comparativoPeriodos($fechaDesde1, $fechaHasta1, $fechaDesde2, $fechaHasta2) {
		$resultado = new stdClass();
		
		// Período actual
		$actual = $this->db->select('COUNT(*) AS total_ventas, COALESCE(SUM(total_final), 0) AS monto_total')
			->from('ventas')
			->where('DATE(fecha_venta) >=', $fechaDesde1)
			->where('DATE(fecha_venta) <=', $fechaHasta1)
			->get()
			->row();
		
		// Período anterior
		$anterior = $this->db->select('COUNT(*) AS total_ventas, COALESCE(SUM(total_final), 0) AS monto_total')
			->from('ventas')
			->where('DATE(fecha_venta) >=', $fechaDesde2)
			->where('DATE(fecha_venta) <=', $fechaHasta2)
			->get()
			->row();
		
		$resultado->actual = $actual;
		$resultado->anterior = $anterior;
		
		// Calcular variación porcentual
		$resultado->variacion_ventas = $anterior->total_ventas > 0 
			? round((($actual->total_ventas - $anterior->total_ventas) / $anterior->total_ventas) * 100, 2) 
			: ($actual->total_ventas > 0 ? 100 : 0);
		
		$resultado->variacion_monto = $anterior->monto_total > 0 
			? round((($actual->monto_total - $anterior->monto_total) / $anterior->monto_total) * 100, 2) 
			: ($actual->monto_total > 0 ? 100 : 0);
		
		return $resultado;
	}
}
