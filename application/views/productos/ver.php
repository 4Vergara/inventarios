<!-- Header de la sección -->
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
	<div>
		<h2 class="mb-1" style="color: var(--color_principal-700);">
			<i class="bi bi-eye me-2"></i>Detalle del Producto
		</h2>
		<nav aria-label="breadcrumb">
			<ol class="breadcrumb mb-0">
				<li class="breadcrumb-item"><a href="<?php echo IP_SERVER; ?>" class="text-decoration-none" style="color: var(--color_principal-500);">Inicio</a></li>
				<li class="breadcrumb-item"><a href="<?php echo IP_SERVER . 'productos'; ?>" class="text-decoration-none" style="color: var(--color_principal-500);">Productos</a></li>
				<li class="breadcrumb-item active">Detalle</li>
			</ol>
		</nav>
	</div>
	<div class="d-flex gap-2">
		<a href="<?php echo IP_SERVER . 'productos/editar/' . $producto->id; ?>" class="btn btn-color_principal">
			<i class="bi bi-pencil me-2"></i>Editar
		</a>
		<a href="<?php echo IP_SERVER . 'productos'; ?>" class="btn btn-outline-secondary">
			<i class="bi bi-arrow-left me-2"></i>Volver
		</a>
	</div>
</div>

<div class="row">
	<!-- Columna izquierda: Imagen y datos principales -->
	<div class="col-lg-4 mb-4">
		<div class="card shadow-sm border-0">
			<div class="card-body text-center">
				<?php if ($producto->imagen_principal_url): ?>
					<img src="<?php echo $producto->imagen_principal_url; ?>" class="img-fluid rounded mb-3" style="max-height: 300px; object-fit: contain;">
				<?php else: ?>
					<div class="bg-light rounded d-flex align-items-center justify-content-center mb-3" style="height: 200px;">
						<i class="bi bi-image text-muted" style="font-size: 4rem;"></i>
					</div>
				<?php endif; ?>
				
				<h4 class="mb-2"><?php echo $producto->nombre; ?></h4>
				
				<?php if ($producto->marca): ?>
					<p class="text-muted mb-2"><i class="bi bi-tag me-1"></i><?php echo $producto->marca; ?></p>
				<?php endif; ?>
				
				<!-- Estado -->
				<?php 
				$estadoBadge = [
					'activo' => 'bg-success',
					'inactivo' => 'bg-secondary',
					'descatalogado' => 'bg-danger'
				];
				?>
				<span class="badge <?php echo $estadoBadge[$producto->estado] ?? 'bg-secondary'; ?> fs-6">
					<?php echo ucfirst($producto->estado); ?>
				</span>
				
				<!-- Precios destacados -->
				<div class="mt-4 p-3 bg-light rounded">
					<div class="row">
						<div class="col-6">
							<small class="text-muted">Precio Venta</small>
							<h3 class="mb-0" style="color: var(--color_principal-600);">
								$<?php echo number_format($producto->precio_venta, 2); ?>
							</h3>
						</div>
						<div class="col-6 border-start">
							<small class="text-muted">Stock</small>
							<?php 
							$stockClass = 'text-success';
							if ($producto->stock_actual <= 0) {
								$stockClass = 'text-danger';
							} elseif ($producto->stock_actual <= $producto->stock_minimo) {
								$stockClass = 'text-warning';
							}
							?>
							<h3 class="mb-0 <?php echo $stockClass; ?>">
								<?php echo $producto->stock_actual; ?>
							</h3>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<!-- Columna derecha: Detalles -->
	<div class="col-lg-8">
		<!-- Identificación -->
		<div class="card shadow-sm border-0 mb-4">
			<div class="card-header bg-white">
				<h5 class="mb-0" style="color: var(--color_principal-600);">
					<i class="bi bi-tag me-2"></i>Identificación
				</h5>
			</div>
			<div class="card-body">
				<div class="row g-3">
					<div class="col-md-4">
						<label class="text-muted small">SKU</label>
						<p class="mb-0 fw-semibold"><?php echo $producto->sku ?: '-'; ?></p>
					</div>
					<div class="col-md-4">
						<label class="text-muted small">Código de Barras</label>
						<p class="mb-0 fw-semibold"><?php echo $producto->codigo_barras ?: '-'; ?></p>
					</div>
					<div class="col-md-4">
						<label class="text-muted small">Categoría</label>
						<?php 
						$categoriaNombre = '-';
						if (isset($categorias) && $producto->id_categoria) {
							foreach ($categorias as $cat) {
								if ($cat->id == $producto->id_categoria) {
									$categoriaNombre = $cat->nombre;
									break;
								}
							}
						}
						?>
						<p class="mb-0 fw-semibold"><?php echo $categoriaNombre; ?></p>
					</div>
					<?php if ($producto->descripcion_corta): ?>
					<div class="col-12">
						<label class="text-muted small">Descripción Corta</label>
						<p class="mb-0"><?php echo $producto->descripcion_corta; ?></p>
					</div>
					<?php endif; ?>
					<?php if ($producto->descripcion_detallada): ?>
					<div class="col-12">
						<label class="text-muted small">Descripción Detallada</label>
						<p class="mb-0"><?php echo nl2br($producto->descripcion_detallada); ?></p>
					</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
		
		<!-- Económicos -->
		<div class="card shadow-sm border-0 mb-4">
			<div class="card-header bg-white">
				<h5 class="mb-0" style="color: var(--color_principal-600);">
					<i class="bi bi-currency-dollar me-2"></i>Información Económica
				</h5>
			</div>
			<div class="card-body">
				<div class="row g-3">
					<div class="col-md-3">
						<label class="text-muted small">Precio Costo</label>
						<p class="mb-0 fw-semibold">$<?php echo number_format($producto->precio_costo, 2); ?></p>
					</div>
					<div class="col-md-3">
						<label class="text-muted small">Precio Venta</label>
						<p class="mb-0 fw-semibold">$<?php echo number_format($producto->precio_venta, 2); ?></p>
					</div>
					<div class="col-md-3">
						<label class="text-muted small">Impuesto</label>
						<p class="mb-0 fw-semibold"><?php echo $producto->porcentaje_impuesto; ?>%</p>
					</div>
					<div class="col-md-3">
						<label class="text-muted small">Margen</label>
						<?php 
						$margen = $producto->precio_venta - $producto->precio_costo;
						$porcentajeMargen = $producto->precio_costo > 0 ? ($margen / $producto->precio_costo) * 100 : 0;
						?>
						<p class="mb-0 fw-semibold text-success">
							$<?php echo number_format($margen, 2); ?> (<?php echo number_format($porcentajeMargen, 1); ?>%)
						</p>
					</div>
					<div class="col-md-3">
						<label class="text-muted small">Stock Actual</label>
						<p class="mb-0 fw-semibold"><?php echo $producto->stock_actual; ?> <?php echo $producto->unidad_medida; ?></p>
					</div>
					<div class="col-md-3">
						<label class="text-muted small">Stock Mínimo</label>
						<p class="mb-0 fw-semibold"><?php echo $producto->stock_minimo; ?> <?php echo $producto->unidad_medida; ?></p>
					</div>
					<div class="col-md-3">
						<label class="text-muted small">Unidad de Medida</label>
						<p class="mb-0 fw-semibold"><?php echo ucfirst($producto->unidad_medida); ?></p>
					</div>
				</div>
			</div>
		</div>
		
		<!-- Logística (si tiene datos) -->
		<?php if ($producto->peso_kg || $producto->ancho_cm || $producto->alto_cm || $producto->profundidad_cm): ?>
		<div class="card shadow-sm border-0 mb-4">
			<div class="card-header bg-white">
				<h5 class="mb-0" style="color: var(--color_principal-600);">
					<i class="bi bi-truck me-2"></i>Logística
				</h5>
			</div>
			<div class="card-body">
				<div class="row g-3">
					<?php if ($producto->peso_kg): ?>
					<div class="col-md-3">
						<label class="text-muted small">Peso</label>
						<p class="mb-0 fw-semibold"><?php echo $producto->peso_kg; ?> kg</p>
					</div>
					<?php endif; ?>
					<?php if ($producto->ancho_cm): ?>
					<div class="col-md-3">
						<label class="text-muted small">Ancho</label>
						<p class="mb-0 fw-semibold"><?php echo $producto->ancho_cm; ?> cm</p>
					</div>
					<?php endif; ?>
					<?php if ($producto->alto_cm): ?>
					<div class="col-md-3">
						<label class="text-muted small">Alto</label>
						<p class="mb-0 fw-semibold"><?php echo $producto->alto_cm; ?> cm</p>
					</div>
					<?php endif; ?>
					<?php if ($producto->profundidad_cm): ?>
					<div class="col-md-3">
						<label class="text-muted small">Profundidad</label>
						<p class="mb-0 fw-semibold"><?php echo $producto->profundidad_cm; ?> cm</p>
					</div>
					<?php endif; ?>
					<div class="col-md-3">
						<label class="text-muted small">Envío Gratis</label>
						<p class="mb-0">
							<?php if ($producto->es_envio_gratis): ?>
								<span class="badge bg-success"><i class="bi bi-check"></i> Sí</span>
							<?php else: ?>
								<span class="badge bg-secondary">No</span>
							<?php endif; ?>
						</p>
					</div>
				</div>
			</div>
		</div>
		<?php endif; ?>
		
		<!-- Perecederos (si aplica) -->
		<?php if ($producto->es_perecedero): ?>
		<div class="card shadow-sm border-0 mb-4">
			<div class="card-header bg-white">
				<h5 class="mb-0" style="color: var(--color_principal-600);">
					<i class="bi bi-cup-hot me-2"></i>Información de Producto Perecedero
				</h5>
			</div>
			<div class="card-body">
				<div class="row g-3">
					<?php if ($producto->fecha_elaboracion): ?>
					<div class="col-md-4">
						<label class="text-muted small">Fecha de Elaboración</label>
						<p class="mb-0 fw-semibold"><?php echo date('d/m/Y', strtotime($producto->fecha_elaboracion)); ?></p>
					</div>
					<?php endif; ?>
					<?php if ($producto->fecha_vencimiento): ?>
					<div class="col-md-4">
						<label class="text-muted small">Fecha de Vencimiento</label>
						<?php 
						$fechaVenc = strtotime($producto->fecha_vencimiento);
						$diasRestantes = floor(($fechaVenc - time()) / 86400);
						$classVenc = $diasRestantes <= 7 ? 'text-danger' : ($diasRestantes <= 30 ? 'text-warning' : '');
						?>
						<p class="mb-0 fw-semibold <?php echo $classVenc; ?>">
							<?php echo date('d/m/Y', $fechaVenc); ?>
							<?php if ($diasRestantes <= 30): ?>
								<small>(<?php echo $diasRestantes; ?> días)</small>
							<?php endif; ?>
						</p>
					</div>
					<?php endif; ?>
					<?php if ($producto->temperatura_conservacion): ?>
					<div class="col-md-4">
						<label class="text-muted small">Temperatura de Conservación</label>
						<p class="mb-0 fw-semibold"><?php echo $producto->temperatura_conservacion; ?></p>
					</div>
					<?php endif; ?>
					<?php if ($producto->ingredientes): ?>
					<div class="col-12">
						<label class="text-muted small">Ingredientes</label>
						<p class="mb-0"><?php echo $producto->ingredientes; ?></p>
					</div>
					<?php endif; ?>
					<?php if ($producto->info_nutricional): ?>
					<div class="col-12">
						<label class="text-muted small">Información Nutricional</label>
						<p class="mb-0"><?php echo nl2br($producto->info_nutricional); ?></p>
					</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php endif; ?>
		
		<!-- Ropa/Moda (si tiene datos) -->
		<?php if ($producto->talla || $producto->color || $producto->material_principal || $producto->genero): ?>
		<div class="card shadow-sm border-0 mb-4">
			<div class="card-header bg-white">
				<h5 class="mb-0" style="color: var(--color_principal-600);">
					<i class="bi bi-bag me-2"></i>Información de Moda
				</h5>
			</div>
			<div class="card-body">
				<div class="row g-3">
					<?php if ($producto->talla): ?>
					<div class="col-md-3">
						<label class="text-muted small">Talla</label>
						<p class="mb-0 fw-semibold"><?php echo $producto->talla; ?></p>
					</div>
					<?php endif; ?>
					<?php if ($producto->color): ?>
					<div class="col-md-3">
						<label class="text-muted small">Color</label>
						<p class="mb-0 fw-semibold"><?php echo $producto->color; ?></p>
					</div>
					<?php endif; ?>
					<?php if ($producto->material_principal): ?>
					<div class="col-md-3">
						<label class="text-muted small">Material</label>
						<p class="mb-0 fw-semibold"><?php echo $producto->material_principal; ?></p>
					</div>
					<?php endif; ?>
					<?php if ($producto->genero): ?>
					<div class="col-md-3">
						<label class="text-muted small">Género</label>
						<p class="mb-0 fw-semibold"><?php echo ucfirst($producto->genero); ?></p>
					</div>
					<?php endif; ?>
					<?php if ($producto->estilo_corte): ?>
					<div class="col-md-3">
						<label class="text-muted small">Estilo/Corte</label>
						<p class="mb-0 fw-semibold"><?php echo $producto->estilo_corte; ?></p>
					</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php endif; ?>
		
		<!-- Tecnología (si tiene datos) -->
		<?php if ($producto->modelo_tecnico || $producto->numero_serie || $producto->garantia_meses || $producto->voltaje): ?>
		<div class="card shadow-sm border-0 mb-4">
			<div class="card-header bg-white">
				<h5 class="mb-0" style="color: var(--color_principal-600);">
					<i class="bi bi-cpu me-2"></i>Información Técnica
				</h5>
			</div>
			<div class="card-body">
				<div class="row g-3">
					<?php if ($producto->modelo_tecnico): ?>
					<div class="col-md-4">
						<label class="text-muted small">Modelo</label>
						<p class="mb-0 fw-semibold"><?php echo $producto->modelo_tecnico; ?></p>
					</div>
					<?php endif; ?>
					<?php if ($producto->numero_serie): ?>
					<div class="col-md-4">
						<label class="text-muted small">Número de Serie</label>
						<p class="mb-0 fw-semibold"><?php echo $producto->numero_serie; ?></p>
					</div>
					<?php endif; ?>
					<?php if ($producto->garantia_meses): ?>
					<div class="col-md-4">
						<label class="text-muted small">Garantía</label>
						<p class="mb-0 fw-semibold"><?php echo $producto->garantia_meses; ?> meses</p>
					</div>
					<?php endif; ?>
					<?php if ($producto->consumo_watts): ?>
					<div class="col-md-4">
						<label class="text-muted small">Consumo</label>
						<p class="mb-0 fw-semibold"><?php echo $producto->consumo_watts; ?> W</p>
					</div>
					<?php endif; ?>
					<?php if ($producto->voltaje): ?>
					<div class="col-md-4">
						<label class="text-muted small">Voltaje</label>
						<p class="mb-0 fw-semibold"><?php echo $producto->voltaje; ?></p>
					</div>
					<?php endif; ?>
					<div class="col-md-4">
						<label class="text-muted small">Dispositivo Smart</label>
						<p class="mb-0">
							<?php if ($producto->es_inteligente): ?>
								<span class="badge bg-info"><i class="bi bi-wifi"></i> IoT</span>
							<?php else: ?>
								<span class="badge bg-secondary">No</span>
							<?php endif; ?>
						</p>
					</div>
				</div>
			</div>
		</div>
		<?php endif; ?>
		
		<!-- Especificaciones extras (si existen) -->
		<?php 
		$especificaciones = [];
		if ($producto->especificaciones_extra) {
			$especificaciones = json_decode($producto->especificaciones_extra, true) ?: [];
		}
		if (!empty($especificaciones)): ?>
		<div class="card shadow-sm border-0 mb-4">
			<div class="card-header bg-white">
				<h5 class="mb-0" style="color: var(--color_principal-600);">
					<i class="bi bi-sliders me-2"></i>Especificaciones Adicionales
				</h5>
			</div>
			<div class="card-body">
				<table class="table table-sm table-borderless mb-0">
					<?php foreach ($especificaciones as $key => $value): ?>
					<tr>
						<td class="text-muted" style="width: 40%;"><?php echo htmlspecialchars($key); ?></td>
						<td class="fw-semibold"><?php echo htmlspecialchars($value); ?></td>
					</tr>
					<?php endforeach; ?>
				</table>
			</div>
		</div>
		<?php endif; ?>
		
		<!-- Metadatos del sistema -->
		<div class="card shadow-sm border-0">
			<div class="card-header bg-white">
				<h5 class="mb-0" style="color: var(--color_principal-600);">
					<i class="bi bi-clock-history me-2"></i>Información del Sistema
				</h5>
			</div>
			<div class="card-body">
				<div class="row g-3">
					<div class="col-md-6">
						<label class="text-muted small">Creado por</label>
						<p class="mb-0 fw-semibold"><?php echo $producto->creado_por ?: '-'; ?></p>
						<small class="text-muted"><?php echo $producto->fec_creacion ? date('d/m/Y H:i', strtotime($producto->fec_creacion)) : '-'; ?></small>
					</div>
					<div class="col-md-6">
						<label class="text-muted small">Última actualización</label>
						<p class="mb-0 fw-semibold"><?php echo $producto->actualizado_por ?: '-'; ?></p>
						<small class="text-muted"><?php echo $producto->fec_actualizacion ? date('d/m/Y H:i', strtotime($producto->fec_actualizacion)) : '-'; ?></small>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>