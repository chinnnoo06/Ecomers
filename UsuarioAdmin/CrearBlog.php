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
        $Titulo = $_POST['Titulo'];
        $Contenido = $_POST['Contenido'];
        $Img = addslashes(file_get_contents($_FILES['Img']['tmp_name']));

        // Insertar la calificación en la base de datos
        $queryInsertarBlog = "INSERT INTO Blog (Titulo, Contenido, Img) VALUES ('$Titulo', '$Contenido', '$Img')";
        
        if (mysqli_query($conexion, $queryInsertarBlog)) {
            header("Location: indexAdmin.php#blog");
            exit();
        } else {
            echo "Error: " . $queryInsertarBlog . "<br>" . mysqli_error($conexion);
        }

    }

    mysqli_close($conexion);
?>