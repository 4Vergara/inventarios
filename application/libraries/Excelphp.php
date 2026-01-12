<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once FCPATH . '/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
class Excelphp {
	private $hoja;
	private $spreadsheet;
	public function __construct() {
		$this->spreadsheet = new Spreadsheet();
		$this->hoja = $this->spreadsheet->setActiveSheetIndex(0);
	}
	public function reporteProductos($data) {
		$this->crearencabezado($data->user, $data->reporte, $data->logo);
		$fila = 10;
		$this->crearcontenido($data->filas, $data->campos, $fila);
		$this->crearExcel($data->reporte->file_name);
		return;
	}
	public function etiquetasgaexcel($data, $nombreexcel = 'archivo') {
		//set style for cells
		$fontStyle = [
			'font' => [
				'name' => 'Sans Serif'
			]
		];
		$st_bold = [
			'font' => ['bold' => true, 'size' => 5],
			'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER]
		];
		$st_vcenter = [
			'font' => ['size' => 5],
			'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
		];
		$st_no_borders = [
			'borders' => [
				'allBorders' => [
					'borderStyle' => Border::BORDER_MEDIUM,
					'color' => ['argb' => 'FFFFFFFF'],
				],
			],
		];
		$st_titulo = [
			'font' => ['bold' => true, 'size' => 9],
			'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
		];
		foreach(range('A', 'L') as $letra) {
			$this->hoja->getColumnDimension($letra)->setWidth(7);
		}
		$numero = 1;
		$letra = 'A';
		$this->hoja->getRowDimension(1)->setRowHeight(70);
		$this->hoja->mergeCells('A1:B1');
		$this->hoja->mergeCells('C1:G1');
		$this->hoja->setCellValue('C1', $data->producto->nombre)->getStyle('C1')->applyFromArray($st_titulo);
		$this->hoja->mergeCells('H1:L1');
		$this->hoja->setCellValue('H1', 'Número CAS: ' . $data->producto->cas)->getStyle('H1')->applyFromArray($st_titulo);
		if(!empty($data->pngqr) && file_exists($data->pngqr)) {
			$drawing = new Drawing();
			$drawing->setName('QR');
			$drawing->setDescription('QR');
			$drawing->setPath($data->pngqr); // Ruta a tu imagen
			$drawing->setHeight(90); // Ajusta la altura de la imagen
			$drawing->setWidth(90); // Ajusta la anchura de la imagen
			$drawing->setCoordinates('A1'); // Celda donde se insertará la imagen
			$drawing->setWorksheet($this->hoja); // Asigna la imagen a la hoja
		}
		// Pictogramas
		if(!empty($data->pictogramas)) {
			$numero++;
			$contadora = 0;
			foreach($data->pictogramas as $p) {
				$this->hoja->mergeCells($letra.$numero.':'.$letra.($numero+2));
				$contadora++;
				$drawing = new Drawing();
				$drawing->setName('Pictogramas'.$numero);
				$drawing->setDescription('Pictogramas');
				$drawing->setPath(FCPATH . 'resources/SGA/' . $p); // Ruta a tu imagen
				$drawing->setHeight(50); // Ajusta la altura de la imagen
				$drawing->setWidth(50); // Ajusta la anchura de la imagen
				$drawing->setCoordinates($letra.($numero)); // Celda donde se insertará la imagen
				$drawing->setWorksheet($this->hoja); // Asigna la imagen a la hoja
				$letra++;
				if($contadora >= 6) break;
			}
			$numero = $numero + 2;
		}
		$this->hoja->mergeCells('H2:L2')->setCellValue('H2', $data->palabra_advertencia)->getStyle('H2')->applyFromArray($st_bold);
		$this->hoja->mergeCells('G3:L3')->setCellValue('G3', 'Indicación de peligro')->getStyle('G3')->applyFromArray($st_bold);
		// Indicaciones de peligro
		$contadorb = 4;
		if(!empty($data->indicaciones)) {
			foreach($data->indicaciones as $i) {
				$this->hoja->mergeCells('G'.$contadorb.':L'.$contadorb)->setCellValue('G'.$contadorb, $i)->getStyle('G'.$contadorb)->applyFromArray($st_vcenter);
				$contadorb++;
				if($contadorb >= 10) break;
			}
		}
		// Consejos de prudencia
		$contadora = 5;
		if(!empty($data->precauciones)) {
			$this->hoja->mergeCells('A'.$contadora.':F'.$contadora)->setCellValue('A'.$contadora, 'Consejos de prudencia')->getStyle('A'.$contadora)->applyFromArray($st_bold);
			$contadora++;
			foreach($data->precauciones as $p) {
				$this->hoja->mergeCells('A'.$contadora.':F'.$contadora)->setCellValue('A'.$contadora, $p.$p.$p)->getStyle('A'.$contadora)->applyFromArray($st_vcenter);
				$contadora++;
			}
		}
		$numero = $contadora > $contadorb? $contadora : $contadorb;
		$numero++;
		// pintar logo
		$this->hoja->mergeCells('A'.$numero.':B'.($numero+3));
		if(!empty($data->logo) && file_exists($data->logo)) {
			$drawing = new Drawing();
			$drawing->setName('Logo');
			$drawing->setDescription('Logo');
			$drawing->setPath($data->logo); // Ruta a tu imagen
			$drawing->setHeight(70); // Ajusta la altura de la imagen
			$drawing->setWidth(90); // Ajusta la anchura de la imagen
			$drawing->setCoordinates('A'.$numero); // Celda donde se insertará la imagen
			$drawing->setWorksheet($this->hoja); // Asigna la imagen a la hoja
		}
		// informacion adicional
		$this->hoja->setCellValue('C'.$numero, 'Cliente:')->getStyle('C'.$numero)->applyFromArray($st_bold);
		$this->hoja->mergeCells('D'.$numero.':H'.$numero)->setCellValue('D'.$numero, $data->usuario->nit . ' ' . $data->usuario->razon_social);
		$this->hoja->mergeCells('I'.$numero.':J'.$numero)->setCellValue('I'.$numero, 'Teléfono de contacto:')->getStyle('I'.$numero)->applyFromArray($st_bold);
		$this->hoja->mergeCells('K'.$numero.':L'.$numero)->setCellValue('K'.$numero, $data->usuario->telefonoHabitacion);
		$numero++;
		$this->hoja->setCellValue('C'.$numero, 'Web:')->getStyle('C'.$numero)->applyFromArray($st_bold);
		$this->hoja->mergeCells('D'.$numero.':H'.$numero)->setCellValue('D'.$numero, $data->usuario->web)->getStyle('D'.$numero);
		$this->hoja->mergeCells('I'.$numero.':J'.$numero)->setCellValue('I'.$numero, 'Teléfono de emergencia:')->getStyle('I'.$numero)->applyFromArray($st_bold);
		$this->hoja->mergeCells('K'.$numero.':L'.$numero) ->setCellValue('K'.$numero, $data->usuario->telefonoMovil);
		$numero++;
		$this->hoja->setCellValue('C'.$numero, 'Dirección:')->getStyle('C'.$numero)->applyFromArray($st_bold);
		$this->hoja->mergeCells('D'.$numero.':H'.$numero)->setCellValue('D'.$numero, $data->usuario->direccion);
		$numero +=2;
		$this->hoja->mergeCells('A'.$numero.':L'.$numero)->setCellValue('A'.$numero, 'Instrucciones de uso: '.$data->params->pdfinuso)->getStyle('A'.$numero)->applyFromArray($st_vcenter);
		$numero++;
		// pie de pagina
		$numero++;
		$this->hoja->mergeCells('B'.$numero.':C'.$numero)->setCellValue('B'.$numero, 'Fecha de expedición: '. $data->params->pdfexpedicion);
		$this->hoja->mergeCells('D'.$numero.':E'.$numero)->setCellValue('D'.$numero, 'Fecha de vencimiento: '. $data->params->pdfvencimiento);
		$this->hoja->mergeCells('F'.$numero.':G'.$numero)->setCellValue('F'.$numero, 'Lote Nº: '. $data->params->pdflote);
		$this->hoja->mergeCells('H'.$numero.':I'.$numero)->setCellValue('H'.$numero, 'Peso neto: '. $data->params->pdfpesoneto);
		$numero++;
		$this->hoja->getStyle('A1:L'.$numero)->applyFromArray($st_no_borders)->applyFromArray($st_vcenter);
		foreach(range(4, $numero) as $i) {
			$this->hoja->getRowDimension($i)->setRowHeight(10);
		}
		$this->hoja->getStyle('A1:I' . $numero)->applyFromArray($fontStyle);
		$this->crearExcel($nombreexcel, 'Etiqueta');
		return;
	}
	// ? funciones privadas
	private function crearencabezado($user, $reporte, $logo) {
		//set style for cells
		$st_bold = [
			'font' => ['bold' => true, 'size' => 5],
			'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER]
		];
		$st_it = [
			'font' => ['italic' => true, 'size' => 6],
			'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
		];
		$st_vcenter = [
			'font' => ['size' => 5],
			'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
		];
		$st_no_borders = [
			'borders' => [
				'allBorders' => [
					'borderStyle' => Border::BORDER_MEDIUM,
					'color' => ['argb' => 'FFFFFFFF'],
				],
			],
		];
		$st_titulo = [
			'font' => ['bold' => true, 'size' => 9],
			'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
		];
		foreach(range('A', 'K') as $letra) {
			$this->hoja->getColumnDimension($letra)->setWidth(7);
		}
		$this->hoja->mergeCells('A1:C7');
		$this->hoja->mergeCells('D1:G1');
		$this->hoja->mergeCells('D2:G2');
		$this->hoja->mergeCells('D3:G3');
		$this->hoja->mergeCells('D4:G4');
		$this->hoja->mergeCells('D5:G5');
		$this->hoja->mergeCells('D6:G6');
		$this->hoja->mergeCells('D7:G7');
		$this->hoja->mergeCells('H1:K1');
		$this->hoja->mergeCells('H2:K2');
		$this->hoja->mergeCells('H3:K3');
		$this->hoja->mergeCells('H4:K4');
		$this->hoja->mergeCells('H5:K5');
		$this->hoja->mergeCells('H6:K6');
		$this->hoja->mergeCells('H7:K7');
		foreach (range(1, 7) as $i) {
			$this->hoja->getRowDimension($i)->setRowHeight(6);
		}
		$this->hoja->setCellValue('D2', 'República de Colombia')->getStyle('D2')->applyFromArray($st_vcenter);
		$this->hoja->setCellValue('D3', $user->nit . ' ' . $user->razon_social)->getStyle('D3')->applyFromArray($st_bold);
		$this->hoja->setCellValue('D4', $user->direccion)->getStyle('D4')->applyFromArray($st_vcenter);
		$this->hoja->setCellValue('H2', 'Fecha de impresión:' . date('Y-m-d H:i:s'))->getStyle('H2')->applyFromArray($st_it);
		$this->hoja->setCellValue('H3', 'Usuario: ' . $user->username)->getStyle('H3')->applyFromArray($st_it);
		$this->hoja->getStyle('A1:K7')->applyFromArray($st_no_borders);
		// Crear una nueva instancia de Drawing
		if (!empty($logo) && file_exists($logo)) {
			$drawing = new Drawing();
			$drawing->setName('Logo');
			$drawing->setDescription('Logo');
			$drawing->setPath($logo); // Ruta a tu imagen
			// $drawing->setHeight(70); // Ajusta la altura de la imagen
			$drawing->setWidth(160); // Ajusta la anchura de la imagen
			$drawing->setCoordinates('A1'); // Celda donde se insertará la imagen
			$drawing->setWorksheet($this->hoja); // Asigna la imagen a la hoja
		}
		//------------------------------------------------------------------------
		$this->hoja->mergeCells('A8:K8');
		$this->hoja->setCellValue('A8', $reporte->nombre )->getStyle('A8')->applyFromArray($st_titulo);
		$this->hoja->mergeCells('A9:K9');
		$this->hoja->setCellValue('A9', $reporte->descripcion)->getStyle('A9')->applyFromArray($st_it);
		return;
	}
	private function crearcontenido($filas, $campos = null, $numero=10) {
		//set style for cells
		$st_bold = [
			'font' => ['bold' => true, 'size' => 5],
			'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER]
		];
		$st_vcenter = [
			'font' => ['size' => 5],
			'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
		];
		$haycampos = !empty($campos);
		if($haycampos) {
			$letra = 'A';
			foreach ($campos as $key => $campo) {
				$this->hoja->setCellValue($letra . $numero, $campo)->getStyle($letra . $numero)->applyFromArray($st_bold);
				$letra++;
			}
			$numero++;
		}
		if(!empty($filas)) {
			if(!isset($letra)) {
				$letra = 'K';
			}
			foreach ($filas as $fila) {
				$letra = 'A';
				if($haycampos) {
					foreach ($campos as $key => $value) {
						$valor = isset($fila->$key)? $fila->$key : '';
						$this->hoja->setCellValue($letra . $numero, $valor)->getStyle($letra . $numero)->applyFromArray($st_vcenter);
						$letra++;
					}
				} else {
					foreach ($fila as $value) {
						$this->hoja->setCellValue($letra . $numero, $value)->getStyle($letra . $numero)->applyFromArray($st_vcenter);
						$letra++;
					}
				}
				$this->hoja->getRowDimension($numero)->setRowHeight(10); // Ajusta la altura según tus necesidades
				$numero++;
			}
		}
	}
	private function crearExcel($nombreexcel,$hoja = 'Hoja') {
		$this->hoja->setTitle($hoja);
		$this->hoja->getSheetView()->setZoomScale(200);
		$writer = IOFactory::createWriter($this->spreadsheet, 'Xlsx');
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="' . $nombreexcel . '.xlsx"');
		header('Cache-Control: public'); // needed for internet explorer
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
		header('Pragma: public'); // HTTP/1.0
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Content-Type: application/force-download");
		header("Content-Type: application/octet-stream");
		header("Content-Type: application/download");
		header("Content-Transfer-Encoding: binary ");
		$writer->save('php://output');
		return;
	}
}
if (!function_exists('str_starts_with')) {
	function str_starts_with($haystack, $needle) {
		return (string) $needle !== '' && strncmp($haystack, $needle, strlen($needle)) === 0;
	}
}
if (!function_exists('convertGifToPng')) {
	function convertGifToPng($gif, $png) {
		$image = imagecreatefromgif($gif);
		imagepng($image, $png);
		imagedestroy($image);
	}
}