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

<style>
/* Page Header */
.page-title {
	font-size: 2rem;
	font-weight: 800;
	color: #1f2937;
	margin-bottom: 5px;
}
.page-subtitle {
	color: #6b7280;
	font-size: 1rem;
	margin-bottom: 0;
}

/* Stat Cards */
.stat-card {
	background: #ffffff;
	border: 1px solid #e5e7eb;
	border-radius: 16px;
	padding: 20px 24px;
	transition: all 0.2s ease;
}
.stat-card:hover {
	box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
}
.stat-card-warning {
	border-color: var(--color_principal-200);
}
.stat-label {
	font-size: 0.7rem;
	font-weight: 600;
	color: #9ca3af;
	letter-spacing: 0.5px;
	text-transform: uppercase;
}
.stat-label-warning {
	color: var(--color_principal-500);
}
.stat-value-row {
	display: flex;
	align-items: flex-end;
	justify-content: space-between;
	margin-top: 8px;
}
.stat-value {
	font-size: 2rem;
	font-weight: 800;
	color: #1f2937;
	line-height: 1;
}
.stat-value-warning {
	color: var(--color_principal-500);
}
.stat-value-danger {
	color: #ef4444;
}
.stat-badge {
	font-size: 0.7rem;
	font-weight: 600;
	padding: 4px 10px;
	border-radius: 20px;
}
.stat-badge-success {
	background: #dcfce7;
	color: #16a34a;
}
.stat-badge-neutral {
	background: #f3f4f6;
	color: #6b7280;
}
.stat-badge-warning {
	background: var(--color_principal-100);
	color: var(--color_principal-600);
}
.stat-badge-danger {
	background: #fee2e2;
	color: #dc2626;
}

/* Table Card */
.table-card {
	border: 1px solid #e5e7eb;
	border-radius: 16px;
	overflow: hidden;
}

/* Custom Tabs */
.nav-tabs-custom {
	display: flex;
	gap: 5px;
	list-style: none;
	padding: 0;
	margin: 0;
}
.nav-link-custom {
	padding: 10px 16px;
	background: transparent;
	border: none;
	border-bottom: 3px solid transparent;
	color: #6b7280;
	font-weight: 600;
	font-size: 0.85rem;
	cursor: pointer;
	transition: all 0.2s ease;
}
.nav-link-custom:hover {
	color: #374151;
}
.nav-link-custom.active {
	color: var(--color_principal-600);
	border-bottom-color: var(--color_principal-500);
}

/* Modern Table */
.table-modern {
	margin: 0;
}
.table-modern thead {
	background: #f9fafb;
}
.table-modern thead th {
	padding: 16px 20px;
	font-size: 0.7rem;
	font-weight: 700;
	color: #6b7280;
	text-transform: uppercase;
	letter-spacing: 0.5px;
	border-bottom: 1px solid #e5e7eb;
	white-space: nowrap;
}
.table-modern tbody tr {
	transition: background 0.15s ease;
}
.table-modern tbody tr:hover {
	background: #fafafa;
}
.table-modern tbody td {
	padding: 16px 20px;
	vertical-align: middle;
	border-bottom: 1px solid #f3f4f6;
}

/* Product Cell */
.product-cell {
	display: flex;
	align-items: center;
	gap: 14px;
}
.product-img {
	width: 50px;
	height: 50px;
	border-radius: 12px;
	object-fit: cover;
	border: 1px solid #e5e7eb;
	flex-shrink: 0;
}
.product-img-placeholder {
	width: 50px;
	height: 50px;
	border-radius: 12px;
	background: #f3f4f6;
	display: flex;
	align-items: center;
	justify-content: center;
	color: #9ca3af;
	flex-shrink: 0;
}
.product-info {
	display: flex;
	flex-direction: column;
}
.product-name {
	font-weight: 600;
	color: #1f2937;
	font-size: 0.95rem;
}
.product-sku {
	font-size: 0.75rem;
	color: #9ca3af;
}

/* Category Badge */
.category-badge {
	display: inline-block;
	padding: 6px 12px;
	background: #f3f4f6;
	border-radius: 20px;
	font-size: 0.75rem;
	font-weight: 600;
	color: #4b5563;
}

/* Price */
.product-price {
	font-weight: 700;
	color: #1f2937;
}

/* Inventory Status */
.inventory-status {
	display: flex;
	flex-direction: column;
	gap: 6px;
}
.inventory-status-header {
	display: flex;
	justify-content: space-between;
	align-items: center;
}
.status-text {
	font-size: 0.65rem;
	font-weight: 700;
	text-transform: uppercase;
	letter-spacing: 0.3px;
}
.status-text.in-stock { color: #16a34a; }
.status-text.low-stock { color: var(--color_principal-500); }
.status-text.out-of-stock { color: #dc2626; }
.stock-count {
	font-size: 0.7rem;
	color: #9ca3af;
}
.progress-bar-custom {
	height: 6px;
	background: #e5e7eb;
	border-radius: 3px;
	overflow: hidden;
}
.progress-bar-fill {
	height: 100%;
	border-radius: 3px;
	transition: width 0.3s ease;
}
.progress-bar-fill.high { background: var(--color_principal-500); }
.progress-bar-fill.medium { background: var(--color_principal-400); }
.progress-bar-fill.low { background: #fbbf24; }
.progress-bar-fill.critical { background: #ef4444; }

/* Action Buttons */
.action-buttons {
	display: flex;
	gap: 6px;
	justify-content: center;
	opacity: 0.6;
	transition: opacity 0.2s ease;
}
.table-modern tbody tr:hover .action-buttons {
	opacity: 1;
}
.action-btn {
	width: 36px;
	height: 36px;
	display: flex;
	align-items: center;
	justify-content: center;
	border-radius: 10px;
	border: 1px solid #e5e7eb;
	background: #ffffff;
	color: #6b7280;
	cursor: pointer;
	transition: all 0.2s ease;
}
.action-btn:hover {
	border-color: var(--color_principal-300);
	color: var(--color_principal-600);
	background: var(--color_principal-50);
}
.action-btn.delete:hover {
	border-color: #fca5a5;
	color: #dc2626;
	background: #fef2f2;
}

/* DataTable - Quitar scroll vertical y horizontal */
.table-responsive {
	overflow: visible !important;
}
#tablaProductos_wrapper,
.dt-container {
	overflow: visible !important;
}
.dataTables_scrollBody,
.dt-scroll-body {
	overflow: visible !important;
	max-height: none !important;
}
.dt-scroll {
	overflow: visible !important;
}

/* DataTable - Buscador al lado derecho */
#tablaProductos_filter,
.dt-search {
	text-align: right !important;
	float: right !important;
}
#tablaProductos_filter input,
.dt-search input {
	padding: 8px 14px !important;
	border: 1px solid #e5e7eb !important;
	border-radius: 8px !important;
	font-size: 0.875rem !important;
	transition: all 0.2s ease !important;
	margin-left: 8px !important;
}
#tablaProductos_filter input:focus,
.dt-search input:focus {
	outline: none !important;
	border-color: var(--color_principal-400) !important;
	box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1) !important;
}

/* DataTable - Paginación personalizada */
.dataTables_paginate,
.dt-paging {
	display: flex !important;
	justify-content: flex-end !important;
	gap: 4px !important;
	padding-top: 10px !important;
}
.dataTables_paginate .paginate_button,
.dt-paging .dt-paging-button {
	padding: 8px 14px !important;
	border: 1px solid #e5e7eb !important;
	border-radius: 8px !important;
	background: #ffffff !important;
	color: #6b7280 !important;
	font-size: 0.875rem !important;
	font-weight: 500 !important;
	cursor: pointer !important;
	transition: all 0.2s ease !important;
	margin: 0 2px !important;
}
.dataTables_paginate .paginate_button:hover:not(.disabled):not(.current),
.dt-paging .dt-paging-button:hover:not(.disabled):not(.current) {
	background: var(--color_principal-50) !important;
	border-color: var(--color_principal-300) !important;
	color: var(--color_principal-600) !important;
}
.dataTables_paginate .paginate_button.current,
.dt-paging .dt-paging-button.current {
	background: var(--color_principal-500) !important;
	border-color: var(--color_principal-500) !important;
	color: #ffffff !important;
}
.dataTables_paginate .paginate_button.disabled,
.dt-paging .dt-paging-button.disabled {
	opacity: 0.5 !important;
	cursor: not-allowed !important;
	background: #f9fafb !important;
	color: #9ca3af !important;
}
.dataTables_paginate .paginate_button.previous,
.dataTables_paginate .paginate_button.next,
.dt-paging .dt-paging-button.previous,
.dt-paging .dt-paging-button.next {
	font-weight: 600 !important;
}

/* DataTable - Información y selector de registros */
.dataTables_info,
.dt-info {
	color: #6b7280 !important;
	font-size: 0.875rem !important;
	padding-top: 12px !important;
}
.dataTables_length select,
.dt-length select,
.dt-input {
	padding: 6px 30px 6px 12px !important;
	border: 1px solid #e5e7eb !important;
	border-radius: 8px !important;
	font-size: 0.875rem !important;
	margin: 0 8px !important;
	cursor: pointer !important;
	transition: all 0.2s ease !important;
}
.dataTables_length select:focus,
.dt-length select:focus,
.dt-input:focus {
	outline: none !important;
	border-color: var(--color_principal-400) !important;
}

/* DataTable - Layout rows padding */
.dt-layout-row {
	padding: 10px 15px !important;
}
.dt-layout-table {
	padding: 0 !important;
}
</style>

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