<!-- Page Header -->
<div class="page-header mb-4">
	<div class="row align-items-center">
		<div class="col-lg-8">
			<h1 class="page-title">Facturación</h1>
			<p class="page-subtitle">Gestión de facturas de venta - Normativa DIAN Colombia</p>
		</div>
		<div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
			<a href="<?php echo IP_SERVER . 'facturacion/configuracion'; ?>" class="btn btn-outline-secondary me-2">
				<i class="bi bi-gear me-1"></i>Configuración
			</a>
			<a href="<?php echo IP_SERVER . 'facturacion/crear'; ?>" class="btn btn-color_principal">
				<i class="bi bi-plus-lg me-1"></i>Nueva Factura
			</a>
		</div>
	</div>
</div>

<!-- Stats Cards -->
<div class="row g-4 mb-4">
	<div class="col-sm-6 col-xl-3">
		<div class="stat-card">
			<div class="stat-content">
				<span class="stat-label">FACTURAS DEL MES</span>
				<div class="stat-value-row">
					<span class="stat-value" id="totalFacturas">0</span>
					<span class="stat-badge stat-badge-success"><i class="bi bi-receipt"></i></span>
				</div>
			</div>
		</div>
	</div>
	<div class="col-sm-6 col-xl-3">
		<div class="stat-card">
			<div class="stat-content">
				<span class="stat-label">MONTO FACTURADO</span>
				<div class="stat-value-row">
					<span class="stat-value" id="montoFacturado">$0</span>
				</div>
			</div>
		</div>
	</div>
	<div class="col-sm-6 col-xl-3">
		<div class="stat-card">
			<div class="stat-content">
				<span class="stat-label">TOTAL IVA</span>
				<div class="stat-value-row">
					<span class="stat-value" id="totalIva">$0</span>
				</div>
			</div>
		</div>
	</div>
	<div class="col-sm-6 col-xl-3">
		<div class="stat-card stat-card-warning">
			<div class="stat-content">
				<span class="stat-label stat-label-warning">ANULADAS</span>
				<div class="stat-value-row">
					<span class="stat-value stat-value-warning" id="facturasAnuladas">0</span>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Filtros -->
<div class="card mb-4">
	<div class="card-body">
		<div class="row g-3 align-items-end">
			<div class="col-md-2">
				<label class="form-label fw-semibold">Fecha Desde</label>
				<input type="date" class="form-control" id="filtroFechaDesde">
			</div>
			<div class="col-md-2">
				<label class="form-label fw-semibold">Fecha Hasta</label>
				<input type="date" class="form-control" id="filtroFechaHasta">
			</div>
			<div class="col-md-2">
				<label class="form-label fw-semibold">Estado</label>
				<select class="form-select" id="filtroEstado">
					<option value="">Todos</option>
					<option value="emitida">Emitidas</option>
					<option value="anulada">Anuladas</option>
				</select>
			</div>
			<div class="col-md-3">
				<label class="form-label fw-semibold">Buscar</label>
				<input type="text" class="form-control" id="filtroNumero" placeholder="N° factura o cliente...">
			</div>
			<div class="col-md-3">
				<button class="btn btn-color_principal w-100" onclick="aplicarFiltros()">
					<i class="bi bi-search me-1"></i>Buscar
				</button>
			</div>
		</div>
	</div>
</div>

<!-- Tabla -->
<div class="card table-card">
	<div class="card-body">
		<div class="table-responsive">
			<table class="table table-modern" id="tablaFacturas">
				<thead>
					<tr>
						<th>N° FACTURA</th>
						<th>FECHA</th>
						<th>CLIENTE</th>
						<th>NIT/CC</th>
						<th>SUBTOTAL</th>
						<th>IVA</th>
						<th>TOTAL</th>
						<th>ESTADO</th>
						<th style="width: 120px;">ACCIONES</th>
					</tr>
				</thead>
				<tbody id="bodyFacturas">
				</tbody>
			</table>
		</div>
	</div>
</div>

<script>
$(document).ready(function() {
	cargarEstadisticas();
	cargarFacturas();
});

function formatMoney(val) {
	return '$' + parseFloat(val || 0).toLocaleString('es-CO', {minimumFractionDigits: 0, maximumFractionDigits: 0});
}

function cargarEstadisticas() {
	$.get(IP_SERVER + 'facturacion/estadisticas?periodo=mes', function(res) {
		if (res.success) {
			$('#totalFacturas').text(res.data.total_facturas);
			$('#montoFacturado').text(formatMoney(res.data.monto_facturado));
			$('#totalIva').text(formatMoney(res.data.total_iva));
			$('#facturasAnuladas').text(res.data.facturas_anuladas);
		}
	});
}

function aplicarFiltros() {
	cargarFacturas();
}

function cargarFacturas() {
	$.post(IP_SERVER + 'facturacion/listar', {
		fecha_desde: $('#filtroFechaDesde').val(),
		fecha_hasta: $('#filtroFechaHasta').val(),
		estado: $('#filtroEstado').val(),
		numero_factura: $('#filtroNumero').val(),
		cliente: $('#filtroNumero').val()
	}, function(res) {
		if (res.success) {
			renderTabla(res.data);
		}
	});
}

function renderTabla(facturas) {
	let html = '';
	
	if (facturas.length === 0) {
		html = '<tr><td colspan="9" class="text-center py-5"><i class="bi bi-receipt display-4 text-muted d-block mb-3"></i><p class="text-muted">No se encontraron facturas</p></td></tr>';
	}
	
	facturas.forEach(function(f) {
		let estadoBadge = f.estado === 'emitida' 
			? '<span class="estado-badge pagada"><i class="bi bi-check-circle me-1"></i>Emitida</span>'
			: '<span class="estado-badge pendiente"><i class="bi bi-x-circle me-1"></i>Anulada</span>';
		
		let fecha = new Date(f.fecha_factura).toLocaleDateString('es-CO', {day:'2-digit', month:'2-digit', year:'numeric'});
		
		html += `<tr>
			<td><strong>${f.numero_factura}</strong></td>
			<td>${fecha}</td>
			<td>${f.cliente_razon_social}</td>
			<td>${f.cliente_nit_cc}</td>
			<td>${formatMoney(f.subtotal)}</td>
			<td>${formatMoney(f.total_iva)}</td>
			<td><strong>${formatMoney(f.total_final)}</strong></td>
			<td>${estadoBadge}</td>
			<td>
				<div class="btn-group btn-group-sm">
					<a href="${IP_SERVER}facturacion/ver/${f.id}" class="btn btn-outline-primary" title="Ver">
						<i class="bi bi-eye"></i>
					</a>
					<a href="${IP_SERVER}facturacion/pdf/${f.id}" target="_blank" class="btn btn-outline-secondary" title="PDF">
						<i class="bi bi-file-earmark-pdf"></i>
					</a>
					${f.estado === 'emitida' ? `<button class="btn btn-outline-danger" onclick="anularFactura(${f.id})" title="Anular"><i class="bi bi-x-lg"></i></button>` : ''}
				</div>
			</td>
		</tr>`;
	});
	
	$('#bodyFacturas').html(html);
}

function anularFactura(id) {
	Swal.fire({
		title: 'Anular Factura',
		html: '<p>Esta acción no se puede deshacer. Por normativa DIAN, las facturas anuladas quedan registradas.</p>' +
			  '<textarea id="motivoAnulacion" class="form-control mt-3" placeholder="Motivo de anulación (obligatorio)" rows="3"></textarea>',
		icon: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#dc3545',
		confirmButtonText: 'Sí, anular',
		cancelButtonText: 'Cancelar',
		preConfirm: () => {
			const motivo = document.getElementById('motivoAnulacion').value;
			if (!motivo.trim()) {
				Swal.showValidationMessage('Debe ingresar un motivo de anulación');
			}
			return motivo;
		}
	}).then((result) => {
		if (result.isConfirmed) {
			$.post(IP_SERVER + 'facturacion/anular', {
				id: id,
				motivo: result.value
			}, function(res) {
				if (res.success) {
					Swal.fire('Anulada', res.message, 'success');
					cargarEstadisticas();
					cargarFacturas();
				} else {
					Swal.fire('Error', res.message, 'error');
				}
			}).fail(function(xhr) {
				let msg = xhr.responseJSON ? xhr.responseJSON.message : 'Error al anular';
				Swal.fire('Error', msg, 'error');
			});
		}
	});
}
</script>
