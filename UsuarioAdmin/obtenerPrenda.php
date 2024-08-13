<?php
// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "", "ecomers");

if ($conexion->connect_error) {
    die("No se ha podido conectar al servidor de Base de datos: " . $conexion->connect_error);
}

$codigo = intval($_GET['Codigo']);
$query = "SELECT Codigo, Nombre, Precio, Tipo FROM Prenda WHERE Codigo = $codigo";
$result = $conexion->query($query);

if ($result->num_rows > 0) {
    $prenda = $result->fetch_assoc();
    echo json_encode($prenda);
} else {
    echo json_encode(['error' => 'No se encontró la prenda.']);
}

$conexion->close();
?>
