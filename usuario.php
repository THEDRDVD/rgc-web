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
	// Se intenta crear la conexión a la BD para leer las publicaciones y los usuarios
	try {
		@$con_bd = new mysqli($datosbd['conexion'], $datosbd['usuario1'], $datosbd['contra1'], $datosbd['nombre_bd']);
	// En caso de fallo, se muestra un mensaje de error, y se cambia el valor de la variable $error_bd a 'true' para que no intente usar la BD.
	} catch (Exception $e) {
		echo '<head><title>Rebel Gamers Clan - Perfil de usuario</title>
		<meta name="description" content="Perfil de un usuario en la página de Rebel Gamers Clan">
		<meta name="keywords" content="rebel,gamers,clan,comunidad,team,fortress,española,españa,steam,perfil,usuario">
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
			$id_usuario = $_GET['id'];
			
			// Se crea la sentencia para buscar al usuario en la BD.
			$sent_prep = $con_bd->prepare("SELECT * FROM usuarios WHERE id = (?) AND id != 0");
			if (!$sent_prep) {
				echo '<head><title>Rebel Gamers Clan - Perfil de usuario</title>
				<meta name="description" content="Perfil de un usuario en la página de Rebel Gamers Clan">
				<meta name="keywords" content="rebel,gamers,clan,comunidad,team,fortress,española,españa,steam,perfil,usuario">
				<meta name="author" content="DR.DVD">';
				mostrarPrimeraPartePagina();
				echo '<div class="notificacion_error"><b>Ha habido un problema buscando el usuario en la base de datos.</b>' .
				' Vuelve más tarde, a ver si se ha podido solucionar el problema.</div>';
			} else {
				$sent_prep->bind_param("i", $id_usuario);
				$sent_prep->execute();
				$result_usuario = $sent_prep->get_result();
				
				// El usuario existe en la BD.
				if (mysqli_num_rows($result_usuario) == 1) {
					// Comprobamos si se ha enviado algun código con información sobre el usuario.
					if (isset($_SESSION['cod_usuario'])) {
						$cod_usuario = $_SESSION['cod_usuario'];
						unset($_SESSION['cod_usuario']);
					} else {
						$cod_usuario = 0;
					}
					
					while($usuario = $result_usuario->fetch_assoc()) {
						// Añadimos el título y demás información de la etiqueta 'head' y abrimos el 'body'.
						echo '<head><title>Rebel Gamers Clan - Perfil de ' . $usuario['nombre'] . '</title>
						<meta name="description" content="Perfil de ' . $usuario['nombre'] . ' en la página de Rebel Gamers Clan">
						<meta name="keywords" content="rebel,gamers,clan,comunidad,team,fortress,española,españa,steam,perfil,usuario">
						<meta name="author" content="DR.DVD">';
						mostrarPrimeraPartePagina();
						
						// Mensaje de alerta sobre el usuario, dependiendo de si se ha realizado una acción.
						switch ($cod_usuario) {
							case 1:
								echo '<div class="notificacion_correcto">Se ha actualizado la información del usuario correctamente.</div>';
								break;
							case 2:
								echo '<div class="notificacion_error"><b>Ha ocurrido un problema actualizando los datos del usuario.</b> Inténtalo de nuevo.</div>';
								break;
							case 3:
								echo '<div class="notificacion_error"><b>Ha fallado la conexión con la base de datos.</b> Vuelve más tarde, a ver si se ha podido solucionar el problema.</div>';
								break;
							case 4:
								echo '<div class="notificacion_error"><b>No tienes permiso para realizar esta acción.</b> Esto es debido a que no eres un administrador de la página.</div>';
								break;
						}
						$avatar_usuario = $usuario['url_avatar'];
						// Si se está usando un avatar de Steam (cosa que pasará siempre, salvo modificado a proposito en la BD), cogeremos el de tamaño '_full'.
						if ((strpos($avatar_usuario, "steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/") != 0) && (strpos($avatar_usuario, ".jpg") != 0)) {
							$avatar_usuario = str_replace(".jpg", "_full.jpg", $avatar_usuario);
						}
						// Si el avatar tiene HTTPS, deberemos quitárselo para evitar problemas al conseguir la información de cuánto mide.
						if ((strpos($avatar_usuario, "ttps://") == 1)) {
							$avatar_usuario = str_replace("https://", "http://", $avatar_usuario);
						}
						
						// Conseguimos el ancho del avatar, para ajustar la anchura de los botones de edición del usuario.
						list($ancho_avatar) = getimagesize($avatar_usuario);
						
						echo '<div id="caja_perfil_usuario">';
						// Abrimos el bloque del avatar del usuario y los botones de edición (si tubieramos permisos de administrador).
						echo '<div class="bloque_caja_perfil_usuario"><img id="imagen_perfil_usuario" src="' . $avatar_usuario . '" title="Imagen de perfil del usuario"></img>';
						if ($conectado_steam) {
							// Comprobamos si el usuario conectado tiene permisos de administrador.
							$id_steam = $steamprofile['steamid'];
							$sent_prep_2 = $con_bd->prepare("SELECT permisos FROM usuarios WHERE id_steam = (?)");
							if ($sent_prep_2) {
								$sent_prep_2->bind_param("s", $id_steam);
								$sent_prep_2->execute();
								$result_usuario_2 = $sent_prep_2->get_result();
								
								// Si el usuario con esa ID de Steam existe, cogeremos su ID en la BD. Si no, se queda vacía.
								if (mysqli_num_rows($result_usuario_2) == 1) {
									while($usuario_2 = $result_usuario_2->fetch_assoc()) {
										// El usuario es un administrador.
										if ($usuario_2['permisos'] == 2) {
											echo '<br><form action="/acciones_post.php" method="post"><button class="boton_normal" style="width: ' .
											$ancho_avatar . 'px;" name="actualizar_usuario" value="' . $id_usuario . '"><b>Actualizar información de Steam del usuario</b></button>' .
											'<input type="hidden" name="id_steam" value="' . $usuario['id_steam'] . '"></form>';
										}
									}
								}
							}
						}
						echo '</div>';
						// Abrimos el bloque de datos del usuario.
						echo '<div class="bloque_caja_perfil_usuario"><h1 id="nombre_perfil_usuario" class="elemento_perfil_usuario"><a href="' . $usuario['url_perfil'] .
						'" target="_blank" title="Perfil de Steam del usuario">' . $usuario['nombre'] . '</a></h1>' .
						'<h4 class="elemento_perfil_usuario">' . claseUsuario($usuario['permisos']) . '</h4>';
						
						// Ahora buscamos las publicaciones que ha escrito este usuario.
						$sent_prep = $con_bd->prepare("SELECT * FROM publicaciones WHERE autor = (?)");
						if (!$sent_prep) {
							echo '<div class="notificacion_error"><b>Ha habido un problema buscando las publicaciones escritas por el usuario en la base de datos.</b>' .
							' Vuelve más tarde, a ver si se ha podido solucionar el problema.</div>';
						} else {
							$sent_prep->bind_param("i", $id_usuario);
							$sent_prep->execute();
							$result_textos_escritos = $sent_prep->get_result();
							$num_escritos = mysqli_num_rows($result_textos_escritos);
							
							// Si ha escrito algo, muestro las publicaciones listadas por título una por una.
							if ($num_escritos > 0) {
								if ($num_escritos == 1) {
									echo 'Ha escrito ' . $num_escritos . ' publicación.<ul id="lista_publicaciones_usuario" class="elemento_perfil_usuario">';
								} else {
									echo 'Ha escrito ' . $num_escritos . ' publicaciones.<ul id="lista_publicaciones_usuario" class="elemento_perfil_usuario">';
								}
								while($texto = $result_textos_escritos->fetch_assoc()) {
									echo '<li class="elemento_lista_publicaciones_usuario"><a href="/leer.php?id=' . $texto['id'] .
									'" title="Leer publicación escrita por este usuario">' . $texto['titulo'] . '</a></li>';
								}
								echo '</ul>';
							} else {
								echo "Este usuario no ha publicado nada aún.";
							}
						}
						// Cerramos el 'div' del bloque de datos del usuario y el del comentario.
						echo '</div></div>';
					}
				// Si no existe una publicación con esa ID, informamos de la situación.
				} else {
					echo '<head><title>Rebel Gamers Clan - Perfil de usuario</title>
					<meta name="description" content="Perfil de un usuario en la página de Rebel Gamers Clan">
					<meta name="keywords" content="rebel,gamers,clan,comunidad,team,fortress,española,españa,steam,perfil,usuario">
					<meta name="author" content="DR.DVD">';
					mostrarPrimeraPartePagina();
					echo '<div class="notificacion_advertencia"><b>No existe ningún usuario que tenga la ID que has introducido.</b></div>';
				}
			}
		// Si no está indicada la ID por parámetro, tampoco se mostrará nada.
		} else {
			echo '<head><title>Rebel Gamers Clan - Perfil de usuario</title>
			<meta name="description" content="Perfil de un usuario en la página de Rebel Gamers Clan">
			<meta name="keywords" content="rebel,gamers,clan,comunidad,team,fortress,española,españa,steam,perfil,usuario">
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