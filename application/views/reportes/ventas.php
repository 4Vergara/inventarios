<!-- Page Header -->
<div class="page-header mb-4">
	<div class="row align-items-center">
		<div class="col-lg-4">
			<nav aria-label="breadcrumb">
				<ol class="breadcrumb mb-1">
					<li class="breadcrumb-item"><a href="<?php echo IP_SERVER . 'reportes'; ?>">Reportes</a></li>
					<li class="breadcrumb-item active">Ventas</li>
				</ol>
			</nav>
			<h1 class="page-title">Reporte de Ventas</h1>
			<p class="page-subtitle">Análisis detallado de ventas por período, vendedor y método de pago.</p>
		</div>
		<div class="col-lg-8 text-lg-end mt-3 mt-lg-0">
			<div class="d-inline-flex align-items-center gap-2 flex-wrap">
				<input type="date" class="form-control form-control-sm" id="ventaFechaDesde" style="width:140px">
				<span class="text-muted">a</span>
				<input type="date" class="form-control form-control-sm" id="ventaFechaHasta" style="width:140px">
				<select class="form-select form-select-sm" id="ventaAgrupacion" style="width:120px">
					<option value="dia">Por Día</option>
					<option value="semana">Por Semana</option>
					<option value="mes" selected>Por Mes</option>
					<option value="anio">Por Año</option>
				</select>
				<button class="btn btn-sm btn-color_principal" onclick="cargarReporteVentas()">
					<i class="bi bi-funnel me-1"></i>Filtrar
				</button>
				<button class="btn btn-sm btn-outline-color_principal" onclick="window.open(IP_SERVER+'reportes/pdfVentas?desde='+document.getElementById('ventaFechaDesde').value+'&hasta='+document.getElementById('ventaFechaHasta').value, '_blank')">
					<i class="bi bi-file-pdf me-1"></i>PDF
				</button>
			</div>
		</div>
	</div>
</div>

<!-- KPI -->
<div class="row g-4 mb-4">
	<div class="col-sm-6 col-xl-3">
		<div class="stat-card">
			<div class="stat-content">
				<span class="stat-label">TOTAL VENTAS</span>
				<div class="stat-value-row">
					<span class="stat-value" id="rvTotalVentas">0</span>
				</div>
			</div>
		</div>
	</div>
	<div class="col-sm-6 col-xl-3">
		<div class="stat-card">
			<div class="stat-content">
				<span class="stat-label">MONTO TOTAL</span>
				<div class="stat-value-row">
					<span class="stat-value" id="rvMontoTotal">$0</span>
				</div>
			</div>
		</div>
	</div>
	<div class="col-sm-6 col-xl-3">
		<div class="stat-card">
			<div class="stat-content">
				<span class="stat-label">PROMEDIO/VENTA</span>
				<div class="stat-value-row">
					<span class="stat-value" id="rvPromedio">$0</span>
				</div>
			</div>
		</div>
	</div>
	<div class="col-sm-6 col-xl-3">
		<div class="stat-card">
			<div class="stat-content">
				<span class="stat-label">VENTA MAYOR</span>
				<div class="stat-value-row">
					<span class="stat-value" id="rvVentaMayor">$0</span>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Gráficas -->
<div class="row g-4 mb-4">
	<div class="col-xl-8">
		<div class="card border-0 shadow-sm">
			<div class="card-header bg-white border-bottom">
				<h6 class="mb-0 fw-bold">Ventas por Período</h6>
			</div>
			<div class="card-body">
				<div style="height: 340px; position: relative;">
					<canvas id="chartVentasPeriodoDetalle"></canvas>
				</div>
			</div>
		</div>
	</div>
	<div class="col-xl-4">
		<div class="card border-0 shadow-sm h-100">
			<div class="card-header bg-white border-bottom">
				<h6 class="mb-0 fw-bold">Distribución por Método de Pago</h6>
			</div>
			<div class="card-body d-flex align-items-center justify-content-center">
				<div style="height: 280px; width: 100%; position: relative;">
					<canvas id="chartMetodosPagoDetalle"></canvas>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="row g-4 mb-4">
	<!-- Ventas por vendedor -->
	<div class="col-xl-6">
		<div class="card border-0 shadow-sm">
			<div class="card-header bg-white border-bottom">
				<h6 class="mb-0 fw-bold">Ventas por Vendedor</h6>
			</div>
			<div class="card-body">
				<div style="height: 300px; position: relative;">
					<canvas id="chartVendedoresDetalle"></canvas>
				</div>
			</div>
		</div>
	</div>
	<!-- Facturado vs no facturado -->
	<div class="col-xl-6">
		<div class="card border-0 shadow-sm h-100">
			<div class="card-header bg-white border-bottom">
				<h6 class="mb-0 fw-bold">Facturado vs No Facturado</h6>
			</div>
			<div class="card-body d-flex align-items-center justify-content-center">
				<div style="height: 300px; width: 100%; position: relative;">
					<canvas id="chartFacturadoDetalle"></canvas>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Tabla detalle -->
<div class="card border-0 shadow-sm mb-4">
	<div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
		<h6 class="mb-0 fw-bold">Detalle de Ventas por Período</h6>
	</div>
	<div class="card-body">
		<div class="table-responsive">
			<table class="table table-hover table-sm" id="tablaVentasPeriodo">
				<thead>
					<tr>
						<th>Período</th>
						<th class="text-center">N° Ventas</th>
						<th class="text-end">Monto Total</th>
						<th class="text-end">Promedio</th>
					</tr>
				</thead>
				<tbody id="tbodyVentasPeriodo"></tbody>
			</table>
		</div>
	</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script src="<?php echo IP_SERVER . 'assets/js/reportes.js'; ?>"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
	document.getElementById('ventaFechaDesde').value = '<?php echo date("Y-01-01"); ?>';
	document.getElementById('ventaFechaHasta').value = '<?php echo date("Y-m-d"); ?>';
	cargarReporteVentas();
});
</script>
