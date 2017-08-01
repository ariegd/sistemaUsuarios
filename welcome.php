<?php
    /**
     * Created by PhpStorm.
     * User: Zodd
     * Date: 31/07/2017
     * Time: 08:53 PM
     */

    /**
     * para iniciar una session o reanudar una session que y este activa
     * si no colocamos esta linea puede que no nos permita utilizar las variables
     * de session creadas
     *
     * En otras palabras es una restriccion que permite ver la pagian solo
     * los usuarios que ahigan iniciado session
     */
    session_start();
    require 'funcs/conexion.php';
    require 'funcs/funcs.php';

    /**
     * Si la variable de session no esta declarada o es nula o vacia lo redireccionamos
     * a index.php. De esta manera aunque el usuaria conozca la URL no permita hasta que
     * no se inicie session
     */
    if(!isset($_SESSION["id_usuario"])){
        header("Location: index.php");
    }

    $idUsuario = $_SESSION['id_usuario'];

    $sql = "SELECT id, nombre FROM usuarios WHERE id = '$idUsuario'";
    $result = $mysqli->query($sql);

    $row = $result->fetch_assoc(); //Envia un mensaje de bienvenido en pantalla y muestra su nombre
?>

<!-- Falta el HTML que tenemos que sacarlo del sitio -->
<html>
<head>
    <title>Welcome</title>
    <link rel="stylesheet" href="css/bootstrap.min.css" >
    <link rel="stylesheet" href="css/bootstrap-theme.min.css" >
    <script src="js/bootstrap.min.js" ></script>

    <style>
        body {
            padding-top: 20px;
            padding-bottom: 20px;
        }
    </style>
</head>

<body>
<div class="container">

    <nav class='navbar navbar-default'>
        <div class='container-fluid'>
            <div class='navbar-header'>
                <button type='button' class='navbar-toggle collapsed' data-toggle='collapse' data-target='#navbar' aria-expanded='false' aria-controls='navbar'>
                    <span class='sr-only'>Men&uacute;</span>
                    <span class='icon-bar'></span>
                    <span class='icon-bar'></span>
                    <span class='icon-bar'></span>
                </button>
            </div>

            <div id='navbar' class='navbar-collapse collapse'>
                <ul class='nav navbar-nav'>
                    <li class='active'><a href='welcome.php'>Inicio</a></li>
                </ul>

                <?php if($_SESSION['tipo_usuario']==1) { ?>
                    <ul class='nav navbar-nav'>
                        <li><a href='#'>Administrar Usuarios</a></li>
                    </ul>
                <?php } ?>

                <ul class='nav navbar-nav navbar-right'>
                    <li><a href='logout.php'>Cerrar Sesi&oacute;n</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="jumbotron">
        <h2><?php echo 'Bienvenid@ '.utf8_decode($row['nombre']); ?></h2>
            <br />
    </div>
</div>
</body>
</html>