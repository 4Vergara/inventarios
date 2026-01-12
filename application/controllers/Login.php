<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Login extends CI_Controller {
	public function __construct() {
		parent::__construct();
	}
	public function index() {
		$this->view('login');
	}
	public function ingresar() {
		if (! empty($this->formData->usuario) && ! empty($this->formData->password)) {
			if (! empty($data['data']) && $data['success'] && empty($data['error'])) {
				$datos = (object) $data['data'];
				if ($datos->usuario == $this->formData->usuario && $datos->mail && $datos->tiempo) {
					$datos->token = hash('sha256', $datos->token);
					$data         = [
						'mail'   => $datos->mail,
						'token'  => $datos->token,
						'roles'  => ! empty($datos->roles) ? implode(',', $datos->roles) : '',
						'tiempo' => $datos->tiempo,
						'nombre' => $datos->displayname,
					];
					// consultar si existe
					$user = $this->lg->findName(['usuario' => $this->formData->usuario]);
					if (! empty($user->id) && $user->usuario) {
						$data['update_at'] = date('Y-m-d H:i:s');
						$data['id']        = $user->id;
					} else {
						$data['created_at'] = date('Y-m-d H:i:s');
						$data['usuario']    = $this->formData->usuario;
						// $data['id'] = $this->lg->insert($data);
					}
					$data['usuario']             = $this->formData->usuario;
					$this->session->datosusuario = (object) $data;
					$this->reques->url           = 'home';
					$this->reques->token         = $datos->token;
					$this->reques->toas          = 'Ingreso exitoso, bienvenido ' . $datos->displayname;
				} else {
					$this->iffalse('Errores al ingresar, usuario o contraseña incorrectos.');
				}
			} else {
				$this->errores($data);
			}
		} else {
			$this->iffalse('El formulario no es válido.');
		}
		$this->json();
	}
	public function salir() {
		$this->session->datosusuario = null;
		redirect(IP_SERVER . 'login');
	}
}
