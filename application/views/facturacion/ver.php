<!-- Page Header -->
<div class="page-header mb-4">
	<div class="row align-items-center">
		<div class="col">
			<nav aria-label="breadcrumb">
				<ol class="breadcrumb mb-2">
					<li class="breadcrumb-item"><a href="<?php echo IP_SERVER; ?>">Inicio</a></li>
					<li class="breadcrumb-item"><a href="<?php echo IP_SERVER . 'facturacion'; ?>">Facturación</a></li>
					<li class="breadcrumb-item active">Detalle</li>
				</ol>
			</nav>
			<div class="d-flex align-items-center gap-3">
				<h1 class="page-title mb-0"><?php echo $factura->numero_factura; ?></h1>
				<?php if ($factura->estado === 'emitida'): ?>
					<span class="estado-badge pagada"><i class="bi bi-check-circle me-1"></i>Emitida</span>
				<?php else: ?>
					<span class="estado-badge pendiente"><i class="bi bi-x-circle me-1"></i>Anulada</span>
				<?php endif; ?>
			</div>
		</div>
		<div class="col-auto">
			<a href="<?php echo IP_SERVER . 'facturacion/pdf/' . $factura->id; ?>" target="_blank" class="btn btn-outline-danger me-2">
				<i class="bi bi-file-earmark-pdf me-1"></i>Descargar PDF
			</a>
			<a href="<?php echo IP_SERVER . 'facturacion'; ?>" class="btn btn-light">
				<i class="bi bi-arrow-left me-1"></i>Volver
			</a>
		</div>
	</div>
</div>

<?php if ($factura->estado === 'anulada'): ?>
<div class="alert alert-danger mb-4">
	<h6 class="alert-heading"><i class="bi bi-exclamation-triangle me-1"></i>Factura Anulada</h6>
	<p class="mb-1"><strong>Motivo:</strong> <?php echo $factura->motivo_anulacion; ?></p>
	<small>Anulada el <?php echo date('d/m/Y H:i', strtotime($factura->fecha_anulacion)); ?> por <?php echo $factura->actualizado_por; ?></small>
</div>
<?php endif; ?>

<div class="row">
	<div class="col-lg-8">
		<!-- Datos Emisor -->
		<div class="card mb-4">
			<div class="card-header bg-white">
				<h5 class="mb-0"><i class="bi bi-building me-2"></i>Datos del Emisor</h5>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-md-6">
						<div class="info-item">
							<span class="info-label">Razón Social</span>
							<span class="info-value"><?php echo $factura->emisor_razon_social; ?></span>
						</div>
						<div class="info-item">
							<span class="info-label">NIT</span>
							<span class="info-value"><?php echo $factura->emisor_nit; ?></span>
						</div>
						<div class="info-item">
							<span class="info-label">Dirección</span>
							<span class="info-value"><?php echo $factura->emisor_direccion; ?></span>
						</div>
					</div>
					<div class="col-md-6">
						<div class="info-item">
							<span class="info-label">Ciudad</span>
							<span class="info-value"><?php echo $factura->emisor_ciudad . ', ' . $factura->emisor_departamento; ?></span>
						</div>
						<div class="info-item">
							<span class="info-label">Teléfono</span>
							<span class="info-value"><?php echo $factura->emisor_telefono ?: 'N/A'; ?></span>
						</div>
						<div class="info-item">
							<span class="info-label">Régimen</span>
							<span class="info-value"><?php echo $factura->emisor_regimen; ?></span>
						</div>
					</div>
				</div>
				<div class="info-item">
					<span class="info-label">Resolución DIAN</span>
					<span class="info-value"><?php echo $factura->emisor_resolucion; ?> del <?php echo date('d/m/Y', strtotime($factura->emisor_fecha_resolucion)); ?></span>
				</div>
			</div>
		</div>
		
		<!-- Datos Cliente -->
		<div class="card mb-4">
			<div class="card-header bg-white">
				<h5 class="mb-0"><i class="bi bi-person-vcard me-2"></i>Datos del Cliente</h5>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-md-6">
						<div class="info-item">
							<span class="info-label">Razón Social / Nombre</span>
							<span class="info-value"><?php echo $factura->cliente_razon_social; ?></span>
						</div>
						<div class="info-item">
							<span class="info-label">NIT / CC</span>
							<span class="info-value"><?php echo $factura->cliente_nit_cc; ?></span>
						</div>
					</div>
					<div class="col-md-6">
						<div class="info-item">
							<span class="info-label">Dirección</span>
							<span class="info-value"><?php echo $factura->cliente_direccion ?: 'N/A'; ?></span>
						</div>
						<div class="info-item">
							<span class="info-label">Correo</span>
							<span class="info-value"><?php echo $factura->cliente_correo ?: 'N/A'; ?></span>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<!-- Productos -->
		<div class="card mb-4">
			<div class="card-header bg-white d-flex justify-content-between align-items-center">
				<h5 class="mb-0"><i class="bi bi-box-seam me-2"></i>Detalle de Productos</h5>
				<span class="badge bg-color_principal rounded-pill"><?php echo count($factura->detalles); ?> ítems</span>
			</div>
			<div class="card-body p-0">
				<div class="table-responsive">
					<table class="table table-modern mb-0">
						<thead>
							<tr>
								<th>PRODUCTO</th>
								<th>SKU</th>
								<th class="text-center">CANT.</th>
								<th class="text-end">PRECIO UNIT.</th>
								<th class="text-center">IVA %</th>
								<th class="text-end">IVA $</th>
								<th class="text-end">SUBTOTAL</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($factura->detalles as $det): ?>
							<tr>
								<td><strong><?php echo $det->nombre_producto; ?></strong></td>
								<td><small class="text-muted"><?php echo $det->sku ?: '-'; ?></small></td>
								<td class="text-center"><?php echo $det->cantidad; ?></td>
								<td class="text-end">$<?php echo number_format($det->precio_unitario, 0, ',', '.'); ?></td>
								<td class="text-center"><?php echo $det->porcentaje_iva; ?>%</td>
								<td class="text-end">$<?php echo number_format($det->monto_iva, 0, ',', '.'); ?></td>
								<td class="text-end"><strong>$<?php echo number_format($det->subtotal_linea, 0, ',', '.'); ?></strong></td>
							</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
	
	<!-- Columna lateral -->
	<div class="col-lg-4">
		<!-- Resumen financiero -->
		<div class="card mb-4">
			<div class="card-header bg-white">
				<h5 class="mb-0"><i class="bi bi-calculator me-2"></i>Resumen</h5>
			</div>
			<div class="card-body">
				<div class="d-flex justify-content-between mb-2">
					<span>Subtotal (Base Gravable):</span>
					<strong>$<?php echo number_format($factura->subtotal, 0, ',', '.'); ?></strong>
				</div>
				<div class="d-flex justify-content-between mb-2">
					<span>Total IVA:</span>
					<strong>$<?php echo number_format($factura->total_iva, 0, ',', '.'); ?></strong>
				</div>
				<div class="d-flex justify-content-between mb-2">
					<span>Descuentos:</span>
					<strong>-$<?php echo number_format($factura->total_descuentos, 0, ',', '.'); ?></strong>
				</div>
				<hr>
				<div class="d-flex justify-content-between fs-5">
					<span>Total Factura:</span>
					<strong class="text-success">$<?php echo number_format($factura->total_final, 0, ',', '.'); ?></strong>
				</div>
			</div>
		</div>
		
		<!-- Información de pago -->
		<div class="card mb-4">
			<div class="card-header bg-white">
				<h5 class="mb-0"><i class="bi bi-credit-card me-2"></i>Información de Pago</h5>
			</div>
			<div class="card-body">
				<div class="info-item">
					<span class="info-label">Forma de Pago</span>
					<span class="info-value"><?php echo $factura->forma_pago; ?></span>
				</div>
				<div class="info-item">
					<span class="info-label">Medio de Pago</span>
					<span class="info-value"><?php echo $factura->medio_pago; ?></span>
				</div>
				<?php if ($factura->fecha_vencimiento): ?>
				<div class="info-item">
					<span class="info-label">Vencimiento</span>
					<span class="info-value"><?php echo date('d/m/Y', strtotime($factura->fecha_vencimiento)); ?></span>
				</div>
				<?php endif; ?>
				<hr>
				<div class="d-flex justify-content-between mb-1">
					<span>Total Pagado:</span>
					<strong class="text-success">$<?php echo number_format($factura->total_pagado, 0, ',', '.'); ?></strong>
				</div>
				<div class="d-flex justify-content-between">
					<span>Saldo Pendiente:</span>
					<strong class="<?php echo $factura->saldo_pendiente > 0 ? 'text-danger' : 'text-success'; ?>">
						$<?php echo number_format($factura->saldo_pendiente, 0, ',', '.'); ?>
					</strong>
				</div>
			</div>
		</div>
		
		<!-- Información adicional -->
		<div class="card mb-4">
			<div class="card-header bg-white">
				<h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Información Adicional</h5>
			</div>
			<div class="card-body">
				<div class="info-item">
					<span class="info-label">Fecha de Emisión</span>
					<span class="info-value"><?php echo date('d/m/Y H:i', strtotime($factura->fecha_factura)); ?></span>
				</div>
				<div class="info-item">
					<span class="info-label">Venta de Origen</span>
					<span class="info-value">
						<a href="<?php echo IP_SERVER . 'ventas/ver/' . $factura->id_venta; ?>"><?php echo $factura->folio_venta; ?></a>
					</span>
				</div>
				<div class="info-item">
					<span class="info-label">Vendedor</span>
					<span class="info-value"><?php echo $factura->vendedor_nombre; ?></span>
				</div>
				<div class="info-item">
					<span class="info-label">Creado por</span>
					<span class="info-value"><?php echo $factura->creado_por; ?></span>
				</div>
				<?php if ($factura->observaciones): ?>
				<div class="info-item">
					<span class="info-label">Observaciones</span>
					<span class="info-value"><?php echo nl2br($factura->observaciones); ?></span>
				</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>
