<?php
session_start();

if (!isset($_SESSION['usuario'])) {
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
$conexion = new mysqli("localhost", "root", "", "ecomers");

if ($conexion->connect_error) {
    die("No se ha podido conectar al servidor de Base de datos: " . $conexion->connect_error);
}

// Obtener el correo del usuario desde la sesión
$correoUsuario = $_SESSION['usuario'];

// Consulta para obtener los detalles del usuario
$queryUsuario = "SELECT Nombre, Apellido, Correo, Img FROM Usuario WHERE Correo = '$correoUsuario'";
$resultadoUsuario = mysqli_query($conexion, $queryUsuario);

if (!$resultadoUsuario) {
    die("Error en la consulta de usuario: " . mysqli_error($conexion));
}

$usuario = mysqli_fetch_assoc($resultadoUsuario);

$nombre = $usuario['Nombre'];
$apellido = $usuario['Apellido'];
$imgData = $usuario['Img'];

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
                <div class="nav-responsive" id="icono-var" onclick="mostrarOcultarMenu()">
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

        <section class="Blogs" id="Blogs">
            <div class="Contenido-Seccion">
                <?php while ($entrada = mysqli_fetch_assoc($resultadoBlog)): ?>
                    <div class="blog-item<?= $entrada['Id'] == $idBlogSeleccionado ? ' destacado' : '' ?>">
                        <div class="blog-item">
                            <div class="contenido">
                                <h3 class="animable"><?=  htmlspecialchars($entrada['Titulo'])?></h3>
                                <p class="animable"><?= nl2br(htmlspecialchars($entrada['Contenido'])) ?></p>
                            </div>
                            <div class="fila">
                                <div class="botones">
                                    <button type="button" class="animable" onclick="editarBlog(<?= $entrada['Id'] ?>)">Editar Blog</button>
                                    <form action="EliminarBlog.php" method="POST" class="formulario">
                                        <input type="hidden" name="id_blog" value="<?= $entrada['Id'] ?>">
                                        <button type="submit" class="animable">Eliminar Blog</button>
                                    </form>
                                </div>
                                <div class="img-blog">
                                    <img src="data:image/jpeg;base64,<?= base64_encode($entrada['Img']) ?>" alt="Imagen de blog" class="animable">
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </section>

        <!-- Editar Blog -->
        <section id="editar-blog" class="form-editar-blog hidden">
            <div class="contenedor-form">
                <div class="volver">
                    <a href="#" onclick="mostrarSeccionBlogs('Blogs'); return false;"><i class="fa-solid fa-arrow-left"></i></a>
                </div>
                <form action="EditarBlog.php" method="POST" enctype="multipart/form-data" id="form-editar-blog">
                    <input type="hidden" name="Id" id="blog-id">
                    <h3>EDITAR BLOG</h3>
                    <div class="form-group">
                        <label for="Titulo">TITULO:</label>
                        <input type="text" name="Titulo" required>
                    </div>
                    <div class="form-group">
                        <label for="comentario">CONTENIDO:</label>
                        <textarea name="Contenido" id="Contenido" rows="20" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="file" class="custom-file-upload">
                            <i class="fa fa-cloud-upload"></i> Actualizar la foto del blog
                        </label>
                        <input type="file" id="file" name="Img" accept="image/*"> 
                        <p id="file-message" class="file-message">Archivo seleccionado: Ninguno</p>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn-subir">SUBIR BLOG</button>
                    </div>
                </form>
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