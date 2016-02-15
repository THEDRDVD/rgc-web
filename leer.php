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
	<?php
	// Se intenta crear la conexión a la BD para leer las publicaciones y los comentarios
	try {
		@$con_bd = new mysqli($datosbd['conexion'], $datosbd['usuario1'], $datosbd['contra1'], $datosbd['nombre_bd']);
	// En caso de fallo, se muestra un mensaje de error, y se cambia el valor de la variable $error_bd a 'true' para que no intente usar la BD.
	} catch (Exception $e) {
		echo '<head><title>Rebel Gamers Clan - Leer publicación</title>
		<meta name="description" content="Leer una publicación escrita en Rebel Gamers Clan">
		<meta name="keywords" content="rebel,gamers,clan,comunidad,team,fortress,española,españa,steam,leer,publicacion">
		<meta name="author" content="DR.DVD">';
		mostrarPrimeraPartePagina();
		echo '<div class="notificacion_error"><b>Ha fallado la conexión con la base de datos.</b> Vuelve más tarde, a ver si se ha podido solucionar el problema.</div>';
		$error_bd = true;
	}
	
	if (!$error_bd) {
		prepararConexionUTF8($con_bd);
		// Comprobamos si se ha introducido el parámetro de ID. En caso afirmativo, mostramos la página.
		if (isset($_GET['id'])) {
			// Obtenemos la ID introducida.
			$id_publicacion = $_GET['id'];
			
			// Se crea la sentencia para buscar la publicación en la BD.
			$sent_prep = $con_bd->prepare("SELECT publicaciones.*, usuarios.id AS id_autor, usuarios.nombre, usuarios.id_steam, usuarios.url_perfil, usuarios.url_avatar FROM publicaciones JOIN usuarios ON usuarios.id = publicaciones.autor WHERE publicaciones.id = (?)");
			if (!$sent_prep) {
				echo '<head><title>Rebel Gamers Clan - Leer publicación</title>
				<meta name="description" content="Leer una publicación escrita en Rebel Gamers Clan">
				<meta name="keywords" content="rebel,gamers,clan,comunidad,team,fortress,española,españa,steam,leer,publicacion">
				<meta name="author" content="DR.DVD">';
				mostrarPrimeraPartePagina();
				echo '<div class="notificacion_error"><b>Ha habido un problema obteniendo los datos de la publicación de la base de datos.</b>' .
				' Vuelve más tarde, a ver si se ha podido solucionar el problema.</div>';
			} else {
				$sent_prep->bind_param("i", $id_publicacion);
				$sent_prep->execute();
				$result_publicacion = $sent_prep->get_result();
				
				// La publicación existe en la BD.
				if (mysqli_num_rows($result_publicacion) == 1) {
					// Comprobamos si se ha enviado algun código con información sobre la publicación (se ha publicado, modificado...).
					if (isset($_SESSION['cod_publicacion'])) {
						$cod_publicacion = $_SESSION['cod_publicacion'];
						unset($_SESSION['cod_publicacion']);
					} else {
						$cod_publicacion = 0;
					}
					// Comprobamos si se ha enviado algun código con información sobre el comentario (se ha publicado, modificado, eliminado...).
					if (isset($_SESSION['cod_comentario'])) {
						$cod_comentario = $_SESSION['cod_comentario'];
						unset($_SESSION['cod_comentario']);
					} else {
						$cod_comentario = 0;
					}
					
					$id_usuario = "";
					$permisos_usuario = 0;
					if ($conectado_steam) {
						// Comprobamos la ID del usuario en la BD según su ID de Steam para saber si es el autor de la publicación o de algún comentario.
						$id_steam = $steamprofile['steamid'];
						$sent_prep = $con_bd->prepare("SELECT id, permisos FROM usuarios WHERE id_steam = (?)");
						if (!$sent_prep) {
							// Si ha fallado la comprobación de nuestro usuario en la BD, en lugar de mostrar error, dejaremos que pueda leer la publicación,
							// pero no pueda controlar nada más: no podrá editar/eliminar la publicación ni los comentarios.
							$conectado_steam = false;
						} else {
							$sent_prep->bind_param("s", $id_steam);
							$sent_prep->execute();
							$result_usuario = $sent_prep->get_result();
							
							// Si el usuario con esa ID de Steam existe, cogeremos su ID en la BD. Si no, se queda vacía.
							if (mysqli_num_rows($result_usuario) == 1) {
								while($usuario = $result_usuario->fetch_assoc()) {
									$id_usuario = $usuario['id'];
									$permisos_usuario = $usuario['permisos'];
								}
							}
						}
					}
					while($publicacion = $result_publicacion->fetch_assoc()) {
						// Añadimos el título y demás información de la etiqueta 'head' y abrimos el 'body' en la función 'mostrarPrimeraPartePagina()'.
						echo '<head><title>Rebel Gamers Clan - ' . $publicacion['titulo'] . '</title>
						<meta name="description" content="' . $publicacion['resumen'] . '">
						<meta name="keywords" content="rebel,gamers,clan,comunidad,team,fortress,española,españa,steam,leer,publicacion">
						<meta name="author" content="DR.DVD">';
						mostrarPrimeraPartePagina();
						
						// Mensaje de alerta sobre la publicación, dependiendo de si se ha realizado una acción.
						switch ($cod_publicacion) {
							case 1:
								echo '<div class="notificacion_correcto">Se ha publicado el texto correctamente.</div>';
								break;
							case 2:
								echo '<div class="notificacion_correcto">Se han publicado los cambios correctamente.</div>';
								break;
							case 3:
								echo '<div class="notificacion_error"><b>Ha fallado la conexión con la base de datos.</b> Vuelve más tarde, a ver si se ha podido solucionar el problema.</div>';
								break;
							case 4:
								echo '<div class="notificacion_error"><b>Ha ocurrido un problema eliminando la publicación.</b> Inténtalo de nuevo.</div>';
								break;
							case 5:
								echo '<div class="notificacion_error"><b>No tienes permiso para realizar esta acción.</b>' .
								' Esto es debido a que no eres el autor de la publicación o no eres un administrador de la página.</div>';
								break;
						}
						
						// Empezamos a mostrar la publicación.
						echo '<article id="publicacion_' . $id_publicacion . '" class="caja_lectura_publicacion">';
						echo '<header><h1 class="titulo_publicacion">' . $publicacion['titulo'] .'</h1>';
						// Si el autor es Anónimo, no se le pone un enlace a la página de su perfil.
						if ($publicacion['nombre'] == 'Anónimo') {
							echo '<h2 class="datos_publicacion">' . tratarCategoria($publicacion['categoria']) . ' - Escrito el ' .
							$publicacion['fecha'] . ' por <a>' . $publicacion['nombre'] . '</a></h2>';
						// Si el autor no es anónimo, se pone un enlace para que los usuarios puedan ver su perfil y lo que ha escrito.
						} else {
							echo '<h2 class="datos_publicacion">' . tratarCategoria($publicacion['categoria']) . ' - Escrito el ' .
							$publicacion['fecha'] . ' por <a href="/usuario.php?id=' . $publicacion['id_autor'] . '" title="Ver perfil del autor">' . $publicacion['nombre'] . '</a></h2>';
						}
						// Sustituímos los saltos de línea por la etiqueta '<br>' para que se muestre correctamente en la página.
						echo '<div class="resumen_publicacion">' . str_replace("\n", "<br>", $publicacion['resumen']) . '</div></header><hr>';
						echo '<div class="contenido_publicacion">' . str_replace("\n", "<br>", $publicacion['contenido']) . '</div>';
						// Comprobamos si el usuario es el mismo autor de la publicación.
						if ($id_usuario != "" && $id_usuario == $publicacion['id_autor']) {
							// En caso de que lo sea, tiene derecho a 'Editar' o 'Eliminar' su publicación.
							echo '<div align="right">';
							echo '<form action="/escribir.php" method="post" class="form_editar"><button class="boton_editar" type="submit" ' .
							'name="editar" value="' . $id_publicacion . '">Editar</button><input type="hidden" name="conseguir_datos_recientes_publicacion"></form>';
							echo ' <form action="/confirmar_eliminar.php" method="post" class="form_eliminar"><button class="boton_eliminar" type="submit" ' .
							'name="publicacion">Eliminar</button><input type="hidden" name="id_publicacion" value="' . $id_publicacion . '" /></form></div>';
						}
						echo '</article>';
						
						// Sección de comentarios. Hay que buscar si hay comentarios en la base de datos.
						echo '<h2 id="comentarios">Comentarios</h2>';
						
						// Mensaje de alerta sobre comentarios, dependiendo de si se ha realizado una acción con ellos.
						switch ($cod_comentario) {
							case 1:
								echo '<div class="notificacion_correcto">Se ha publicado el comentario correctamente.</div>';
								break;
							case 2:
								echo '<div class="notificacion_correcto">Se ha <a href="/leer.php?id=' . $_SESSION['id_publicacion'] .
								'#coment_' . $_SESSION['id_comentario'] . '">modificado el comentario</a> correctamente.</div>';
								unset($_SESSION['id_publicacion']);
								unset($_SESSION['id_comentario']);
								break;
							case 3:
								echo '<div class="notificacion_advertencia"><b>Has introducido un comentario vacío o con etiquetas no permitidas.</b></div>';
								break;
							case 4:
								echo '<div class="notificacion_error"><b>Ha fallado la conexión con la base de datos.</b>' .
								' Vuelve más tarde, a ver si se ha podido solucionar el problema.</div>';
								break;
							case 5:
								echo '<div class="notificacion_error"><b>Ha ocurrido un problema enviando el comentario.</b> Inténtalo de nuevo.</div>';
								break;
							case 6:
								echo '<div class="notificacion_correcto"><b>Se ha eliminado el comentario correctamente.</b></div>';
								break;
							case 7:
								echo '<div class="notificacion_error"><b>Ha ocurrido un problema eliminando el comentario.</b> Inténtalo de nuevo.</div>';
								break;
							case 8:
								echo '<div class="notificacion_error"><b>No tienes permiso para realizar esta acción.</b>' .
								' Esto es debido a que no eres el autor del comentario o no eres un administrador de la página.</div>';
								break;
						}
						
						// Se crea la sentencia para buscar los comentarios que correspondan a la publicación que queremos.
						$sent_prep = $con_bd->prepare("SELECT comentarios.id AS id_comentario, comentarios.publicacion_comentada, comentarios.fecha, comentarios.contenido, usuarios.id AS id_autor, usuarios.nombre, usuarios.id_steam, usuarios.url_perfil, usuarios.url_avatar FROM comentarios JOIN usuarios ON usuarios.id = comentarios.autor_comentario WHERE comentarios.publicacion_comentada = (?) ORDER BY fecha DESC");
						if (!$sent_prep) {
							echo '<div class="notificacion_error"><b>Ha ocurrido un problema buscando los comentarios para este texto.</b>' .
							' Vuelve más tarde, a ver si se ha podido solucionar el problema.</div>';
						} else {
							$sent_prep->bind_param("i", $id_publicacion);
							$sent_prep->execute();
							$result_comentarios = $sent_prep->get_result();
							
							// Si hay comentarios para la publicación, los muestra uno a uno.
							if (mysqli_num_rows($result_comentarios) >= 1) {
								while($comentario = $result_comentarios->fetch_assoc()) {
									$id_comentario = $comentario['id_comentario'];
									$editar_comentario = false;
									if (isset($_POST['editar_comentario'])) {
										if ($id_comentario == $_POST['editar_comentario']) {
											$editar_comentario = true;
										}
									}
									if ($editar_comentario && $conectado_steam && $id_usuario != "" && $id_usuario == $comentario['id_autor']) {
										echo '<form id="edit_coment_' . $id_comentario . '" action="/acciones_post.php" method="post">';
										echo '<div class="notificacion_info">Edita el contenido del comentario.</div>';
										echo '<textarea name="texto_comentario_editado" placeholder="Escribe aquí un comentario" class="caja_texto" form="edit_coment_' .
										$id_comentario . '" rows=5 maxlength=1000>' . $comentario['contenido'] . '</textarea>';
										echo '<button class="boton_normal" type="submit" name="modificar_comentario" value="modificar_comentario" ' .
										'title="Publica las modificaciones al comentario usando la cuenta de Steam con la que estás conectado actualmente."><b>Confirmar cambios</b></button>';
										echo '<input type="hidden" name="id_publicacion" value="' . $id_publicacion . '" />';
										echo '<input type="hidden" name="id_comentario" value="' . $id_comentario . '" /></form><br>';
									} else {
										echo '<div id="coment_' . $id_comentario . '" class="comentario">';
										echo '<div class="datos_comentarios"><span class="dato_comentario_avatar"><img src="' . $comentario['url_avatar'] . 
										'"></span> <span class="dato_comentario_nombre_y_fecha"><a href="/usuario.php?id=' . $comentario['id_autor'] . '">' .
										$comentario['nombre'] . '</a><br>' . $comentario['fecha'] . '</span></div>';
										echo '<div class="cuerpo_comentario">' . $comentario['contenido']  . '</div>';
										// Si el usuario es el autor del comentario se muestran los botones para editar/eliminar.
										if ($id_usuario != "" && $id_usuario == $comentario['id_autor']) {
											echo '<div align="right">';
											echo '<form action="/leer.php?id=' . $id_publicacion . '#edit_coment_' . $id_comentario . '" method="post" class="form_editar">';
											echo '<button class="boton_eliminar" type="submit" name="editar_comentario" value="' . $id_comentario . '">Editar</button></form>';
											echo ' <form action="/confirmar_eliminar.php" method="post" class="form_eliminar">';
											echo '<button class="boton_eliminar" type="submit" name="comentario">Eliminar</button>';
											echo '<input type="hidden" name="id_publicacion" value="' . $id_publicacion . '" />';
											echo '<input type="hidden" name="id_comentario" value="' . $id_comentario . '" /></form></div>';
										}
										echo '</div>';
									}
								}
							// Si no hay comentarios, anuncia que nadie ha comentado el texto.
							} else {
								echo '<div>Nadie ha comentado aún este texto.</div>';
							}
							
							// Autorizar comentarios si estás logeado a Steam.
							if ($conectado_steam) {
								echo '<form id="form_nuevo_comentario" action="/acciones_post.php" method="post">';
								echo '<textarea name="texto_comentario" placeholder="Escribe aquí un comentario" class="caja_texto" form="form_nuevo_comentario" rows=5 maxlength=1000>';
								if (($cod_comentario == 3  || $cod_comentario == 4) && (isset($_SESSION['texto_comentario']))) {
									echo $_SESSION['texto_comentario'];
									unset($_SESSION['texto_comentario']);
								}
								echo '</textarea><br>';
								echo '<button class="boton_normal" type="submit" name="enviar_comentario" value="enviar_comentario" ' .
								'title="Publica el comentario usando la cuenta de Steam con la que estás conectado actualmente."><b>Enviar comentario</b></button>';
								echo '<input type="hidden" name="id_publicacion" value="' . $id_publicacion . '" />';
								echo '<input type="hidden" name="id_usuario" value="' . $id_usuario . '" /></form>';
							}
						}
					}
				// Si no existe una publicación con esa ID, informamos de la situación.
				} else {
					echo '<head><title>Rebel Gamers Clan - Leer publicación</title>
					<meta name="description" content="Leer una publicación escrita en Rebel Gamers Clan">
					<meta name="keywords" content="rebel,gamers,clan,comunidad,team,fortress,española,españa,steam,leer,publicacion">
					<meta name="author" content="DR.DVD">';
					mostrarPrimeraPartePagina();
					echo '<div class="notificacion_advertencia"><b>No existe ninguna publicación con la ID que has introducido.</b></div>';
				}
			}
		// Si no está indicada la ID por parámetro, tampoco se mostrará nada.
		} else {
			echo '<head><title>Rebel Gamers Clan - Leer publicación</title>
			<meta name="description" content="Leer una publicación escrita en Rebel Gamers Clan">
			<meta name="keywords" content="rebel,gamers,clan,comunidad,team,fortress,española,españa,steam,leer,publicacion">
			<meta name="author" content="DR.DVD">';
			mostrarPrimeraPartePagina();
			echo '<div class="notificacion_advertencia"><b>No has introducido ninguna ID.</b></div>';
		}
		// Cerramos la conexión con la BD, ya no la necesitamos.
		mysqli_close($con_bd);
	}
	?>
			</section>
			<?php include('zona_lateral.php'); ?>
		</div>
		<?php include('pie_pagina.php'); ?>
	</body>
</html>