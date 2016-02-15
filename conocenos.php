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
		<title>Rebel Gamers Clan - Conócenos</title>
		<meta charset="utf-8" />
		<meta name="description" content="Conoce mejor a Rebel Gamers Clan">
		<meta name="keywords" content="rebel,gmaers,clan,comunidad,team,fortress,española,españa,steam,conocenos,informacion">
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
				<h1 class="titulo_conocenos">¿Qué es Rebel Gamers Clan?</h1>
				<p class="parrofo_conocenos"><b>Rebel Gamers Clan</b>, abreviado también a <b>RGC</b> es una comunidad española de <i>Team Fortress 2</i>, basada en la plataforma Steam.<br>
					Nuestros únicos objetivos son <strike>dominar el mundo</strike> pasar un buen rato jugando unas partiditas a nuestros juegos favoritos, echar unas risas y desconectar de todo lo demás.<br>
					Siempre intentamos cumplir esto basándonos en un ambiente de buen rollo y colegueo, dónde los usuarios puedan sentirse agusto y hacer amigos.
				</p>
				<br>
				<br>
				<h1 class="titulo_conocenos">¿Qué ofrece Rebel Gamers Clan?</h1>
				<p class="parrofo_conocenos">Lo que nos ha definido durante mucho tiempo son los <b>servidores de TF2</b> públicos que ofrecemos a toda la comunidad.<br>
				De entrada libre para cualquier usuario, intentamos que los jugadores que entren puedan gozar del juego tal como es, y al mismo tiempo, que puedan conocer a los miembros de la comunidad.<br>
				Además, proporcionamos algunas cositas más, como <b>servicios de voz</b> de TeamSpeak 3 o Mumble, también con las mismas bases de uso libre y abierto a todo el mundo.<br>
				Si quieres conocer los detalles de todos nuestros servicios, consulta <a href="/servicios.php">la sección de 'Servicios'</a> de la web.
				</p>
				<br>
				<br>
				<h1 class="titulo_conocenos">¿Quién está detrás de RGC?</h1>
				<p class="parrofo_conocenos">Podríamos decir que detrás de RGC hay <b>decenas de jugadores y miembros</b>, los cuáles aportan su granito de arena para que la comunidad siga activa y vaya prosperando.<br>
				Pero si preguntas por los administradores... supongo que te refieres a <a href="http://steamcommunity.com/id/thedrdvd">DR.DVD</a> y <a href="http://steamcommunity.com/id/thedrdvd">Dark_Trainer</a>.<br>
				Son además los fundadores del grupo, formado durante sus años mozos de instituto, allá por el 2009. No es casualidad que a día de hoy sigan siendo amigos (y conciudadanos de la mágica ciudad de Ripollet).<br>
				En parte, esperamos que gracias a RGC este no sea sólo nuestro caso: quizás también pueda ser el tuyo y conozcas amigos con los que estarás en contacto durante muchos años.
				</p>
				<br>
				<br>
				<h1 class="titulo_conocenos">¿Y qué hay del oscuro pasado del grupo?</h1>
				<p class="parrofo_conocenos">Durante tantos años, se podría decir que los miembros de RGC hemos vivido <b>muchas aventuras y experiencias</b>. Por fortuna, casi todo bueno.<br>
				Sin embargo, si eres un cotilla y no puedes resistir en remover los escombros de nuestro pasado, hemos preparado la <a href="/historia.php">sección de 'Historia'</a> del grupo, para que puedas chafardear muchas de las peripecias y cosas que nos definían en el pasado.<br>
				Hemos pasado por muchas cosas y, en parte, eso es lo que nos hace tener la <b>voluntad de seguir adelante</b>. ¡Tú también puedes formar parte de la historia del grupo!
				</p>
				<br>
				<br>
				<h1 class="titulo_conocenos">¿Cómo has hecho esta página?</h1>
				<p class="parrofo_conocenos">La verdad es que se lo debo todo a <strike>los sobornos</strike> los consejos que me han ofrecido ciertas personas, cómo Jack Webster, Capu o Cúspide.<br>
				También hay gente, cómo Drawer, que han aportado de forma significativa a Rebel Gamers Clan, creando el logotipo tan chachi que podéis ver.<br>
				Sin embargo, si lo que te interesa es como funciona a "nivel informático", siempre puedes consultar <a href="https://github.com/THEDRDVD/rgc-web">este repositorio de GitHub</a>, con la mayor parte del código de la web.
				</p>
			</section>
			<?php include('zona_lateral.php'); ?>
		</div>
		<?php include('pie_pagina.php'); ?>
	</body> 
</html>