<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión - SIGES</title>

    <link rel="stylesheet" href="../assets/css/login.css">
</head>

<body>

    <div class="form-container">

        <h3 class="titulo">SIGES</h3>

        <?php if (isset($_GET['error'])): ?>
            <p role="alert" style="color: #b91c1c; text-align: center; margin-bottom: 16px;">Correo o contraseña incorrectos.</p>
        <?php endif; ?>

        <?php if (isset($_GET['recuperada'])): ?>
            <p role="status" style="color: #15803d; text-align: center; margin-bottom: 16px;">
                Tu contraseña se ha cambiado correctamente. Ya puedes iniciar sesión.
            </p>
        <?php endif; ?>

        <form action="procesar_login.php" method="POST">

            <div class="campo">
                <label class="label" for="correo">Correo electrónico</label>
                <input
                    type="email"
                    name="correo"
                    id="correo"
                    class="input"
                    required
                >
            </div>

            <div class="campo">
                <label class="label" for="contrasena">Contraseña</label>
                <input
                    type="password"
                    name="contrasena"
                    id="contrasena"
                    class="input"
                    required
                >
            </div>

            <button type="submit" class="btn btn-primary">
                Iniciar sesión
            </button>

        </form>

        <p class="recuperar">
            <a href="recuperar.php">¿Olvidaste tu contraseña?</a>
        </p>

    </div>

</body>
</html>