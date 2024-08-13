<?php
session_start();

// Verificar si se recibió una solicitud POST para agregar una prenda al carrito
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Conexión a la base de datos
    $conexion = mysqli_connect("localhost", "root", "", "ecomers");
    if (!$conexion) {
        die("No se ha podido conectar al servidor de Base de datos: " . mysqli_connect_error());
    }

    // Obtener los datos del formulario
    $codigo = $_POST['Codigo'];
    $talla = $_POST['Talla'];
    $cantidad = $_POST['Cantidad'];
    $precio = $_POST['Precio'];

    // Verificar si ya hay un carrito de visitante en la sesión
    if (!isset($_SESSION['carrito_visitante'])) {
        $_SESSION['carrito_visitante'] = [];
    }

    // Crear el array de la prenda a agregar
    $prendaCarrito = [
        'Codigo' => $codigo,
        'Talla' => $talla,
        'Cantidad' => $cantidad,
        'Precio' => $precio,
        'Total' => $precio * $cantidad
    ];

    // Agregar la prenda al carrito
    $_SESSION['carrito_visitante'][] = $prendaCarrito;

    echo '
    <script>
    alert("Prenda agregada al carrito.");
    window.location = "PrendasVisitante.php?Codigo=' . $codigo . '";
    </script>
    ';
    exit();
} else {
    die("Solicitud inválida.");
}
?>