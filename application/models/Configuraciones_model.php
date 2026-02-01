<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Configuraciones_model extends MY_Model {
	
	public $table = "configuraciones";
	public $table_id = "id";
	
	public function __construct() {
		parent::__construct();
	}
	
	/**
	 * Obtener categorías organizadas jerárquicamente para productos
	 * Retorna categorías principales con sus subcategorías
	 * @return array
	 */
	public function obtenerCategoriasProductos() {
		// Obtener el nodo raíz de categorías (valor = 'CAT_ROOT')
		$raiz = $this->db->from($this->table)
			->where('valor', 'CAT_ROOT')
			->get()
			->row();
		
		if (!$raiz) {
			return [];
		}
		
		// Obtener categorías principales (hijas del nodo raíz)
		$categoriasPrincipales = $this->db->from($this->table)
			->where('id_padre', $raiz->id)
			->order_by('nombre', 'ASC')
			->get()
			->result();
		
		$resultado = [];
		
		foreach ($categoriasPrincipales as $categoria) {
			// Obtener subcategorías de cada categoría principal
			$subcategorias = $this->db->from($this->table)
				->where('id_padre', $categoria->id)
				->order_by('nombre', 'ASC')
				->get()
				->result();
			
			$resultado[] = [
				'categoria' => $categoria,
				'subcategorias' => $subcategorias
			];
		}
		
		return $resultado;
	}
	
	/**
	 * Obtener configuraciones por tipo/valor padre
	 * @param string $valorPadre El valor del nodo padre
	 * @return array
	 */
	public function obtenerPorValorPadre($valorPadre) {
		$padre = $this->db->from($this->table)
			->where('valor', $valorPadre)
			->get()
			->row();
		
		if (!$padre) {
			return [];
		}
		
		return $this->db->from($this->table)
			->where('id_padre', $padre->id)
			->order_by('nombre', 'ASC')
			->get()
			->result();
	}
	
	/**
	 * Obtener hijos directos de una configuración
	 * @param int $idPadre
	 * @return array
	 */
	public function obtenerHijos($idPadre) {
		return $this->db->from($this->table)
			->where('id_padre', $idPadre)
			->order_by('nombre', 'ASC')
			->get()
			->result();
	}
}
