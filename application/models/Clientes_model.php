<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Clientes_model
 * 
 * Modelo para la gestión de clientes
 */
class Clientes_model extends MY_Model {
	
	public $table = 'clientes';
	public $table_id = 'id';
	
	public function __construct() {
		parent::__construct();
	}
	
	/**
	 * Guardar nuevo cliente
	 * 
	 * @param array $data
	 * @return int|false
	 */
	public function save($data) {
		$this->db->insert($this->table, $data);
		return $this->db->insert_id();
	}
	
	/**
	 * Actualizar cliente
	 * 
	 * @param int $id
	 * @param array $data
	 * @return bool
	 */
	public function update($id, $data) {
		return $this->db->where($this->table_id, $id)
			->update($this->table, $data);
	}
	
	/**
	 * Buscar cliente por documento
	 * 
	 * @param string $documento
	 * @return object|null
	 */
	public function buscarPorDocumento($documento) {
		return $this->db->from($this->table)
			->where('numero_documento', $documento)
			->get()
			->row();
	}
	
	/**
	 * Buscar clientes con filtro
	 * 
	 * @param string $termino
	 * @return array
	 */
	public function buscar($termino) {
		return $this->db->from($this->table)
			->group_start()
				->like('nombre_completo', $termino)
				->or_like('numero_documento', $termino)
				->or_like('correo_electronico', $termino)
			->group_end()
			->limit(20)
			->get()
			->result();
	}
	
	/**
	 * Listar todos los clientes
	 * 
	 * @return array
	 */
	public function listar() {
		return $this->db->from($this->table)
			->order_by('nombre_completo', 'ASC')
			->get()
			->result();
	}
	
	/**
	 * Verificar si documento ya existe
	 * 
	 * @param string $documento
	 * @param int $excludeId
	 * @return bool
	 */
	public function documentoExiste($documento, $excludeId = null) {
		$this->db->from($this->table)
			->where('numero_documento', $documento);
		
		if ($excludeId) {
			$this->db->where('id !=', $excludeId);
		}
		
		return $this->db->count_all_results() > 0;
	}
	
	/**
	 * Obtener estadísticas de cliente
	 * 
	 * @param int $idCliente
	 * @return object
	 */
	public function obtenerEstadisticas($idCliente) {
		$stats = new stdClass();
		
		// Total de compras
		$result = $this->db->select('COUNT(*) as total_compras, COALESCE(SUM(total_final), 0) as monto_total')
			->from('ventas')
			->where('id_cliente', $idCliente)
			->get()
			->row();
		
		$stats->total_compras = $result->total_compras;
		$stats->monto_total = $result->monto_total;
		
		// Última compra
		$ultima = $this->db->select('fecha_venta')
			->from('ventas')
			->where('id_cliente', $idCliente)
			->order_by('fecha_venta', 'DESC')
			->limit(1)
			->get()
			->row();
		
		$stats->ultima_compra = $ultima ? $ultima->fecha_venta : null;
		
		return $stats;
	}
	
	/**
	 * Listar clientes con estadísticas de compras
	 * 
	 * @return array
	 */
	public function listarConEstadisticas() {
		return $this->db->select('c.id,
				c.nombre_completo,
				c.numero_documento,
				c.correo_electronico,
				c.fec_creacion,
				(SELECT COUNT(*) FROM ventas v WHERE v.id_cliente = c.id) as total_compras,
				(SELECT COALESCE(SUM(v2.total_final), 0) FROM ventas v2 WHERE v2.id_cliente = c.id) as monto_total,
				(SELECT MAX(v3.fecha_venta) FROM ventas v3 WHERE v3.id_cliente = c.id) as ultima_compra')
			->from($this->table . ' c')
			->order_by('c.nombre_completo', 'ASC')
			->get()
			->result();
	}
	
	/**
	 * Obtener historial de compras de un cliente
	 * 
	 * @param int $idCliente
	 * @return array
	 */
	public function obtenerHistorialCompras($idCliente) {
		return $this->db->select('v.id,
				v.folio_factura,
				v.fecha_venta,
				v.subtotal,
				v.total_impuestos,
				v.total_descuentos,
				v.total_final,
				(SELECT COALESCE(SUM(p.monto), 0) FROM pagos p WHERE p.id_venta = v.id) as total_pagado,
				(v.total_final - (SELECT COALESCE(SUM(p2.monto), 0) FROM pagos p2 WHERE p2.id_venta = v.id)) as saldo_pendiente,
				u.nombre_completo as vendedor_nombre,
				(SELECT COUNT(*) FROM ventas_detalle vd WHERE vd.id_venta = v.id) as total_productos')
			->from('ventas v')
			->join('usuarios u', 'u.id_usuario = v.id_vendedor', 'left')
			->where('v.id_cliente', $idCliente)
			->order_by('v.fecha_venta', 'DESC')
			->get()
			->result();
	}
}
