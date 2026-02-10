<!-- Page Header -->
<style>
/* Cliente Cell */
.cliente-cell {
	display: flex;
	align-items: center;
	gap: 12px;
}
.cliente-avatar {
	width: 40px;
	height: 40px;
	background: linear-gradient(135deg, var(--color_principal-500), var(--color_principal-600));
	border-radius: 10px;
	display: flex;
	align-items: center;
	justify-content: center;
	color: white;
	font-size: 1.1rem;
}
.cliente-info {
	display: flex;
	flex-direction: column;
}
.cliente-nombre {
	font-weight: 600;
	color: #1f2937;
}
.cliente-fecha {
	font-size: 0.75rem;
	color: #9ca3af;
}

/* Documento Badge */
.documento-badge {
	font-family: 'Courier New', monospace;
	font-weight: 700;
	color: var(--color_principal-600);
	background: var(--color_principal-50);
	padding: 6px 12px;
	border-radius: 8px;
	font-size: 0.85rem;
}

/* Badge compras */
.badge-compras {
	display: inline-block;
	background: rgba(99, 102, 241, 0.1);
	color: #6366f1;
	font-weight: 600;
	padding: 6px 12px;
	border-radius: 8px;
	font-size: 0.85rem;
}
.badge-sin-compras {
	background: #f3f4f6;
	color: #9ca3af;
}

/* Monto Total */
.monto-total {
	font-weight: 600;
	color: #059669;
}

/* Action Buttons */
.action-buttons {
	display: flex;
	gap: 4px;
	justify-content: center;
}
.action-btn {
	width: 32px;
	height: 32px;
	padding: 0;
	display: inline-flex;
	align-items: center;
	justify-content: center;
	border-radius: 8px;
	border: 1px solid #e5e7eb;
	background: white;
	color: #6b7280;
	transition: all 0.2s;
	text-decoration: none;
}
.action-btn:hover {
	background: #f3f4f6;
	color: #1f2937;
}
.action-btn.delete:hover {
	background: rgba(239, 68, 68, 0.1);
	color: #ef4444;
	border-color: #ef4444;
}
</style>
<div class="page-header mb-4">
	<div class="row align-items-center">
		<div class="col-lg-8">
			<h1 class="page-title">Gestión de Clientes</h1>
			<p class="page-subtitle">Administra la información y el historial de compras de tus clientes.</p>
		</div>
		<div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
			<button class="btn btn-color_principal" onclick="abrirModalCliente()">
				<i class="bi bi-plus-lg me-1"></i>Nuevo Cliente
			</button>
		</div>
	</div>
</div>

<!-- Stats Cards -->
<div class="row g-4 mb-4">
	<div class="col-sm-6 col-xl-3">
		<div class="stat-card">
			<div class="stat-content">
				<span class="stat-label">TOTAL CLIENTES</span>
				<div class="stat-value-row">
					<span class="stat-value" id="totalClientes">0</span>
				</div>
			</div>
		</div>
	</div>
	<div class="col-sm-6 col-xl-3">
		<div class="stat-card">
			<div class="stat-content">
				<span class="stat-label">CON COMPRAS</span>
				<div class="stat-value-row">
					<span class="stat-value" id="clientesConCompras">0</span>
					<span class="stat-badge stat-badge-success"><i class="bi bi-cart-check"></i></span>
				</div>
			</div>
		</div>
	</div>
	<div class="col-sm-6 col-xl-3">
		<div class="stat-card">
			<div class="stat-content">
				<span class="stat-label">NUEVOS ESTE MES</span>
				<div class="stat-value-row">
					<span class="stat-value" id="clientesNuevos">0</span>
				</div>
			</div>
		</div>
	</div>
	<div class="col-sm-6 col-xl-3">
		<div class="stat-card">
			<div class="stat-content">
				<span class="stat-label">TOTAL EN VENTAS</span>
				<div class="stat-value-row">
					<span class="stat-value" id="totalVentas">$0</span>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Tabla de Clientes -->
<div class="card table-card">
	<div class="card-header bg-white border-bottom">
		<div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
			<h5 class="mb-0"><i class="bi bi-people me-2"></i>Listado de Clientes</h5>
			<button class="btn btn-sm btn-outline-secondary" onclick="recargarTabla()">
				<i class="bi bi-arrow-clockwise me-1"></i>Actualizar
			</button>
		</div>
	</div>
	
	<div class="card-body p-0">
		<div class="table-responsive p-3">
			<table id="tablaClientes" class="table table-modern align-middle mb-0">
				<thead>
					<tr>
						<th style="width: 280px;">CLIENTE</th>
						<th>DOCUMENTO</th>
						<th>CORREO</th>
						<th class="text-center">COMPRAS</th>
						<th class="text-end">MONTO TOTAL</th>
						<th>ÚLTIMA COMPRA</th>
						<th class="text-center" style="width: 120px;">ACCIONES</th>
					</tr>
				</thead>
				<tbody>
					<!-- Los datos se cargan por AJAX -->
				</tbody>
			</table>
		</div>
	</div>
</div>

<!-- Modal Cliente -->
<div class="modal fade" id="modalCliente" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="modalClienteTitulo">
					<i class="bi bi-person-plus me-2"></i>Nuevo Cliente
				</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
			</div>
			<div class="modal-body">
				<form id="formCliente">
					<input type="hidden" id="clienteId" name="id">
					
					<div class="mb-3">
						<label class="form-label fw-semibold">Nombre Completo <span class="text-danger">*</span></label>
						<input type="text" class="form-control" id="nombreCompleto" name="nombre_completo" 
							placeholder="Ingrese el nombre completo" required maxlength="200">
					</div>
					
					<div class="mb-3">
						<label class="form-label fw-semibold">Número de Documento <span class="text-danger">*</span></label>
						<input type="text" class="form-control" id="numeroDocumento" name="numero_documento" 
							placeholder="Ingrese el número de documento" required maxlength="30">
					</div>
					
					<div class="mb-3">
						<label class="form-label fw-semibold">Correo Electrónico</label>
						<input type="email" class="form-control" id="correoElectronico" name="correo_electronico" 
							placeholder="ejemplo@correo.com" maxlength="100">
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
				<button type="button" class="btn btn-color_principal" onclick="guardarCliente()">
					<i class="bi bi-check-lg me-1"></i>Guardar
				</button>
			</div>
		</div>
	</div>
</div>

<!-- Modal de confirmación para eliminar -->
<div class="modal fade" id="modalEliminar" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content border-0 shadow">
			<div class="modal-header border-0 pb-0">
				<h5 class="modal-title text-danger">
					<i class="bi bi-exclamation-triangle me-2"></i>Confirmar Eliminación
				</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<p>¿Estás seguro de que deseas eliminar al cliente <strong id="nombreClienteEliminar"></strong>?</p>
				<p class="text-muted small mb-0">Esta acción no se puede deshacer.</p>
				<input type="hidden" id="idClienteEliminar">
			</div>
			<div class="modal-footer border-0 pt-0">
				<button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
				<button type="button" class="btn btn-danger" onclick="confirmarEliminar()">
					<i class="bi bi-trash me-1"></i>Eliminar
				</button>
			</div>
		</div>
	</div>
</div>

<script>
let tablaClientes;
let clientesData = [];
let modalCliente;

$(document).ready(function() {
	modalCliente = new bootstrap.Modal(document.getElementById('modalCliente'));
	inicializarTabla();
});

function inicializarTabla() {
	tablaClientes = $('#tablaClientes').DataTable({
		ajax: {
			url: IP_SERVER + 'clientes/listar',
			type: 'POST',
			dataSrc: function(json) {
				clientesData = json.data;
				actualizarEstadisticas(json.data);
				return json.data;
			}
		},
		columns: [
			{ 
				data: null,
				render: function(data, type, row) {
					let fechaRegistro = row.fec_creacion ? new Date(row.fec_creacion).toLocaleDateString('es-ES') : '-';
					return `
						<div class="cliente-cell">
							<div class="cliente-avatar">
								<i class="bi bi-person-fill"></i>
							</div>
							<div class="cliente-info">
								<span class="cliente-nombre">${row.nombre_completo}</span>
								<span class="cliente-fecha">Registrado: ${fechaRegistro}</span>
							</div>
						</div>
					`;
				}
			},
			{ 
				data: 'numero_documento',
				render: function(data) {
					return `<code class="documento-badge">${data}</code>`;
				}
			},
			{ 
				data: 'correo_electronico',
				render: function(data) {
					return data ? `<span>${data}</span>` : '<span class="text-muted">-</span>';
				}
			},
			{ 
				data: 'total_compras',
				className: 'text-center',
				render: function(data) {
					let badgeClass = parseInt(data) > 0 ? 'badge-compras' : 'badge-compras badge-sin-compras';
					return `<span class="${badgeClass}">${data}</span>`;
				}
			},
			{ 
				data: 'monto_total',
				className: 'text-end',
				render: function(data) {
					return `<span class="monto-total">$${parseFloat(data).toLocaleString('es-MX', {minimumFractionDigits: 2})}</span>`;
				}
			},
			{ 
				data: 'ultima_compra',
				render: function(data) {
					if (!data) return '<span class="text-muted">Sin compras</span>';
					return new Date(data).toLocaleDateString('es-ES');
				}
			},
			{ 
				data: 'id',
				className: 'text-center',
				orderable: false,
				render: function(data, type, row) {
					return `
						<div class="action-buttons">
							<a href="${IP_SERVER}clientes/ver/${data}" class="action-btn" title="Ver historial">
								<i class="bi bi-eye"></i>
							</a>
							<button type="button" class="action-btn" title="Editar" onclick="editarCliente(${data})">
								<i class="bi bi-pencil"></i>
							</button>
							<button type="button" class="action-btn delete" title="Eliminar" onclick="prepararEliminar(${data}, '${row.nombre_completo.replace(/'/g, "\\'")}')">
								<i class="bi bi-trash"></i>
							</button>
						</div>
					`;
				}
			}
		],
		language: TABLA_CONFIGURACION.language,
		order: [[0, 'asc']],
		responsive: true,
		pageLength: 10,
		lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Todos"]],
		dom: '<"row mb-3"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row mt-3"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>'
	});
}

function actualizarEstadisticas(data) {
	let totalClientes = data.length;
	let clientesConCompras = data.filter(c => parseInt(c.total_compras) > 0).length;
	let totalVentas = data.reduce((sum, c) => sum + parseFloat(c.monto_total || 0), 0);
	
	// Clientes nuevos este mes
	let inicioMes = new Date();
	inicioMes.setDate(1);
	inicioMes.setHours(0, 0, 0, 0);
	let clientesNuevos = data.filter(c => new Date(c.fec_creacion) >= inicioMes).length;
	
	$('#totalClientes').text(totalClientes.toLocaleString());
	$('#clientesConCompras').text(clientesConCompras.toLocaleString());
	$('#clientesNuevos').text(clientesNuevos.toLocaleString());
	$('#totalVentas').text('$' + totalVentas.toLocaleString('es-MX', {minimumFractionDigits: 2}));
}

function recargarTabla() {
	tablaClientes.ajax.reload(null, false);
}

function abrirModalCliente() {
	$('#formCliente')[0].reset();
	$('#clienteId').val('');
	$('#modalClienteTitulo').html('<i class="bi bi-person-plus me-2"></i>Nuevo Cliente');
	modalCliente.show();
}

function editarCliente(id) {
	$.ajax({
		url: IP_SERVER + 'clientes/obtener/' + id,
		type: 'GET',
		dataType: 'json',
		success: function(response) {
			if (response.success) {
				let c = response.data;
				$('#clienteId').val(c.id);
				$('#nombreCompleto').val(c.nombre_completo);
				$('#numeroDocumento').val(c.numero_documento);
				$('#correoElectronico').val(c.correo_electronico || '');
				$('#modalClienteTitulo').html('<i class="bi bi-pencil me-2"></i>Editar Cliente');
				modalCliente.show();
			} else {
				Swal.fire({
					icon: 'error',
					title: 'Error',
					text: response.message
				});
			}
		},
		error: function(xhr) {
			let mensaje = 'No se pudo obtener los datos del cliente';
			if (xhr.responseJSON && xhr.responseJSON.message) {
				mensaje = xhr.responseJSON.message;
			}
			Swal.fire({
				icon: 'error',
				title: 'Error',
				text: mensaje
			});
		}
	});
}

function guardarCliente() {
	let nombre = $('#nombreCompleto').val().trim();
	let documento = $('#numeroDocumento').val().trim();
	
	if (!nombre || !documento) {
		Swal.fire({
			icon: 'warning',
			title: 'Atención',
			text: 'El nombre y el número de documento son requeridos'
		});
		return;
	}
	
	let datos = {
		id: $('#clienteId').val(),
		nombre_completo: nombre,
		numero_documento: documento,
		correo_electronico: $('#correoElectronico').val().trim()
	};
	
	$.ajax({
		url: IP_SERVER + 'clientes/guardar',
		type: 'POST',
		data: datos,
		dataType: 'json',
		success: function(response) {
			if (response.success) {
				modalCliente.hide();
				Swal.fire({
					icon: 'success',
					title: '¡Guardado!',
					text: response.message,
					timer: 2000,
					showConfirmButton: false
				});
				recargarTabla();
			} else {
				Swal.fire({
					icon: 'error',
					title: 'Error',
					text: response.message
				});
			}
		},
		error: function(xhr) {
			let mensaje = 'Ocurrió un error al guardar el cliente';
			if (xhr.responseJSON && xhr.responseJSON.message) {
				mensaje = xhr.responseJSON.message;
			}
			Swal.fire({
				icon: 'error',
				title: 'Error',
				text: mensaje
			});
		}
	});
}

function prepararEliminar(id, nombre) {
	$('#idClienteEliminar').val(id);
	$('#nombreClienteEliminar').text(nombre);
	new bootstrap.Modal(document.getElementById('modalEliminar')).show();
}

function confirmarEliminar() {
	let id = $('#idClienteEliminar').val();
	$.ajax({
		url: IP_SERVER + 'clientes/eliminar/' + id,
		type: 'POST',
		dataType: 'json',
		success: function(response) {
			bootstrap.Modal.getInstance(document.getElementById('modalEliminar')).hide();
			if (response.success) {
				Swal.fire({
					icon: 'success',
					title: '¡Eliminado!',
					text: response.message,
					timer: 2000,
					showConfirmButton: false
				});
				recargarTabla();
			} else {
				Swal.fire({
					icon: 'error',
					title: 'Error',
					text: response.message
				});
			}
		},
		error: function(xhr) {
			let mensaje = 'Ocurrió un error al procesar la solicitud';
			if (xhr.responseJSON && xhr.responseJSON.message) {
				mensaje = xhr.responseJSON.message;
			}
			Swal.fire({
				icon: 'error',
				title: 'Error',
				text: mensaje
			});
		}
	});
}
</script>

