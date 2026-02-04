<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Pagos_model
 * 
 * Modelo para la gestión de pagos de ventas
 * Implementa operaciones con validaciones de negocio
 */
class Pagos_model extends MY_Model {
	
	public $table = 'pagos';
	public $table_id = 'id';
	
	// Métodos de pago válidos
	const METODO_EFECTIVO = 'Efectivo';
	const METODO_TARJETA_CREDITO = 'Tarjeta de Crédito';
	const METODO_TARJETA_DEBITO = 'Tarjeta de Débito';
	const METODO_TRANSFERENCIA = 'Transferencia';
	const METODO_CHEQUE = 'Cheque';
	
	public function __construct() {
		parent::__construct();
	}
	
	/**
	 * Obtener métodos de pago disponibles
	 * 
	 * @return array
	 */
	public static function obtenerMetodosPago() {
		return [
			self::METODO_EFECTIVO,
			self::METODO_TARJETA_CREDITO,
			self::METODO_TARJETA_DEBITO,
			self::METODO_TRANSFERENCIA,
			self::METODO_CHEQUE
		];
	}
	
	/**
	 * Registrar un nuevo pago
	 * Valida que no exceda el total de la venta
	 * 
	 * @param array $pagoData
	 * @param string $creadoPor
	 * @return array ['success' => bool, 'message' => string, 'id' => int|null]
	 */
	public function registrarPago($pagoData, $creadoPor) {
		$this->db->trans_start();
		
		try {
			$idVenta = $pagoData['id_venta'];
			$montoPago = (float)$pagoData['monto'];
			
			// Obtener información de la venta
			$venta = $this->db->from('ventas')
				->where('id', $idVenta)
				->get()
				->row();
			
			if (!$venta) {
				return ['success' => false, 'message' => 'Venta no encontrada'];
			}
			
			// Calcular total ya pagado
			$totalPagado = $this->db->select('COALESCE(SUM(monto), 0) as total')
				->from($this->table)
				->where('id_venta', $idVenta)
				->get()
				->row()
				->total;
			
			$saldoPendiente = $venta->total_final - $totalPagado;
			
			// Validar que el pago no exceda el saldo pendiente
			if ($montoPago > $saldoPendiente) {
				return [
					'success' => false, 
					'message' => "El monto del pago (\${$montoPago}) excede el saldo pendiente (\${$saldoPendiente})"
				];
			}
			
			if ($montoPago <= 0) {
				return ['success' => false, 'message' => 'El monto del pago debe ser mayor a cero'];
			}
			
			// Preparar datos del pago
			$pagoData['fecha_pago'] = $pagoData['fecha_pago'] ?? date('Y-m-d H:i:s');
			$pagoData['creado_por'] = $creadoPor;
			
			// Insertar pago
			$this->db->insert($this->table, $pagoData);
			$idPago = $this->db->insert_id();
			
			if (!$idPago) {
				$this->db->trans_rollback();
				return ['success' => false, 'message' => 'Error al registrar el pago'];
			}
			
			$this->db->trans_complete();
			
			if ($this->db->trans_status() === FALSE) {
				return ['success' => false, 'message' => 'Error en la transacción'];
			}
			
			// Calcular nuevo saldo
			$nuevoSaldo = $saldoPendiente - $montoPago;
			$estadoPago = $nuevoSaldo <= 0 ? 'pagada' : 'parcial';
			
			return [
				'success' => true,
				'message' => 'Pago registrado correctamente',
				'id' => $idPago,
				'saldo_pendiente' => $nuevoSaldo,
				'estado' => $estadoPago
			];
			
		} catch (Exception $e) {
			$this->db->trans_rollback();
			log_message('error', 'Error al registrar pago: ' . $e->getMessage());
			return ['success' => false, 'message' => 'Error al procesar el pago'];
		}
	}
	
	/**
	 * Obtener pagos de una venta
	 * 
	 * @param int $idVenta
	 * @return array
	 */
	public function obtenerPagosPorVenta($idVenta) {
		return $this->db->from($this->table)
			->where('id_venta', $idVenta)
			->order_by('fecha_pago', 'DESC')
			->get()
			->result();
	}
	
	/**
	 * Obtener resumen de pagos de una venta
	 * 
	 * @param int $idVenta
	 * @return object
	 */
	public function obtenerResumenPagos($idVenta) {
		$venta = $this->db->from('ventas')
			->where('id', $idVenta)
			->get()
			->row();
		
		if (!$venta) {
			return null;
		}
		
		$pagos = $this->obtenerPagosPorVenta($idVenta);
		$totalPagado = array_sum(array_column($pagos, 'monto'));
		
		$resumen = new stdClass();
		$resumen->total_venta = $venta->total_final;
		$resumen->total_pagado = $totalPagado;
		$resumen->saldo_pendiente = $venta->total_final - $totalPagado;
		$resumen->cantidad_pagos = count($pagos);
		$resumen->pagos = $pagos;
		$resumen->esta_pagada = $resumen->saldo_pendiente <= 0;
		
		// Porcentaje pagado
		$resumen->porcentaje_pagado = $venta->total_final > 0 
			? round(($totalPagado / $venta->total_final) * 100, 2) 
			: 0;
		
		return $resumen;
	}
	
	/**
	 * Eliminar un pago
	 * Solo si la venta no está cerrada/completada
	 * 
	 * @param int $idPago
	 * @param string $actualizadoPor
	 * @return array
	 */
	public function eliminarPago($idPago, $actualizadoPor) {
		$pago = $this->find($idPago);
		
		if (!$pago) {
			return ['success' => false, 'message' => 'Pago no encontrado'];
		}
		
		// Eliminar el pago
		$this->db->where('id', $idPago)->delete($this->table);
		
		return [
			'success' => true,
			'message' => 'Pago eliminado correctamente',
			'id_venta' => $pago->id_venta
		];
	}
	
	/**
	 * Actualizar un pago existente
	 * 
	 * @param int $idPago
	 * @param array $pagoData
	 * @param string $actualizadoPor
	 * @return array
	 */
	public function actualizarPago($idPago, $pagoData, $actualizadoPor) {
		$pagoExistente = $this->find($idPago);
		
		if (!$pagoExistente) {
			return ['success' => false, 'message' => 'Pago no encontrado'];
		}
		
		$idVenta = $pagoExistente->id_venta;
		$nuevoMonto = (float)$pagoData['monto'];
		
		// Obtener información de la venta
		$venta = $this->db->from('ventas')
			->where('id', $idVenta)
			->get()
			->row();
		
		// Calcular total pagado excluyendo el pago actual
		$totalPagadoSinEste = $this->db->select('COALESCE(SUM(monto), 0) as total')
			->from($this->table)
			->where('id_venta', $idVenta)
			->where('id !=', $idPago)
			->get()
			->row()
			->total;
		
		$saldoDisponible = $venta->total_final - $totalPagadoSinEste;
		
		// Validar que el nuevo monto no exceda el saldo disponible
		if ($nuevoMonto > $saldoDisponible) {
			return [
				'success' => false,
				'message' => "El monto del pago (\${$nuevoMonto}) excede el saldo disponible (\${$saldoDisponible})"
			];
		}
		
		// Actualizar
		$pagoData['actualizado_por'] = $actualizadoPor;
		$this->db->where('id', $idPago)->update($this->table, $pagoData);
		
		return [
			'success' => true,
			'message' => 'Pago actualizado correctamente',
			'id_venta' => $idVenta
		];
	}
	
	/**
	 * Obtener estadísticas de pagos
	 * 
	 * @return object
	 */
	public function obtenerEstadisticas() {
		$stats = new stdClass();
		
		// Total recaudado hoy
		$stats->recaudado_hoy = $this->db->select('COALESCE(SUM(monto), 0) as total')
			->from($this->table)
			->where('DATE(fecha_pago)', date('Y-m-d'))
			->get()
			->row()
			->total;
		
		// Total recaudado este mes
		$stats->recaudado_mes = $this->db->select('COALESCE(SUM(monto), 0) as total')
			->from($this->table)
			->where('YEAR(fecha_pago)', date('Y'))
			->where('MONTH(fecha_pago)', date('m'))
			->get()
			->row()
			->total;
		
		// Por método de pago
		$stats->por_metodo = $this->db->select('metodo_pago, COUNT(*) as cantidad, SUM(monto) as total')
			->from($this->table)
			->where('YEAR(fecha_pago)', date('Y'))
			->where('MONTH(fecha_pago)', date('m'))
			->group_by('metodo_pago')
			->get()
			->result();
		
		return $stats;
	}
	
	/**
	 * Listar todos los pagos con información de venta
	 * 
	 * @param array $filtros
	 * @return array
	 */
	public function listarPagos($filtros = []) {
		$this->db->select('p.*, v.folio_factura, c.nombre_completo AS cliente_nombre')
			->from("{$this->table} p")
			->join('ventas v', 'p.id_venta = v.id', 'left')
			->join('clientes c', 'v.id_cliente = c.id', 'left');
		
		if (!empty($filtros['fecha_desde'])) {
			$this->db->where('DATE(p.fecha_pago) >=', $filtros['fecha_desde']);
		}
		
		if (!empty($filtros['fecha_hasta'])) {
			$this->db->where('DATE(p.fecha_pago) <=', $filtros['fecha_hasta']);
		}
		
		if (!empty($filtros['metodo_pago'])) {
			$this->db->where('p.metodo_pago', $filtros['metodo_pago']);
		}
		
		if (!empty($filtros['id_venta'])) {
			$this->db->where('p.id_venta', $filtros['id_venta']);
		}
		
		$this->db->order_by('p.fecha_pago', 'DESC');
		
		return $this->db->get()->result();
	}
}
