<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="utf-8">
	<title>BASE</title>
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
	<meta http-equiv="cache-control" content="max-age=0" />
	<meta http-equiv="pragma" content="no-cache" />
	<meta http-equiv="expires" content="0" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="copyright" content="CCS" />
	<meta name="revisit" content="5 days" />
	<meta name="Author" content="gffabio" />
	<meta name="medium" content="medium_type" />
	<meta name="Author Email" content="fabio.grandas@ccs.org.co" />
	<meta name="DC.creator" content="gffabio" />
	<meta name="DC.date" content="2020-04-22 010:00:00 AM" />
	<meta name="DC.language" content="ES" />
	<link rel="icon" type="image/png" href="<?php echo IP_SERVER ?>icon.png">
	<link rel="shortcut icon" href="<?php echo IP_SERVER ?>favicon.ico" title="CCS" id="CCS" type="image/x-icon" />
	<link href="<?php echo IP_SERVER ?>assets/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
	<link href="<?php echo IP_SERVER ?>assets/fontawesome/css/all.min.css" rel="stylesheet" type="text/css">
	<link href="<?php echo IP_SERVER ?>assets/sweetalert2/sweetalert2.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo IP_SERVER ?>assets/css/main.css?<?php echo rand() ?>" rel="stylesheet" type="text/css" />
	<script src="<?php echo IP_SERVER ?>assets/jquery/jquery.min.js"></script>
	<script src="<?php echo IP_SERVER ?>assets/bootstrap/js/bootstrap.bundle.min.js"></script>
	<script>
		var IP_SERVER = '<?php echo IP_SERVER ?>';
	</script>
</head>
<body>
	<style>
		img {
			border: 0;
			vertical-align: top;
			max-width: 100%;
			height: auto;
		}
	</style>
	<div id="divpreload">
		<div class="spinner-border text-success" role="status">
		</div>
		<span>Cargando...</span>
	</div>
	<div class="login-wrap d-flex align-items-center flex-wrap justify-content-center">
		<div class="container">
			<div class="row align-items-center">
				<div class="col-md-6 col-lg-7">
					<img src="<?php echo IP_SERVER ?>assets/imagen/logo_grande.png" alt="">
				</div>
				<div class="col-md-6 col-lg-5">
					<div class="login-box bg-gradient box-shadow border-radius-10">
						<form action="#" method="POST" novalidate>
							<div class="p-2">
								<div class="text-center">
									<img src="<?php echo IP_SERVER ?>assets/imagen/test.png" alt="">
								</div>
							</div>
							<div class="input-group custom pb-10">
								<input type="text" class="form-control form-control-lg" id="usuario" name="usuario" value="" placeholder="Usuario" required>
								<div class="input-group-append custom">
									<span class="input-group-text"><i class="fas fa-user"></i></span>
								</div>
							</div>
							<div class="input-group custom pb-10">
								<input id="password" type="password" class="form-control form-control-lg" name="password" value="" placeholder="Clave de acceso" required>
								<div class="input-group-append custom">
									<span class="input-group-text"><i id="verclave" class="fa fa-eye cursor"></i></span>
								</div>
							</div>
							<div class="row pb-20">
								<div class="col-6">
									<div class="custom-checkbox">
										<input type="checkbox" class="custom-control-input" id="recordar" name="recordar">
										<label class="custom-control-label" for="recordar">Recordar</label>
									</div>
								</div>
								<div class="col-6">
									<div class="forgot-password btn-link"><a href="#recuperar" id="formrecuperar">¿Olvidó su clave de acceso?</a></div>
								</div>
							</div>
							<div class="row pb-30">
								<div class="col-sm">
									Al ingresar acepto la totalidad de la <a href="https://ccs.org.co/politica-de-privacidad/" target="_blank" class="text-primary">política de tratamiento de datos personales</a> del CONSEJO COLOMBIANO DE SEGURIDAD
								</div>
							</div>
							<div class="row">
								<div class="col-sm-12">
									<div class="d-grid gap-2">
										<input class="btn btn-primary btn-lg" type="submit" value="Ingresar">
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
	<script>
		var localData = 'codeigniter';
		$(function() {
			var form = $('form');
			// datos de usuario recordados
			var dudq = localStorage.getItem(localData);
			if (dudq) {
				var user = decode64(dudq,3,true);
				$('#usuario').val(user.usuario);
				$('#password').val(user.password);
				$('#recordar').prop('checked', true);
			}
			form.submit(function(eve){
				form.addClass('was-validated');
				eve.preventDefault();
				eve.stopPropagation();
				var data = formToObjet(this);
				if(data.usuario && data.password) {
					if(data.recordar) {
						localStorage.setItem(localData, encode64(data,3,true));
					} else localStorage.removeItem(localData);
					$.post(IP_SERVER +'login/ingresar', data,function(res){
						if(res.success) {
							if( res.url && res.token) {
								localStorage.setItem(varToken, res.token);
								setTimeout(function() {
									window.location.replace(res.url);
								}, 2000);
							}
						}
					});
				} else toas('Debe ingresar usuario y contraseña');
			});
			$('#formrecuperar').on('click', function(ev){
				ev.preventDefault();
				var inputValue = $('#usuario').val();
				var objeto = Swal.mixin({
					customClass: {
						confirmButton: "btn",
						cancelButton: "btn"
					},
					showCancelButton: true,
					confirmButtonColor: "#28a745",
					inputPlaceholder: "correo@dominio.com"
				});
				objeto.fire({
					title: 'RESTAURAR CLAVE DE ACCESO',
					icon: "info",
					input: 'email',
					inputValue,
					html: "Para recuperar su clave de acceso, por favor complete la información solicitada. El sistema enviará al <b>correo electrónico suministrado una nueva clave de acceso temporal.</b>",
					confirmButtonText: "¡RESTAURAR!",
					inputValidator: (value) => {
						if (!value) {
						return "Dirección de correo electrónico no válida";
						}
					},
					preConfirm: function(email) {
						Swal.showLoading();
						return new Promise(function(resolve) {
							$.post(IP_SERVER+'login/recuperar', {email},function(res){
								console.log(res);
							});
						})
					},
				});
			});
			$('#verclave').on('click', function(e){
				var tipo = $('#password').prop('type');
				$('#password').prop('type',tipo=='text'?'password':'text');
			});
		});
	</script>
		<!-- Footer -->
	<footer class="sticky-footer bg-white">
		<div class="container my-auto">
			<div class="copyright text-center my-auto">
				<span>Copyright &copy; Your Website 2019</span> <span id="versionapp"></span>
			</div>
		</div>
	</footer>
	<script src="<?php echo IP_SERVER ?>assets/sweetalert2/sweetalert2.all.js"></script>
	<script src="<?php echo IP_SERVER ?>assets/js/main.js?<?php echo rand() ?>"></script>
</body>
</html>