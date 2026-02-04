<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Ventas_model
 * 
 * Modelo para la gestión de ventas/pedidos
 * Implementa operaciones CRUD con transacciones y validaciones de negocio
 */
class Ventas_model extends MY_Model {
	
	public $table = 'ventas';
	public $table_id = 'id';
	
	// Tabla de detalle
	protected $tableDetalle = 'ventas_detalle';
	
	// Estados de venta
	const ESTADO_PENDIENTE = 'pendiente';
	const ESTADO_CONFIRMADA = 'confirmada';
	const ESTADO_PAGADA = 'pagada';
	const ESTADO_ENVIADA = 'enviada';
	const ESTADO_COMPLETADA = 'completada';
	const ESTADO_CANCELADA = 'cancelada';
	
	public function __construct() {
		parent::__construct();
		$this->load->model('Productos_model');
	}
	
	/**
	 * Crear una nueva venta con sus detalles
	 * Ejecuta todo dentro de una transacción
	 * 
	 * @param array $ventaData Datos de la venta
	 * @param array $detalles Array de productos [{id_producto, cantidad, precio_unitario, descuento}]
	 * @param string $creadoPor Usuario que crea la venta
	 * @return array ['success' => bool, 'message' => string, 'id' => int|null]
	 */
	public function crearVenta($ventaData, $detalles, $creadoPor) {
		$this->db->trans_start();
		
		try {
			// Validar stock disponible antes de procesar
			$validacionStock = $this->validarStockDisponible($detalles);
			if (!$validacionStock['success']) {
				$this->db->trans_rollback();
				return $validacionStock;
			}
			
			// Generar folio único
			$ventaData['folio_factura'] = $this->generarFolio();
			$ventaData['creado_por'] = $creadoPor;
			$ventaData['fecha_venta'] = date('Y-m-d H:i:s');
			
			// Calcular totales
			$totales = $this->calcularTotales($detalles);
			$ventaData['subtotal'] = $totales['subtotal'];
			$ventaData['total_impuestos'] = $totales['total_impuestos'];
			$ventaData['total_descuentos'] = $totales['total_descuentos'];
			$ventaData['total_final'] = $totales['total_final'];
			
			// Insertar venta
			$this->db->insert($this->table, $ventaData);
			$idVenta = $this->db->insert_id();
			
			if (!$idVenta) {
				$this->db->trans_rollback();
				return ['success' => false, 'message' => 'Error al crear la venta'];
			}
			
			// Insertar detalles y actualizar stock
			foreach ($detalles as $detalle) {
				$producto = $this->Productos_model->find($detalle['id_producto']);
				
				// Obtener nombre de categoría
				$categoria = $this->db->select('nombre')
					->from('configuraciones')
					->where('id', $producto->id_categoria)
					->get()
					->row();
				
				// Calcular valores del detalle
				$cantidad = (int)$detalle['cantidad'];
				$precioUnitario = (float)$detalle['precio_unitario'];
				$costoUnitario = (float)$producto->precio_costo;
				$porcentajeImpuesto = (float)($producto->porcentaje_impuesto ?? 0);
				$descuento = (float)($detalle['descuento'] ?? 0);
				
				$subtotalLinea = ($cantidad * $precioUnitario);
				$montoImpuesto = $subtotalLinea * ($porcentajeImpuesto / 100);
				$montoDescuento = $descuento;
				$subtotalLineaFinal = $subtotalLinea + $montoImpuesto - $montoDescuento;
				
				$detalleData = [
					'id_venta' => $idVenta,
					'id_producto' => $detalle['id_producto'],
					'nombre_historico' => $producto->nombre,
					'sku_historico' => $producto->sku,
					'categoria_historica' => $categoria ? $categoria->nombre : null,
					'cantidad' => $cantidad,
					'precio_unitario' => $precioUnitario,
					'costo_unitario_historico' => $costoUnitario,
					'porcentaje_impuesto' => $porcentajeImpuesto,
					'monto_impuesto' => $montoImpuesto,
					'monto_descuento' => $montoDescuento,
					'subtotal_linea' => $subtotalLineaFinal,
					'creado_por' => $creadoPor
				];
				
				$this->db->insert($this->tableDetalle, $detalleData);
				
				// Actualizar stock del producto
				$nuevoStock = $producto->stock_actual - $cantidad;
				$this->db->where('id', $detalle['id_producto'])
					->update('productos', [
						'stock_actual' => $nuevoStock,
						'actualizado_por' => $creadoPor,
						'fec_actualizacion' => date('Y-m-d H:i:s')
					]);
			}
			
			$this->db->trans_complete();
			
			if ($this->db->trans_status() === FALSE) {
				return ['success' => false, 'message' => 'Error en la transacción'];
			}
			
			return [
				'success' => true, 
				'message' => 'Venta creada correctamente',
				'id' => $idVenta,
				'folio' => $ventaData['folio_factura']
			];
			
		} catch (Exception $e) {
			$this->db->trans_rollback();
			log_message('error', 'Error al crear venta: ' . $e->getMessage());
			return ['success' => false, 'message' => 'Error al procesar la venta: ' . $e->getMessage()];
		}
	}
	
	/**
	 * Validar que hay stock suficiente para todos los productos
	 * 
	 * @param array $detalles
	 * @return array
	 */
	private function validarStockDisponible($detalles) {
		$errores = [];
		
		foreach ($detalles as $detalle) {
			$producto = $this->Productos_model->find($detalle['id_producto']);
			
			if (!$producto) {
				$errores[] = "Producto ID {$detalle['id_producto']} no encontrado";
				continue;
			}
			
			if ($producto->estado !== 'activo') {
				$errores[] = "El producto '{$producto->nombre}' no está disponible para venta";
				continue;
			}
			
			if ($producto->stock_actual < $detalle['cantidad']) {
				$errores[] = "Stock insuficiente para '{$producto->nombre}'. Disponible: {$producto->stock_actual}, Solicitado: {$detalle['cantidad']}";
			}
		}
		
		if (!empty($errores)) {
			return ['success' => false, 'message' => implode('. ', $errores)];
		}
		
		return ['success' => true];
	}
	
	/**
	 * Calcular totales de la venta
	 * 
	 * @param array $detalles
	 * @return array
	 */
	private function calcularTotales($detalles) {
		$subtotal = 0;
		$totalImpuestos = 0;
		$totalDescuentos = 0;
		
		foreach ($detalles as $detalle) {
			$producto = $this->Productos_model->find($detalle['id_producto']);
			
			$cantidad = (int)$detalle['cantidad'];
			$precioUnitario = (float)$detalle['precio_unitario'];
			$porcentajeImpuesto = (float)($producto->porcentaje_impuesto ?? 0);
			$descuento = (float)($detalle['descuento'] ?? 0);
			
			$subtotalLinea = $cantidad * $precioUnitario;
			$impuestoLinea = $subtotalLinea * ($porcentajeImpuesto / 100);
			
			$subtotal += $subtotalLinea;
			$totalImpuestos += $impuestoLinea;
			$totalDescuentos += $descuento;
		}
		
		return [
			'subtotal' => round($subtotal, 2),
			'total_impuestos' => round($totalImpuestos, 2),
			'total_descuentos' => round($totalDescuentos, 2),
			'total_final' => round($subtotal + $totalImpuestos - $totalDescuentos, 2)
		];
	}
	
	/**
	 * Generar folio único para la factura
	 * Formato: VTA-YYYYMMDD-XXXX
	 * 
	 * @return string
	 */
	private function generarFolio() {
		$fecha = date('Ymd');
		$prefijo = "VTA-{$fecha}-";
		
		// Obtener el último folio del día
		$ultimoFolio = $this->db->select('folio_factura')
			->from($this->table)
			->like('folio_factura', $prefijo, 'after')
			->order_by('id', 'DESC')
			->limit(1)
			->get()
			->row();
		
		if ($ultimoFolio) {
			$numero = (int)substr($ultimoFolio->folio_factura, -4) + 1;
		} else {
			$numero = 1;
		}
		
		return $prefijo . str_pad($numero, 4, '0', STR_PAD_LEFT);
	}
	
	/**
	 * Obtener venta por ID con todos sus detalles
	 * 
	 * @param int $id
	 * @return object|null
	 */
	public function obtenerVentaCompleta($id) {
		$venta = $this->db->select('v.*, 
				c.nombre_completo AS cliente_nombre,
				c.numero_documento AS cliente_documento,
				c.correo_electronico AS cliente_correo,
				u.nombre_completo AS vendedor_nombre')
			->from("{$this->table} v")
			->join('clientes c', 'v.id_cliente = c.id', 'left')
			->join('usuarios u', 'v.id_vendedor = u.id_usuario', 'left')
			->where('v.id', $id)
			->get()
			->row();
		
		if (!$venta) {
			return null;
		}
		
		// Obtener detalles
		$venta->detalles = $this->db->select('vd.*, p.imagen_principal_url')
			->from("{$this->tableDetalle} vd")
			->join('productos p', 'vd.id_producto = p.id', 'left')
			->where('vd.id_venta', $id)
			->get()
			->result();
		
		// Obtener pagos
		$venta->pagos = $this->db->from('pagos')
			->where('id_venta', $id)
			->order_by('fecha_pago', 'ASC')
			->get()
			->result();
		
		// Calcular total pagado
		$venta->total_pagado = array_sum(array_column($venta->pagos, 'monto'));
		$venta->saldo_pendiente = $venta->total_final - $venta->total_pagado;
		
		return $venta;
	}
	
	/**
	 * Listar ventas con filtros para DataTable
	 * 
	 * @param array $filtros
	 * @return array
	 */
	public function listarVentas($filtros = []) {
		$this->db->select('v.*, 
				c.nombre_completo AS cliente_nombre,
				u.nombre_completo AS vendedor_nombre,
				(SELECT COALESCE(SUM(p.monto), 0) FROM pagos p WHERE p.id_venta = v.id) AS total_pagado')
			->from("{$this->table} v")
			->join('clientes c', 'v.id_cliente = c.id', 'left')
			->join('usuarios u', 'v.id_vendedor = u.id_usuario', 'left');
		
		// Aplicar filtros
		if (!empty($filtros['fecha_desde'])) {
			$this->db->where('DATE(v.fecha_venta) >=', $filtros['fecha_desde']);
		}
		
		if (!empty($filtros['fecha_hasta'])) {
			$this->db->where('DATE(v.fecha_venta) <=', $filtros['fecha_hasta']);
		}
		
		if (!empty($filtros['id_vendedor'])) {
			$this->db->where('v.id_vendedor', $filtros['id_vendedor']);
		}
		
		if (!empty($filtros['id_cliente'])) {
			$this->db->where('v.id_cliente', $filtros['id_cliente']);
		}
		
		if (!empty($filtros['folio'])) {
			$this->db->like('v.folio_factura', $filtros['folio']);
		}
		
		$this->db->order_by('v.fecha_venta', 'DESC');
		
		return $this->db->get()->result();
	}
	
	/**
	 * Cancelar una venta
	 * Revierte el stock de los productos
	 * 
	 * @param int $id
	 * @param string $actualizadoPor
	 * @return array
	 */
	public function cancelarVenta($id, $actualizadoPor) {
		$this->db->trans_start();
		
		try {
			$venta = $this->find($id);
			
			if (!$venta) {
				return ['success' => false, 'message' => 'Venta no encontrada'];
			}
			
			// Verificar si tiene pagos
			$totalPagado = $this->db->select('COALESCE(SUM(monto), 0) as total')
				->from('pagos')
				->where('id_venta', $id)
				->get()
				->row()
				->total;
			
			if ($totalPagado > 0) {
				return ['success' => false, 'message' => 'No se puede cancelar una venta con pagos registrados. Primero elimine los pagos.'];
			}
			
			// Obtener detalles para revertir stock
			$detalles = $this->db->from($this->tableDetalle)
				->where('id_venta', $id)
				->get()
				->result();
			
			// Revertir stock
			foreach ($detalles as $detalle) {
				$producto = $this->Productos_model->find($detalle->id_producto);
				if ($producto) {
					$nuevoStock = $producto->stock_actual + $detalle->cantidad;
					$this->db->where('id', $detalle->id_producto)
						->update('productos', [
							'stock_actual' => $nuevoStock,
							'actualizado_por' => $actualizadoPor,
							'fec_actualizacion' => date('Y-m-d H:i:s')
						]);
				}
			}
			
			// Marcar detalles como cancelados (soft delete con campo adicional si existiera)
			// Por ahora solo eliminamos los detalles ya que la venta principal mantiene el histórico
			$this->db->where('id_venta', $id)->delete($this->tableDetalle);
			
			// Eliminar la venta (o marcar como cancelada si tuviéramos campo estado)
			$this->db->where('id', $id)->delete($this->table);
			
			$this->db->trans_complete();
			
			if ($this->db->trans_status() === FALSE) {
				return ['success' => false, 'message' => 'Error al cancelar la venta'];
			}
			
			return ['success' => true, 'message' => 'Venta cancelada correctamente'];
			
		} catch (Exception $e) {
			$this->db->trans_rollback();
			log_message('error', 'Error al cancelar venta: ' . $e->getMessage());
			return ['success' => false, 'message' => 'Error al cancelar la venta'];
		}
	}
	
	/**
	 * Obtener estadísticas de ventas
	 * 
	 * @param string $periodo 'hoy', 'semana', 'mes', 'año'
	 * @return object
	 */
	public function obtenerEstadisticas($periodo = 'mes') {
		$stats = new stdClass();
		
		// Definir rango de fechas según período
		switch ($periodo) {
			case 'hoy':
				$fechaInicio = date('Y-m-d');
				$fechaFin = date('Y-m-d');
				break;
			case 'semana':
				$fechaInicio = date('Y-m-d', strtotime('monday this week'));
				$fechaFin = date('Y-m-d');
				break;
			case 'mes':
				$fechaInicio = date('Y-m-01');
				$fechaFin = date('Y-m-d');
				break;
			case 'año':
				$fechaInicio = date('Y-01-01');
				$fechaFin = date('Y-m-d');
				break;
			default:
				$fechaInicio = date('Y-m-01');
				$fechaFin = date('Y-m-d');
		}
		
		// Total de ventas en el período
		$result = $this->db->select('COUNT(*) as total_ventas, COALESCE(SUM(total_final), 0) as monto_total')
			->from($this->table)
			->where('DATE(fecha_venta) >=', $fechaInicio)
			->where('DATE(fecha_venta) <=', $fechaFin)
			->get()
			->row();
		
		$stats->total_ventas = $result->total_ventas;
		$stats->monto_total = $result->monto_total;
		
		// Ventas pendientes de pago
		$pendientes = $this->db->select('v.id, v.total_final, COALESCE(SUM(p.monto), 0) as pagado')
			->from("{$this->table} v")
			->join('pagos p', 'v.id = p.id_venta', 'left')
			->group_by('v.id')
			->having('v.total_final > COALESCE(SUM(p.monto), 0)')
			->get()
			->result();
		
		$stats->ventas_pendientes = count($pendientes);
		$stats->monto_pendiente = array_sum(array_map(function($v) {
			return $v->total_final - $v->pagado;
		}, $pendientes));
		
		// Promedio por venta
		$stats->promedio_venta = $stats->total_ventas > 0 
			? round($stats->monto_total / $stats->total_ventas, 2) 
			: 0;
		
		return $stats;
	}
	
	/**
	 * Obtener productos más vendidos
	 * 
	 * @param int $limite
	 * @return array
	 */
	public function productosMasVendidos($limite = 10) {
		return $this->db->select('vd.id_producto, vd.nombre_historico, 
				SUM(vd.cantidad) as total_cantidad,
				SUM(vd.subtotal_linea) as total_vendido,
				p.imagen_principal_url')
			->from("{$this->tableDetalle} vd")
			->join('productos p', 'vd.id_producto = p.id', 'left')
			->group_by('vd.id_producto')
			->order_by('total_cantidad', 'DESC')
			->limit($limite)
			->get()
			->result();
	}
	
	/**
	 * Obtener clientes para select
	 * 
	 * @return array
	 */
	public function obtenerClientes() {
		return $this->db->from('clientes')
			->order_by('nombre_completo', 'ASC')
			->get()
			->result();
	}
	
	/**
	 * Obtener vendedores para select
	 * 
	 * @return array
	 */
	public function obtenerVendedores() {
		return $this->db->from('usuarios')
			->order_by('nombre_completo', 'ASC')
			->get()
			->result();
	}
}
