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
$conexion = new mysqli("localhost", "root", "", "ecomers");

if ($conexion->connect_error) {
    die("No se ha podido conectar al servidor de Base de datos: " . $conexion->connect_error);
}

// Obtener el correo del usuario desde la sesión
$correoUsuario = $_SESSION['usuario'];


// Obtener el término de búsqueda
$consulta = mysqli_real_escape_string($conexion, $_GET['query']);

// Consulta para buscar el tipo de prenda
$queryTipo = "SELECT * FROM Tipo WHERE Nombre LIKE '%$consulta%'";
$resultadoTipo = mysqli_query($conexion, $queryTipo);

if (!$resultadoTipo) {
    die("Error en la consulta de tipo: " . mysqli_error($conexion));
}

if (mysqli_num_rows($resultadoTipo) == 0) {
// Si no se encontraron ni tipos ni prendas
    header("Location: SinResultadosUsuario.php");
    exit();
}

// Obtener el primer tipo encontrado
$tipo = mysqli_fetch_assoc($resultadoTipo);
$Tipo_id = $tipo['PK_Tipo'];

// Redirigir a la sección de categorías con el tipo de prenda filtrado
header("Location: CategoriasUsuario.php?tipo=$Tipo_id");
exit();
?>