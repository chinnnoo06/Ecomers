<?php
session_start(); // Iniciar la sesión

// Verificar si se recibió una solicitud POST para vaciar el carrito
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar si el carrito de visitante existe en la sesión
    if (isset($_SESSION['carrito_visitante'])) {
        // Vaciar el carrito
        unset($_SESSION['carrito_visitante']);
        
        // Redireccionar al carrito
        header('Location: CarritoVisitante.php');
        exit();
    } else {
        die("No hay artículos en el carrito para vaciar.");
    }
} else {
    die("Solicitud inválida.");
}
?>
