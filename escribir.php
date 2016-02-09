<?php
// Incluímos 'funciones.php' para llamar a las funciones correspondientes cuando sea necesario.
include('funciones.php');
// Incluímos 'steamauth.php' para las funciones de login/logout a Steam, y 'settings.php' para datos como la API key.
include('steamauth/steamauth.php');
include('steamauth/settings.php');
// Incluímos 'datosbd.php' para conseguir los datos de conexión a la base de datos.
include('datosbd.php');

// Comprobamos si ya nos hemos conectado.
$conectado_steam = conectadoSteam();
if ($conectado_steam) {
	// Incluímos 'userInfo.php' para conseguir los datos de Steam del usuario conectado.
	include('steamauth/userInfo.php');
}

// Variable para comprobar si ha habido fallos a la hora de conectarse a la BD.
$error_bd = false;

// Variable para comprobar si estamos en la página de publicaciones, la cuál activa el formulario de búsqueda.
$es_pagina_publicaciones = false;
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Rebel Gamers Clan - Escribir publicación</title>
		<meta charset="utf-8" />
		<link rel="icon" type="image/png" href="./img/logo_rgc01.png">
		<!-- Carga el CSS de RGC -->
		<link rel="stylesheet" type="text/css" href="./rgc.css">
	</head>
	<body>
		<div id="cabecera">
			<?php include('cabecera.php'); ?>
		</div>
		<div id="cuerpo_pagina">
			<section id="zona_principal">
			<?php
			if ($conectado_steam) {
				// Se intenta crear la conexión a la BD para leer las publicaciones y los comentarios
				try {
					@$con_bd = new mysqli($datosbd['conexion'], $datosbd['usuario1'], $datosbd['contra1'], $datosbd['nombre_bd']);
				// En caso de fallo, se muestra un mensaje de error, y se cambia el valor de la variable $error_bd a 'true' para que no intente usar la BD.
				} catch (Exception $e) {
					echo '<div class="notificacion_error"><b>Ha fallado la conexión con la base de datos.</b> Vuelve más tarde, a ver si se ha podido solucionar el problema.</div>';
					$error_bd = true;
				}
				
				if (!$error_bd) {
					prepararConexionUTF8($con_bd);
					
					$tipoUsuario = comprobarPermisosUsuario($con_bd, $steamprofile['steamid']);
					if ($tipoUsuario == -1) {
						echo '<div class="notificacion_error"><b>Ha habido un problema comprobando los permisos de tu usuario actual.</b>' .
						' Vuelve más tarde, a ver si se ha podido solucionar el problema.</div>';
					} else if ($tipoUsuario == 1 || $tipoUsuario == 2) {
						paginaEscribir($con_bd, $steamprofile['steamid']);
					} else {
						echo '<div class="notificacion_advertencia"><b>No tienes suficientes permisos para escribir textos.</b></div>';
					}
					
					// Cerramos la conexión tras terminar de consultar los permisos sobre el usuario.
					mysqli_close($con_bd);
				}
			// Si no estamos conectados, nos dice que primero hay que conectarse a Steam.
			} else {
				echo '<div class="notificacion_advertencia"><b>Tienes que estar conectado a Steam a través de la página para poder escribir textos.</b></div>';
			}
			?>
			</section>
			<?php include('zona_lateral.php'); ?>
		</div>
		<?php include('pie_pagina.php'); ?>
	</body>
</html>