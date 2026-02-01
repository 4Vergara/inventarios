<!-- Header de la sección -->
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
	<div>
		<h2 class="mb-1" style="color: var(--color_principal-700);">
			<i class="bi bi-<?php echo isset($producto) ? 'pencil-square' : 'plus-circle'; ?> me-2"></i>
			<?php echo isset($producto) ? 'Editar Producto' : 'Nuevo Producto'; ?>
		</h2>
		<nav aria-label="breadcrumb">
			<ol class="breadcrumb mb-0">
				<li class="breadcrumb-item"><a href="<?php echo IP_SERVER; ?>" class="text-decoration-none" style="color: var(--color_principal-500);">Inicio</a></li>
				<li class="breadcrumb-item"><a href="<?php echo IP_SERVER . 'productos'; ?>" class="text-decoration-none" style="color: var(--color_principal-500);">Productos</a></li>
				<li class="breadcrumb-item active"><?php echo isset($producto) ? 'Editar' : 'Nuevo'; ?></li>
			</ol>
		</nav>
	</div>
	<a href="<?php echo IP_SERVER . 'productos'; ?>" class="btn btn-outline-secondary">
		<i class="bi bi-arrow-left me-2"></i>Volver al listado
	</a>
</div>

<form id="formProducto" class="needs-validation" novalidate>
	<input type="hidden" name="id" value="<?php echo isset($producto) ? $producto->id : ''; ?>">
	
	<!-- Navegación por secciones -->
	<div class="card shadow-sm border-0 mb-4">
		<div class="card-body py-2">
			<ul class="nav nav-pills nav-fill flex-column flex-md-row" id="seccionesTab" role="tablist">
				<li class="nav-item" role="presentation">
					<button class="nav-link active" id="identificacion-tab" data-bs-toggle="pill" data-bs-target="#identificacion" type="button" role="tab">
						<i class="bi bi-tag me-1"></i><span class="d-none d-sm-inline">Identificación</span>
					</button>
				</li>
				<li class="nav-item" role="presentation">
					<button class="nav-link" id="economicos-tab" data-bs-toggle="pill" data-bs-target="#economicos" type="button" role="tab">
						<i class="bi bi-currency-dollar me-1"></i><span class="d-none d-sm-inline">Precios e Inventario</span>
					</button>
				</li>
				<li class="nav-item" role="presentation">
					<button class="nav-link" id="logistica-tab" data-bs-toggle="pill" data-bs-target="#logistica" type="button" role="tab">
						<i class="bi bi-truck me-1"></i><span class="d-none d-sm-inline">Logística</span>
					</button>
				</li>
				<li class="nav-item" role="presentation">
					<button class="nav-link" id="perecederos-tab" data-bs-toggle="pill" data-bs-target="#perecederos" type="button" role="tab">
						<i class="bi bi-cup-hot me-1"></i><span class="d-none d-sm-inline">Perecederos</span>
					</button>
				</li>
				<li class="nav-item" role="presentation">
					<button class="nav-link" id="ropa-tab" data-bs-toggle="pill" data-bs-target="#ropa" type="button" role="tab">
						<i class="bi bi-bag me-1"></i><span class="d-none d-sm-inline">Ropa/Moda</span>
					</button>
				</li>
				<li class="nav-item" role="presentation">
					<button class="nav-link" id="tecnologia-tab" data-bs-toggle="pill" data-bs-target="#tecnologia" type="button" role="tab">
						<i class="bi bi-cpu me-1"></i><span class="d-none d-sm-inline">Tecnología</span>
					</button>
				</li>
				<li class="nav-item" role="presentation">
					<button class="nav-link" id="extras-tab" data-bs-toggle="pill" data-bs-target="#extras" type="button" role="tab">
						<i class="bi bi-sliders me-1"></i><span class="d-none d-sm-inline">Extras</span>
					</button>
				</li>
			</ul>
		</div>
	</div>

	<!-- Contenido de las secciones -->
	<div class="tab-content tarjeta_tab" id="seccionesTabContent">
		
		<!-- ==========================================
			 1. IDENTIFICACIÓN Y GENERALES
			 ========================================== -->
		<div class="tab-pane fade show active" id="identificacion" role="tabpanel">
			<div class="card shadow-sm border-0 mb-4">
				<div class="card-header bg-white border-bottom">
					<h5 class="mb-0" style="color: var(--color_principal-600);">
						<i class="bi bi-tag me-2"></i>Identificación y Datos Generales
					</h5>
				</div>
				<div class="card-body">
					<div class="row g-3">
						<!-- Categoría -->
						<div class="col-md-6 col-lg-4">
							<label for="id_categoria" class="form-label fw-semibold">Categoría <span class="text-danger">*</span></label>
							<select class="form-select" id="id_categoria" name="id_categoria" required>
								<option value="">Seleccione una categoría</option>
								<?php if (isset($categorias) && !empty($categorias)): ?>
									<?php foreach ($categorias as $grupo): ?>
										<?php if (!empty($grupo['subcategorias'])): ?>
											<!-- Categoría principal como optgroup -->
											<optgroup label="<?php echo $grupo['categoria']->nombre; ?>">
												<?php foreach ($grupo['subcategorias'] as $subcat): ?>
													<option value="<?php echo $subcat->id; ?>" <?php echo (isset($producto) && $producto->id_categoria == $subcat->id) ? 'selected' : ''; ?>>
														<?php echo $subcat->nombre; ?>
													</option>
												<?php endforeach; ?>
											</optgroup>
										<?php else: ?>
											<!-- Si no tiene subcategorías, mostrar la categoría principal como opción -->
											<option value="<?php echo $grupo['categoria']->id; ?>" <?php echo (isset($producto) && $producto->id_categoria == $grupo['categoria']->id) ? 'selected' : ''; ?>>
												<?php echo $grupo['categoria']->nombre; ?>
											</option>
										<?php endif; ?>
									<?php endforeach; ?>
								<?php endif; ?>
							</select>
							<div class="invalid-feedback">Seleccione una categoría</div>
						</div>
						
						<!-- SKU -->
						<div class="col-md-6 col-lg-4">
							<label for="sku" class="form-label fw-semibold">SKU</label>
							<div class="input-group">
								<span class="input-group-text"><i class="bi bi-upc"></i></span>
								<input type="text" class="form-control" id="sku" name="sku" 
									value="<?php echo isset($producto) ? $producto->sku : ''; ?>" 
									placeholder="Código único de inventario">
							</div>
							<small class="text-muted">Stock Keeping Unit - Código único</small>
						</div>
						
						<!-- Código de barras -->
						<div class="col-md-6 col-lg-4">
							<label for="codigo_barras" class="form-label fw-semibold">Código de Barras</label>
							<div class="input-group">
								<span class="input-group-text"><i class="bi bi-upc-scan"></i></span>
								<input type="text" class="form-control" id="codigo_barras" name="codigo_barras" 
									value="<?php echo isset($producto) ? $producto->codigo_barras : ''; ?>" 
									placeholder="EAN, UPC o ISBN">
							</div>
						</div>
						
						<!-- Nombre -->
						<div class="col-12">
							<label for="nombre" class="form-label fw-semibold">Nombre del Producto <span class="text-danger">*</span></label>
							<input type="text" class="form-control form-control-lg" id="nombre" name="nombre" 
								value="<?php echo isset($producto) ? $producto->nombre : ''; ?>" 
								placeholder="Ingrese el nombre del producto" required>
							<div class="invalid-feedback">El nombre es requerido</div>
						</div>
						
						<!-- Marca -->
						<div class="col-md-6">
							<label for="marca" class="form-label fw-semibold">Marca</label>
							<input type="text" class="form-control" id="marca" name="marca" 
								value="<?php echo isset($producto) ? $producto->marca : ''; ?>" 
								placeholder="Marca del producto">
						</div>
						
						<!-- Imagen URL -->
						<div class="col-md-6">
							<label for="imagen_principal_url" class="form-label fw-semibold">URL de Imagen Principal</label>
							<div class="input-group">
								<span class="input-group-text"><i class="bi bi-image"></i></span>
								<input type="url" class="form-control" id="imagen_principal_url" name="imagen_principal_url" 
									value="<?php echo isset($producto) ? $producto->imagen_principal_url : ''; ?>" 
									placeholder="https://ejemplo.com/imagen.jpg">
							</div>
						</div>
						
						<!-- Vista previa de imagen -->
						<div class="col-12">
							<div id="previewImagenContainer" class="text-center p-3 bg-light rounded <?php echo (isset($producto) && $producto->imagen_principal_url) ? '' : 'd-none'; ?>">
								<img id="previewImagen" src="<?php echo isset($producto) ? $producto->imagen_principal_url : ''; ?>" 
									class="img-fluid rounded" style="max-height: 200px;">
							</div>
						</div>
						
						<!-- Descripción corta -->
						<div class="col-12">
							<label for="descripcion_corta" class="form-label fw-semibold">Descripción Corta</label>
							<textarea class="form-control" id="descripcion_corta" name="descripcion_corta" rows="2" 
								maxlength="500" placeholder="Breve descripción del producto (máx. 500 caracteres)"><?php echo isset($producto) ? $producto->descripcion_corta : ''; ?></textarea>
							<div class="form-text text-end"><span id="contadorDescCorta">0</span>/500</div>
						</div>
						
						<!-- Descripción detallada -->
						<div class="col-12">
							<label for="descripcion_detallada" class="form-label fw-semibold">Descripción Detallada</label>
							<textarea class="form-control" id="descripcion_detallada" name="descripcion_detallada" rows="4" 
								placeholder="Descripción completa del producto"><?php echo isset($producto) ? $producto->descripcion_detallada : ''; ?></textarea>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- ==========================================
			 2. ECONÓMICOS E INVENTARIO
			 ========================================== -->
		<div class="tab-pane fade" id="economicos" role="tabpanel">
			<div class="card shadow-sm border-0 mb-4">
				<div class="card-header bg-white border-bottom">
					<h5 class="mb-0" style="color: var(--color_principal-600);">
						<i class="bi bi-currency-dollar me-2"></i>Precios e Inventario
					</h5>
				</div>
				<div class="card-body">
					<div class="row g-3">
						<!-- Precio Costo -->
						<div class="col-md-6 col-lg-4">
							<label for="precio_costo" class="form-label fw-semibold">Precio de Costo <span class="text-danger">*</span></label>
							<div class="input-group">
								<span class="input-group-text">$</span>
								<input type="number" class="form-control" id="precio_costo" name="precio_costo" 
									value="<?php echo isset($producto) ? $producto->precio_costo : '0.00'; ?>" 
									step="0.01" min="0" required>
							</div>
							<small class="text-muted">Costo de adquisición</small>
						</div>
						
						<!-- Precio Venta -->
						<div class="col-md-6 col-lg-4">
							<label for="precio_venta" class="form-label fw-semibold">Precio de Venta <span class="text-danger">*</span></label>
							<div class="input-group">
								<span class="input-group-text">$</span>
								<input type="number" class="form-control" id="precio_venta" name="precio_venta" 
									value="<?php echo isset($producto) ? $producto->precio_venta : '0.00'; ?>" 
									step="0.01" min="0" required>
							</div>
							<small class="text-muted">Precio al público</small>
						</div>
						
						<!-- Porcentaje de Impuesto -->
						<div class="col-md-6 col-lg-4">
							<label for="porcentaje_impuesto" class="form-label fw-semibold">Impuesto (%)</label>
							<div class="input-group">
								<input type="number" class="form-control" id="porcentaje_impuesto" name="porcentaje_impuesto" 
									value="<?php echo isset($producto) ? $producto->porcentaje_impuesto : '0.00'; ?>" 
									step="0.01" min="0" max="100">
								<span class="input-group-text">%</span>
							</div>
							<small class="text-muted">IVA u otro impuesto</small>
						</div>
						
						<!-- Margen de ganancia calculado -->
						<div class="col-12">
							<div class="alert alert-info d-flex align-items-center" role="alert">
								<i class="bi bi-calculator me-2 fs-4"></i>
								<div>
									<strong>Margen de Ganancia:</strong> <span id="margenGanancia">$0.00</span> 
									(<span id="porcentajeMargen">0%</span>)
								</div>
							</div>
						</div>
						
						<!-- Stock Actual -->
						<div class="col-md-6 col-lg-3">
							<label for="stock_actual" class="form-label fw-semibold">Stock Actual <span class="text-danger">*</span></label>
							<input type="number" class="form-control" id="stock_actual" name="stock_actual" 
								value="<?php echo isset($producto) ? $producto->stock_actual : '0'; ?>" 
								min="0" required>
						</div>
						
						<!-- Stock Mínimo -->
						<div class="col-md-6 col-lg-3">
							<label for="stock_minimo" class="form-label fw-semibold">Stock Mínimo</label>
							<input type="number" class="form-control" id="stock_minimo" name="stock_minimo" 
								value="<?php echo isset($producto) ? $producto->stock_minimo : '5'; ?>" 
								min="0">
							<small class="text-muted">Alerta de reabastecimiento</small>
						</div>
						
						<!-- Unidad de Medida -->
						<div class="col-md-6 col-lg-3">
							<label for="unidad_medida" class="form-label fw-semibold">Unidad de Medida</label>
							<select class="form-select" id="unidad_medida" name="unidad_medida">
								<?php 
								$unidades = ['unidad', 'kg', 'g', 'litro', 'ml', 'metro', 'cm', 'paquete', 'caja', 'docena'];
								$unidadActual = isset($producto) ? $producto->unidad_medida : 'unidad';
								foreach ($unidades as $unidad): ?>
									<option value="<?php echo $unidad; ?>" <?php echo ($unidadActual == $unidad) ? 'selected' : ''; ?>>
										<?php echo ucfirst($unidad); ?>
									</option>
								<?php endforeach; ?>
							</select>
						</div>
						
						<!-- Estado -->
						<div class="col-md-6 col-lg-3">
							<label for="estado" class="form-label fw-semibold">Estado</label>
							<select class="form-select" id="estado" name="estado">
								<?php 
								$estados = ['activo' => 'Activo', 'inactivo' => 'Inactivo', 'descatalogado' => 'Descatalogado'];
								$estadoActual = isset($producto) ? $producto->estado : 'activo';
								foreach ($estados as $key => $val): ?>
									<option value="<?php echo $key; ?>" <?php echo ($estadoActual == $key) ? 'selected' : ''; ?>>
										<?php echo $val; ?>
									</option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- ==========================================
			 3. LOGÍSTICA
			 ========================================== -->
		<div class="tab-pane fade" id="logistica" role="tabpanel">
			<div class="card shadow-sm border-0 mb-4">
				<div class="card-header bg-white border-bottom">
					<h5 class="mb-0" style="color: var(--color_principal-600);">
						<i class="bi bi-truck me-2"></i>Logística y Envío
					</h5>
				</div>
				<div class="card-body">
					<div class="row g-3">
						<!-- Peso -->
						<div class="col-md-6 col-lg-3">
							<label for="peso_kg" class="form-label fw-semibold">Peso</label>
							<div class="input-group">
								<input type="number" class="form-control" id="peso_kg" name="peso_kg" 
									value="<?php echo isset($producto) ? $producto->peso_kg : ''; ?>" 
									step="0.001" min="0">
								<span class="input-group-text">kg</span>
							</div>
						</div>
						
						<!-- Ancho -->
						<div class="col-md-6 col-lg-3">
							<label for="ancho_cm" class="form-label fw-semibold">Ancho</label>
							<div class="input-group">
								<input type="number" class="form-control" id="ancho_cm" name="ancho_cm" 
									value="<?php echo isset($producto) ? $producto->ancho_cm : ''; ?>" 
									step="0.01" min="0">
								<span class="input-group-text">cm</span>
							</div>
						</div>
						
						<!-- Alto -->
						<div class="col-md-6 col-lg-3">
							<label for="alto_cm" class="form-label fw-semibold">Alto</label>
							<div class="input-group">
								<input type="number" class="form-control" id="alto_cm" name="alto_cm" 
									value="<?php echo isset($producto) ? $producto->alto_cm : ''; ?>" 
									step="0.01" min="0">
								<span class="input-group-text">cm</span>
							</div>
						</div>
						
						<!-- Profundidad -->
						<div class="col-md-6 col-lg-3">
							<label for="profundidad_cm" class="form-label fw-semibold">Profundidad</label>
							<div class="input-group">
								<input type="number" class="form-control" id="profundidad_cm" name="profundidad_cm" 
									value="<?php echo isset($producto) ? $producto->profundidad_cm : ''; ?>" 
									step="0.01" min="0">
								<span class="input-group-text">cm</span>
							</div>
						</div>
						
						<!-- Volumen calculado -->
						<div class="col-md-6">
							<div class="bg-light p-3 rounded">
								<strong><i class="bi bi-box me-2"></i>Volumen:</strong> 
								<span id="volumenCalculado">0</span> cm³
							</div>
						</div>
						
						<!-- Envío gratis -->
						<div class="col-md-6">
							<div class="form-check form-switch mt-3">
								<input class="form-check-input" type="checkbox" id="es_envio_gratis" name="es_envio_gratis" value="1"
									<?php echo (isset($producto) && $producto->es_envio_gratis) ? 'checked' : ''; ?>>
								<label class="form-check-label fw-semibold" for="es_envio_gratis">
									<i class="bi bi-gift me-1"></i>Envío Gratis
								</label>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- ==========================================
			 4. PERECEDEROS / COMIDA
			 ========================================== -->
		<div class="tab-pane fade" id="perecederos" role="tabpanel">
			<div class="card shadow-sm border-0 mb-4">
				<div class="card-header bg-white border-bottom">
					<h5 class="mb-0" style="color: var(--color_principal-600);">
						<i class="bi bi-cup-hot me-2"></i>Productos Perecederos / Alimentos
					</h5>
				</div>
				<div class="card-body">
					<div class="row g-3">
						<!-- Es perecedero -->
						<div class="col-12">
							<div class="form-check form-switch">
								<input class="form-check-input" type="checkbox" id="es_perecedero" name="es_perecedero" value="1"
									<?php echo (isset($producto) && $producto->es_perecedero) ? 'checked' : ''; ?>>
								<label class="form-check-label fw-semibold" for="es_perecedero">
									<i class="bi bi-exclamation-triangle text-warning me-1"></i>Es producto perecedero
								</label>
							</div>
						</div>
						
						<div id="camposPerecederos" class="<?php echo (isset($producto) && $producto->es_perecedero) ? '' : 'd-none'; ?>">
							<div class="row g-3">
								<!-- Fecha de elaboración -->
								<div class="col-md-6">
									<label for="fecha_elaboracion" class="form-label fw-semibold">Fecha de Elaboración</label>
									<input type="date" class="form-control" id="fecha_elaboracion" name="fecha_elaboracion" 
										value="<?php echo isset($producto) ? $producto->fecha_elaboracion : ''; ?>">
								</div>
								
								<!-- Fecha de vencimiento -->
								<div class="col-md-6">
									<label for="fecha_vencimiento" class="form-label fw-semibold">Fecha de Vencimiento</label>
									<input type="date" class="form-control" id="fecha_vencimiento" name="fecha_vencimiento" 
										value="<?php echo isset($producto) ? $producto->fecha_vencimiento : ''; ?>">
								</div>
								
								<!-- Temperatura de conservación -->
								<div class="col-md-6">
									<label for="temperatura_conservacion" class="form-label fw-semibold">Temperatura de Conservación</label>
									<select class="form-select" id="temperatura_conservacion" name="temperatura_conservacion">
										<option value="">Seleccione...</option>
										<?php 
										$temperaturas = ['Ambiente', 'Refrigerado (0-4°C)', 'Congelado (-18°C)', 'Fresco (10-15°C)'];
										$tempActual = isset($producto) ? $producto->temperatura_conservacion : '';
										foreach ($temperaturas as $temp): ?>
											<option value="<?php echo $temp; ?>" <?php echo ($tempActual == $temp) ? 'selected' : ''; ?>>
												<?php echo $temp; ?>
											</option>
										<?php endforeach; ?>
									</select>
								</div>
								
								<!-- Ingredientes -->
								<div class="col-12">
									<label for="ingredientes" class="form-label fw-semibold">Ingredientes</label>
									<textarea class="form-control" id="ingredientes" name="ingredientes" rows="3" 
										placeholder="Lista de ingredientes separados por coma"><?php echo isset($producto) ? $producto->ingredientes : ''; ?></textarea>
								</div>
								
								<!-- Información nutricional -->
								<div class="col-12">
									<label for="info_nutricional" class="form-label fw-semibold">Información Nutricional</label>
									<textarea class="form-control" id="info_nutricional" name="info_nutricional" rows="3" 
										placeholder="Tabla nutricional, calorías, etc."><?php echo isset($producto) ? $producto->info_nutricional : ''; ?></textarea>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- ==========================================
			 5. ROPA / MODA
			 ========================================== -->
		<div class="tab-pane fade" id="ropa" role="tabpanel">
			<div class="card shadow-sm border-0 mb-4">
				<div class="card-header bg-white border-bottom">
					<h5 class="mb-0" style="color: var(--color_principal-600);">
						<i class="bi bi-bag me-2"></i>Ropa y Moda
					</h5>
				</div>
				<div class="card-body">
					<div class="row g-3">
						<!-- Talla -->
						<div class="col-md-6 col-lg-4">
							<label for="talla" class="form-label fw-semibold">Talla</label>
							<select class="form-select" id="talla" name="talla">
								<option value="">Sin talla</option>
								<?php 
								$tallas = ['XS', 'S', 'M', 'L', 'XL', 'XXL', 'XXXL', '36', '38', '40', '42', '44', '46', '48'];
								$tallaActual = isset($producto) ? $producto->talla : '';
								foreach ($tallas as $talla): ?>
									<option value="<?php echo $talla; ?>" <?php echo ($tallaActual == $talla) ? 'selected' : ''; ?>>
										<?php echo $talla; ?>
									</option>
								<?php endforeach; ?>
							</select>
						</div>
						
						<!-- Color -->
						<div class="col-md-6 col-lg-4">
							<label for="color" class="form-label fw-semibold">Color</label>
							<input type="text" class="form-control" id="color" name="color" 
								value="<?php echo isset($producto) ? $producto->color : ''; ?>" 
								placeholder="Ej: Azul marino, Rojo">
						</div>
						
						<!-- Material -->
						<div class="col-md-6 col-lg-4">
							<label for="material_principal" class="form-label fw-semibold">Material Principal</label>
							<input type="text" class="form-control" id="material_principal" name="material_principal" 
								value="<?php echo isset($producto) ? $producto->material_principal : ''; ?>" 
								placeholder="Ej: Algodón, Poliéster, Cuero">
						</div>
						
						<!-- Género -->
						<div class="col-md-6">
							<label for="genero" class="form-label fw-semibold">Género</label>
							<select class="form-select" id="genero" name="genero">
								<option value="">Sin especificar</option>
								<?php 
								$generos = ['hombre' => 'Hombre', 'mujer' => 'Mujer', 'unisex' => 'Unisex', 'niño' => 'Niño', 'niña' => 'Niña'];
								$generoActual = isset($producto) ? $producto->genero : '';
								foreach ($generos as $key => $val): ?>
									<option value="<?php echo $key; ?>" <?php echo ($generoActual == $key) ? 'selected' : ''; ?>>
										<?php echo $val; ?>
									</option>
								<?php endforeach; ?>
							</select>
						</div>
						
						<!-- Estilo/Corte -->
						<div class="col-md-6">
							<label for="estilo_corte" class="form-label fw-semibold">Estilo / Corte</label>
							<input type="text" class="form-control" id="estilo_corte" name="estilo_corte" 
								value="<?php echo isset($producto) ? $producto->estilo_corte : ''; ?>" 
								placeholder="Ej: Slim fit, Regular, Oversize">
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- ==========================================
			 6. TECNOLOGÍA / ELECTRÓNICA
			 ========================================== -->
		<div class="tab-pane fade" id="tecnologia" role="tabpanel">
			<div class="card shadow-sm border-0 mb-4">
				<div class="card-header bg-white border-bottom">
					<h5 class="mb-0" style="color: var(--color_principal-600);">
						<i class="bi bi-cpu me-2"></i>Tecnología y Electrónica
					</h5>
				</div>
				<div class="card-body">
					<div class="row g-3">
						<!-- Modelo técnico -->
						<div class="col-md-6">
							<label for="modelo_tecnico" class="form-label fw-semibold">Modelo Técnico</label>
							<input type="text" class="form-control" id="modelo_tecnico" name="modelo_tecnico" 
								value="<?php echo isset($producto) ? $producto->modelo_tecnico : ''; ?>" 
								placeholder="Número de modelo">
						</div>
						
						<!-- Número de serie -->
						<div class="col-md-6">
							<label for="numero_serie" class="form-label fw-semibold">Número de Serie</label>
							<input type="text" class="form-control" id="numero_serie" name="numero_serie" 
								value="<?php echo isset($producto) ? $producto->numero_serie : ''; ?>" 
								placeholder="Serial del producto">
						</div>
						
						<!-- Garantía -->
						<div class="col-md-6 col-lg-3">
							<label for="garantia_meses" class="form-label fw-semibold">Garantía</label>
							<div class="input-group">
								<input type="number" class="form-control" id="garantia_meses" name="garantia_meses" 
									value="<?php echo isset($producto) ? $producto->garantia_meses : '0'; ?>" 
									min="0">
								<span class="input-group-text">meses</span>
							</div>
						</div>
						
						<!-- Consumo -->
						<div class="col-md-6 col-lg-3">
							<label for="consumo_watts" class="form-label fw-semibold">Consumo</label>
							<div class="input-group">
								<input type="number" class="form-control" id="consumo_watts" name="consumo_watts" 
									value="<?php echo isset($producto) ? $producto->consumo_watts : ''; ?>" 
									min="0">
								<span class="input-group-text">W</span>
							</div>
						</div>
						
						<!-- Voltaje -->
						<div class="col-md-6 col-lg-3">
							<label for="voltaje" class="form-label fw-semibold">Voltaje</label>
							<select class="form-select" id="voltaje" name="voltaje">
								<option value="">Sin especificar</option>
								<?php 
								$voltajes = ['110V', '220V', '110V-220V', 'USB', 'USB-C', 'Batería'];
								$voltajeActual = isset($producto) ? $producto->voltaje : '';
								foreach ($voltajes as $voltaje): ?>
									<option value="<?php echo $voltaje; ?>" <?php echo ($voltajeActual == $voltaje) ? 'selected' : ''; ?>>
										<?php echo $voltaje; ?>
									</option>
								<?php endforeach; ?>
							</select>
						</div>
						
						<!-- Es inteligente -->
						<div class="col-md-6 col-lg-3">
							<label class="form-label fw-semibold d-block">Dispositivo Smart</label>
							<div class="form-check form-switch mt-2">
								<input class="form-check-input" type="checkbox" id="es_inteligente" name="es_inteligente" value="1"
									<?php echo (isset($producto) && $producto->es_inteligente) ? 'checked' : ''; ?>>
								<label class="form-check-label" for="es_inteligente">
									<i class="bi bi-wifi me-1"></i>IoT / Smart
								</label>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- ==========================================
			 7. ESPECIFICACIONES EXTRAS (JSON)
			 ========================================== -->
		<div class="tab-pane fade" id="extras" role="tabpanel">
			<div class="card shadow-sm border-0 mb-4">
				<div class="card-header bg-white border-bottom">
					<h5 class="mb-0" style="color: var(--color_principal-600);">
						<i class="bi bi-sliders me-2"></i>Especificaciones Adicionales
					</h5>
				</div>
				<div class="card-body">
					<p class="text-muted mb-3">Agregue especificaciones adicionales que no estén contempladas en los campos anteriores.</p>
					
					<div id="contenedorEspecificaciones">
						<?php 
						$especificaciones = [];
						if (isset($producto) && $producto->especificaciones_extra) {
							$especificaciones = json_decode($producto->especificaciones_extra, true) ?: [];
						}
						if (!empty($especificaciones)):
							foreach ($especificaciones as $key => $value): ?>
								<div class="row g-2 mb-2 especificacion-row">
									<div class="col-5">
										<input type="text" class="form-control espec-key" placeholder="Característica" value="<?php echo htmlspecialchars($key); ?>">
									</div>
									<div class="col-5">
										<input type="text" class="form-control espec-value" placeholder="Valor" value="<?php echo htmlspecialchars($value); ?>">
									</div>
									<div class="col-2">
										<button type="button" class="btn btn-outline-danger w-100" onclick="eliminarEspecificacion(this)">
											<i class="bi bi-trash"></i>
										</button>
									</div>
								</div>
							<?php endforeach;
						endif; ?>
					</div>
					
					<button type="button" class="btn btn-outline-secondary mt-2" onclick="agregarEspecificacion()">
						<i class="bi bi-plus-circle me-1"></i>Agregar Especificación
					</button>
					
					<input type="hidden" id="especificaciones_extra" name="especificaciones_extra" 
						value="<?php echo isset($producto) ? htmlspecialchars($producto->especificaciones_extra) : '{}'; ?>">
				</div>
			</div>
		</div>
	</div>

	<!-- Botones de acción -->
	<div class="card shadow-sm border-0 sticky-bottom bg-white">
		<div class="card-body">
			<div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
				<div class="text-muted small">
					<i class="bi bi-info-circle me-1"></i>Los campos marcados con <span class="text-danger">*</span> son obligatorios
				</div>
				<div class="d-flex gap-2">
					<a href="<?php echo IP_SERVER . 'productos'; ?>" class="btn btn-outline-secondary">
						<i class="bi bi-x-circle me-1"></i>Cancelar
					</a>
					<button type="submit" class="btn btn-color_principal btn-lg">
						<i class="bi bi-save me-1"></i>
						<?php echo isset($producto) ? 'Actualizar Producto' : 'Guardar Producto'; ?>
					</button>
				</div>
			</div>
		</div>
	</div>
</form>

<script>
$(document).ready(function() {
	// Contador de descripción corta
	actualizarContadorDescCorta();
	$('#descripcion_corta').on('input', actualizarContadorDescCorta);
	
	// Preview de imagen
	$('#imagen_principal_url').on('change blur', function() {
		let url = $(this).val();
		if (url) {
			$('#previewImagen').attr('src', url);
			$('#previewImagenContainer').removeClass('d-none');
		} else {
			$('#previewImagenContainer').addClass('d-none');
		}
	});
	
	// Cálculo de margen
	$('#precio_costo, #precio_venta').on('input', calcularMargen);
	calcularMargen();
	
	// Cálculo de volumen
	$('#ancho_cm, #alto_cm, #profundidad_cm').on('input', calcularVolumen);
	calcularVolumen();
	
	// Toggle campos perecederos
	$('#es_perecedero').on('change', function() {
		if ($(this).is(':checked')) {
			$('#camposPerecederos').removeClass('d-none');
		} else {
			$('#camposPerecederos').addClass('d-none');
		}
	});
	
	// Envío del formulario
	$('#formProducto').on('submit', function(e) {
		e.preventDefault();
		
		if (!this.checkValidity()) {
			e.stopPropagation();
			$(this).addClass('was-validated');
			
			// Navegar a la primera sección con errores
			let primeraSeccionError = $(this).find('.tab-pane :invalid').first().closest('.tab-pane').attr('id');
			if (primeraSeccionError) {
				$('#' + primeraSeccionError + '-tab').tab('show');
			}
			return;
		}
		
		// Recolectar especificaciones extras
		recolectarEspecificaciones();
		
		let formData = $(this).serialize();
		let url = IP_SERVER + 'productos/guardar';
		
		Swal.fire({
			title: 'Guardando...',
			text: 'Por favor espere',
			allowOutsideClick: false,
			didOpen: () => {
				Swal.showLoading();
			}
		});
		
		$.ajax({
			url: url,
			type: 'POST',
			data: formData,
			dataType: 'json',
			success: function(response) {
				if (response.success) {
					Swal.fire({
						icon: 'success',
						title: '¡Guardado!',
						text: response.message,
						timer: 2000,
						showConfirmButton: false
					}).then(() => {
						window.location.href = IP_SERVER + 'productos';
					});
				} else {
					Swal.fire({
						icon: 'error',
						title: 'Error',
						text: response.message
					});
				}
			},
			error: function() {
				Swal.fire({
					icon: 'error',
					title: 'Error',
					text: 'Ocurrió un error al procesar la solicitud'
				});
			}
		});
	});
});

function actualizarContadorDescCorta() {
	let length = $('#descripcion_corta').val().length;
	$('#contadorDescCorta').text(length);
}

function calcularMargen() {
	let costo = parseFloat($('#precio_costo').val()) || 0;
	let venta = parseFloat($('#precio_venta').val()) || 0;
	let margen = venta - costo;
	let porcentaje = costo > 0 ? ((margen / costo) * 100) : 0;
	
	$('#margenGanancia').text('$' + margen.toFixed(2));
	$('#porcentajeMargen').text(porcentaje.toFixed(1) + '%');
}

function calcularVolumen() {
	let ancho = parseFloat($('#ancho_cm').val()) || 0;
	let alto = parseFloat($('#alto_cm').val()) || 0;
	let profundidad = parseFloat($('#profundidad_cm').val()) || 0;
	let volumen = ancho * alto * profundidad;
	
	$('#volumenCalculado').text(volumen.toLocaleString('es-MX', {maximumFractionDigits: 2}));
}

function agregarEspecificacion() {
	let html = `
		<div class="row g-2 mb-2 especificacion-row">
			<div class="col-5">
				<input type="text" class="form-control espec-key" placeholder="Característica">
			</div>
			<div class="col-5">
				<input type="text" class="form-control espec-value" placeholder="Valor">
			</div>
			<div class="col-2">
				<button type="button" class="btn btn-outline-danger w-100" onclick="eliminarEspecificacion(this)">
					<i class="bi bi-trash"></i>
				</button>
			</div>
		</div>
	`;
	$('#contenedorEspecificaciones').append(html);
}

function eliminarEspecificacion(btn) {
	$(btn).closest('.especificacion-row').remove();
}

function recolectarEspecificaciones() {
	let especificaciones = {};
	$('.especificacion-row').each(function() {
		let key = $(this).find('.espec-key').val().trim();
		let value = $(this).find('.espec-value').val().trim();
		if (key && value) {
			especificaciones[key] = value;
		}
	});
	$('#especificaciones_extra').val(JSON.stringify(especificaciones));
}
</script>

<style>
/* Estilos para el formulario */
.nav-pills .nav-link {
	color: var(--color_principal-600);
	border-radius: 10px;
	transition: all 0.3s ease;
}

.nav-pills .nav-link:hover {
	background-color: var(--color_principal-50);
}

.nav-pills .nav-link.active {
	background-color: var(--color_principal-500);
	color: white;
}

.form-control:focus, .form-select:focus {
	border-color: var(--color_principal-400);
	box-shadow: 0 0 0 0.2rem rgba(249, 115, 22, 0.15);
}

.form-check-input:checked {
	background-color: var(--color_principal-500);
	border-color: var(--color_principal-500);
}

.sticky-bottom {
	position: sticky;
	bottom: 0;
	z-index: 100;
	border-top: 1px solid #e9ecef;
}

@media (max-width: 768px) {
	.nav-pills .nav-link {
		padding: 0.5rem;
		font-size: 0.85rem;
	}
}
</style>