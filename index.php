<?php
    session_start(); // al momento de iniciar session pueda generar nuestras variables de session
    require 'funcs/conexion.php';
    require 'funcs/funcs.php';

    /**
     * Utilizamos al igual que en el registro un arreglo para ir guardando todos
     * los errores que tengamos.
     */
    $errors = array();

    /**
     * Y vamos a validar lo que el usuario envia en el $_POST. Se puede observar
     * en el HTML que se envia la informacon asi mismo mediante el $_SERVER['PHP_SELF']
     */
    if(!empty($_POST)){
        $usuario = $mysqli->real_escape_string($_POST['usuario']);
        $password = $mysqli->real_escape_string($_POST['password']);

        /**
         * Ahora validamos que los campos no sean nulos mediante la función
         * 'isNullLogin'
         */
        if(isNullLogin($usuario, $password)){
            $errors = "Debe llenar todos los campos";
        }

        /**
         * Ya ahora implementamos el inicio de sesion mediante la función 'login',
         * es la que se encarga de todo practicamente, es decir la validacion
         */
        $errors[] = login($usuario, $password);
    }

?>
<html>
	<head>
		<title>Login</title>
		
		<link rel="stylesheet" href="css/bootstrap.min.css" >
		<link rel="stylesheet" href="css/bootstrap-theme.min.css" >
		<script src="js/bootstrap.min.js" ></script>
		
	</head>
	
	<body>
		
		<div class="container">    
			<div id="loginbox" style="margin-top:50px;" class="mainbox col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">                    
				<div class="panel panel-info" >
					<div class="panel-heading">
						<div class="panel-title">Iniciar Sesi&oacute;n</div>
						<div style="float:right; font-size: 80%; position: relative; top:-10px"><a href="recupera.php">¿Se te olvid&oacute; tu contraseña?</a></div>
					</div>     
					
					<div style="padding-top:30px" class="panel-body" >
						
						<div style="display:none" id="login-alert" class="alert alert-danger col-sm-12"></div>
						
						<form id="loginform" class="form-horizontal" role="form" action="<?php $_SERVER['PHP_SELF'] ?>" method="POST" autocomplete="off">
							
							<div style="margin-bottom: 25px" class="input-group">
								<span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
								<input id="usuario" type="text" class="form-control" name="usuario" value="" placeholder="usuario o email" required>                                        
							</div>
							
							<div style="margin-bottom: 25px" class="input-group">
								<span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
								<input id="password" type="password" class="form-control" name="password" placeholder="password" required>
							</div>
							
							<div style="margin-top:10px" class="form-group">
								<div class="col-sm-12 controls">
									<button id="btn-login" type="submit" class="btn btn-success">Iniciar Sesi&oacute;n</button>
								</div>
							</div>
							
							<div class="form-group">
								<div class="col-md-12 control">
									<div style="border-top: 1px solid#888; padding-top:15px; font-size:85%" >
										No tiene una cuenta! <a href="registro.php">Registrate aquí</a>
									</div>
								</div>
							</div>    
						</form>
                        <?php //Esto es para mostrar los errores
                            echo resultBlock($errors);
                        ?>
					</div>                     
				</div>  
			</div>
		</div>
	</body>
</html>				