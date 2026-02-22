<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Cierre_caja_model
 * 
 * Modelo para la gestión de cierres de caja
 * Soporta cierres por día, semana, mes y año
 */
class Cierre_caja_model extends MY_Model {
	
	public $table = 'cierres_caja';
	public $table_id = 'id';
	
	protected $tableDetalle = 'cierres_caja_detalle';
	
	// Tipos de período
	const PERIODO_DIA = 'dia';
	const PERIODO_SEMANA = 'semana';
	const PERIODO_MES = 'mes';
	const PERIODO_ANIO = 'anio';
	
	public function __construct() {
		parent::__construct();
	}
	
	/**
	 * Generar código de cierre único
	 * 
	 * @return string
	 */
	private function generarCodigoCierre() {
		$fecha = date('Ymd');
		$prefijo = "CC-{$fecha}-";
		
		$ultimo = $this->db->select('codigo_cierre')
			->from($this->table)
			->like('codigo_cierre', $prefijo, 'after')
			->order_by('id', 'DESC')
			->limit(1)
			->get()
			->row();
		
		$numero = 1;
		if ($ultimo) {
			$numero = (int)substr($ultimo->codigo_cierre, -4) + 1;
		}
		
		return $prefijo . str_pad($numero, 4, '0', STR_PAD_LEFT);
	}
	
	/**
	 * Obtener rango de fechas según tipo de período
	 * 
	 * @param string $tipoPeriodo
	 * @param string|null $fechaReferencia
	 * @return array ['inicio' => string, 'fin' => string]
	 */
	public function obtenerRangoFechas($tipoPeriodo, $fechaReferencia = null) {
		$ref = $fechaReferencia ? strtotime($fechaReferencia) : time();
		
		switch ($tipoPeriodo) {
			case self::PERIODO_DIA:
				$inicio = date('Y-m-d 00:00:00', $ref);
				$fin = date('Y-m-d 23:59:59', $ref);
				break;
			case self::PERIODO_SEMANA:
				$inicio = date('Y-m-d 00:00:00', strtotime('monday this week', $ref));
				$fin = date('Y-m-d 23:59:59', strtotime('sunday this week', $ref));
				break;
			case self::PERIODO_MES:
				$inicio = date('Y-m-01 00:00:00', $ref);
				$fin = date('Y-m-t 23:59:59', $ref);
				break;
			case self::PERIODO_ANIO:
				$inicio = date('Y-01-01 00:00:00', $ref);
				$fin = date('Y-12-31 23:59:59', $ref);
				break;
			default:
				$inicio = date('Y-m-d 00:00:00');
				$fin = date('Y-m-d 23:59:59');
		}
		
		return ['inicio' => $inicio, 'fin' => $fin];
	}
	
	/**
	 * Obtener preview de los datos del cierre (sin guardar)
	 * 
	 * @param string $tipoPeriodo
	 * @param string|null $fechaReferencia
	 * @return object
	 */
	public function obtenerPreviewCierre($tipoPeriodo, $fechaReferencia = null) {
		$rango = $this->obtenerRangoFechas($tipoPeriodo, $fechaReferencia);
		$preview = new stdClass();
		$preview->fecha_inicio = $rango['inicio'];
		$preview->fecha_fin = $rango['fin'];
		$preview->tipo_periodo = $tipoPeriodo;
		
		// Totales de ventas en el período
		$ventasTotales = $this->db->select('
				COUNT(*) as total_ventas,
				COALESCE(SUM(subtotal), 0) as monto_subtotal,
				COALESCE(SUM(total_impuestos), 0) as monto_impuestos,
				COALESCE(SUM(total_descuentos), 0) as monto_descuentos,
				COALESCE(SUM(total_final), 0) as monto_total_vendido')
			->from('ventas')
			->where('fecha_venta >=', $rango['inicio'])
			->where('fecha_venta <=', $rango['fin'])
			->get()
			->row();
		
		$preview->total_ventas = (int)$ventasTotales->total_ventas;
		$preview->monto_subtotal = (float)$ventasTotales->monto_subtotal;
		$preview->monto_impuestos = (float)$ventasTotales->monto_impuestos;
		$preview->monto_descuentos = (float)$ventasTotales->monto_descuentos;
		$preview->monto_total_vendido = (float)$ventasTotales->monto_total_vendido;
		
		// Desglose por método de pago
		$pagosPorMetodo = $this->db->select('
				p.metodo_pago,
				COALESCE(SUM(p.monto), 0) as total')
			->from('pagos p')
			->join('ventas v', 'p.id_venta = v.id')
			->where('v.fecha_venta >=', $rango['inicio'])
			->where('v.fecha_venta <=', $rango['fin'])
			->group_by('p.metodo_pago')
			->get()
			->result();
		
		$preview->total_efectivo = 0;
		$preview->total_tarjeta_credito = 0;
		$preview->total_tarjeta_debito = 0;
		$preview->total_transferencia = 0;
		$preview->total_cheque = 0;
		
		foreach ($pagosPorMetodo as $pago) {
			switch ($pago->metodo_pago) {
				case 'Efectivo':
					$preview->total_efectivo = (float)$pago->total;
					break;
				case 'Tarjeta de Crédito':
					$preview->total_tarjeta_credito = (float)$pago->total;
					break;
				case 'Tarjeta de Débito':
					$preview->total_tarjeta_debito = (float)$pago->total;
					break;
				case 'Transferencia':
					$preview->total_transferencia = (float)$pago->total;
					break;
				case 'Cheque':
					$preview->total_cheque = (float)$pago->total;
					break;
			}
		}
		
		// Facturas emitidas en el período
		$facturasCount = $this->db->from('facturas')
			->where('fecha_factura >=', $rango['inicio'])
			->where('fecha_factura <=', $rango['fin'])
			->where('estado', 'emitida')
			->count_all_results();
		
		$preview->total_facturas = $facturasCount;
		
		// Ventas anuladas (no tenemos campo estado en ventas, así que se cuenta 0 por defecto)
		$preview->ventas_anuladas = 0;
		$preview->monto_anulado = 0;
		
		// Listado de ventas del período
		$preview->ventas = $this->db->select('v.*, 
				c.nombre_completo AS cliente_nombre,
				(SELECT COALESCE(SUM(p.monto), 0) FROM pagos p WHERE p.id_venta = v.id) AS total_pagado,
				(SELECT GROUP_CONCAT(DISTINCT p.metodo_pago) FROM pagos p WHERE p.id_venta = v.id) AS metodos_pago')
			->from('ventas v')
			->join('clientes c', 'v.id_cliente = c.id', 'left')
			->where('v.fecha_venta >=', $rango['inicio'])
			->where('v.fecha_venta <=', $rango['fin'])
			->order_by('v.fecha_venta', 'ASC')
			->get()
			->result();
		
		return $preview;
	}
	
	/**
	 * Realizar cierre de caja
	 * 
	 * @param string $tipoPeriodo
	 * @param array $datosExtra efectivo_inicial, efectivo_contado, observaciones
	 * @param string $creadoPor
	 * @param string|null $fechaReferencia
	 * @return array
	 */
	public function realizarCierre($tipoPeriodo, $datosExtra, $creadoPor, $fechaReferencia = null) {
		$this->db->trans_start();
		
		try {
			$preview = $this->obtenerPreviewCierre($tipoPeriodo, $fechaReferencia);
			
			if ($preview->total_ventas == 0) {
				return ['success' => false, 'message' => 'No hay ventas en el período seleccionado'];
			}
			
			// Verificar si ya existe un cierre para este período exacto
			$cierreExistente = $this->db->from($this->table)
				->where('tipo_periodo', $tipoPeriodo)
				->where('fecha_inicio', $preview->fecha_inicio)
				->where('fecha_fin', $preview->fecha_fin)
				->get()
				->row();
			
			if ($cierreExistente) {
				return [
					'success' => false, 
					'message' => 'Ya existe un cierre para este período: ' . $cierreExistente->codigo_cierre
				];
			}
			
			$efectivoInicial = (float)($datosExtra['efectivo_inicial'] ?? 0);
			$efectivoContado = isset($datosExtra['efectivo_contado']) ? (float)$datosExtra['efectivo_contado'] : null;
			$efectivoEsperado = $efectivoInicial + $preview->total_efectivo;
			$diferencia = $efectivoContado !== null ? ($efectivoContado - $efectivoEsperado) : null;
			
			$cierreData = [
				'codigo_cierre' => $this->generarCodigoCierre(),
				'tipo_periodo' => $tipoPeriodo,
				'fecha_inicio' => $preview->fecha_inicio,
				'fecha_fin' => $preview->fecha_fin,
				'total_ventas' => $preview->total_ventas,
				'monto_total_vendido' => $preview->monto_total_vendido,
				'monto_subtotal' => $preview->monto_subtotal,
				'monto_impuestos' => $preview->monto_impuestos,
				'monto_descuentos' => $preview->monto_descuentos,
				'total_efectivo' => $preview->total_efectivo,
				'total_tarjeta_credito' => $preview->total_tarjeta_credito,
				'total_tarjeta_debito' => $preview->total_tarjeta_debito,
				'total_transferencia' => $preview->total_transferencia,
				'total_cheque' => $preview->total_cheque,
				'ventas_anuladas' => $preview->ventas_anuladas,
				'monto_anulado' => $preview->monto_anulado,
				'efectivo_inicial' => $efectivoInicial,
				'efectivo_esperado' => $efectivoEsperado,
				'efectivo_contado' => $efectivoContado,
				'diferencia_caja' => $diferencia,
				'total_facturas' => $preview->total_facturas,
				'observaciones' => $datosExtra['observaciones'] ?? null,
				'cerrado_por' => $creadoPor,
				'creado_por' => $creadoPor
			];
			
			$this->db->insert($this->table, $cierreData);
			$idCierre = $this->db->insert_id();
			
			if (!$idCierre) {
				$this->db->trans_rollback();
				return ['success' => false, 'message' => 'Error al crear el cierre de caja'];
			}
			
			// Guardar detalle de ventas incluidas
			foreach ($preview->ventas as $venta) {
				$this->db->insert($this->tableDetalle, [
					'id_cierre' => $idCierre,
					'id_venta' => $venta->id,
					'folio_venta' => $venta->folio_factura,
					'total_venta' => $venta->total_final,
					'total_pagado' => $venta->total_pagado,
					'metodo_pago_principal' => $venta->metodos_pago
				]);
			}
			
			$this->db->trans_complete();
			
			if ($this->db->trans_status() === FALSE) {
				return ['success' => false, 'message' => 'Error en la transacción'];
			}
			
			return [
				'success' => true,
				'message' => 'Cierre de caja realizado correctamente',
				'id' => $idCierre,
				'codigo' => $cierreData['codigo_cierre']
			];
			
		} catch (Exception $e) {
			$this->db->trans_rollback();
			log_message('error', 'Error en cierre de caja: ' . $e->getMessage());
			return ['success' => false, 'message' => 'Error al procesar el cierre: ' . $e->getMessage()];
		}
	}
	
	/**
	 * Obtener cierre completo por ID
	 * 
	 * @param int $id
	 * @return object|null
	 */
	public function obtenerCierreCompleto($id) {
		$cierre = $this->db->from($this->table)
			->where('id', $id)
			->get()
			->row();
		
		if (!$cierre) {
			return null;
		}
		
		// Obtener detalle de ventas
		$cierre->ventas = $this->db->select('cd.*, v.fecha_venta, 
				c.nombre_completo AS cliente_nombre')
			->from("{$this->tableDetalle} cd")
			->join('ventas v', 'cd.id_venta = v.id', 'left')
			->join('clientes c', 'v.id_cliente = c.id', 'left')
			->where('cd.id_cierre', $id)
			->order_by('v.fecha_venta', 'ASC')
			->get()
			->result();
		
		return $cierre;
	}
	
	/**
	 * Listar cierres con filtros
	 * 
	 * @param array $filtros
	 * @return array
	 */
	public function listarCierres($filtros = []) {
		$this->db->from($this->table);
		
		if (!empty($filtros['tipo_periodo'])) {
			$this->db->where('tipo_periodo', $filtros['tipo_periodo']);
		}
		if (!empty($filtros['fecha_desde'])) {
			$this->db->where('DATE(fecha_inicio) >=', $filtros['fecha_desde']);
		}
		if (!empty($filtros['fecha_hasta'])) {
			$this->db->where('DATE(fecha_fin) <=', $filtros['fecha_hasta']);
		}
		if (!empty($filtros['codigo'])) {
			$this->db->like('codigo_cierre', $filtros['codigo']);
		}
		
		$this->db->order_by('fec_creacion', 'DESC');
		
		return $this->db->get()->result();
	}
	
	/**
	 * Tipos de período disponibles con etiquetas
	 * 
	 * @return array
	 */
	public static function obtenerTiposPeriodo() {
		return [
			self::PERIODO_DIA => 'Día',
			self::PERIODO_SEMANA => 'Semana',
			self::PERIODO_MES => 'Mes',
			self::PERIODO_ANIO => 'Año'
		];
	}
}
