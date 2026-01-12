<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2019 - 2022, CodeIgniter Foundation
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package	CodeIgniter
 * @author	EllisLab Dev Team
 * @copyright	Copyright (c) 2008 - 2014, EllisLab, Inc. (https://ellislab.com/)
 * @copyright	Copyright (c) 2014 - 2019, British Columbia Institute of Technology (https://bcit.ca/)
 * @copyright	Copyright (c) 2019 - 2022, CodeIgniter Foundation (https://codeigniter.com/)
 * @license	https://opensource.org/licenses/MIT	MIT License
 * @link	https://codeigniter.com
 * @since	Version 1.0.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * CodeIgniter URL Helpers
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		EllisLab Dev Team
 * @link		https://codeigniter.com/userguide3/helpers/url_helper.html
 */
// ------------------------------------------------------------------------
if (!function_exists('site_url')) {
	/**
	 * Site URL
	 *
	 * Create a local URL based on your basepath. Segments can be passed via the
	 * first parameter either as a string or an array.
	 *
	 * @param	string	$uri
	 * @param	string	$protocol
	 * @return	string
	 */
	function site_url($uri = '', $protocol = NULL) {
		return get_instance()->config->site_url($uri, $protocol);
	}
}
// ------------------------------------------------------------------------
if (!function_exists('base_url')) {
	/**
	 * Base URL
	 *
	 * Create a local URL based on your basepath.
	 * Segments can be passed in as a string or an array, same as site_url
	 * or a URL to a file can be passed in, e.g. to an image file.
	 *
	 * @param	string	$uri
	 * @param	string	$protocol
	 * @return	string
	 */
	function base_url($uri = '', $protocol = NULL) {
		return get_instance()->config->base_url($uri, $protocol);
	}
}
// ------------------------------------------------------------------------
if (!function_exists('current_url')) {
	/**
	 * Current URL
	 *
	 * Returns the full URL (including segments) of the page where this
	 * function is placed
	 *
	 * @return	string
	 */
	function current_url() {
		$CI = &get_instance();
		return $CI->config->site_url($CI->uri->uri_string());
	}
}
// ------------------------------------------------------------------------
if (!function_exists('uri_string')) {
	/**
	 * URL String
	 *
	 * Returns the URI segments.
	 *
	 * @return	string
	 */
	function uri_string() {
		return get_instance()->uri->uri_string();
	}
}
// ------------------------------------------------------------------------
if (!function_exists('index_page')) {
	/**
	 * Index page
	 *
	 * Returns the "index_page" from your config file
	 *
	 * @return	string
	 */
	function index_page() {
		return get_instance()->config->item('index_page');
	}
}
// ------------------------------------------------------------------------
if (!function_exists('anchor')) {
	/**
	 * Anchor Link
	 *
	 * Creates an anchor based on the local URL.
	 *
	 * @param	string	the URL
	 * @param	string	the link title
	 * @param	mixed	any attributes
	 * @return	string
	 */
	function anchor($uri = '', $title = '', $attributes = '') {
		$title = (string) $title;
		$site_url = is_array($uri)
		? site_url($uri)
		: (preg_match('#^(\w+:)?//#i', $uri) ? $uri : site_url($uri));
		if ($title === '') {
			$title = $site_url;
		}
		if ($attributes !== '') {
			$attributes = _stringify_attributes($attributes);
		}
		return '<a href="' . $site_url . '"' . $attributes . '>' . $title . '</a>';
	}
}
// ------------------------------------------------------------------------
if (!function_exists('anchor_popup')) {
	/**
	 * Anchor Link - Pop-up version
	 *
	 * Creates an anchor based on the local URL. The link
	 * opens a new window based on the attributes specified.
	 *
	 * @param	string	the URL
	 * @param	string	the link title
	 * @param	mixed	any attributes
	 * @return	string
	 */
	function anchor_popup($uri = '', $title = '', $attributes = FALSE) {
		$title = (string) $title;
		$site_url = preg_match('#^(\w+:)?//#i', $uri) ? $uri : site_url($uri);
		if ($title === '') {
			$title = $site_url;
		}
		if ($attributes === FALSE) {
			return '<a href="' . $site_url . '" onclick="window.open(\'' . $site_url . "', '_blank'); return false;\">" . $title . '</a>';
		}
		if (!is_array($attributes)) {
			$attributes = array($attributes);
			// Ref: http://www.w3schools.com/jsref/met_win_open.asp
			$window_name = '_blank';
		} elseif (!empty($attributes['window_name'])) {
			$window_name = $attributes['window_name'];
			unset($attributes['window_name']);
		} else {
			$window_name = '_blank';
		}
		foreach (array('width' => '800', 'height' => '600', 'scrollbars' => 'yes', 'menubar' => 'no', 'status' => 'yes', 'resizable' => 'yes', 'screenx' => '0', 'screeny' => '0') as $key => $val) {
			$atts[$key] = isset($attributes[$key]) ? $attributes[$key] : $val;
			unset($attributes[$key]);
		}
		$attributes = _stringify_attributes($attributes);
		return '<a href="' . $site_url
		. '" onclick="window.open(\'' . $site_url . "', '" . $window_name . "', '" . _stringify_attributes($atts, TRUE) . "'); return false;\""
			. $attributes . '>' . $title . '</a>';
	}
}
// ------------------------------------------------------------------------
if (!function_exists('mailto')) {
	/**
	 * Mailto Link
	 *
	 * @param	string	the email address
	 * @param	string	the link title
	 * @param	mixed	any attributes
	 * @return	string
	 */
	function mailto($email, $title = '', $attributes = '') {
		$title = (string) $title;
		if ($title === '') {
			$title = $email;
		}
		return '<a href="mailto:' . $email . '"' . _stringify_attributes($attributes) . '>' . $title . '</a>';
	}
}
// ------------------------------------------------------------------------
if (!function_exists('safe_mailto')) {
	/**
	 * Encoded Mailto Link
	 *
	 * Create a spam-protected mailto link written in Javascript
	 *
	 * @param	string	the email address
	 * @param	string	the link title
	 * @param	mixed	any attributes
	 * @return	string
	 */
	function safe_mailto($email, $title = '', $attributes = '') {
		$title = (string) $title;
		if ($title === '') {
			$title = $email;
		}
		$x = str_split('<a href="mailto:', 1);
		for ($i = 0, $l = strlen($email); $i < $l; $i++) {
			$x[] = '|' . ord($email[$i]);
		}
		$x[] = '"';
		if ($attributes !== '') {
			if (is_array($attributes)) {
				foreach ($attributes as $key => $val) {
					$x[] = ' ' . $key . '="';
					for ($i = 0, $l = strlen($val); $i < $l; $i++) {
						$x[] = '|' . ord($val[$i]);
					}
					$x[] = '"';
				}
			} else {
				for ($i = 0, $l = strlen($attributes); $i < $l; $i++) {
					$x[] = $attributes[$i];
				}
			}
		}
		$x[] = '>';
		$temp = array();
		for ($i = 0, $l = strlen($title); $i < $l; $i++) {
			$ordinal = ord($title[$i]);
			if ($ordinal < 128) {
				$x[] = '|' . $ordinal;
			} else {
				if (count($temp) === 0) {
					$count = ($ordinal < 224) ? 2 : 3;
				}
				$temp[] = $ordinal;
				if (count($temp) === $count) {
					$number = ($count === 3)
					? (($temp[0] % 16) * 4096) + (($temp[1] % 64) * 64) + ($temp[2] % 64)
					: (($temp[0] % 32) * 64) + ($temp[1] % 64);
					$x[] = '|' . $number;
					$count = 1;
					$temp = array();
				}
			}
		}
		$x[] = '<';
		$x[] = '/';
		$x[] = 'a';
		$x[] = '>';
		$x = array_reverse($x);
		$output = "<script type=\"text/javascript\">\n"
			. "\t//<![CDATA[\n"
			. "\tvar l=new Array();\n";
		for ($i = 0, $c = count($x); $i < $c; $i++) {
			$output .= "\tl[" . $i . "] = '" . $x[$i] . "';\n";
		}
		$output .= "\n\tfor (var i = l.length-1; i >= 0; i=i-1) {\n"
			. "\t\tif (l[i].substring(0, 1) === '|') document.write(\"&#\"+unescape(l[i].substring(1))+\";\");\n"
			. "\t\telse document.write(unescape(l[i]));\n"
			. "\t}\n"
			. "\t//]]>\n"
			. '</script>';
		return $output;
	}
}
// ------------------------------------------------------------------------
if (!function_exists('auto_link')) {
	/**
	 * Auto-linker
	 *
	 * Automatically links URL and Email addresses.
	 * Note: There's a bit of extra code here to deal with
	 * URLs or emails that end in a period. We'll strip these
	 * off and add them after the link.
	 *
	 * @param	string	the string
	 * @param	string	the type: email, url, or both
	 * @param	bool	whether to create pop-up links
	 * @return	string
	 */
	function auto_link($str, $type = 'both', $popup = FALSE) {
		// Find and replace any URLs.
		if ($type !== 'email' && preg_match_all('#(\w*://|www\.)[a-z0-9]+(-+[a-z0-9]+)*(\.[a-z0-9]+(-+[a-z0-9]+)*)+(/([^\s()<>;]+\w)?/?)?#i', $str, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER)) {
			// Set our target HTML if using popup links.
			$target = ($popup) ? ' target="_blank" rel="noopener"' : '';
			// We process the links in reverse order (last -> first) so that
			// the returned string offsets from preg_match_all() are not
			// moved as we add more HTML.
			foreach (array_reverse($matches) as $match) {
				// $match[0] is the matched string/link
				// $match[1] is either a protocol prefix or 'www.'
				//
				// With PREG_OFFSET_CAPTURE, both of the above is an array,
				// where the actual value is held in [0] and its offset at the [1] index.
				$a = '<a href="' . (strpos($match[1][0], '/') ? '' : 'http://') . $match[0][0] . '"' . $target . '>' . $match[0][0] . '</a>';
				$str = substr_replace($str, $a, $match[0][1], strlen($match[0][0]));
			}
		}
		// Find and replace any emails.
		if ($type !== 'url' && preg_match_all('#([\w\.\-\+]+@[a-z0-9\-]+\.[a-z0-9\-\.]+[^[:punct:]\s])#i', $str, $matches, PREG_OFFSET_CAPTURE)) {
			foreach (array_reverse($matches[0]) as $match) {
				if (filter_var($match[0], FILTER_VALIDATE_EMAIL) !== FALSE) {
					$str = substr_replace($str, safe_mailto($match[0]), $match[1], strlen($match[0]));
				}
			}
		}
		return $str;
	}
}
// ------------------------------------------------------------------------
if (!function_exists('prep_url')) {
	/**
	 * Prep URL
	 *
	 * Simply adds the http:// part if no scheme is included
	 *
	 * @param	string	the URL
	 * @return	string
	 */
	function prep_url($str = '') {
		if ($str === 'http://' OR $str === '') {
			return '';
		}
		$url = parse_url($str);
		if (!$url OR !isset($url['scheme'])) {
			return 'http://' . $str;
		}
		return $str;
	}
}
// ------------------------------------------------------------------------
if (!function_exists('url_title')) {
	/**
	 * Create URL Title
	 *
	 * Takes a "title" string as input and creates a
	 * human-friendly URL string with a "separator" string
	 * as the word separator.
	 *
	 * @todo	Remove old 'dash' and 'underscore' usage in 3.1+.
	 * @param	string	$str		Input string
	 * @param	string	$separator	Word separator
	 *			(usually '-' or '_')
	 * @param	bool	$lowercase	Whether to transform the output string to lowercase
	 * @return	string
	 */
	function url_title($str, $separator = '-', $lowercase = FALSE) {
		if ($separator === 'dash') {
			$separator = '-';
		} elseif ($separator === 'underscore') {
			$separator = '_';
		}
		$q_separator = preg_quote($separator, '#');
		$trans = array(
			'&.+?;' => '',
			'[^\w\d _-]' => '',
			'\s+' => $separator,
			'(' . $q_separator . ')+' => $separator,
		);
		$str = strip_tags($str);
		foreach ($trans as $key => $val) {
			$str = preg_replace('#' . $key . '#i' . (UTF8_ENABLED ? 'u' : ''), $val, $str);
		}
		if ($lowercase === TRUE) {
			$str = strtolower($str);
		}
		return trim(trim($str, $separator));
	}
}
// ------------------------------------------------------------------------
if (!function_exists('redirect')) {
	/**
	 * Header Redirect
	 *
	 * Header redirect in two flavors
	 * For very fine grained control over headers, you could use the Output
	 * Library's set_header() function.
	 *
	 * @param	string	$uri	URL
	 * @param	string	$method	Redirect method
	 *			'auto', 'location' or 'refresh'
	 * @param	int	$code	HTTP Response status code
	 * @return	void
	 */
	function redirect($uri = '', $method = 'auto', $code = NULL) {
		if (!preg_match('#^(\w+:)?//#i', $uri)) {
			$uri = site_url($uri);
		}
		// IIS environment likely? Use 'refresh' for better compatibility
		if ($method === 'auto' && isset($_SERVER['SERVER_SOFTWARE']) && strpos($_SERVER['SERVER_SOFTWARE'], 'Microsoft-IIS') !== FALSE) {
			$method = 'refresh';
		} elseif ($method !== 'refresh' && (empty($code) OR !is_numeric($code))) {
			if (isset($_SERVER['SERVER_PROTOCOL'], $_SERVER['REQUEST_METHOD']) && $_SERVER['SERVER_PROTOCOL'] === 'HTTP/1.1') {
				$code = ($_SERVER['REQUEST_METHOD'] !== 'GET')
				? 303// reference: http://en.wikipedia.org/wiki/Post/Redirect/Get
				 : 307;
			} else {
				$code = 302;
			}
		}
		switch ($method) {
		case 'refresh':
			header('Refresh:0;url=' . $uri);
			break;
		default:
			header('Location: ' . $uri, TRUE, $code);
			break;
		}
		exit;
	}
}
// ! de usuario ////////////////////////////////////////////
if (!function_exists('console')) {
	/**
	 * imprime en un archivo los parametros ingresados
	 *
	 * @param mixed $level
	 * @param mixed $msg
	 * @return void
	 */
	function console($level = '', $msg = '') {
		if (empty($msg)) {
			$msg = $level;
			$level = 'nn';}
		if (is_array($level) || !is_string($level)) {
			$level = log_mesage($level);
		}
		if (is_array($msg) || !is_string($msg)) {
			$msg = log_mesage($msg);
		}
		$filepath = str_replace('\\', '/', APPPATH) . '/../log-' . date('Y-m-d') . '.php';
		$newfile = false;
		if (!file_exists($filepath)) {
			$newfile = true;
			$titulo = '<?php 	exit(\'Hey!!\') ?>';
		}
		if (!$fp = @fopen($filepath, FOPEN_WRITE_CREATE)) {
			return FALSE;
		}
		$message = "\n" . $level . ' ' . date('Y-m-d H:i:s') . ' --> ' . $msg . "\n";
		flock($fp, LOCK_EX);
		if ($newfile) {
			fwrite($fp, $titulo);
		}
		fwrite($fp, $message);
		flock($fp, LOCK_UN);
		fclose($fp);
		if ($newfile) {
			@chmod($filepath, FILE_WRITE_MODE);
		}
		return TRUE;
	}
}
if (!function_exists('log_mesage')) {
	/**
	 * CMS_Controller::log_mesage()
	 *
	 * @return
	 */
	function log_mesage($data = '') {
		$cadena = '';
		if (is_object($data)) {
			$cadena = print_r($data, 1);
		} elseif (is_array($data)) {
			foreach ($data AS $id => $val) {
				$cadena .= "\n" . '[' . $id . ']=>{';
				if (is_array($val) || is_object($val)) {
					$cadena .= log_mesage($val);
					$cadena .= '},';
				} else {
					$cadena .= $val . '}, ';
				}
			}
		} elseif (is_string($data)) {
			$cadena = $data;
		} else {
			$cadena = print_r($data, 1);
		}
		return rtrim($cadena, ', ');
	}
}
if (!function_exists('codificar')) {
	function codificar($data = '', $campos = '') {
		$t = '';
		if (!empty($data)) {
			if (is_string($data)) {
				$t = base64_encode($data);
			} elseif (is_array($data) || is_object($data)) {
				if ($campos) {
					if (strpos($campos, ',')) {
						$f = new stdClass;
						foreach (explode(',', $campos) as $campo) {
							if (isset($data->{$campo})) {
								$f->{$campo} = $data->{$campo};
							} elseif (isset($data[$campo])) {
								$f->{$campo} = $data[$campo];
							}
						}
						$t = base64_encode(json_encode($f));
					}
				} else {
					$t = base64_encode(json_encode($data));
				}
			}
			// Reemplazar caracteres
			$t = strtr($t, '+/', '-_');
		}
		return rtrim($t, '=');
	}
}
if (!function_exists('decodificar')) {
	function decodificar($data = '', $string = false) {
		$t = null;
		if (!empty($data) && is_string($data)) {
			// Reemplazar caracteres
			$data = strtr($data, '-_', '+/');
			// Agregar los caracteres '=' eliminados durante la codificación
			while (strlen($data) % 4) {
				$data .= '=';
			}
			// Decodificar base64
			$decoded = base64_decode($data);
			if ($decoded === false) {
				return '';
			}
			if ($string) {
				return $decoded;
			}
			// Decodificar JSON
			$t = json_decode($decoded); // Decodificar como array asociativo
			if (json_last_error() !== JSON_ERROR_NONE) {
				return 'Error de decodificación JSON: ' . json_last_error_msg();
			}
		}
		return $t;
	}
}
if (!function_exists('decodificarssl')) {
	function decodificarssl($data = '', $key = '') {
		$key = empty($key) ? md5(KEY_ALGO) : $key;
		$iv_num_bytes = openssl_cipher_iv_length(CIPHER_ALGO);
		$raw = $data;
		if (strlen($raw) < $iv_num_bytes) {
			return 'longitud de datos ' . strlen($raw) . " tiene menos de iv longitud {$iv_num_bytes}";
		}
		// Extraiga el vector de inicialización y los datos cifrados.
		$iv = substr($raw, 0, $iv_num_bytes);
		$raw = substr($raw, $iv_num_bytes);
		//Hash la clave
		$keyhash = openssl_digest($key, HASH_ALGO, true);
		// y descifrar.
		$data = openssl_decrypt($raw, CIPHER_ALGO, $keyhash, OPENSSL_RAW_DATA, $iv);
		if ($data === false) {
			return 'el descifrado falló: ' . openssl_error_string();
		}
		return $data;
	}
}
if (!function_exists('codificarssl')) {
	function codificarssl($data = '', $key = '') {
		if (empty($data)) {
			return 'No hay datos para codificar';
		}
		$key = empty($key) ? md5(KEY_ALGO) : $key;
		$iv_num_bytes = openssl_cipher_iv_length(CIPHER_ALGO);
		// Construir un vector de inicialización
		$iv = openssl_random_pseudo_bytes($iv_num_bytes, $isStrongCrypto);
		if (!$isStrongCrypto) {
			return 'No es una clave fuerte';
		}
		//Hash la clave
		$keyhash = openssl_digest($key, HASH_ALGO, true);
		$encrypted = openssl_encrypt($data, CIPHER_ALGO, $keyhash, OPENSSL_RAW_DATA, $iv);
		if ($encrypted === false) {
			return 'Error de cifrado: ' . openssl_error_string();
		}
		// El resultado comprende el IV y los datos cifrados.
		return $iv . $encrypted;
	}
}
if (!function_exists('traduceMes')) {
	function traduceMes($date = '') {
		$month_spanish = '';
		if(is_string($date) && !empty($date)) {
			// obtener el numero del mes
			$mes = date('m', strtotime($date));
			// matris con nombres de mes en español
			$meses = array(
				'01' => 'Enero',
				'02' => 'Febrero',
				'03' => 'Marzo',
				'04' => 'Abril',
				'05' => 'Mayo',
				'06' => 'Junio',
				'07' => 'Julio',
				'08' => 'Agosto',
				'09' => 'Septiembre',
				'10' => 'Octubre',
				'11' => 'Noviembre',
				'12' => 'Diciembre',
			);
			$month_spanish = $meses[$mes];
		}
		return $month_spanish;
	}
}
if (!function_exists('implode_r')) {
	/**
	 * implode_r() concatenar array a un strin
	 *
	 * @param mixed $glue separador
	 * @param mixed $arr array a concatenar
	 * @return
	 */
	function implode_r(array $arr, $glue = '<hr>') {
		$ret = array();
		foreach ($arr as $piece) {
			if (is_array($piece) && !empty($piece)) {
				$ret[] = implode_r($piece, $glue);
			} else {
				$ret[] = $piece;
			}
		}
		return implode($glue, $ret);
	}
}
if (!function_exists('pingip')) {
	/**
	 * pingip() verifica si una ip esta activa
	 *
	 * @param mixed $ip
	 * @return
	 */
	function pingip($ip, $port = 80, $timeout = 1) {
		$op = @fsockopen($ip, $port, $errno, $errstr, $timeout);
		if (!$op) {
			return false;
		} else {
			fclose($op); //explicitly close open socket connection
			return true; //DC is up & running, we can safely connect with ldap_connect
		}
	}
}