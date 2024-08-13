<?php
session_start();

// Verifica si el usuario está autenticado
if (!isset($_SESSION['usuario'])) {
    echo '
    <script>
        alert("Por favor, inicie sesión");
        window.location = "../LoginRegistro.php";
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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $Titulo = $_POST['Titulo'];
    $Contenido = $_POST['Contenido'];
    $Id = intval($_POST['Id']);

    // Verifica si se ha subido una imagen
    if (isset($_FILES['Img']['tmp_name']) && $_FILES['Img']['tmp_name'] != "") {
        $Img = addslashes(file_get_contents($_FILES['Img']['tmp_name']));
        $consulta = "UPDATE Blog SET Titulo = '$Titulo', Contenido = '$Contenido', Img = '$Img' WHERE Id = $Id";
    } else {
        $consulta = "UPDATE Blog SET Titulo = '$Titulo', Contenido = '$Contenido' WHERE Id = $Id";
    }

    $resultado = mysqli_query($conexion, $consulta);

    if ($resultado) {
        header("Location: BlogDetalleAdmin.php");
    } else {
        echo '
        <script>
        alert("Algo falló, inténtelo de nuevo");
        window.history.back();
        </script>
        ';
    }
}

mysqli_close($conexion);
?>
