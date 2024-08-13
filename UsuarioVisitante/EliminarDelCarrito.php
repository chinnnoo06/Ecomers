<?php
session_start(); // Iniciar la sesión

// Verificar si se recibió una solicitud POST para eliminar una prenda del carrito
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar si el carrito de visitante existe en la sesión
    if (isset($_SESSION['carrito_visitante'])) {
        // Obtener los datos del formulario
        $codigo = $_POST['Codigo'];
        $talla = $_POST['Talla'];

        // Filtrar el carrito para actualizar la cantidad del artículo específico
        $_SESSION['carrito_visitante'] = array_map(function($item) use ($codigo, $talla) {
            if ($item['Codigo'] === $codigo && $item['Talla'] === $talla) {
              return null; // Eliminar el artículo si la cantidad es 0 o menor
            }
            return $item;
        }, $_SESSION['carrito_visitante']);

        // Eliminar los elementos nulos del array del carrito
        $_SESSION['carrito_visitante'] = array_filter($_SESSION['carrito_visitante']);

        // Reindexar el array del carrito para evitar agujeros en los índices
        $_SESSION['carrito_visitante'] = array_values($_SESSION['carrito_visitante']);

        // Redireccionar al carrito
        header('Location: CarritoVisitante.php');
        exit();
    } else {
        die("No hay artículos en el carrito para eliminar.");
    }
} else {
    die("Solicitud inválida.");
}
?>
