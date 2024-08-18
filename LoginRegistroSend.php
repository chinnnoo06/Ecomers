<?php
session_start();

// Conexión a la base de datos
$conexion = mysqli_connect("localhost", "root", "", "ecomers");

if (!$conexion) {
    die("No se ha podido conectar al servidor de Base de datos: " . mysqli_connect_error());
}

if (isset($_POST['Nombre']) && isset($_POST['Apellido']) && isset($_POST['Correo_Electronico']) && isset($_POST['Password'])) {
    $Nombre = $_POST['Nombre'];
    $Apellido = $_POST['Apellido'];
    $Correo = $_POST['Correo_Electronico'];
    $Password = $_POST['Password'];
    $Password = hash('sha512', $Password);

    if (isset($_FILES['Img']['tmp_name']) && $_FILES['Img']['tmp_name'] != "") {
        $Img = addslashes(file_get_contents($_FILES['Img']['tmp_name']));
    } else {
        $rutaImagenDefecto = __DIR__ . "/IMG/Usuario.png";
        $Img = addslashes(file_get_contents($rutaImagenDefecto));
    }

    // Llamada al procedimiento almacenado para verificar el correo
    $sql_verificar_correo = "CALL VerificacionCorreo(?, @correo_existente)";
    $stmt = mysqli_prepare($conexion, $sql_verificar_correo);
    mysqli_stmt_bind_param($stmt, "s", $Correo);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // Recuperar el resultado del procedimiento almacenado
    $result_verificar_correo = mysqli_query($conexion, "SELECT @correo_existente AS correo_existente");
    $row_verificar_correo = mysqli_fetch_assoc($result_verificar_correo);
    $correo_existente = (bool)$row_verificar_correo['correo_existente'];

    // Verificar si el correo existe
    if ($correo_existente) {
        echo '
        <script>
            alert("Este correo ya ha sido registrado");
            window.history.back();
        </script>
        ';
        exit();
    }

    $consulta = "INSERT INTO Usuario (Nombre, Apellido, Correo, Img, Password)
                 VALUES ('$Nombre', '$Apellido', '$Correo', '$Img', '$Password')";

    $resultado = mysqli_query($conexion, $consulta);

    if ($resultado) {
        echo '
        <script>
            alert("Se ha registrado exitosamente");
            window.history.back();
        </script>
        ';
    } else {
        echo '
        <script>
            alert("Algo falló, inténtelo de nuevo");
            window.history.back();
        </script>
        ';
    }
} elseif (isset($_POST['Correo_Electronico']) && isset($_POST['Password'])) {
    $correo = $_POST['Correo_Electronico'];
    $password = $_POST['Password'];
    $password = hash('sha512', $password);

    $consulta = "SELECT * FROM Usuario WHERE Correo='$correo' AND Password='$password'";
    $resultado = mysqli_query($conexion, $consulta);
    
    $filas = mysqli_fetch_array($resultado);
    
    if ($filas) {
        $_SESSION['usuario'] = $correo;
        $usuarioId = $filas['ID'];  // Aquí obtienes el ID del usuario
    
        // Buscar el carrito existente del usuario
        $queryBuscarCarrito = "SELECT PK_Carrito FROM Carrito WHERE Usuario = '$usuarioId'";
        $resultadoCarrito = mysqli_query($conexion, $queryBuscarCarrito);
        $carritoUsuario = mysqli_fetch_assoc($resultadoCarrito);
        
        if ($carritoUsuario) {
            // Si el carrito existe, obtener el ID del carrito
            $carritoId = $carritoUsuario['PK_Carrito'];
        } else {
            // Si no existe un carrito, crear uno nuevo
            $queryCrearCarrito = "INSERT INTO Carrito (Usuario) VALUES ('$usuarioId')";
            mysqli_query($conexion, $queryCrearCarrito);
            $carritoId = mysqli_insert_id($conexion);
        }
    
        // Transferir artículos del carrito del visitante al carrito del usuario
        if (isset($_SESSION['carrito_visitante']) && !empty($_SESSION['carrito_visitante'])) {
            $carritoVisitante = $_SESSION['carrito_visitante'];
    
            foreach ($carritoVisitante as $item) {
                $codigoPrenda = $item['Codigo'];
                $talla = $item['Talla'];
                $cantidad = $item['Cantidad'];
                $precio = $item['Total'];
    
                $queryAgregarPrenda = "INSERT INTO Carrito_Prenda (Carrito, Prenda, Talla, Cantidad, Precio) VALUES ('$carritoId', '$codigoPrenda', '$talla', '$cantidad', '$precio')";
                mysqli_query($conexion, $queryAgregarPrenda);
            }
    
            // Limpiar el carrito del visitante
            unset($_SESSION['carrito_visitante']);
        }
    
        // Redirigir según el tipo de usuario
        if ($filas['Tipo_Usuario'] == 'Administrador') {
            header("Location:UsuarioAdmin/indexAdmin.php");
        } elseif ($filas['Tipo_Usuario'] == 'Regular') {
            header("Location:UsuarioCliente/indexUsuario.php");
        }
        exit;
    } else {
        echo '
        <script>
            alert("Error al iniciar sesión, inténtelo de nuevo");
            window.history.back();
        </script>
        ';
        exit;
    }
}

mysqli_close($conexion);
?>
