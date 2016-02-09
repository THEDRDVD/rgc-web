<aside id="zona_lateral">
	<h2 class="titulo_seccion_zona_lateral">CONEXIÓN A STEAM</h2>
	<div id="boton_steam">
		<div>
			<?php
				// Si se ha intentando acceder a la página a través de Steam, pero ha habido un problema, lo indicaremos aquí.
				if (isset($_SESSION['error_conectando'])) {
					echo '<div class="notificacion_error"><b>Ha ocurrido un problema validando tu usuario en la base de datos de la página, y por lo tanto, no puedes conectarte con tu cuenta de Steam.</b>' .
					' Vuelve más tarde, a ver si se ha podido solucionar el problema.</div>';
					// Quitamos esta variable de sesión, la cuál tan sólo debe mostrarse una vez después de intentar conectarnos.
					unset($_SESSION['error_conectando']);
				}
				// Mostrar Login/Logout de Steam
				if (!$conectado_steam) {
					echo steamlogin();
				} else {
					logoutbutton();
				}
			?>
		</div>
	</div>
	<?php
		if ($es_pagina_publicaciones) {
	?>
		<hr>
		<h2 class="titulo_seccion_zona_lateral">BÚSQUEDA AVANZADA</h2>
		<?php
			if ($error_bd) {
				echo '<div class="notificacion_advertencia"><b>La búsqueda está desactivada porque ha fallado la conexión con la base de datos.</b></div>';
			} else {
		?>
		<form id="busqueda_avanzada" action="index.php" method="get">
			<div><label for="palabras_clave"><b>Palabras clave</label></b></div>
			<div><input type="text" class="elemento_busqueda" placeholder="Separa por espacios" id="palabras_clave" name="buscar"></div>
			<div><label for="categorias_publicacion"><b>Categorías</label></b></div>
			<div id="categorias_publicacion" class="elemento_busqueda">
				<label class="casilla_con_texto"><input type="checkbox" value="1" name="categoria[]">Noticias</label>
				<label class="casilla_con_texto"><input type="checkbox" value="2" name="categoria[]">Artículos</label>
				<label class="casilla_con_texto"><input type="checkbox" value="3" name="categoria[]">Análisis</label>
				<label class="casilla_con_texto"><input type="checkbox" value="4" name="categoria[]">Eventos</label>
				<label class="casilla_con_texto"><input type="checkbox" value="5" name="categoria[]">Actualizaciones</label>
			</div>
			<div><label for="autor_busqueda"><b>Autor de la publicación</b></label></div>
			<div><input type="text" class="elemento_busqueda" placeholder="Nombre del autor" id="autor_busqueda" name="autor"></div>
			<div><label for="num_publicaciones_mostrar"><b>Número de resultados a mostrar</b></label></div>
			<div><input type="number" class="elemento_busqueda" placeholder="Nombre del autor" id="num_publicaciones_mostrar" value="5" min="1" name="num_publicaciones"></div>
			<div><button class="boton_normal" type="submit" title="Buscar publicaciones con los parámetros indicados"><b>Confirmar</b></button></div>
		</form>
		<?php
			}
		}
		?>
	<hr>
	<h2 class="titulo_seccion_zona_lateral">COMUNIDADES AMIGAS</h2>
	<div id="comunidades_amigas">
		<a href="http://tf2portal.es/" target="_blank" title="TF2Portal.es"><img class="boton_comunidad_amiga" src="./img/comunidades/amigos_tf2portal01.png"></img></a>
		<a href="http://onlinesdk.webege.com/" title="OnlineSDK" target="_blank"><img class="boton_comunidad_amiga" src="./img/comunidades/amigos_spanishdevelopmentkit03.png"></img></a>
		<a href="http://steelandstone.es/" title="Steel & Stone" target="_blank"><img class="boton_comunidad_amiga" src="./img/comunidades/amigos_steelandstone03.png"></img></a>
		<a href="http://revolver.tf/" title="Revolver.tf" target="_blank"><img class="boton_comunidad_amiga" src="./img/comunidades/amigos_revolvertf01.png"></img></a>
		<a href="http://pajarosdebarro.com/" title="Pájaros de Barro" target="_blank"><img class="boton_comunidad_amiga" src="./img/comunidades/amigos_pdb01.png"></img></a>
		<a href="http://tf2stadium.com/" title="TF2Stadium" target="_blank"><img class="boton_comunidad_amiga" src="./img/comunidades/amigos_tf2stadium01.jpg"></img></a>
	</div>
	<hr>
	<h2 class="titulo_seccion_zona_lateral">NUESTRO TWITTER</h2>
	<div id="caja_twitter">
		<a class="twitter-timeline" href="https://twitter.com/RebelGamersClan" data-widget-id="661836514062680064">Tuits de @RebelGamersClan</a>
		<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
	</div>
</aside>