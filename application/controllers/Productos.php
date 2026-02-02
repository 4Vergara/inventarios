<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Productos extends CI_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->model('Productos_model');
		$this->load->model('Configuraciones_model');
	}

	/**
	 * Listado de productos
	 */
	public function index() {
		$this->vista('productos/index');
	}

	/**
	 * Crear nuevo producto
	 */
	public function crear() {
		// ? Cargar datos necesarios para el formulario de creación
		$data['categorias'] = $this->Configuraciones_model->obtenerCategoriasProductos();
		$data['tallas'] = $this->Configuraciones_model->obtenerPorValorPadre('SIZE_MASTER');
		$data['unidades'] = $this->Configuraciones_model->obtenerPorValorPadre('UNIT_MASTER');
		$data['temperaturas'] = $this->Configuraciones_model->obtenerPorValorPadre('TEMP_MASTER');
		$data['generos'] = $this->Configuraciones_model->obtenerPorValorPadre('GENDER_MASTER');
		$data['voltajes'] = $this->Configuraciones_model->obtenerPorValorPadre('VOLT_MASTER');
		$this->vista('productos/formulario', $data);
	}

	/**
	 * Editar producto existente
	 */
	public function editar($id = null) {
		if (!$id) {
			redirect('productos');
		}
		
		$data['producto'] = $this->Productos_model->obtenerPorIdConDetalles($id);
		
		if (!$data['producto']) {
			redirect('productos');
		}
		
		$data['categorias'] = $this->Configuraciones_model->obtenerCategoriasProductos();
		$data['tallas'] = $this->Configuraciones_model->obtenerPorValorPadre('SIZE_MASTER');
		$data['unidades'] = $this->Configuraciones_model->obtenerPorValorPadre('UNIT_MASTER');
		$data['temperaturas'] = $this->Configuraciones_model->obtenerPorValorPadre('TEMP_MASTER');
		$data['generos'] = $this->Configuraciones_model->obtenerPorValorPadre('GENDER_MASTER');
		$data['voltajes'] = $this->Configuraciones_model->obtenerPorValorPadre('VOLT_MASTER');
		$this->vista('productos/formulario', $data);
	}

	/**
	 * Ver detalle de producto
	 */
	public function ver($id = null) {
		if (!$id) {
			redirect('productos');
		}
		
		$data['producto'] = $this->Productos_model->obtenerPorIdConDetalles($id);
		if (!$data['producto']) {
			redirect('productos');
		}
		
		$data['categorias'] = $this->Configuraciones_model->obtenerCategoriasProductos();
		$data['tallas'] = $this->Configuraciones_model->obtenerPorValorPadre('SIZE_MASTER');
		$data['unidades'] = $this->Configuraciones_model->obtenerPorValorPadre('UNIT_MASTER');
		$data['temperaturas'] = $this->Configuraciones_model->obtenerPorValorPadre('TEMP_MASTER');
		$data['generos'] = $this->Configuraciones_model->obtenerPorValorPadre('GENDER_MASTER');
		$data['voltajes'] = $this->Configuraciones_model->obtenerPorValorPadre('VOLT_MASTER');
		$this->vista('productos/ver', $data);
	}

	/**
	 * Listar productos para DataTable (AJAX)
	 */
	public function listar() {
		$productos = $this->Productos_model->findAll();
		
		echo json_encode([
			'success' => true,
			'data' => $productos
		]);
	}

	/**
	 * Guardar producto (crear o actualizar)
	 */
	public function guardar() {
		$response = ['success' => false, 'message' => 'Error al procesar la solicitud'];
		
		$id = $this->input->post('id');
		
		// Obtener usuario actual
		$usuario = isset($this->session->datosusuario) ? $this->session->datosusuario->nombre_completo : 'Sistema';
		
		// Preparar datos
		$data = [
			'id_categoria' => $this->input->post('id_categoria'),
			'sku' => $this->input->post('sku') ?: null,
			'codigo_barras' => $this->input->post('codigo_barras') ?: null,
			'nombre' => $this->input->post('nombre'),
			'descripcion_corta' => $this->input->post('descripcion_corta') ?: null,
			'descripcion_detallada' => $this->input->post('descripcion_detallada') ?: null,
			'marca' => $this->input->post('marca') ?: null,
			'imagen_principal_url' => $this->input->post('imagen_principal_url') ?: null,
			'precio_costo' => $this->input->post('precio_costo') ?: 0,
			'precio_venta' => $this->input->post('precio_venta') ?: 0,
			'porcentaje_impuesto' => $this->input->post('porcentaje_impuesto') ?: 0,
			'stock_actual' => $this->input->post('stock_actual') ?: 0,
			'stock_minimo' => $this->input->post('stock_minimo') ?: 5,
			'id_unidad_medida' => $this->input->post('id_unidad_medida') ?: 'unidad',
			'estado' => $this->input->post('estado') ?: 'activo',
			'peso_kg' => $this->input->post('peso_kg') ?: null,
			'ancho_cm' => $this->input->post('ancho_cm') ?: null,
			'alto_cm' => $this->input->post('alto_cm') ?: null,
			'profundidad_cm' => $this->input->post('profundidad_cm') ?: null,
			'es_envio_gratis' => $this->input->post('es_envio_gratis') ? 1 : 0,
			'es_perecedero' => $this->input->post('es_perecedero') ? 1 : 0,
			'fecha_vencimiento' => $this->input->post('fecha_vencimiento') ?: null,
			'fecha_elaboracion' => $this->input->post('fecha_elaboracion') ?: null,
			'ingredientes' => $this->input->post('ingredientes') ?: null,
			'info_nutricional' => $this->input->post('info_nutricional') ?: null,
			'id_temperatura_conservacion' => $this->input->post('id_temperatura_conservacion') ?: null,
			'id_talla' => $this->input->post('id_talla') ?: null,
			'color' => $this->input->post('color') ?: null,
			'material_principal' => $this->input->post('material_principal') ?: null,
			'id_genero' => $this->input->post('id_genero') ?: null,
			'estilo_corte' => $this->input->post('estilo_corte') ?: null,
			'modelo_tecnico' => $this->input->post('modelo_tecnico') ?: null,
			'numero_serie' => $this->input->post('numero_serie') ?: null,
			'garantia_meses' => $this->input->post('garantia_meses') ?: 0,
			'consumo_watts' => $this->input->post('consumo_watts') ?: null,
			'id_voltaje' => $this->input->post('id_voltaje') ?: null,
			'es_inteligente' => $this->input->post('es_inteligente') ? 1 : 0,
			'especificaciones_extra' => $this->input->post('especificaciones_extra') ?: null
		];
		
		if ($id) {
			// Actualizar
			$data['actualizado_por'] = $usuario;
			$result = $this->Productos_model->update($id, $data);
			if ($result) {
				$response = ['success' => true, 'message' => 'Producto actualizado correctamente'];
			} else {
				$response['message'] = 'Error al actualizar el producto';
			}
		} else {
			// Crear nuevo
			$data['creado_por'] = $usuario;
			$result = $this->Productos_model->save($data);
			if ($result) {
				$response = ['success' => true, 'message' => 'Producto creado correctamente', 'id' => $result];
			} else {
				$response['message'] = 'Error al crear el producto';
			}
		}
		
		echo json_encode($response);
	}

	/**
	 * Eliminar producto
	 */
	public function eliminar() {
		$response = ['success' => false, 'message' => 'Error al procesar la solicitud'];
		
		$id = $this->input->post('id');
		
		if ($id) {
			$result = $this->Productos_model->delete($id);
			if ($result) {
				$response = ['success' => true, 'message' => 'Producto eliminado correctamente'];
			} else {
				$response['message'] = 'Error al eliminar el producto';
			}
		}
		
		echo json_encode($response);
	}

	/**
	 * Exportar a Excel
	 */
	public function exportar() {
		$this->load->library('Excelphp');
		
		$productos = $this->Productos_model->findAll();
		
		// Crear archivo Excel
		$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		
		// Encabezados
		$headers = ['ID', 'SKU', 'Código de Barras', 'Nombre', 'Marca', 'Precio Costo', 'Precio Venta', 'Stock', 'Estado'];
		$col = 'A';
		foreach ($headers as $header) {
			$sheet->setCellValue($col . '1', $header);
			$col++;
		}
		
		// Datos
		$row = 2;
		foreach ($productos as $producto) {
			$sheet->setCellValue('A' . $row, $producto->id);
			$sheet->setCellValue('B' . $row, $producto->sku);
			$sheet->setCellValue('C' . $row, $producto->codigo_barras);
			$sheet->setCellValue('D' . $row, $producto->nombre);
			$sheet->setCellValue('E' . $row, $producto->marca);
			$sheet->setCellValue('F' . $row, $producto->precio_costo);
			$sheet->setCellValue('G' . $row, $producto->precio_venta);
			$sheet->setCellValue('H' . $row, $producto->stock_actual);
			$sheet->setCellValue('I' . $row, $producto->estado);
			$row++;
		}
		
		// Descargar
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="productos_' . date('Y-m-d') . '.xlsx"');
		header('Cache-Control: max-age=0');
		
		$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
		$writer->save('php://output');
		exit;
	}
}
