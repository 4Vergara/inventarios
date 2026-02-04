<!-- Page Header -->
<div class="page-header mb-4">
	<div class="row align-items-center">
		<div class="col-lg-8">
			<h1 class="page-title">Gestión de Pedidos</h1>
			<p class="page-subtitle">Administra las ventas y pagos del sistema.</p>
		</div>
		<div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
			<button class="btn btn-outline-secondary me-2" onclick="exportarVentas()">
				<i class="bi bi-file-earmark-arrow-up me-1"></i>Exportar Excel
			</button>
			<a href="<?php echo IP_SERVER . 'ventas/crear'; ?>" class="btn btn-color_principal">
				<i class="bi bi-plus-lg me-1"></i>Nueva Venta
			</a>
		</div>
	</div>
</div>

<!-- Stats Cards -->
<div class="row g-4 mb-4">
	<div class="col-sm-6 col-xl-3">
		<div class="stat-card">
			<div class="stat-content">
				<span class="stat-label">VENTAS DEL MES</span>
				<div class="stat-value-row">
					<span class="stat-value" id="totalVentas">0</span>
					<span class="stat-badge stat-badge-success">
						<i class="bi bi-graph-up"></i>
					</span>
				</div>
			</div>
		</div>
	</div>
	<div class="col-sm-6 col-xl-3">
		<div class="stat-card">
			<div class="stat-content">
				<span class="stat-label">MONTO TOTAL</span>
				<div class="stat-value-row">
					<span class="stat-value" id="montoTotal">$0</span>
				</div>
			</div>
		</div>
	</div>
	<div class="col-sm-6 col-xl-3">
		<div class="stat-card stat-card-warning">
			<div class="stat-content">
				<span class="stat-label stat-label-warning">PENDIENTES DE PAGO</span>
				<div class="stat-value-row">
					<span class="stat-value stat-value-warning" id="ventasPendientes">0</span>
					<span class="stat-badge stat-badge-warning">Pendiente</span>
				</div>
			</div>
		</div>
	</div>
	<div class="col-sm-6 col-xl-3">
		<div class="stat-card">
			<div class="stat-content">
				<span class="stat-label">PROMEDIO POR VENTA</span>
				<div class="stat-value-row">
					<span class="stat-value" id="promedioVenta">$0</span>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Filtros -->
<div class="card mb-4">
	<div class="card-body">
		<div class="row g-3 align-items-end">
			<div class="col-md-3">
				<label class="form-label fw-semibold">Fecha Desde</label>
				<input type="date" class="form-control" id="filtroFechaDesde">
			</div>
			<div class="col-md-3">
				<label class="form-label fw-semibold">Fecha Hasta</label>
				<input type="date" class="form-control" id="filtroFechaHasta">
			</div>
			<div class="col-md-3">
				<label class="form-label fw-semibold">Buscar Folio</label>
				<input type="text" class="form-control" id="filtroFolio" placeholder="VTA-...">
			</div>
			<div class="col-md-3">
				<button class="btn btn-color_principal w-100" onclick="aplicarFiltros()">
					<i class="bi bi-search me-1"></i>Buscar
				</button>
			</div>
		</div>
	</div>
</div>

<!-- Tabla de Ventas -->
<div class="card table-card">
	<div class="card-header bg-white border-bottom">
		<div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
			<ul class="nav nav-tabs-custom" id="ventasTabs" role="tablist">
				<li class="nav-item" role="presentation">
					<button class="nav-link-custom active" id="all-tab" data-filter="all" type="button">
						Todas las Ventas
					</button>
				</li>
				<li class="nav-item" role="presentation">
					<button class="nav-link-custom" id="pending-tab" data-filter="pending" type="button">
						Pendientes de Pago
					</button>
				</li>
				<li class="nav-item" role="presentation">
					<button class="nav-link-custom" id="paid-tab" data-filter="paid" type="button">
						Pagadas
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
			<table id="tablaVentas" class="table table-modern align-middle mb-0">
				<thead>
					<tr>
						<th>FOLIO</th>
						<th>FECHA</th>
						<th>CLIENTE</th>
						<th>VENDEDOR</th>
						<th class="text-end">TOTAL</th>
						<th class="text-center">ESTADO PAGO</th>
						<th class="text-center" style="width: 150px;">ACCIONES</th>
					</tr>
				</thead>
				<tbody>
					<!-- Los datos se cargan por AJAX -->
				</tbody>
			</table>
		</div>
	</div>
</div>

<!-- Modal de confirmación para cancelar -->
<div class="modal fade" id="modalCancelar" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content border-0 shadow">
			<div class="modal-header border-0 pb-0">
				<h5 class="modal-title text-danger">
					<i class="bi bi-exclamation-triangle me-2"></i>Cancelar Venta
				</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<p>¿Estás seguro de que deseas cancelar la venta <strong id="folioVentaCancelar"></strong>?</p>
				<div class="alert alert-warning">
					<i class="bi bi-info-circle me-2"></i>
					Esta acción revertirá el stock de todos los productos de la venta.
				</div>
				<input type="hidden" id="idVentaCancelar">
			</div>
			<div class="modal-footer border-0 pt-0">
				<button type="button" class="btn btn-light" data-bs-dismiss="modal">No, mantener</button>
				<button type="button" class="btn btn-danger" onclick="confirmarCancelar()">
					<i class="bi bi-x-circle me-1"></i>Sí, cancelar
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
	font-size: 1.8rem;
	font-weight: 800;
	color: #1f2937;
	line-height: 1;
}
.stat-value-warning {
	color: var(--color_principal-500);
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
.stat-badge-warning {
	background: var(--color_principal-100);
	color: var(--color_principal-600);
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

/* Folio Badge */
.folio-badge {
	font-family: 'Courier New', monospace;
	font-weight: 700;
	color: var(--color_principal-600);
	background: var(--color_principal-50);
	padding: 6px 12px;
	border-radius: 8px;
	font-size: 0.85rem;
}

/* Estado Pago Badges */
.estado-pago {
	display: inline-flex;
	align-items: center;
	gap: 6px;
	padding: 6px 14px;
	border-radius: 20px;
	font-size: 0.75rem;
	font-weight: 600;
}
.estado-pago.pagada {
	background: #dcfce7;
	color: #16a34a;
}
.estado-pago.parcial {
	background: #fef3c7;
	color: #d97706;
}
.estado-pago.pendiente {
	background: #fee2e2;
	color: #dc2626;
}

/* Action Buttons */
.action-buttons {
	display: flex;
	gap: 6px;
	justify-content: center;
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
	text-decoration: none;
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
.action-btn.print:hover {
	border-color: #93c5fd;
	color: #2563eb;
	background: #eff6ff;
}

/* DataTable customizations */
.dt-layout-row {
	padding: 10px 15px !important;
}
.dt-layout-table {
	padding: 0 !important;
}
</style>

<script>
let tablaVentas;
let ventasData = [];
let filtroActivo = 'all';

$(document).ready(function() {
	// Establecer fechas por defecto (mes actual)
	let hoy = new Date();
	let primerDiaMes = new Date(hoy.getFullYear(), hoy.getMonth(), 1);
	$('#filtroFechaDesde').val(formatearFecha(primerDiaMes));
	$('#filtroFechaHasta').val(formatearFecha(hoy));
	
	inicializarTabla();
	cargarEstadisticas();
	
	// Tab filtering
	$('.nav-link-custom').on('click', function() {
		$('.nav-link-custom').removeClass('active');
		$(this).addClass('active');
		filtroActivo = $(this).data('filter');
		filtrarTabla(filtroActivo);
	});
});

function formatearFecha(fecha) {
	return fecha.toISOString().split('T')[0];
}

function inicializarTabla() {
	tablaVentas = $('#tablaVentas').DataTable({
		ajax: {
			url: IP_SERVER + 'ventas/listar',
			type: 'POST',
			data: function(d) {
				d.fecha_desde = $('#filtroFechaDesde').val();
				d.fecha_hasta = $('#filtroFechaHasta').val();
				d.folio = $('#filtroFolio').val();
			},
			dataSrc: function(json) {
				ventasData = json.data;
				return json.data;
			}
		},
		columns: [
			{ 
				data: 'folio_factura',
				render: function(data) {
					return `<span class="folio-badge">${data}</span>`;
				}
			},
			{ 
				data: 'fecha_venta',
				render: function(data) {
					let fecha = new Date(data);
					return fecha.toLocaleDateString('es-MX', {
						day: '2-digit',
						month: 'short',
						year: 'numeric',
						hour: '2-digit',
						minute: '2-digit'
					});
				}
			},
			{ 
				data: 'cliente_nombre',
				render: function(data) {
					return data || '<span class="text-muted">Sin cliente</span>';
				}
			},
			{ 
				data: 'vendedor_nombre',
				render: function(data) {
					return data || '<span class="text-muted">-</span>';
				}
			},
			{ 
				data: 'total_final',
				className: 'text-end fw-bold',
				render: function(data) {
					return '$' + parseFloat(data).toLocaleString('es-MX', {minimumFractionDigits: 2});
				}
			},
			{ 
				data: null,
				className: 'text-center',
				render: function(data, type, row) {
					let totalPagado = parseFloat(row.total_pagado) || 0;
					let totalFinal = parseFloat(row.total_final);
					let porcentaje = totalFinal > 0 ? (totalPagado / totalFinal * 100) : 0;
					
					let estado, clase;
					if (porcentaje >= 100) {
						estado = 'Pagada';
						clase = 'pagada';
					} else if (porcentaje > 0) {
						estado = 'Parcial ' + Math.round(porcentaje) + '%';
						clase = 'parcial';
					} else {
						estado = 'Pendiente';
						clase = 'pendiente';
					}
					
					return `<span class="estado-pago ${clase}">
						<i class="bi bi-${clase === 'pagada' ? 'check-circle' : clase === 'parcial' ? 'clock-history' : 'exclamation-circle'}"></i>
						${estado}
					</span>`;
				}
			},
			{ 
				data: 'id',
				className: 'text-center',
				orderable: false,
				render: function(data, type, row) {
					return `
						<div class="action-buttons">
							<a href="${IP_SERVER}ventas/ver/${data}" class="action-btn" title="Ver detalle">
								<i class="bi bi-eye"></i>
							</a>
							<a href="${IP_SERVER}ventas/imprimir/${data}" target="_blank" class="action-btn print" title="Imprimir">
								<i class="bi bi-printer"></i>
							</a>
							<button type="button" class="action-btn delete" title="Cancelar" 
								onclick="prepararCancelar(${data}, '${row.folio_factura}')">
								<i class="bi bi-x-lg"></i>
							</button>
						</div>
					`;
				}
			}
		],
		language: TABLA_CONFIGURACION.language,
		order: [[1, 'desc']],
		responsive: true,
		pageLength: 10,
		lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
		dom: '<"row mb-3"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row mt-3"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>'
	});
}

function cargarEstadisticas() {
	$.get(IP_SERVER + 'ventas/estadisticas', { periodo: 'mes' }, function(response) {
		if (response.success) {
			let stats = response.data;
			$('#totalVentas').text(stats.total_ventas);
			$('#montoTotal').text('$' + parseFloat(stats.monto_total).toLocaleString('es-MX', {minimumFractionDigits: 0, maximumFractionDigits: 0}));
			$('#ventasPendientes').text(stats.ventas_pendientes);
			$('#promedioVenta').text('$' + parseFloat(stats.promedio_venta).toLocaleString('es-MX', {minimumFractionDigits: 0, maximumFractionDigits: 0}));
		}
	});
}

function aplicarFiltros() {
	tablaVentas.ajax.reload();
}

function filtrarTabla(filter) {
	if (filter === 'all') {
		tablaVentas.search('').draw();
	} else if (filter === 'pending') {
		tablaVentas.search('Pendiente').draw();
	} else if (filter === 'paid') {
		tablaVentas.search('Pagada').draw();
	}
}

function recargarTabla() {
	tablaVentas.ajax.reload(null, false);
	cargarEstadisticas();
}

function exportarVentas() {
	let fechaDesde = $('#filtroFechaDesde').val();
	let fechaHasta = $('#filtroFechaHasta').val();
	window.location.href = IP_SERVER + 'ventas/exportar?fecha_desde=' + fechaDesde + '&fecha_hasta=' + fechaHasta;
}

function prepararCancelar(id, folio) {
	$('#idVentaCancelar').val(id);
	$('#folioVentaCancelar').text(folio);
	new bootstrap.Modal(document.getElementById('modalCancelar')).show();
}

function confirmarCancelar() {
	let id = $('#idVentaCancelar').val();
	
	$.ajax({
		url: IP_SERVER + 'ventas/cancelar',
		type: 'POST',
		data: { id: id },
		dataType: 'json',
		success: function(response) {
			bootstrap.Modal.getInstance(document.getElementById('modalCancelar')).hide();
			
			if (response.success) {
				Swal.fire({
					icon: 'success',
					title: '¡Cancelada!',
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
		error: function(xhr) {
			let msg = 'Ocurrió un error al procesar la solicitud';
			if (xhr.responseJSON && xhr.responseJSON.message) {
				msg = xhr.responseJSON.message;
			}
			Swal.fire({
				icon: 'error',
				title: 'Error',
				text: msg
			});
		}
	});
}
</script>
