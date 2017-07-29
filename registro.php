<?php
    /**
    * Es como un include
    */
    require 'funcs/conexion.php';
    include 'funcs/funcs.php';

    /**
     * Aquí vamos a ir colocando todos los errores
     */
    $errors = array();

    /**
     * Cuando nos envien un POST con el boton, lo validamos dentro del if
     */
    if(!empty($_POST))
    {
        /**
         * Aquí recogemos todos los elementos del formulario y lo guardamos dentro de variables.
         * Con la función "real_escape_string" limpiamos la cadena que recibimos del formulario
         * y así evitamos el sql_injection.
         */
        $nombre = $mysqli->real_escape_string($_POST['nombre']);
        $usuario = $mysqli->real_escape_string($_POST['usuario']);
        $password = $mysqli->real_escape_string($_POST['password']);
        $con_password = $mysqli->real_escape_string($_POST['con_password']);
        $email = $mysqli->real_escape_string($_POST['email']);
        $captcha = $mysqli->real_escape_string($_POST['g-recaptcha-response']);

        /**
         * $activo cuando registremos el usuario que siempre esté desactivado
         * $tipo_usuario indica los privilegios para este usuario, van a ser user estandar
         * $secret colocar la clave secreta del catch
         */
        $activo = 0;
        $tipo_usuario = 2;
        $secret = 'clave secreta de reCaptcha';//Modificar

        /**
         * A partir de aquí comenzamos con las validaciones de cada una de las variables
         * Como se puede ver las validaciones utilizan funciones ya implementadas en funcs.php
         */
        if(!$captcha){
            $errors[] = "Por favor verifica el captcha";
        }

        if(isNull($nombre, $usuario, $password, $con_password, $email))
        {
            $errors[] = "Debe llenar todos los campos";
        }

        if(!isEmail($email))
        {
            $errors[] = "Dirección de correo inválida";
        }

        if(!validaPassword($password, $con_password))
        {
            $errors[] = "Las contraseñas no coinciden";
        }

        if(usuarioExiste($usuario))
        {
            $errors[] = "El nombre de usuario $usuario ya existe";
        }

        if(emailExiste($email))
        {
            $errors[] = "El correo electronico $email ya existe";
        }

        /**
         * Una vez terminado con las validaciones vamos a mostrar los resultados
         * y guardarlo en la la base de datos.
         */
        if(count($errors) == 0)
        {
            /**
             * Validar el catcha directamente en google y verificar que sea correcto
             * colocalmos la url y enviamos el secret y el catch
             */
            $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secret&response=$captcha");

            /**
             * Cuando validomos el catcha nos retorna un objeto json
             */
            $arr = json_decode($response, TRUE);

            /**
             * Ahora verificamos que los datos que nos retorno google sean correctos
             * Si es correcto comenzamos a registrar al usuario.
             */
            if($arr['success'])
            {
                /**
                 * Utilizamos el método hash_passoword para cifrar la contraseña
                 * y guardarla en la base de datos.
                 */
                $pass_hash = hashPassword($password);
                /**
                 * Es un complemento para cifrar la contraseña, nos genera un valor
                 * dependiendo de la fecha y hora de nuestro sistema nos saca un identificador
                 * y lo pasa a md5. Este token es unico para cada uno de los usuarios
                 */
                $token = generateToken();

                /**
                 * Esta funcion es la encargada de registrar el usuario
                 * Recordar que no se envia el password del usuario sino el hash
                 * Esta función en caso que se registre el usuario nos devuelve el id
                 * del usuario registrado. En caso de error nos retorna cero.
                 */
                $registro = registraUsuario($usuario, $pass_hash, $nombre, $email, $activo, $token, $tipo_usuario);
                if($registro > 0)
                {
                    /**
                     * Ahora el siguiente paso es enviar la URL por el correo
                     * saca el nombre del servidor en el cual estamos
                     * se le dice que va al sistema de login
                     * y al script activar.php,
                     * a este script le vamos a enviar el id del registro
                     * y una varible valor que va a ser el token,
                     * para que así el usuario pueda validarse
                     */
                    $url = 'http://'.$_SERVER["SERVER_NAME"].'/login/activar.php?id='.$registro.'&val='.$token;

                    /**
                     * Ahora agremos el asunto y cuerpo para el correo electronico.
                     * cuerpo agremos el nombre del usuario que nos proporsionó
                     * y también la url que creamos. De esta manera se personaliza el
                     * correo electronico para cada uno de nuestros usuarios.
                     */
                    $asunto = 'Activar Cuenta - Sistema de Usuarios';
                    $cuerpo = "Estimado $nombre: <br /><br />Para continuar con el proceso de registro, es indispensable de click en la siguiente liga <a href='$url'>Activar Cuenta</a>";

                    /**
                     * La función 'enviarEmail' como todals las demás se pueden ver en
                     * el script funcs.php. Esta función en particular es la que envia
                     * el correo electronico. Esta función es la que llama la librería
                     * PHPMailer. Si se envia correctamente los retorna true, de lo
                     * en caso contrario false.
                     */
                    if(enviarEmail($email, $nombre, $asunto, $cuerpo)){

                        /**
                         * Se coloca un mensaje que se envio correctamente con instrucciones
                         * para iniciar su cueta
                         */
                        echo "Para terminar el proceso de registro siga las instrucciones que le hemos enviado la direccion de correo electronico: $email";
                        /**
                         * Este un link para iniciar sección
                         */
                        echo "<br><a href='index.php' >Iniciar Sesion</a>";
                        /**
                         * Una vez echa toda esta configuración con el 'exit' se corta
                         * el script y así no me muestre nuevamente el formulario. Si tiene un
                         * error entonces va a continuar hasta que todo este correcto
                         */
                        exit;

                        /**
                         * Ahora solamente nos queda mostrar los errores utilizando el
                         * la variable array '$erros'.
                         * En el script funcs.php hay un función que se llama 'resultBlock'
                         * el cual recibe los errores mediante un <div> con un estilo de
                         * bootstrap
                         */
                    } else {
                        $erros[] = "Error al enviar Email";
                    }

                } else {
                    $errors[] = "Error al Registrar";
                }

            } else {
                $errors[] = 'Error al comprobar Captcha';
            }
        }
    }
?>
<html>
	<head>
		<title>Registro</title>
		
		<link rel="stylesheet" href="css/bootstrap.min.css" >
		<link rel="stylesheet" href="css/bootstrap-theme.min.css" >
		<script src="js/bootstrap.min.js" ></script>
		<script src='https://www.google.com/recaptcha/api.js'></script>
	</head>
	
	<body>
		<div class="container">
			<div id="signupbox" style="margin-top:50px" class="mainbox col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
				<div class="panel panel-info">
					<div class="panel-heading">
						<div class="panel-title">Reg&iacute;strate</div>
						<div style="float:right; font-size: 85%; position: relative; top:-10px"><a id="signinlink" href="index.php">Iniciar Sesi&oacute;n</a></div>
					</div>  
					
					<div class="panel-body" >
						
						<form id="signupform" class="form-horizontal" role="form" action="<?php $_SERVER['PHP_SELF'] ?>" method="POST" autocomplete="off">
							
							<div id="signupalert" style="display:none" class="alert alert-danger">
								<p>Error:</p>
								<span></span>
							</div>
							
							<div class="form-group">
								<label for="nombre" class="col-md-3 control-label">Nombre:</label>
								<div class="col-md-9">
									<input type="text" class="form-control" name="nombre" placeholder="Nombre" value="<?php if(isset($nombre)) echo $nombre; ?>" required >
								</div>
							</div>
							
							<div class="form-group">
								<label for="usuario" class="col-md-3 control-label">Usuario</label>
								<div class="col-md-9">
									<input type="text" class="form-control" name="usuario" placeholder="Usuario" value="<?php if(isset($usuario)) echo $usuario; ?>" required>
								</div>
							</div>
							
							<div class="form-group">
								<label for="password" class="col-md-3 control-label">Password</label>
								<div class="col-md-9">
									<input type="password" class="form-control" name="password" placeholder="Password" required>
								</div>
							</div>
							
							<div class="form-group">
								<label for="con_password" class="col-md-3 control-label">Confirmar Password</label>
								<div class="col-md-9">
									<input type="password" class="form-control" name="con_password" placeholder="Confirmar Password" required>
								</div>
							</div>
							
							<div class="form-group">
								<label for="email" class="col-md-3 control-label">Email</label>
								<div class="col-md-9">
									<input type="email" class="form-control" name="email" placeholder="Email" value="<?php if(isset($email)) echo $email; ?>" required>
								</div>
							</div>
							
							<div class="form-group">
								<label for="captcha" class="col-md-3 control-label"></label>
								<div class="g-recaptcha col-md-9" data-sitekey="clave de reCaptcha"></div>
							</div>
							
							<div class="form-group">                                      
								<div class="col-md-offset-3 col-md-9">
									<button id="btn-signup" type="submit" class="btn btn-info"><i class="icon-hand-right"></i>Registrar</button> 
								</div>
							</div>
						</form>
                        <?php
                        /**
                         * Con esto hemos terminado nuestro registro
                         */
                            echo resultBlock($errors);
                        ?>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>													