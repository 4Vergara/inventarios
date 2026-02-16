<!-- Catálogo Público de Productos - Vista Bento -->
<style>
	.filtro-select {
		padding: 10px 40px 10px 16px;
		border: 1px solid #e5e7eb;
		border-radius: 25px;
		background: #ffffff;
		font-size: 0.9rem;
		font-weight: 500;
		color: #1f2937;
		cursor: pointer;
		transition: all 0.2s ease;
		min-width: 200px;
		appearance: none;
		background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
		background-position: right 12px center;
		background-repeat: no-repeat;
		background-size: 20px;
	}
	
	.filtro-select:focus {
		outline: none;
		border-color: var(--color_principal-400);
		box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1);
	}
	
	.filtro-select:hover {
		border-color: var(--color_principal-400);
	}
	
	.filtro-select optgroup {
		font-weight: 700;
		color: var(--color_principal-700);
		background-color: #f8f9fa;
	}
	
	.filtro-select optgroup option {
		font-weight: 400;
		color: #333;
		background-color: #fff;
		padding-left: 10px;
	}

	.bento-grid {
		display: grid;
		grid-template-columns: repeat(4, 1fr);
		gap: 1.5rem;
	}
	
	.bento-card {
		background: #ffffff;
		border-radius: 16px;
		overflow: hidden;
		box-shadow: 0 1px 3px rgba(0,0,0,0.08);
		transition: all 0.3s ease;
	}
	
	.bento-card:hover {
		box-shadow: 0 8px 30px rgba(0,0,0,0.12);
		transform: translateY(-4px);
	}
	
	.bento-featured {
		grid-column: span 2;
		grid-row: span 2;
	}
	
	.bento-wide {
		grid-column: span 2;
	}
	
	.bento-card-img {
		width: 100%;
		height: 200px;
		object-fit: cover;
		background: linear-gradient(135deg, var(--color_principal-50), var(--color_principal-100));
	}
	
	.bento-featured .bento-card-img {
		height: 320px;
	}
	
	.bento-card-placeholder {
		width: 100%;
		height: 200px;
		background: linear-gradient(135deg, var(--color_principal-50), var(--color_principal-100));
		display: flex;
		align-items: center;
		justify-content: center;
		color: var(--color_principal-300);
	}
	
	.bento-featured .bento-card-placeholder {
		height: 320px;
	}
	
	.bento-card-body {
		padding: 1.25rem;
	}
	
	.bento-featured .bento-card-body {
		padding: 1.75rem;
	}
	
	.bento-card-category {
		display: inline-block;
		padding: 4px 12px;
		background: var(--color_principal-50);
		color: var(--color_principal-600);
		border-radius: 20px;
		font-size: 0.7rem;
		font-weight: 600;
		text-transform: uppercase;
		letter-spacing: 0.5px;
		margin-bottom: 8px;
	}
	
	.bento-card-title {
		font-weight: 700;
		color: #1f2937;
		margin-bottom: 4px;
	}
	
	.bento-featured .bento-card-title {
		font-size: 1.5rem;
	}
	
	.bento-card-sku {
		font-size: 0.75rem;
		color: #9ca3af;
		margin-bottom: 8px;
	}
	
	.bento-card-price {
		font-size: 1.1rem;
		font-weight: 700;
		color: var(--color_principal-500);
	}
	
	.bento-featured .bento-card-price {
		font-size: 1.5rem;
	}
	
	.bento-card-footer {
		display: flex;
		justify-content: space-between;
		align-items: center;
		margin-top: 12px;
	}
	
	.bento-btn-cart {
		width: 42px;
		height: 42px;
		display: flex;
		align-items: center;
		justify-content: center;
		border-radius: 12px;
		background: var(--color_principal-50);
		color: var(--color_principal-500);
		border: none;
		cursor: pointer;
		transition: all 0.2s ease;
	}
	
	.bento-btn-cart:hover {
		background: var(--color_principal-500);
		color: #ffffff;
	}
	
	.bento-info-card {
		background: linear-gradient(135deg, var(--color_principal-50), var(--color_principal-100));
		border: 2px dashed var(--color_principal-200);
		display: flex;
		flex-direction: column;
		justify-content: center;
		padding: 2rem;
	}
	
	.bento-promo-card {
		background: linear-gradient(135deg, #1f2937, #374151);
		color: #ffffff;
		position: relative;
		overflow: hidden;
		display: flex;
		align-items: center;
		padding: 2rem;
	}
	
	.bento-promo-content {
		position: relative;
		z-index: 2;
		width: 60%;
	}
	
	.bento-promo-bg {
		position: absolute;
		right: 0;
		top: 0;
		bottom: 0;
		width: 50%;
		background: linear-gradient(135deg, var(--color_principal-400), var(--color_principal-600));
		opacity: 0.3;
	}
	
	.bento-stock-badge {
		display: inline-block;
		padding: 4px 10px;
		border-radius: 8px;
		font-size: 0.7rem;
		font-weight: 600;
	}
	
	.bento-stock-available {
		background: #dcfce7;
		color: #16a34a;
	}
	
	.bento-stock-low {
		background: var(--color_principal-100);
		color: var(--color_principal-600);
	}
	
	.bento-stock-out {
		background: #fee2e2;
		color: #dc2626;
	}
	
	/* Responsive */
	@media (max-width: 1199.98px) {
		.bento-grid {
			grid-template-columns: repeat(3, 1fr);
		}
		.bento-featured {
			grid-column: span 2;
		}
	}
	
	@media (max-width: 991.98px) {
		.bento-grid {
			grid-template-columns: repeat(2, 1fr);
		}
	}
	
	@media (max-width: 575.98px) {
		.bento-grid {
			grid-template-columns: 1fr;
		}
		.bento-featured,
		.bento-wide {
			grid-column: span 1;
		}
	}
</style>

<!-- Hero Section -->
<div class="mb-5">
	<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-end gap-4 mb-4">
		<div>
			<h1 class="page-title mb-2">Catálogo de Productos</h1>
			<p class="text-muted mb-0" style="max-width: 500px;">
				Descubre nuestra selección de productos de alta calidad para tu negocio.
			</p>
		</div>
		<div class="d-flex gap-3 align-items-center" id="filtros-categoria">
			<label class="text-muted fw-medium mb-0">Filtrar por:</label>
			<select class="filtro-select" id="select-categoria">
				<option value="todos">Todas las categorías</option>
				<?php if (isset($categorias_catalogo) && !empty($categorias_catalogo)): ?>
					<?php foreach ($categorias_catalogo as $grupo): ?>
						<?php if (!empty($grupo['subcategorias'])): ?>
							<optgroup label="<?php echo htmlspecialchars($grupo['categoria']->nombre); ?>">
								<?php foreach ($grupo['subcategorias'] as $subcat): ?>
									<option value="<?php echo $subcat->id; ?>">
										<?php echo htmlspecialchars($subcat->nombre); ?>
									</option>
								<?php endforeach; ?>
							</optgroup>
						<?php else: ?>
							<option value="<?php echo $grupo['categoria']->id; ?>">
								<?php echo htmlspecialchars($grupo['categoria']->nombre); ?>
							</option>
						<?php endif; ?>
					<?php endforeach; ?>
				<?php endif; ?>
			</select>
		</div>
	</div>
</div>

<!-- Bento Grid de Productos -->
<div class="bento-grid">
	<?php 
	$contador = 0;
	$total_productos = count($productos_catalogo ?? []);
	
	foreach ($productos_catalogo as $index => $producto): 
		$isFeatured = ($contador === 0 && $total_productos >= 4);
		$isWide = ($contador === 3 || $contador === 6) && !$isFeatured;
		
		// Determinar estado de stock
		$stockClass = 'bento-stock-available';
		$stockText = 'Disponible';
		if ($producto->stock_actual <= 0) {
			$stockClass = 'bento-stock-out';
			$stockText = 'Agotado';
		} elseif ($producto->stock_actual <= $producto->stock_minimo) {
			$stockClass = 'bento-stock-low';
			$stockText = 'Últimas unidades';
		}
	?>
		<?php if ($contador === 2 && $total_productos >= 4): ?>
		<!-- Tarjeta informativa -->
		<div class="bento-card bento-info-card">
			<i class="bi bi-truck fs-1 mb-3" style="color: var(--color_principal-500);"></i>
			<h4 class="fw-bold mb-2">Envío Disponible</h4>
			<p class="text-muted small mb-0">Consulta nuestras opciones de envío para pedidos mayoristas.</p>
		</div>
		<?php endif; ?>
		
		<div class="bento-card producto-card <?php echo $isFeatured ? 'bento-featured' : ''; ?> <?php echo $isWide ? 'bento-wide' : ''; ?>" data-categoria="<?php echo $producto->id_categoria ?? ''; ?>">
			<?php if (!empty($producto->imagen_principal_url)): ?>
				<img src="<?php echo $producto->imagen_principal_url; ?>" 
					 alt="<?php echo htmlspecialchars($producto->nombre); ?>" 
					 class="bento-card-img">
			<?php else: ?>
				<div class="bento-card-placeholder">
					<i class="bi bi-box-seam" style="font-size: <?php echo $isFeatured ? '5rem' : '3rem'; ?>;"></i>
				</div>
			<?php endif; ?>
			
			<div class="bento-card-body">
				<?php if (!empty($producto->categoria_nombre)): ?>
					<span class="bento-card-category"><?php echo htmlspecialchars($producto->categoria_nombre); ?></span>
				<?php endif; ?>
				
				<h3 class="bento-card-title"><?php echo htmlspecialchars($producto->nombre); ?></h3>
				
				<?php if (!empty($producto->sku)): ?>
					<p class="bento-card-sku">SKU: <?php echo htmlspecialchars($producto->sku); ?></p>
				<?php endif; ?>
				
				<?php if ($isFeatured && !empty($producto->descripcion)): ?>
					<p class="text-muted small mb-3"><?php echo mb_strimwidth(htmlspecialchars($producto->descripcion), 0, 120, '...'); ?></p>
				<?php endif; ?>
				
				<div class="bento-card-footer">
					<div>
						<span class="bento-card-price">$<?php echo number_format($producto->precio_venta, 2); ?></span>
						<span class="bento-stock-badge <?php echo $stockClass; ?> ms-2"><?php echo $stockText; ?></span>
					</div>
					<button class="bento-btn-cart" title="Ver detalles">
						<i class="bi bi-eye"></i>
					</button>
				</div>
			</div>
		</div>
		
		<?php if ($contador === 4 && $total_productos >= 6): ?>
		<!-- Banner informativo -->
		<div class="bento-card bento-promo-card bento-wide">
			<div class="bento-promo-bg"></div>
			<div class="bento-promo-content">
				<h3 class="fw-bold mb-2">Productos de Calidad</h3>
				<p class="text-white-50 small mb-3">Contamos con una amplia variedad de productos seleccionados para ti.</p>
			</div>
		</div>
		<?php endif; ?>
		
	<?php 
		$contador++;
	endforeach; 
	?>
	
	<?php if (empty($productos_catalogo)): ?>
	<!-- Estado vacío -->
	<div class="col-span-4 text-center py-5">
		<i class="bi bi-box-seam display-1 text-muted mb-3 d-block"></i>
		<h3 class="text-muted">No hay productos disponibles</h3>
		<p class="text-muted">Vuelve pronto para ver nuestro catálogo.</p>
	</div>
	<?php endif; ?>
</div>

<!-- Información de contacto -->
<div class="text-center mt-5 pt-4">
	<div class="d-inline-block p-5 rounded-4" style="background: var(--color_principal-50);">
		<i class="bi bi-headset fs-1 mb-3 d-block" style="color: var(--color_principal-500);"></i>
		<h4 class="fw-bold mb-2">¿Te interesa algún producto?</h4>
		<p class="text-muted mb-0">Contáctanos para más información sobre disponibilidad y precios.</p>
	</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
	const selectCategoria = document.getElementById('select-categoria');
	const productos = document.querySelectorAll('.producto-card');
	const tarjetasInfo = document.querySelectorAll('.bento-info-card, .bento-promo-card');
	
	selectCategoria.addEventListener('change', function() {
		const categoriaSeleccionada = this.value;
		
		// Filtrar productos
		productos.forEach(producto => {
			const categoriaProducto = producto.getAttribute('data-categoria');
			
			if (categoriaSeleccionada === 'todos' || categoriaProducto === categoriaSeleccionada) {
				producto.style.display = '';
			} else {
				producto.style.display = 'none';
			}
		});
		
		// Ocultar/mostrar tarjetas informativas según filtro
		tarjetasInfo.forEach(tarjeta => {
			if (categoriaSeleccionada === 'todos') {
				tarjeta.style.display = '';
			} else {
				tarjeta.style.display = 'none';
			}
		});
	});
});
</script>
