<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Saho - Iniciar Sesión</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta http-equiv="cache-control" content="max-age=0" />
    <meta http-equiv="pragma" content="no-cache" />
    <meta http-equiv="expires" content="0" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="DC.language" content="ES" />
    <link rel="icon" type="image/png" href="<?php echo IP_SERVER . 'assets/imagen/icon_solo.png'; ?>" />
    <link rel="stylesheet" href="<?php echo IP_SERVER . 'assets/css/main.css'; ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    var IP_SERVER = '<?php echo IP_SERVER ?>';
    </script>
    <style>
    /* small helpers to better match the original visual intent */
    .brand-circle {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: var(--orange-500);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .brand-circle svg {
        width: 36px;
        height: 36px;
        color: #fff;
    }

    .card-login {
        box-shadow: 0 4px 12px #000000b0;
        border-radius: 8px;
    }

    /* Reduce the logo size so it doesn't take too much vertical space
       Use max-height + auto width and object-fit to preserve aspect ratio */
    .imagen-logo {
        height: 90px;
        max-height: 90px;
        width: auto;
        display: block;
        margin: 0 auto 8px auto;
        object-fit: contain;
    }
    </style>
</head>

<body class="bg-light" id="body-login">
    <div class="container min-vh-100 d-flex align-items-center justify-content-center">
        <div class="col-12 col-md-8 col-lg-5">

            <div class="card card-login">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <div class="">
                            <img class="imagen-logo" src="<?php echo IP_SERVER . 'assets/imagen/icon_saho.png'; ?>"
                                alt="Logo">
                        </div>
                        <h2 class="mt-3">Sistema de ventas</h2>
                        <p class="text-muted">Inicia sesión en tu cuenta</p>
                    </div>
                    <form method="post" action="" novalidate id="loginForm">
                        <div class="mb-3">
                            <label for="correo" class="form-label">Correo electrónico</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <input id="correo" name="correo" type="email" class="form-control"
                                    placeholder="ejemplo@correo.com" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="contrasena" class="form-label">Contraseña</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input id="contrasena" name="contrasena" type="password" class="form-control"
                                    placeholder="••••••••" required>
                                <button type="button" class="btn btn-outline-orange" id="togglePassword"
                                    aria-label="Mostrar contraseña">
                                    <i id="eyeIcon" class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1" id="remember-me"
                                    name="remember-me">
                                <label class="form-check-label small" for="remember-me">Recordarme</label>
                            </div>
                            <div class="small">
                                <a href="#">¿Olvidaste tu contraseña?</a>
                            </div>
                        </div>

                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-orange btn-lg">Iniciar Sesión</button>
                        </div>

                        <div class="text-center">
                            <p class="mb-0 small text-muted">¿No tienes una cuenta? <a href="#">Regístrate aquí</a></p>
                        </div>

                    </form>
                </div>
            </div>

            <div class="text-center mt-3 small text-muted">
                <p class="mb-0">© Sistema de ventas. Todos los derechos reservados.</p>
            </div>
        </div>
    </div>
</body>
<script>
// Toggle password visibility using Bootstrap Icons classes
(function() {
    var toggle = document.getElementById('togglePassword');
    var pwd = document.getElementById('contrasena');
    var eyeIcon = document.getElementById('eyeIcon');
    if (!toggle || !pwd || !eyeIcon) return;
    toggle.addEventListener('click', function() {
        if (pwd.type === 'password') {
            pwd.type = 'text';
            eyeIcon.classList.remove('bi-eye');
            eyeIcon.classList.add('bi-eye-slash');
        } else {
            pwd.type = 'password';
            eyeIcon.classList.remove('bi-eye-slash');
            eyeIcon.classList.add('bi-eye');
        }
    });

    $('#loginForm').on('submit', function(e) {
        e.preventDefault();

        $.ajax({
            url: IP_SERVER + 'login/ingresar',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.resp == 1) {
                    window.location.href = IP_SERVER + 'productos';
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        html: response.msg,
                    });
                }
            },
            error: function() {

            }
        });
    });
})();
</script>