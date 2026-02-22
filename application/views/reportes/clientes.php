<!-- Page Header -->
<div class="page-header mb-4">
	<div class="row align-items-center">
		<div class="col-lg-4">
			<nav aria-label="breadcrumb">
				<ol class="breadcrumb mb-1">
					<li class="breadcrumb-item"><a href="<?php echo IP_SERVER . 'reportes'; ?>">Reportes</a></li>
					<li class="breadcrumb-item active">Clientes</li>
				</ol>
			</nav>
			<h1 class="page-title">Reporte de Clientes</h1>
			<p class="page-subtitle">Análisis de clientes: top compradores, facturación, inactividad e historial.</p>
		</div>
		<div class="col-lg-8 text-lg-end mt-3 mt-lg-0">
			<div class="d-inline-flex align-items-center gap-2 flex-wrap">
				<input type="number" class="form-control form-control-sm" id="cliLimite" value="20" min="5" max="100" style="width:80px" placeholder="Límite">
				<input type="number" class="form-control form-control-sm" id="cliDiasInactivo" value="90" min="10" max="365" style="width:90px" placeholder="Días inact.">
				<button class="btn btn-sm btn-color_principal" onclick="cargarReporteClientes()">
					<i class="bi bi-funnel me-1"></i>Filtrar
				</button>
				<button class="btn btn-sm btn-outline-color_principal" onclick="window.open(IP_SERVER+'reportes/pdfClientes', '_blank')">
					<i class="bi bi-file-pdf me-1"></i>PDF
				</button>
			</div>
		</div>
	</div>
</div>

<!-- Tabs -->
<ul class="nav nav-tabs mb-4" role="tablist">
	<li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tabTopCompras"><i class="bi bi-trophy me-1"></i>Más Compras</a></li>
	<li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tabTopFacturacion"><i class="bi bi-cash-coin me-1"></i>Mayor Facturación</a></li>
	<li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tabInactivos"><i class="bi bi-person-slash me-1"></i>Inactivos</a></li>
	<li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tabHistorial"><i class="bi bi-clock-history me-1"></i>Historial</a></li>
</ul>

<div class="tab-content">
	<!-- Top compras -->
	<div class="tab-pane fade show active" id="tabTopCompras">
		<div class="row g-4 mb-4">
			<div class="col-xl-7">
				<div class="card border-0 shadow-sm">
					<div class="card-header bg-white border-bottom">
						<h6 class="mb-0 fw-bold">Clientes con Más Compras</h6>
					</div>
					<div class="card-body">
						<div style="height: 360px; position: relative;">
							<canvas id="chartTopCompras"></canvas>
						</div>
					</div>
				</div>
			</div>
			<div class="col-xl-5">
				<div class="card border-0 shadow-sm h-100">
					<div class="card-body">
						<div class="table-responsive">
							<table class="table table-hover table-sm">
								<thead><tr><th>#</th><th>Cliente</th><th class="text-center">Compras</th><th class="text-end">Total</th></tr></thead>
								<tbody id="tbodyTopCompras"></tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Mayor facturación -->
	<div class="tab-pane fade" id="tabTopFacturacion">
		<div class="row g-4 mb-4">
			<div class="col-xl-7">
				<div class="card border-0 shadow-sm">
					<div class="card-header bg-white border-bottom">
						<h6 class="mb-0 fw-bold">Clientes con Mayor Facturación</h6>
					</div>
					<div class="card-body">
						<div style="height: 360px; position: relative;">
							<canvas id="chartTopFacturacion"></canvas>
						</div>
					</div>
				</div>
			</div>
			<div class="col-xl-5">
				<div class="card border-0 shadow-sm h-100">
					<div class="card-body">
						<div class="table-responsive">
							<table class="table table-hover table-sm">
								<thead><tr><th>#</th><th>Cliente</th><th class="text-end">Total Facturado</th><th class="text-center">Facturas</th></tr></thead>
								<tbody id="tbodyTopFacturacion"></tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Inactivos -->
	<div class="tab-pane fade" id="tabInactivos">
		<div class="card border-0 shadow-sm mb-4">
			<div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
				<h6 class="mb-0 fw-bold">Clientes Inactivos</h6>
				<span class="text-muted small">Sin compras hace más de <span id="spanDiasInactivo">90</span> días</span>
			</div>
			<div class="card-body">
				<div class="table-responsive">
					<table class="table table-hover table-sm">
						<thead>
							<tr>
								<th>Cliente</th>
								<th>Documento</th>
								<th>Correo</th>
								<th class="text-center">Última Compra</th>
								<th class="text-center">Días Sin Comprar</th>
							</tr>
						</thead>
						<tbody id="tbodyInactivos"></tbody>
					</table>
				</div>
			</div>
		</div>
	</div>

	<!-- Historial -->
	<div class="tab-pane fade" id="tabHistorial">
		<div class="card border-0 shadow-sm mb-4">
			<div class="card-body">
				<div class="row g-3 align-items-end mb-4">
					<div class="col-md-4">
						<label class="form-label fw-bold">Buscar Cliente</label>
						<select class="form-select" id="selectClienteHistorial">
							<option value="">Seleccione un cliente...</option>
							<?php if (!empty($clientes)): ?>
								<?php foreach ($clientes as $c): ?>
									<option value="<?php echo $c->id; ?>"><?php echo $c->nombre_completo; ?></option>
								<?php endforeach; ?>
							<?php endif; ?>
						</select>
					</div>
					<div class="col-md-2">
						<button class="btn btn-color_principal w-100" onclick="cargarHistorialCliente()">
							<i class="bi bi-search me-1"></i>Buscar
						</button>
					</div>
				</div>
				<!-- Datos del cliente -->
				<div id="panelHistorial" style="display:none;">
					<div class="row g-4 mb-4">
						<div class="col-sm-6 col-xl-3">
							<div class="stat-card">
								<div class="stat-content">
									<span class="stat-label">TOTAL COMPRAS</span>
									<div class="stat-value-row"><span class="stat-value" id="hcTotalCompras">0</span></div>
								</div>
							</div>
						</div>
						<div class="col-sm-6 col-xl-3">
							<div class="stat-card">
								<div class="stat-content">
									<span class="stat-label">MONTO TOTAL</span>
									<div class="stat-value-row"><span class="stat-value" id="hcMontoTotal">$0</span></div>
								</div>
							</div>
						</div>
						<div class="col-sm-6 col-xl-3">
							<div class="stat-card">
								<div class="stat-content">
									<span class="stat-label">PRIMERA COMPRA</span>
									<div class="stat-value-row"><span class="stat-value" id="hcPrimeraCompra">-</span></div>
								</div>
							</div>
						</div>
						<div class="col-sm-6 col-xl-3">
							<div class="stat-card">
								<div class="stat-content">
									<span class="stat-label">ÚLTIMA COMPRA</span>
									<div class="stat-value-row"><span class="stat-value" id="hcUltimaCompra">-</span></div>
								</div>
							</div>
						</div>
					</div>
					<div class="table-responsive">
						<table class="table table-hover table-sm">
							<thead>
								<tr>
									<th>Fecha</th>
									<th>N° Venta</th>
									<th>Productos</th>
									<th class="text-end">Subtotal</th>
									<th class="text-end">Impuesto</th>
									<th class="text-end">Total</th>
									<th>Estado</th>
								</tr>
							</thead>
							<tbody id="tbodyHistorial"></tbody>
						</table>
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
	cargarReporteClientes();
});
</script>
