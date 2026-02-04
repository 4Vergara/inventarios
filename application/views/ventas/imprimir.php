<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Ticket - <?php echo $venta->folio_factura; ?></title>
	<style>
		* {
			margin: 0;
			padding: 0;
			box-sizing: border-box;
		}
		
		body {
			font-family: 'Courier New', Courier, monospace;
			font-size: 12px;
			line-height: 1.4;
			color: #000;
			background: #fff;
		}
		
		.ticket {
			width: 80mm;
			max-width: 100%;
			margin: 0 auto;
			padding: 10px;
		}
		
		.header {
			text-align: center;
			border-bottom: 1px dashed #000;
			padding-bottom: 10px;
			margin-bottom: 10px;
		}
		
		.logo {
			font-size: 24px;
			font-weight: bold;
			margin-bottom: 5px;
		}
		
		.empresa-info {
			font-size: 10px;
		}
		
		.folio {
			text-align: center;
			font-size: 14px;
			font-weight: bold;
			margin: 10px 0;
			padding: 8px;
			background: #f0f0f0;
			border-radius: 4px;
		}
		
		.info-row {
			display: flex;
			justify-content: space-between;
			margin: 4px 0;
		}
		
		.info-label {
			font-weight: bold;
		}
		
		.divider {
			border-top: 1px dashed #000;
			margin: 10px 0;
		}
		
		.productos-header {
			display: flex;
			font-weight: bold;
			padding: 5px 0;
			border-bottom: 1px solid #000;
			font-size: 10px;
		}
		
		.productos-header .col-producto {
			flex: 1;
		}
		
		.productos-header .col-cant,
		.productos-header .col-precio,
		.productos-header .col-subtotal {
			width: 60px;
			text-align: right;
		}
		
		.producto-row {
			display: flex;
			padding: 5px 0;
			border-bottom: 1px dotted #ccc;
			font-size: 11px;
		}
		
		.producto-row .col-producto {
			flex: 1;
		}
		
		.producto-row .col-cant,
		.producto-row .col-precio,
		.producto-row .col-subtotal {
			width: 60px;
			text-align: right;
		}
		
		.totales {
			margin-top: 10px;
		}
		
		.total-row {
			display: flex;
			justify-content: space-between;
			padding: 4px 0;
		}
		
		.total-row.total-final {
			font-size: 16px;
			font-weight: bold;
			border-top: 2px solid #000;
			border-bottom: 2px solid #000;
			padding: 8px 0;
			margin: 5px 0;
		}
		
		.pagos-section {
			margin-top: 10px;
			padding-top: 10px;
			border-top: 1px dashed #000;
		}
		
		.pagos-title {
			font-weight: bold;
			margin-bottom: 5px;
		}
		
		.pago-row {
			display: flex;
			justify-content: space-between;
			font-size: 10px;
			padding: 2px 0;
		}
		
		.saldo-row {
			display: flex;
			justify-content: space-between;
			font-weight: bold;
			padding: 5px 0;
			margin-top: 5px;
			border-top: 1px solid #000;
		}
		
		.footer {
			text-align: center;
			margin-top: 20px;
			padding-top: 10px;
			border-top: 1px dashed #000;
			font-size: 10px;
		}
		
		.footer .gracias {
			font-size: 14px;
			font-weight: bold;
			margin-bottom: 5px;
		}
		
		.qr-code {
			text-align: center;
			margin: 15px 0;
		}
		
		@media print {
			body {
				-webkit-print-color-adjust: exact;
				print-color-adjust: exact;
			}
			
			.no-print {
				display: none !important;
			}
			
			.ticket {
				width: 100%;
			}
		}
		
		.btn-imprimir {
			position: fixed;
			top: 20px;
			right: 20px;
			padding: 12px 24px;
			background: #f97316;
			color: white;
			border: none;
			border-radius: 8px;
			font-size: 14px;
			cursor: pointer;
			box-shadow: 0 4px 12px rgba(0,0,0,0.2);
		}
		
		.btn-imprimir:hover {
			background: #ea580c;
		}
	</style>
</head>
<body>
	<button class="btn-imprimir no-print" onclick="window.print()">
		🖨️ Imprimir Ticket
	</button>
	
	<div class="ticket">
		<!-- Header -->
		<div class="header">
			<div class="logo">SAHO</div>
			<div class="empresa-info">
				Sistema de Inventarios<br>
				Tel: (000) 000-0000<br>
				www.empresa.com
			</div>
		</div>
		
		<!-- Folio -->
		<div class="folio">
			<?php echo $venta->folio_factura; ?>
		</div>
		
		<!-- Info de la venta -->
		<div class="info-section">
			<div class="info-row">
				<span class="info-label">Fecha:</span>
				<span><?php echo date('d/m/Y H:i', strtotime($venta->fecha_venta)); ?></span>
			</div>
			<div class="info-row">
				<span class="info-label">Cliente:</span>
				<span><?php echo $venta->cliente_nombre ?: 'Público General'; ?></span>
			</div>
			<?php if ($venta->cliente_documento): ?>
			<div class="info-row">
				<span class="info-label">Doc:</span>
				<span><?php echo $venta->cliente_documento; ?></span>
			</div>
			<?php endif; ?>
			<div class="info-row">
				<span class="info-label">Atendió:</span>
				<span><?php echo $venta->vendedor_nombre ?: 'N/A'; ?></span>
			</div>
		</div>
		
		<div class="divider"></div>
		
		<!-- Productos -->
		<div class="productos-header">
			<div class="col-producto">Producto</div>
			<div class="col-cant">Cant</div>
			<div class="col-precio">P.U.</div>
			<div class="col-subtotal">Subt.</div>
		</div>
		
		<?php foreach ($venta->detalles as $detalle): ?>
		<div class="producto-row">
			<div class="col-producto">
				<?php echo strlen($detalle->nombre_historico) > 20 
					? substr($detalle->nombre_historico, 0, 20) . '...' 
					: $detalle->nombre_historico; ?>
			</div>
			<div class="col-cant"><?php echo $detalle->cantidad; ?></div>
			<div class="col-precio">$<?php echo number_format($detalle->precio_unitario, 0); ?></div>
			<div class="col-subtotal">$<?php echo number_format($detalle->subtotal_linea, 0); ?></div>
		</div>
		<?php endforeach; ?>
		
		<!-- Totales -->
		<div class="totales">
			<div class="total-row">
				<span>Subtotal:</span>
				<span>$<?php echo number_format($venta->subtotal, 2); ?></span>
			</div>
			<?php if ($venta->total_impuestos > 0): ?>
			<div class="total-row">
				<span>Impuestos:</span>
				<span>$<?php echo number_format($venta->total_impuestos, 2); ?></span>
			</div>
			<?php endif; ?>
			<?php if ($venta->total_descuentos > 0): ?>
			<div class="total-row">
				<span>Descuentos:</span>
				<span>-$<?php echo number_format($venta->total_descuentos, 2); ?></span>
			</div>
			<?php endif; ?>
			<div class="total-row total-final">
				<span>TOTAL:</span>
				<span>$<?php echo number_format($venta->total_final, 2); ?></span>
			</div>
		</div>
		
		<!-- Pagos -->
		<?php if (!empty($venta->pagos)): ?>
		<div class="pagos-section">
			<div class="pagos-title">PAGOS RECIBIDOS</div>
			<?php foreach ($venta->pagos as $pago): ?>
			<div class="pago-row">
				<span><?php echo $pago->metodo_pago; ?></span>
				<span>$<?php echo number_format($pago->monto, 2); ?></span>
			</div>
			<?php endforeach; ?>
			
			<div class="saldo-row">
				<span>SALDO:</span>
				<span>$<?php echo number_format($venta->saldo_pendiente, 2); ?></span>
			</div>
		</div>
		<?php endif; ?>
		
		<!-- Footer -->
		<div class="footer">
			<div class="gracias">¡Gracias por su compra!</div>
			<p>Conserve este ticket para cualquier aclaración.</p>
			<p>Impreso: <?php echo date('d/m/Y H:i:s'); ?></p>
		</div>
	</div>
	
	<script>
		// Auto-print si se abre en nueva ventana
		// window.onload = function() { window.print(); }
	</script>
</body>
</html>
