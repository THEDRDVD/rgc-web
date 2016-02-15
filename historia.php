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
		<title>Rebel Gamers Clan - Historia</title>
		<meta charset="utf-8" />
		<meta name="description" content="Historia resumida de Rebel Gamers Clan">
		<meta name="keywords" content="rebel,gmaers,clan,comunidad,team,fortress,española,españa,steam,historia">
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
				<div id="prologo_historia">
					<h1 id="titulo_prologo_historia">LA HISTORIA DE REBEL GAMERS CLAN</h1>
					<p id="parrofo_prologo_historia"><i>Estás a punto de descubrir los más oscuros y antiguos secretos de RGC, que tiene la fortuna de mantenerse a día de hoy desde 2009.<br>
					¿Qué dices? ¿Qué si salen dinosaurios en la historia? Pues no, lo siento... No, tampoco salen memes de John Cena.<br>
					¿Qué pasa? ¿Quieres hacerme quedar mal con mi historia? Calla y sigue leyendo.</i></p>
				</div>
				<h1 class="titulo_conocenos">El principio de todo</h1>
				<div class="caja_imagen_parrafo_derecha" style="width: 240px;">
					<img src="img/historia_imagen01.jpg" style="width: 240px;"><br>
					<span><i>Antes de centrarse en TF2, en Rebel Gamers Clan se jugaba principalmente a HL2:DM y GMOD.</i></span>
				</div>
				<p class="parrofo_conocenos">Una de las primeras cosas que tienes que saber es quiénes fueron los fundadores originales de RGC: <a href="http://steamcommunity.com/id/thedrdvd">DR.DVD</a>, <a href="http://steamcommunity.com/id/darktrainer">Dark_Trainer</a> (los cuales siguen siendo administradores a día de hoy), y <a href="http://steamcommunity.com/profiles/76561198011499014/">Carlos-Snake</a>.<br>
				Antes de crear el grupo que se llamaría <b>Rebel Gamers Clan</b>, pasamos por diversos nombres y grupos diferentes. <b><a href="http://steamcommunity.com/groups/sourcespanishplayers">Source Spanish Players</a></b> es uno de ellos. Los otros son un tanto cutres y penosos como para tenerlos en cuenta.<br>
				Inicialmente, el grupo que pretendíamos crear iba a estar basado no sólo en <i>Team Fortress 2</i>, sino <b>también en <i>Half-Life 2: Deathmatch</i> y <i>Garry's Mod</i></b>.<br>
				Por aquél entonces, cuando eramos unos "pringaillos" con apenas un duro, teníamos muy pocos juegos a los que jugar, y HL2:DM se <a href="http://steamcommunity.com/groups/sourcespanishplayers#announcements/detail/91350424324658786">podía conseguir gratis</a> a través de una oferta de tarjetas gráficas NVidia o ATI. Además, al ser unos fans puros y duros del universo de <i>Half-Life 2</i>, pasamos muy buenos ratos en su versión multijugador.<br>
				Finalmente, TF2 se acabó imponiendo al resto de juegos que frecuentabamos, y decidimos centrarnos principalmente en este título.
				</p>
				<br>
				<br clear="both">
				<h1 class="titulo_conocenos">Los inicios de RGC</h1>
				<div class="caja_imagen_parrafo_derecha" style="width: 180px;">
					<img src="img/historia_imagen02.jpg"style="width: 180px;"><br>
					<span><i>Team Focata fue uno de los primeros 'clanes' de TF2 en el que el objetivo era el buen rollito y el colegueo.</i></span>
				</div>
				<p class="parrofo_conocenos">Durante el camino que hemos recorrido hasta el día de hoy, hemos hecho algunas cositas, como esta <a href="http://rebelgamersclan.jimdo.com/">antigua página web</a>, ya un tanto cutre y desactualizada, pero que aún sigue en pie, por el bien de nuestra historia. Supongo...<br>
				También hemos ido evolucionando a la hora de ofrecer servicios: inicialmente usabamos Hamachi para jugar entre nosotros, luego pasamos a usar HLDSUpdateTool... desde uno de nuestros PC caseros.<br>
				Considero que el primer servidor en condiciones que recibimos fue uno del <b>grupo <a href="http://steamcommunity.com/groups/scavengersa">SCAVENGAR S.A.</a></b>, y ese pasó a ser <b>el primer servidor que contratamos</b>, y el primero que tenía el privilegio de estar abierto 24/7 y no tener un lag equiparable a jugar en Marte.<br>
				Junto al servidor que ofrecíamos, teníamos que pensar un sistema de reclutamiento para conseguir nuevos miembros. Por aquél entonces, eramos miembros del <b>grupo <a href="http://steamcommunity.com/groups/team_focata">Team Focata</a></b>, en el que nos basamos para crear un sistema de reclutamiento similar al que usaban ellos. En realidad, le debemos muchísimo a este grupo: tenían un gran afán por querer hacer un grupo de amigos en que prevalezca el <b>buen rollo y el compañerismo</b>, algo que también quisimos que se hiciera en Rebel Gamers Clan.<br>
				</p>
				<br>
				<br clear="both">
				<h1 class="titulo_conocenos">El foro de Rebel Gamers Clan</h1>
				<p class="parrofo_conocenos"><a href="http://steamcommunity.com/groups/rebelgamersclan_viejo#announcements/detail/72233592414717422">En abril de 2010</a>, se creó el primer foro del grupo, de la mano de por aquél entonces uno de los administradores, Kraquen. La dirección del foro creado entonces, ya no está disponible, ya que nos cambiamos al servicio de ForoActivo, y a la <b>dirección <a href="http://rebelgamersclan.foroactivo.com/">rebelgamersclan.foroactivo.com</a></b>.</p>
				<div class="caja_imagen_parrafo_izquierda" style="width: 220px;">
					<img src="img/historia_imagen03.jpg"style="width: 220px;"><br>
					<span><i>El foro de Rebel Gamers Clan marcó una de las etapas más importantes del grupo, con un importante crecimiento.</i></span>
				</div>
				<p class="parrofo_conocenos">Desde finales de 2010, hasta casi 2015, el <b>foro de Rebel Gamers Clan fue nuestra "base de operaciones"</b>, y punto de reunión común (junto al servidor de <i>Team Fortress 2</i>), en el que centralizábamos todo lo que hacía el grupo.<br>
				Publicábamos noticias, eventos, quedadas, hacíamos spam... También ofrecíamos a los nuevos miembros un sitio en el que presentarse y unirse al grupo de forma oficial.<br>
				A través de un <b>sistema de pruebas</b>, evaluabamos a los miembros, en base a su participación, su relación con los demás, etc. Este modelo era uno casi igual que el que empezó a usar originalmente Team Focata (otra cosa más que le debemos).<br>
				Hicimos muchas cosas juntos: torneos, eventos, charlas en Skype, quedadas para conocernos en persona, vídeos de presentación del grupo...<br>
				Finalmente, llegando a finales de 2014, debido a un declive en la actividad general, tomamos una decisión: decidimos cerrar el foro y empezar una reforma que abarcaría varios aspectos del grupo.
				</p>
				<br>
				<br clear="both">
				<h1 class="titulo_conocenos">Reforma y estado actual de RGC</h1>
				<div class="caja_imagen_parrafo_derecha" style="width: 220px;">
					<img src="img/historia_imagen04.jpg"style="width: 220px;"><br>
					<span><i>Pese a los cambios que se han ido haciendo al grupo, seguimos haciendo los mismos eventos y partidas, centrándonos en que todo el mundo se pueda divertir.</i></span>
				</div>
				<p class="parrofo_conocenos">El grupo empezó a reformarse y ampliar sus servicios a partir de 2014. Cambiamos el modelo del grupo de clan "privado" a una <b>comunidad abierta</b>: los usuarios se unen de forma simbólica, y era decisión suya cuanto quieren aportar al grupo, y qué servicios quieren aprovechar.<br>
				Una de las cosas que más marcó el camino por el que trabajar fue la adquisición de un <b>servidor dedicado</b>, en lugar de contratar los servicios de TF2 a una empresa en particular. Al tener un servidor del cual tenemos un control casi total, podemos ofrecer muchas más cosas y configurarlas a nuestra manera, pudiendo hacer cosas que hasta entonces no podíamos.<br>
				Entre los servicios que decidimos ofrecer incluímos <b>más de un servidor de TF2</b>, <b>servicios de voz</b> (Mumble y TeamSpeak) y la tan anticipada <b>página web</b>, la cuál estás viendo ahora mismo.<br>
				Mantenemos un coste más o menos estable, y hemos llegado a un punto de equilibrio muy bueno, en el que aún podemos explorar qué más ofrecer a la comunidad española, sin tener que realizar grandes gastos.<br>
				Pero olvidando ahora el punto de vista técnico/informático, seguimos intentando cumplir con lo que nos propusimos: tener una comunidad en la que podamos jugar al <i>Team Fortress 2</i> entre amigos, muchas risas y buenos momentos.<br>
				Digan lo que digan, en Rebel Gamers Clan sentimos una <b>gran pasión por este juego y por gente que hace de esta una comunidad excepcional</b>. Osea que esperamos que consideras formar parte de nuestro grupo, y podamos ofrecerte un lugar en el que te sientas como en casa.
				</p>
			</section>
			<?php include('zona_lateral.php'); ?>
		</div>
		<?php include('pie_pagina.php'); ?>
	</body> 
</html>