<?php
session_start();
require_once __DIR__ . "/clases/Usuario.php";

$mensajeError = "";

// Si ya hay sesión activa, no tiene sentido mostrar el login de nuevo
if (isset($_SESSION['tipo_usuario'])) {
    $destino = ($_SESSION['tipo_usuario'] === Usuario::TIPO_ADMINISTRADOR)
        ? "administrador/inicio.php"
        : "funcionario/inicio.php";
    header("Location: $destino");
    exit;
}


// Mensajes según el motivo por el que se llegó acá (sesión expirada, sin sesión, etc.)
if (isset($_GET['error']) && $_GET['error'] === 'sesion_expirada') {
    $mensajeError = "Su sesión expiró por inactividad. Vuelva a ingresar.";
}

if (isset($_POST["Ingresar"])) {
    
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $usuario = (new Usuario())->autenticar($email, $password);

    if ($usuario === null) {
        $mensajeError = "Email o contraseña incorrectos.";
    } else {
        $_SESSION['id_usuario']    = $usuario->getCi();
        $_SESSION['primer_nombre'] = $usuario->getPrimerNombre();
        $_SESSION['tipo_usuario']  = $usuario->getTipoUsuario();
        $_SESSION['id_sucursal']   = $usuario->getIdSucursal();

        // Límite de inactividad según el rol (regla de negocio 10 del enunciado),
        // controlado con una cookie en lugar de la sesión.
        $limite = ($usuario->getTipoUsuario() === Usuario::TIPO_ADMINISTRADOR) ? 3600 : 900;
        setcookie('ultimo_acceso', (string) time(), time() + $limite, '/');

        $destino = ($usuario->getTipoUsuario() === Usuario::TIPO_ADMINISTRADOR)
            ? "administrador/inicio.php"
            : "funcionario/inicio.php";

        header("Location: $destino");
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <fieldset>
        <legend align="center">Login</legend>

        <?php if ($mensajeError !== ""): ?>
            <p style="color: red; text-align: center;"><?php echo htmlspecialchars($mensajeError); ?></p>
        <?php endif; ?>

        <table align="center">
            <tr><td>
                <form action="login.php" method="POST">
                    <div>
                        Email:
                        <p>
                            <input type="email" id="email" name="email"
                                   placeholder="Ingrese el email"
                                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                                   required>
                        </p>
                    </div>
                    <div>
                        Contraseña:
                        <p>
                            <input type="password" id="password" name="password"
                                   placeholder="Mínimo 8 caracteres" minlength="8" required>
                        </p>
                    </div>

                    <input type="submit" name="Ingresar" value="Ingresar">
                </form>
            </td></tr>
        </table>
    </fieldset>
</body>
</html> 