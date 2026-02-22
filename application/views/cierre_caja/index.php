<!-- Page Header -->
<div class="page-header mb-4">
	<div class="row align-items-center">
		<div class="col-lg-8">
			<h1 class="page-title">Cierre de Caja</h1>
			<p class="page-subtitle">Gestiona los cierres de caja por período y consulta el historial.</p>
		</div>
		<div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
			<a href="<?php echo IP_SERVER . 'cierre_caja/crear'; ?>" class="btn btn-color_principal">
				<i class="bi bi-cash-stack me-1"></i>Nuevo Cierre
			</a>
		</div>
	</div>
</div>

<!-- Stats Cards -->
<div class="row g-4 mb-4">
	<div class="col-sm-6 col-xl-3">
		<div class="stat-card">
			<div class="stat-content">
				<span class="stat-label">CIERRES REALIZADOS</span>
				<div class="stat-value-row">
					<span class="stat-value" id="totalCierres">0</span>
					<span class="stat-badge stat-badge-success">
						<i class="bi bi-check-circle"></i>
					</span>
				</div>
			</div>
		</div>
	</div>
	<div class="col-sm-6 col-xl-3">
		<div class="stat-card">
			<div class="stat-content">
				<span class="stat-label">TOTAL VENTAS REGISTRADAS</span>
				<div class="stat-value-row">
					<span class="stat-value" id="totalVentas">$0</span>
				</div>
			</div>
		</div>
	</div>
	<div class="col-sm-6 col-xl-3">
		<div class="stat-card">
			<div class="stat-content">
				<span class="stat-label">TOTAL IVA RECAUDADO</span>
				<div class="stat-value-row">
					<span class="stat-value" id="totalIva">$0</span>
				</div>
			</div>
		</div>
	</div>
	<div class="col-sm-6 col-xl-3">
		<div class="stat-card">
			<div class="stat-content">
				<span class="stat-label">ÚLTIMO CIERRE</span>
				<div class="stat-value-row">
					<span class="stat-value" id="ultimoCierre" style="font-size: 1rem;">Sin cierres</span>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Filtros -->
<div class="card card-modern mb-4">
	<div class="card-body">
		<div class="row g-3 align-items-end">
			<div class="col-md-3">
				<label class="form-label fw-semibold">Tipo de Período</label>
				<select class="form-select" id="filtroTipo">
					<option value="">Todos</option>
					<option value="dia">Diario</option>
					<option value="semana">Semanal</option>
					<option value="mes">Mensual</option>
					<option value="anio">Anual</option>
				</select>
			</div>
			<div class="col-md-3">
				<label class="form-label fw-semibold">Fecha Desde</label>
				<input type="date" class="form-control" id="filtroDesde">
			</div>
			<div class="col-md-3">
				<label class="form-label fw-semibold">Fecha Hasta</label>
				<input type="date" class="form-control" id="filtroHasta">
			</div>
			<div class="col-md-3 d-flex gap-2">
				<button class="btn btn-color_principal flex-grow-1" onclick="aplicarFiltros()">
					<i class="bi bi-search me-1"></i>Filtrar
				</button>
				<button class="btn btn-outline-secondary" onclick="limpiarFiltros()">
					<i class="bi bi-x-lg"></i>
				</button>
			</div>
		</div>
	</div>
</div>

<!-- Tabla -->
<div class="card table-card">
	<div class="card-header bg-white border-bottom">
		<div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
			<span class="fw-semibold text-muted">Listado de Cierres de Caja</span>
			<button class="btn btn-sm btn-outline-secondary" onclick="recargarTabla()">
				<i class="bi bi-arrow-clockwise me-1"></i>Actualizar
			</button>
		</div>
	</div>
	<div class="card-body p-0">
		<div class="table-responsive p-3">
			<table class="table table-modern align-middle mb-0" id="tablaCierres">
				<thead>
					<tr>
						<th>CÓDIGO</th>
						<th>PERÍODO</th>
						<th>RANGO FECHAS</th>
						<th class="text-center">VENTAS</th>
						<th class="text-end">TOTAL VENTAS</th>
						<th class="text-end">TOTAL IVA</th>
						<th class="text-end">DIFERENCIA</th>
						<th>REALIZADO POR</th>
						<th>FECHA CIERRE</th>
						<th class="text-center" style="width: 120px;">ACCIONES</th>
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
		</div>
	</div>
</div>


<script>
let tablaCierres;

$(document).ready(function() {
	cargarEstadisticas();
	inicializarTabla();
});

function formatMoney(n) {
	return '$' + parseFloat(n || 0).toLocaleString('es-CO', {minimumFractionDigits: 0, maximumFractionDigits: 0});
}

function getTipoLabel(tipo) {
	var labels = { dia: 'Diario', semana: 'Semanal', mes: 'Mensual', anio: 'Anual' };
	return labels[tipo] || tipo;
}

function formatDate(d) {
	if (!d) return '-';
	var datePart = d.split(' ')[0];
	var parts = datePart.split('-');
	return parts[2] + '/' + parts[1] + '/' + parts[0];
}

function formatDateTime(d) {
	if (!d) return '-';
	var dt = new Date(d);
	return dt.toLocaleDateString('es-CO') + ' ' + dt.toLocaleTimeString('es-CO', {hour: '2-digit', minute: '2-digit'});
}

function cargarEstadisticas() {
	$.post(IP_SERVER + 'cierre_caja/listar', {
		tipo_periodo: $('#filtroTipo').val(),
		fecha_desde: $('#filtroDesde').val(),
		fecha_hasta: $('#filtroHasta').val()
	}, function(res) {
		if (res.success) {
			actualizarEstadisticas(res.data);
		}
	}, 'json');
}

function actualizarEstadisticas(cierres) {
	if (!cierres || cierres.length === 0) {
		$('#totalCierres').text('0');
		$('#totalVentas').text('$0');
		$('#totalIva').text('$0');
		$('#ultimoCierre').text('Sin cierres');
		return;
	}

	$('#totalCierres').text(cierres.length);

	var totalV = 0, totalI = 0;
	cierres.forEach(function(c) {
		totalV += parseFloat(c.monto_total_vendido || 0);
		totalI += parseFloat(c.monto_impuestos || 0);
	});

	$('#totalVentas').text(formatMoney(totalV));
	$('#totalIva').text(formatMoney(totalI));
	$('#ultimoCierre').text(cierres[0].codigo_cierre || 'Sin cierres');
}

function inicializarTabla() {
	tablaCierres = $('#tablaCierres').DataTable({
		ajax: {
			url: IP_SERVER + 'cierre_caja/listar',
			type: 'POST',
			data: function(d) {
				d.tipo_periodo = $('#filtroTipo').val();
				d.fecha_desde = $('#filtroDesde').val();
				d.fecha_hasta = $('#filtroHasta').val();
			},
			dataSrc: function(json) {
				return json.data || [];
			}
		},
		columns: [
			{
				data: 'codigo_cierre',
				render: function(data) {
					return '<span class="folio-badge">' + data + '</span>';
				}
			},
			{
				data: 'tipo_periodo',
				render: function(data) {
					return '<span class="tipo-badge ' + data + '">' + getTipoLabel(data) + '</span>';
				}
			},
			{
				data: 'fecha_inicio',
				render: function(data, type, row) {
					return '<small>' + formatDate(data) + ' — ' + formatDate(row.fecha_fin) + '</small>';
				}
			},
			{
				data: 'total_ventas',
				className: 'text-center',
				render: function(data) {
					return data || 0;
				}
			},
			{
				data: 'monto_total_vendido',
				className: 'text-end fw-bold',
				render: function(data) { return formatMoney(data); }
			},
			{
				data: 'monto_impuestos',
				className: 'text-end',
				render: function(data) { return formatMoney(data); }
			},
			{
				data: 'diferencia_caja',
				className: 'text-end',
				render: function(data) {
					var dif = parseFloat(data || 0);
					var cls = dif > 0 ? 'text-success' : (dif < 0 ? 'text-danger' : '');
					var prefix = dif > 0 ? '+' : '';
					return '<span class="' + cls + '">' + prefix + formatMoney(dif) + '</span>';
				}
			},
			{
				data: 'creado_por',
				render: function(data) { return data || '-'; }
			},
			{
				data: 'fec_creacion',
				render: function(data) {
					return '<small>' + formatDateTime(data) + '</small>';
				}
			},
			{
				data: 'id',
				className: 'text-center',
				orderable: false,
				render: function(data) {
					return '<div class="action-buttons">' +
						'<a href="' + IP_SERVER + 'cierre_caja/ver/' + data + '" class="action-btn" title="Ver detalle"><i class="bi bi-eye"></i></a>' +
						'<a href="' + IP_SERVER + 'cierre_caja/pdf/' + data + '" target="_blank" class="action-btn print" title="Imprimir"><i class="bi bi-printer"></i></a>' +
						'</div>';
				}
			}
		],
		language: TABLA_CONFIGURACION.language,
		order: [[8, 'desc']],
		responsive: true,
		pageLength: 10,
		lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
		dom: '<"row mb-3"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row mt-3"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>'
	});
}

function aplicarFiltros() {
	tablaCierres.ajax.reload();
	cargarEstadisticas();
}

function recargarTabla() {
	tablaCierres.ajax.reload(null, false);
	cargarEstadisticas();
}

function limpiarFiltros() {
	$('#filtroTipo').val('');
	$('#filtroDesde').val('');
	$('#filtroHasta').val('');
	aplicarFiltros();
}
</script>
