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
		<title>Rebel Gamers Clan - Error 404</title>
		<meta charset="utf-8" />
		<meta name="description" content="Página de Error 404 de Rebel Gamers Clan">
		<meta name="keywords" content="rebel,gamers,clan,comunidad,team,fortress,española,españa,steam,error">
		<meta name="author" content="DR.DVD">
		<link rel="icon" href="/img/favicon.ico">
		<!-- Carga el CSS de RGC -->
		<link rel="stylesheet" type="text/css" href="/rgc.css">
	</head>
	<body>
		<div id="cabecera">
			<?php include('cabecera.php'); ?>
		</div>
		<div id="cuerpo_pagina">
			<section id="zona_principal">
				<div class="notificacion_error"><b>ERROR 404</b>: La página que buscas no existe. Me parece que te has perdido, Caperucita.</div>
			</section>
			<?php include('zona_lateral.php'); ?>
		</div>
		<div style="clear: both;"></div>
		<?php include('pie_pagina.php'); ?>
	</body> 
</html>