<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once 'application/third_party/Autoloader.php';
require_once 'application/third_party/psr/Autoloader.php';

/**
 * Home Controller
 * 
 * Controlador principal del Dashboard
 * Muestra resumen general del sistema
 */
class Home extends CI_Controller {
	
	public function __construct() {
		parent::__construct();
	}
	
	/**
	 * Dashboard principal
	 * Muestra resumen de productos, clientes y ventas si hay sesión activa
	 */
	public function index() {
		$data = [];
		
		// Obtener datos de resumen solo si hay sesión activa
		if (isset($this->session->datosusuario) && $this->session->datosusuario) {
			$this->load->model('Productos_model');
			$this->load->model('Clientes_model');
			$this->load->model('Ventas_model');
			
			$data['resumen'] = [
				'total_productos' => $this->Productos_model->contar(),
				'total_clientes' => $this->Clientes_model->contar(),
				'total_ventas' => $this->Ventas_model->contar()
			];
		}
		
		$this->load->view('layouts/header');
		$this->load->view('home', $data);
		$this->load->view('layouts/footer');
	}
}
