<!-- Header de la sección -->
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
	<div>
		<h2 class="mb-1" style="color: var(--color_principal-700);">
			<i class="bi bi-box-seam me-2"></i>Gestión de Productos
		</h2>
		<p class="text-muted mb-0">Administra tu inventario de productos</p>
	</div>
	<a href="<?php echo IP_SERVER . 'productos/crear'; ?>" class="btn btn-color_principal btn-lg">
		<i class="bi bi-plus-circle me-2"></i>Nuevo Producto
	</a>
</div>

<!-- Card con la tabla -->
<div class="card shadow-sm border-0">
	<div class="card-header bg-white py-3">
		<div class="row align-items-center">
			<div class="col-md-6">
				<h5 class="mb-0" style="color: var(--color_principal-600);">
					<i class="bi bi-list-ul me-2"></i>Lista de Productos
				</h5>
			</div>
			<div class="col-md-6 text-md-end mt-2 mt-md-0">
				<button class="btn btn-outline-secondary btn-sm me-2" onclick="recargarTabla()">
					<i class="bi bi-arrow-clockwise me-1"></i>Actualizar
				</button>
				<button class="btn btn-outline-success btn-sm" onclick="exportarExcel()">
					<i class="bi bi-file-earmark-excel me-1"></i>Exportar
				</button>
			</div>
		</div>
	</div>
	<div class="card-body">
		<div class="table-responsive">
			<table id="tablaProductos" class="table table-hover table-striped align-middle w-100">
				<thead class="table-light">
					<tr>
						<th class="text-center" style="width: 60px;">#</th>
						<th style="min-width: 80px;">Imagen</th>
						<th style="min-width: 120px;">SKU</th>
						<th style="min-width: 200px;">Nombre</th>
						<th style="min-width: 120px;">Marca</th>
						<th class="text-end" style="min-width: 100px;">P. Costo</th>
						<th class="text-end" style="min-width: 100px;">P. Venta</th>
						<th class="text-center" style="min-width: 80px;">Stock</th>
						<th class="text-center" style="min-width: 100px;">Estado</th>
						<th class="text-center" style="min-width: 120px;">Acciones</th>
					</tr>
				</thead>
				<tbody>
					<!-- Los datos se cargan por AJAX -->
				</tbody>
			</table>
		</div>
	</div>
</div>

<!-- Modal de confirmación para eliminar -->
<div class="modal fade" id="modalEliminar" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header border-0">
				<h5 class="modal-title text-danger">
					<i class="bi bi-exclamation-triangle me-2"></i>Confirmar Eliminación
				</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<p>¿Estás seguro de que deseas eliminar el producto <strong id="nombreProductoEliminar"></strong>?</p>
				<p class="text-muted small">Esta acción no se puede deshacer.</p>
				<input type="hidden" id="idProductoEliminar">
			</div>
			<div class="modal-footer border-0">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
				<button type="button" class="btn btn-danger" onclick="confirmarEliminar()">
					<i class="bi bi-trash me-1"></i>Eliminar
				</button>
			</div>
		</div>
	</div>
</div>

<script>
let tablaProductos;

$(document).ready(function() {
	inicializarTabla();
});

function inicializarTabla() {
	tablaProductos = $('#tablaProductos').DataTable({
		ajax: {
			url: IP_SERVER + 'productos/listar',
			type: 'POST',
			dataSrc: 'data'
		},
		columns: [
			{ 
				data: null,
				className: 'text-center',
				render: function(data, type, row, meta) {
					return meta.row + 1;
				}
			},
			{ 
				data: 'imagen_principal_url',
				orderable: false,
				render: function(data) {
					if (data) {
						return `<img src="${data}" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">`;
					}
					return `<div class="bg-light d-flex align-items-center justify-content-center rounded" style="width: 50px; height: 50px;">
						<i class="bi bi-image text-muted"></i>
					</div>`;
				}
			},
			{ data: 'sku', render: function(data) { return data || '<span class="text-muted">-</span>'; } },
			{ 
				data: 'nombre',
				render: function(data, type, row) {
					let html = `<strong>${data}</strong>`;
					if (row.descripcion_corta) {
						html += `<br><small class="text-muted">${row.descripcion_corta.substring(0, 50)}${row.descripcion_corta.length > 50 ? '...' : ''}</small>`;
					}
					return html;
				}
			},
			{ data: 'marca', render: function(data) { return data || '<span class="text-muted">-</span>'; } },
			{ 
				data: 'precio_costo',
				className: 'text-end',
				render: function(data) {
					return '$' + parseFloat(data).toLocaleString('es-MX', {minimumFractionDigits: 2, maximumFractionDigits: 2});
				}
			},
			{ 
				data: 'precio_venta',
				className: 'text-end',
				render: function(data) {
					return '$' + parseFloat(data).toLocaleString('es-MX', {minimumFractionDigits: 2, maximumFractionDigits: 2});
				}
			},
			{ 
				data: 'stock_actual',
				className: 'text-center',
				render: function(data, type, row) {
					let stockMin = parseInt(row.stock_minimo) || 5;
					let stock = parseInt(data);
					let badgeClass = 'bg-success';
					if (stock <= 0) {
						badgeClass = 'bg-danger';
					} else if (stock <= stockMin) {
						badgeClass = 'bg-warning text-dark';
					}
					return `<span class="badge ${badgeClass}">${stock}</span>`;
				}
			},
			{ 
				data: 'estado',
				className: 'text-center',
				render: function(data) {
					let estados = {
						'activo': '<span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Activo</span>',
						'inactivo': '<span class="badge bg-secondary"><i class="bi bi-pause-circle me-1"></i>Inactivo</span>',
						'descatalogado': '<span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Descatalogado</span>'
					};
					return estados[data] || data;
				}
			},
			{ 
				data: 'id',
				className: 'text-center',
				orderable: false,
				render: function(data, type, row) {
					return `
						<div class="btn-group btn-group-sm" role="group">
							<a href="${IP_SERVER}productos/ver/${data}" class="btn btn-outline-info" title="Ver detalle">
								<i class="bi bi-eye"></i>
							</a>
							<a href="${IP_SERVER}productos/editar/${data}" class="btn btn-outline-warning" title="Editar">
								<i class="bi bi-pencil"></i>
							</a>
							<button type="button" class="btn btn-outline-danger" title="Eliminar" onclick="prepararEliminar(${data}, '${row.nombre.replace(/'/g, "\\'")}')">
								<i class="bi bi-trash"></i>
							</button>
						</div>
					`;
				}
			}
		],
		language: TABLA_CONFIGURACION.language,
		order: [[3, 'asc']],
		responsive: true,
		pageLength: 10,
		lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Todos"]],
		dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 text-end"f>>rtip'
	});
}

function recargarTabla() {
	tablaProductos.ajax.reload(null, false);
}

function exportarExcel() {
	window.location.href = IP_SERVER + 'productos/exportar';
}

function prepararEliminar(id, nombre) {
	$('#idProductoEliminar').val(id);
	$('#nombreProductoEliminar').text(nombre);
	new bootstrap.Modal(document.getElementById('modalEliminar')).show();
}

function confirmarEliminar() {
	let id = $('#idProductoEliminar').val();
	$.ajax({
		url: IP_SERVER + 'productos/eliminar',
		type: 'POST',
		data: { id: id },
		dataType: 'json',
		success: function(response) {
			bootstrap.Modal.getInstance(document.getElementById('modalEliminar')).hide();
			if (response.success) {
				Swal.fire({
					icon: 'success',
					title: '¡Eliminado!',
					text: response.message,
					timer: 2000,
					showConfirmButton: false
				});
				recargarTabla();
			} else {
				Swal.fire({
					icon: 'error',
					title: 'Error',
					text: response.message
				});
			}
		},
		error: function() {
			Swal.fire({
				icon: 'error',
				title: 'Error',
				text: 'Ocurrió un error al procesar la solicitud'
			});
		}
	});
}
</script>