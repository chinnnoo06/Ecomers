<?php
// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "", "ecomers");

if ($conexion->connect_error) {
    die("No se ha podido conectar al servidor de Base de datos: " . $conexion->connect_error);
}

// Obtener el código de la prenda de la solicitud GET
$codigo = intval($_GET['Prenda']);

// Consulta para obtener las cantidades de cada talla
$query = "SELECT Talla, Cantidad FROM prenda_talla WHERE Prenda = $codigo";
$result = $conexion->query($query);

$tallas = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        switch ($row['Talla']) {
            case 1: $tallas['XS'] = $row['Cantidad']; break;
            case 2: $tallas['S'] = $row['Cantidad']; break;
            case 3: $tallas['M'] = $row['Cantidad']; break;
            case 4: $tallas['L'] = $row['Cantidad']; break;
            case 5: $tallas['XL'] = $row['Cantidad']; break;
            case 6: $tallas['Unitalla'] = $row['Cantidad']; break;
        }
    }
} else {
    echo json_encode(['error' => 'No se encontraron las tallas']);
}

echo json_encode($tallas);

$conexion->close();
?>