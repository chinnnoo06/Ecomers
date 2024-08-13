<?php
    session_start();

    if(!isset($_SESSION['usuario'])){
        echo '
        <script>
            alert("Por favor, inicie sesión para realizar este proceso");
            window.location = "../LoginRegistro.php";
        </script>
        ';
        session_destroy();
        die();
    }

?>