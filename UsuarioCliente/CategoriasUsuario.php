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

    // Verificar si se ha pasado un parámetro 'tipo' en la URL
    $tipoFiltro = isset($_GET['tipo']) ? intval($_GET['tipo']) : 0;

    // Consulta para obtener todos los tipos o el tipo filtrado
    if ($tipoFiltro) {
        // Si hay un valor en $tipoFiltro, se filtra por el tipo específico
        $consultaTipo = "SELECT * FROM Tipo WHERE PK_Tipo = $tipoFiltro";
    } else {
        // Si no hay un valor en $tipoFiltro, se obtienen todos los tipos
        $consultaTipo = "SELECT * FROM Tipo";
    }

    $resultadoTipo = mysqli_query($conexion, $consultaTipo);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" integrity="sha512-MV7K8+y+gLIBoVD59lQIYicR65iaqukzvf/nwasF0nqhPay5w/9lJmVM2hMDcnK1OnMGCdVK+iQrJ7lzPJQd1w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../styleIndex.css">
    <link rel="stylesheet" href="../styleTipo.css">
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
                <a href="IndexUsuario.php" onclick="seleccionar()">Inicio</a>
                <a href="CategoriasUsuario.php" onclick="seleccionar()">Categorías</a>
                <a href="CarritoUsuario.php" onclick="seleccionar()">Carrito</a>
                <a href="HistorialUsuario.php" onclick="seleccionar()">Historial</a>
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

    <section class="Categorias">

        <div class="Contenido-Seccion">

            <?php while ($row = mysqli_fetch_assoc($resultadoTipo)): ?>
                    <div class="prendas-container">
                    <div class="titulo-tipo">
                        <h2 class="animable"><?= $row['Nombre'] ?></h2>
                    </div>
                    <hr>
                    <div class="box-container">
                        <?php 
                            $Tipo_id = $row['PK_Tipo'];
                            $queryPrendas = "SELECT * FROM Prenda WHERE Tipo = $Tipo_id";
                            $resultPrendas = mysqli_query($conexion, $queryPrendas);
                            if (!$resultPrendas) {
                                die("Error en la consulta de prendas: " . mysqli_error($conexion));
                            }
                            foreach ($resultPrendas as $prenda): 
                        ?>
                            <div class="box animable">
                                <div class="content">
                                    <a href="PrendasUsuario.php?Codigo=<?= $prenda['Codigo']?>">
                                        <img src="data:image/jpeg;base64,<?= base64_encode($prenda['Img']) ?>" alt="Imagen de prenda" class="img_prenda animable">
                                        <p class="animable"><?= $prenda['Nombre'] ?></p>
                                        <p class="precio animable">$<?= $prenda['Precio'] ?></p>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    </div>
                <?php endwhile; ?>
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
                    <li><a href="indexUsuario.php">Inicio</a></li>
                    <li><a href="CategoriasUsuario.php">Categorías</a></li>
                    <li><a href="CarritoUsuario.php">Carrito</a></li>
                    <li><a href="HistorialUsuario.php">Historial</a></li>
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
