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

// Verificar si el parámetro 'PK_Carrito' está presente en la solicitud POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $PK_Carrito = $_POST['PK_Carrito'];

    // Consulta para obtener las cantidades de las prendas en el carrito
    $queryObtenerCantidades = "SELECT Prenda, Cantidad, Talla, Precio FROM Carrito_Prenda WHERE Carrito = $PK_Carrito";
    $resultadoCantidades = mysqli_query($conexion, $queryObtenerCantidades);

    if (!$resultadoCantidades) {
        die("Error en la consulta de cantidades: " . mysqli_error($conexion));
    }

    while ($fila = mysqli_fetch_assoc($resultadoCantidades)) {
        $Prenda = $fila['Prenda'];
        $Cantidad = $fila['Cantidad'];
        $Talla = $fila['Talla'];
        $Precio = $fila['Precio'];

        // Validar la cantidad disponible de la prenda en la talla seleccionada
        $queryCantidad = "SELECT Cantidad FROM Prenda_Talla WHERE Prenda = $Prenda AND Talla = $Talla";
        $resultadoCantidad = mysqli_query($conexion, $queryCantidad); 

        if (!$resultadoCantidad) {
            die("Error en la consulta de cantidad de prendas: " . mysqli_error($conexion));
        }
        
        $filaCantidad = mysqli_fetch_assoc($resultadoCantidad);
        $CantidadTallaPrenda = $filaCantidad['Cantidad']; // Obtener la cantidad disponible

        if ($Cantidad <= $CantidadTallaPrenda) {
            // Actualizar la cantidad en el almacén
            $queryActualizarAlmacen = "UPDATE Prenda_Talla SET Cantidad = Cantidad - $Cantidad WHERE Prenda = $Prenda AND Talla = $Talla";
            $resultadoActualizar = mysqli_query($conexion, $queryActualizarAlmacen);

            if (!$resultadoActualizar) {
                die("Error al actualizar la cantidad en el almacén: " . mysqli_error($conexion));
            }

            // Insertar la transacción en el historial
            $queryPagar = "INSERT INTO historial (Usuario, Prenda, Talla, Cantidad, Precio, Fecha)
            VALUES ('$usuarioId', '$Prenda', '$Talla', '$Cantidad', '$Precio', NOW())";
            $resultadoPagar = mysqli_query($conexion, $queryPagar);

            if (!$resultadoPagar) {
                die("Error al pagar el carrito: " . mysqli_error($conexion));
            }
        } else {
            echo '
            <script>
                alert("No hay la cantidad suficiente del producto seleccionado, lo sentimos :(.");
                window.location = "PrendasUsuario.php?Codigo=' . $Prenda . '";
            </script>
            ';
            exit();
        }
    }

    // Eliminar todos los registros del carrito
    $queryEliminarCarrito = "DELETE FROM Carrito_Prenda WHERE Carrito = $PK_Carrito";
    $resultadoEliminarCarrito = mysqli_query($conexion, $queryEliminarCarrito);

    if (!$resultadoEliminarCarrito) {
        die("Error al vaciar el carrito: " . mysqli_error($conexion));
    }

    header("Location: CarritoUsuario.php");
    exit();
} else {
    header("Location: CarritoUsuario.php");
    exit();
}

// Cerrar la conexión a la base de datos
mysqli_close($conexion);
?>
