<?php

// Conexión a la base de datos
$conexion = mysqli_connect("localhost", "root", "", "ecomers");
if (!$conexion) {
    die("No se ha podido conectar al servidor de Base de datos: " . mysqli_connect_error());
}

// Verificar si el parámetro 'Codigo' está presente en la URL
if (isset($_GET['Codigo'])) {
    $CodigoPrenda = $_GET['Codigo'];

    // Consulta para obtener los detalles de la prenda
    $query = "SELECT * FROM DetallesPrenda WHERE Codigo = '$CodigoPrenda'";
    $resultado = mysqli_query($conexion, $query);
    if (!$resultado) {
        die("Error en la consulta: " . mysqli_error($conexion));
    }
    $prenda = mysqli_fetch_assoc($resultado);

    // Consulta para obtener las opiniones de la prenda
    $queryOpiniones = "SELECT * FROM OpinionesPrenda WHERE Prenda = '$CodigoPrenda'";
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

    // Consulta para obtener las tallas disponibles
    $queryTallasDisponibles = "SELECT Talla, Nombre FROM prenda_talla 
        INNER JOIN Talla ON prenda_talla.Talla = Talla.PK_Talla 
        WHERE Prenda = '$CodigoPrenda' AND Cantidad > 0";
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

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" integrity="sha512-MV7K8+y+gLIBoVD59lQIYicR65iaqukzvf/nwasF0nqhPay5w/9lJmVM2hMDcnK1OnMGCdVK+iQrJ7lzPJQd1w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../styleIndex.css">
    <link rel="stylesheet" href="../stylePrenda.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <title>TODOMODA</title>
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
                    <a href="indexVisitante.php" onclick="seleccionar()">Inicio</a>
                    <a href="CategoriasVisitante.php" onclick="seleccionar()">Categorías</a>
                    <a href="CarritoVisitante.php" onclick="seleccionar()">Carrito</a>
                    <a href="HistorialVisitante.php" onclick="seleccionar()">Historial</a>
                </nav>
                <div class="nav-responsive"id="icono-var" onclick="mostrarOcultarMenu()">
                    <i class="fa-solid fa-bars"></i>
                </div>
                <div class="busqueda">
                <form action="BuscarUsuario.php" method="GET" class="busqueda-form">
                    <input type="text" name="query" placeholder="Buscar..." class="busqueda-input">
                    <button type="submit" class="busqueda-btn">
                        <i class="fa fa-search"></i>
                    </button>
                </form>
                </div>
                <div class="perfil">
                    <img src="../IMG/Usuario.png" alt="Imagen de perfil" class="img_perfil" onclick="toggleProfileMenu()">
                    <div class="perfil-info">
                        <div id="profileMenu" class="perfil-links hidden">
                            <a href="../LoginRegistro.php">Iniciar Sesión</a>
                        </div>
                    </div>
                </div> 
            </header>
        </div>

    <section class="Prenda">
        <div class="detalles-prenda">
            <div class="ImgPrenda">
                <img src="data:image/jpeg;base64,<?= base64_encode($prenda['Img']) ?>" alt="Imagen de prenda"  class="animable">
            </div>
            <div class="DatosPrenda">
                <h2 class="animable"><?= $prenda['Nombre'] ?></h2>
                <p class="animable"><strong>Codigo:</strong> <?= $prenda['Codigo'] ?></p>
                <p class="animable"><strong>Precio: $</strong> <?= $prenda['Precio'] ?></p>
                <p class="animable"><strong>Promedio de calificación:</strong> <?= is_numeric($promedioCalificaciones) ? number_format($promedioCalificaciones, 1) : $promedioCalificaciones ?> <i class="fa-solid fa-star"></i></p> 
                <div class="btn">
                    <form action="AgregarCarritoVisitante.php" method="POST" class="formulario">
                        <div class="opciones-talla">
                            <?php foreach ($tallasDisponibles as $talla): ?>
                                <button type="button" class="boton-talla animable" value="<?= $talla['Talla'] ?>" data-talla="<?= $talla['Talla'] ?>"><?= $talla['Nombre']?></button>
                            <?php endforeach; ?>
                        </div>
                        <input type="hidden" name="Talla" id="Talla" required>
                        <div class="cantidad">
                            <label for="Cantidad"  class="animable">Cantidad:</label>
                            <input type="number" name="Cantidad"  class="animable" id="Cantidad" required min="1">
                        </div>
                        <input type="hidden" name="Precio"  class="animable" value="<?= $prenda['Precio'] ?>">
                        <input type="hidden" name="Codigo"  class="animable" value="<?= $prenda['Codigo'] ?>">
                        <button type="submit" class="boton-agregar animable">Agregar al Carrito</button>
                    </form>
                    <form action="Alerta.php" method="POST" id="pagarAhoraForm" onsubmit="return confirm('La cantidad a pagar total es de: <?= number_format($totalPagar, 2) ?>');">
                        <button type="submit" class="boton-Pagar animable">Pagar Ahora</button>
                        <input type="hidden" name="Codigo" value="<?= $prenda['Codigo'] ?>">
                        <input type="hidden" name="Talla" id="TallaPagar">
                        <input type="hidden" name="Cantidad" id="CantidadPagar">
                        <input type="hidden" name="Precio" value="<?= $prenda['Precio'] ?>">
                    </form>
                </div>
            </div>
        </div>
    </section>

    <section class="Calificacion" id="Calificacion">
        <div class="contenido-Calificacion">
            <h3 class="animable">Agrega una calificación</h3>
            <form action="Alerta.php" method="POST">
                <div class="form-group">
                    <label for="calificacion" class="animable">Calificación:</label>
                    <div class="rating">
                        <input type="radio" id="star5" name="calificacion"  class="animable" value="5" required>
                        <label for="star5" title="5 estrellas" class="animable"><i class="fa-solid fa-star"></i></label>
                        <input type="radio" id="star4" name="calificacion" class="animable" value="4" required>
                        <label for="star4" title="4 estrellas" class="animable"><i class="fa-solid fa-star"></i></label>
                        <input type="radio" id="star3" name="calificacion" class="animable" value="3" required>
                        <label for="star3" title="3 estrellas" class="animable"><i class="fa-solid fa-star"></i></label>
                        <input type="radio" id="star2" name="calificacion" class="animable" value="2" required>
                        <label for="star2" title="2 estrellas" class="animable"><i class="fa-solid fa-star"></i></label>
                        <input type="radio" id="star1" name="calificacion" class="animable" value="1" required>
                        <label for="star1" title="1 estrella" class="animable"><i class="fa-solid fa-star"></i></label>
                    </div>
                </div>
                <div class="form-group">
                    <label for="comentario" class="animable">Comentario:</label>
                    <textarea name="comentario" class="animable" id="comentario" rows="4" required></textarea>
                </div>
                <input type="hidden" name="codigo_prenda" value="<?= $prenda['Codigo'] ?>">
                <div class="form-group">
                    <button type="submit" class="animable">Enviar calificación</button>
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
                    <li><a href="#inicio">Inicio</a></li>
                    <li><a href="CategoriasVisitante.php">Categorías</a></li>
                    <li><a href="CarritoVisitante.php">Carrito</a></li>
                    <li><a href="HistorialVisitante.php">Historial</a></li>
                    <li><a href="../LoginRegistro.php">Iniciar Sesión</a></li>
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