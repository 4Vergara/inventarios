<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Facturacion_model
 * 
 * Modelo para la gestión de facturación electrónica
 * Cumple con requisitos DIAN Colombia
 */
class Facturacion_model extends MY_Model {
	
	public $table = 'facturas';
	public $table_id = 'id';
	
	protected $tableDetalle = 'facturas_detalle';
	protected $tableEmisor = 'empresa_emisor';
	
	// Estados de factura
	const ESTADO_EMITIDA = 'emitida';
	const ESTADO_ANULADA = 'anulada';
	
	public function __construct() {
		parent::__construct();
	}
	
	/**
	 * Obtener datos del emisor (empresa)
	 * 
	 * @return object|null
	 */
	public function obtenerEmisor() {
		return $this->db->from($this->tableEmisor)
			->where('id', 1)
			->get()
			->row();
	}
	
	/**
	 * Actualizar datos del emisor
	 * 
	 * @param array $data
	 * @param string $actualizadoPor
	 * @return bool
	 */
	public function actualizarEmisor($data, $actualizadoPor) {
		$data['actualizado_por'] = $actualizadoPor;
		$this->db->where('id', 1)->update($this->tableEmisor, $data);
		return $this->db->affected_rows() >= 0;
	}
	
	/**
	 * Generar número de factura consecutivo
	 * Formato: {PREFIJO}-YYYYMMDD-XXXX
	 * 
	 * @return array ['numero' => string, 'consecutivo' => int]
	 */
	private function generarNumeroFactura() {
		$emisor = $this->obtenerEmisor();
		$prefijo = $emisor->prefijo_factura ?: 'FAC';
		$fecha = date('Ymd');
		
		$nuevoConsecutivo = $emisor->consecutivo_actual + 1;
		
		// Validar que no exceda el rango autorizado
		if ($nuevoConsecutivo > $emisor->rango_hasta) {
			return ['error' => 'Se ha excedido el rango de numeración autorizado por la DIAN. Solicite una nueva resolución.'];
		}
		
		// Actualizar consecutivo
		$this->db->where('id', $emisor->id)
			->update($this->tableEmisor, ['consecutivo_actual' => $nuevoConsecutivo]);
		
		$numero = $prefijo . '-' . $fecha . '-' . str_pad($nuevoConsecutivo, 4, '0', STR_PAD_LEFT);
		
		return [
			'numero' => $numero,
			'consecutivo' => $nuevoConsecutivo
		];
	}
	
	/**
	 * Generar factura a partir de una venta existente
	 * 
	 * @param int $idVenta
	 * @param array $datosExtra Datos adicionales del cliente para la factura
	 * @param string $creadoPor
	 * @return array
	 */
	public function generarFactura($idVenta, $datosExtra, $creadoPor) {
		$this->db->trans_start();
		
		try {
			// Verificar que la venta exista
			$venta = $this->db->select('v.*, 
					c.nombre_completo AS cliente_nombre,
					c.numero_documento AS cliente_documento,
					c.correo_electronico AS cliente_correo,
					u.nombre_completo AS vendedor_nombre')
				->from('ventas v')
				->join('clientes c', 'v.id_cliente = c.id', 'left')
				->join('usuarios u', 'v.id_vendedor = u.id_usuario', 'left')
				->where('v.id', $idVenta)
				->get()
				->row();
			
			if (!$venta) {
				return ['success' => false, 'message' => 'Venta no encontrada'];
			}
			
			// Verificar que no tenga ya una factura activa
			$facturaExistente = $this->db->from($this->table)
				->where('id_venta', $idVenta)
				->where('estado', self::ESTADO_EMITIDA)
				->get()
				->row();
			
			if ($facturaExistente) {
				return [
					'success' => false, 
					'message' => 'Esta venta ya tiene una factura emitida: ' . $facturaExistente->numero_factura
				];
			}
			
			// Generar número de factura
			$numeracion = $this->generarNumeroFactura();
			if (isset($numeracion['error'])) {
				return ['success' => false, 'message' => $numeracion['error']];
			}
			
			// Determinar forma y medio de pago
			$pagosPrincipal = $this->db->select('metodo_pago, SUM(monto) as total')
				->from('pagos')
				->where('id_venta', $idVenta)
				->group_by('metodo_pago')
				->order_by('total', 'DESC')
				->get()
				->row();
			
			$totalPagado = $this->db->select('COALESCE(SUM(monto), 0) as total')
				->from('pagos')
				->where('id_venta', $idVenta)
				->get()
				->row()
				->total;
			
			$formaPago = ($totalPagado >= $venta->total_final) ? 'Contado' : 'Crédito';
			$medioPago = $pagosPrincipal ? $pagosPrincipal->metodo_pago : 'Efectivo';
			
			// Preparar datos de la factura
			$facturaData = [
				'numero_factura' => $numeracion['numero'],
				'id_venta' => $idVenta,
				'id_emisor' => 1,
				'id_cliente' => $venta->id_cliente,
				'id_vendedor' => $venta->id_vendedor,
				'cliente_razon_social' => $datosExtra['cliente_razon_social'] ?? $venta->cliente_nombre,
				'cliente_nit_cc' => $datosExtra['cliente_nit_cc'] ?? $venta->cliente_documento,
				'cliente_direccion' => $datosExtra['cliente_direccion'] ?? null,
				'cliente_correo' => $datosExtra['cliente_correo'] ?? $venta->cliente_correo,
				'cliente_telefono' => $datosExtra['cliente_telefono'] ?? null,
				'fecha_factura' => date('Y-m-d H:i:s'),
				'fecha_vencimiento' => $datosExtra['fecha_vencimiento'] ?? null,
				'subtotal' => $venta->subtotal,
				'total_iva' => $venta->total_impuestos,
				'total_descuentos' => $venta->total_descuentos,
				'total_final' => $venta->total_final,
				'forma_pago' => $formaPago,
				'medio_pago' => $medioPago,
				'estado' => self::ESTADO_EMITIDA,
				'observaciones' => $datosExtra['observaciones'] ?? null,
				'creado_por' => $creadoPor
			];
			
			$this->db->insert($this->table, $facturaData);
			$idFactura = $this->db->insert_id();
			
			if (!$idFactura) {
				$this->db->trans_rollback();
				return ['success' => false, 'message' => 'Error al crear la factura'];
			}
			
			// Obtener detalles de la venta y copiar a detalle de factura
			$detallesVenta = $this->db->from('ventas_detalle')
				->where('id_venta', $idVenta)
				->get()
				->result();
			
			foreach ($detallesVenta as $det) {
				$detalleFactura = [
					'id_factura' => $idFactura,
					'id_producto' => $det->id_producto,
					'nombre_producto' => $det->nombre_historico,
					'sku' => $det->sku_historico,
					'cantidad' => $det->cantidad,
					'precio_unitario' => $det->precio_unitario,
					'porcentaje_iva' => $det->porcentaje_impuesto,
					'monto_iva' => $det->monto_impuesto,
					'monto_descuento' => $det->monto_descuento,
					'subtotal_linea' => $det->subtotal_linea,
					'creado_por' => $creadoPor
				];
				$this->db->insert($this->tableDetalle, $detalleFactura);
			}
			
			$this->db->trans_complete();
			
			if ($this->db->trans_status() === FALSE) {
				return ['success' => false, 'message' => 'Error en la transacción de facturación'];
			}
			
			return [
				'success' => true,
				'message' => 'Factura generada correctamente',
				'id' => $idFactura,
				'numero_factura' => $numeracion['numero']
			];
			
		} catch (Exception $e) {
			$this->db->trans_rollback();
			log_message('error', 'Error al generar factura: ' . $e->getMessage());
			return ['success' => false, 'message' => 'Error al generar la factura: ' . $e->getMessage()];
		}
	}
	
	/**
	 * Obtener factura completa con detalles
	 * 
	 * @param int $id
	 * @return object|null
	 */
	public function obtenerFacturaCompleta($id) {
		$factura = $this->db->select('f.*, 
				e.razon_social AS emisor_razon_social,
				e.nit AS emisor_nit,
				e.direccion AS emisor_direccion,
				e.ciudad AS emisor_ciudad,
				e.departamento AS emisor_departamento,
				e.telefono AS emisor_telefono,
				e.correo AS emisor_correo,
				e.regimen AS emisor_regimen,
				e.resolucion_dian AS emisor_resolucion,
				e.fecha_resolucion AS emisor_fecha_resolucion,
				e.prefijo_factura AS emisor_prefijo,
				e.rango_desde AS emisor_rango_desde,
				e.rango_hasta AS emisor_rango_hasta,
				e.logo_url AS emisor_logo,
				e.actividad_economica AS emisor_actividad_economica,
				v.folio_factura AS folio_venta,
				u.nombre_completo AS vendedor_nombre')
			->from("{$this->table} f")
			->join("{$this->tableEmisor} e", 'f.id_emisor = e.id', 'left')
			->join('ventas v', 'f.id_venta = v.id', 'left')
			->join('usuarios u', 'f.id_vendedor = u.id_usuario', 'left')
			->where('f.id', $id)
			->get()
			->row();
		
		if (!$factura) {
			return null;
		}
		
		// Obtener detalles
		$factura->detalles = $this->db->from($this->tableDetalle)
			->where('id_factura', $id)
			->get()
			->result();
		
		// Obtener pagos de la venta
		$factura->pagos = $this->db->from('pagos')
			->where('id_venta', $factura->id_venta)
			->order_by('fecha_pago', 'ASC')
			->get()
			->result();
		
		$factura->total_pagado = array_sum(array_column($factura->pagos, 'monto'));
		$factura->saldo_pendiente = $factura->total_final - $factura->total_pagado;
		
		return $factura;
	}
	
	/**
	 * Listar facturas con filtros
	 * 
	 * @param array $filtros
	 * @return array
	 */
	public function listarFacturas($filtros = []) {
		$this->db->select('f.*, 
				v.folio_factura AS folio_venta,
				u.nombre_completo AS vendedor_nombre')
			->from("{$this->table} f")
			->join('ventas v', 'f.id_venta = v.id', 'left')
			->join('usuarios u', 'f.id_vendedor = u.id_usuario', 'left');
		
		if (!empty($filtros['fecha_desde'])) {
			$this->db->where('DATE(f.fecha_factura) >=', $filtros['fecha_desde']);
		}
		if (!empty($filtros['fecha_hasta'])) {
			$this->db->where('DATE(f.fecha_factura) <=', $filtros['fecha_hasta']);
		}
		if (!empty($filtros['estado'])) {
			$this->db->where('f.estado', $filtros['estado']);
		}
		if (!empty($filtros['numero_factura'])) {
			$this->db->like('f.numero_factura', $filtros['numero_factura']);
		}
		if (!empty($filtros['cliente'])) {
			$this->db->like('f.cliente_razon_social', $filtros['cliente']);
		}
		
		$this->db->order_by('f.fecha_factura', 'DESC');
		
		return $this->db->get()->result();
	}
	
	/**
	 * Anular una factura
	 * 
	 * @param int $id
	 * @param string $motivo
	 * @param string $actualizadoPor
	 * @return array
	 */
	public function anularFactura($id, $motivo, $actualizadoPor) {
		$factura = $this->find($id);
		
		if (!$factura) {
			return ['success' => false, 'message' => 'Factura no encontrada'];
		}
		
		if ($factura->estado === self::ESTADO_ANULADA) {
			return ['success' => false, 'message' => 'La factura ya se encuentra anulada'];
		}
		
		if (empty($motivo)) {
			return ['success' => false, 'message' => 'Debe indicar un motivo de anulación'];
		}
		
		$this->db->where('id', $id)->update($this->table, [
			'estado' => self::ESTADO_ANULADA,
			'motivo_anulacion' => $motivo,
			'fecha_anulacion' => date('Y-m-d H:i:s'),
			'actualizado_por' => $actualizadoPor
		]);
		
		return ['success' => true, 'message' => 'Factura anulada correctamente'];
	}
	
	/**
	 * Verificar si una venta tiene factura emitida
	 * 
	 * @param int $idVenta
	 * @return object|null
	 */
	public function obtenerFacturaPorVenta($idVenta) {
		return $this->db->from($this->table)
			->where('id_venta', $idVenta)
			->where('estado', self::ESTADO_EMITIDA)
			->get()
			->row();
	}
	
	/**
	 * Obtener estadísticas de facturación
	 * 
	 * @param string $periodo
	 * @return object
	 */
	public function obtenerEstadisticas($periodo = 'mes') {
		$stats = new stdClass();
		
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
			case 'anio':
				$fechaInicio = date('Y-01-01');
				$fechaFin = date('Y-m-d');
				break;
			default:
				$fechaInicio = date('Y-m-01');
				$fechaFin = date('Y-m-d');
		}
		
		$result = $this->db->select('
				COUNT(*) as total_facturas,
				COALESCE(SUM(CASE WHEN estado = "emitida" THEN total_final ELSE 0 END), 0) as monto_facturado,
				COALESCE(SUM(CASE WHEN estado = "emitida" THEN total_iva ELSE 0 END), 0) as total_iva,
				COALESCE(SUM(CASE WHEN estado = "anulada" THEN 1 ELSE 0 END), 0) as facturas_anuladas')
			->from($this->table)
			->where('DATE(fecha_factura) >=', $fechaInicio)
			->where('DATE(fecha_factura) <=', $fechaFin)
			->get()
			->row();
		
		$stats->total_facturas = $result->total_facturas;
		$stats->monto_facturado = $result->monto_facturado;
		$stats->total_iva = $result->total_iva;
		$stats->facturas_anuladas = $result->facturas_anuladas;
		
		return $stats;
	}
	
	/**
	 * Obtener ventas que no tienen factura emitida
	 * 
	 * @return array
	 */
	public function obtenerVentasSinFactura() {
		return $this->db->select('v.*, 
				c.nombre_completo AS cliente_nombre,
				c.numero_documento AS cliente_documento,
				u.nombre_completo AS vendedor_nombre,
				(SELECT COALESCE(SUM(p.monto), 0) FROM pagos p WHERE p.id_venta = v.id) AS total_pagado')
			->from('ventas v')
			->join('clientes c', 'v.id_cliente = c.id', 'left')
			->join('usuarios u', 'v.id_vendedor = u.id_usuario', 'left')
			->where('v.id NOT IN (SELECT id_venta FROM facturas WHERE estado = "emitida")', NULL, FALSE)
			->order_by('v.fecha_venta', 'DESC')
			->get()
			->result();
	}
}
