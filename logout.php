<?php
/**
 * Created by PhpStorm.
 * User: Zodd
 * Date: 31/07/2017
 * Time: 09:16 PM
 *
 * Este scrpt es muy necesario para cerrar la session, ya hay que cerrarla
 * sino siempre nos muestra la pagina de la session abierta
 */

    session_start(); // abrimos la session
    session_destroy(); // y destruimos la session

    /**
     * Y redireccionamos al index.php
     */
    header('location: index.php');