<!-- Page Header -->
<div class="page-header mb-4">
	<div class="row align-items-center">
		<div class="col-lg-4">
			<nav aria-label="breadcrumb">
				<ol class="breadcrumb mb-1">
					<li class="breadcrumb-item"><a href="<?php echo IP_SERVER . 'reportes'; ?>">Reportes</a></li>
					<li class="breadcrumb-item active">Productos</li>
				</ol>
			</nav>
			<h1 class="page-title">Reporte de Productos</h1>
			<p class="page-subtitle">Más vendidos, menos vendidos, stock bajo, por vencer y rotación.</p>
		</div>
		<div class="col-lg-8 text-lg-end mt-3 mt-lg-0">
			<div class="d-inline-flex align-items-center gap-2 flex-wrap">
				<input type="number" class="form-control form-control-sm" id="prodLimite" value="20" min="5" max="100" style="width:80px" placeholder="Límite">
				<input type="number" class="form-control form-control-sm" id="prodDiasVencer" value="30" min="1" max="365" style="width:80px" placeholder="Días">
				<button class="btn btn-sm btn-color_principal" onclick="cargarReporteProductos()">
					<i class="bi bi-funnel me-1"></i>Filtrar
				</button>
				<button class="btn btn-sm btn-outline-color_principal" onclick="window.open(IP_SERVER+'reportes/pdfProductos', '_blank')">
					<i class="bi bi-file-pdf me-1"></i>PDF
				</button>
			</div>
		</div>
	</div>
</div>

<!-- Tabs de navegación -->
<ul class="nav nav-tabs mb-4" id="prodTabs" role="tablist">
	<li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tabMasVendidos"><i class="bi bi-trophy me-1"></i>Más Vendidos</a></li>
	<li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tabMenosVendidos"><i class="bi bi-graph-down me-1"></i>Menos Vendidos</a></li>
	<li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tabStockBajo"><i class="bi bi-exclamation-triangle me-1"></i>Stock Bajo</a></li>
	<li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tabPorVencer"><i class="bi bi-clock-history me-1"></i>Por Vencer</a></li>
	<li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tabRotacion"><i class="bi bi-arrow-repeat me-1"></i>Rotación</a></li>
</ul>

<div class="tab-content">
	<!-- Más vendidos -->
	<div class="tab-pane fade show active" id="tabMasVendidos">
		<div class="row g-4 mb-4">
			<div class="col-xl-7">
				<div class="card border-0 shadow-sm">
					<div class="card-header bg-white border-bottom">
						<h6 class="mb-0 fw-bold">Top Productos Más Vendidos</h6>
					</div>
					<div class="card-body">
						<div style="height: 360px; position: relative;">
							<canvas id="chartMasVendidos"></canvas>
						</div>
					</div>
				</div>
			</div>
			<div class="col-xl-5">
				<div class="card border-0 shadow-sm h-100">
					<div class="card-body">
						<div class="table-responsive">
							<table class="table table-hover table-sm">
								<thead><tr><th>#</th><th>Producto</th><th class="text-center">Uds.</th><th class="text-end">Ingresos</th></tr></thead>
								<tbody id="tbodyMasVendidos"></tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Menos vendidos -->
	<div class="tab-pane fade" id="tabMenosVendidos">
		<div class="card border-0 shadow-sm mb-4">
			<div class="card-header bg-white border-bottom">
				<h6 class="mb-0 fw-bold">Productos Menos Vendidos</h6>
			</div>
			<div class="card-body">
				<div class="table-responsive">
					<table class="table table-hover table-sm">
						<thead>
							<tr>
								<th>#</th>
								<th>Código</th>
								<th>Producto</th>
								<th class="text-center">Cantidad Vendida</th>
								<th class="text-end">Ingresos</th>
								<th class="text-center">Stock Actual</th>
							</tr>
						</thead>
						<tbody id="tbodyMenosVendidos"></tbody>
					</table>
				</div>
			</div>
		</div>
	</div>

	<!-- Stock bajo -->
	<div class="tab-pane fade" id="tabStockBajo">
		<div class="card border-0 shadow-sm mb-4">
			<div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
				<h6 class="mb-0 fw-bold">Productos con Stock Bajo</h6>
				<span class="badge bg-danger" id="badgeStockBajo">0</span>
			</div>
			<div class="card-body">
				<div class="table-responsive">
					<table class="table table-hover table-sm">
						<thead>
							<tr>
								<th>Código</th>
								<th>Producto</th>
								<th class="text-center">Stock Actual</th>
								<th class="text-center">Stock Mínimo</th>
								<th class="text-center">Déficit</th>
								<th class="text-end">Precio Compra</th>
								<th class="text-end">Costo Reposición</th>
							</tr>
						</thead>
						<tbody id="tbodyStockBajo"></tbody>
					</table>
				</div>
			</div>
		</div>
	</div>

	<!-- Por vencer -->
	<div class="tab-pane fade" id="tabPorVencer">
		<div class="card border-0 shadow-sm mb-4">
			<div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
				<h6 class="mb-0 fw-bold">Productos por Vencer</h6>
				<span class="text-muted small">Próximos <span id="spanDiasVencer">30</span> días</span>
			</div>
			<div class="card-body">
				<div class="table-responsive">
					<table class="table table-hover table-sm">
						<thead>
							<tr>
								<th>Código</th>
								<th>Producto</th>
								<th class="text-center">Stock</th>
								<th class="text-center">Fecha Vencimiento</th>
								<th class="text-center">Días Restantes</th>
								<th>Estado</th>
							</tr>
						</thead>
						<tbody id="tbodyPorVencer"></tbody>
					</table>
				</div>
			</div>
		</div>
	</div>

	<!-- Rotación -->
	<div class="tab-pane fade" id="tabRotacion">
		<div class="row g-4 mb-4">
			<div class="col-xl-7">
				<div class="card border-0 shadow-sm">
					<div class="card-header bg-white border-bottom">
						<h6 class="mb-0 fw-bold">Índice de Rotación de Inventario</h6>
					</div>
					<div class="card-body">
						<div style="height: 360px; position: relative;">
							<canvas id="chartRotacion"></canvas>
						</div>
					</div>
				</div>
			</div>
			<div class="col-xl-5">
				<div class="card border-0 shadow-sm h-100">
					<div class="card-body">
						<div class="table-responsive">
							<table class="table table-hover table-sm">
								<thead><tr><th>Producto</th><th class="text-center">Vendido</th><th class="text-center">Stock</th><th class="text-end">Rotación</th></tr></thead>
								<tbody id="tbodyRotacion"></tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script src="<?php echo IP_SERVER . 'assets/js/reportes.js'; ?>"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
	cargarReporteProductos();
});
</script>
