<!-- Page Header -->
<div class="page-header mb-4">
	<div class="row align-items-center">
		<div class="col">
			<nav aria-label="breadcrumb">
				<ol class="breadcrumb mb-2">
					<li class="breadcrumb-item"><a href="<?php echo IP_SERVER; ?>">Inicio</a></li>
					<li class="breadcrumb-item"><a href="<?php echo IP_SERVER . 'ventas'; ?>">Pedidos</a></li>
					<li class="breadcrumb-item active">Nueva Venta</li>
				</ol>
			</nav>
			<h1 class="page-title">Nueva Venta</h1>
			<p class="page-subtitle">Registra una nueva venta en el sistema.</p>
		</div>
	</div>
</div>

<div class="row">
	<!-- Panel Izquierdo: Búsqueda y carrito -->
	<div class="col-lg-8">
		<!-- Buscador de productos -->
		<div class="card mb-4">
			<div class="card-header bg-white">
				<h5 class="mb-0"><i class="bi bi-search me-2"></i>Buscar Productos</h5>
			</div>
			<div class="card-body">
				<div class="row g-3">
					<div class="col-md-8">
						<div class="input-group input-group-lg">
							<span class="input-group-text bg-white"><i class="bi bi-upc-scan"></i></span>
							<input type="text" class="form-control" id="buscarProducto" 
								placeholder="Buscar por nombre, SKU o código de barras..." autofocus>
						</div>
						<div id="resultadosBusqueda" class="search-results"></div>
					</div>
					<div class="col-md-4">
						<button class="btn btn-outline-secondary btn-lg w-100" onclick="abrirBuscadorAvanzado()">
							<i class="bi bi-grid me-2"></i>Catálogo
						</button>
					</div>
				</div>
			</div>
		</div>
		
		<!-- Carrito de productos -->
		<div class="card">
			<div class="card-header bg-white d-flex justify-content-between align-items-center">
				<h5 class="mb-0"><i class="bi bi-cart3 me-2"></i>Productos en la Venta</h5>
				<span class="badge bg-primary rounded-pill" id="cantidadItems">0</span>
			</div>
			<div class="card-body p-0">
				<div class="table-responsive">
					<table class="table table-modern mb-0" id="tablaCarrito">
						<thead>
							<tr>
								<th style="width: 50px;"></th>
								<th>PRODUCTO</th>
								<th class="text-center" style="width: 120px;">CANTIDAD</th>
								<th class="text-end" style="width: 130px;">PRECIO</th>
								<th class="text-end" style="width: 120px;">SUBTOTAL</th>
								<th class="text-center" style="width: 60px;"></th>
							</tr>
						</thead>
						<tbody id="carritoBody">
							<tr id="carritoVacio">
								<td colspan="6" class="text-center py-5 text-muted">
									<i class="bi bi-cart-x display-4 d-block mb-3"></i>
									<p class="mb-0">No hay productos agregados</p>
									<small>Busca y agrega productos usando el buscador de arriba</small>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
	
	<!-- Panel Derecho: Resumen y cliente -->
	<div class="col-lg-4">
		<!-- Datos del cliente -->
		<div class="card mb-4">
			<div class="card-header bg-white">
				<h5 class="mb-0"><i class="bi bi-person me-2"></i>Datos de la Venta</h5>
			</div>
			<div class="card-body">
				<div class="mb-3">
					<label class="form-label fw-semibold">Cliente <span class="text-danger">*</span></label>
					<select class="form-select" id="selectCliente" required>
						<option value="">Seleccionar cliente...</option>
						<?php foreach ($clientes as $cliente): ?>
						<option value="<?php echo $cliente->id; ?>">
							<?php echo $cliente->nombre_completo; ?> - <?php echo $cliente->numero_documento; ?>
						</option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="mb-3">
					<label class="form-label fw-semibold">Vendedor <span class="text-danger">*</span></label>
					<select class="form-select" id="selectVendedor" required>
						<option value="">Seleccionar vendedor...</option>
						<?php foreach ($vendedores as $vendedor): ?>
						<option value="<?php echo $vendedor->id_usuario; ?>" 
							<?php echo (isset($this->session->datosusuario->id_usuario) && $this->session->datosusuario->id_usuario == $vendedor->id_usuario) ? 'selected' : ''; ?>>
							<?php echo $vendedor->nombre_completo; ?>
						</option>
						<?php endforeach; ?>
					</select>
				</div>
			</div>
		</div>
		
		<!-- Resumen de venta -->
		<div class="card resumen-card">
			<div class="card-header bg-white">
				<h5 class="mb-0"><i class="bi bi-receipt me-2"></i>Resumen</h5>
			</div>
			<div class="card-body">
				<div class="resumen-linea">
					<span>Subtotal</span>
					<span id="resumenSubtotal">$0.00</span>
				</div>
				<div class="resumen-linea">
					<span>Impuestos</span>
					<span id="resumenImpuestos">$0.00</span>
				</div>
				<div class="resumen-linea">
					<span>Descuentos</span>
					<span id="resumenDescuentos" class="text-success">-$0.00</span>
				</div>
				<hr>
				<div class="resumen-linea total">
					<span>TOTAL A PAGAR</span>
					<span id="resumenTotal">$0.00</span>
				</div>
			</div>
			<div class="card-footer bg-white border-top-0">
				<button class="btn btn-color_principal btn-lg w-100" id="btnProcesarVenta" onclick="procesarVenta()" disabled>
					<i class="bi bi-check-circle me-2"></i>Procesar Venta
				</button>
				<button class="btn btn-outline-secondary w-100 mt-2" onclick="limpiarCarrito()">
					<i class="bi bi-trash me-2"></i>Limpiar Carrito
				</button>
			</div>
		</div>
	</div>
</div>

<!-- Modal Buscador Avanzado -->
<div class="modal fade" id="modalCatalogo" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog modal-xl modal-dialog-scrollable">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title"><i class="bi bi-grid me-2"></i>Catálogo de Productos</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
			</div>
			<div class="modal-body">
				<div class="row g-3" id="catalogoProductos">
					<!-- Se carga dinámicamente -->
				</div>
			</div>
		</div>
	</div>
</div>

<style>
/* Page Header */
.page-title {
	font-size: 1.8rem;
	font-weight: 800;
	color: #1f2937;
	margin-bottom: 5px;
}
.page-subtitle {
	color: #6b7280;
	font-size: 0.95rem;
	margin-bottom: 0;
}

/* Search Results */
.search-results {
	position: absolute;
	top: 100%;
	left: 0;
	right: 0;
	background: white;
	border: 1px solid #e5e7eb;
	border-top: none;
	border-radius: 0 0 12px 12px;
	max-height: 350px;
	overflow-y: auto;
	z-index: 1000;
	display: none;
	box-shadow: 0 10px 40px rgba(0,0,0,0.15);
}
.search-results.active {
	display: block;
}
.search-item {
	display: flex;
	align-items: center;
	padding: 12px 16px;
	cursor: pointer;
	transition: background 0.15s;
	border-bottom: 1px solid #f3f4f6;
}
.search-item:hover {
	background: #f9fafb;
}
.search-item:last-child {
	border-bottom: none;
}
.search-item-img {
	width: 45px;
	height: 45px;
	border-radius: 10px;
	object-fit: cover;
	margin-right: 12px;
	border: 1px solid #e5e7eb;
}
.search-item-img-placeholder {
	width: 45px;
	height: 45px;
	border-radius: 10px;
	background: #f3f4f6;
	display: flex;
	align-items: center;
	justify-content: center;
	color: #9ca3af;
	margin-right: 12px;
}
.search-item-info {
	flex: 1;
}
.search-item-name {
	font-weight: 600;
	color: #1f2937;
	font-size: 0.95rem;
}
.search-item-sku {
	font-size: 0.75rem;
	color: #9ca3af;
}
.search-item-price {
	font-weight: 700;
	color: var(--color_principal-600);
	font-size: 1rem;
}
.search-item-stock {
	font-size: 0.7rem;
	color: #6b7280;
}

/* Table Modern */
.table-modern thead {
	background: #f9fafb;
}
.table-modern thead th {
	padding: 14px 16px;
	font-size: 0.7rem;
	font-weight: 700;
	color: #6b7280;
	text-transform: uppercase;
	letter-spacing: 0.5px;
	border-bottom: 1px solid #e5e7eb;
}
.table-modern tbody td {
	padding: 14px 16px;
	vertical-align: middle;
	border-bottom: 1px solid #f3f4f6;
}

/* Cart Item */
.cart-item-img {
	width: 40px;
	height: 40px;
	border-radius: 8px;
	object-fit: cover;
	border: 1px solid #e5e7eb;
}
.cart-item-img-placeholder {
	width: 40px;
	height: 40px;
	border-radius: 8px;
	background: #f3f4f6;
	display: flex;
	align-items: center;
	justify-content: center;
	color: #9ca3af;
}
.cart-item-name {
	font-weight: 600;
	color: #1f2937;
	font-size: 0.9rem;
}
.cart-item-sku {
	font-size: 0.75rem;
	color: #9ca3af;
}
.cantidad-control {
	display: flex;
	align-items: center;
	justify-content: center;
	gap: 8px;
}
.cantidad-control button {
	width: 32px;
	height: 32px;
	border-radius: 8px;
	border: 1px solid #e5e7eb;
	background: white;
	display: flex;
	align-items: center;
	justify-content: center;
	cursor: pointer;
	transition: all 0.2s;
}
.cantidad-control button:hover {
	background: var(--color_principal-50);
	border-color: var(--color_principal-300);
	color: var(--color_principal-600);
}
.cantidad-control input {
	width: 50px;
	text-align: center;
	border: 1px solid #e5e7eb;
	border-radius: 8px;
	padding: 6px;
	font-weight: 600;
}
.btn-eliminar-item {
	width: 36px;
	height: 36px;
	border-radius: 10px;
	border: 1px solid #e5e7eb;
	background: white;
	color: #6b7280;
	cursor: pointer;
	transition: all 0.2s;
	display: flex;
	align-items: center;
	justify-content: center;
}
.btn-eliminar-item:hover {
	border-color: #fca5a5;
	color: #dc2626;
	background: #fef2f2;
}

/* Resumen Card */
.resumen-card {
	position: sticky;
	top: 20px;
}
.resumen-linea {
	display: flex;
	justify-content: space-between;
	padding: 10px 0;
	font-size: 0.95rem;
}
.resumen-linea.total {
	font-size: 1.3rem;
	font-weight: 800;
	color: #1f2937;
}

/* Catálogo Modal */
.producto-card-catalogo {
	background: white;
	border: 1px solid #e5e7eb;
	border-radius: 12px;
	padding: 16px;
	cursor: pointer;
	transition: all 0.2s;
}
.producto-card-catalogo:hover {
	border-color: var(--color_principal-300);
	box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}
.producto-card-catalogo img {
	width: 100%;
	height: 120px;
	object-fit: cover;
	border-radius: 8px;
	margin-bottom: 12px;
}
.producto-card-catalogo .nombre {
	font-weight: 600;
	color: #1f2937;
	font-size: 0.9rem;
	margin-bottom: 4px;
}
.producto-card-catalogo .precio {
	font-weight: 700;
	color: var(--color_principal-600);
	font-size: 1.1rem;
}
.producto-card-catalogo .stock {
	font-size: 0.75rem;
	color: #6b7280;
}
</style>

<script>
// Carrito de productos
let carrito = [];
let timeoutBusqueda;

$(document).ready(function() {
	// Buscador de productos
	$('#buscarProducto').on('input', function() {
		let termino = $(this).val().trim();
		
		clearTimeout(timeoutBusqueda);
		
		if (termino.length < 2) {
			$('#resultadosBusqueda').removeClass('active').empty();
			return;
		}
		
		timeoutBusqueda = setTimeout(function() {
			buscarProductos(termino);
		}, 300);
	});
	
	// Cerrar resultados al hacer clic fuera
	$(document).on('click', function(e) {
		if (!$(e.target).closest('.input-group').length) {
			$('#resultadosBusqueda').removeClass('active');
		}
	});
	
	// Enter en buscador para agregar producto por código
	$('#buscarProducto').on('keypress', function(e) {
		if (e.which === 13) {
			let codigo = $(this).val().trim();
			if (codigo) {
				agregarProductoPorCodigo(codigo);
			}
		}
	});
});

function buscarProductos(termino) {
	$.post(IP_SERVER + 'ventas/buscarProductos', { termino: termino }, function(response) {
		if (response.success && response.data.length > 0) {
			let html = '';
			response.data.forEach(function(p) {
				let img = p.imagen_principal_url 
					? `<img src="${p.imagen_principal_url}" class="search-item-img">`
					: `<div class="search-item-img-placeholder"><i class="bi bi-image"></i></div>`;
				
				html += `
					<div class="search-item" onclick="agregarAlCarrito(${JSON.stringify(p).replace(/"/g, '&quot;')})">
						${img}
						<div class="search-item-info">
							<div class="search-item-name">${p.nombre}</div>
							<div class="search-item-sku">SKU: ${p.sku || 'N/A'} | ${p.marca || ''}</div>
						</div>
						<div class="text-end">
							<div class="search-item-price">$${parseFloat(p.precio_venta).toLocaleString('es-MX', {minimumFractionDigits: 2})}</div>
							<div class="search-item-stock">Stock: ${p.stock_actual}</div>
						</div>
					</div>
				`;
			});
			$('#resultadosBusqueda').html(html).addClass('active');
		} else {
			$('#resultadosBusqueda').html('<div class="search-item text-muted">No se encontraron productos</div>').addClass('active');
		}
	});
}

function agregarProductoPorCodigo(codigo) {
	$.post(IP_SERVER + 'ventas/obtenerProducto', { codigo: codigo }, function(response) {
		if (response.success) {
			agregarAlCarrito(response.data);
			$('#buscarProducto').val('').focus();
		} else {
			Swal.fire({
				icon: 'warning',
				title: 'Producto no encontrado',
				text: 'No se encontró ningún producto con ese código',
				timer: 2000,
				showConfirmButton: false
			});
		}
	});
}

function agregarAlCarrito(producto) {
	$('#resultadosBusqueda').removeClass('active');
	$('#buscarProducto').val('').focus();
	
	// Verificar si ya existe en el carrito
	let index = carrito.findIndex(p => p.id == producto.id);
	
	if (index >= 0) {
		// Verificar stock disponible
		if (carrito[index].cantidad >= producto.stock_actual) {
			Swal.fire({
				icon: 'warning',
				title: 'Stock insuficiente',
				text: `Solo hay ${producto.stock_actual} unidades disponibles`,
				timer: 2000,
				showConfirmButton: false
			});
			return;
		}
		carrito[index].cantidad++;
	} else {
		carrito.push({
			id: producto.id,
			sku: producto.sku,
			nombre: producto.nombre,
			imagen: producto.imagen_principal_url,
			precio: parseFloat(producto.precio_venta),
			impuesto: parseFloat(producto.porcentaje_impuesto || 0),
			stock: parseInt(producto.stock_actual),
			cantidad: 1,
			descuento: 0
		});
	}
	
	renderizarCarrito();
}

function renderizarCarrito() {
	let html = '';
	
	if (carrito.length === 0) {
		html = `
			<tr id="carritoVacio">
				<td colspan="6" class="text-center py-5 text-muted">
					<i class="bi bi-cart-x display-4 d-block mb-3"></i>
					<p class="mb-0">No hay productos agregados</p>
					<small>Busca y agrega productos usando el buscador de arriba</small>
				</td>
			</tr>
		`;
		$('#btnProcesarVenta').prop('disabled', true);
	} else {
		carrito.forEach(function(item, index) {
			let img = item.imagen 
				? `<img src="${item.imagen}" class="cart-item-img">`
				: `<div class="cart-item-img-placeholder"><i class="bi bi-image"></i></div>`;
			
			let subtotal = item.cantidad * item.precio;
			
			html += `
				<tr>
					<td>${img}</td>
					<td>
						<div class="cart-item-name">${item.nombre}</div>
						<div class="cart-item-sku">SKU: ${item.sku || 'N/A'}</div>
					</td>
					<td>
						<div class="cantidad-control">
							<button onclick="cambiarCantidad(${index}, -1)"><i class="bi bi-dash"></i></button>
							<input type="number" value="${item.cantidad}" min="1" max="${item.stock}" 
								onchange="setCantidad(${index}, this.value)">
							<button onclick="cambiarCantidad(${index}, 1)"><i class="bi bi-plus"></i></button>
						</div>
					</td>
					<td class="text-end fw-semibold">$${item.precio.toLocaleString('es-MX', {minimumFractionDigits: 2})}</td>
					<td class="text-end fw-bold">$${subtotal.toLocaleString('es-MX', {minimumFractionDigits: 2})}</td>
					<td class="text-center">
						<button class="btn-eliminar-item" onclick="eliminarDelCarrito(${index})">
							<i class="bi bi-trash"></i>
						</button>
					</td>
				</tr>
			`;
		});
		$('#btnProcesarVenta').prop('disabled', false);
	}
	
	$('#carritoBody').html(html);
	$('#cantidadItems').text(carrito.length);
	calcularTotales();
}

function cambiarCantidad(index, delta) {
	let nuevaCantidad = carrito[index].cantidad + delta;
	
	if (nuevaCantidad < 1) {
		eliminarDelCarrito(index);
		return;
	}
	
	if (nuevaCantidad > carrito[index].stock) {
		Swal.fire({
			icon: 'warning',
			title: 'Stock insuficiente',
			text: `Solo hay ${carrito[index].stock} unidades disponibles`,
			timer: 2000,
			showConfirmButton: false
		});
		return;
	}
	
	carrito[index].cantidad = nuevaCantidad;
	renderizarCarrito();
}

function setCantidad(index, cantidad) {
	cantidad = parseInt(cantidad);
	
	if (cantidad < 1) {
		eliminarDelCarrito(index);
		return;
	}
	
	if (cantidad > carrito[index].stock) {
		carrito[index].cantidad = carrito[index].stock;
		Swal.fire({
			icon: 'warning',
			title: 'Stock insuficiente',
			text: `Solo hay ${carrito[index].stock} unidades disponibles`,
			timer: 2000,
			showConfirmButton: false
		});
	} else {
		carrito[index].cantidad = cantidad;
	}
	
	renderizarCarrito();
}

function eliminarDelCarrito(index) {
	carrito.splice(index, 1);
	renderizarCarrito();
}

function limpiarCarrito() {
	if (carrito.length === 0) return;
	
	Swal.fire({
		title: '¿Limpiar carrito?',
		text: 'Se eliminarán todos los productos del carrito',
		icon: 'question',
		showCancelButton: true,
		confirmButtonColor: '#dc2626',
		cancelButtonText: 'Cancelar',
		confirmButtonText: 'Sí, limpiar'
	}).then((result) => {
		if (result.isConfirmed) {
			carrito = [];
			renderizarCarrito();
		}
	});
}

function calcularTotales() {
	let subtotal = 0;
	let impuestos = 0;
	let descuentos = 0;
	
	carrito.forEach(function(item) {
		let lineaSubtotal = item.cantidad * item.precio;
		let lineaImpuesto = lineaSubtotal * (item.impuesto / 100);
		
		subtotal += lineaSubtotal;
		impuestos += lineaImpuesto;
		descuentos += item.descuento || 0;
	});
	
	let total = subtotal + impuestos - descuentos;
	
	$('#resumenSubtotal').text('$' + subtotal.toLocaleString('es-MX', {minimumFractionDigits: 2}));
	$('#resumenImpuestos').text('$' + impuestos.toLocaleString('es-MX', {minimumFractionDigits: 2}));
	$('#resumenDescuentos').text('-$' + descuentos.toLocaleString('es-MX', {minimumFractionDigits: 2}));
	$('#resumenTotal').text('$' + total.toLocaleString('es-MX', {minimumFractionDigits: 2}));
}

function procesarVenta() {
	let idCliente = $('#selectCliente').val();
	let idVendedor = $('#selectVendedor').val();
	
	if (!idCliente) {
		Swal.fire({
			icon: 'warning',
			title: 'Cliente requerido',
			text: 'Por favor selecciona un cliente'
		});
		$('#selectCliente').focus();
		return;
	}
	
	if (!idVendedor) {
		Swal.fire({
			icon: 'warning',
			title: 'Vendedor requerido',
			text: 'Por favor selecciona un vendedor'
		});
		$('#selectVendedor').focus();
		return;
	}
	
	if (carrito.length === 0) {
		Swal.fire({
			icon: 'warning',
			title: 'Carrito vacío',
			text: 'Agrega al menos un producto'
		});
		return;
	}
	
	// Confirmar venta
	Swal.fire({
		title: '¿Procesar venta?',
		html: `<p>Se registrará la venta con <strong>${carrito.length}</strong> producto(s)</p>
			   <p class="h4 text-primary fw-bold">${$('#resumenTotal').text()}</p>`,
		icon: 'question',
		showCancelButton: true,
		confirmButtonColor: 'var(--color_principal-500)',
		cancelButtonText: 'Cancelar',
		confirmButtonText: 'Sí, procesar'
	}).then((result) => {
		if (result.isConfirmed) {
			enviarVenta(idCliente, idVendedor);
		}
	});
}

function enviarVenta(idCliente, idVendedor) {
	$('#btnProcesarVenta').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Procesando...');
	
	$.ajax({
		url: IP_SERVER + 'ventas/guardar',
		type: 'POST',
		data: {
			id_cliente: idCliente,
			id_vendedor: idVendedor,
			productos: JSON.stringify(carrito)
		},
		dataType: 'json',
		success: function(response) {
			if (response.success) {
				Swal.fire({
					icon: 'success',
					title: '¡Venta registrada!',
					html: `<p>Folio: <strong>${response.folio}</strong></p>`,
					confirmButtonText: 'Ver detalle',
					showCancelButton: true,
					cancelButtonText: 'Nueva venta'
				}).then((result) => {
					if (result.isConfirmed) {
						window.location.href = IP_SERVER + 'ventas/ver/' + response.id;
					} else {
						// Limpiar para nueva venta
						carrito = [];
						renderizarCarrito();
						$('#selectCliente').val('');
						$('#btnProcesarVenta').prop('disabled', true).html('<i class="bi bi-check-circle me-2"></i>Procesar Venta');
					}
				});
			} else {
				Swal.fire({
					icon: 'error',
					title: 'Error',
					text: response.message
				});
				$('#btnProcesarVenta').prop('disabled', false).html('<i class="bi bi-check-circle me-2"></i>Procesar Venta');
			}
		},
		error: function(xhr) {
			let msg = 'Ocurrió un error al procesar la venta';
			if (xhr.responseJSON && xhr.responseJSON.message) {
				msg = xhr.responseJSON.message;
			}
			Swal.fire({
				icon: 'error',
				title: 'Error',
				text: msg
			});
			$('#btnProcesarVenta').prop('disabled', false).html('<i class="bi bi-check-circle me-2"></i>Procesar Venta');
		}
	});
}

function abrirBuscadorAvanzado() {
	// Cargar todos los productos disponibles
	$.post(IP_SERVER + 'ventas/buscarProductos', { termino: '' }, function(response) {
		// Por ahora mostramos un mensaje
		Swal.fire({
			icon: 'info',
			title: 'Catálogo',
			text: 'Usa el buscador para encontrar productos'
		});
	});
}
</script>
