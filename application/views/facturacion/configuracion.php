<!-- Page Header -->
<div class="page-header mb-4">
	<div class="row align-items-center">
		<div class="col">
			<nav aria-label="breadcrumb">
				<ol class="breadcrumb mb-2">
					<li class="breadcrumb-item"><a href="<?php echo IP_SERVER; ?>">Inicio</a></li>
					<li class="breadcrumb-item"><a href="<?php echo IP_SERVER . 'facturacion'; ?>">Facturación</a></li>
					<li class="breadcrumb-item active">Configuración del Emisor</li>
				</ol>
			</nav>
			<h1 class="page-title">Configuración del Emisor</h1>
			<p class="page-subtitle">Datos de la empresa para la facturación electrónica (Resolución DIAN)</p>
		</div>
		<div class="col-auto">
			<a href="<?php echo IP_SERVER . 'facturacion'; ?>" class="btn btn-light">
				<i class="bi bi-arrow-left me-1"></i>Volver
			</a>
		</div>
	</div>
</div>

<div class="row justify-content-center">
	<div class="col-lg-10">
		<form id="formEmisor" onsubmit="guardarConfiguracion(event)">
			<!-- Datos de la Empresa -->
			<div class="card mb-4">
				<div class="card-header bg-white">
					<h5 class="mb-0"><i class="bi bi-building me-2"></i>Datos de la Empresa</h5>
				</div>
				<div class="card-body">
					<div class="row g-3">
						<div class="col-md-8">
							<label class="form-label fw-semibold">Razón Social <span class="text-danger">*</span></label>
							<input type="text" class="form-control" id="razonSocial" value="<?php echo $emisor->razon_social ?? ''; ?>" required>
						</div>
						<div class="col-md-4">
							<label class="form-label fw-semibold">NIT <span class="text-danger">*</span></label>
							<input type="text" class="form-control" id="nit" value="<?php echo $emisor->nit ?? ''; ?>" placeholder="900000000-0" required>
						</div>
						<div class="col-md-6">
							<label class="form-label fw-semibold">Dirección <span class="text-danger">*</span></label>
							<input type="text" class="form-control" id="direccion" value="<?php echo $emisor->direccion ?? ''; ?>" required>
						</div>
						<div class="col-md-3">
							<label class="form-label fw-semibold">Ciudad <span class="text-danger">*</span></label>
							<input type="text" class="form-control" id="ciudad" value="<?php echo $emisor->ciudad ?? ''; ?>" required>
						</div>
						<div class="col-md-3">
							<label class="form-label fw-semibold">Departamento <span class="text-danger">*</span></label>
							<input type="text" class="form-control" id="departamento" value="<?php echo $emisor->departamento ?? ''; ?>" required>
						</div>
						<div class="col-md-4">
							<label class="form-label fw-semibold">Teléfono</label>
							<input type="text" class="form-control" id="telefono" value="<?php echo $emisor->telefono ?? ''; ?>">
						</div>
						<div class="col-md-4">
							<label class="form-label fw-semibold">Correo</label>
							<input type="email" class="form-control" id="correo" value="<?php echo $emisor->correo ?? ''; ?>">
						</div>
						<div class="col-md-4">
							<label class="form-label fw-semibold">Régimen <span class="text-danger">*</span></label>
							<select class="form-select" id="regimen" required>
								<option value="Responsable de IVA" <?php echo (isset($emisor->regimen) && $emisor->regimen == 'Responsable de IVA') ? 'selected' : ''; ?>>Responsable de IVA</option>
								<option value="No responsable de IVA" <?php echo (isset($emisor->regimen) && $emisor->regimen == 'No responsable de IVA') ? 'selected' : ''; ?>>No responsable de IVA</option>
							</select>
						</div>
						<div class="col-md-4">
							<label class="form-label fw-semibold">Actividad Económica (CIIU)</label>
							<input type="text" class="form-control" id="actividadEconomica" value="<?php echo $emisor->actividad_economica ?? ''; ?>" placeholder="Ej: 4791">
						</div>
					</div>
				</div>
			</div>
			
			<!-- Resolución DIAN -->
			<div class="card mb-4">
				<div class="card-header bg-white">
					<h5 class="mb-0"><i class="bi bi-file-earmark-text me-2"></i>Resolución de Facturación DIAN</h5>
				</div>
				<div class="card-body">
					<div class="alert alert-info mb-3">
						<i class="bi bi-info-circle me-1"></i>
						Ingrese los datos de la resolución de facturación autorizada por la DIAN. Esta numeración será usada para las facturas electrónicas.
					</div>
					<div class="row g-3">
						<div class="col-md-6">
							<label class="form-label fw-semibold">Número de Resolución</label>
							<input type="text" class="form-control" id="resolucionDian" value="<?php echo $emisor->resolucion_dian ?? ''; ?>" placeholder="Resolución No. 18764000000000">
						</div>
						<div class="col-md-3">
							<label class="form-label fw-semibold">Fecha Resolución</label>
							<input type="date" class="form-control" id="fechaResolucion" value="<?php echo $emisor->fecha_resolucion ?? ''; ?>">
						</div>
						<div class="col-md-3">
							<label class="form-label fw-semibold">Prefijo Factura</label>
							<input type="text" class="form-control" id="prefijoFactura" value="<?php echo $emisor->prefijo_factura ?? 'FAC'; ?>" maxlength="10">
						</div>
						<div class="col-md-3">
							<label class="form-label fw-semibold">Rango Desde</label>
							<input type="number" class="form-control" id="rangoDesde" value="<?php echo $emisor->rango_desde ?? 1; ?>" min="1">
						</div>
						<div class="col-md-3">
							<label class="form-label fw-semibold">Rango Hasta</label>
							<input type="number" class="form-control" id="rangoHasta" value="<?php echo $emisor->rango_hasta ?? 99999; ?>" min="1">
						</div>
						<div class="col-md-3">
							<label class="form-label fw-semibold">Consecutivo Actual</label>
							<input type="number" class="form-control" value="<?php echo $emisor->consecutivo_actual ?? 0; ?>" disabled>
							<small class="text-muted">Se actualiza automáticamente</small>
						</div>
					</div>
				</div>
			</div>
			
			<div class="d-flex justify-content-end gap-3">
				<a href="<?php echo IP_SERVER . 'facturacion'; ?>" class="btn btn-light btn-lg">Cancelar</a>
				<button type="submit" class="btn btn-color_principal btn-lg" id="btnGuardar">
					<i class="bi bi-check-lg me-1"></i>Guardar Configuración
				</button>
			</div>
		</form>
	</div>
</div>

<script>
function guardarConfiguracion(e) {
	e.preventDefault();
	
	$('#btnGuardar').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Guardando...');
	
	$.post(IP_SERVER + 'facturacion/guardarConfiguracion', {
		razon_social: $('#razonSocial').val(),
		nit: $('#nit').val(),
		direccion: $('#direccion').val(),
		ciudad: $('#ciudad').val(),
		departamento: $('#departamento').val(),
		telefono: $('#telefono').val(),
		correo: $('#correo').val(),
		regimen: $('#regimen').val(),
		actividad_economica: $('#actividadEconomica').val(),
		resolucion_dian: $('#resolucionDian').val(),
		fecha_resolucion: $('#fechaResolucion').val(),
		prefijo_factura: $('#prefijoFactura').val(),
		rango_desde: $('#rangoDesde').val(),
		rango_hasta: $('#rangoHasta').val()
	}, function(res) {
		if (res.success) {
			Swal.fire('Guardado', res.message, 'success');
		} else {
			Swal.fire('Error', res.message, 'error');
		}
		$('#btnGuardar').prop('disabled', false).html('<i class="bi bi-check-lg me-1"></i>Guardar Configuración');
	}).fail(function(xhr) {
		let msg = xhr.responseJSON ? xhr.responseJSON.message : 'Error al guardar';
		Swal.fire('Error', msg, 'error');
		$('#btnGuardar').prop('disabled', false).html('<i class="bi bi-check-lg me-1"></i>Guardar Configuración');
	});
}
</script>
