<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Factura <?php echo $factura->numero_factura; ?></title>
	<style>
		* { margin: 0; padding: 0; box-sizing: border-box; }
		
		body {
			font-family: 'Segoe UI', Arial, Helvetica, sans-serif;
			font-size: 12px;
			line-height: 1.5;
			color: #333;
			background: #f5f5f5;
		}
		
		.factura-container {
			width: 210mm;
			min-height: 297mm;
			margin: 0 auto;
			padding: 20mm 15mm;
			background: #fff;
			box-shadow: 0 0 10px rgba(0,0,0,0.1);
		}
		
		/* Encabezado */
		.factura-header {
			display: flex;
			justify-content: space-between;
			align-items: flex-start;
			border-bottom: 3px solid #2c3e50;
			padding-bottom: 15px;
			margin-bottom: 15px;
		}
		
		.empresa-info h1 {
			font-size: 20px;
			color: #2c3e50;
			margin-bottom: 5px;
		}
		
		.empresa-info p {
			font-size: 11px;
			color: #555;
			margin: 2px 0;
		}
		
		.factura-numero-box {
			text-align: right;
			background: #2c3e50;
			color: #fff;
			padding: 15px 20px;
			border-radius: 4px;
			min-width: 250px;
		}
		
		.factura-numero-box h2 {
			font-size: 14px;
			font-weight: 400;
			margin-bottom: 3px;
		}
		
		.factura-numero-box .numero {
			font-size: 18px;
			font-weight: 700;
			letter-spacing: 1px;
		}
		
		.factura-numero-box .fecha {
			font-size: 11px;
			margin-top: 8px;
			opacity: 0.9;
		}
		
		/* Resolución DIAN */
		.resolucion-dian {
			text-align: center;
			font-size: 9px;
			color: #666;
			border: 1px solid #ddd;
			padding: 6px 10px;
			margin-bottom: 15px;
			background: #fafafa;
		}
		
		/* Info boxes */
		.info-grid {
			display: flex;
			gap: 15px;
			margin-bottom: 15px;
		}
		
		.info-box {
			flex: 1;
			border: 1px solid #ddd;
			border-radius: 4px;
			padding: 12px;
		}
		
		.info-box h3 {
			font-size: 11px;
			text-transform: uppercase;
			color: #2c3e50;
			letter-spacing: 1px;
			margin-bottom: 8px;
			padding-bottom: 5px;
			border-bottom: 1px solid #eee;
		}
		
		.info-row {
			display: flex;
			justify-content: space-between;
			margin: 3px 0;
			font-size: 11px;
		}
		
		.info-row .label {
			color: #666;
			font-weight: 600;
		}
		
		/* Tabla productos */
		.tabla-productos {
			width: 100%;
			border-collapse: collapse;
			margin: 15px 0;
		}
		
		.tabla-productos thead th {
			background: #2c3e50;
			color: #fff;
			padding: 8px 10px;
			font-size: 10px;
			text-transform: uppercase;
			letter-spacing: 0.5px;
			text-align: left;
		}
		
		.tabla-productos thead th.text-right {
			text-align: right;
		}
		
		.tabla-productos thead th.text-center {
			text-align: center;
		}
		
		.tabla-productos tbody td {
			padding: 8px 10px;
			border-bottom: 1px solid #eee;
			font-size: 11px;
		}
		
		.tabla-productos tbody td.text-right {
			text-align: right;
		}
		
		.tabla-productos tbody td.text-center {
			text-align: center;
		}
		
		.tabla-productos tbody tr:nth-child(even) {
			background: #f9f9f9;
		}
		
		/* Totales */
		.totales-section {
			display: flex;
			justify-content: flex-end;
			margin: 15px 0;
		}
		
		.totales-box {
			width: 300px;
			border: 1px solid #ddd;
			border-radius: 4px;
			overflow: hidden;
		}
		
		.total-row {
			display: flex;
			justify-content: space-between;
			padding: 8px 15px;
			font-size: 11px;
		}
		
		.total-row.total-final {
			background: #2c3e50;
			color: #fff;
			font-size: 14px;
			font-weight: 700;
		}
		
		.total-row:not(:last-child) {
			border-bottom: 1px solid #eee;
		}
		
		/* Pago info */
		.pago-info {
			display: flex;
			gap: 15px;
			margin: 15px 0;
			font-size: 11px;
		}
		
		.pago-info-item {
			flex: 1;
			background: #f8f9fa;
			padding: 10px;
			border-radius: 4px;
			text-align: center;
		}
		
		.pago-info-item .label {
			display: block;
			font-size: 9px;
			text-transform: uppercase;
			color: #666;
			margin-bottom: 3px;
		}
		
		.pago-info-item .value {
			font-weight: 700;
			color: #2c3e50;
		}
		
		/* Observaciones */
		.observaciones {
			margin: 15px 0;
			padding: 10px;
			border: 1px solid #ddd;
			border-radius: 4px;
			font-size: 11px;
		}
		
		.observaciones h4 {
			font-size: 10px;
			text-transform: uppercase;
			color: #666;
			margin-bottom: 5px;
		}
		
		/* Footer */
		.factura-footer {
			margin-top: 30px;
			border-top: 2px solid #eee;
			padding-top: 15px;
			text-align: center;
			font-size: 9px;
			color: #999;
		}
		
		/* Estado anulada */
		.sello-anulada {
			position: relative;
		}
		
		.sello-anulada::after {
			content: 'ANULADA';
			position: fixed;
			top: 45%;
			left: 50%;
			transform: translate(-50%, -50%) rotate(-30deg);
			font-size: 100px;
			font-weight: 900;
			color: rgba(220, 53, 69, 0.15);
			z-index: 1;
			pointer-events: none;
			letter-spacing: 10px;
		}
		
		/* Imprimir */
		@media print {
			body { background: #fff; }
			.factura-container {
				box-shadow: none;
				margin: 0;
				padding: 10mm;
			}
			.no-print { display: none !important; }
			@page {
				size: letter;
				margin: 0;
			}
		}
		
		/* Botones */
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
		
		.btn-print {
			background: #2c3e50;
			color: #fff;
		}
		
		.btn-back {
			background: #eee;
			color: #333;
		}
	</style>
</head>
<body class="<?php echo $factura->estado === 'anulada' ? 'sello-anulada' : ''; ?>">
	
	<!-- Toolbar -->
	<div class="toolbar no-print">
		<button class="btn-print" onclick="window.print()">
			<span style="margin-right: 5px;">🖨️</span> Imprimir / Descargar PDF
		</button>
		<button class="btn-back" onclick="history.back()">
			← Volver
		</button>
	</div>
	
	<div class="factura-container">
		<!-- Encabezado -->
		<div class="factura-header">
			<div class="empresa-info">
				<h1><?php echo $factura->emisor_razon_social; ?></h1>
				<p><strong>NIT:</strong> <?php echo $factura->emisor_nit; ?></p>
				<p><?php echo $factura->emisor_direccion; ?></p>
				<p><?php echo $factura->emisor_ciudad . ', ' . $factura->emisor_departamento; ?></p>
				<p>Tel: <?php echo $factura->emisor_telefono; ?> | <?php echo $factura->emisor_correo; ?></p>
				<p>Régimen: <?php echo $factura->emisor_regimen; ?></p>
				<?php if ($factura->emisor_actividad_economica): ?>
				<p>Actividad Económica: <?php echo $factura->emisor_actividad_economica; ?></p>
				<?php endif; ?>
			</div>
			<div class="factura-numero-box">
				<h2>FACTURA DE VENTA</h2>
				<div class="numero"><?php echo $factura->numero_factura; ?></div>
				<div class="fecha">
					Fecha: <?php echo date('d/m/Y', strtotime($factura->fecha_factura)); ?><br>
					Hora: <?php echo date('H:i:s', strtotime($factura->fecha_factura)); ?>
				</div>
			</div>
		</div>
		
		<!-- Resolución DIAN -->
		<?php if ($factura->emisor_resolucion): ?>
		<div class="resolucion-dian">
			<?php echo $factura->emisor_resolucion; ?> de <?php echo date('d/m/Y', strtotime($factura->emisor_fecha_resolucion)); ?> — 
			Rango autorizado del <?php echo $factura->emisor_prefijo; ?> <?php echo $factura->emisor_rango_desde; ?> al <?php echo $factura->emisor_prefijo; ?> <?php echo $factura->emisor_rango_hasta; ?>
		</div>
		<?php endif; ?>
		
		<!-- Datos de cliente y factura -->
		<div class="info-grid">
			<div class="info-box">
				<h3>Datos del Cliente</h3>
				<div class="info-row">
					<span class="label">Razón Social:</span>
					<span><?php echo $factura->cliente_razon_social; ?></span>
				</div>
				<div class="info-row">
					<span class="label">NIT / CC:</span>
					<span><?php echo $factura->cliente_nit_cc; ?></span>
				</div>
				<?php if ($factura->cliente_direccion): ?>
				<div class="info-row">
					<span class="label">Dirección:</span>
					<span><?php echo $factura->cliente_direccion; ?></span>
				</div>
				<?php endif; ?>
				<?php if ($factura->cliente_correo): ?>
				<div class="info-row">
					<span class="label">Correo:</span>
					<span><?php echo $factura->cliente_correo; ?></span>
				</div>
				<?php endif; ?>
				<?php if ($factura->cliente_telefono): ?>
				<div class="info-row">
					<span class="label">Teléfono:</span>
					<span><?php echo $factura->cliente_telefono; ?></span>
				</div>
				<?php endif; ?>
			</div>
			<div class="info-box">
				<h3>Información de Factura</h3>
				<div class="info-row">
					<span class="label">Forma de Pago:</span>
					<span><?php echo $factura->forma_pago; ?></span>
				</div>
				<div class="info-row">
					<span class="label">Medio de Pago:</span>
					<span><?php echo $factura->medio_pago; ?></span>
				</div>
				<?php if ($factura->fecha_vencimiento): ?>
				<div class="info-row">
					<span class="label">Vencimiento:</span>
					<span><?php echo date('d/m/Y', strtotime($factura->fecha_vencimiento)); ?></span>
				</div>
				<?php endif; ?>
				<div class="info-row">
					<span class="label">Venta de origen:</span>
					<span><?php echo $factura->folio_venta; ?></span>
				</div>
				<div class="info-row">
					<span class="label">Vendedor:</span>
					<span><?php echo $factura->vendedor_nombre; ?></span>
				</div>
			</div>
		</div>
		
		<!-- Tabla de productos -->
		<table class="tabla-productos">
			<thead>
				<tr>
					<th style="width: 40px;">#</th>
					<th>Descripción</th>
					<th style="width: 70px;">SKU</th>
					<th class="text-center" style="width: 60px;">Cant.</th>
					<th class="text-right" style="width: 100px;">Precio Unit.</th>
					<th class="text-center" style="width: 60px;">IVA %</th>
					<th class="text-right" style="width: 100px;">IVA $</th>
					<th class="text-right" style="width: 110px;">Subtotal</th>
				</tr>
			</thead>
			<tbody>
				<?php $i = 1; foreach ($factura->detalles as $det): ?>
				<tr>
					<td><?php echo $i++; ?></td>
					<td><strong><?php echo $det->nombre_producto; ?></strong></td>
					<td><?php echo $det->sku ?: '-'; ?></td>
					<td class="text-center"><?php echo $det->cantidad; ?></td>
					<td class="text-right">$<?php echo number_format($det->precio_unitario, 0, ',', '.'); ?></td>
					<td class="text-center"><?php echo number_format($det->porcentaje_iva, 0); ?>%</td>
					<td class="text-right">$<?php echo number_format($det->monto_iva, 0, ',', '.'); ?></td>
					<td class="text-right"><strong>$<?php echo number_format($det->subtotal_linea, 0, ',', '.'); ?></strong></td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		
		<!-- Totales -->
		<div class="totales-section">
			<div class="totales-box">
				<div class="total-row">
					<span>Subtotal (Base Gravable):</span>
					<span>$<?php echo number_format($factura->subtotal, 0, ',', '.'); ?></span>
				</div>
				<div class="total-row">
					<span>Total IVA:</span>
					<span>$<?php echo number_format($factura->total_iva, 0, ',', '.'); ?></span>
				</div>
				<?php if ($factura->total_descuentos > 0): ?>
				<div class="total-row">
					<span>Descuentos:</span>
					<span>-$<?php echo number_format($factura->total_descuentos, 0, ',', '.'); ?></span>
				</div>
				<?php endif; ?>
				<div class="total-row total-final">
					<span>TOTAL FACTURA:</span>
					<span>$<?php echo number_format($factura->total_final, 0, ',', '.'); ?></span>
				</div>
			</div>
		</div>
		
		<!-- Estado de pagos -->
		<div class="pago-info">
			<div class="pago-info-item">
				<span class="label">Total Factura</span>
				<span class="value">$<?php echo number_format($factura->total_final, 0, ',', '.'); ?></span>
			</div>
			<div class="pago-info-item">
				<span class="label">Total Pagado</span>
				<span class="value" style="color: #28a745;">$<?php echo number_format($factura->total_pagado, 0, ',', '.'); ?></span>
			</div>
			<div class="pago-info-item">
				<span class="label">Saldo Pendiente</span>
				<span class="value" style="color: <?php echo $factura->saldo_pendiente > 0 ? '#dc3545' : '#28a745'; ?>;">
					$<?php echo number_format($factura->saldo_pendiente, 0, ',', '.'); ?>
				</span>
			</div>
		</div>
		
		<!-- Observaciones -->
		<?php if ($factura->observaciones): ?>
		<div class="observaciones">
			<h4>Observaciones</h4>
			<p><?php echo nl2br($factura->observaciones); ?></p>
		</div>
		<?php endif; ?>
		
		<!-- Footer -->
		<div class="factura-footer">
			<p><strong><?php echo $factura->emisor_razon_social; ?></strong> — NIT: <?php echo $factura->emisor_nit; ?></p>
			<p><?php echo $factura->emisor_direccion; ?>, <?php echo $factura->emisor_ciudad; ?> — <?php echo $factura->emisor_correo; ?></p>
			<p style="margin-top: 10px;">Esta factura se asimila en todos sus efectos a una letra de cambio (Art. 774 del Código de Comercio).</p>
			<p>Generada por Sistema Saho — <?php echo date('d/m/Y H:i:s'); ?></p>
		</div>
	</div>
</body>
</html>
