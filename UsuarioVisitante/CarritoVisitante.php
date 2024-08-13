<?php
session_start(); // Iniciar la sesión

// Conexión a la base de datos
$conexion = mysqli_connect("localhost", "root", "", "ecomers");
if (!$conexion) {
    die("No se ha podido conectar al servidor de Base de datos: " . mysqli_connect_error());
}

// Inicializar el total a pagar
$totalPagar = 0;

// Verificar si hay artículos en el carrito
$carritoItems = isset($_SESSION['carrito_visitante']) ? $_SESSION['carrito_visitante'] : [];

if (!empty($carritoItems)) {
    foreach ($carritoItems as $item) {
        $totalPagar += $item['Total'];
    }
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
    <link rel="stylesheet" href="../styleCarrito.css">
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
                <div class="nav-responsive" id="icono-var" onclick="mostrarOcultarMenu()">
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

        <!-- CARRITO -->
        <section class="carrito">
            <div class="carrito-contenido">
                <div class="carrito-header">
                    <h2>Mi Carrito</h2>
                    <?php if (count($carritoItems) > 0): ?>
                        <div class="botones">
                            <form action="VaciarCarrito.php" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas vaciar tu carrito?');">
                                <button type="submit" class="btn-vaciar">Vaciar Carrito<i class="fa-solid fa-cart-shopping"></i></button>
                            </form>
                            <form action="../LoginRegistro.php" method="POST" onsubmit="return confirm('Tienes que iniciar sesión para realizar la compra, ¿Quieres iniciar sesión?');">
                                <button type="submit" class="btn-Pagar">Pagar<i class="fa-solid fa-credit-card"></i></button>
                            </form>
                            <a href="CategoriasVisitante.php"><button type="submit" class="btn-agregar">Agregar productos<i class="fa-solid fa-plus"></i></button></a>
                        </div>
                    <?php endif; ?>
                </div>
                <?php if (count($carritoItems) > 0): ?>
                    <ul class="lista-carrito">
                        <?php foreach ($carritoItems as $item):
                            $codigoPrenda = $item['Codigo'];
                            $queryPrenda = "SELECT Nombre, Precio, Img FROM Prenda WHERE Codigo = '$codigoPrenda'";
                            $resultadoPrenda = mysqli_query($conexion, $queryPrenda);
                            $talla = $item['Talla'];
                            $cantidad = $item['Cantidad'];
                            $precio = $item['Total'];

                            if ($resultadoPrenda) {
                                $prenda = mysqli_fetch_assoc($resultadoPrenda);
                                $nombrePrenda = $prenda['Nombre'];
                                $imgPrenda = $prenda['Img'];
                            }
                        ?>
                        <a href="PrendasVisitante.php?Codigo=<?=$item['Codigo']?>">
                            <li class="item-carrito">
                                <img src="data:image/jpeg;base64,<?= base64_encode($imgPrenda) ?>" alt="Imagen de prenda" class="img_prenda animable">
                                <div class="detalle-item">
                                    <h3 class="animable"><?= $nombrePrenda ?></h3>
                                    <p class="animable"><strong>Talla:</strong> <?= $talla ?></p>
                                    <p class="animable"><strong>Cantidad:</strong> <?= $cantidad ?></p>
                                    <p class="animable"><strong>Precio:</strong> $<?= number_format($precio, 2) ?></p>
                                    <form action="EliminarDelCarrito.php" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas eliminar esta prenda?');">
                                        <input type="hidden" name="Codigo" value="<?= $codigoPrenda ?>">
                                        <input type="hidden" name="Talla" value="<?= $talla ?>">
                                        <button type="submit" class="animable">Eliminar</button>
                                    </form>
                                </div>
                            </li>
                        </a>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <div class="carrito-vacio">
                        <i class="fa-solid fa-cart-shopping"></i>
                        <p>Tu carrito está vacío.</p>
                        <a href="CategoriasVisitante.php"><button type="submit" class="btn-agregar">Ver productos</button></a>
                    </div>
                <?php endif; ?>
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
                        <li><a href="indexVisitante.php">Inicio</a></li>
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
    </main>
</body>
</html>
