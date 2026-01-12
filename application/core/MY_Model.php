<?php
/**
 * MY_Model
 *
 * @package
 * @author gfabio
 * @copyright gfabio
 * @version 2020
 * @access public
 */
class MY_Model extends CI_Model {
	/**
	 * MY_Model::__construct()
	 *
	 * @return
	 */
	public function __construct() {
		parent::__construct();
		$this->load->database();
	}
	/**
	 * MY_Model::findAll()
	 *
	 * @param string $where
	 * @param string $select
	 * @return objet
	 */
	public function findAll($where = '', $select = '') {
		if ($select) {
			$this->db->select($select, false);
		}
		if ($where) {
			$this->db->where(is_array($where) ? $where : [$this->table_id => $where]);
		}
		$rows = $this->db->from($this->table)->get();
		return $rows->result();
	}
	/**
	 * MY_Model::find()
	 *
	 * @param mixed $id
	 * @param string $select
	 * @return objet
	 */
	public function find($id, $select = '') {
		if ($select) {
			$this->db->select($select, false);
		}
		$rows = $this->db->from($this->table)->where((is_array($id) ? $id : [$this->table_id => $id]))->get();
		return $rows->row();
	}
	/**
	 * MY_Model::findName()
	 *
	 * @param mixed $name
	 * @param mixed $id
	 * @param string $select
	 * @return objet
	 */
	public function findName($name, $id = '', $select = '') {
		if ($select) {
			$this->db->select($select, false);
		}
		$rows = $this->db->from($this->table)->where((is_array($name) ? $name : [$name => $id]))->get();
		return $rows->row();
	}
	public function whereIn($key, $values, $select = '') {
		if ($select) {
			$this->db->select($select, false);
		}
		$this->db->where_in($key, $values);
		$rows = $this->db->from($this->table)->get();
		return $rows->result();
	}
	/**
	 * MY_Model::findIn()
	 *
	 * @param	array	$keys	The field to search
	 * @param	array	$values	The values searched on
	 * @param	bool	$escape
	 * @return	CI_DB_query_builder
	 */
	public function findIn($array, $select = '') {
		if ($select) {
			$this->db->select($select, false);
		}
		foreach ($array as $key => $value) {
			$this->db->where_in($key, $value);
		}
		$rows = $this->db->from($this->table)->get();
		return $rows->result();
	}
	/**
	 * MY_Model::update()
	 *
	 * @param mixed $id
	 * @param mixed $data
	 * @return void
	 */
	public function update($id, $data) {
		$this->db->where((is_array($id) ? $id : [$this->table_id => $id]))->update($this->table, $data);
	}
	/**
	 * MY_Model::delete()
	 *
	 * @param mixed $id
	 * @return void
	 */
	public function delete($id) {
		$this->db->where((is_array($id) ? $id : [$this->table_id => $id]));
		$this->db->delete($this->table);
	}
	/**
	 * MY_Model::insert()
	 *
	 * @param mixed $data
	 * @return id
	 */
	public function insert($data) {
		$this->db->insert($this->table, $data);
		return $this->db->insert_id();
	}
	/**
	 * MY_Model::count()
	 *
	 * @param string $where
	 * @return int
	 */
	public function count($where = '') {
		if ($where && is_array($where)) {
			$this->db->where($where);
		}
		$this->db->select("COUNT($this->table_id) count", false);
		$count = $this->db->get($this->table)->row();
		// console($this->db->last_query());
		return $count->count;
	}
	/**
	 * MY_Model::database()
	 *
	 * @param string $db
	 * @param string $otradb
	 * @return void
	 */
	public function database($db = '', $otradb = '') {
		if ($db) {
			$this->db->db_select($db);
		} else {
			$this->load->database($otradb);
		}
	}
}
