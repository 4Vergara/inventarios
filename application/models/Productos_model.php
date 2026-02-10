<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Productos_model
 * 
 * Modelo para la gestión de productos
 */
class Productos_model extends MY_Model {
	
	public $table = 'productos';
	public $table_id = 'id';
	
	public function __construct() {
		parent::__construct();
	}
	
	/**
	 * Guardar un nuevo producto
	 * @param array $data
	 * @return int|false
	 */
	public function save($data) {
		$this->db->insert($this->table, $data);
		return $this->db->insert_id();
	}
	
	/**
	 * Actualizar un producto existente
	 * @param int $id
	 * @param array $data
	 * @return bool
	 */
	public function update($id, $data) {
		return $this->db->where($this->table_id, $id)
			->update($this->table, $data);
	}
	
	/**
	 * Eliminar un producto
	 * @param int $id
	 * @return bool
	 */
	public function delete($id) {
		return $this->db->where($this->table_id, $id)
			->delete($this->table);
	}
	
	/**
	 * Buscar productos con filtros
	 * @param array $filtros
	 * @return array
	 */
	public function buscar($filtros = []) {
		$this->db->from($this->table);
		
		if (!empty($filtros['categoria'])) {
			$this->db->where('id_categoria', $filtros['categoria']);
		}
		
		if (!empty($filtros['estado'])) {
			$this->db->where('estado', $filtros['estado']);
		}
		
		if (!empty($filtros['marca'])) {
			$this->db->like('marca', $filtros['marca']);
		}
		
		if (!empty($filtros['nombre'])) {
			$this->db->like('nombre', $filtros['nombre']);
		}
		
		if (!empty($filtros['stock_bajo'])) {
			$this->db->where('stock_actual <= stock_minimo');
		}
		
		if (!empty($filtros['perecedero'])) {
			$this->db->where('es_perecedero', 1);
		}
		
		return $this->db->get()->result();
	}
	
	/**
	 * Obtener productos con stock bajo
	 * @return array
	 */
	public function productosStockBajo() {
		return $this->db->from($this->table)
			->where('stock_actual <= stock_minimo')
			->where('estado', 'activo')
			->get()
			->result();
	}
	
	/**
	 * Obtener productos por vencer
	 * @param int $dias Días hasta el vencimiento
	 * @return array
	 */
	public function productosPorVencer($dias = 30) {
		$fechaLimite = date('Y-m-d', strtotime("+{$dias} days"));
		
		return $this->db->from($this->table)
			->where('es_perecedero', 1)
			->where('fecha_vencimiento IS NOT NULL')
			->where('fecha_vencimiento <=', $fechaLimite)
			->where('fecha_vencimiento >=', date('Y-m-d'))
			->where('estado', 'activo')
			->order_by('fecha_vencimiento', 'ASC')
			->get()
			->result();
	}
	
	/**
	 * Obtener estadísticas de productos
	 * @return object
	 */
	public function obtenerEstadisticas() {
		$stats = new stdClass();
		
		// Total de productos
		$stats->total = $this->db->count_all($this->table);
		
		// Productos activos
		$stats->activos = $this->db->from($this->table)
			->where('estado', 'activo')
			->count_all_results();
		
		// Productos con stock bajo
		$stats->stock_bajo = $this->db->from($this->table)
			->where('stock_actual <= stock_minimo')
			->where('estado', 'activo')
			->count_all_results();
		
		// Valor total del inventario
		$result = $this->db->select('SUM(precio_costo * stock_actual) as valor_costo, SUM(precio_venta * stock_actual) as valor_venta')
			->from($this->table)
			->where('estado', 'activo')
			->get()
			->row();
		
		$stats->valor_costo = $result->valor_costo ?: 0;
		$stats->valor_venta = $result->valor_venta ?: 0;
		
		return $stats;
	}
	
	/**
	 * Verificar si el SKU ya existe
	 * @param string $sku
	 * @param int $excludeId Excluir este ID (para edición)
	 * @return bool
	 */
	public function skuExiste($sku, $excludeId = null) {
		$this->db->from($this->table)
			->where('sku', $sku);
		
		if ($excludeId) {
			$this->db->where('id !=', $excludeId);
		}
		
		return $this->db->count_all_results() > 0;
	}
	
	/**
	 * Actualizar stock de un producto
	 * @param int $id
	 * @param int $cantidad Cantidad a agregar (negativa para restar)
	 * @return bool
	 */
	public function actualizarStock($id, $cantidad) {
		$producto = $this->find($id);
		if (!$producto) {
			return false;
		}
		
		$nuevoStock = $producto->stock_actual + $cantidad;
		if ($nuevoStock < 0) {
			$nuevoStock = 0;
		}
		
		return $this->update($id, ['stock_actual' => $nuevoStock]);
	}

    /**
     * ? Traer un producto por su ID con detalles adicionales
     * @param int $id
     * @return array
     */
    public function obtenerPorIdConDetalles($id) {
        return $this->db->select('p.*,
            um.nombre AS unidad_medida,
            t.nombre AS talla,
            temp.nombre AS temperatura_conservacion,
            g.nombre AS genero,
            v.nombre AS voltaje')
            ->from("$this->table AS p")
            ->join('configuraciones AS um', 'p.id_unidad_medida = um.id', 'left')
            ->join('configuraciones AS t', 'p.id_talla = t.id', 'left')
            ->join('configuraciones AS temp', 'p.id_temperatura_conservacion = temp.id', 'left')
            ->join('configuraciones AS g', 'p.id_genero = g.id', 'left')
            ->join('configuraciones AS v', 'p.id_voltaje = v.id', 'left')
            ->where('p.id', $id)
            ->get()
            ->row();
    }
	
	/**
	 * Contar total de productos
	 * 
	 * @return int
	 */
	public function contar() {
		return $this->db->from($this->table)->count_all_results();
	}
}
