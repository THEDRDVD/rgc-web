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

if (isset($_POST['enviar_comentario']) && ($conectado_steam)) {
	$texto_comentario = $_POST['texto_comentario'];
	$id_publicacion = $_POST['id_publicacion'];
	$id_usuario = $_POST['id_usuario'];
	
	// Controlamos que no se usen más etiquetas de las permitidas.
	$texto_comentario = strip_tags($texto_comentario, '<br><a><b><i><u><ol><ul><li>');
	$sin_etiquetas = strip_tags($texto_comentario);
	if (empty($sin_etiquetas)) {
		$_SESSION['cod_comentario'] = 3;
	} else {
		// Se crea la conexión a la BD para leer las publicaciones y los comentarios.
		@$con_bd = new mysqli($datosbd['conexion'], $datosbd['usuario2'], $datosbd['contra2'], $datosbd['nombre_bd']);
		if ($con_bd->connect_error) {
			$_SESSION['cod_comentario'] = 4;
			$_SESSION['texto_comentario'] = $texto_comentario;
		} else {
			prepararConexionUTF8($con_bd);
			// Comprobamos que existe el usuario, usando la variable 'id_usuario' obtenida anteriormente.
			if ($id_usuario != "") {
				// Enviar comentario a la BD.
				$sent_prep = $con_bd->prepare("INSERT INTO comentarios (publicacion_comentada, autor_comentario, contenido) VALUES (?, ?, ?)");
				if (!$sent_prep) {
					$_SESSION['cod_comentario'] = 5;
					$_SESSION['texto_comentario'] = $texto_comentario;
				} else {
					$sent_prep->bind_param("iss", $id_publicacion, $id_usuario, $texto_comentario);
					if (!$sent_prep->execute()) {
						$_SESSION['cod_comentario'] = 5;
						$_SESSION['texto_comentario'] = $texto_comentario;
					} else if (mysqli_affected_rows($con_bd) == 1) {
						$_SESSION['cod_comentario'] = 1;
					} else {
						$_SESSION['cod_comentario'] = 5;
						$_SESSION['texto_comentario'] = $texto_comentario;
					}
				}
			// Si no existe se comunica el error. Esto no debería de pasar, porque todos los usuarios 
			// que se conectan a través de Steam a la página deberían estar en la BD. Pero por si acaso...
			} else {
				$_SESSION['cod_comentario'] = 5;
				$_SESSION['texto_comentario'] = $texto_comentario;
			}
			// Se cierra la conexión a la BD.
			mysqli_close($con_bd);
		}
	}
	header('HTTP/1.1 303 See Other');
	header('Location: /leer.php?id=' . $id_publicacion . '#comentarios');
} else if (isset($_POST['modificar_comentario']) && ($conectado_steam)) {
	$texto_comentario_editado = $_POST['texto_comentario_editado'];
	$id_publicacion = $_POST['id_publicacion'];
	$id_comentario = $_POST['id_comentario'];
	
	// Controlamos que no se usen más etiquetas de las permitidas.
	$texto_comentario_editado = strip_tags($texto_comentario_editado, '<br><a><b><i><u><ol><ul><li>');
	$sin_etiquetas = strip_tags($texto_comentario_editado);
	if (empty($sin_etiquetas)) {
		$_SESSION['cod_comentario'] = 3;
	} else {
		// Se crea la conexión a la BD para leer las publicaciones y los comentarios.
		@$con_bd = new mysqli($datosbd['conexion'], $datosbd['usuario2'], $datosbd['contra2'], $datosbd['nombre_bd']);
		if ($con_bd_comentarista->connect_error) {
			$_SESSION['cod_comentario'] = 4;
		} else {
			prepararConexionUTF8($con_bd);
			// Antes tenemos que comprobar la autoridad del usuario para modificar el comentario, usando su ID de Steam.
			include('steamauth/userInfo.php');
			$id_steam = $steamprofile['steamid'];
			$sent_prep = $con_bd->prepare("SELECT * FROM comentarios JOIN usuarios ON usuarios.id = comentarios.autor_comentario WHERE comentarios.id = (?) AND usuarios.id_steam = (?)");
			$sent_prep_2 = $con_bd->prepare("SELECT * FROM usuarios WHERE id_steam = (?) AND permisos = (2)");
			if (!$sent_prep || !$sent_prep_2) {
				$_SESSION['cod_comentario'] = 5;
			} else {
				$sent_prep->bind_param("is", $id_comentario, $id_steam);
				$sent_prep_2->bind_param("s", $id_steam);
				if (!$sent_prep->execute() || !$sent_prep_2->execute()) {
					$_SESSION['cod_comentario'] = 5;
				} else {
					$result_comentario = $sent_prep->get_result();
					$result_usuario = $sent_prep_2->get_result();
					// Comprobamos si el es el autor del comentario o si tiene permisos de administrador.
					if (mysqli_num_rows($result_comentario) == 1 || mysqli_num_rows($result_usuario) == 1) {
						// Enviar comentario a la BD.
						$sent_prep = $con_bd->prepare("UPDATE comentarios SET contenido=(?) WHERE id=(?)");
						if (!$sent_prep) {
							$_SESSION['cod_comentario'] = 5;
						} else {
							$sent_prep->bind_param("si", $texto_comentario_editado, $id_comentario);
							if (!$sent_prep->execute()) {
								$_SESSION['cod_comentario'] = 5;
							} else if (mysqli_affected_rows($con_bd) == 1) {
								$_SESSION['cod_comentario'] = 2;
								$_SESSION['id_publicacion'] = $id_publicacion;
								$_SESSION['id_comentario'] = $id_comentario;
							} else {
								$_SESSION['cod_comentario'] = 5;
							}
						}
					} else {
						$_SESSION['cod_comentario'] = 8;
					}
				}
			}
			// Se cierra la conexión a la BD.
			mysqli_close($con_bd);
		}
	}
	header('HTTP/1.1 303 See Other');
	header('Location: /leer.php?id=' . $id_publicacion . '#comentarios');
} else if (isset($_POST['eliminar_comentario']) && ($conectado_steam)) {
	$id_publicacion = $_POST['id_publicacion'];
	$id_comentario = $_POST['id_comentario'];
	
	// Se crea la conexión a la BD para leer las publicaciones y los comentarios.
	@$con_bd = new mysqli($datosbd['conexion'], $datosbd['usuario2'], $datosbd['contra2'], $datosbd['nombre_bd']);
	if ($con_bd->connect_error) {
		$_SESSION['cod_comentario'] = 4;
	} else {
		prepararConexionUTF8($con_bd);
		// Antes tenemos que comprobar la autoridad del usuario para borrar el comentario, usando su ID de Steam.
		include('steamauth/userInfo.php');
		$id_steam = $steamprofile['steamid'];
		$sent_prep = $con_bd->prepare("SELECT * FROM comentarios JOIN usuarios ON usuarios.id = comentarios.autor_comentario WHERE comentarios.id = (?) AND usuarios.id_steam = (?)");
		$sent_prep_2 = $con_bd->prepare("SELECT * FROM usuarios WHERE id_steam = (?) AND permisos = (2)");
		if (!$sent_prep || !$sent_prep_2) {
			$_SESSION['cod_comentario'] = 5;
		} else {
			$sent_prep->bind_param("is", $id_comentario, $id_steam);
			$sent_prep_2->bind_param("s", $id_steam);
			if (!$sent_prep->execute() || !$sent_prep_2->execute()) {
				$_SESSION['cod_comentario'] = 5;
			} else {
				$result_comentario = $sent_prep->get_result();
				$result_usuario = $sent_prep_2->get_result();
				// Comprobamos si el es el autor del comentario o si tiene permisos de administrador.
				if (mysqli_num_rows($result_comentario) == 1 || mysqli_num_rows($result_usuario) == 1) {
					$sent_prep = $con_bd->prepare("DELETE FROM comentarios WHERE id = (?)");
					if (!$sent_prep) {
						$_SESSION['cod_comentario'] = 7;
					} else {
						$sent_prep->bind_param("i", $id_comentario);
						if (!$sent_prep->execute()) {
							$_SESSION['cod_comentario'] = 7;
						} else if (mysqli_affected_rows($con_bd) == 1) {
							$_SESSION['cod_comentario'] = 6;
						} else {
							$_SESSION['cod_comentario'] = 7;
						}
					}
				} else {
					$_SESSION['cod_comentario'] = 8;
				}
			}
		}
		// Se cierra la conexión a la BD.
		mysqli_close($con_bd);
	}
	header('HTTP/1.1 303 See Other');
	header('Location: /leer.php?id=' . $id_publicacion . '#comentarios');
} else if (isset($_POST['eliminar_publicacion']) && ($conectado_steam)) {
	$id_publicacion = $_POST['id_publicacion'];
	
	// Se crea la conexión a la BD para leer las publicaciones y los comentarios.
	@$con_bd = new mysqli($datosbd['conexion'], $datosbd['usuario3'], $datosbd['contra3'], $datosbd['nombre_bd']);
	if ($con_bd->connect_error) {
		$_SESSION['cod_publicacion'] = 3;
	} else {
		prepararConexionUTF8($con_bd);
		// Antes tenemos que comprobar la autoridad del usuario para eliminar la publicación, usando su ID de Steam.
		include('steamauth/userInfo.php');
		$id_steam = $steamprofile['steamid'];
		$sent_prep = $con_bd->prepare("SELECT * FROM publicaciones JOIN usuarios ON usuarios.id = publicaciones.autor WHERE publicaciones.id = (?) AND usuarios.id_steam = (?)");
		$sent_prep_2 = $con_bd->prepare("SELECT * FROM usuarios WHERE id_steam = (?) AND permisos = (2)");
		if (!$sent_prep || !$sent_prep_2) {
			$_SESSION['cod_publicacion'] = 4;
		} else {
			$sent_prep->bind_param("is", $id_publicacion, $id_steam);
			$sent_prep_2->bind_param("s", $id_steam);
			if (!$sent_prep->execute() || !$sent_prep_2->execute()) {
				$_SESSION['cod_publicacion'] = 4;
			} else {
				$result_comentario = $sent_prep->get_result();
				$result_usuario = $sent_prep_2->get_result();
				// Comprobamos si el es el autor de la publicación o si tiene permisos de administrador.
				if (mysqli_num_rows($result_comentario) == 1 || mysqli_num_rows($result_usuario) == 1) {
					$sent_prep = $con_bd->prepare("DELETE FROM publicaciones WHERE id = (?)");
					if (!$sent_prep) {
						$_SESSION['cod_publicacion'] = 4;
					} else {
						$sent_prep->bind_param("i", $id_publicacion);
						if (!$sent_prep->execute()) {
							$_SESSION['cod_publicacion'] = 4;
						} else if (mysqli_affected_rows($con_bd) == 1) {
							$_SESSION['cod_publicacion'] = 6;
						} else {
							$_SESSION['cod_publicacion'] = 4;
						}
					}
				} else {
					$_SESSION['cod_publicacion'] = 5;
				}
			}
		}
		// Se cierra la conexión a la BD.
		mysqli_close($con_bd);
	}
	header('HTTP/1.1 303 See Other');
	if ($_SESSION['cod_publicacion'] == 6) {
		header('Location: /index.php');
	} else {
		header('Location: /leer.php?id=' . $id_publicacion);
	}
} else if(isset($_POST['actualizar_usuario']) && ($conectado_steam)) {
	$id_usuario = $_POST['actualizar_usuario'];
	$id_steam = $_POST['id_steam'];
	$array_usuario = obtenerDatosSteamUsuario($id_steam);
	$nombre = $array_usuario['response']['players'][0]['personaname'];
	$url_perfil = $array_usuario['response']['players'][0]['profileurl'];
	$url_avatar = $array_usuario['response']['players'][0]['avatar'];
	
	// Se crea la conexión a la BD para leer las publicaciones y los comentarios.
	@$con_bd = new mysqli($datosbd['conexion'], $datosbd['usuario4'], $datosbd['contra4'], $datosbd['nombre_bd']);
	if ($con_bd->connect_error) {
		$_SESSION['cod_usuario'] = 3;
	} else {
		prepararConexionUTF8($con_bd);
		// Antes tenemos que comprobar la autoridad del usuario modificar otros usuarios, usando su ID de Steam.
		include('steamauth/userInfo.php');
		$id_steam = $steamprofile['steamid'];
		$sent_prep = $con_bd->prepare("SELECT * FROM usuarios WHERE id_steam = (?) AND permisos = (2)");
		if (!$sent_prep) {
			$_SESSION['cod_usuario'] = 4;
		} else {
			$sent_prep->bind_param("s", $id_steam);
			if (!$sent_prep->execute()) {
				$_SESSION['cod_usuario'] = 4;
			} else {
				$result_usuario = $sent_prep->get_result();
				// Comprobamos si el es el autor de la publicación o si tiene permisos de administrador.
				if (mysqli_num_rows($result_usuario) == 1) {
					$sent_prep = $con_bd->prepare("UPDATE usuarios SET nombre=(?), url_perfil=(?), url_avatar=(?) WHERE id=(?)");
					if (!$sent_prep) {
						$_SESSION['cod_usuario'] = 2;
					} else {
						$sent_prep->bind_param("ssss", $nombre, $url_perfil, $url_avatar, $id_usuario);
						if (!$sent_prep->execute()) {
							$_SESSION['cod_usuario'] = 2;
						} else {
							$_SESSION['cod_usuario'] = 1;
						}
					}
				} else {
					$_SESSION['cod_usuario'] = 4;
				}
			}
		}
		// Se cierra la conexión a la BD.
		mysqli_close($con_bd);
	}
	header('HTTP/1.1 303 See Other');
	header('Location: /usuario.php?id=' . $id_usuario);
} else if(isset($_POST['cancelar'])) {
	$id_publicacion = $_POST['id_publicacion'];
	header('HTTP/1.1 303 See Other');
	header('Location: /leer.php?id=' . $id_publicacion . '#comentarios');
} else {
	header('Location: /index.php');
}
?>