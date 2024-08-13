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

    <section class="Categorias" id="Categorias">
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
                                    <a href="PrendasAdmin.php?Codigo=<?= $prenda['Codigo']?>">
                                        <img src="data:image/jpeg;base64,<?= base64_encode($prenda['Img']) ?>" alt="Imagen de prenda" class="img_prenda animable">
                                        <p class="animable"><?= $prenda['Nombre'] ?></p>
                                        <p class="precio animable">$<?= $prenda['Precio'] ?></p>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <div class="box animable Agregar">
                            <div class="content">
                                <p class="animable">Agregar Prenda</p>
                                <button type="button" class="animable" onclick="agregarPrenda(<?= $row['PK_Tipo'] ?>)"><i class="fa-solid fa-plus"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </section>

    <section id="Agregar-Prenda" class="form-prenda hidden">
        <div class="contenedor-form">
            <div class="volver">
                <a href="#" onclick="mostrarSeccionCategorias('Categorias'); return false;"><i class="fa-solid fa-arrow-left"></i></a>
            </div>
            <form action="AgregarPrenda.php" method="POST" enctype="multipart/form-data" id="form-agregar-prenda">
                <input type="hidden" name="PK_Tipo" id="PK_Tipo">
                <h3>Agregar Prenda</h3>
                <div class="form-group">
                    <label for="Codigo">CODIGO:</label>
                    <input type="text" name="Codigo" required>
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
                    <input type="text" name="Tipo" readonly required>
                </div>
                <div class="form-group">
                    <label for="file" class="custom-file-upload">
                        <i class="fa fa-cloud-upload"></i> SUBE LA FOTO DE LA PRENDA
                    </label>
                    <input type="file" id="file" name="Img" accept="image/*" required> 
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
                    <button type="submit" class="btn-subir">AGREGAR PRENDA</button>
                </div>
            </form>
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
                    <li><a href="CategoriasUsuario.php">Categorías</a></li>
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
