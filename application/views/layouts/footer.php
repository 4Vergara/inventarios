
			</div>
	</main>

	<!-- Footer -->
	<footer class="footer-custom bg-white py-4 mt-auto">
		<div class="container-fluid px-4">
			<div class="row align-items-center">
				<!-- Logo y nombre -->
				<div class="col-md-4 text-center text-md-start mb-3 mb-md-0">
					<div class="d-flex align-items-center justify-content-center justify-content-md-start gap-2">
						<img src="<?php echo IP_SERVER . 'assets/imagen/icon_solo.png'; ?>" alt="Logo" style="height: 30px; width: auto;">
						<span class="fw-semibold" style="color: var(--color_principal-600);">Saho</span>
					</div>
				</div>
				
				<!-- Copyright -->
				<div class="col-md-4 text-center mb-3 mb-md-0">
					<span class="text-muted small">
						&copy; <?php echo date('Y'); ?> Saho - Sistema de Inventarios
					</span>
					<span id="versionapp" class="text-muted small"></span>
				</div>
				
				<!-- Enlaces rápidos -->
				<div class="col-md-4 text-center text-md-end">
					<div class="d-flex align-items-center justify-content-center justify-content-md-end gap-3">
						<?php if (isset($this->session->datosusuario) && $this->session->datosusuario): ?>
							<span class="text-muted small">
								<i class="bi bi-person-check text-success me-1"></i>
								Conectado como: <strong><?php echo isset($this->session->datosusuario->nombre_completo) ? $this->session->datosusuario->nombre_completo : 'Usuario'; ?></strong>
							</span>
						<?php else: ?>
							<a href="<?php echo IP_SERVER . 'login'; ?>" class="text-decoration-none small" style="color: var(--color_principal-500);">
								<i class="bi bi-box-arrow-in-right me-1"></i> Iniciar Sesión
							</a>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</footer>

	<style>
		.footer-custom {
			border-top: 1px solid #e9ecef;
			box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.05);
		}
	</style>
</body>
</html>