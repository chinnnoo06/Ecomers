<?php
// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "", "ecomers");

if ($conexion->connect_error) {
    die("No se ha podido conectar al servidor de Base de datos: " . $conexion->connect_error);
}

$id = intval($_GET['id']);
$query = "SELECT Titulo, Contenido FROM Blog WHERE Id = $id";
$result = $conexion->query($query);

if ($result->num_rows > 0) {
    $blog = $result->fetch_assoc();
    echo json_encode($blog);
} else {
    echo json_encode(['error' => 'No se encontró el blog.']);
}

$conexion->close();
?>
