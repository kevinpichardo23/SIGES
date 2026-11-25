<?php
session_start();
require_once "../config/conexion.php";

$error = "";
$correo = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $correo = trim($_POST["correo"] ?? "");

    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {

        $error = "Introduce un correo electrónico válido.";

    } else {

        $consulta = $conexion->prepare(
            "SELECT id FROM usuarios WHERE correo = ?"
        );

        $consulta->execute([$correo]);

        $usuario = $consulta->fetch(PDO::FETCH_ASSOC);

        if ($usuario) {

            $codigo = (string) random_int(100000, 999999);

            $_SESSION["recuperacion"] = [
                "usuario_id" => $usuario["id"],
                "codigo" => $codigo,
                "expira" => time() + 300
            ];

            header("Location: verificar_codigo.php");
            exit;

        } else {

            $error = "No existe una cuenta registrada con ese correo.";

        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar contraseña - SIGES</title>

    <link rel="stylesheet" href="../assets/css/login.css">
</head>

<body>

    <div class="form-container">

        <h3 class="titulo">Recuperar contraseña</h3>

        <p>Introduce el correo electrónico asociado a tu cuenta.</p>

        <?php if ($error): ?>
            <p role="alert" style="color: #b91c1c;">
                <?= htmlspecialchars($error) ?>
            </p>
        <?php endif; ?>

        <form method="POST">

            <div class="campo">
                <label class="label" for="correo">
                    Correo electrónico
                </label>

                <input
                    type="email"
                    name="correo"
                    id="correo"
                    class="input"
                    value="<?= htmlspecialchars($correo) ?>"
                    required
                >
            </div>

            <button type="submit" class="btn btn-primary">
                Continuar
            </button>

        </form>

        <p class="recuperar">
            <a href="login.php">Volver al inicio de sesión</a>
        </p>

    </div>

</body>
</html>