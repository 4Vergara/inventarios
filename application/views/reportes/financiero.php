<!-- Page Header -->
<div class="page-header mb-4">
	<div class="row align-items-center">
		<div class="col-lg-4">
			<nav aria-label="breadcrumb">
				<ol class="breadcrumb mb-1">
					<li class="breadcrumb-item"><a href="<?php echo IP_SERVER . 'reportes'; ?>">Reportes</a></li>
					<li class="breadcrumb-item active">Financiero</li>
				</ol>
			</nav>
			<h1 class="page-title">Reporte Financiero</h1>
			<p class="page-subtitle">Ingresos, impuestos, descuentos, cuentas por cobrar y flujo de caja.</p>
		</div>
		<div class="col-lg-8 text-lg-end mt-3 mt-lg-0">
			<div class="d-inline-flex align-items-center gap-2 flex-wrap">
				<input type="date" class="form-control form-control-sm" id="finFechaDesde" style="width:140px">
				<span class="text-muted">a</span>
				<input type="date" class="form-control form-control-sm" id="finFechaHasta" style="width:140px">
				<button class="btn btn-sm btn-color_principal" onclick="cargarReporteFinanciero()">
					<i class="bi bi-funnel me-1"></i>Filtrar
				</button>
				<button class="btn btn-sm btn-outline-color_principal" onclick="window.open(IP_SERVER+'reportes/pdfFinanciero?desde='+document.getElementById('finFechaDesde').value+'&hasta='+document.getElementById('finFechaHasta').value, '_blank')">
					<i class="bi bi-file-pdf me-1"></i>PDF
				</button>
			</div>
		</div>
	</div>
</div>

<!-- KPI Financiero -->
<div class="row g-4 mb-4">
	<div class="col-sm-6 col-xl-3">
		<div class="stat-card">
			<div class="stat-content">
				<span class="stat-label">INGRESOS TOTALES</span>
				<div class="stat-value-row">
					<span class="stat-value" id="finIngresos">$0</span>
					<span class="stat-badge stat-badge-success"><i class="bi bi-arrow-up"></i></span>
				</div>
			</div>
		</div>
	</div>
	<div class="col-sm-6 col-xl-3">
		<div class="stat-card">
			<div class="stat-content">
				<span class="stat-label">IMPUESTOS COBRADOS</span>
				<div class="stat-value-row">
					<span class="stat-value" id="finImpuestos">$0</span>
				</div>
			</div>
		</div>
	</div>
	<div class="col-sm-6 col-xl-3">
		<div class="stat-card">
			<div class="stat-content">
				<span class="stat-label">DESCUENTOS OTORGADOS</span>
				<div class="stat-value-row">
					<span class="stat-value" id="finDescuentos">$0</span>
				</div>
			</div>
		</div>
	</div>
	<div class="col-sm-6 col-xl-3">
		<div class="stat-card stat-card-warning">
			<div class="stat-content">
				<span class="stat-label stat-label-warning">CUENTAS POR COBRAR</span>
				<div class="stat-value-row">
					<span class="stat-value stat-value-warning" id="finPorCobrar">$0</span>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Gráficas -->
<div class="row g-4 mb-4">
	<!-- Flujo de Caja -->
	<div class="col-xl-8">
		<div class="card border-0 shadow-sm">
			<div class="card-header bg-white border-bottom">
				<h6 class="mb-0 fw-bold">Ingresos Diarios</h6>
			</div>
			<div class="card-body">
				<div style="height: 340px; position: relative;">
					<canvas id="chartIngresosDia"></canvas>
				</div>
			</div>
		</div>
	</div>
	<!-- Flujo de caja por cierre -->
	<div class="col-xl-4">
		<div class="card border-0 shadow-sm h-100">
			<div class="card-header bg-white border-bottom">
				<h6 class="mb-0 fw-bold">Flujo de Caja (Cierres)</h6>
			</div>
			<div class="card-body">
				<div style="height: 340px; position: relative;">
					<canvas id="chartFlujoCaja"></canvas>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Tablas -->
<div class="row g-4 mb-4">
	<!-- Resumen Financiero -->
	<div class="col-xl-6">
		<div class="card border-0 shadow-sm">
			<div class="card-header bg-white border-bottom">
				<h6 class="mb-0 fw-bold">Resumen por Método de Pago</h6>
			</div>
			<div class="card-body">
				<div class="table-responsive">
					<table class="table table-hover table-sm">
						<thead>
							<tr>
								<th>Método</th>
								<th class="text-center">Transacciones</th>
								<th class="text-end">Monto Total</th>
								<th class="text-end">% del Total</th>
							</tr>
						</thead>
						<tbody id="tbodyResumenFinanciero"></tbody>
						<tfoot>
							<tr class="table-light fw-bold">
								<td>TOTAL</td>
								<td class="text-center" id="tfResumenTx">0</td>
								<td class="text-end" id="tfResumenMonto">$0</td>
								<td class="text-end">100%</td>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
	</div>

	<!-- Cuentas por cobrar -->
	<div class="col-xl-6">
		<div class="card border-0 shadow-sm">
			<div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
				<h6 class="mb-0 fw-bold">Cuentas por Cobrar</h6>
				<span class="badge bg-danger" id="badgePorCobrar">0</span>
			</div>
			<div class="card-body">
				<div class="table-responsive">
					<table class="table table-hover table-sm">
						<thead>
							<tr>
								<th>Cliente</th>
								<th class="text-center">N° Venta</th>
								<th class="text-center">Fecha</th>
								<th class="text-end">Total</th>
								<th class="text-end">Pagado</th>
								<th class="text-end">Pendiente</th>
							</tr>
						</thead>
						<tbody id="tbodyCuentasCobrar"></tbody>
						<tfoot>
							<tr class="table-light fw-bold">
								<td colspan="5">TOTAL PENDIENTE</td>
								<td class="text-end" id="tfTotalPendiente">$0</td>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Flujo caja detalle -->
<div class="card border-0 shadow-sm mb-4">
	<div class="card-header bg-white border-bottom">
		<h6 class="mb-0 fw-bold">Cierres de Caja</h6>
	</div>
	<div class="card-body">
		<div class="table-responsive">
			<table class="table table-hover table-sm">
				<thead>
					<tr>
						<th>Fecha</th>
						<th>Usuario</th>
						<th class="text-end">Monto Apertura</th>
						<th class="text-end">Total Ventas</th>
						<th class="text-end">Monto Cierre</th>
						<th class="text-end">Diferencia</th>
					</tr>
				</thead>
				<tbody id="tbodyFlujoCaja"></tbody>
			</table>
		</div>
	</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script src="<?php echo IP_SERVER . 'assets/js/reportes.js'; ?>"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
	document.getElementById('finFechaDesde').value = '<?php echo date("Y-01-01"); ?>';
	document.getElementById('finFechaHasta').value = '<?php echo date("Y-m-d"); ?>';
	cargarReporteFinanciero();
});
</script>
