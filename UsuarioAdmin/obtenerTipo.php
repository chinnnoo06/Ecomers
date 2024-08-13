<?php
// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "", "ecomers");

if ($conexion->connect_error) {
    die("No se ha podido conectar al servidor de Base de datos: " . $conexion->connect_error);
}

$id = intval($_GET['id']);
$query = "SELECT Nombre FROM Tipo WHERE PK_Tipo = $id";
$result = $conexion->query($query);

if ($result->num_rows > 0) {
    $tipo = $result->fetch_assoc();
    echo json_encode($tipo);
} else {
    echo json_encode(['error' => 'No se encontró el tipo.']);
}

$conexion->close();
?>
