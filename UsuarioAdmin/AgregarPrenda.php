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
        //Prenda
        $Codigo = $_POST['Codigo'];
        $Nombre = $_POST['Nombre'];
        $Precio = $_POST['Precio'];
        $PK_Tipo = intval($_POST['PK_Tipo']);
        $Img = addslashes(file_get_contents($_FILES['Img']['tmp_name']));
        //Prenda_Talla
        $XS = $_POST['Talla_XS'];
        $S = $_POST['Talla_S'];
        $M = $_POST['Talla_M'];
        $L = $_POST['Talla_L'];
        $XL = $_POST['Talla_XL'];
        $Unitalla = $_POST['Talla_Unitalla'];
        $CantidadXS = $_POST['XS'];
        $CantidadS = $_POST['S'];
        $CantidadM = $_POST['M'];
        $CantidadL = $_POST['L'];
        $CantidadXL = $_POST['XL'];
        $CantidadUnitalla = $_POST['Unitalla'];

        // Insertar la prenda en la tabla prenda
        $queryInsertarPrenda = "INSERT INTO Prenda (Codigo, Nombre, Precio, Tipo, Img) 
        VALUES ('$Codigo', '$Nombre', '$Precio', '$PK_Tipo', '$Img')";

        $resultadoPrenda = mysqli_query($conexion, $queryInsertarPrenda);

        // Insertar las tallas en la tabla prenda_talla
        $queryInsertarPrenda_Talla = "INSERT INTO Prenda_Talla (Prenda, Talla, Cantidad) 
        VALUES ('$Codigo', '$XS', '$CantidadXS'),
        ('$Codigo', '$S', '$CantidadS'),
        ('$Codigo', '$M', '$CantidadM'),
        ('$Codigo', '$L', '$CantidadL'),
        ('$Codigo', '$XL', '$CantidadXL'),
        ('$Codigo', '$Unitalla', '$CantidadUnitalla')";

        $resultadoPrenda_Talla = mysqli_query($conexion, $queryInsertarPrenda_Talla);


        
        if ($resultadoPrenda && $resultadoPrenda_Talla) {
            header("Location: CategoriasAdmin.php");
            exit();
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