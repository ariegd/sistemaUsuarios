<?php
/**
 * Created by PhpStorm.
 * User: Zodd
 * Date: 29/07/2017
 * Time: 07:01 PM
 */

    require 'funcs/conexion.php';
    include 'funcs/funcs.php';

    /**
     * Validamos que nos estén enviando los datos por un método GET
     * el id y el valor, este es el token
     */
    if(isset($_GET["id"]) AND isset($_GET['val']))
    {
        $idUsuario = $_GET['id'];
        $token = $_GET['val'];

        /**
         * Esta función va a verificar varias cosas:
         * 1. la activacion sea igual a cero
         * Si es igual a uno significa que alguien ya acivo la cuenta
         * sino llamamos a la función 'activarUsuario'
         * Lo único que hace es en la base de datos en el campo ´activacion´
         * lo coloca a uno
         */
        $mensaje = validaIdToken($idUsuario, $token);
    }
?>
<!-- Y aquí validamos el $mensaje -->
<html>
<head>
    <!-- cargamos las librerias de bootstrap -->
    <title>Registro</title>
    <link rel="stylesheet" href="css/bootstrap.min.css" >
    <link rel="stylesheet" href="css/bootstrap-theme.min.css" >
    <script src="js/bootstrap.min.js" ></script>

</head>

<body>
<!-- colocamos un poco de bootstrap -->
<div class="container">
    <div class="jumbotron">
        <!-- enviamos el mensaje -->
        <h1><?php echo $mensaje; ?></h1>

        <!-- y colocamos un link para que envie  al usuario para iniciar sesion -->
        <br />
        <p><a class="btn btn-primary btn-lg" href="index.php" role="button">Iniciar Sesi&oacute;n</a></p>
    </div>
</div>
</body>
</html>
