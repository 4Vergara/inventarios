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
	<link rel="stylesheet" href="<?php echo IP_SERVER . 'assets/css/main.css'; ?>">	
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
	<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
	<link rel="stylesheet" href="<?php echo IP_SERVER . 'assets/datatables/datatables.min.css'; ?>">
	<script src="<?php echo IP_SERVER . 'assets/datatables/datatables.min.js'; ?>"></script>
	<script>
		var IP_SERVER = '<?php echo IP_SERVER ?>';
	</script>
	<script src="<?php echo IP_SERVER . 'assets/js/main.js'; ?>"></script>
	<style>
		/* Header personalizado */
		.header-navbar {
			background: linear-gradient(135deg, var(--color_principal-500) 0%, var(--color_principal-600) 100%);
			box-shadow: 0 2px 10px rgba(0, 0, 0, 0.15);
		}
		.header-navbar .navbar-brand {
			color: #fff;
			font-weight: 600;
			font-size: 1.3rem;
		}
		.header-navbar .navbar-brand:hover {
			color: rgba(255, 255, 255, 0.9);
		}
		.header-navbar .nav-link {
			color: rgba(255, 255, 255, 0.9) !important;
			transition: all 0.3s ease;
		}
		.header-navbar .nav-link:hover {
			color: #fff !important;
			background: rgba(255, 255, 255, 0.1);
			border-radius: 5px;
		}
		.user-badge {
			background: rgba(255, 255, 255, 0.2);
			border-radius: 25px;
			padding: 5px 15px;
			color: #fff;
			font-size: 0.9rem;
			display: flex;
			align-items: center;
			gap: 8px;
		}
		.user-badge .status-dot {
			width: 10px;
			height: 10px;
			background: #4ade80;
			border-radius: 50%;
			animation: pulse 2s infinite;
		}
		@keyframes pulse {
			0%, 100% { opacity: 1; }
			50% { opacity: 0.5; }
		}
		.btn-logout {
			background: rgba(255, 255, 255, 0.15);
			border: 1px solid rgba(255, 255, 255, 0.3);
			color: #fff;
			border-radius: 20px;
			padding: 5px 15px;
			transition: all 0.3s ease;
		}
		.btn-logout:hover {
			background: rgba(255, 255, 255, 0.25);
			color: #fff;
			border-color: rgba(255, 255, 255, 0.5);
		}
		.logo-header {
			height: 40px;
			width: auto;
		}
		.main-content {
			min-height: calc(100vh - 140px);
			padding-top: 20px;
			padding-bottom: 20px;
			background-color: #f8f9fa;
		}
		.dropdown-menu {
			border: none;
			box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
			border-radius: 10px;
		}
		.dropdown-item:hover {
			background-color: var(--color_principal-50);
			color: var(--color_principal-600);
		}
		.dropdown-item i {
			width: 20px;
		}
	</style>
</head>
<body class="bg-light">
	<!-- Header / Navbar -->
	<nav class="navbar navbar-expand-lg header-navbar sticky-top">
		<div class="container-fluid px-4">
			<!-- Logo y nombre -->
			<a class="navbar-brand d-flex align-items-center gap-2" href="<?php echo IP_SERVER; ?>">
				<img src="<?php echo IP_SERVER . 'assets/imagen/icon_solo.png'; ?>" alt="Logo" class="logo-header">
				<span>Saho</span>
			</a>

			<!-- Botón hamburguesa para móvil -->
			<button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
				<i class="bi bi-list text-white fs-4"></i>
			</button>

			<!-- Contenido del navbar -->
			<div class="collapse navbar-collapse" id="navbarContent">
				<!-- Menú de navegación -->
				<ul class="navbar-nav me-auto mb-2 mb-lg-0">
					<li class="nav-item">
						<a class="nav-link" href="<?php echo IP_SERVER; ?>">
							<i class="bi bi-house-door me-1"></i> Inicio
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="<?php echo IP_SERVER . 'productos'; ?>">
							<i class="bi bi-box-seam me-1"></i> Productos
						</a>
					</li>
				</ul>

				<!-- Usuario activo y opciones -->
				<div class="d-flex align-items-center gap-3">
					<?php if (isset($this->session->datosusuario) && $this->session->datosusuario): ?>
						<!-- Badge de usuario activo -->
						<div class="user-badge d-none d-md-flex">
							<span class="status-dot"></span>
							<span>Activo</span>
						</div>

						<!-- Dropdown de usuario -->
						<div class="dropdown">
							<button class="btn btn-logout dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
								<i class="bi bi-person-circle me-1"></i>
								<?php echo isset($this->session->datosusuario->nombre_completo) ? $this->session->datosusuario->nombre_completo : 'Usuario'; ?>
							</button>
							<ul class="dropdown-menu dropdown-menu-end">
								<li>
									<span class="dropdown-item-text text-muted small">
										<i class="bi bi-envelope me-2"></i>
										<?php echo isset($this->session->datosusuario->correo) ? $this->session->datosusuario->correo : ''; ?>
									</span>
								</li>
								<li><hr class="dropdown-divider"></li>
								<li>
									<a class="dropdown-item" href="#">
										<i class="bi bi-person me-2"></i> Mi Perfil
									</a>
								</li>
								<li>
									<a class="dropdown-item" href="#">
										<i class="bi bi-gear me-2"></i> Configuración
									</a>
								</li>
								<li><hr class="dropdown-divider"></li>
								<li>
									<a class="dropdown-item text-danger" href="<?php echo IP_SERVER . 'login/salir'; ?>">
										<i class="bi bi-box-arrow-right me-2"></i> Cerrar Sesión
									</a>
								</li>
							</ul>
						</div>
					<?php else: ?>
						<!-- Botón de iniciar sesión -->
						<a href="<?php echo IP_SERVER . 'login'; ?>" class="btn btn-logout">
							<i class="bi bi-box-arrow-in-right me-1"></i> Iniciar Sesión
						</a>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</nav>

	<!-- Contenido principal -->
	<main class="main-content">
		<div class="container-fluid px-4">