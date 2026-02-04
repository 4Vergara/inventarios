<!-- Page Header -->
<div class="page-header mb-4">
	<div class="row align-items-center">
		<div class="col">
			<nav aria-label="breadcrumb">
				<ol class="breadcrumb mb-2">
					<li class="breadcrumb-item"><a href="<?php echo IP_SERVER; ?>">Inicio</a></li>
					<li class="breadcrumb-item"><a href="<?php echo IP_SERVER . 'ventas'; ?>">Pedidos</a></li>
					<li class="breadcrumb-item active">Detalle de Venta</li>
				</ol>
			</nav>
			<div class="d-flex align-items-center gap-3">
				<h1 class="page-title mb-0"><?php echo $venta->folio_factura; ?></h1>
				<?php 
				$porcentajePagado = $venta->total_final > 0 ? ($venta->total_pagado / $venta->total_final * 100) : 0;
				if ($porcentajePagado >= 100): ?>
					<span class="estado-badge pagada"><i class="bi bi-check-circle me-1"></i>Pagada</span>
				<?php elseif ($porcentajePagado > 0): ?>
					<span class="estado-badge parcial"><i class="bi bi-clock-history me-1"></i>Pago Parcial (<?php echo round($porcentajePagado); ?>%)</span>
				<?php else: ?>
					<span class="estado-badge pendiente"><i class="bi bi-exclamation-circle me-1"></i>Pendiente de Pago</span>
				<?php endif; ?>
			</div>
		</div>
		<div class="col-auto">
			<a href="<?php echo IP_SERVER . 'ventas/imprimir/' . $venta->id; ?>" target="_blank" class="btn btn-outline-secondary me-2">
				<i class="bi bi-printer me-1"></i>Imprimir
			</a>
			<a href="<?php echo IP_SERVER . 'ventas'; ?>" class="btn btn-light">
				<i class="bi bi-arrow-left me-1"></i>Volver
			</a>
		</div>
	</div>
</div>

<div class="row">
	<!-- Columna Principal -->
	<div class="col-lg-8">
		<!-- Información General -->
		<div class="card mb-4">
			<div class="card-header bg-white">
				<h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Información de la Venta</h5>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-md-6">
						<div class="info-item">
							<span class="info-label">Fecha de Venta</span>
							<span class="info-value">
								<?php echo date('d/m/Y H:i', strtotime($venta->fecha_venta)); ?>
							</span>
						</div>
						<div class="info-item">
							<span class="info-label">Cliente</span>
							<span class="info-value">
								<i class="bi bi-person me-1"></i>
								<?php echo $venta->cliente_nombre ?: 'Sin cliente asignado'; ?>
							</span>
						</div>
						<?php if ($venta->cliente_documento): ?>
						<div class="info-item">
							<span class="info-label">Documento</span>
							<span class="info-value"><?php echo $venta->cliente_documento; ?></span>
						</div>
						<?php endif; ?>
					</div>
					<div class="col-md-6">
						<div class="info-item">
							<span class="info-label">Vendedor</span>
							<span class="info-value">
								<i class="bi bi-person-badge me-1"></i>
								<?php echo $venta->vendedor_nombre ?: 'N/A'; ?>
							</span>
						</div>
						<div class="info-item">
							<span class="info-label">Creado por</span>
							<span class="info-value"><?php echo $venta->creado_por; ?></span>
						</div>
						<div class="info-item">
							<span class="info-label">Fecha de Registro</span>
							<span class="info-value"><?php echo date('d/m/Y H:i', strtotime($venta->fec_creacion)); ?></span>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<!-- Productos -->
		<div class="card mb-4">
			<div class="card-header bg-white d-flex justify-content-between align-items-center">
				<h5 class="mb-0"><i class="bi bi-box-seam me-2"></i>Productos Vendidos</h5>
				<span class="badge bg-color_principal rounded-pill"><?php echo count($venta->detalles); ?> productos</span>
			</div>
			<div class="card-body p-0">
				<div class="table-responsive">
					<table class="table table-modern mb-0">
						<thead>
							<tr>
								<th style="width: 50px;"></th>
								<th>PRODUCTO</th>
								<th class="text-center">CANTIDAD</th>
								<th class="text-end">PRECIO UNIT.</th>
								<th class="text-end">IMPUESTO</th>
								<th class="text-end">SUBTOTAL</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($venta->detalles as $detalle): ?>
							<tr>
								<td>
									<?php if ($detalle->imagen_principal_url): ?>
									<img src="<?php echo $detalle->imagen_principal_url; ?>" class="producto-img">
									<?php else: ?>
									<div class="producto-img-placeholder"><i class="bi bi-image"></i></div>
									<?php endif; ?>
								</td>
								<td>
									<div class="producto-nombre"><?php echo $detalle->nombre_historico; ?></div>
									<div class="producto-meta">
										SKU: <?php echo $detalle->sku_historico ?: 'N/A'; ?>
										<?php if ($detalle->categoria_historica): ?>
										| <?php echo $detalle->categoria_historica; ?>
										<?php endif; ?>
									</div>
								</td>
								<td class="text-center fw-semibold"><?php echo $detalle->cantidad; ?></td>
								<td class="text-end">$<?php echo number_format($detalle->precio_unitario, 2); ?></td>
								<td class="text-end">
									<?php if ($detalle->porcentaje_impuesto > 0): ?>
									<small class="text-muted">(<?php echo $detalle->porcentaje_impuesto; ?>%)</small>
									$<?php echo number_format($detalle->monto_impuesto, 2); ?>
									<?php else: ?>
									<span class="text-muted">-</span>
									<?php endif; ?>
								</td>
								<td class="text-end fw-bold">$<?php echo number_format($detalle->subtotal_linea, 2); ?></td>
							</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		
		<!-- Historial de Pagos -->
		<div class="card">
			<div class="card-header bg-white d-flex justify-content-between align-items-center">
				<h5 class="mb-0"><i class="bi bi-credit-card me-2"></i>Historial de Pagos</h5>
				<?php if ($venta->saldo_pendiente > 0): ?>
				<button class="btn btn-sm btn-color_principal" onclick="abrirModalPago()">
					<i class="bi bi-plus-lg me-1"></i>Registrar Pago
				</button>
				<?php endif; ?>
			</div>
			<div class="card-body p-0">
				<?php if (empty($venta->pagos)): ?>
				<div class="text-center py-5 text-muted">
					<i class="bi bi-credit-card-2-front display-4 d-block mb-3"></i>
					<p class="mb-0">No hay pagos registrados</p>
				</div>
				<?php else: ?>
				<div class="table-responsive">
					<table class="table table-modern mb-0">
						<thead>
							<tr>
								<th>FECHA</th>
								<th>MÉTODO</th>
								<th>REFERENCIA</th>
								<th class="text-end">MONTO</th>
								<th class="text-center" style="width: 80px;">ACCIONES</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($venta->pagos as $pago): ?>
							<tr>
								<td>
									<i class="bi bi-calendar me-1 text-muted"></i>
									<?php echo date('d/m/Y H:i', strtotime($pago->fecha_pago)); ?>
								</td>
								<td>
									<span class="metodo-badge <?php echo strtolower(str_replace(' ', '-', $pago->metodo_pago)); ?>">
										<?php 
										$iconos = [
											'Efectivo' => 'cash-stack',
											'Tarjeta de Crédito' => 'credit-card',
											'Tarjeta de Débito' => 'credit-card-2-front',
											'Transferencia' => 'bank',
											'Cheque' => 'file-earmark-text'
										];
										$icono = $iconos[$pago->metodo_pago] ?? 'cash';
										?>
										<i class="bi bi-<?php echo $icono; ?> me-1"></i>
										<?php echo $pago->metodo_pago; ?>
									</span>
								</td>
								<td><?php echo $pago->referencia ?: '-'; ?></td>
								<td class="text-end fw-bold text-success">
									+$<?php echo number_format($pago->monto, 2); ?>
								</td>
								<td class="text-center">
									<button class="btn btn-sm btn-outline-danger" 
										onclick="eliminarPago(<?php echo $pago->id; ?>)" title="Eliminar">
										<i class="bi bi-trash"></i>
									</button>
								</td>
							</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
	
	<!-- Columna Lateral -->
	<div class="col-lg-4">
		<!-- Resumen Financiero -->
		<div class="card resumen-card mb-4">
			<div class="card-header bg-white">
				<h5 class="mb-0"><i class="bi bi-calculator me-2"></i>Resumen Financiero</h5>
			</div>
			<div class="card-body">
				<div class="resumen-linea">
					<span>Subtotal</span>
					<span>$<?php echo number_format($venta->subtotal, 2); ?></span>
				</div>
				<div class="resumen-linea">
					<span>Impuestos</span>
					<span>$<?php echo number_format($venta->total_impuestos, 2); ?></span>
				</div>
				<div class="resumen-linea">
					<span>Descuentos</span>
					<span class="text-success">-$<?php echo number_format($venta->total_descuentos, 2); ?></span>
				</div>
				<hr>
				<div class="resumen-linea total">
					<span>TOTAL</span>
					<span>$<?php echo number_format($venta->total_final, 2); ?></span>
				</div>
				<hr>
				<div class="resumen-linea pagado">
					<span><i class="bi bi-check-circle me-1"></i>Pagado</span>
					<span class="text-success">$<?php echo number_format($venta->total_pagado, 2); ?></span>
				</div>
				<div class="resumen-linea saldo">
					<span><i class="bi bi-clock me-1"></i>Saldo Pendiente</span>
					<span class="<?php echo $venta->saldo_pendiente > 0 ? 'text-danger' : 'text-success'; ?>">
						$<?php echo number_format($venta->saldo_pendiente, 2); ?>
					</span>
				</div>
				
				<!-- Barra de progreso de pago -->
				<div class="mt-4">
					<div class="d-flex justify-content-between mb-2">
						<small class="text-muted">Progreso de pago</small>
						<small class="fw-semibold"><?php echo round(min($porcentajePagado, 100)); ?>%</small>
					</div>
					<div class="progress" style="height: 10px; border-radius: 5px;">
						<div class="progress-bar bg-success" role="progressbar" 
							style="width: <?php echo min($porcentajePagado, 100); ?>%"></div>
					</div>
				</div>
			</div>
			
			<?php if ($venta->saldo_pendiente > 0): ?>
			<div class="card-footer bg-white border-top-0">
				<button class="btn btn-color_principal w-100" onclick="abrirModalPago()">
					<i class="bi bi-plus-circle me-2"></i>Registrar Pago
				</button>
			</div>
			<?php endif; ?>
		</div>
		
		<!-- Acciones Rápidas -->
		<div class="card">
			<div class="card-header bg-white">
				<h5 class="mb-0"><i class="bi bi-lightning me-2"></i>Acciones</h5>
			</div>
			<div class="card-body">
				<a href="<?php echo IP_SERVER . 'ventas/imprimir/' . $venta->id; ?>" target="_blank" 
					class="btn btn-outline-secondary w-100 mb-2">
					<i class="bi bi-printer me-2"></i>Imprimir Ticket
				</a>
				<a href="<?php echo IP_SERVER . 'ventas/crear'; ?>" class="btn btn-color_principal w-100">
					<i class="bi bi-plus-lg me-2"></i>Nueva Venta
				</a>
			</div>
		</div>
	</div>
</div>

<!-- Modal Registrar Pago -->
<div class="modal fade" id="modalPago" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content border-0 shadow">
			<div class="modal-header border-0 pb-0">
				<h5 class="modal-title">
					<i class="bi bi-credit-card me-2"></i>Registrar Pago
				</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
			</div>
			<form id="formPago">
				<div class="modal-body">
					<input type="hidden" name="id_venta" value="<?php echo $venta->id; ?>">
					
					<div class="alert alert-info">
						<div class="d-flex justify-content-between">
							<span>Saldo pendiente:</span>
							<strong>$<?php echo number_format($venta->saldo_pendiente, 2); ?></strong>
						</div>
					</div>
					
					<div class="mb-3">
						<label class="form-label fw-semibold">Monto <span class="text-danger">*</span></label>
						<div class="input-group input-group-lg">
							<span class="input-group-text">$</span>
							<input type="number" step="0.01" min="0.01" max="<?php echo $venta->saldo_pendiente; ?>" 
								class="form-control" name="monto" id="montoPago" required
								value="<?php echo $venta->saldo_pendiente; ?>">
						</div>
					</div>
					
					<div class="mb-3">
						<label class="form-label fw-semibold">Método de Pago <span class="text-danger">*</span></label>
						<select class="form-select" name="metodo_pago" id="metodoPago" required>
							<?php foreach ($metodos_pago as $metodo): ?>
							<option value="<?php echo $metodo; ?>"><?php echo $metodo; ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					
					<div class="mb-3">
						<label class="form-label fw-semibold">Referencia / Número de Transacción</label>
						<input type="text" class="form-control" name="referencia" id="referenciaPago" 
							placeholder="Ej: Número de autorización, folio de transferencia...">
					</div>
					
					<div class="mb-3">
						<label class="form-label fw-semibold">Fecha del Pago</label>
						<input type="datetime-local" class="form-control" name="fecha_pago" id="fechaPago" 
							value="<?php echo date('Y-m-d\TH:i'); ?>">
					</div>
				</div>
				<div class="modal-footer border-0 pt-0">
					<button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
					<button type="submit" class="btn btn-color_principal">
						<i class="bi bi-check-circle me-1"></i>Registrar Pago
					</button>
				</div>
			</form>
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

/* Estado Badge */
.estado-badge {
	display: inline-flex;
	align-items: center;
	padding: 8px 16px;
	border-radius: 25px;
	font-size: 0.85rem;
	font-weight: 600;
}
.estado-badge.pagada {
	background: #dcfce7;
	color: #16a34a;
}
.estado-badge.parcial {
	background: #fef3c7;
	color: #d97706;
}
.estado-badge.pendiente {
	background: #fee2e2;
	color: #dc2626;
}

/* Info Items */
.info-item {
	padding: 12px 0;
	border-bottom: 1px solid #f3f4f6;
}
.info-item:last-child {
	border-bottom: none;
}
.info-label {
	display: block;
	font-size: 0.75rem;
	font-weight: 600;
	color: #9ca3af;
	text-transform: uppercase;
	letter-spacing: 0.5px;
	margin-bottom: 4px;
}
.info-value {
	font-size: 1rem;
	color: #1f2937;
	font-weight: 500;
}

/* Table Modern */
.table-modern thead {
	background: #f9fafb;
}
.table-modern thead th {
	padding: 14px 16px;
	font-size: 0.7rem;
	font-weight: 700;
	color: #6b7280;
	text-transform: uppercase;
	letter-spacing: 0.5px;
	border-bottom: 1px solid #e5e7eb;
}
.table-modern tbody td {
	padding: 14px 16px;
	vertical-align: middle;
	border-bottom: 1px solid #f3f4f6;
}

/* Producto en tabla */
.producto-img {
	width: 40px;
	height: 40px;
	border-radius: 8px;
	object-fit: cover;
	border: 1px solid #e5e7eb;
}
.producto-img-placeholder {
	width: 40px;
	height: 40px;
	border-radius: 8px;
	background: #f3f4f6;
	display: flex;
	align-items: center;
	justify-content: center;
	color: #9ca3af;
}
.producto-nombre {
	font-weight: 600;
	color: #1f2937;
	font-size: 0.9rem;
}
.producto-meta {
	font-size: 0.75rem;
	color: #9ca3af;
}

/* Método Badge */
.metodo-badge {
	display: inline-flex;
	align-items: center;
	padding: 6px 12px;
	border-radius: 20px;
	font-size: 0.8rem;
	font-weight: 500;
	background: #f3f4f6;
	color: #4b5563;
}
.metodo-badge.efectivo {
	background: #dcfce7;
	color: #16a34a;
}
.metodo-badge.tarjeta-de-crédito,
.metodo-badge.tarjeta-de-débito {
	background: #dbeafe;
	color: #2563eb;
}
.metodo-badge.transferencia {
	background: #fef3c7;
	color: #d97706;
}

/* Resumen Card */
.resumen-card {
	position: sticky;
	top: 20px;
}
.resumen-linea {
	display: flex;
	justify-content: space-between;
	padding: 10px 0;
	font-size: 0.95rem;
}
.resumen-linea.total {
	font-size: 1.3rem;
	font-weight: 800;
	color: #1f2937;
}
.resumen-linea.pagado,
.resumen-linea.saldo {
	font-weight: 600;
}
</style>

<script>
let modalPago;

$(document).ready(function() {
	modalPago = new bootstrap.Modal(document.getElementById('modalPago'));
	
	$('#formPago').on('submit', function(e) {
		e.preventDefault();
		registrarPago();
	});
});

function abrirModalPago() {
	$('#montoPago').val(<?php echo $venta->saldo_pendiente; ?>);
	$('#referenciaPago').val('');
	$('#fechaPago').val(new Date().toISOString().slice(0, 16));
	modalPago.show();
}

function registrarPago() {
	let formData = {
		id_venta: $('input[name="id_venta"]').val(),
		monto: $('#montoPago').val(),
		metodo_pago: $('#metodoPago').val(),
		referencia: $('#referenciaPago').val(),
		fecha_pago: $('#fechaPago').val()
	};
	
	$.ajax({
		url: IP_SERVER + 'ventas/registrarPago',
		type: 'POST',
		data: formData,
		dataType: 'json',
		success: function(response) {
			modalPago.hide();
			
			if (response.success) {
				Swal.fire({
					icon: 'success',
					title: '¡Pago registrado!',
					text: response.message,
					timer: 2000,
					showConfirmButton: false
				}).then(() => {
					location.reload();
				});
			} else {
				Swal.fire({
					icon: 'error',
					title: 'Error',
					text: response.message
				});
			}
		},
		error: function(xhr) {
			modalPago.hide();
			let msg = 'Ocurrió un error al registrar el pago';
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

function eliminarPago(idPago) {
	Swal.fire({
		title: '¿Eliminar pago?',
		text: 'Esta acción no se puede deshacer',
		icon: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#dc2626',
		cancelButtonText: 'Cancelar',
		confirmButtonText: 'Sí, eliminar'
	}).then((result) => {
		if (result.isConfirmed) {
			$.ajax({
				url: IP_SERVER + 'ventas/eliminarPago',
				type: 'POST',
				data: { id: idPago },
				dataType: 'json',
				success: function(response) {
					if (response.success) {
						Swal.fire({
							icon: 'success',
							title: '¡Eliminado!',
							text: response.message,
							timer: 2000,
							showConfirmButton: false
						}).then(() => {
							location.reload();
						});
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
						text: 'Ocurrió un error al eliminar el pago'
					});
				}
			});
		}
	});
}
</script>
