<?php
session_start();
require_once "../config/conexion.php";

$recuperacion = $_SESSION["recuperacion"] ?? null;

// Verificar que el código haya sido validado y no haya expirado
if (
    !$recuperacion ||
    ($_SESSION["codigo_verificado"] ?? false) !== true ||
    time() > $recuperacion["expira"]
) {
    unset($_SESSION["recuperacion"], $_SESSION["codigo_verificado"]);
    header("Location: recuperar.php");
    exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $contrasena = $_POST["contrasena"] ?? "";
    $confirmar = $_POST["confirmar"] ?? "";

    if (strlen($contrasena) < 8) {
        $error = "La contraseña debe tener al menos 8 caracteres.";
    } elseif ($contrasena !== $confirmar) {
        $error = "Las contraseñas no coinciden.";
    } else {
        $hash = password_hash($contrasena, PASSWORD_DEFAULT);

        $consulta = $conexion->prepare(
            "UPDATE usuarios SET contraseña = ? WHERE id = ?"
        );

        $consulta->execute([
            $hash,
            $recuperacion["usuario_id"]
        ]);

        unset($_SESSION["recuperacion"], $_SESSION["codigo_verificado"]);

        header("Location: login.php?recuperada=1");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva contraseña - SIGES</title>
    <link rel="stylesheet" href="../assets/css/login.css">
</head>
<body>

    <div class="form-container">
        <h3 class="titulo">Nueva contraseña</h3>

        <p>Introduce tu nueva contraseña para continuar.</p>

        <?php if ($error): ?>
            <p role="alert" style="color: #b91c1c;">
                <?= htmlspecialchars($error) ?>
            </p>
        <?php endif; ?>

        <form method="POST">

            <div class="campo">
                <label class="label" for="contrasena">
                    Nueva contraseña
                </label>

                <input
                    type="password"
                    name="contrasena"
                    id="contrasena"
                    class="input"
                    minlength="8"
                    required
                >
            </div>

            <div class="campo">
                <label class="label" for="confirmar">
                    Confirmar contraseña
                </label>

                <input
                    type="password"
                    name="confirmar"
                    id="confirmar"
                    class="input"
                    minlength="8"
                    required
                >
            </div>

            <button type="submit" class="btn btn-primary">
                Cambiar contraseña
            </button>

        </form>
    </div>

</body>
</html>