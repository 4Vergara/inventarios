<!-- Page Header -->
<div class="page-header mb-4">
	<div class="row align-items-center">
		<div class="col">
			<nav aria-label="breadcrumb">
				<ol class="breadcrumb mb-2">
					<li class="breadcrumb-item"><a href="<?php echo IP_SERVER; ?>">Inicio</a></li>
					<li class="breadcrumb-item"><a href="<?php echo IP_SERVER . 'clientes'; ?>">Clientes</a></li>
					<li class="breadcrumb-item active">Detalle del Cliente</li>
				</ol>
			</nav>
			<div class="d-flex align-items-center gap-3">
				<h1 class="page-title mb-0"><?php echo $cliente->nombre_completo; ?></h1>
			</div>
		</div>
		<div class="col-auto">
			<button class="btn btn-outline-secondary me-2" onclick="editarCliente()">
				<i class="bi bi-pencil me-1"></i>Editar
			</button>
			<a href="<?php echo IP_SERVER . 'clientes'; ?>" class="btn btn-light">
				<i class="bi bi-arrow-left me-1"></i>Volver
			</a>
		</div>
	</div>
</div>

<div class="row">
	<!-- Columna Izquierda: Información del Cliente -->
	<div class="col-lg-4">
		<!-- Tarjeta de Información -->
		<div class="card mb-4">
			<div class="card-header bg-white">
				<h5 class="mb-0"><i class="bi bi-person me-2"></i>Información del Cliente</h5>
			</div>
			<div class="card-body">
				<div class="cliente-avatar-container text-center mb-4">
					<div class="cliente-avatar">
						<i class="bi bi-person-fill"></i>
					</div>
					<h5 class="mt-3 mb-1"><?php echo $cliente->nombre_completo; ?></h5>
					<p class="text-muted mb-0"><?php echo $cliente->numero_documento; ?></p>
				</div>
				
				<div class="info-list">
					<div class="info-item">
						<span class="info-label"><i class="bi bi-envelope me-2"></i>Correo Electrónico</span>
						<span class="info-value"><?php echo $cliente->correo_electronico ?: 'No registrado'; ?></span>
					</div>
					<div class="info-item">
						<span class="info-label"><i class="bi bi-calendar me-2"></i>Fecha de Registro</span>
						<span class="info-value"><?php echo date('d/m/Y', strtotime($cliente->fec_creacion)); ?></span>
					</div>
					<div class="info-item">
						<span class="info-label"><i class="bi bi-person-check me-2"></i>Registrado por</span>
						<span class="info-value"><?php echo $cliente->creado_por; ?></span>
					</div>
				</div>
			</div>
		</div>
		
		<!-- Tarjeta de Estadísticas -->
		<div class="card">
			<div class="card-header bg-white">
				<h5 class="mb-0"><i class="bi bi-graph-up me-2"></i>Resumen de Compras</h5>
			</div>
			<div class="card-body">
				<div class="stat-item">
					<div class="stat-icon bg-primary-soft">
						<i class="bi bi-cart-check"></i>
					</div>
					<div class="stat-info">
						<span class="stat-number"><?php echo $estadisticas->total_compras; ?></span>
						<span class="stat-label-small">Total de compras</span>
					</div>
				</div>
				
				<div class="stat-item">
					<div class="stat-icon bg-success-soft">
						<i class="bi bi-currency-dollar"></i>
					</div>
					<div class="stat-info">
						<span class="stat-number">$<?php echo number_format($estadisticas->monto_total, 2); ?></span>
						<span class="stat-label-small">Monto total comprado</span>
					</div>
				</div>
				
				<div class="stat-item">
					<div class="stat-icon bg-info-soft">
						<i class="bi bi-calendar-check"></i>
					</div>
					<div class="stat-info">
						<span class="stat-number">
							<?php echo $estadisticas->ultima_compra 
								? date('d/m/Y', strtotime($estadisticas->ultima_compra)) 
								: 'Sin compras'; ?>
						</span>
						<span class="stat-label-small">Última compra</span>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<!-- Columna Derecha: Historial de Compras -->
	<div class="col-lg-8">
		<div class="card">
			<div class="card-header bg-white d-flex justify-content-between align-items-center">
				<h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Historial de Compras</h5>
				<span class="badge bg-color_principal rounded-pill" id="totalComprasLabel">
					<?php echo $estadisticas->total_compras; ?> compras
				</span>
			</div>
			<div class="card-body p-0">
				<div class="table-responsive">
					<table id="tablaHistorial" class="table table-modern mb-0">
						<thead>
							<tr>
								<th>FOLIO</th>
								<th>FECHA</th>
								<th class="text-center">PRODUCTOS</th>
								<th class="text-end">TOTAL</th>
								<th class="text-center">ESTADO</th>
								<th class="text-center" style="width: 80px;">ACCIONES</th>
							</tr>
						</thead>
						<tbody id="historialBody">
							<!-- Se carga dinámicamente -->
						</tbody>
					</table>
				</div>
				
				<div id="sinCompras" class="text-center py-5 d-none">
					<i class="bi bi-cart-x display-4 text-muted d-block mb-3"></i>
					<p class="text-muted mb-0">Este cliente aún no tiene compras registradas</p>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Modal Editar Cliente -->
<div class="modal fade" id="modalEditarCliente" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Editar Cliente</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
			</div>
			<div class="modal-body">
				<form id="formEditarCliente">
					<input type="hidden" id="clienteId" value="<?php echo $cliente->id; ?>">
					
					<div class="mb-3">
						<label class="form-label fw-semibold">Nombre Completo <span class="text-danger">*</span></label>
						<input type="text" class="form-control" id="nombreCompleto" 
							value="<?php echo $cliente->nombre_completo; ?>" required maxlength="200">
					</div>
					
					<div class="mb-3">
						<label class="form-label fw-semibold">Número de Documento <span class="text-danger">*</span></label>
						<input type="text" class="form-control" id="numeroDocumento" 
							value="<?php echo $cliente->numero_documento; ?>" required maxlength="30">
					</div>
					
					<div class="mb-3">
						<label class="form-label fw-semibold">Correo Electrónico</label>
						<input type="email" class="form-control" id="correoElectronico" 
							value="<?php echo $cliente->correo_electronico; ?>" maxlength="100">
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
				<button type="button" class="btn btn-color_principal" onclick="guardarCambios()">
					<i class="bi bi-check-lg me-1"></i>Guardar Cambios
				</button>
			</div>
		</div>
	</div>
</div>

<style>
/* Page Header */
.page-title {
	font-size: 1.8rem;
	font-weight: 800;
	color: #1f2937;
}

/* Cliente Avatar */
.cliente-avatar-container {
	padding: 20px 0;
	border-bottom: 1px solid #f3f4f6;
}
.cliente-avatar {
	width: 80px;
	height: 80px;
	background: linear-gradient(135deg, var(--color_principal-500), var(--color_principal-600));
	border-radius: 50%;
	display: inline-flex;
	align-items: center;
	justify-content: center;
	color: white;
	font-size: 2rem;
}

/* Info List */
.info-list {
	padding-top: 16px;
}
.info-item {
	display: flex;
	flex-direction: column;
	padding: 12px 0;
	border-bottom: 1px solid #f3f4f6;
}
.info-item:last-child {
	border-bottom: none;
}
.info-label {
	font-size: 0.75rem;
	color: #6b7280;
	font-weight: 600;
	text-transform: uppercase;
	letter-spacing: 0.5px;
	margin-bottom: 4px;
}
.info-value {
	font-size: 0.95rem;
	color: #1f2937;
	font-weight: 500;
}

/* Stat Items */
.stat-item {
	display: flex;
	align-items: center;
	padding: 16px 0;
	border-bottom: 1px solid #f3f4f6;
}
.stat-item:last-child {
	border-bottom: none;
}
.stat-icon {
	width: 48px;
	height: 48px;
	border-radius: 12px;
	display: flex;
	align-items: center;
	justify-content: center;
	font-size: 1.25rem;
	margin-right: 16px;
}
.bg-primary-soft {
	background: rgba(99, 102, 241, 0.1);
	color: #6366f1;
}
.bg-success-soft {
	background: rgba(16, 185, 129, 0.1);
	color: #10b981;
}
.bg-info-soft {
	background: rgba(59, 130, 246, 0.1);
	color: #3b82f6;
}
.stat-info {
	display: flex;
	flex-direction: column;
}
.stat-number {
	font-size: 1.25rem;
	font-weight: 700;
	color: #1f2937;
}
.stat-label-small {
	font-size: 0.75rem;
	color: #6b7280;
}

/* Table */
.table-modern thead th {
	background: #f9fafb;
	font-size: 0.7rem;
	font-weight: 700;
	color: #6b7280;
	letter-spacing: 0.5px;
	text-transform: uppercase;
	padding: 14px 16px;
	border-bottom: 1px solid #e5e7eb;
}
.table-modern tbody td {
	padding: 16px;
	vertical-align: middle;
	border-bottom: 1px solid #f3f4f6;
}
.table-modern tbody tr:hover {
	background: #f9fafb;
}

/* Folio Link */
.folio-link {
	color: var(--color_principal-600);
	font-weight: 600;
	text-decoration: none;
}
.folio-link:hover {
	text-decoration: underline;
}

/* Estado Badges */
.estado-badge {
	padding: 6px 12px;
	border-radius: 8px;
	font-size: 0.75rem;
	font-weight: 600;
}
.estado-pagada {
	background: rgba(16, 185, 129, 0.1);
	color: #059669;
}
.estado-parcial {
	background: rgba(245, 158, 11, 0.1);
	color: #d97706;
}
.estado-pendiente {
	background: rgba(239, 68, 68, 0.1);
	color: #dc2626;
}

/* Action Button */
.btn-action {
	width: 32px;
	height: 32px;
	padding: 0;
	display: inline-flex;
	align-items: center;
	justify-content: center;
	border-radius: 8px;
	border: 1px solid #e5e7eb;
	background: white;
	color: #6b7280;
	transition: all 0.2s;
}
.btn-action:hover {
	background: rgba(59, 130, 246, 0.1);
	color: #3b82f6;
	border-color: #3b82f6;
}
</style>

<script>
let modalEditarCliente;
const clienteId = <?php echo $cliente->id; ?>;

$(document).ready(function() {
	modalEditarCliente = new bootstrap.Modal(document.getElementById('modalEditarCliente'));
	cargarHistorial();
});

function cargarHistorial() {
	$.get(IP_SERVER + 'clientes/historial/' + clienteId, function(response) {
		if (response.success) {
			renderizarHistorial(response.data);
		}
	}, 'json');
}

function renderizarHistorial(compras) {
	if (compras.length === 0) {
		$('#tablaHistorial').hide();
		$('#sinCompras').removeClass('d-none');
		return;
	}
	
	$('#tablaHistorial').show();
	$('#sinCompras').addClass('d-none');
	
	let tbody = '';
	
	compras.forEach(function(c) {
		let fecha = new Date(c.fecha_venta).toLocaleDateString('es-ES', {
			day: '2-digit',
			month: '2-digit',
			year: 'numeric',
			hour: '2-digit',
			minute: '2-digit'
		});
		
		let total = parseFloat(c.total_final).toLocaleString('es-ES', {style: 'currency', currency: 'USD'});
		let pagado = parseFloat(c.total_pagado);
		let saldo = parseFloat(c.saldo_pendiente);
		
		let estadoBadge = '';
		if (saldo <= 0) {
			estadoBadge = '<span class="estado-badge estado-pagada"><i class="bi bi-check-circle me-1"></i>Pagada</span>';
		} else if (pagado > 0) {
			let porcentaje = Math.round((pagado / parseFloat(c.total_final)) * 100);
			estadoBadge = `<span class="estado-badge estado-parcial"><i class="bi bi-clock-history me-1"></i>${porcentaje}%</span>`;
		} else {
			estadoBadge = '<span class="estado-badge estado-pendiente"><i class="bi bi-exclamation-circle me-1"></i>Pendiente</span>';
		}
		
		tbody += `
			<tr>
				<td>
					<a href="${IP_SERVER}ventas/ver/${c.id}" class="folio-link">${c.folio_factura}</a>
				</td>
				<td>${fecha}</td>
				<td class="text-center">
					<span class="badge bg-secondary rounded-pill">${c.total_productos}</span>
				</td>
				<td class="text-end fw-bold">${total}</td>
				<td class="text-center">${estadoBadge}</td>
				<td class="text-center">
					<a href="${IP_SERVER}ventas/ver/${c.id}" class="btn-action" title="Ver detalle">
						<i class="bi bi-eye"></i>
					</a>
				</td>
			</tr>
		`;
	});
	
	$('#historialBody').html(tbody);
	
	// Inicializar DataTable
	$('#tablaHistorial').DataTable({
		language: {
			url: IP_SERVER + 'assets/datatables/es-ES.json'
		},
		order: [[1, 'desc']],
		pageLength: 10,
		responsive: true
	});
}

function editarCliente() {
	modalEditarCliente.show();
}

function guardarCambios() {
	let nombre = $('#nombreCompleto').val().trim();
	let documento = $('#numeroDocumento').val().trim();
	
	if (!nombre || !documento) {
		Swal.fire('Atención', 'El nombre y el número de documento son requeridos', 'warning');
		return;
	}
	
	let datos = {
		id: clienteId,
		nombre_completo: nombre,
		numero_documento: documento,
		correo_electronico: $('#correoElectronico').val().trim()
	};
	
	$.post(IP_SERVER + 'clientes/guardar', datos, function(response) {
		if (response.success) {
			modalEditarCliente.hide();
			Swal.fire({
				title: 'Éxito',
				text: response.message,
				icon: 'success'
			}).then(() => {
				location.reload();
			});
		} else {
			Swal.fire('Error', response.message, 'error');
		}
	}, 'json');
}
</script>
