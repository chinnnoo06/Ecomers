<?php
    session_start();

    if(!isset($_SESSION['usuario'])){
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

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $correoUsuario = $_SESSION['usuario'];
        $codigoPrenda = $_POST['codigo_prenda'];
        $calificacion = $_POST['calificacion'];
        $comentario = $_POST['comentario'];

        // Consulta para obtener el ID del usuario
        $queryUsuario = "SELECT ID FROM Usuario WHERE Correo = '$correoUsuario'";
        $resultadoUsuario = mysqli_query($conexion, $queryUsuario);

        if (!$resultadoUsuario) {
            die("Error en la consulta de usuario: " . mysqli_error($conexion));
        }

        $usuario = mysqli_fetch_assoc($resultadoUsuario);
        $usuarioID = $usuario['ID'];

        // Insertar la calificación en la base de datos
        $queryInsertarCalificacion = "INSERT INTO Calificacion (Usuario, Prenda, Calificacion, Comentario) VALUES ('$usuarioID', '$codigoPrenda', '$calificacion', '$comentario')";
        
        if (mysqli_query($conexion, $queryInsertarCalificacion)) {
            header("Location: PrendasUsuario.php?Codigo=$codigoPrenda");
            exit();
        } else {
            echo "Error: " . $queryInsertarCalificacion . "<br>" . mysqli_error($conexion);
        }

    }

    mysqli_close($conexion);
?>