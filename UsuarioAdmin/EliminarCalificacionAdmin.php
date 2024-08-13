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

// Verificar si el parámetro 'id_opinion' está presente en la solicitud POST
if (isset($_POST['id_opinion']) && isset($_POST['codigo_prenda'])) {
    $idOpinion = mysqli_real_escape_string($conexion, $_POST['id_opinion']);
    $codigoPrenda = mysqli_real_escape_string($conexion, $_POST['codigo_prenda']);



    // Eliminar la calificación
    $queryEliminar = "DELETE FROM Calificacion WHERE PK_Cal = '$idOpinion'";
    $resultadoEliminar = mysqli_query($conexion, $queryEliminar);

    if ($resultadoEliminar) {
        header("Location: PrendasAdmin.php?Codigo=" . $codigoPrenda);
        exit();
    } else {
        die("Error al eliminar la calificación: " . mysqli_error($conexion));
    }

} else {
    header("Location: PrendasAdmin.php?Codigo=" . $codigoPrenda);
    exit();
}
 
?>