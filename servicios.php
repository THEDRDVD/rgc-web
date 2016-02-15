<?php
// Incluímos 'funciones.php' para llamar a las funciones correspondientes cuando sea necesario.
include('funciones.php');
// Incluímos 'steamauth.php' para las funciones de login/logout a Steam, y 'settings.php' para datos como la API key.
include('steamauth/steamauth.php');
include('steamauth/settings.php');

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
		<title>Rebel Gamers Clan - Servicios</title>
		<meta charset="utf-8" />
		<meta name="description" content="Servicios ofrecidos por Rebel Gamers Clan">
		<meta name="keywords" content="rebel,gamers,clan,comunidad,team,fortress,española,españa,steam,servicios">
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
				<h1 class="titulo_tipo_servicio"><u>SERVIDORES DE JUEGO</u></h1>
				<p class="desc_tipo_servicio">El plato principal: este es el tipo de servicio que más usamos, y lo que nos da identidad dentro de la comunidad. ¡Añade los servidores a tus favoritos!</p>
				<div class="bloque_servicio">
					<div class="subbloque_servicio"><img src="/img/servicio_tf2.png" width="60px"></div>
					<div class="subbloque_servicio">
						<h1 class="elemento_subbloque_servicio">[ESP] Rebel Gamers Clan | Público</h1>
						<p class="elemento_subbloque_servicio"><b>Dirección:</b> <a href="steam://connect/rebelgamersclan.es:27015" target="_BLANK">rebelgamersclan.es:27015</a></p>
					</div>
					<div class="subbloque_servicio_desc">
						<p class="elemento_subbloque_servicio">He aquí lo que nos ha definido durante mucho tiempo como lo que somos: el <b>servidor público de 24 espacios</b>, abierto las 24 horas del día, 7 días a la semana.
						Disfruta de la esencia de <i>Team Fortress 2</i>, sin modificaciones ni <i>plugins</i> que interfieran en la jugabilidad clásica, y con una rotación de mapas compuesta por sólo mapas oficiales del juego.
						Nos gusta charlar y reírnos juntos, osea que tenemos el <i>alltalk</i> activado. Y si eres de los que tiene su propia opinión sobre los críticos aleatorios, siempre puedes votar al principio de la partida, con el sistema de votación para críticos aleatorios que tenemos configurado.</p>
					</div>
				</div>
				<div class="bloque_servicio">
					<div class="subbloque_servicio"><img src="/img/servicio_tf2.png" width="60px"></div>
					<div class="subbloque_servicio">
						<h1 class="elemento_subbloque_servicio">[ESP] Rebel Gamers Clan | Mann vs. Machine</h1>
						<p class="elemento_subbloque_servicio"><b>Dirección:</b> <a href="steam://connect/rebelgamersclan.es:27016" target="_BLANK">rebelgamersclan.es:27016</a></p>
					</div>
					<div class="subbloque_servicio_desc">
						<p class="elemento_subbloque_servicio">Servidor destinado a <b>partidas del modo <i>Mann vs. Machine</i></b>, con espacio para 6 jugadores, y con un propósito similar al servidor público: hacer que los jugadores disfruten de una experiencia lo más fiel posible al juego original.
						No te preocupes de que sea un servidor de MvM: puedes añadir este servidor a favoritos sin problema y <b>entrar directamente, sin necesidad de ticket</b> o sistema de <i>matchmaking</i>.
						¡Dale caña a esos robots!</p>
					</div>
				</div>
				<br>
				<h1 class="titulo_tipo_servicio"><u>SERVICIOS DE VOZ</u></h1>
				<p class="desc_tipo_servicio">Si eres de los que no te callas ni debajo del agua (como yo) tenemos un par de servicios de voz a tu disposición.</p>
				<div class="bloque_servicio">
					<div class="subbloque_servicio"><img src="/img/servicio_ts3.png" width="60px"></div>
					<div class="subbloque_servicio">
						<h1 class="elemento_subbloque_servicio">Canal de TeamSpeak 3</h1>
						<p class="elemento_subbloque_servicio"><b>Dirección:</b> <a href="ts3server://rebelgamersclan.es:9987/" target="_BLANK">rebelgamersclan.es:9987</a></p>
					</div>
					<div class="subbloque_servicio_desc">
						<p class="elemento_subbloque_servicio">Ofrecemos un servidor para hasta <b>32 usuarios simultáneos</b>, con <b>10 salas públicas</b> y la posibilidad de crear <b>salas temporales</b> para uso propio.</p>
					</div>
				</div>
				<div class="bloque_servicio">
					<div class="subbloque_servicio"><img src="/img/servicio_mumble02.png" width="60px"></div>
					<div class="subbloque_servicio">
						<h1 class="elemento_subbloque_servicio">Canal de Mumble</h1>
						<p class="elemento_subbloque_servicio"><b>Dirección:</b> <a href="mumble://rebelgamersclan.es:64738/" target="_BLANK">rebelgamersclan.es:64738</a></p>
					</div>
					<div class="subbloque_servicio_desc">
						<p class="elemento_subbloque_servicio">Con espacio para <b>hasta 100 usuarios</b>, este servicio de voz está destinado a dar salas de voz para <b>juegos por equipos</b>, tipo <i>Team Fortress 2</i>.</p>
					</div>
				</div>
				<br>
				<h1 class="titulo_tipo_servicio"><u>REDES SOCIALES</u></h1>
				<p class="desc_tipo_servicio">No dudes en echar un vistazo a todas nuestras redes sociales para estar enterado de todas las novedades sobre el grupo.</p>
				<div class="bloque_servicio">
					<div class="subbloque_servicio"><a href="http://steamcommunity.com/groups/rebelgamersclan" target="_BLANK"><img src="/img/servicio_steam.png" width="60px"></a></div>
					<div class="subbloque_servicio">
						<h1 class="elemento_subbloque_servicio"><a href="http://steamcommunity.com/groups/rebelgamersclan" target="_BLANK">Página de Steam</a></h1>
						<p class="elemento_subbloque_servicio">Grupo público de <b>Rebel Gamers Clan</b>, desde el cuál se publican <b>anuncios y eventos</b>.</p>
					</div>
				</div>
				<div class="bloque_servicio">
					<div class="subbloque_servicio"><a href="http://twitter.com/RebelGamersClan" target="_BLANK"><img src="/img/servicio_twitter.png" width="60px"></a></div>
					<div class="subbloque_servicio">
						<h1 class="elemento_subbloque_servicio"><a href="http://twitter.com/RebelGamersClan" target="_BLANK">Twitter</a></h1>
						<p class="elemento_subbloque_servicio">Cuenta de Twitter de <b>RGC</b>, con <b>enlaces a las últimas noticias</b> y novedades para estar al día con el grupo.</p>
					</div>
				</div>
				<div class="bloque_servicio">
					<div class="subbloque_servicio"><a href="http://facebook.com/RebelGamersClan" target="_BLANK"><img src="/img/servicio_facebook.png" width="60px"></a></div>
					<div class="subbloque_servicio">
						<h1 class="elemento_subbloque_servicio"><a href="http://facebook.com/RebelGamersClan" target="_BLANK">Facebook</a></h1>
						<p class="elemento_subbloque_servicio">Además de las últimas noticias, la página de Facebook de <b>RGC</b> también sirve para <b>compartir contenido</b> como fotografías de quedadas, viajes, encuentros...</p>
					</div>
				</div>
				<div class="bloque_servicio">
					<div class="subbloque_servicio"><a href="http://youtube.com/user/RebelGamersClan" target="_BLANK"><img src="/img/servicio_youtube.png" width="60px"></a></div>
					<div class="subbloque_servicio">
						<h1 class="elemento_subbloque_servicio"><a href="http://youtube.com/user/RebelGamersClan" target="_BLANK">YouTube</a></h1>
						<p class="elemento_subbloque_servicio">Aunque no tenemos un canal de cientos de suscriptores ni contenido frecuente, puedes encontrar más de un <b>vídeo de nuestros peripecias</b> en nuestro canal de YouTube.</p>
					</div>
				</div>
				<br>
				<h1 class="titulo_tipo_servicio"><u>OTROS SERVICIOS</u></h1>
				<p class="desc_tipo_servicio">Aquí se agrupan otros servicios ofrecidos que no corresponden a ninguna categoría particular.</p>
				<div class="bloque_servicio">
					<div class="subbloque_servicio"><a href="https://github.com/THEDRDVD/rgc-web" target="_BLANK"><img src="/img/servicio_github.png" width="60px"></a></div>
					<div class="subbloque_servicio">
						<h1 class="elemento_subbloque_servicio">rgc-web (Repositorio de GitHub)</h1>
						<p class="elemento_subbloque_servicio"><b>Dirección:</b> <a href="https://github.com/THEDRDVD/rgc-web" target="_BLANK">https://github.com/THEDRDVD/rgc-web</a></p>
					</div>
					<div class="subbloque_servicio_desc">
						<p class="elemento_subbloque_servicio">En este repositorio puedes encontrar la mayor parte del <b>código de esta página web</b>, escrito en PHP. Hemos decidido compartirlo en caso de que alguien sienta curiosidad por conocer el funcionamiento de la página, ya sea a nivel de HTML, PHP o CSS. El código está escrito de una forma más o menos legible, y dispone de muchos comentarios como referencia para saber lo que hace cada cosa.</p>
					</div>
				</div>
				<div class="bloque_servicio">
					<div class="subbloque_servicio"><a href="http://latostadora.com/rebelgamersclan" target="_BLANK"><img src="/img/servicio_tostadora.png" width="60px"></a></div>
					<div class="subbloque_servicio">
						<h1 class="elemento_subbloque_servicio"><a href="http://latostadora.com/rebelgamersclan" target="_BLANK">Tienda de LaTostadora.com</a></h1>
						<p class="elemento_subbloque_servicio">Quizás te resulte curioso saber que tenemos una pequeña sección de tienda propia en LaTostadora.com, una popular página española, que imprime camisetas con diseños personalizados. La camiseta con el logotipo de RGC, creado por Drawer, es irrestible, tanto por su precio como por su fabulosidad.</p>
					</div>
				</div>
			</section>
			<?php include('zona_lateral.php'); ?>
		</div>
		<?php include('pie_pagina.php'); ?>
	</body> 
</html>