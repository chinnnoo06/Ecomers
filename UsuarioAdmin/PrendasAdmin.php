<?php
    session_start();

    if(!isset($_SESSION['usuario'])){
        echo '
        <script>
            alert("Por favor, inicie sesión");
            window.location = "../LoginRegistro.php";
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

    // Verificar si el parámetro 'Codigo' está presente en la URL
    if (isset($_GET['Codigo'])) {
        $CodigoPrenda = $_GET['Codigo'];

        // Consulta a la vista para obtener los detalles de la prenda
        $query = "SELECT * FROM DetallesPrenda WHERE Codigo = $CodigoPrenda";
        $resultado = mysqli_query($conexion, $query);

        if (!$resultado) {
            die("Error en la consulta: " . mysqli_error($conexion));
        }

        $prenda = mysqli_fetch_assoc($resultado);

        // Consulta a la vista para obtener las opiniones de la prenda
        $queryOpiniones = "SELECT * FROM OpinionesPrenda WHERE Prenda = $CodigoPrenda";
        $resultadoOpiniones = mysqli_query($conexion, $queryOpiniones);

        if (!$resultadoOpiniones) {
            die("Error en la consulta de opiniones: " . mysqli_error($conexion));
        }

        $opiniones = [];
        $totalCalificaciones = 0;
        $numCalificaciones = 0;

        while ($opinion = mysqli_fetch_assoc($resultadoOpiniones)) {
            $opiniones[] = $opinion;
            $totalCalificaciones += $opinion['Calificacion'];
            $numCalificaciones++;
        }

        if ($numCalificaciones > 0) {
            $promedioCalificaciones = $totalCalificaciones / $numCalificaciones;
        } else {
            $promedioCalificaciones = "Sin calificaciones";
        }

        // Consulta a la vista para obtener las tallas disponibles
        $queryTallasDisponibles = "SELECT Talla, Nombre, Cantidad FROM prenda_talla 
        INNER JOIN Talla ON prenda_talla.Talla = Talla.PK_Talla 
        WHERE Prenda = $CodigoPrenda AND Cantidad > 0";
        $resultadoTallasDisponibles = mysqli_query($conexion, $queryTallasDisponibles);

        if (!$resultadoTallasDisponibles) {
            die("Error en la consulta de tallas disponibles: " . mysqli_error($conexion));
        }

        $tallasDisponibles = [];
        while ($talla = mysqli_fetch_assoc($resultadoTallasDisponibles)) {
            $tallasDisponibles[] = $talla;
        }
    } else {
        die("No se ha especificado una prenda.");
    }

    // Obtener el correo del usuario desde la sesión
    $correoUsuario = $_SESSION['usuario'];

    // Consulta para obtener los detalles del usuario, incluyendo el id
    $queryUsuario = "SELECT Nombre, Apellido, Correo, Img, id FROM Usuario WHERE Correo = '$correoUsuario'";
    $resultadoUsuario = mysqli_query($conexion, $queryUsuario);

    if (!$resultadoUsuario) {
        die("Error en la consulta de usuario: " . mysqli_error($conexion));
    }

    $usuario = mysqli_fetch_assoc($resultadoUsuario);

    if (isset($usuario['id'])) {
        $usuarioId = $usuario['id'];
        $nombre = $usuario['Nombre'];
        $apellido = $usuario['Apellido'];
        $imgData = $usuario['Img'];
    } else {
        die("No se pudo obtener el ID del usuario.");
    }
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" integrity="sha512-MV7K8+y+gLIBoVD59lQIYicR65iaqukzvf/nwasF0nqhPay5w/9lJmVM2hMDcnK1OnMGCdVK+iQrJ7lzPJQd1w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../styleIndex.css">
    <link rel="stylesheet" href="../stylePrendaAdmin.css">
    <title>TODOMODA</title>
    <style>
    .hidden {
        display: none;
    }
    </style>
</head>
<body>
    <main>
        <!-- MENU -->
        <div class="contenedor-header">
            <header>
                <div class="logo">
                    <h1>TODO<span class="txtAzul">MODA</span></h1>
                </div>
                <nav id="nav">
                    <a href="IndexAdmin.php" onclick="seleccionar()">Inicio</a>
                    <a href="CategoriasAdmin.php" onclick="seleccionar()">Categorías</a>
                    <a href="AlmacenAdmin.php" onclick="seleccionar()">Almacén</a>
                    <a href="UsuariosAdmin.php" onclick="seleccionar()">Usuarios</a>
                </nav>
                <div class="nav-responsive"id="icono-var" onclick="mostrarOcultarMenu()">
                    <i class="fa-solid fa-bars"></i>
                </div>
                <div class="busqueda">
                <form action="BuscarAdmin.php" method="GET" class="busqueda-form">
                    <input type="text" name="query" placeholder="Buscar..." class="busqueda-input">
                    <button type="submit" class="busqueda-btn">
                        <i class="fa fa-search"></i>
                    </button>
                </form>
                </div>
                <div class="perfil">
                    <img src="data:image/jpeg;base64,<?= base64_encode($imgData) ?>" alt="Imagen de perfil" class="img_perfil" onclick="toggleProfileMenu()">
                    <div class="perfil-info">
                        <div id="profileMenu" class="perfil-links hidden">
                            <p><strong>¡Hola! <?= $nombre ?></strong></p>
                            <a href="Editar_Perfil.php">Editar Perfil</a>
                            <a href="../cerrar_sesion.php">Cerrar Sesión</a>
                        </div>
                    </div>
                </div> 
            </header>
        </div>

    <section id="Prenda" class="Prenda">
        <div class="detalles-prenda">
            <div class="ImgPrenda">
                <img src="data:image/jpeg;base64,<?= base64_encode($prenda['Img']) ?>" alt="Imagen de prenda" class="animable">
            </div>
            <div class="DatosPrenda">
                <h2 class="animable"><?= $prenda['Nombre'] ?></h2>
                <p class="animable"><strong>Codigo:</strong> <?= $prenda['Codigo'] ?></p>
                <p class="animable"><strong>Precio: $</strong> <?= $prenda['Precio'] ?></p>
                <p class="animable"><strong>Promedio de calificación:</strong> <?= is_numeric($promedioCalificaciones) ? number_format($promedioCalificaciones, 1) : $promedioCalificaciones ?> <i class="fa-solid fa-star"></i></p>
                <p><strong>Tallas Disponibles:</strong></p>
                <div class="tallas-disponibles">
                    <?php foreach ($tallasDisponibles as $talla): ?>
                        <div class="tallas">
                            <p><strong><?= $talla['Nombre'] ?>:</strong> <?= $talla['Cantidad']?></p>
                        </div>
                    <?php endforeach; ?>
                </div> 
                <div class="btn">
                    <a href="PrendaHistorial.php?Codigo=<?= $prenda['Codigo'] ?>"><button type="button" class="boton-almacen">Ver historial</button></a>
                    <button type="button" class="boton-editar animable" onclick="editarPrenda(<?= $prenda['Codigo'] ?>)">Editar Prenda</button>
                    <form action="EliminarPrenda.php" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas eliminar esta prenda?');">
                        <input type="hidden" name="Codigo" value="<?= $prenda['Codigo'] ?>">    
                        <button type="submit" class="boton-eliminar animable">Eliminar Prenda</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <section id="Editar-Prenda" class="form-editar-prenda hidden">
        <div class="contenedor-form">
            <div class="volver">
                <a href="#" onclick="mostrarSeccionPrenda('Prenda'); return false;"><i class="fa-solid fa-arrow-left"></i></a>
            </div>
            <form action="EditarPrenda.php" method="POST" enctype="multipart/form-data" id="form-agregar-prenda">
                <input type="hidden" name="Codigo" id="Codigo">
                <h3>Editar Prenda</h3>
                <div class="form-group">
                    <label for="Codigo">CODIGO:</label>
                    <input type="text" name="IdCodigo" required>
                </div>
                <div class="form-group">
                    <label for="Nombre">NOMBRE:</label>
                    <input type="text" name="Nombre" required>
                </div>
                <div class="form-group">
                    <label for="Nombre">PRECIO:</label>
                    <input type="number" name="Precio" min="0" step="0.01" required>
                </div>
                <div class="form-group">
                    <label for="Tipo">TIPO:</label>
                    <select id="Tipo" name="Tipo" required>
                        <?php
                            $Genero = mysqli_query($conexion,"SELECT * from Tipo");

                            while($row = mysqli_fetch_array($Genero)){
                                $selected = ($row['PK_Tipo'] == $prenda['Tipo']) ? 'selected' : '';
                                echo "<option value='{$row['PK_Tipo']}' $selected>{$row['PK_Tipo']} - {$row['Nombre']}</option>";
                            }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="file" class="custom-file-upload">
                        <i class="fa fa-cloud-upload"></i> EDITA LA FOTO DE LA PRENDA
                    </label>
                    <input type="file" id="file" name="Img" accept="image/*"> 
                    <p id="file-message" class="file-message">Archivo seleccionado: Ninguno</p>
                    </div>
                <div class="form-tallas">
                    <div class="tallas">
                        <input type="hidden" name="Talla_XS" value=1>
                        <label for="XS">XS:</label>
                        <input type="number" name="XS" min="0"required>
                    </div>
                    <div class="tallas">
                        <input type="hidden" name="Talla_S" value=2>
                        <label for="S">S:</label>
                        <input type="number" name="S" min="0" required>
                    </div>
                    <div class="tallas">
                        <input type="hidden" name="Talla_M" value=3>
                        <label for="M">M:</label>
                        <input type="number" name="M" min="0" required>       
                    </div>
                    <div class="tallas">
                        <input type="hidden" name="Talla_L" value=4>
                        <label for="L">L:</label>
                        <input type="number" name="L" min="0" required>
                    </div>
                    <div class="tallas">
                        <input type="hidden" name="Talla_XL" value=5>
                        <label for="XL">XL:</label>
                        <input type="number" name="XL" min="0" required>
                    </div>
                    <div class="tallas">
                        <input type="hidden" name="Talla_Unitalla" value=6>
                        <label for="Unitalla">Unitalla:</label>
                        <input type="number" name="Unitalla" min="0" required>
                    </div>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn-subir">ACTUALIZAR PRENDA</button>
                </div>
            </form>
        </div>
    </section>


    <section class="Opiniones">
        <div class="contenido-opiniones">
            <h3  class="animable">Opiniones de los usuarios</h3>
            <ul>
                <?php foreach ($opiniones as $opinion): ?>
                    <li class="opinion-item">
                        <div class="Usuario">
                            <p class="animable"><i class="fa-solid fa-user"></i> <?= $opinion['UsuarioNombre'] ?> <?= $opinion['UsuarioApellido'] ?></p>
                        </div>
                        <p class="animable"><strong>Calificación:</strong> <?= $opinion['Calificacion'] ?> <i class="fa-solid fa-star"></i></p>
                        <p class="animable"><strong>Opinión:</strong> <?= $opinion['Comentario'] ?></p>
                        <form action="EliminarCalificacionAdmin.php" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas eliminar esta calificación?');">
                            <input type="hidden" name="id_opinion" value="<?= $opinion['PK_Cal'] ?>">
                            <input type="hidden" name="codigo_prenda" value="<?= $CodigoPrenda ?>">
                            <button type="submit" class="btn-eliminar" class="animable">Eliminar</button>
                        </form>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </section>

    <footer>
        <div class="info">
            <div class="seccion">
                <h4>Sobre Nosotros</h4>
                <p>Somos una tienda de moda comprometida con ofrecer productos de alta calidad a precios accesibles. Nuestro objetivo es que todos encuentren su estilo perfecto.</p>
            </div>
            <div class="seccion">
                <h4>Enlaces Rápidos</h4>
                <ul>
                    <li><a href="indexAdmin.php">Inicio</a></li>
                    <li><a href="CategoriasAdmin.php">Categorías</a></li>
                    <li><a href="AlmacenAdmin.php">Almacén</a></li>
                    <li><a href="UsuariosAdmin.php">Usuarios</a></li>
                    <li><a href="Editar_Perfil.php">Editar Perfil</a></li>
                    <li><a href="../cerrar_sesion.php">Cerrar Sesión</a></li>
                </ul>
            </div>
            <div class="seccion">
                <h4>Contacto</h4>
                <p>2024 - <span class="txtAzul">TODOMODA</span> Todos los derechos reservados</p>
                <p>Teléfono: <span class="txtAzul">3318237277</span></p>
                <p>Email: <span class="txtAzul">contacto@todamoda.com</span></p>
            </div>
            <div class="seccion">
                <h4>Redes Sociales</h4>
                <div class="redes-sociales">
                    <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#"><i class="fa-brands fa-twitter"></i></a>
                    <a href="#"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#"><i class="fa-brands fa-linkedin-in"></i></a>
                </div>
            </div>
        </div>
    </footer>
    <script src="../Archivos js/menu-responsive.js"></script>
    <script src="../Archivos js/loginregistro.js"></script>
    <script src="../Archivos js/animaciones.js"></script>

</body>
</html>