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

// Verificar si el parámetro 'PK_Carrito' está presente en la solicitud POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $PK_Carrito = $_POST['PK_Carrito'];
    $PK_CP = $_POST['PK_CP'];

    // Consulta para eliminar todos los registros del carrito
    $queryEliminar = "DELETE FROM carrito_prenda WHERE PK_CP = $PK_CP";
    $resultadoEliminar = mysqli_query($conexion, $queryEliminar);

    if (!$resultadoEliminar) {
        die("Error al eliminar el registro: " . mysqli_error($conexion));
    }

    header("Location: CarritoUsuario.php");
    exit();
} else {
    header("Location: CarritoUsuario.php");
    exit();
}

// Cerrar la conexión a la base de datos
mysqli_close($conexion);
?>
