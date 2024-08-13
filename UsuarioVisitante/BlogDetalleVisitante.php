<?php
// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "", "ecomers");

if ($conexion->connect_error) {
    die("No se ha podido conectar al servidor de Base de datos: " . $conexion->connect_error);
}

// Obtener el ID del blog desde la URL, si está presente
$idBlogSeleccionado = isset($_GET['Id']) ? intval($_GET['Id']) : 0;

// Consulta para obtener publicaciones del blog
if ($idBlogSeleccionado > 0) {
    // Consulta para un blog específico
    $queryBlog = "SELECT Id, Titulo, Contenido, Img, Fecha FROM Blog WHERE Id = $idBlogSeleccionado";
} else {
    // Consulta para todos los blogs
    $queryBlog = "SELECT Id, Titulo, Contenido, Img, Fecha FROM Blog";
}

$resultadoBlog = mysqli_query($conexion, $queryBlog);

if (!$resultadoBlog || mysqli_num_rows($resultadoBlog) == 0) {
    die("No se encontró el blog.");
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
    <link rel="stylesheet" href="../styleBlogs.css">
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

        <section class="Blogs" id="">
            <div class="Contenido-Seccion">
                <?php while ($entrada = mysqli_fetch_assoc($resultadoBlog)): ?>
                    <div class="blog-item<?= $entrada['Id'] == $idBlogSeleccionado ? ' destacado' : '' ?>">
                        <div class="blog-item">
                            <div class="contenido">
                                <h3 class="animable"><?=  htmlspecialchars($entrada['Titulo'])?></h3>
                                <p class="animable"><?= nl2br(htmlspecialchars($entrada['Contenido'])) ?></p>
                            </div>
                            <div class="img-blog">
                                <img src="data:image/jpeg;base64,<?= base64_encode($entrada['Img']) ?>" alt="Imagen de blog" class="animable">
                            </div>
                        </div>
                    </div>

                <?php endwhile; ?>
            </div>
        </section>




    </main>


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
</body>
</html>