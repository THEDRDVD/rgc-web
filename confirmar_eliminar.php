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
		echo '<head><title>Rebel Gamers Clan - Eliminar publicación/comentario</title>';
		mostrarPrimeraPartePagina();
		echo '<div class="notificacion_error"><b>Ha fallado la conexión con la base de datos.</b> Vuelve más tarde, a ver si se ha podido solucionar el problema.</div>';
		$error_bd = true;
	}
	
	if (!$error_bd) {
		prepararConexionUTF8($con_bd);
		// Comprobamos si se quiere borrar una 'publicacion' o un 'comentario'.
		if (isset($_POST['publicacion'])) {
			echo '<head><title>Rebel Gamers Clan - Eliminar publicación</title>';
			mostrarPrimeraPartePagina();
			if ($conectado_steam) {
				// Obtenemos la ID introducida.
				$id_publicacion = $_POST['id_publicacion'];
				$sent_prep = $con_bd->prepare("SELECT publicaciones.id AS id_publicacion, publicaciones.titulo, usuarios.id AS id_autor, usuarios.id_steam, usuarios.permisos FROM publicaciones JOIN usuarios ON usuarios.id = publicaciones.autor WHERE publicaciones.id = (?)");
				if (!$sent_prep) {
					echo '<div class="notificacion_error"><b>Ha ocurrido un problema buscando los datos de la publicación.</b>' .
					' Vuelve más tarde, a ver si se ha podido solucionar el problema.</div>';
				} else {
					$sent_prep->bind_param("i", $id_publicacion);
					$sent_prep->execute();
					$result_publicacion = $sent_prep->get_result();
					
					// Si se ha encontrado la publicación, procedemos a mostrar el formulario de confirmación para borrar la publicación.
					if (mysqli_num_rows($result_publicacion) == 1) {
						while($publicacion = $result_publicacion->fetch_assoc()) {
							// Si es el autor de la publicación:
							if ($publicacion['id_steam'] ==  $steamprofile['steamid']) {
								echo '<div class="notificacion_advertencia">¿Estás seguro de que quieres borrar la publicación <b>\'' . $publicacion['titulo'] . '\'</b>?</div>';
								echo '<form action="acciones_post.php" method="post">';
								echo '<button class="boton_normal" type="submit" name="eliminar_publicacion" ' .
								'title="Haz desaparecer esta publicación de la faz de la... página web. >:)"><b>Sí, bórrala</b></button> ';
								echo '<button class="boton_normal" type="submit" name="cancelar" title="¡No, ha habido una confusión! D\':"><b>No, cancelar la acción</b></button>';
								echo '<input type="hidden" name="id_publicacion" value="' . $id_publicacion . '" /></form>';
							} else {
								echo '<div class="notificacion_error"><b>No eres el autor de la publicación o no tienes permisos suficientes para realizar esta acción.</b></div>';
							}
						}
					} else {
						echo '<div class="notificacion_error"><b>No se ha encontrado ninguna publicación con la ID enviada.</b></div>';
					}
				}
			} else {
				echo '<div class="notificacion_advertencia">Tienes que estar conectado a Steam a través de la página para poder eliminar un comentario.</div>';
			}
		} else if (isset($_POST['comentario'])) {
			echo '<head><title>Rebel Gamers Clan - Eliminar comentario</title>';
			mostrarPrimeraPartePagina();
			if ($conectado_steam) {
				// Obtenemos la ID introducida.
				$id_publicacion = $_POST['id_publicacion'];
				$id_comentario = $_POST['id_comentario'];
				$sent_prep = $con_bd->prepare("SELECT comentarios.id AS id_comentario, usuarios.id AS id_autor, usuarios.id_steam, usuarios.permisos FROM comentarios JOIN usuarios ON usuarios.id = comentarios.autor_comentario WHERE comentarios.id = (?)");
				if (!$sent_prep) {
					echo '<div class="notificacion_error"><b>Ha ocurrido un problema buscando los datos del comentario.</b> Vuelve más tarde, a ver si se ha podido solucionar el problema.</div>';
				} else {
					$sent_prep->bind_param("i", $id_comentario);
					$sent_prep->execute();
					$result_comentario = $sent_prep->get_result();
					
					// Si se ha encontrado el comentario, procedemos a mostrar el formulario de confirmación para borrar el comentario.
					if (mysqli_num_rows($result_comentario) == 1) {
						while($comentario = $result_comentario->fetch_assoc()) {
							// Si es el autor del comentario:
							if ($comentario['id_steam'] ==  $steamprofile['steamid']) {
								echo '<div class="notificacion_advertencia">¿Estás seguro de que quieres borrar este comentario?</div>';
								echo '<form action="acciones_post.php" method="post">';
								echo '<button class="boton_normal" type="submit" name="eliminar_comentario" ' .
								'title="Haz desaparecer este comentario de la faz de la... página web. >:)"><b>Sí, bórralo</b></button> ';
								echo '<button class="boton_normal" type="submit" name="cancelar" title="¡No, ha habido una confusión! D\':"><b>No, cancelar la acción</b></button>';
								echo '<input type="hidden" name="id_publicacion" value="' . $id_publicacion . '" />';
								echo '<input type="hidden" name="id_comentario" value="' . $id_comentario . '" /></form>';
							} else {
								echo '<div class="notificacion_error"><b>No eres el autor del comentario o no tienes permisos suficientes para realizar esta acción.</b></div>';
							}
						}
					} else {
						echo '<div class="notificacion_error"><b>No se ha encontrado ningún comentario con la ID enviada.</b></div>';
					}
				}
			} else {
				echo '<div class="notificacion_advertencia">Tienes que estar conectado a Steam a través de la página para poder eliminar un comentario.</div>';
			}
		// Si no está indicado por parámetro POST 'publicacion' o 'comentario', no se mostrará nada.
		} else {
			echo '<head><title>Rebel Gamers Clan - Eliminar publicación/comentario</title>';
			mostrarPrimeraPartePagina();
			echo '<div class="notificacion_advertencia"><b>No se han reconocido los parámetros enviados.</b> Quizás hayas llegado a esta página por error.</div>';
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