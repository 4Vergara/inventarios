<!-- Page Header -->
<div class="page-header mb-4">
	<div class="row align-items-center">
		<div class="col">
			<nav aria-label="breadcrumb">
				<ol class="breadcrumb mb-2">
					<li class="breadcrumb-item"><a href="<?php echo IP_SERVER; ?>">Inicio</a></li>
					<li class="breadcrumb-item"><a href="<?php echo IP_SERVER . 'facturacion'; ?>">Facturación</a></li>
					<li class="breadcrumb-item active">Nueva Factura</li>
				</ol>
			</nav>
			<h1 class="page-title">Generar Factura</h1>
			<p class="page-subtitle">Genere una factura formal a partir de una venta existente</p>
		</div>
		<div class="col-auto">
			<a href="<?php echo IP_SERVER . 'facturacion'; ?>" class="btn btn-light">
				<i class="bi bi-arrow-left me-1"></i>Volver
			</a>
		</div>
	</div>
</div>

<div class="row">
	<!-- Columna izquierda: Selección de venta -->
	<div class="col-lg-5">
		<div class="card mb-4">
			<div class="card-header bg-white">
				<h5 class="mb-0"><i class="bi bi-cart-check me-2"></i>Seleccionar Venta</h5>
			</div>
			<div class="card-body">
				<?php if (isset($venta) && $venta): ?>
					<!-- Venta pre-seleccionada -->
					<div class="alert alert-info">
						<i class="bi bi-info-circle me-1"></i>
						Venta seleccionada: <strong><?php echo $venta->folio_factura; ?></strong>
					</div>
					<input type="hidden" id="idVentaSeleccionada" value="<?php echo $venta->id; ?>">
				<?php else: ?>
					<div class="mb-3">
						<label class="form-label fw-semibold">Ventas sin facturar</label>
						<select class="form-select" id="selectVenta" onchange="cargarDatosVenta()">
							<option value="">-- Seleccione una venta --</option>
							<?php if (isset($ventas_sin_factura)): ?>
							<?php foreach ($ventas_sin_factura as $v): ?>
								<option value="<?php echo $v->id; ?>" 
									data-cliente="<?php echo htmlspecialchars($v->cliente_nombre); ?>"
									data-documento="<?php echo $v->cliente_documento; ?>"
									data-total="<?php echo $v->total_final; ?>">
									<?php echo $v->folio_factura; ?> - <?php echo $v->cliente_nombre; ?> - $<?php echo number_format($v->total_final, 0, ',', '.'); ?>
								</option>
							<?php endforeach; ?>
							<?php endif; ?>
						</select>
					</div>
				<?php endif; ?>
				
				<!-- Detalle de la venta seleccionada -->
				<div id="detalleVentaContainer" style="display: none;">
					<hr>
					<h6 class="fw-semibold mb-3">Detalle de la Venta</h6>
					<div class="info-item">
						<span class="info-label">Folio</span>
						<span class="info-value" id="ventaFolio">-</span>
					</div>
					<div class="info-item">
						<span class="info-label">Fecha</span>
						<span class="info-value" id="ventaFecha">-</span>
					</div>
					<div class="info-item">
						<span class="info-label">Vendedor</span>
						<span class="info-value" id="ventaVendedor">-</span>
					</div>
					
					<div class="table-responsive mt-3">
						<table class="table table-sm table-bordered">
							<thead class="table-light">
								<tr>
									<th>Producto</th>
									<th class="text-center">Cant.</th>
									<th class="text-end">Precio</th>
									<th class="text-end">IVA</th>
									<th class="text-end">Subtotal</th>
								</tr>
							</thead>
							<tbody id="ventaProductos"></tbody>
						</table>
					</div>
					
					<div class="border-top pt-3 mt-2">
						<div class="d-flex justify-content-between mb-1">
							<span>Subtotal:</span>
							<strong id="ventaSubtotal">$0</strong>
						</div>
						<div class="d-flex justify-content-between mb-1">
							<span>IVA:</span>
							<strong id="ventaImpuestos">$0</strong>
						</div>
						<div class="d-flex justify-content-between mb-1">
							<span>Descuentos:</span>
							<strong id="ventaDescuentos">$0</strong>
						</div>
						<div class="d-flex justify-content-between fs-5">
							<span>Total:</span>
							<strong class="text-success" id="ventaTotal">$0</strong>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<!-- Columna derecha: Datos de facturación -->
	<div class="col-lg-7">
		<!-- Datos del emisor -->
		<div class="card mb-4">
			<div class="card-header bg-white">
				<h5 class="mb-0"><i class="bi bi-building me-2"></i>Datos del Emisor</h5>
			</div>
			<div class="card-body">
				<?php if (isset($emisor) && $emisor): ?>
				<div class="row">
					<div class="col-md-6">
						<div class="info-item">
							<span class="info-label">Razón Social</span>
							<span class="info-value"><?php echo $emisor->razon_social; ?></span>
						</div>
						<div class="info-item">
							<span class="info-label">NIT</span>
							<span class="info-value"><?php echo $emisor->nit; ?></span>
						</div>
					</div>
					<div class="col-md-6">
						<div class="info-item">
							<span class="info-label">Dirección</span>
							<span class="info-value"><?php echo $emisor->direccion; ?></span>
						</div>
						<div class="info-item">
							<span class="info-label">Régimen</span>
							<span class="info-value"><?php echo $emisor->regimen; ?></span>
						</div>
					</div>
				</div>
				<div class="info-item">
					<span class="info-label">Resolución DIAN</span>
					<span class="info-value"><?php echo $emisor->resolucion_dian; ?> (<?php echo $emisor->fecha_resolucion; ?>)</span>
				</div>
				<small class="text-muted">Rango autorizado: <?php echo $emisor->prefijo_factura; ?> <?php echo $emisor->rango_desde; ?> al <?php echo $emisor->rango_hasta; ?> | Consecutivo actual: <?php echo $emisor->consecutivo_actual; ?></small>
				<?php else: ?>
				<div class="alert alert-warning mb-0">
					<i class="bi bi-exclamation-triangle me-1"></i>
					Debe configurar los datos del emisor antes de facturar.
					<a href="<?php echo IP_SERVER . 'facturacion/configuracion'; ?>">Configurar ahora</a>
				</div>
				<?php endif; ?>
			</div>
		</div>
		
		<!-- Datos del cliente para la factura -->
		<div class="card mb-4">
			<div class="card-header bg-white">
				<h5 class="mb-0"><i class="bi bi-person-vcard me-2"></i>Datos del Cliente (Factura)</h5>
			</div>
			<div class="card-body">
				<div class="row g-3">
					<div class="col-md-8">
						<label class="form-label fw-semibold">Razón Social / Nombre <span class="text-danger">*</span></label>
						<input type="text" class="form-control" id="clienteRazonSocial" placeholder="Nombre o razón social">
					</div>
					<div class="col-md-4">
						<label class="form-label fw-semibold">NIT / CC <span class="text-danger">*</span></label>
						<input type="text" class="form-control" id="clienteNitCc" placeholder="NIT o C.C.">
					</div>
					<div class="col-md-6">
						<label class="form-label fw-semibold">Dirección</label>
						<input type="text" class="form-control" id="clienteDireccion" placeholder="Dirección">
					</div>
					<div class="col-md-6">
						<label class="form-label fw-semibold">Correo Electrónico</label>
						<input type="email" class="form-control" id="clienteCorreo" placeholder="correo@ejemplo.com">
					</div>
					<div class="col-md-4">
						<label class="form-label fw-semibold">Teléfono</label>
						<input type="text" class="form-control" id="clienteTelefono" placeholder="Teléfono">
					</div>
					<div class="col-md-4">
						<label class="form-label fw-semibold">Fecha Vencimiento</label>
						<input type="date" class="form-control" id="fechaVencimiento">
					</div>
					<div class="col-md-4">
						<!-- Espacio reservado -->
					</div>
					<div class="col-12">
						<label class="form-label fw-semibold">Observaciones</label>
						<textarea class="form-control" id="observaciones" rows="2" placeholder="Notas adicionales para la factura"></textarea>
					</div>
				</div>
			</div>
		</div>
		
		<!-- Botón de generación -->
		<div class="d-grid">
			<button class="btn btn-color_principal btn-lg" id="btnGenerarFactura" onclick="generarFactura()" disabled>
				<i class="bi bi-receipt me-2"></i>Generar Factura
			</button>
		</div>
	</div>
</div>

<script>
$(document).ready(function() {
	<?php if (isset($venta) && $venta): ?>
		cargarDatosVentaDirecto(<?php echo json_encode($venta); ?>);
	<?php endif; ?>
});

function formatMoney(val) {
	return '$' + parseFloat(val || 0).toLocaleString('es-CO', {minimumFractionDigits: 0, maximumFractionDigits: 0});
}

function cargarDatosVenta() {
	let idVenta = $('#selectVenta').val();
	if (!idVenta) {
		$('#detalleVentaContainer').hide();
		$('#btnGenerarFactura').prop('disabled', true);
		return;
	}
	
	$.post(IP_SERVER + 'facturacion/obtenerVenta', { id_venta: idVenta }, function(res) {
		if (res.success) {
			cargarDatosVentaDirecto(res.data);
		}
	});
}

function cargarDatosVentaDirecto(venta) {
	$('#detalleVentaContainer').show();
	$('#ventaFolio').text(venta.folio_factura);
	$('#ventaFecha').text(new Date(venta.fecha_venta).toLocaleDateString('es-CO'));
	$('#ventaVendedor').text(venta.vendedor_nombre || 'N/A');
	
	// Prellenar datos del cliente
	$('#clienteRazonSocial').val(venta.cliente_nombre || '');
	$('#clienteNitCc').val(venta.cliente_documento || '');
	$('#clienteCorreo').val(venta.cliente_correo || '');
	
	// Productos
	let html = '';
	if (venta.detalles) {
		venta.detalles.forEach(function(d) {
			html += `<tr>
				<td><small>${d.nombre_historico}</small></td>
				<td class="text-center">${d.cantidad}</td>
				<td class="text-end">${formatMoney(d.precio_unitario)}</td>
				<td class="text-end">${d.porcentaje_impuesto}%</td>
				<td class="text-end">${formatMoney(d.subtotal_linea)}</td>
			</tr>`;
		});
	}
	$('#ventaProductos').html(html);
	
	$('#ventaSubtotal').text(formatMoney(venta.subtotal));
	$('#ventaImpuestos').text(formatMoney(venta.total_impuestos));
	$('#ventaDescuentos').text(formatMoney(venta.total_descuentos));
	$('#ventaTotal').text(formatMoney(venta.total_final));
	
	$('#btnGenerarFactura').prop('disabled', false);
}

function generarFactura() {
	let idVenta = $('#selectVenta').val() || $('#idVentaSeleccionada').val();
	let razonSocial = $('#clienteRazonSocial').val().trim();
	let nitCc = $('#clienteNitCc').val().trim();
	
	if (!idVenta) {
		Swal.fire('Error', 'Seleccione una venta', 'warning');
		return;
	}
	if (!razonSocial) {
		Swal.fire('Error', 'La razón social del cliente es requerida', 'warning');
		$('#clienteRazonSocial').focus();
		return;
	}
	if (!nitCc) {
		Swal.fire('Error', 'El NIT/CC del cliente es requerido', 'warning');
		$('#clienteNitCc').focus();
		return;
	}
	
	$('#btnGenerarFactura').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Generando...');
	
	$.post(IP_SERVER + 'facturacion/guardar', {
		id_venta: idVenta,
		cliente_razon_social: razonSocial,
		cliente_nit_cc: nitCc,
		cliente_direccion: $('#clienteDireccion').val(),
		cliente_correo: $('#clienteCorreo').val(),
		cliente_telefono: $('#clienteTelefono').val(),
		fecha_vencimiento: $('#fechaVencimiento').val(),
		observaciones: $('#observaciones').val()
	}, function(res) {
		if (res.success) {
			Swal.fire({
				title: '¡Factura Generada!',
				html: `<p>Factura <strong>${res.numero_factura}</strong> creada exitosamente.</p>`,
				icon: 'success',
				showCancelButton: true,
				confirmButtonText: 'Ver Factura',
				cancelButtonText: 'Nueva Factura'
			}).then((result) => {
				if (result.isConfirmed) {
					window.location.href = IP_SERVER + 'facturacion/ver/' + res.id;
				} else {
					window.location.href = IP_SERVER + 'facturacion/crear';
				}
			});
		} else {
			Swal.fire('Error', res.message, 'error');
			$('#btnGenerarFactura').prop('disabled', false).html('<i class="bi bi-receipt me-2"></i>Generar Factura');
		}
	}).fail(function(xhr) {
		let msg = xhr.responseJSON ? xhr.responseJSON.message : 'Error al generar la factura';
		Swal.fire('Error', msg, 'error');
		$('#btnGenerarFactura').prop('disabled', false).html('<i class="bi bi-receipt me-2"></i>Generar Factura');
	});
}
</script>
