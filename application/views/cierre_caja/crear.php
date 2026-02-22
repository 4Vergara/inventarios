<!-- Page Header -->
<div class="page-header mb-4">
	<div class="row align-items-center">
		<div class="col-lg-8">
			<h1 class="page-title">Nuevo Cierre de Caja</h1>
			<p class="page-subtitle">Selecciona el período y genera la vista previa antes de confirmar el cierre.</p>
		</div>
		<div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
			<a href="<?php echo IP_SERVER . 'cierre_caja'; ?>" class="btn btn-outline-secondary">
				<i class="bi bi-arrow-left me-1"></i>Volver al Listado
			</a>
		</div>
	</div>
</div>

<!-- Configuración del Cierre -->
<div class="row g-4">
	<div class="col-lg-4">
		<div class="card card-modern">
			<div class="card-header bg-transparent border-bottom">
				<h5 class="mb-0"><i class="bi bi-gear me-2"></i>Configuración</h5>
			</div>
			<div class="card-body">
				<div class="mb-3">
					<label class="form-label fw-semibold">Tipo de Período <span class="text-danger">*</span></label>
					<select class="form-select" id="tipoPeriodo">
						<option value="">Seleccione...</option>
						<option value="dia">Diario</option>
						<option value="semana">Semanal</option>
						<option value="mes">Mensual</option>
						<option value="anio">Anual</option>
					</select>
					<div class="form-text" id="periodoHelp"></div>
				</div>
				<div class="mb-3">
					<label class="form-label fw-semibold">Fecha de Referencia <span class="text-danger">*</span></label>
					<input type="date" class="form-control" id="fechaReferencia" value="<?php echo date('Y-m-d'); ?>">
					<div class="form-text">El rango se calculará automáticamente según el tipo de período.</div>
				</div>
				<div class="mb-3">
					<label class="form-label fw-semibold">Efectivo Inicial en Caja</label>
					<div class="input-group">
						<span class="input-group-text">$</span>
						<input type="number" class="form-control" id="efectivoInicial" value="0" min="0" step="100">
					</div>
					<div class="form-text">Monto de efectivo con el que se abrió la caja.</div>
				</div>
				<div class="mb-3">
					<label class="form-label fw-semibold">Efectivo Contado (Real)</label>
					<div class="input-group">
						<span class="input-group-text">$</span>
						<input type="number" class="form-control" id="efectivoContado" value="0" min="0" step="100">
					</div>
					<div class="form-text">Monto de efectivo contado físicamente al cierre.</div>
				</div>
				<div class="mb-3">
					<label class="form-label fw-semibold">Observaciones</label>
					<textarea class="form-control" id="observaciones" rows="3" placeholder="Notas importantes del cierre..."></textarea>
				</div>
				
				<button class="btn btn-color_principal w-100 mb-2" onclick="cargarPreview()" id="btnPreview">
					<i class="bi bi-eye me-1"></i>Generar Vista Previa
				</button>
			</div>
		</div>
	</div>
	
	<!-- Vista Previa -->
	<div class="col-lg-8">
		<div id="previewContainer" style="display: none;">
			<!-- Rango del período -->
			<div class="alert alert-info d-flex align-items-center mb-4">
				<i class="bi bi-calendar-range me-2 fs-5"></i>
				<div>
					<strong>Período:</strong> <span id="prevTipoPeriodo"></span><br>
					<span id="prevRango"></span>
				</div>
			</div>
			
			<!-- Resumen General -->
			<div class="row g-3 mb-4">
				<div class="col-sm-6 col-md-3">
					<div class="card card-modern text-center">
						<div class="card-body py-3">
							<small class="text-muted d-block">Total Ventas</small>
							<strong class="fs-5 text-primary" id="prevTotalVentas">0</strong>
						</div>
					</div>
				</div>
				<div class="col-sm-6 col-md-3">
					<div class="card card-modern text-center">
						<div class="card-body py-3">
							<small class="text-muted d-block">Monto Ventas</small>
							<strong class="fs-5 text-success" id="prevMontoVentas">$0</strong>
						</div>
					</div>
				</div>
				<div class="col-sm-6 col-md-3">
					<div class="card card-modern text-center">
						<div class="card-body py-3">
							<small class="text-muted d-block">Total IVA</small>
							<strong class="fs-5" id="prevTotalIva">$0</strong>
						</div>
					</div>
				</div>
				<div class="col-sm-6 col-md-3">
					<div class="card card-modern text-center">
						<div class="card-body py-3">
							<small class="text-muted d-block">Anuladas</small>
							<strong class="fs-5 text-danger" id="prevAnuladas">0</strong>
						</div>
					</div>
				</div>
			</div>
			
			<!-- Detalle por método de pago -->
			<div class="card card-modern mb-4">
				<div class="card-header bg-transparent border-bottom">
					<h6 class="mb-0"><i class="bi bi-wallet2 me-2"></i>Desglose por Método de Pago</h6>
				</div>
				<div class="card-body p-0">
					<div class="table-responsive">
						<table class="table table-modern mb-0">
							<thead>
								<tr>
									<th>Método de Pago</th>
									<th class="text-end">Total</th>
								</tr>
							</thead>
							<tbody id="prevMetodosPago">
							</tbody>
						</table>
					</div>
				</div>
			</div>
			
			<!-- Diferencia de Caja -->
			<div class="card card-modern mb-4">
				<div class="card-header bg-transparent border-bottom">
					<h6 class="mb-0"><i class="bi bi-cash-coin me-2"></i>Cuadre de Caja (Efectivo)</h6>
				</div>
				<div class="card-body">
					<div class="row text-center">
						<div class="col-md-3">
							<small class="text-muted d-block">Efectivo Inicial</small>
							<strong id="prevEfectivoInicial">$0</strong>
						</div>
						<div class="col-md-3">
							<small class="text-muted d-block">Ventas en Efectivo</small>
							<strong id="prevVentasEfectivo">$0</strong>
						</div>
						<div class="col-md-3">
							<small class="text-muted d-block">Efectivo Esperado</small>
							<strong id="prevEfectivoEsperado">$0</strong>
						</div>
						<div class="col-md-3">
							<small class="text-muted d-block">Diferencia</small>
							<strong id="prevDiferencia" class="fs-5">$0</strong>
						</div>
					</div>
				</div>
			</div>
			
			<!-- Listado de ventas incluidas -->
			<div class="card card-modern mb-4">
				<div class="card-header bg-transparent border-bottom">
					<h6 class="mb-0"><i class="bi bi-receipt me-2"></i>Ventas Incluidas en el Cierre</h6>
				</div>
				<div class="card-body p-0">
					<div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
						<table class="table table-modern table-sm mb-0">
							<thead>
								<tr>
									<th>Folio</th>
									<th>Cliente</th>
									<th>Fecha</th>
									<th>Método Pago</th>
									<th class="text-end">Total</th>
									<th class="text-center">Estado</th>
								</tr>
							</thead>
							<tbody id="prevVentasDetalle">
							</tbody>
						</table>
					</div>
				</div>
			</div>
			
			<!-- Botón confirmar -->
			<div class="text-end">
				<button class="btn btn-lg btn-color_principal" onclick="confirmarCierre()" id="btnConfirmar">
					<i class="bi bi-lock me-1"></i>Confirmar y Guardar Cierre
				</button>
			</div>
		</div>
		
		<!-- Placeholder cuando no hay preview -->
		<div id="placeholderPreview" class="text-center py-5">
			<i class="bi bi-cash-stack" style="font-size: 4rem; color: #ddd;"></i>
			<p class="text-muted mt-3">Selecciona un tipo de período y genera la vista previa<br>para ver el resumen del cierre de caja.</p>
		</div>
	</div>
</div>

<script>
let previewData = null;

function formatMoney(n) {
	return '$' + parseFloat(n || 0).toLocaleString('es-CO', {minimumFractionDigits: 0, maximumFractionDigits: 0});
}

function formatDate(d) {
	if (!d) return '-';
	var datePart = d.split(' ')[0];
	var parts = datePart.split('-');
	return parts[2] + '/' + parts[1] + '/' + parts[0];
}

$('#tipoPeriodo').on('change', function(){
	const tipo = $(this).val();
	const helps = {
		dia: 'Se cerrará el día seleccionado en la fecha de referencia.',
		semana: 'Se cerrará la semana completa (lunes a domingo) que contiene la fecha seleccionada.',
		mes: 'Se cerrará el mes completo de la fecha seleccionada.',
		anio: 'Se cerrará el año completo de la fecha seleccionada.'
	};
	$('#periodoHelp').text(helps[tipo] || '');
});

function cargarPreview() {
	let tipo = $('#tipoPeriodo').val();
	let fecha = $('#fechaReferencia').val();
	
	if (!tipo) {
		Swal.fire('Atención', 'Selecciona un tipo de período.', 'warning');
		return;
	}
	if (!fecha) {
		Swal.fire('Atención', 'Selecciona una fecha de referencia.', 'warning');
		return;
	}
	
	$('#btnPreview').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Calculando...');
	
	$.post(IP_SERVER + 'cierre_caja/preview', {
		tipo_periodo: tipo,
		fecha_referencia: fecha
	}, function(res){
		$('#btnPreview').prop('disabled', false).html('<i class="bi bi-eye me-1"></i>Generar Vista Previa');
		
		if (res.success) {
			previewData = res.data;
			mostrarPreview(res.data);
		} else {
			Swal.fire('Error', res.message, 'error');
		}
	}, 'json').fail(function(){
		$('#btnPreview').prop('disabled', false).html('<i class="bi bi-eye me-1"></i>Generar Vista Previa');
		Swal.fire('Error', 'No se pudo conectar con el servidor.', 'error');
	});
}

function mostrarPreview(data) {
	$('#placeholderPreview').hide();
	$('#previewContainer').show();
	
	// Labels del tipo
	const tipoLabels = { dia: 'Diario', semana: 'Semanal', mes: 'Mensual', anio: 'Anual' };
	$('#prevTipoPeriodo').text(tipoLabels[$('#tipoPeriodo').val()] || '');
	$('#prevRango').html('<strong>Desde:</strong> ' + formatDate(data.fecha_inicio) + ' &nbsp;&mdash;&nbsp; <strong>Hasta:</strong> ' + formatDate(data.fecha_fin));
	
	// Resumen
	$('#prevTotalVentas').text(data.total_ventas || 0);
	$('#prevMontoVentas').text(formatMoney(data.monto_total_vendido));
	$('#prevTotalIva').text(formatMoney(data.monto_impuestos));
	$('#prevAnuladas').text(data.ventas_anuladas || 0);
	
	// Métodos de pago
	let htmlMetodos = '';
	let metodosPago = [
		{ nombre: 'Efectivo', total: parseFloat(data.total_efectivo || 0) },
		{ nombre: 'Tarjeta de Crédito', total: parseFloat(data.total_tarjeta_credito || 0) },
		{ nombre: 'Tarjeta de Débito', total: parseFloat(data.total_tarjeta_debito || 0) },
		{ nombre: 'Transferencia', total: parseFloat(data.total_transferencia || 0) },
		{ nombre: 'Cheque', total: parseFloat(data.total_cheque || 0) }
	];
	metodosPago.forEach(function(mp) {
		if (mp.total > 0) {
			htmlMetodos += '<tr>' +
				'<td><i class="bi bi-credit-card me-1"></i>' + mp.nombre + '</td>' +
				'<td class="text-end"><strong>' + formatMoney(mp.total) + '</strong></td>' +
			'</tr>';
		}
	});
	$('#prevMetodosPago').html(htmlMetodos || '<tr><td colspan="2" class="text-center text-muted py-3">Sin datos de pago</td></tr>');
	
	// Cuadre de caja
	let efectivoInicial = parseFloat($('#efectivoInicial').val()) || 0;
	let efectivoContado = parseFloat($('#efectivoContado').val()) || 0;
	let ventasEfectivo = parseFloat(data.total_efectivo) || 0;
	let esperado = efectivoInicial + ventasEfectivo;
	let diferencia = efectivoContado - esperado;
	
	$('#prevEfectivoInicial').text(formatMoney(efectivoInicial));
	$('#prevVentasEfectivo').text(formatMoney(ventasEfectivo));
	$('#prevEfectivoEsperado').text(formatMoney(esperado));
	
	let difClass = diferencia > 0 ? 'text-success' : (diferencia < 0 ? 'text-danger' : 'text-muted');
	let difPrefix = diferencia > 0 ? '+' : '';
	$('#prevDiferencia').attr('class', 'fs-5 ' + difClass).text(difPrefix + formatMoney(diferencia));
	
	// Ventas detalle
	let htmlVentas = '';
	if (data.ventas && data.ventas.length > 0) {
		data.ventas.forEach(function(v){
			htmlVentas += '<tr>' +
				'<td>' + (v.folio_factura || '-') + '</td>' +
				'<td>' + (v.cliente_nombre || 'Sin cliente') + '</td>' +
				'<td>' + formatDate(v.fecha_venta) + '</td>' +
				'<td>' + (v.metodos_pago || '-') + '</td>' +
				'<td class="text-end">' + formatMoney(v.total_final) + '</td>' +
				'<td class="text-center"><span class="badge bg-success">Registrada</span></td>' +
			'</tr>';
		});
	}
	$('#prevVentasDetalle').html(htmlVentas || '<tr><td colspan="6" class="text-center text-muted py-3">No hay ventas en este período</td></tr>');
}

// Recalcular cuadre al cambiar efectivo
$('#efectivoInicial, #efectivoContado').on('input', function(){
	if (previewData) {
		let efectivoInicial = parseFloat($('#efectivoInicial').val()) || 0;
		let efectivoContado = parseFloat($('#efectivoContado').val()) || 0;
		let ventasEfectivo = parseFloat(previewData.total_efectivo) || 0;
		let esperado = efectivoInicial + ventasEfectivo;
		let diferencia = efectivoContado - esperado;
		
		$('#prevEfectivoInicial').text(formatMoney(efectivoInicial));
		$('#prevEfectivoEsperado').text(formatMoney(esperado));
		
		let difClass = diferencia > 0 ? 'text-success' : (diferencia < 0 ? 'text-danger' : 'text-muted');
		let difPrefix = diferencia > 0 ? '+' : '';
		$('#prevDiferencia').attr('class', 'fs-5 ' + difClass).text(difPrefix + formatMoney(diferencia));
	}
});

function confirmarCierre() {
	if (!previewData) {
		Swal.fire('Atención', 'Primero genera la vista previa.', 'warning');
		return;
	}
	
	Swal.fire({
		title: '¿Confirmar cierre de caja?',
		html: 'Se registrará el cierre con <strong>' + (previewData.total_ventas || 0) + ' ventas</strong> por un total de <strong>' + formatMoney(previewData.monto_total_vendido) + '</strong>.<br><br><em>Esta acción no se puede deshacer.</em>',
		icon: 'question',
		showCancelButton: true,
		confirmButtonColor: '#2c3e50',
		confirmButtonText: '<i class="bi bi-lock me-1"></i>Confirmar Cierre',
		cancelButtonText: 'Cancelar'
	}).then((result) => {
		if (result.isConfirmed) {
			ejecutarCierre();
		}
	});
}

function ejecutarCierre() {
	$('#btnConfirmar').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Guardando...');
	
	$.post(IP_SERVER + 'cierre_caja/guardar', {
		tipo_periodo: $('#tipoPeriodo').val(),
		fecha_referencia: $('#fechaReferencia').val(),
		efectivo_inicial: $('#efectivoInicial').val(),
		efectivo_contado: $('#efectivoContado').val(),
		observaciones: $('#observaciones').val()
	}, function(res){
		$('#btnConfirmar').prop('disabled', false).html('<i class="bi bi-lock me-1"></i>Confirmar y Guardar Cierre');
		
		if (res.success) {
			Swal.fire({
				title: '¡Cierre Registrado!',
				html: 'Código: <strong>' + (res.codigo || '') + '</strong>',
				icon: 'success',
				confirmButtonColor: '#2c3e50',
				confirmButtonText: 'Ver Detalle'
			}).then(() => {
				window.location.href = IP_SERVER + 'cierre_caja/ver/' + res.id;
			});
		} else {
			Swal.fire('Error', res.message, 'error');
		}
	}, 'json').fail(function(){
		$('#btnConfirmar').prop('disabled', false).html('<i class="bi bi-lock me-1"></i>Confirmar y Guardar Cierre');
		Swal.fire('Error', 'No se pudo conectar con el servidor.', 'error');
	});
}
</script>
