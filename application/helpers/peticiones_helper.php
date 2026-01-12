<?php
defined('BASEPATH') or exit('No direct script access allowed');
function peticionesPost($url, $params, $headers = []) {
	$curl = curl_init($url);
	if (! empty($headers) && is_array($headers)) {
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
	}
	curl_setopt($curl, CURLOPT_HEADER, false);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_POST, true);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
	if (ENVIRONMENT !== 'production') {
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	}
	$json_response = curl_exec($curl);
	$status        = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	$response      = [];
	if ($status != 200) {
		$response['error']['1'] = "Error: URL $url failed with status $status";
		$response['error']['2'] = "Response $json_response, curl_error " . curl_error($curl) . ', curl_errno ' . curl_errno($curl);
	} else {
		$response = json_decode($json_response, true);
	}
	curl_close($curl);
	return $response;
}
function peticionesGet($url, $params = null, $headers = []) {
	if (! empty($params) && is_array($params)) {
		$params = http_build_query($params);
		if (strpos($url, '?') === false) {
			$url .= '?' . $params;
		} else {
			$url . $params;
		}
	} elseif (! empty($params) && is_string($params)) {
		if (strpos($url, '?') === false) {
			$url .= '?' . $params;
		} else {
			$url .= $params;
		}
	}
	$curl = curl_init($url);
	if (! empty($headers) && is_array($headers)) {
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
	}
	curl_setopt($curl, CURLOPT_HEADER, false);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	if (ENVIRONMENT !== 'production') {
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	}
	$json_response = curl_exec($curl);
	$status        = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	$response      = [];
	if ($status != 200) {
		$response['error']['1'] = "Error: URL $url failed with status $status";
		$response['error']['2'] = "Response $json_response, curl_error " . curl_error($curl) . ', curl_errno ' . curl_errno($curl);
	} else {
		$response = json_decode($json_response, true);
	}
	curl_close($curl);
	return $response;
}
function serviceping($url, $timeout = 1, $code = false) {
	$curl = curl_init($url);
	// Establecer opciones cURL para un ping HEAD
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	if (ENVIRONMENT !== 'production') {
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	}
	curl_setopt($curl, CURLOPT_NOBODY, true); // Esto convierte la solicitud en un ping de tipo HEAD
	curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $timeout);
	curl_setopt($curl, CURLOPT_TIMEOUT, $timeout); // Tiempo máximo para la operación total de cURL
	                                               // Ejecutar la solicitud cURL
	curl_exec($curl);
	$errno     = curl_errno($curl);
	$http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	curl_close($curl);
	// console('error->'.$errno, 'codigo=>'.$http_code);
	if ($errno === 0) {
		if ($code) {
			return $http_code;
		}
		// Verificar el código de respuesta HTTP
		if ($http_code >= 200 && $http_code < 400) {
			// La página existe y está habilitada
			return true;
		}
	}
	return false;
}
function enviocorreo($parametros) {
	$return = (object) [];
	if (! empty($parametros) && is_array($parametros)) {
		$parametros = (object) $parametros;
	}
	if (! empty($parametros->email)) {
		$return->email = $parametros->email;
		$url           = 'https://sendmail-apps-329425773532.us-central1.run.app';
		$token         = '49fce305f98ee39bbb89c3da8c6e94d1';
		try {
			$params = (object) [
				'destinatario'  => $parametros->destinatario,
				'asunto'        => empty($parametros->asunto) ? 'Consejo Colombiano de Seguridad' : $parametros->asunto,
				'cuerpo_correo' => base64_encode($parametros->cuerpo_correo),
				'from'          => empty($parametros->from) ? 'Consejo Colombiano de Seguridad <aplicaciones@ccs.org.co>' : $parametros->from,
			];
			if (! empty($parametros->bcc)) {
				if (is_array($parametros->bcc)) {
					$parametros->bcc = implode(',', $parametros->bcc);
				}
				$params->bcc = $parametros->bcc;
			}
			$headers = [
				'Authorization: Bearer ' . $token,
				'Content-Type: application/json; charset=utf-8',
			];
			$response = (object) peticionesPost($url, (array) $params, $headers);
			if (! empty($response->success)) {
				$return          = $response;
				$return->success = 1;
			}
			if (isset($response->error)) {
				$return->error = "No se pudo enviar el mensaje. Error de envío: {$response->error}";
				console('No se envio el correo', $return);
			}
		} catch (Exception $e) {
			$return->error = print_r($e, 1);
			console('Exception de envio de correo', $return);
		}
	}
	return $return;
}