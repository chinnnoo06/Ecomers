<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" integrity="sha512-MV7K8+y+gLIBoVD59lQIYicR65iaqukzvf/nwasF0nqhPay5w/9lJmVM2hMDcnK1OnMGCdVK+iQrJ7lzPJQd1w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href=styleLoginRegistro.css>
    <title>TODOMODA</title>
</head>
<body>
    <main>
        <div class="contenedor_todo">

            <div class="caja_trasera">
                <div class="caja_trasera-login">
                    <h3>¿Ya tienes una cuenta?</h3>
                    <p>Inicia sesión para entrar a la página</p>
                    <button id="btn_iniciar-sesión">Iniciar Sesión</button>
                </div>
                <div class="caja_trasera-registro">
                    <h3>¿Aún no tienes una cuenta?</h3>
                    <p>Regístrate para entrar a la página</p>
                    <button id="btn_registrarse">Registrarse</button>
                </div>
            </div>

            <div class="contenedor_login-registro">

                <form action="LoginRegistroSend.php" method="POST" class="formulario_login">

                    <h2>Iniciar Sesión</h2>
                    <input type="email" placeholder="Correo Electronico" name="Correo_Electronico" required>
                    <input type="password" placeholder="Contraseña" name="Password" required>
                    <button>Entrar</button>

                </form>

                <form action="LoginRegistroSend.php" method="POST" class="formulario_registro" enctype="multipart/form-data">

                    <h2>Registrarse</h2>
                    <input type="text" placeholder="Nombre" name="Nombre" required>
                    <input type="text" placeholder="Apellido" name="Apellido" required>
                    <input type="email" placeholder="Correo Electronico" name="Correo_Electronico" required>
                    <input type="password" placeholder="Contraseña" name="Password" required>
                    <label for="file" class="custom-file-upload">
                        <i class="fa fa-cloud-upload"></i> Elige una foto de perfil
                    </label>
                    <input type="file" id="file" name="Img" accept="image/*"> 
                    <p id="file-message" class="file-message"></p>
                    
                    <button>Registrarse</button>

                </form>

            </div>

        </div>
    </main>
    <script src="Archivos js/loginregistro.js"></script>
    <script>
        document.getElementById('file').addEventListener('change', function() {
            var fileMessage = document.getElementById('file-message');
            if (this.files && this.files.length > 0) {
                fileMessage.textContent = 'Foto de perfil cargada con éxito.';
                fileMessage.style.color = 'green';
            } else {
                fileMessage.textContent = '';
            }
        });
    </script>

</body>
</html>