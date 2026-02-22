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
	<div class="card-header bg-white border-bottom">
		<div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
			<span class="fw-semibold text-muted">Listado de Facturas</span>
			<button class="btn btn-sm btn-outline-secondary" onclick="recargarTabla()">
				<i class="bi bi-arrow-clockwise me-1"></i>Actualizar
			</button>
		</div>
	</div>
	<div class="card-body p-0">
		<div class="table-responsive p-3">
			<table class="table table-modern align-middle mb-0" id="tablaFacturas">
				<thead>
					<tr>
						<th>N° FACTURA</th>
						<th>FECHA</th>
						<th>CLIENTE</th>
						<th>NIT/CC</th>
						<th class="text-end">SUBTOTAL</th>
						<th class="text-end">IVA</th>
						<th class="text-end">TOTAL</th>
						<th class="text-center">ESTADO</th>
						<th class="text-center" style="width: 150px;">ACCIONES</th>
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
		</div>
	</div>
</div>


<script>
let tablaFacturas;

$(document).ready(function() {
	cargarEstadisticas();
	inicializarTabla();
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

function inicializarTabla() {
	tablaFacturas = $('#tablaFacturas').DataTable({
		ajax: {
			url: IP_SERVER + 'facturacion/listar',
			type: 'POST',
			data: function(d) {
				d.fecha_desde = $('#filtroFechaDesde').val();
				d.fecha_hasta = $('#filtroFechaHasta').val();
				d.estado = $('#filtroEstado').val();
				d.numero_factura = $('#filtroNumero').val();
				d.cliente = $('#filtroNumero').val();
			},
			dataSrc: function(json) {
				return json.data || [];
			}
		},
		columns: [
			{
				data: 'numero_factura',
				render: function(data) {
					return '<span class="folio-badge">' + data + '</span>';
				}
			},
			{
				data: 'fecha_factura',
				render: function(data) {
					let fecha = new Date(data);
					return fecha.toLocaleDateString('es-CO', {day: '2-digit', month: 'short', year: 'numeric'});
				}
			},
			{ data: 'cliente_razon_social' },
			{ data: 'cliente_nit_cc' },
			{
				data: 'subtotal',
				className: 'text-end',
				render: function(data) { return formatMoney(data); }
			},
			{
				data: 'total_iva',
				className: 'text-end',
				render: function(data) { return formatMoney(data); }
			},
			{
				data: 'total_final',
				className: 'text-end fw-bold',
				render: function(data) { return formatMoney(data); }
			},
			{
				data: 'estado',
				className: 'text-center',
				render: function(data) {
					if (data === 'emitida') {
						return '<span class="estado-pago pagada"><i class="bi bi-check-circle"></i> Emitida</span>';
					}
					return '<span class="estado-pago pendiente"><i class="bi bi-x-circle"></i> Anulada</span>';
				}
			},
			{
				data: 'id',
				className: 'text-center',
				orderable: false,
				render: function(data, type, row) {
					let anularBtn = row.estado === 'emitida'
						? '<button type="button" class="action-btn delete" title="Anular" onclick="anularFactura(' + data + ')"><i class="bi bi-x-lg"></i></button>'
						: '';
					return '<div class="action-buttons">' +
						'<a href="' + IP_SERVER + 'facturacion/ver/' + data + '" class="action-btn" title="Ver detalle"><i class="bi bi-eye"></i></a>' +
						'<a href="' + IP_SERVER + 'facturacion/pdf/' + data + '" target="_blank" class="action-btn print" title="PDF"><i class="bi bi-file-earmark-pdf"></i></a>' +
						anularBtn +
						'</div>';
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

function aplicarFiltros() {
	tablaFacturas.ajax.reload();
	cargarEstadisticas();
}

function recargarTabla() {
	tablaFacturas.ajax.reload(null, false);
	cargarEstadisticas();
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
					recargarTabla();
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
