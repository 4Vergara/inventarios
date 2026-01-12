<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once 'application/third_party/Autoloader.php';
require_once 'application/third_party/psr/Autoloader.php';
class Home extends CI_Controller {
	public function __construct() {
		parent::__construct();
		// if (!$this->validatoken()) {
		//     $this->iffalse('Acceso denegado');
		//     $this->json();
		//     die();
		// }
	}
	public function index() {
		$this->vista('home');
	}
	public function base() {
		$this->vista('base');
	}
}
