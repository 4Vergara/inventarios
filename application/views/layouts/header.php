<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="utf-8">
	<title>Saho - Sistema de Inventarios</title>
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
	<meta http-equiv="cache-control" content="max-age=0" />
	<meta http-equiv="pragma" content="no-cache" />
	<meta http-equiv="expires" content="0" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="DC.language" content="ES" />
	<link rel="icon" type="image/png" href="<?php echo IP_SERVER . 'assets/imagen/icon_solo.png'; ?>" />
	<!-- Bootstrap primero -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
	<link rel="stylesheet" href="<?php echo IP_SERVER . 'assets/datatables/datatables.min.css'; ?>">
	<!-- Main CSS después para sobrescribir -->
	<link rel="stylesheet" href="<?php echo IP_SERVER . 'assets/css/main.css'; ?>">
	<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
	<script src="<?php echo IP_SERVER . 'assets/datatables/datatables.min.js'; ?>"></script>
	<script>
		var IP_SERVER = '<?php echo IP_SERVER ?>';
	</script>
	<script src="<?php echo IP_SERVER . 'assets/js/main.js'; ?>"></script>
</head>
<body class="admin-layout<?php echo (!isset($this->session->datosusuario) || !$this->session->datosusuario) ? ' no-sidebar' : ''; ?>">
	<?php if (isset($this->session->datosusuario) && $this->session->datosusuario): ?>
	<!-- Sidebar -->
	<aside class="sidebar" id="sidebar">
		<!-- Logo -->
		<div class="sidebar-header">
			<div class="logo-container">
				<img src="<?php echo IP_SERVER . 'assets/imagen/icon_solo.png'; ?>" alt="Logo" class="sidebar-logo">
				<span class="sidebar-brand">Saho</span>
			</div>
			<button class="sidebar-toggle-btn d-lg-none" id="sidebarClose">
				<i class="bi bi-x-lg"></i>
			</button>
		</div>

		<!-- Menu Principal -->
		<nav class="sidebar-nav">
			<div class="nav-section">
				<span class="nav-section-title">MENÚ PRINCIPAL</span>
				<ul class="nav-list">
					<li class="nav-item">
						<a href="<?php echo IP_SERVER; ?>" class="nav-link <?php echo ($this->uri->segment(1) == '' || $this->uri->segment(1) == 'home') ? 'active' : ''; ?>">
							<i class="bi bi-grid-1x2-fill"></i>
							<span class="nav-text">Dashboard</span>
						</a>
					</li>
					<li class="nav-item">
						<a href="<?php echo IP_SERVER . 'productos'; ?>" class="nav-link <?php echo ($this->uri->segment(1) == 'productos') ? 'active' : ''; ?>">
							<i class="bi bi-box-seam-fill"></i>
							<span class="nav-text">Inventario</span>
						</a>
					</li>
					<li class="nav-item">
						<a href="<?php echo IP_SERVER . 'ventas'; ?>" class="nav-link <?php echo ($this->uri->segment(1) == 'ventas') ? 'active' : ''; ?>">
							<i class="bi bi-cart-fill"></i>
							<span class="nav-text">Pedidos</span>
						</a>
					</li>
					<li class="nav-item">
						<a href="<?php echo IP_SERVER . 'clientes'; ?>" class="nav-link <?php echo ($this->uri->segment(1) == 'clientes') ? 'active' : ''; ?>">
							<i class="bi bi-people-fill"></i>
							<span class="nav-text">Clientes</span>
						</a>
					</li>
				</ul>
			</div>

			<div class="nav-section">
				<span class="nav-section-title">OPERACIONES</span>
				<ul class="nav-list">
					<li class="nav-item">
						<a href="<?php echo IP_SERVER . 'facturacion'; ?>" class="nav-link <?php echo ($this->uri->segment(1) == 'facturacion') ? 'active' : ''; ?>">
							<i class="bi bi-receipt"></i>
							<span class="nav-text">Facturación</span>
						</a>
					</li>
					<li class="nav-item">
						<a href="<?php echo IP_SERVER . 'cierre_caja'; ?>" class="nav-link <?php echo ($this->uri->segment(1) == 'cierre_caja') ? 'active' : ''; ?>">
							<i class="bi bi-cash-stack"></i>
							<span class="nav-text">Cierre de Caja</span>
						</a>
					</li>
					<li class="nav-item">
						<a href="#" class="nav-link">
							<i class="bi bi-graph-up"></i>
							<span class="nav-text">Reportes</span>
						</a>
					</li>
				</ul>
			</div>
		</nav>

		<!-- Botón Nuevo Producto -->
		<div class="sidebar-footer">
			<a href="<?php echo IP_SERVER . 'productos/crear'; ?>" class="btn-new-product">
				<i class="bi bi-plus-lg"></i>
				<span class="nav-text">Nuevo Producto</span>
			</a>
			<a href="#" class="nav-link settings-link">
				<i class="bi bi-gear-fill"></i>
				<span class="nav-text">Configuración</span>
			</a>
		</div>
	</aside>

	<!-- Overlay para móvil -->
	<div class="sidebar-overlay" id="sidebarOverlay"></div>
	<?php endif; ?>

	<!-- Contenedor Principal -->
	<div class="main-wrapper" id="mainWrapper">
		<!-- Top Header -->
		<header class="top-header">
			<div class="header-left">
				<button class="sidebar-toggle-btn" id="sidebarToggle" title="Contraer/Expandir menú">
					<i class="bi bi-list"></i>
				</button>
			</div>

			<div class="header-right">

				<!-- Usuario -->
				<?php if (isset($this->session->datosusuario) && $this->session->datosusuario): ?>
				<div class="dropdown user-dropdown">
					<button class="user-btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
						<div class="user-info d-none d-md-block">
							<span class="user-name"><?php echo isset($this->session->datosusuario->nombre_completo) ? $this->session->datosusuario->nombre_completo : 'Usuario'; ?></span>
							<span class="user-role"><?php echo isset($this->session->datosusuario->rol) ? $this->session->datosusuario->rol : ''; ?></span>
						</div>
						<div class="user-avatar">
							<i class="bi bi-person-fill"></i>
						</div>
					</button>
					<ul class="dropdown-menu dropdown-menu-end">
						<li>
							<div class="dropdown-header">
								<strong><?php echo isset($this->session->datosusuario->nombre_completo) ? $this->session->datosusuario->nombre_completo : 'Usuario'; ?></strong>
								<small class="text-muted d-block"><?php echo isset($this->session->datosusuario->correo) ? $this->session->datosusuario->correo : ''; ?></small>
							</div>
						</li>
						<li><hr class="dropdown-divider"></li>
						<li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>Mi Perfil</a></li>
						<li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i>Configuración</a></li>
						<li><hr class="dropdown-divider"></li>
						<li><a class="dropdown-item text-danger" href="<?php echo IP_SERVER . 'login/salir'; ?>"><i class="bi bi-box-arrow-right me-2"></i>Cerrar Sesión</a></li>
					</ul>
				</div>
				<?php else: ?>
				<a href="<?php echo IP_SERVER . 'login'; ?>" class="btn btn-sm btn-color_principal">
					<i class="bi bi-box-arrow-in-right me-1"></i> Iniciar Sesión
				</a>
				<?php endif; ?>
			</div>
		</header>

		<!-- Contenido Principal -->
		<main class="main-content">
			<div class="content-wrapper">