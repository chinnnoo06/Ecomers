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

// Obtener el término de búsqueda
$consulta = mysqli_real_escape_string($conexion, $_GET['query']);

// Consulta para buscar el ID del usuario
$queryUsuarios = "SELECT * FROM Usuario WHERE ID LIKE '%$consulta%'";
$resultadoUsuarios = mysqli_query($conexion, $queryUsuarios);

if (!$resultadoUsuarios) {
    die("Error en la consulta de tipo: " . mysqli_error($conexion));
}

// Verificar si se encontraron tipos de prenda
if (mysqli_num_rows($resultadoUsuarios) > 0) {
    // Obtener el primer ID encontrado
    $Usuario = mysqli_fetch_assoc($resultadoUsuarios);
    $ID = $Usuario['ID'];
    // Redirigir a la sección de categorías con el tipo de prenda filtrado
    header("Location: UsuariosAdmin.php?ID=$ID");
    exit();
}

// Si no se encontraron ni tipos ni prendas
header("Location: SinResultadosAdmin.php");
exit();
?>