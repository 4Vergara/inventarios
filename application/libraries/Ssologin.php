<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Ssologin {
	public function ingresar($user, $pw) {
		$data = [
			'password'   => codificar(codificarssl($pw, KEY_SSOLOGIN)),
			'username'   => $user,
			'aplicacion' => 0,
		];
		$head = [
			'Content-Type: application/json; charset=utf-8',
		];
		$params = json_encode(['datos' => codificar($data)]);
		return peticionesPost(SSOLOGIN . 'datos', $params, $head);
	}
}
