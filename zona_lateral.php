<aside id="zona_lateral">
	<h2 class="titulo_seccion_zona_lateral">Conexión a Steam</h2>
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
					$id_steam = $steamprofile['steamid'];
					@$con_bd = new mysqli($datosbd['conexion'], $datosbd['usuario1'], $datosbd['contra1'], $datosbd['nombre_bd']);
					prepararConexionUTF8($con_bd);
					$sent_prep = $con_bd->prepare("SELECT id, nombre, url_avatar FROM usuarios WHERE id_steam = (?)");
					if ($sent_prep) {
						$sent_prep->bind_param("s", $id_steam);
						$sent_prep->execute();
						$result_usuario = $sent_prep->get_result();
						
						// Si el usuario con esa ID de Steam existe, cogeremos su ID en la BD. Si no, se queda vacía.
						if (mysqli_num_rows($result_usuario) == 1) {
							while($usuario = $result_usuario->fetch_assoc()) {
								$id_usuario = $usuario['id'];
								$nombre_usuario = $usuario['nombre'];
								$avatar_usuario = $usuario['url_avatar'];
							}
							echo '<div id="bloque_perfil_propio"><span id="img_perfil_propio"><a href="/usuario.php?id=' . $id_usuario . '"><img src="' . $avatar_usuario . '"></a></span> ';
							echo '<span id="texto_perfil_propio">Bienvenido, <b><a href="/usuario.php?id=' . $id_usuario . '">' . $nombre_usuario . '</a></b></span></div>';
						}
					}
					mysqli_close($con_bd);
					logoutbutton();
				}
			?>
		</div>
	</div>
	<?php
		if ($es_pagina_publicaciones) {
	?>
		<h2 class="titulo_seccion_zona_lateral">Búsqueda Avanzada</h2>
		<?php
			if ($error_bd) {
				echo '<div class="notificacion_advertencia"><b>La búsqueda está desactivada porque ha fallado la conexión con la base de datos.</b></div>';
			} else {
		?>
		<form id="busqueda_avanzada" action="/index.php" method="get">
			<div><label for="palabras_clave"><b>Palabras clave</b></label></div>
			<div><input type="text" class="elemento_busqueda" placeholder="Separa por espacios" id="palabras_clave" name="buscar"></div>
			<div><label><b>Categorías</b></label></div>
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
			<div><input type="number" class="elemento_busqueda" placeholder="Nombre del autor" id="num_publicaciones_mostrar" value="10" min="1" name="num_publicaciones"></div>
			<div><button class="boton_normal" type="submit" title="Buscar publicaciones con los parámetros indicados"><b>Confirmar</b></button></div>
		</form>
		<?php
			}
		}
		?>
	<h2 class="titulo_seccion_zona_lateral">Comunidades Amigas</h2>
	<div id="comunidades_amigas">
		<a href="http://tf2portal.es/" target="_blank" title="TF2Portal.es"><img class="boton_comunidad_amiga" src="/img/comunidades/amigos_tf2portal01.png" alt="TF2Portal.es"></a>
		<a href="http://blogocio.eleconomista.es/team-fortress-2-gr/" target="_blank" title="Blogocio - TF2"><img class="boton_comunidad_amiga" src="/img/comunidades/amigos_blogotf2.png" alt="Blogocio - TF2"></a>
		<a href="http://onlinesdk.webege.com/" title="OnlineSDK" target="_blank"><img class="boton_comunidad_amiga" src="/img/comunidades/amigos_spanishdevelopmentkit03.png" alt="OnlineSDK"></a>
		<a href="http://steelandstone.es/" title="Steel & Stone" target="_blank"><img class="boton_comunidad_amiga" src="/img/comunidades/amigos_steelandstone03.png" alt="Steel & Stone"></a>
		<a href="http://revolver.tf/" title="Revolver.tf" target="_blank"><img class="boton_comunidad_amiga" src="/img/comunidades/amigos_revolvertf01.png" alt="Revolver.tf"></a>
		<a href="http://pajarosdebarro.com/" title="Pájaros de Barro" target="_blank"><img class="boton_comunidad_amiga" src="/img/comunidades/amigos_pdb01.png" alt="Pájaros de Barro"></a>
	</div>
	<h2 class="titulo_seccion_zona_lateral">Últimos Tweets</h2>
	<div id="caja_twitter">
		<a class="twitter-timeline" href="https://twitter.com/RebelGamersClan" data-widget-id="661836514062680064">Tuits de @RebelGamersClan</a>
		<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
	</div>
</aside>