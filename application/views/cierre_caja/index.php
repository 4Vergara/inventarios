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
				<button class="btn btn-color_principal flex-grow-1" onclick="cargarCierres()">
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
<div class="card card-modern">
	<div class="card-body p-0">
		<div class="table-responsive">
			<table class="table table-modern mb-0" id="tablaCierres">
				<thead>
					<tr>
						<th>Código</th>
						<th>Período</th>
						<th>Rango Fechas</th>
						<th>Ventas</th>
						<th class="text-end">Total Ventas</th>
						<th class="text-end">Total IVA</th>
						<th class="text-end">Diferencia</th>
						<th>Realizado por</th>
						<th>Fecha Cierre</th>
						<th class="text-center">Acciones</th>
					</tr>
				</thead>
				<tbody id="bodyCierres">
					<tr>
						<td colspan="10" class="text-center text-muted py-4">Cargando...</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>

<script>
const IP_SERVER = '<?php echo IP_SERVER; ?>';

$(document).ready(function(){
	cargarCierres();
});

function formatMoney(n) {
	return '$' + parseFloat(n || 0).toLocaleString('es-CO', {minimumFractionDigits: 0, maximumFractionDigits: 0});
}

function getTipoLabel(tipo) {
	const labels = { dia: 'Diario', semana: 'Semanal', mes: 'Mensual', anio: 'Anual' };
	return labels[tipo] || tipo;
}

function getTipoBadge(tipo) {
	const colors = { dia: 'primary', semana: 'info', mes: 'success', anio: 'warning' };
	return `<span class="badge bg-${colors[tipo] || 'secondary'}">${getTipoLabel(tipo)}</span>`;
}

function cargarCierres() {
	let datos = {
		tipo_periodo: $('#filtroTipo').val(),
		fecha_desde: $('#filtroDesde').val(),
		fecha_hasta: $('#filtroHasta').val()
	};
	
	$.post(IP_SERVER + 'cierre_caja/listar', datos, function(res){
		if (res.success) {
			renderTabla(res.data);
			actualizarEstadisticas(res.data);
		} else {
			$('#bodyCierres').html('<tr><td colspan="10" class="text-center text-muted py-4">' + res.message + '</td></tr>');
		}
	}, 'json').fail(function(){
		$('#bodyCierres').html('<tr><td colspan="10" class="text-center text-danger py-4">Error al cargar datos</td></tr>');
	});
}

function renderTabla(cierres) {
	if (!cierres || cierres.length === 0) {
		$('#bodyCierres').html('<tr><td colspan="10" class="text-center text-muted py-4"><i class="bi bi-inbox me-2"></i>No se encontraron cierres de caja</td></tr>');
		return;
	}
	
	let html = '';
	cierres.forEach(function(c){
		let diferencia = parseFloat(c.diferencia_caja || 0);
		let difClass = diferencia > 0 ? 'text-success' : (diferencia < 0 ? 'text-danger' : '');
		let difPrefix = diferencia > 0 ? '+' : '';
		
		html += `<tr>
			<td><strong>${c.codigo_cierre}</strong></td>
			<td>${getTipoBadge(c.tipo_periodo)}</td>
			<td>
				<small>${formatDate(c.fecha_inicio)} — ${formatDate(c.fecha_fin)}</small>
			</td>
			<td class="text-center">${c.total_ventas_count || 0}</td>
			<td class="text-end"><strong>${formatMoney(c.total_ventas)}</strong></td>
			<td class="text-end">${formatMoney(c.total_iva)}</td>
			<td class="text-end"><span class="${difClass}">${difPrefix}${formatMoney(diferencia)}</span></td>
			<td>${c.creado_por || '-'}</td>
			<td><small>${formatDateTime(c.fec_creacion)}</small></td>
			<td class="text-center">
				<div class="btn-group btn-group-sm">
					<a href="${IP_SERVER}cierre_caja/ver/${c.id}" class="btn btn-outline-primary" title="Ver detalle">
						<i class="bi bi-eye"></i>
					</a>
					<a href="${IP_SERVER}cierre_caja/pdf/${c.id}" class="btn btn-outline-secondary" title="Imprimir" target="_blank">
						<i class="bi bi-printer"></i>
					</a>
				</div>
			</td>
		</tr>`;
	});
	
	$('#bodyCierres').html(html);
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
	
	let totalV = 0, totalI = 0;
	cierres.forEach(function(c){
		totalV += parseFloat(c.total_ventas || 0);
		totalI += parseFloat(c.total_iva || 0);
	});
	
	$('#totalVentas').text(formatMoney(totalV));
	$('#totalIva').text(formatMoney(totalI));
	$('#ultimoCierre').text(cierres[0].codigo_cierre || 'Sin cierres');
}

function formatDate(d) {
	if (!d) return '-';
	const parts = d.split('-');
	return parts[2] + '/' + parts[1] + '/' + parts[0];
}

function formatDateTime(d) {
	if (!d) return '-';
	const dt = new Date(d);
	return dt.toLocaleDateString('es-CO') + ' ' + dt.toLocaleTimeString('es-CO', {hour: '2-digit', minute: '2-digit'});
}

function limpiarFiltros() {
	$('#filtroTipo').val('');
	$('#filtroDesde').val('');
	$('#filtroHasta').val('');
	cargarCierres();
}
</script>
