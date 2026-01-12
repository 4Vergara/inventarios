<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once 'application/third_party/Autoloader.php';
require_once 'application/third_party/psr/Autoloader.php';
class Home extends CI_Controller {
	public function __construct() {
		parent::__construct();
	}
	public function index() {
		$this->view('login');
	}
}
