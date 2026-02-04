<!-- Page Header -->
<div class="page-header mb-4">
	<div class="row align-items-center">
		<div class="col-lg-8">
			<h1 class="page-title">Catálogo de Productos</h1>
			<p class="page-subtitle">Administra tu inventario de productos con precisión.</p>
		</div>
		<div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
			<button class="btn btn-outline-secondary me-2" onclick="exportarExcel()">
				<i class="bi bi-file-earmark-arrow-up me-1"></i>Exportar CSV
			</button>
			<a href="<?php echo IP_SERVER . 'productos/crear'; ?>" class="btn btn-color_principal">
				<i class="bi bi-plus-lg me-1"></i>Agregar Producto
			</a>
		</div>
	</div>
</div>

<!-- Stats Cards -->
<div class="row g-4 mb-4">
	<div class="col-sm-6 col-xl-3">
		<div class="stat-card">
			<div class="stat-content">
				<span class="stat-label">TOTAL PRODUCTOS</span>
				<div class="stat-value-row">
					<span class="stat-value" id="totalProductos">0</span>
					<span class="stat-badge stat-badge-success">+2.5%</span>
				</div>
			</div>
		</div>
	</div>
	<div class="col-sm-6 col-xl-3">
		<div class="stat-card">
			<div class="stat-content">
				<span class="stat-label">PRODUCTOS ACTIVOS</span>
				<div class="stat-value-row">
					<span class="stat-value" id="productosActivos">0</span>
					<span class="stat-badge stat-badge-neutral">Estable</span>
				</div>
			</div>
		</div>
	</div>
	<div class="col-sm-6 col-xl-3">
		<div class="stat-card stat-card-warning">
			<div class="stat-content">
				<span class="stat-label stat-label-warning">STOCK BAJO</span>
				<div class="stat-value-row">
					<span class="stat-value stat-value-warning" id="stockBajo">0</span>
					<span class="stat-badge stat-badge-warning">Urgente</span>
				</div>
			</div>
		</div>
	</div>
	<div class="col-sm-6 col-xl-3">
		<div class="stat-card">
			<div class="stat-content">
				<span class="stat-label">SIN STOCK</span>
				<div class="stat-value-row">
					<span class="stat-value stat-value-danger" id="sinStock">0</span>
					<span class="stat-badge stat-badge-danger">Crítico</span>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Products Table Card -->
<div class="card table-card">
	<!-- Tabs -->
	<div class="card-header bg-white border-bottom">
		<div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
			<ul class="nav nav-tabs-custom" id="productTabs" role="tablist">
				<li class="nav-item" role="presentation">
					<button class="nav-link-custom active" id="all-tab" data-filter="all" type="button">
						Todo el Inventario
					</button>
				</li>
				<li class="nav-item" role="presentation">
					<button class="nav-link-custom" id="lowstock-tab" data-filter="lowstock" type="button">
						Stock Bajo
					</button>
				</li>
				<li class="nav-item" role="presentation">
					<button class="nav-link-custom" id="inactive-tab" data-filter="inactive" type="button">
						Inactivos
					</button>
				</li>
			</ul>
			<button class="btn btn-sm btn-outline-secondary" onclick="recargarTabla()">
				<i class="bi bi-arrow-clockwise me-1"></i>Actualizar
			</button>
		</div>
	</div>
	
	<div class="card-body p-0">
		<div class="table-responsive p-3">
			<table id="tablaProductos" class="table table-modern align-middle mb-0">
				<thead>
					<tr>
						<th style="width: 280px;">PRODUCTO</th>
						<th>CATEGORÍA</th>
						<th class="text-end">PRECIO</th>
						<th style="width: 180px;">ESTADO INVENTARIO</th>
						<th class="text-center" style="width: 120px;">ACCIONES</th>
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
		<div class="modal-content border-0 shadow">
			<div class="modal-header border-0 pb-0">
				<h5 class="modal-title text-danger">
					<i class="bi bi-exclamation-triangle me-2"></i>Confirmar Eliminación
				</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<p>¿Estás seguro de que deseas eliminar el producto <strong id="nombreProductoEliminar"></strong>?</p>
				<p class="text-muted small mb-0">Esta acción no se puede deshacer.</p>
				<input type="hidden" id="idProductoEliminar">
			</div>
			<div class="modal-footer border-0 pt-0">
				<button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
				<button type="button" class="btn btn-danger" onclick="confirmarEliminar()">
					<i class="bi bi-trash me-1"></i>Eliminar
				</button>
			</div>
		</div>
	</div>
</div>

<script>
let tablaProductos;
let productosData = [];

$(document).ready(function() {
	inicializarTabla();
	
	// Tab filtering
	$('.nav-link-custom').on('click', function() {
		$('.nav-link-custom').removeClass('active');
		$(this).addClass('active');
		
		let filter = $(this).data('filter');
		filtrarTabla(filter);
	});
});

function inicializarTabla() {
	tablaProductos = $('#tablaProductos').DataTable({
		ajax: {
			url: IP_SERVER + 'productos/listar',
			type: 'POST',
			dataSrc: function(json) {
				productosData = json.data;
				actualizarEstadisticas(json.data);
				return json.data;
			}
		},
		columns: [
			{ 
				data: null,
				render: function(data, type, row) {
					let imgHtml = row.imagen_principal_url 
						? `<img src="${row.imagen_principal_url}" class="product-img" alt="${row.nombre}">`
						: `<div class="product-img-placeholder"><i class="bi bi-image"></i></div>`;
					
					return `
						<div class="product-cell">
							${imgHtml}
							<div class="product-info">
								<span class="product-name">${row.nombre}</span>
								<span class="product-sku">SKU: ${row.sku || 'N/A'}</span>
							</div>
						</div>
					`;
				}
			},
			{ 
				data: 'marca',
				render: function(data) {
					return `<span class="category-badge">${data || 'Sin categoría'}</span>`;
				}
			},
			{ 
				data: 'precio_venta',
				className: 'text-end',
				render: function(data) {
					return `<span class="product-price">$${parseFloat(data).toLocaleString('es-MX', {minimumFractionDigits: 2})}</span>`;
				}
			},
			{ 
				data: null,
				render: function(data, type, row) {
					let stock = parseInt(row.stock_actual) || 0;
					let stockMin = parseInt(row.stock_minimo) || 5;
					let maxStock = 100;
					let percentage = Math.min((stock / maxStock) * 100, 100);
					
					let statusClass, statusText, progressClass;
					if (stock <= 0) {
						statusClass = 'out-of-stock';
						statusText = 'SIN STOCK';
						progressClass = 'critical';
					} else if (stock <= stockMin) {
						statusClass = 'low-stock';
						statusText = 'STOCK BAJO';
						progressClass = 'low';
					} else if (stock <= stockMin * 2) {
						statusClass = 'low-stock';
						statusText = 'STOCK BAJO';
						progressClass = 'medium';
					} else {
						statusClass = 'in-stock';
						statusText = 'EN STOCK';
						progressClass = 'high';
					}
					
					return `
						<div class="inventory-status">
							<div class="inventory-status-header">
								<span class="status-text ${statusClass}">${statusText}</span>
								<span class="stock-count">${stock}/${maxStock}</span>
							</div>
							<div class="progress-bar-custom">
								<div class="progress-bar-fill ${progressClass}" style="width: ${percentage}%"></div>
							</div>
						</div>
					`;
				}
			},
			{ 
				data: 'id',
				className: 'text-center',
				orderable: false,
				render: function(data, type, row) {
					return `
						<div class="action-buttons">
							<a href="${IP_SERVER}productos/ver/${data}" class="action-btn" title="Ver detalle">
								<i class="bi bi-eye"></i>
							</a>
							<a href="${IP_SERVER}productos/editar/${data}" class="action-btn" title="Editar">
								<i class="bi bi-pencil"></i>
							</a>
							<button type="button" class="action-btn delete" title="Eliminar" onclick="prepararEliminar(${data}, '${row.nombre.replace(/'/g, "\\'")}')">
								<i class="bi bi-trash"></i>
							</button>
						</div>
					`;
				}
			}
		],
		language: TABLA_CONFIGURACION.language,
		order: [[0, 'asc']],
		responsive: true,
		pageLength: 10,
		lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Todos"]],
		dom: '<"row mb-3"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row mt-3"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>'
	});
}

function actualizarEstadisticas(data) {
	let total = data.length;
	let activos = data.filter(p => p.estado === 'activo').length;
	let stockBajo = data.filter(p => {
		let stock = parseInt(p.stock_actual) || 0;
		let min = parseInt(p.stock_minimo) || 5;
		return stock > 0 && stock <= min;
	}).length;
	let sinStock = data.filter(p => (parseInt(p.stock_actual) || 0) <= 0).length;
	
	$('#totalProductos').text(total.toLocaleString());
	$('#productosActivos').text(activos.toLocaleString());
	$('#stockBajo').text(stockBajo.toLocaleString());
	$('#sinStock').text(sinStock.toLocaleString());
}

function filtrarTabla(filter) {
	if (filter === 'all') {
		tablaProductos.search('').columns().search('').draw();
	} else if (filter === 'lowstock') {
		tablaProductos.search('STOCK BAJO').draw();
	} else if (filter === 'inactive') {
		tablaProductos.search('inactivo').draw();
	}
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