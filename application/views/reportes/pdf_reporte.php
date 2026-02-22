<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Reporte <?php echo ucfirst($tipo); ?> - Saho</title>
	<style>
		* { margin: 0; padding: 0; box-sizing: border-box; }
		body { font-family: 'Segoe UI', Arial, sans-serif; color: #333; font-size: 12px; padding: 20px; }
		.header { display: flex; justify-content: space-between; align-items: center; border-bottom: 3px solid #e8630a; padding-bottom: 15px; margin-bottom: 20px; }
		.header h1 { font-size: 20px; color: #e8630a; }
		.header .info { text-align: right; font-size: 11px; color: #666; }
		.kpi-row { display: flex; gap: 15px; margin-bottom: 20px; flex-wrap: wrap; }
		.kpi-box { flex: 1; min-width: 150px; background: #f8f9fa; border-radius: 6px; padding: 12px; border-left: 4px solid #e8630a; }
		.kpi-box .label { font-size: 10px; text-transform: uppercase; color: #888; margin-bottom: 4px; }
		.kpi-box .value { font-size: 18px; font-weight: bold; color: #333; }
		table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
		th { background: #f1f3f5; padding: 8px 10px; text-align: left; font-size: 11px; text-transform: uppercase; color: #555; border-bottom: 2px solid #dee2e6; }
		td { padding: 7px 10px; border-bottom: 1px solid #eee; }
		tr:hover { background: #fafafa; }
		.text-end { text-align: right; }
		.text-center { text-align: center; }
		.section-title { font-size: 14px; font-weight: bold; color: #e8630a; margin: 20px 0 10px; padding-bottom: 5px; border-bottom: 1px solid #eee; }
		.footer { text-align: center; color: #999; font-size: 10px; margin-top: 30px; padding-top: 15px; border-top: 1px solid #eee; }
		.badge-success { background: #d4edda; color: #155724; padding: 2px 8px; border-radius: 10px; font-size: 10px; }
		.badge-danger { background: #f8d7da; color: #721c24; padding: 2px 8px; border-radius: 10px; font-size: 10px; }
		.badge-warning { background: #fff3cd; color: #856404; padding: 2px 8px; border-radius: 10px; font-size: 10px; }
		@media print {
			body { padding: 10px; }
			.no-print { display: none !important; }
			@page { margin: 1cm; }
		}
	</style>
</head>
<body>
	<!-- Botón imprimir -->
	<div class="no-print" style="text-align: right; margin-bottom: 15px;">
		<button onclick="window.print()" style="padding: 8px 20px; background: #e8630a; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 13px;">
			<b>Imprimir / Guardar PDF</b>
		</button>
	</div>

	<!-- Cabecera -->
	<div class="header">
		<div>
			<h1>Reporte de <?php echo ucfirst($tipo); ?></h1>
			<?php if (!empty($empresa)): ?>
				<p style="color: #666; margin-top: 4px;"><?php echo $empresa->razon_social; ?></p>
			<?php endif; ?>
		</div>
		<div class="info">
			<p><strong>Fecha:</strong> <?php echo date('d/m/Y H:i'); ?></p>
			<?php if (!empty($desde) && !empty($hasta)): ?>
				<p><strong>Período:</strong> <?php echo date('d/m/Y', strtotime($desde)) . ' - ' . date('d/m/Y', strtotime($hasta)); ?></p>
			<?php endif; ?>
			<p><strong>Generado por:</strong> <?php echo $usuario; ?></p>
		</div>
	</div>

	<?php if ($tipo == 'ventas'): ?>
		<!-- REPORTE DE VENTAS -->
		<div class="kpi-row">
			<div class="kpi-box">
				<div class="label">Total Ventas</div>
				<div class="value"><?php echo $datos['total_ventas']; ?></div>
			</div>
			<div class="kpi-box">
				<div class="label">Monto Total</div>
				<div class="value">$<?php echo number_format($datos['monto_total'], 2); ?></div>
			</div>
			<div class="kpi-box">
				<div class="label">Promedio por Venta</div>
				<div class="value">$<?php echo number_format($datos['promedio'], 2); ?></div>
			</div>
		</div>

		<?php if (!empty($datos['por_periodo'])): ?>
			<div class="section-title">Ventas por Período</div>
			<table>
				<thead><tr><th>Período</th><th class="text-center">N° Ventas</th><th class="text-end">Monto Total</th></tr></thead>
				<tbody>
					<?php foreach ($datos['por_periodo'] as $row): ?>
						<tr>
							<td><?php echo $row->periodo; ?></td>
							<td class="text-center"><?php echo $row->total_ventas; ?></td>
							<td class="text-end">$<?php echo number_format($row->monto_total, 2); ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif; ?>

		<?php if (!empty($datos['por_metodo'])): ?>
			<div class="section-title">Ventas por Método de Pago</div>
			<table>
				<thead><tr><th>Método</th><th class="text-center">Transacciones</th><th class="text-end">Monto Total</th></tr></thead>
				<tbody>
					<?php foreach ($datos['por_metodo'] as $row): ?>
						<tr>
							<td><?php echo ucfirst($row->metodo_pago); ?></td>
							<td class="text-center"><?php echo $row->total_ventas; ?></td>
							<td class="text-end">$<?php echo number_format($row->monto_total, 2); ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif; ?>

		<?php if (!empty($datos['por_vendedor'])): ?>
			<div class="section-title">Ventas por Vendedor</div>
			<table>
				<thead><tr><th>Vendedor</th><th class="text-center">N° Ventas</th><th class="text-end">Monto Total</th></tr></thead>
				<tbody>
					<?php foreach ($datos['por_vendedor'] as $row): ?>
						<tr>
							<td><?php echo $row->vendedor; ?></td>
							<td class="text-center"><?php echo $row->total_ventas; ?></td>
							<td class="text-end">$<?php echo number_format($row->monto_total, 2); ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif; ?>

	<?php elseif ($tipo == 'productos'): ?>
		<!-- REPORTE DE PRODUCTOS -->
		<?php if (!empty($datos['mas_vendidos'])): ?>
			<div class="section-title">Productos Más Vendidos</div>
			<table>
				<thead><tr><th>#</th><th>Producto</th><th class="text-center">Cantidad</th><th class="text-end">Ingresos</th></tr></thead>
				<tbody>
					<?php foreach ($datos['mas_vendidos'] as $i => $row): ?>
						<tr>
							<td><?php echo $i+1; ?></td>
							<td><?php echo $row->nombre; ?></td>
							<td class="text-center"><?php echo $row->total_vendido; ?></td>
							<td class="text-end">$<?php echo number_format($row->total_ingresos, 2); ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif; ?>

		<?php if (!empty($datos['stock_bajo'])): ?>
			<div class="section-title">Productos con Stock Bajo</div>
			<table>
				<thead><tr><th>Código</th><th>Producto</th><th class="text-center">Stock</th><th class="text-center">Mínimo</th><th class="text-center">Déficit</th></tr></thead>
				<tbody>
					<?php foreach ($datos['stock_bajo'] as $row): ?>
						<tr>
							<td><?php echo $row->codigo; ?></td>
							<td><?php echo $row->nombre; ?></td>
							<td class="text-center"><span class="badge-danger"><?php echo $row->stock; ?></span></td>
							<td class="text-center"><?php echo $row->stock_minimo; ?></td>
							<td class="text-center"><?php echo $row->stock_minimo - $row->stock; ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif; ?>

		<?php if (!empty($datos['por_vencer'])): ?>
			<div class="section-title">Productos por Vencer (30 días)</div>
			<table>
				<thead><tr><th>Código</th><th>Producto</th><th class="text-center">Stock</th><th class="text-center">Vencimiento</th><th class="text-center">Días</th></tr></thead>
				<tbody>
					<?php foreach ($datos['por_vencer'] as $row): ?>
						<tr>
							<td><?php echo $row->codigo; ?></td>
							<td><?php echo $row->nombre; ?></td>
							<td class="text-center"><?php echo $row->stock; ?></td>
							<td class="text-center"><?php echo date('d/m/Y', strtotime($row->fecha_vencimiento)); ?></td>
							<td class="text-center">
								<?php
								$d = $row->dias_restantes;
								$cls = $d <= 7 ? 'badge-danger' : ($d <= 15 ? 'badge-warning' : 'badge-success');
								?>
								<span class="<?php echo $cls; ?>"><?php echo $d; ?></span>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif; ?>

	<?php elseif ($tipo == 'clientes'): ?>
		<!-- REPORTE DE CLIENTES -->
		<?php if (!empty($datos['mas_compras'])): ?>
			<div class="section-title">Clientes con Más Compras</div>
			<table>
				<thead><tr><th>#</th><th>Cliente</th><th class="text-center">Compras</th><th class="text-end">Total</th></tr></thead>
				<tbody>
					<?php foreach ($datos['mas_compras'] as $i => $row): ?>
						<tr>
							<td><?php echo $i+1; ?></td>
							<td><?php echo $row->cliente; ?></td>
							<td class="text-center"><?php echo $row->total_compras; ?></td>
							<td class="text-end">$<?php echo number_format($row->monto_total, 2); ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif; ?>

		<?php if (!empty($datos['mayor_facturacion'])): ?>
			<div class="section-title">Mayor Facturación</div>
			<table>
				<thead><tr><th>#</th><th>Cliente</th><th class="text-end">Facturado</th><th class="text-center">N° Facturas</th></tr></thead>
				<tbody>
					<?php foreach ($datos['mayor_facturacion'] as $i => $row): ?>
						<tr>
							<td><?php echo $i+1; ?></td>
							<td><?php echo $row->cliente; ?></td>
							<td class="text-end">$<?php echo number_format($row->total_facturado, 2); ?></td>
							<td class="text-center"><?php echo $row->total_facturas; ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif; ?>

		<?php if (!empty($datos['inactivos'])): ?>
			<div class="section-title">Clientes Inactivos</div>
			<table>
				<thead><tr><th>Cliente</th><th>Documento</th><th>Correo</th><th class="text-center">Última Compra</th><th class="text-center">Días</th></tr></thead>
				<tbody>
					<?php foreach ($datos['inactivos'] as $row): ?>
						<tr>
							<td><?php echo $row->cliente; ?></td>
							<td><?php echo $row->numero_documento; ?></td>
							<td><?php echo $row->correo; ?></td>
							<td class="text-center"><?php echo $row->ultima_compra ? date('d/m/Y', strtotime($row->ultima_compra)) : 'Nunca'; ?></td>
							<td class="text-center"><span class="badge-warning"><?php echo $row->dias_inactivo; ?></span></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif; ?>

	<?php elseif ($tipo == 'financiero'): ?>
		<!-- REPORTE FINANCIERO -->
		<div class="kpi-row">
			<div class="kpi-box">
				<div class="label">Ingresos Totales</div>
				<div class="value">$<?php echo number_format($datos['resumen']->ingresos_totales ?? 0, 2); ?></div>
			</div>
			<div class="kpi-box">
				<div class="label">Impuestos</div>
				<div class="value">$<?php echo number_format($datos['resumen']->total_impuestos ?? 0, 2); ?></div>
			</div>
			<div class="kpi-box">
				<div class="label">Descuentos</div>
				<div class="value">$<?php echo number_format($datos['resumen']->total_descuentos ?? 0, 2); ?></div>
			</div>
		</div>

		<?php if (!empty($datos['cuentas_cobrar'])): ?>
			<div class="section-title">Cuentas por Cobrar</div>
			<table>
				<thead><tr><th>Cliente</th><th class="text-center">N° Venta</th><th class="text-center">Fecha</th><th class="text-end">Total</th><th class="text-end">Pagado</th><th class="text-end">Pendiente</th></tr></thead>
				<tbody>
					<?php $totalPend = 0; foreach ($datos['cuentas_cobrar'] as $row): $totalPend += $row->saldo_pendiente; ?>
						<tr>
							<td><?php echo $row->cliente; ?></td>
							<td class="text-center"><?php echo $row->id_venta; ?></td>
							<td class="text-center"><?php echo date('d/m/Y', strtotime($row->fecha_venta)); ?></td>
							<td class="text-end">$<?php echo number_format($row->total_venta, 2); ?></td>
							<td class="text-end">$<?php echo number_format($row->total_pagado, 2); ?></td>
							<td class="text-end"><strong>$<?php echo number_format($row->saldo_pendiente, 2); ?></strong></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
				<tfoot>
					<tr style="background:#f1f3f5;"><td colspan="5"><strong>TOTAL PENDIENTE</strong></td><td class="text-end"><strong>$<?php echo number_format($totalPend, 2); ?></strong></td></tr>
				</tfoot>
			</table>
		<?php endif; ?>

		<?php if (!empty($datos['flujo_caja'])): ?>
			<div class="section-title">Cierres de Caja</div>
			<table>
				<thead><tr><th>Fecha</th><th>Usuario</th><th class="text-end">Apertura</th><th class="text-end">Ventas</th><th class="text-end">Cierre</th><th class="text-end">Diferencia</th></tr></thead>
				<tbody>
					<?php foreach ($datos['flujo_caja'] as $row): ?>
						<tr>
							<td><?php echo $row->fecha_cierre; ?></td>
							<td><?php echo $row->usuario; ?></td>
							<td class="text-end">$<?php echo number_format($row->monto_apertura, 2); ?></td>
							<td class="text-end">$<?php echo number_format($row->total_ventas, 2); ?></td>
							<td class="text-end">$<?php echo number_format($row->monto_cierre, 2); ?></td>
							<td class="text-end">
								<?php $diff = $row->monto_cierre - ($row->monto_apertura + $row->total_ventas); ?>
								<span class="<?php echo $diff < 0 ? 'badge-danger' : 'badge-success'; ?>">
									$<?php echo number_format($diff, 2); ?>
								</span>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif; ?>
	<?php endif; ?>

	<div class="footer">
		<p>Reporte generado por Sistema Saho &mdash; <?php echo date('d/m/Y H:i:s'); ?></p>
	</div>
</body>
</html>
