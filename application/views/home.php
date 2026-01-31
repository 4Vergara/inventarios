<div class="row">
	<div class="col-12">
		<div class="card shadow-sm">
			<div class="card-body text-center py-5">
				<i class="bi bi-house-door display-1" style="color: var(--color_principal-500);"></i>
				<h1 class="mt-4" style="color: var(--color_principal-600);">Home</h1>
				<p class="text-muted mb-4">Bienvenido al Sistema de Inventarios Saho</p>
				
				<?php if (!isset($this->session->datosusuario) || !$this->session->datosusuario): ?>
					<div class="mt-4">
						<p class="text-muted">Inicia sesión para acceder a todas las funcionalidades</p>
						<a href="<?php echo IP_SERVER . 'login'; ?>" class="btn btn-lg" style="background: var(--color_principal-500); color: white;">
							<i class="bi bi-box-arrow-in-right me-2"></i>Iniciar Sesión
						</a>
					</div>
				<?php else: ?>
					<div class="mt-4">
						<p class="text-muted">Hola, <strong><?php echo $this->session->datosusuario->nombre_completo; ?></strong>. ¿Qué deseas hacer hoy?</p>
						<a href="<?php echo IP_SERVER . 'productos'; ?>" class="btn btn-lg" style="background: var(--color_principal-500); color: white;">
							<i class="bi bi-box-seam me-2"></i>Ver Productos
						</a>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>