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
    // Verificar si todos los datos necesarios están presentes
    if (isset($_POST['Codigo'], $_POST['Precio'], $_POST['Cantidad'], $_POST['Talla'])) {
        $CodigoPrenda = $_POST['Codigo'];
        $Precio = isset($_POST['Precio']) ? floatval($_POST['Precio']) : 0; // Convertir a float
        $Cantidad = isset($_POST['Cantidad']) ? intval($_POST['Cantidad']) : 0; // Convertir a int
        $Talla = $_POST['Talla'];

        if ($Precio <= 0 || $Cantidad <= 0) {
            echo '
            <script>
                alert("Datos de precio o cantidad no válidos.");
                window.location = "PrendasUsuario.php?Codigo=' . $CodigoPrenda . '";
            </script>
            ';
            exit();
        }

        $PrecioTotal = ($Precio * $Cantidad);

        // Validar la cantidad disponible de la prenda en la talla seleccionada
        $queryCantidad = "SELECT Cantidad FROM Prenda_Talla WHERE Prenda = $CodigoPrenda AND Talla = $Talla";
        $resultadoCantidad = mysqli_query($conexion, $queryCantidad); 

        if (!$resultadoCantidad) {
            die("Error en la consulta de cantidad de prendas: " . mysqli_error($conexion));
        }
            
        $filaCantidad = mysqli_fetch_assoc($resultadoCantidad);
        $CantidadTallaPrenda = $filaCantidad['Cantidad']; // Obtener la cantidad disponible

        if ($Cantidad <= $CantidadTallaPrenda) {
            // Actualizar la cantidad en el almacén
            $queryActualizarAlmacen = "UPDATE Prenda_Talla SET Cantidad = Cantidad - $Cantidad WHERE Prenda = $CodigoPrenda AND Talla = $Talla";
            $resultadoActualizar = mysqli_query($conexion, $queryActualizarAlmacen);

            if (!$resultadoActualizar) {
                die("Error al actualizar la cantidad en el almacén: " . mysqli_error($conexion));
            }
            // Insertar la transacción en el historial
            $queryPagar = "INSERT INTO historial (Usuario, Prenda, Talla, Cantidad, Precio, Fecha)
            VALUES ('$usuarioId', '$CodigoPrenda', '$Talla', '$Cantidad', '$PrecioTotal', NOW())";
            $resultadoPagar = mysqli_query($conexion, $queryPagar);

            if (!$resultadoPagar) {
                die("Error al pagar: " . mysqli_error($conexion));
            } else {
                echo '
                <script>
                    alert("Compra realizada con éxito, su pago total fue de $' . number_format($PrecioTotal, 2) . '");
                    window.location = "PrendasUsuario.php?Codigo=' . $CodigoPrenda . '";
                </script>
                ';
                exit();
            }
        } else {
            echo '
            <script>
                alert("No hay la cantidad suficiente del producto seleccionado, lo sentimos.");
                window.location = "PrendasUsuario.php?Codigo=' . $CodigoPrenda . '";
            </script>
            ';
            exit();
        }
    } else {
        echo '
        <script>
            alert("Faltan datos en el formulario.");
            window.location = "PrendasUsuario.php";
        </script>
        ';
        exit();
    }
} 

// Cerrar la conexión a la base de datos
mysqli_close($conexion);
?>