<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Cierre de Caja <?php echo $cierre->codigo_cierre; ?></title>
	<style>
		* { margin: 0; padding: 0; box-sizing: border-box; }
		
		body {
			font-family: 'Segoe UI', Arial, Helvetica, sans-serif;
			font-size: 11px;
			line-height: 1.5;
			color: #333;
			background: #f5f5f5;
		}
		
		.reporte-container {
			width: 210mm;
			min-height: 297mm;
			margin: 0 auto;
			padding: 15mm;
			background: #fff;
			box-shadow: 0 0 10px rgba(0,0,0,0.1);
		}
		
		/* Header */
		.reporte-header {
			text-align: center;
			border-bottom: 3px solid #2c3e50;
			padding-bottom: 15px;
			margin-bottom: 15px;
		}
		
		.reporte-header h1 {
			font-size: 20px;
			color: #2c3e50;
			margin-bottom: 3px;
		}
		
		.reporte-header h2 {
			font-size: 14px;
			color: #555;
			font-weight: 400;
		}
		
		.reporte-header .codigo {
			display: inline-block;
			background: #2c3e50;
			color: #fff;
			padding: 5px 20px;
			border-radius: 3px;
			font-size: 14px;
			font-weight: 700;
			margin-top: 8px;
			letter-spacing: 1px;
		}
		
		/* Info grid */
		.info-grid {
			display: flex;
			gap: 10px;
			margin-bottom: 15px;
		}
		
		.info-box {
			flex: 1;
			border: 1px solid #ddd;
			border-radius: 4px;
			padding: 10px;
		}
		
		.info-box h3 {
			font-size: 10px;
			text-transform: uppercase;
			color: #2c3e50;
			letter-spacing: 1px;
			margin-bottom: 6px;
			padding-bottom: 4px;
			border-bottom: 1px solid #eee;
		}
		
		.info-row {
			display: flex;
			justify-content: space-between;
			margin: 2px 0;
		}
		
		.info-row .label { color: #666; font-weight: 600; }
		
		/* Stats row */
		.stats-row {
			display: flex;
			gap: 10px;
			margin-bottom: 15px;
		}
		
		.stat-box {
			flex: 1;
			text-align: center;
			padding: 12px;
			border: 1px solid #ddd;
			border-radius: 4px;
			background: #f8f9fa;
		}
		
		.stat-box .label {
			display: block;
			font-size: 9px;
			text-transform: uppercase;
			color: #666;
			margin-bottom: 3px;
		}
		
		.stat-box .value {
			font-size: 16px;
			font-weight: 700;
			color: #2c3e50;
		}
		
		.stat-box .value.success { color: #28a745; }
		.stat-box .value.danger { color: #dc3545; }
		.stat-box .value.primary { color: #0d6efd; }
		
		/* Tabla */
		.tabla {
			width: 100%;
			border-collapse: collapse;
			margin-bottom: 15px;
		}
		
		.tabla thead th {
			background: #2c3e50;
			color: #fff;
			padding: 6px 8px;
			font-size: 9px;
			text-transform: uppercase;
			letter-spacing: 0.5px;
			text-align: left;
		}
		
		.tabla thead th.text-right { text-align: right; }
		.tabla thead th.text-center { text-align: center; }
		
		.tabla tbody td {
			padding: 5px 8px;
			border-bottom: 1px solid #eee;
			font-size: 10px;
		}
		
		.tabla tbody td.text-right { text-align: right; }
		.tabla tbody td.text-center { text-align: center; }
		
		.tabla tbody tr:nth-child(even) { background: #f9f9f9; }
		
		.tabla tfoot td {
			padding: 6px 8px;
			font-weight: 700;
			background: #f0f0f0;
			font-size: 10px;
		}
		
		/* Section title */
		.section-title {
			font-size: 12px;
			color: #2c3e50;
			font-weight: 700;
			margin: 15px 0 8px;
			padding-bottom: 4px;
			border-bottom: 2px solid #eee;
		}
		
		/* Cuadre box */
		.cuadre-container {
			display: flex;
			gap: 10px;
			margin-bottom: 15px;
		}
		
		.cuadre-item {
			flex: 1;
			text-align: center;
			padding: 10px;
			border: 2px solid #ddd;
			border-radius: 4px;
		}
		
		.cuadre-item .label {
			display: block;
			font-size: 9px;
			text-transform: uppercase;
			color: #666;
		}
		
		.cuadre-item .value {
			font-size: 14px;
			font-weight: 700;
		}
		
		.cuadre-resultado {
			border-color: #2c3e50;
			background: #f8f9fa;
		}
		
		/* Observaciones */
		.observaciones {
			border: 1px solid #ddd;
			border-radius: 4px;
			padding: 10px;
			margin-bottom: 15px;
		}
		
		.observaciones h4 {
			font-size: 10px;
			text-transform: uppercase;
			color: #666;
			margin-bottom: 4px;
		}
		
		/* Footer */
		.reporte-footer {
			margin-top: 25px;
			border-top: 2px solid #eee;
			padding-top: 10px;
			text-align: center;
			font-size: 9px;
			color: #999;
		}
		
		.firma-section {
			display: flex;
			justify-content: space-around;
			margin-top: 40px;
		}
		
		.firma-line {
			text-align: center;
			width: 200px;
		}
		
		.firma-line .line {
			border-top: 1px solid #333;
			margin-bottom: 5px;
		}
		
		.firma-line .name {
			font-size: 9px;
			color: #666;
		}
		
		/* Print */
		@media print {
			body { background: #fff; }
			.reporte-container { box-shadow: none; margin: 0; padding: 10mm; }
			.no-print { display: none !important; }
			@page { size: letter; margin: 0; }
		}
		
		.toolbar {
			text-align: center;
			padding: 15px;
			background: #fff;
			position: sticky;
			top: 0;
			z-index: 10;
			border-bottom: 1px solid #ddd;
		}
		
		.toolbar button {
			padding: 10px 25px;
			border: none;
			border-radius: 4px;
			cursor: pointer;
			font-size: 14px;
			margin: 0 5px;
		}
		
		.btn-print { background: #2c3e50; color: #fff; }
		.btn-back { background: #eee; color: #333; }
	</style>
</head>
<body>
	<!-- Toolbar -->
	<div class="toolbar no-print">
		<button class="btn-print" onclick="window.print()">
			<span style="margin-right: 5px;">🖨️</span> Imprimir / Descargar PDF
		</button>
		<button class="btn-back" onclick="history.back()">
			← Volver
		</button>
	</div>
	
	<div class="reporte-container">
		<!-- Header -->
		<div class="reporte-header">
			<h1>REPORTE DE CIERRE DE CAJA</h1>
			<h2>Sistema Saho</h2>
			<div class="codigo"><?php echo $cierre->codigo_cierre; ?></div>
		</div>
		
		<?php
			$tipoLabels = ['dia' => 'Diario', 'semana' => 'Semanal', 'mes' => 'Mensual', 'anio' => 'Anual'];
			$tipoLabel = isset($tipoLabels[$cierre->tipo_periodo]) ? $tipoLabels[$cierre->tipo_periodo] : $cierre->tipo_periodo;
		?>
		
		<!-- Info del período -->
		<div class="info-grid">
			<div class="info-box">
				<h3>Información del Período</h3>
				<div class="info-row">
					<span class="label">Tipo:</span>
					<span><?php echo $tipoLabel; ?></span>
				</div>
				<div class="info-row">
					<span class="label">Fecha Inicio:</span>
					<span><?php echo date('d/m/Y', strtotime($cierre->fecha_inicio)); ?></span>
				</div>
				<div class="info-row">
					<span class="label">Fecha Fin:</span>
					<span><?php echo date('d/m/Y', strtotime($cierre->fecha_fin)); ?></span>
				</div>
			</div>
			<div class="info-box">
				<h3>Datos del Cierre</h3>
				<div class="info-row">
					<span class="label">Realizado por:</span>
					<span><?php echo $cierre->creado_por; ?></span>
				</div>
				<div class="info-row">
					<span class="label">Fecha Cierre:</span>
					<span><?php echo date('d/m/Y H:i:s', strtotime($cierre->fec_creacion)); ?></span>
				</div>
			</div>
		</div>
		
		<!-- Stats -->
		<div class="stats-row">
			<div class="stat-box">
				<span class="label">Total Ventas</span>
				<span class="value primary"><?php echo $cierre->total_ventas; ?></span>
			</div>
			<div class="stat-box">
				<span class="label">Monto Total</span>
				<span class="value success">$<?php echo number_format($cierre->monto_total_vendido, 0, ',', '.'); ?></span>
			</div>
			<div class="stat-box">
				<span class="label">Total IVA</span>
				<span class="value">$<?php echo number_format($cierre->monto_impuestos, 0, ',', '.'); ?></span>
			</div>
			<div class="stat-box">
				<span class="label">Subtotal</span>
				<span class="value">$<?php echo number_format($cierre->monto_subtotal, 0, ',', '.'); ?></span>
			</div>
			<div class="stat-box">
				<span class="label">Anuladas</span>
				<span class="value danger"><?php echo $cierre->ventas_anuladas; ?></span>
			</div>
		</div>
		
		<!-- Desglose por método de pago -->
		<div class="section-title">Desglose por Método de Pago</div>
		<table class="tabla">
			<thead>
				<tr>
					<th>Método de Pago</th>
					<th class="text-right">Total</th>
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
					<td><?php echo $metodo; ?></td>
					<td class="text-right"><strong>$<?php echo number_format($total, 0, ',', '.'); ?></strong></td>
				</tr>
				<?php endif; endforeach; ?>
				<?php if (!$hayMetodos): ?>
				<tr>
					<td colspan="2" class="text-center">Sin datos de pago</td>
				</tr>
				<?php endif; ?>
			</tbody>
		</table>
		
		<!-- Cuadre de Caja -->
		<div class="section-title">Cuadre de Caja (Efectivo)</div>
		<div class="cuadre-container">
			<div class="cuadre-item">
				<span class="label">Efectivo Inicial</span>
				<span class="value">$<?php echo number_format($cierre->efectivo_inicial, 0, ',', '.'); ?></span>
			</div>
			<div class="cuadre-item">
				<span class="label">Ventas Efectivo</span>
				<span class="value">$<?php echo number_format($cierre->total_efectivo, 0, ',', '.'); ?></span>
			</div>
			<div class="cuadre-item">
				<span class="label">Efectivo Contado</span>
				<span class="value">$<?php echo number_format($cierre->efectivo_contado, 0, ',', '.'); ?></span>
			</div>
			<div class="cuadre-item cuadre-resultado">
				<span class="label">Diferencia</span>
				<?php 
					$diferencia = $cierre->diferencia_caja;
					$difColor = $diferencia > 0 ? '#28a745' : ($diferencia < 0 ? '#dc3545' : '#333');
					$difPrefix = $diferencia > 0 ? '+' : '';
				?>
				<span class="value" style="color: <?php echo $difColor; ?>;">
					<?php echo $difPrefix; ?>$<?php echo number_format($diferencia, 0, ',', '.'); ?>
				</span>
			</div>
		</div>
		
		<!-- Ventas del período -->
		<div class="section-title">Detalle de Ventas del Período (<?php echo count($cierre->ventas); ?> registros)</div>
		<table class="tabla">
			<thead>
				<tr>
					<th>#</th>
					<th>Folio</th>
					<th>Cliente</th>
					<th>Fecha</th>
					<th>Método Pago</th>
					<th class="text-right">Total Venta</th>
					<th class="text-right">Total Pagado</th>
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
						<td class="text-right"><strong>$<?php echo number_format($v->total_venta, 0, ',', '.'); ?></strong></td>
						<td class="text-right">$<?php echo number_format($v->total_pagado, 0, ',', '.'); ?></td>
					</tr>
					<?php endforeach; ?>
				<?php else: ?>
					<tr>
						<td colspan="7" class="text-center">No hay ventas en este período</td>
					</tr>
				<?php endif; ?>
			</tbody>
			<?php if ($cierre->ventas && count($cierre->ventas) > 0): ?>
			<tfoot>
				<tr>
					<td colspan="5" style="text-align: right;">TOTALES:</td>
					<td class="text-right">$<?php echo number_format($cierre->monto_total_vendido, 0, ',', '.'); ?></td>
					<td class="text-right">$<?php echo number_format($cierre->monto_subtotal, 0, ',', '.'); ?></td>
				</tr>
			</tfoot>
			<?php endif; ?>
		</table>
		
		<!-- Observaciones -->
		<?php if ($cierre->observaciones): ?>
		<div class="observaciones">
			<h4>Observaciones</h4>
			<p><?php echo nl2br($cierre->observaciones); ?></p>
		</div>
		<?php endif; ?>
		
		<!-- Firmas -->
		<div class="firma-section">
			<div class="firma-line">
				<div class="line"></div>
				<div class="name">Responsable de Caja</div>
			</div>
			<div class="firma-line">
				<div class="line"></div>
				<div class="name">Supervisor / Administrador</div>
			</div>
		</div>
		
		<!-- Footer -->
		<div class="reporte-footer">
			<p>Reporte generado automáticamente por Sistema Saho — <?php echo date('d/m/Y H:i:s'); ?></p>
			<p>Este documento es un soporte interno de control de caja.</p>
		</div>
	</div>
</body>
</html>
