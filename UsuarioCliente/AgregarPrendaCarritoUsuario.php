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

// Obtener el correo del usuario desde la sesión
$correoUsuario = $_SESSION['usuario'];

// Consulta para obtener los detalles del usuario, incluyendo el id
$queryUsuario = "SELECT id FROM Usuario WHERE Correo = '$correoUsuario'";
$resultadoUsuario = mysqli_query($conexion, $queryUsuario);

if (!$resultadoUsuario) {
    die("Error en la consulta de usuario: " . mysqli_error($conexion));
}

$usuario = mysqli_fetch_assoc($resultadoUsuario);

if (isset($usuario['id'])) {
    $usuarioId = $usuario['id'];
} else {
    die("No se pudo obtener el ID del usuario.");
}

// Obtener el ID del carrito del usuario
$queryCarrito = "SELECT PK_Carrito FROM Carrito WHERE Usuario = $usuarioId";
$resultadoCarrito = mysqli_query($conexion, $queryCarrito);

if (!$resultadoCarrito) {
    die("Error en la consulta del carrito: " . mysqli_error($conexion));
}

$carrito = mysqli_fetch_assoc($resultadoCarrito);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $Prenda = $_POST['Codigo'];
    $Talla = $_POST['Talla'];
    $Cantidad = $_POST['Cantidad'];
    $PrecioPrenda = $_POST['Precio']; 
    $Precio = ($Cantidad * $PrecioPrenda);

    // Validación de datos antes de la inserción
    if (isset($carrito['PK_Carrito']) && isset($Prenda) && isset($Talla) && isset($Cantidad) && isset($Precio)) {
        $PK_Carrito = $carrito['PK_Carrito'];

        // Validación de la talla
        $queryValidarTalla = "SELECT * FROM talla WHERE PK_Talla = '$Talla'";
        $resultadoValidarTalla = mysqli_query($conexion, $queryValidarTalla);

        if (mysqli_num_rows($resultadoValidarTalla) == 0) {
            die("La talla seleccionada no es válida.");
        }

        // Insertar la prenda en el carrito
        $queryInsertarCarrito = "INSERT INTO carrito_prenda (Carrito, Prenda, Talla, Cantidad, Precio) 
        VALUES ('$PK_Carrito', '$Prenda', '$Talla', '$Cantidad', '$Precio')";

        if (mysqli_query($conexion, $queryInsertarCarrito)) {
            echo '
            <script>
            alert("Prenda agregada al carrito.");
            window.location = "PrendasUsuario.php?Codigo=' . $Prenda . '";
            </script>
            ';
        } else {
            echo "Error: " . $queryInsertarCarrito . "<br>" . mysqli_error($conexion);
        }
    } else {
        echo '
        <script>
            alert("Faltan datos para agregar la prenda al carrito.");
            window.location = "PrendasUsuario.php?Codigo=' . $Prenda . '";
        </script>
        ';
    }
}

mysqli_close($conexion);
?>
