<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Login extends CI_Controller {
	public function __construct() {
		parent::__construct();

		$this->load->model('Usuarios_model');
	}
	public function index() {
		$this->view('login');
	}

	/**
	 * ? Funcion para ingresar al sistema
	 * @return void
	 */
	public function ingresar() {
		if (!empty($this->formData->correo) && ! empty($this->formData->contrasena)) {

			//? Encriptar la contraseña
			$contrasena_encrypted = hash('sha256', $this->formData->contrasena . KEY_ALGO);
			$usuario = $this->Usuarios_model->usuario_correo($this->formData->correo);

			//? Validar si el usuario existe
			if(isset($usuario)){
				//? Validar la contraseña
				if ($usuario->contrasena === $contrasena_encrypted) {
					//? Crear la sesion del usuario
					$this->session->datosusuario = $usuario;
					$this->json([
						'resp' => 1, 
						'msg' => 'Ingreso exitoso.'
					]);
					return;
				} else {
					$this->json([
						'resp' => 0, 
						'msg' => 'Contraseña incorrecta.'
					]);
					return;
				}
			}else{
				$this->json([
					'resp' => 0, 
					'msg' => 'El usuario no existe.'
				]);
				return;
			}
		} else {
			$this->json([
				'resp' => 0, 
				'msg' => 'Debe completar todos los campos obligatorios.'
			]);
			return;
		}
	}
	public function salir() {
		$this->session->datosusuario = null;
		redirect(IP_SERVER . 'login');
	}
}
