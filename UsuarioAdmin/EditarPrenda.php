
<?php
session_start();

// Verifica si el usuario está autenticado
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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    //Prenda
    $Codigo = intval($_POST['Codigo']);
    $Nombre = $_POST['Nombre'];
    $Precio = $_POST['Precio'];
    $Tipo = $_POST['Tipo'];
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

    if(isset($_FILES['Img']['tmp_name']) && $_FILES['Img']['tmp_name'] != ""){
        $Img = addslashes(file_get_contents($_FILES['Img']['tmp_name']));
        $queryPrenda = "UPDATE Prenda 
        SET Codigo = '$Codigo', Nombre = '$Nombre', Precio = '$Precio', Tipo = '$Tipo', Img = '$Img'
        WHERE Codigo = $Codigo";

        $resultadoPrenda = mysqli_query($conexion, $queryPrenda);
    } else {
        $queryPrenda = "UPDATE Prenda 
        SET Codigo = '$Codigo', Nombre = '$Nombre', Precio = '$Precio', Tipo = '$Tipo'
        WHERE Codigo = $Codigo";

        $resultadoPrenda = mysqli_query($conexion, $queryPrenda);
    }

    $queryTallas = [
        "UPDATE Prenda_Talla SET Cantidad = $CantidadXS WHERE Talla = '$XS' AND Prenda = $Codigo",
        "UPDATE Prenda_Talla SET Cantidad = $CantidadS WHERE Talla = '$S' AND Prenda = $Codigo",
        "UPDATE Prenda_Talla SET Cantidad = $CantidadM WHERE Talla = '$M' AND Prenda = $Codigo",
        "UPDATE Prenda_Talla SET Cantidad = $CantidadL WHERE Talla = '$L' AND Prenda = $Codigo",
        "UPDATE Prenda_Talla SET Cantidad = $CantidadXL WHERE Talla = '$XL' AND Prenda = $Codigo",
        "UPDATE Prenda_Talla SET Cantidad = $CantidadUnitalla WHERE Talla = '$Unitalla' AND Prenda = $Codigo"
    ];

    $resultadoTallas = true;
    foreach ($queryTallas as $query) {
        if (!mysqli_query($conexion, $query)) {
            $resultadoTallas = false;
            break;
        }
    }

    if ($resultadoPrenda == true && $resultadoTallas == true) {
        header("Location: PrendasAdmin.php?Codigo=" . $Codigo);
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
