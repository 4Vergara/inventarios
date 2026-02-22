<!-- Page Header -->
<div class="page-header mb-4">
	<div class="row align-items-center">
		<div class="col-lg-8">
			<h1 class="page-title">Detalle de Cierre de Caja</h1>
			<p class="page-subtitle">
				<span class="badge bg-primary fs-6"><?php echo $cierre->codigo_cierre; ?></span>
			</p>
		</div>
		<div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
			<a href="<?php echo IP_SERVER . 'cierre_caja/pdf/' . $cierre->id; ?>" class="btn btn-outline-secondary me-2" target="_blank">
				<i class="bi bi-printer me-1"></i>Imprimir
			</a>
			<a href="<?php echo IP_SERVER . 'cierre_caja'; ?>" class="btn btn-outline-secondary">
				<i class="bi bi-arrow-left me-1"></i>Volver
			</a>
		</div>
	</div>
</div>

<?php
	$tipoLabels = ['dia' => 'Diario', 'semana' => 'Semanal', 'mes' => 'Mensual', 'anio' => 'Anual'];
	$tipoColors = ['dia' => 'primary', 'semana' => 'info', 'mes' => 'success', 'anio' => 'warning'];
	$tipoLabel = isset($tipoLabels[$cierre->tipo_periodo]) ? $tipoLabels[$cierre->tipo_periodo] : $cierre->tipo_periodo;
	$tipoColor = isset($tipoColors[$cierre->tipo_periodo]) ? $tipoColors[$cierre->tipo_periodo] : 'secondary';
?>

<!-- Información General -->
<div class="row g-4 mb-4">
	<!-- Período -->
	<div class="col-lg-6">
		<div class="card card-modern h-100">
			<div class="card-header bg-transparent border-bottom">
				<h5 class="mb-0"><i class="bi bi-calendar-range me-2"></i>Información del Período</h5>
			</div>
			<div class="card-body">
				<div class="row g-3">
					<div class="col-sm-6">
						<div class="info-item">
							<span class="info-label">Tipo de Período</span>
							<span class="info-value">
								<span class="badge bg-<?php echo $tipoColor; ?>"><?php echo $tipoLabel; ?></span>
							</span>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="info-item">
							<span class="info-label">Código</span>
							<span class="info-value"><?php echo $cierre->codigo_cierre; ?></span>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="info-item">
							<span class="info-label">Fecha Inicio</span>
							<span class="info-value"><?php echo date('d/m/Y', strtotime($cierre->fecha_inicio)); ?></span>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="info-item">
							<span class="info-label">Fecha Fin</span>
							<span class="info-value"><?php echo date('d/m/Y', strtotime($cierre->fecha_fin)); ?></span>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="info-item">
							<span class="info-label">Realizado por</span>
							<span class="info-value"><?php echo $cierre->creado_por; ?></span>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="info-item">
							<span class="info-label">Fecha de Registro</span>
							<span class="info-value"><?php echo date('d/m/Y H:i', strtotime($cierre->fec_creacion)); ?></span>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<!-- Resumen Financiero -->
	<div class="col-lg-6">
		<div class="card card-modern h-100">
			<div class="card-header bg-transparent border-bottom">
				<h5 class="mb-0"><i class="bi bi-graph-up me-2"></i>Resumen Financiero</h5>
			</div>
			<div class="card-body">
				<div class="row g-3">
					<div class="col-sm-6">
						<div class="info-item">
							<span class="info-label">Total Ventas (Cant.)</span>
							<span class="info-value fs-4 text-primary"><?php echo $cierre->total_ventas; ?></span>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="info-item">
							<span class="info-label">Ventas Anuladas</span>
							<span class="info-value fs-4 text-danger"><?php echo $cierre->ventas_anuladas; ?></span>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="info-item">
							<span class="info-label">Total Ventas</span>
							<span class="info-value fs-5 fw-bold text-success">$<?php echo number_format($cierre->monto_total_vendido, 0, ',', '.'); ?></span>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="info-item">
							<span class="info-label">Total IVA</span>
							<span class="info-value fs-5">$<?php echo number_format($cierre->monto_impuestos, 0, ',', '.'); ?></span>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="info-item">
							<span class="info-label">Subtotal (sin IVA)</span>
							<span class="info-value">$<?php echo number_format($cierre->monto_subtotal, 0, ',', '.'); ?></span>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="info-item">
							<span class="info-label">Total Descuentos</span>
							<span class="info-value">$<?php echo number_format($cierre->monto_descuentos, 0, ',', '.'); ?></span>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Detalle por Método de Pago y Cuadre de Caja -->
<div class="row g-4 mb-4">
	<div class="col-lg-6">
		<div class="card card-modern h-100">
			<div class="card-header bg-transparent border-bottom">
				<h5 class="mb-0"><i class="bi bi-wallet2 me-2"></i>Desglose por Método de Pago</h5>
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
						<tbody>
							<?php
								$metodosPago = [
									'Efectivo' => $cierre->total_efectivo,
									'Tarjeta de Crédito' => $cierre->total_tarjeta_credito,
									'Tarjeta de Débito' => $cierre->total_tarjeta_debito,
									'Transferencia' => $cierre->total_transferencia,
									'Cheque' => $cierre->total_cheque
								];
								$hayMetodos = false;
								foreach ($metodosPago as $metodo => $total):
									if ($total > 0): $hayMetodos = true;
							?>
							<tr>
								<td><i class="bi bi-credit-card me-1"></i><?php echo $metodo; ?></td>
								<td class="text-end"><strong>$<?php echo number_format($total, 0, ',', '.'); ?></strong></td>
							</tr>
							<?php endif; endforeach; ?>
							<?php if (!$hayMetodos): ?>
							<tr>
								<td colspan="2" class="text-center text-muted py-3">Sin datos de pago</td>
							</tr>
							<?php endif; ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
	
	<div class="col-lg-6">
		<div class="card card-modern h-100">
			<div class="card-header bg-transparent border-bottom">
				<h5 class="mb-0"><i class="bi bi-cash-coin me-2"></i>Cuadre de Caja (Efectivo)</h5>
			</div>
			<div class="card-body">
				<div class="row g-3 text-center">
					<div class="col-6">
						<div class="info-item">
							<span class="info-label">Efectivo Inicial</span>
							<span class="info-value">$<?php echo number_format($cierre->efectivo_inicial, 0, ',', '.'); ?></span>
						</div>
					</div>
					<div class="col-6">
						<div class="info-item">
							<span class="info-label">Ventas en Efectivo</span>
							<span class="info-value">$<?php echo number_format($cierre->total_efectivo, 0, ',', '.'); ?></span>
						</div>
					</div>
					<div class="col-6">
						<div class="info-item">
							<span class="info-label">Efectivo Contado</span>
							<span class="info-value">$<?php echo number_format($cierre->efectivo_contado, 0, ',', '.'); ?></span>
						</div>
					</div>
					<div class="col-6">
						<div class="info-item">
							<span class="info-label">Diferencia</span>
							<?php 
								$diferencia = $cierre->diferencia_caja;
								$difClass = $diferencia > 0 ? 'text-success' : ($diferencia < 0 ? 'text-danger' : 'text-muted');
								$difPrefix = $diferencia > 0 ? '+' : '';
							?>
							<span class="info-value fs-4 <?php echo $difClass; ?>">
								<?php echo $difPrefix; ?>$<?php echo number_format($diferencia, 0, ',', '.'); ?>
							</span>
						</div>
					</div>
				</div>
				
				<?php if ($diferencia != 0): ?>
				<div class="alert <?php echo $diferencia > 0 ? 'alert-success' : 'alert-danger'; ?> mt-3 mb-0">
					<i class="bi bi-exclamation-triangle me-1"></i>
					<?php if ($diferencia > 0): ?>
						Hay un sobrante de $<?php echo number_format(abs($diferencia), 0, ',', '.'); ?> en caja.
					<?php else: ?>
						Hay un faltante de $<?php echo number_format(abs($diferencia), 0, ',', '.'); ?> en caja.
					<?php endif; ?>
				</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>

<!-- Observaciones -->
<?php if ($cierre->observaciones): ?>
<div class="card card-modern mb-4">
	<div class="card-header bg-transparent border-bottom">
		<h5 class="mb-0"><i class="bi bi-chat-left-text me-2"></i>Observaciones</h5>
	</div>
	<div class="card-body">
		<p class="mb-0"><?php echo nl2br($cierre->observaciones); ?></p>
	</div>
</div>
<?php endif; ?>

<!-- Detalle de Ventas -->
<div class="card card-modern">
	<div class="card-header bg-transparent border-bottom">
		<h5 class="mb-0"><i class="bi bi-receipt me-2"></i>Ventas Incluidas en el Cierre</h5>
	</div>
	<div class="card-body p-0">
		<div class="table-responsive">
			<table class="table table-modern mb-0">
				<thead>
					<tr>
						<th>#</th>
						<th>Folio</th>
						<th>Cliente</th>
						<th>Fecha Venta</th>
						<th>Método Pago</th>
						<th class="text-end">Total Venta</th>
						<th class="text-end">Total Pagado</th>
						<th class="text-center">Estado</th>
						<th class="text-center">Ver</th>
					</tr>
				</thead>
				<tbody>
					<?php if ($cierre->ventas && count($cierre->ventas) > 0): ?>
						<?php $i = 1; foreach ($cierre->ventas as $v): ?>
						<tr>
							<td><?php echo $i++; ?></td>
							<td><strong><?php echo $v->folio_venta ?: '-'; ?></strong></td>
							<td><?php echo isset($v->cliente_nombre) ? $v->cliente_nombre : 'Sin cliente'; ?></td>
							<td><?php echo isset($v->fecha_venta) ? date('d/m/Y', strtotime($v->fecha_venta)) : '-'; ?></td>
							<td><?php echo $v->metodo_pago_principal ?: '-'; ?></td>
							<td class="text-end">$<?php echo number_format($v->total_venta, 0, ',', '.'); ?></td>
							<td class="text-end">$<?php echo number_format($v->total_pagado, 0, ',', '.'); ?></td>
							<td class="text-center">
								<span class="badge bg-success">Registrada</span>
							</td>
							<td class="text-center">
								<a href="<?php echo IP_SERVER . 'ventas/ver/' . $v->id_venta; ?>" class="btn btn-sm btn-outline-primary" title="Ver venta">
									<i class="bi bi-eye"></i>
								</a>
							</td>
						</tr>
						<?php endforeach; ?>
					<?php else: ?>
						<tr>
							<td colspan="10" class="text-center text-muted py-4">No se registraron ventas en este cierre</td>
						</tr>
					<?php endif; ?>
				</tbody>
				<?php if ($cierre->ventas && count($cierre->ventas) > 0): ?>
				<tfoot>
					<tr class="table-light fw-bold">
						<td colspan="5" class="text-end">TOTALES:</td>
						<td class="text-end">$<?php echo number_format($cierre->monto_subtotal, 0, ',', '.'); ?></td>
						<td class="text-end">$<?php echo number_format($cierre->monto_impuestos, 0, ',', '.'); ?></td>
						<td colspan="2"></td>
					</tr>
				</tfoot>
				<?php endif; ?>
			</table>
		</div>
	</div>
</div>
