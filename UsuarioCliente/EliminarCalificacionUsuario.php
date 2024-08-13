<?php
session_start();

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

// Consulta para obtener los detalles del usuario, incluyendo el id
$queryUsuario = "SELECT Nombre, Apellido, Correo, Img, id FROM Usuario WHERE Correo = '$correoUsuario'";
$resultadoUsuario = mysqli_query($conexion, $queryUsuario);

if (!$resultadoUsuario) {
    die("Error en la consulta de usuario: " . mysqli_error($conexion));
}

$usuario = mysqli_fetch_assoc($resultadoUsuario);

if (isset($usuario['id'])) {
    $usuarioId = $usuario['id'];
    $nombre = $usuario['Nombre'];
    $apellido = $usuario['Apellido'];
    $imgData = $usuario['Img'];
} else {
    die("No se pudo obtener el ID del usuario.");
}

// Verificar si el parámetro 'id_opinion' está presente en la solicitud POST
if (isset($_POST['id_opinion']) && isset($_POST['codigo_prenda'])) {
    $idOpinion = mysqli_real_escape_string($conexion, $_POST['id_opinion']);
    $codigoPrenda = mysqli_real_escape_string($conexion, $_POST['codigo_prenda']);

    // Consulta para verificar si la calificación pertenece al usuario
    $queryVerificar = "SELECT Usuario FROM OpinionesPrenda WHERE PK_Cal = '$idOpinion'";
    $resultadoVerificar = mysqli_query($conexion, $queryVerificar);

    if (!$resultadoVerificar) {
        die("Error en la consulta de verificación: " . mysqli_error($conexion));
    }

    $opinion = mysqli_fetch_assoc($resultadoVerificar);

    if ($opinion && $opinion['Usuario'] == $usuarioId) {
        // Eliminar la calificación
        $queryEliminar = "DELETE FROM Calificacion WHERE PK_Cal = '$idOpinion'";
        $resultadoEliminar = mysqli_query($conexion, $queryEliminar);

        if ($resultadoEliminar) {
            header("Location: PrendasUsuario.php?Codigo=" . $codigoPrenda);
            exit();
        } else {
            die("Error al eliminar la calificación: " . mysqli_error($conexion));
        }
    } else {
        header("Location: PrendasUsuario.php?Codigo=" . $codigoPrenda);
        exit();
    }
    } else {
        header("Location: indexUsuario.php");
        exit();
    }
?>