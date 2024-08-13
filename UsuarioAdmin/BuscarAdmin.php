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

// Consulta para buscar el tipo de prenda
$queryTipo = "SELECT * FROM Tipo WHERE Nombre LIKE '%$consulta%'";
$resultadoTipo = mysqli_query($conexion, $queryTipo);

if (!$resultadoTipo) {
    die("Error en la consulta de tipo: " . mysqli_error($conexion));
}

// Consulta para buscar la prenda
$queryPrenda = "SELECT * FROM Prenda WHERE Codigo LIKE '%$consulta%'";
$resultadoPrenda = mysqli_query($conexion, $queryPrenda);

if (!$resultadoPrenda) {
    die("Error en la consulta de prenda: " . mysqli_error($conexion));
}

// Verificar si se encontraron tipos de prenda
if (mysqli_num_rows($resultadoTipo) > 0) {
    // Obtener el primer tipo encontrado
    $tipo = mysqli_fetch_assoc($resultadoTipo);
    $Tipo_id = $tipo['PK_Tipo'];
    // Redirigir a la sección de categorías con el tipo de prenda filtrado
    header("Location: CategoriasAdmin.php?tipo=$Tipo_id");
    exit();
}

// Verificar si se encontraron prendas
if (mysqli_num_rows($resultadoPrenda) > 0) {
    // Obtener la primera prenda encontrada
    $prenda = mysqli_fetch_assoc($resultadoPrenda);
    $Codigo = $prenda['Codigo'];
    // Redirigir a la página de la prenda específica
    header("Location: PrendasAdmin.php?Codigo=$Codigo");
    exit();
}

// Si no se encontraron ni tipos ni prendas
header("Location: SinResultadosAdmin.php");
exit();
?>