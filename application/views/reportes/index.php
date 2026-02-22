<!-- Page Header -->
<div class="page-header mb-4">
	<div class="row align-items-center">
		<div class="col-lg-6">
			<h1 class="page-title">Reportes y Estadísticas</h1>
			<p class="page-subtitle">Dashboard general con indicadores clave del negocio.</p>
		</div>
		<div class="col-lg-6 text-lg-end mt-3 mt-lg-0">
			<div class="d-inline-flex align-items-center gap-2 flex-wrap">
				<input type="date" class="form-control form-control-sm" id="kpiFechaDesde" style="width:150px">
				<span class="text-muted">a</span>
				<input type="date" class="form-control form-control-sm" id="kpiFechaHasta" style="width:150px">
				<button class="btn btn-sm btn-color_principal" onclick="cargarDashboard()">
					<i class="bi bi-funnel me-1"></i>Filtrar
				</button>
			</div>
		</div>
	</div>
</div>

<!-- KPI Cards -->
<div class="row g-4 mb-4">
	<div class="col-sm-6 col-xl-3">
		<div class="stat-card">
			<div class="stat-content">
				<span class="stat-label">VENTAS DEL PERÍODO</span>
				<div class="stat-value-row">
					<span class="stat-value" id="kpiTotalVentas">0</span>
					<span class="stat-badge stat-badge-success"><i class="bi bi-cart-check"></i></span>
				</div>
			</div>
		</div>
	</div>
	<div class="col-sm-6 col-xl-3">
		<div class="stat-card">
			<div class="stat-content">
				<span class="stat-label">INGRESOS TOTALES</span>
				<div class="stat-value-row">
					<span class="stat-value" id="kpiMontoTotal">$0</span>
				</div>
			</div>
		</div>
	</div>
	<div class="col-sm-6 col-xl-3">
		<div class="stat-card stat-card-warning">
			<div class="stat-content">
				<span class="stat-label stat-label-warning">PENDIENTE DE COBRO</span>
				<div class="stat-value-row">
					<span class="stat-value stat-value-warning" id="kpiPendienteCobro">$0</span>
				</div>
			</div>
		</div>
	</div>
	<div class="col-sm-6 col-xl-3">
		<div class="stat-card">
			<div class="stat-content">
				<span class="stat-label">CLIENTES ACTIVOS</span>
				<div class="stat-value-row">
					<span class="stat-value" id="kpiClientesActivos">0</span>
					<span class="stat-badge stat-badge-success"><i class="bi bi-people"></i></span>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- KPI Cards Row 2 -->
<div class="row g-4 mb-4">
	<div class="col-sm-6 col-xl-3">
		<div class="stat-card">
			<div class="stat-content">
				<span class="stat-label">PROMEDIO POR VENTA</span>
				<div class="stat-value-row">
					<span class="stat-value" id="kpiPromedioVenta">$0</span>
				</div>
			</div>
		</div>
	</div>
	<div class="col-sm-6 col-xl-3">
		<div class="stat-card">
			<div class="stat-content">
				<span class="stat-label">FACTURAS EMITIDAS</span>
				<div class="stat-value-row">
					<span class="stat-value" id="kpiFacturas">0</span>
					<span class="stat-badge stat-badge-success"><i class="bi bi-receipt"></i></span>
				</div>
			</div>
		</div>
	</div>
	<div class="col-sm-6 col-xl-3">
		<div class="stat-card stat-card-warning">
			<div class="stat-content">
				<span class="stat-label stat-label-warning">STOCK BAJO</span>
				<div class="stat-value-row">
					<span class="stat-value stat-value-warning" id="kpiStockBajo">0</span>
					<span class="stat-badge stat-badge-warning">Alerta</span>
				</div>
			</div>
		</div>
	</div>
	<div class="col-sm-6 col-xl-3">
		<div class="stat-card">
			<div class="stat-content">
				<span class="stat-label">VALOR INVENTARIO</span>
				<div class="stat-value-row">
					<span class="stat-value" id="kpiValorInventario">$0</span>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Gráficas principales -->
<div class="row g-4 mb-4">
	<!-- Ventas por período -->
	<div class="col-xl-8">
		<div class="card border-0 shadow-sm">
			<div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
				<h6 class="mb-0 fw-bold">Tendencia de Ventas</h6>
				<select class="form-select form-select-sm" id="chartAgrupacion" style="width:120px" onchange="cargarChartVentas()">
					<option value="dia">Por Día</option>
					<option value="semana">Por Semana</option>
					<option value="mes" selected>Por Mes</option>
					<option value="anio">Por Año</option>
				</select>
			</div>
			<div class="card-body">
				<div style="height: 320px; position: relative;">
					<canvas id="chartVentasPeriodo"></canvas>
				</div>
			</div>
		</div>
	</div>
	<!-- Métodos de pago -->
	<div class="col-xl-4">
		<div class="card border-0 shadow-sm h-100">
			<div class="card-header bg-white border-bottom">
				<h6 class="mb-0 fw-bold">Métodos de Pago</h6>
			</div>
			<div class="card-body d-flex align-items-center justify-content-center">
				<div style="height: 280px; width: 100%; position: relative;">
					<canvas id="chartMetodosPago"></canvas>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Comparativo y Top -->
<div class="row g-4 mb-4">
	<!-- Comparativo -->
	<div class="col-xl-4">
		<div class="card border-0 shadow-sm h-100">
			<div class="card-header bg-white border-bottom">
				<h6 class="mb-0 fw-bold">Comparativo vs Período Anterior</h6>
			</div>
			<div class="card-body">
				<div class="comparativo-item mb-4">
					<div class="d-flex justify-content-between align-items-center mb-2">
						<span class="text-muted">Ventas</span>
						<span class="fw-bold" id="compVentasActual">0</span>
					</div>
					<div class="d-flex justify-content-between align-items-center mb-1">
						<small class="text-muted">Período anterior</small>
						<small class="text-muted" id="compVentasAnterior">0</small>
					</div>
					<div class="d-flex align-items-center gap-2">
						<div class="progress flex-grow-1" style="height: 6px;">
							<div class="progress-bar" id="compVentasBar" role="progressbar" style="width: 0%; background: var(--color_principal-500);"></div>
						</div>
						<span class="badge rounded-pill" id="compVentasBadge">0%</span>
					</div>
				</div>
				<hr>
				<div class="comparativo-item">
					<div class="d-flex justify-content-between align-items-center mb-2">
						<span class="text-muted">Ingresos</span>
						<span class="fw-bold" id="compMontoActual">$0</span>
					</div>
					<div class="d-flex justify-content-between align-items-center mb-1">
						<small class="text-muted">Período anterior</small>
						<small class="text-muted" id="compMontoAnterior">$0</small>
					</div>
					<div class="d-flex align-items-center gap-2">
						<div class="progress flex-grow-1" style="height: 6px;">
							<div class="progress-bar" id="compMontoBar" role="progressbar" style="width: 0%; background: var(--color_principal-500);"></div>
						</div>
						<span class="badge rounded-pill" id="compMontoBadge">0%</span>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- Facturado vs No facturado -->
	<div class="col-xl-4">
		<div class="card border-0 shadow-sm h-100">
			<div class="card-header bg-white border-bottom">
				<h6 class="mb-0 fw-bold">Facturado vs No Facturado</h6>
			</div>
			<div class="card-body d-flex align-items-center justify-content-center">
				<div style="height: 240px; width: 100%; position: relative;">
					<canvas id="chartFacturado"></canvas>
				</div>
			</div>
		</div>
	</div>
	<!-- Top vendedores -->
	<div class="col-xl-4">
		<div class="card border-0 shadow-sm h-100">
			<div class="card-header bg-white border-bottom">
				<h6 class="mb-0 fw-bold">Vendedores del Período</h6>
			</div>
			<div class="card-body p-0">
				<div class="list-group list-group-flush" id="listaVendedores">
					<div class="text-center py-4 text-muted">Cargando...</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Accesos rápidos a reportes detallados -->
<div class="row g-4 mb-4">
	<div class="col-12">
		<h5 class="fw-bold mb-3">Reportes Detallados</h5>
	</div>
	<div class="col-sm-6 col-xl-3">
		<a href="<?php echo IP_SERVER . 'reportes/ventas'; ?>" class="text-decoration-none">
			<div class="card border-0 shadow-sm reporte-card">
				<div class="card-body text-center py-4">
					<div class="reporte-icon mb-3"><i class="bi bi-cart-check-fill"></i></div>
					<h6 class="fw-bold mb-1">Ventas</h6>
					<p class="text-muted mb-0 small">Por período, vendedor, método de pago</p>
				</div>
			</div>
		</a>
	</div>
	<div class="col-sm-6 col-xl-3">
		<a href="<?php echo IP_SERVER . 'reportes/productos'; ?>" class="text-decoration-none">
			<div class="card border-0 shadow-sm reporte-card">
				<div class="card-body text-center py-4">
					<div class="reporte-icon mb-3"><i class="bi bi-box-seam-fill"></i></div>
					<h6 class="fw-bold mb-1">Productos</h6>
					<p class="text-muted mb-0 small">Más vendidos, stock bajo, por vencer</p>
				</div>
			</div>
		</a>
	</div>
	<div class="col-sm-6 col-xl-3">
		<a href="<?php echo IP_SERVER . 'reportes/clientes'; ?>" class="text-decoration-none">
			<div class="card border-0 shadow-sm reporte-card">
				<div class="card-body text-center py-4">
					<div class="reporte-icon mb-3"><i class="bi bi-people-fill"></i></div>
					<h6 class="fw-bold mb-1">Clientes</h6>
					<p class="text-muted mb-0 small">Top clientes, inactivos, historial</p>
				</div>
			</div>
		</a>
	</div>
	<div class="col-sm-6 col-xl-3">
		<a href="<?php echo IP_SERVER . 'reportes/financiero'; ?>" class="text-decoration-none">
			<div class="card border-0 shadow-sm reporte-card">
				<div class="card-body text-center py-4">
					<div class="reporte-icon mb-3"><i class="bi bi-cash-stack"></i></div>
					<h6 class="fw-bold mb-1">Financiero</h6>
					<p class="text-muted mb-0 small">Ingresos, impuestos, cuentas por cobrar</p>
				</div>
			</div>
		</a>
	</div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script src="<?php echo IP_SERVER . 'assets/js/reportes.js'; ?>"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
	// Fechas por defecto: primer día del mes actual hasta hoy
	document.getElementById('kpiFechaDesde').value = '<?php echo date("Y-m-01"); ?>';
	document.getElementById('kpiFechaHasta').value = '<?php echo date("Y-m-d"); ?>';
	cargarDashboard();
});
</script>
