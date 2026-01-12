<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once FCPATH . '/application/third_party/phpqrcode/qrlib.php';
// ? https://phpqrcode.sourceforge.net/examples/index.php
class Phpqrcode {
	public function generaQr($data, $filename = 'qrcode.png') {
		QRcode::png($data, $filename, 'M', 7, 0);
	}
}