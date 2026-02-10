<?php if (!isset($this->session->datosusuario) || !$this->session->datosusuario): ?>
	<!-- Sin sesión: Mensaje de bienvenida -->
	<div class="row">
		<div class="col-12">
			<div class="card shadow-sm">
				<div class="card-body text-center py-5">
					<i class="bi bi-house-door display-1" style="color: var(--color_principal-500);"></i>
					<h1 class="mt-4" style="color: var(--color_principal-600);">Home</h1>
					<p class="text-muted mb-4">Bienvenido al Sistema de Inventarios Saho</p>
					<div class="mt-4">
						<p class="text-muted">Inicia sesión para acceder a todas las funcionalidades</p>
						<a href="<?php echo IP_SERVER . 'login'; ?>" class="btn btn-lg btn-color_principal">
							<i class="bi bi-box-arrow-in-right me-2"></i>Iniciar Sesión
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php else: ?>
	<!-- Con sesión: Dashboard con resumen -->
	<div class="row mb-4">
		<div class="col-12">
			<div class="d-flex align-items-center mb-3">
				<div>
					<h2 class="mb-1 page-title">Resumen</h2>
					<p class="text-muted mb-0">Bienvenido, <strong><?php echo $this->session->datosusuario->nombre_completo; ?></strong></p>
				</div>
			</div>
		</div>
	</div>

	<!-- Tarjetas de resumen -->
	<div class="row g-4">
		<!-- Productos -->
		<div class="col-12 col-md-6 col-xl-4">
			<div class="card shadow-sm h-100 border-0" style="transition: transform 0.3s ease, box-shadow 0.3s ease;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 25px rgba(249, 115, 22, 0.15)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow=''">
				<div class="card-body">
					<div class="d-flex align-items-center justify-content-between mb-3">
						<div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; background: var(--color_principal-50);">
							<i class="bi bi-box-seam fs-3" style="color: var(--color_principal-500);"></i>
						</div>
						<a href="<?php echo IP_SERVER . 'productos'; ?>" class="btn btn-sm btn-outline-color_principal">
							<i class="bi bi-arrow-right"></i>
						</a>
					</div>
					<h6 class="text-muted text-uppercase mb-2" style="font-size: 0.75rem; font-weight: 600; letter-spacing: 0.5px;">Productos</h6>
					<h2 class="mb-0" style="color: var(--color_principal-600); font-weight: 700;">
						<?php echo number_format($resumen['total_productos'] ?? 0); ?>
					</h2>
					<p class="text-muted mb-0 mt-2" style="font-size: 0.85rem;">Total registrados</p>
				</div>
			</div>
		</div>

		<!-- Clientes -->
		<div class="col-12 col-md-6 col-xl-4">
			<div class="card shadow-sm h-100 border-0" style="transition: transform 0.3s ease, box-shadow 0.3s ease;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 25px rgba(249, 115, 22, 0.15)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow=''">
				<div class="card-body">
					<div class="d-flex align-items-center justify-content-between mb-3">
						<div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; background: var(--color_principal-50);">
							<i class="bi bi-people fs-3" style="color: var(--color_principal-500);"></i>
						</div>
						<a href="<?php echo IP_SERVER . 'clientes'; ?>" class="btn btn-sm btn-outline-color_principal">
							<i class="bi bi-arrow-right"></i>
						</a>
					</div>
					<h6 class="text-muted text-uppercase mb-2" style="font-size: 0.75rem; font-weight: 600; letter-spacing: 0.5px;">Clientes</h6>
					<h2 class="mb-0" style="color: var(--color_principal-600); font-weight: 700;">
						<?php echo number_format($resumen['total_clientes'] ?? 0); ?>
					</h2>
					<p class="text-muted mb-0 mt-2" style="font-size: 0.85rem;">Total registrados</p>
				</div>
			</div>
		</div>

		<!-- Ventas -->
		<div class="col-12 col-md-6 col-xl-4">
			<div class="card shadow-sm h-100 border-0" style="transition: transform 0.3s ease, box-shadow 0.3s ease;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 25px rgba(249, 115, 22, 0.15)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow=''">
				<div class="card-body">
					<div class="d-flex align-items-center justify-content-between mb-3">
						<div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; background: var(--color_principal-50);">
							<i class="bi bi-cart-check fs-3" style="color: var(--color_principal-500);"></i>
						</div>
						<a href="<?php echo IP_SERVER . 'ventas'; ?>" class="btn btn-sm btn-outline-color_principal">
							<i class="bi bi-arrow-right"></i>
						</a>
					</div>
					<h6 class="text-muted text-uppercase mb-2" style="font-size: 0.75rem; font-weight: 600; letter-spacing: 0.5px;">Ventas</h6>
					<h2 class="mb-0" style="color: var(--color_principal-600); font-weight: 700;">
						<?php echo number_format($resumen['total_ventas'] ?? 0); ?>
					</h2>
					<p class="text-muted mb-0 mt-2" style="font-size: 0.85rem;">Total realizadas</p>
				</div>
			</div>
		</div>
	</div>
<?php endif; ?>