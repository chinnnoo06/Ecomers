
<?php
session_start();

// Verifica si el usuario está autenticado
if (!isset($_SESSION['usuario'])) {
    echo '
    <script>
        alert("Por favor, inicie sesión");
        window.location = "LoginRegistro.php";
    </script>
    ';
    session_destroy();
    die();
}

// Conexión a la base de datos
$conexion = mysqli_connect("localhost", "root", "", "ecomers");

if (!$conexion) {
    die("No se ha podido conectar al servidor de Base de datos: " . mysqli_connect_error());
}

// Obtener el correo del usuario desde la sesión
$correoUsuario = $_SESSION['usuario'];

$queryUsuario = "SELECT Nombre, Apellido, Correo, id, Password FROM Usuario WHERE Correo = '$correoUsuario'";
$resultadoUsuario = mysqli_query($conexion, $queryUsuario);

if (!$resultadoUsuario) {
    die("Error en la consulta de usuario: " . mysqli_error($conexion));
}

$usuario = mysqli_fetch_assoc($resultadoUsuario);

if (isset($usuario['id'])) {
    $usuarioId = $usuario['id'];
    $passwordHash = $usuario['Password'];
} else {
    die("No se pudo obtener el ID del usuario.");
}

if (isset($_POST['Nombre']) && isset($_POST['Apellido']) && isset($_POST['Password'])) {
    $Nombre = $_POST['Nombre'];
    $Apellido = $_POST['Apellido'];
    $Password = $_POST['Password'];
    $Password = hash('sha512', $Password);

    $consulta = "SELECT * FROM Usuario WHERE id='$usuarioId' AND Password='$Password'";
    $resultado = mysqli_query($conexion, $consulta);

    if ($resultado && mysqli_num_rows($resultado) > 0) {

        if(isset($_FILES['Img']['tmp_name']) && $_FILES['Img']['tmp_name'] != ""){
            $Img = addslashes(file_get_contents($_FILES['Img']['tmp_name']));
            $consulta = "UPDATE Usuario 
            SET Nombre = '$Nombre', Apellido = '$Apellido', Img = '$Img'
            WHERE Id = $usuarioId";

            $resultado = mysqli_query($conexion, $consulta);
        } else {
            $consulta = "UPDATE Usuario 
            SET Nombre = '$Nombre', Apellido = '$Apellido'
            WHERE Id = $usuarioId";

            $resultado = mysqli_query($conexion, $consulta);
        }
        if ($resultado) {
            echo '
            <script>
            alert("Perfil actualizado con exito");
            window.history.back();
            </script>
            ';
        } else {
            echo '
            <script>
            alert("Algo falló, inténtelo de nuevo");
            window.history.back();
            </script>
            ';
        }
    } else {
        echo '
        <script>
        alert("Contraseña incorrecta");
        window.history.back();
        </script>
        ';
    }
}

mysqli_close($conexion);
?>
