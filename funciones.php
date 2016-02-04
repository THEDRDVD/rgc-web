<?php
	/* Comprobamos si ya nos hemos conectado a Steam.
	Se usa en 'index.php', 'leer.php', 'escribir.php', 'usuario.php' y 'acciones_post.php'.
	*/
	function conectadoSteam() {
		if(isset($_SESSION['steamid'])) {
			return true;
		} else {
			return false;
		}
	}
	
	/* Prepara una conexión a una BD para trabajar en UTF8. Se ejecuta siempre que se crea la conexión
	y se ha validado que es correcta.
	Se le envía como parámetro la conexión a la BD.
	Se usa en 'index.php', 'leer.php', 'escribir.php' y 'usuario.php'.
	*/
	function prepararConexionUTF8($con_bd) {
		mysqli_query($con_bd, "SET CHARACTER_SET_CLIENT='utf8'");
		mysqli_query($con_bd, "SET CHARACTER_SET_RESULTS='utf8'");
		mysqli_query($con_bd, "SET CHARACTER_SET_CONNECTION='utf8'");
	}
	
	/* Devuelve una cadena con el tipo de publicación correspondiente según el número de la categoría.
	Se le envía como parámetro el entero que representa la categoría de la publicación.
	Se usa en 'index.php'.
	*/
	function tratarCategoria($num_categoria) {
		if ($num_categoria == 1) {
			return "Noticia";
		} else if ($num_categoria == 2) {
			return "Artículo";
		} else if ($num_categoria == 3) {
			return "Análisis";
		} else if ($num_categoria == 4) {
			return "Evento";
		} else if ($num_categoria == 5) {
			return "Actualización";
		}
	}
	
	/* Imprime la primera parte de la página, desde 'head' (con la excepción de la etiqueta 'title')
	hasta la 'zona_principal' del 'body'.
	Se usa en la página 'leer.php' y 'usuario.php'.
	*/
	function mostrarPrimeraPartePagina() {
		echo '<meta charset="utf-8" />
		<link rel="icon" type="image/png" href="./img/logo_rgc01.png">
		<!-- Carga el CSS de RGC -->
		<link rel="stylesheet" type="text/css" href="./rgc.css">
		</head><body>
		<div id="cabecera">';
		include('cabecera.php');
		echo '</div>
		<div id="cuerpo_pagina">
		<div id="zona_principal">';
	}
	
	/* Devuelve, a partir de un entero que representa la categoría del usuario, la cadena de texto
	que describe la clase de usuario que es.
	Se le envía como parámetro el entero que representa los permisos del usuario.
	Se usa en la página 'usuario.php'.
	*/
	function claseUsuario($permisos) {
		if ($permisos == 0) {
			return "Usuario normal";
		} else if ($permisos == 1) {
			return "Redactor de la página";
		} else if ($permisos == 2) {
			return "Administrador de la página";
		}
	}
	
	/* Comprueba si el usuario conectado existe en la BD y qué permisos tiene.
	Se le envían como parámetros la conexión a la BD y la ID de Steam del usuario actual.
	Se usa en la página 'leer.php' y 'escribir.php'.
	*/
	function comprobarPermisosUsuario($con_bd, $id_steam) {
		$sent_prep = $con_bd->prepare("SELECT * FROM usuarios WHERE id_steam LIKE (?)");
		// No se ha podido realizar la consulta por algún problema con la tabla en sí.
		if (!$sent_prep) {
			return -1;
		} else {
			$sent_prep->bind_param("s", $id_steam);
			$sent_prep->execute();
			$result = $sent_prep->get_result();
			
			// El usuario está registrado en la BD. Hay que comprobar los permisos.
			if (mysqli_num_rows($result) == 1) {
				while($usuario = $result->fetch_assoc()) {
					return $usuario['permisos']; // Devolvemos el tipo de categoría/permisos del usuario.
				}
			// El usuario no está registrado en la BD. Esto no debería pasar, ya que todos los usuarios que se conectan a la página mediante Steam aparecen en la BD. Pero por si acaso.
			} else {
				return -1;
			}
		}
	}
	
	/* Realiza el procedimiento de muestra de la página 'escribir.php', según los parámetros enviados,
	valorando en qué "modo" estamos: vista previa, enviar publicación o por defecto.
	Se le envían como parámetros la conexión a la BD y la ID de Steam del usuario actual.
	Se usa en 'escribir.php'.
	*/
	function paginaEscribir($con_bd, $id_steam) {
		// Modo de 'Vista previa'
		if (isset($_POST['vista_previa'])) {
			mostrarFormularioEscribirPublicacion($con_bd, $id_steam, true);
		// Modo de 'Publicar texto'
		} else if (isset($_POST['enviar_texto'])) {
			@$titulo = $_POST['titulo'];
			@$resumen = $_POST['resumen'];
			@$contenido = $_POST['contenido'];
			@$autor = $_POST['autor'];
			@$idioma = $_POST['idioma'];
			@$categoria = $_POST['categoria'];
			@$fecha_actual = $_POST['fecha_actual'];
			if ($fecha_actual) {
				@$dia = date('j', time());
				@$mes = date('n', time());
				@$ano = date('Y', time());
				@$hora = date('H', time());
				@$minuto = date('i', time());
				@$segundo = date('s', time());
			} else {
				@$dia = $_POST['dia'];
				@$mes = $_POST['mes'];
				@$ano = $_POST['ano'];
				@$hora = $_POST['hora'];
				@$minuto = $_POST['minuto'];
				@$segundo = $_POST['segundo'];
			}
			
			// Si está vacío, volvemos al formulario de escritura de la publicación.
			if (empty($titulo) || empty($resumen) || empty($contenido)) {
				mostrarFormularioEscribirPublicacion($con_bd, $id_steam, true);
			// Se intenta crear la conexión a la BD para escribir publicaciones.
			} else {
				// Incluímos 'datosbd.php' para conseguir los datos de conexión a la base de datos.
				include('datosbd.php');
				try {
					@$con_bd_2 = new mysqli($datosbd['conexion'], $datosbd['usuario3'], $datosbd['contra3'], $datosbd['nombre_bd']);
					$error_bd = false;
				// En caso de fallo, se muestra un mensaje de error, y se cambia el valor de la variable $error_bd a 'true' para que no intente usar la BD.
				} catch (Exception $e) {
					echo '<div class="notificacion_error"><b>Ha fallado la conexión con la base de datos.</b> Vuelve más tarde, a ver si se ha podido solucionar el problema.</div></div>';
					$error_bd = true;
				}
				
				if (!$error_bd) {
					// Se juntan estas variables para formar la cadena de texto que representa el 'TIMESTAMP'.
					$fecha = "$ano-$mes-$dia $hora:$minuto:$segundo";
					
					prepararConexionUTF8($con_bd_2);
					// Primero comprueba si el texto se creará o se editará.
					if (isset($_POST['editar'])) {
						$sent_prep = $con_bd_2->prepare("UPDATE publicaciones SET titulo=(?), resumen=(?), contenido=(?), autor=(?), idioma=(?), categoria=(?), fecha=(?) WHERE id=(?)");
						$sent_prep->bind_param("sssisisi", $titulo, $resumen, $contenido, $autor, $idioma, $categoria, $fecha, $_POST['editar']);
					} else {
						$sent_prep = $con_bd_2->prepare("INSERT INTO publicaciones (titulo, resumen, contenido, autor, idioma, categoria, fecha) VALUES (?, ?, ?, ?, ?, ?, ?)");
						$sent_prep->bind_param("sssisis", $titulo, $resumen, $contenido, $autor, $idioma, $categoria, $fecha);
					}
					
					if ($sent_prep->execute()) {
						if (isset($_POST['editar'])) {
							$_SESSION['cod_publicacion'] = 2;
							$id_publicacion = $_POST['editar'];
						} else {
							$_SESSION['cod_publicacion'] = 1;
							$id_publicacion = $con_bd_2->insert_id;
						}
						mysqli_close($con_bd_2);
						header('HTTP/1.1 303 See Other');
						header('Location: leer.php?id=' . $id_publicacion);
					} else {
						echo '<div class="notificacion_error"><b>No se ha podido publicar el texto en la base de datos.</b> Inténtalo de nuevo.</div>';
					}
					
					// Cerramos la conexión tras terminar de insertar el texto en la base de datos.
					mysqli_close($con_bd_2);
				}
			}
		// Modo por defecto
		} else {
			mostrarFormularioEscribirPublicacion($con_bd, $id_steam, false);
		}
	}
	
	/* Muestra el formulario para escribir una publicación, con todos los campos correspondientes.
	Se le envían como parámetros la conexión a la BD, la ID de Steam del usuario actual, y un valor booleano
	que indica si estamos en "modo vista previa".
	Se usa en 'escribir.php'.
	*/
	function mostrarFormularioEscribirPublicacion($con_bd, $id_steam, $vista_previa) {
		@$editar = $_POST['editar'];
		// La variable 'conseguir_datos_recientes_publicacion' se recoge una única vez al hacer clic en el botón 'Editar' de la página 'leer.php' cuando queremos editar una publicación.
		// Consigue los datos de la publicación de la BD, por un lado, para evitar problemas al enviar a través de la petición POST, 
		// y también para tener los datos más recientes de la publicación hasta el momento.
		if (isset($_POST['conseguir_datos_recientes_publicacion'])) {
			$sent_prep = $con_bd->prepare("SELECT publicaciones.*, usuarios.id AS id_autor, usuarios.nombre, usuarios.id_steam FROM publicaciones JOIN usuarios ON usuarios.id = publicaciones.autor WHERE publicaciones.id = (?)");
			// El valor de la variable $editar contiene la ID de la publicación.
			$sent_prep->bind_param("i", $editar);
			$sent_prep->execute();
			$result_publicacion = $sent_prep->get_result();
			while($publicacion = $result_publicacion->fetch_assoc()) {
				@$titulo = $publicacion['titulo'];
				@$resumen = $publicacion['resumen'];
				@$contenido = $publicacion['contenido'];
				@$autor = $publicacion['autor'];
				@$idioma = $publicacion['idioma'];
				@$categoria = $publicacion['categoria'];
				@$fecha_entera = date_parse($publicacion['fecha']);
				@$dia = $fecha_entera['day'];
				@$mes = $fecha_entera['month'];
				@$ano = $fecha_entera['year'];
				@$hora = $fecha_entera['hour'];
				@$minuto = $fecha_entera['minute'];
				@$segundo = $fecha_entera['second'];
			}
		// En condiciones normales, tanto si estamos editando la publicación o escribiendo una nueva, se intenta conseguir los datos
		// según lo que el usuario haya podido enviar a través de POST en la misma página 'escribir.php'.
		} else {
			@$titulo = $_POST['titulo'];
			@$resumen = $_POST['resumen'];
			@$contenido = $_POST['contenido'];
			@$autor = $_POST['autor'];
			@$idioma = $_POST['idioma'];
			@$categoria = $_POST['categoria'];
			@$dia = $_POST['dia'];
			@$mes = $_POST['mes'];
			@$ano = $_POST['ano'];
			@$hora = $_POST['hora'];
			@$minuto = $_POST['minuto'];
			@$segundo = $_POST['segundo'];
		}
		if (isset($editar)) {
			@$fecha_actual = false;
		} else {
			@$fecha_actual = true;
		}
		date_default_timezone_set('Europe/Madrid');
		$lista_usuarios = $con_bd->query("SELECT * FROM usuarios ORDER BY permisos DESC");
		
		if (!isset($autor)) {
			while ($usuario = $lista_usuarios->fetch_array()) {
				if ($usuario['id_steam'] == $id_steam) {
					$autor = $usuario['id'];
					$lista_usuarios->data_seek(0);
					break;
				}
			}
		}
		
		if ($vista_previa) {
			@$fecha_actual = $_POST['fecha_actual'];
			$titulo = strip_tags($titulo, '<i>');
			
			if (empty($titulo)) {
				echo '<div class="notificacion_advertencia"><b>¡Atención! No has introducido ningún título para la publicación.</b> Este campo es obligatorio.</div>';
			}
			if (empty($resumen)) {
				echo '<div class="notificacion_advertencia"><b>¡Atención! No has introducido ningún texto para el resumen de la publicación.</b> Este campo es obligatorio.</div>';
			}
			if (empty($contenido)) {
				echo '<div class="notificacion_advertencia"><b>¡Atención! No has escrito nada para la publicación en sí.</b> Este campo es obligatorio.</div>';
			}
			echo '<h1 class"titulos_escribir_publicacion">Vista previa</h1>';
			echo '<div class="caja_lectura_publicacion">';
			echo '<h2 class="titulo_publicacion">' . $titulo . '</h2>';
			echo '<div class="resumen_publicacion">' . str_replace("\n", "<br>", $resumen) . '</div><hr>';
			echo '<div class="contenido_publicacion">' . str_replace("\n", "<br>", $contenido) . '</div>';
			echo '</div>';
		}
		
		if (isset($editar)) {
			echo '<h1>Edita la publicación</h1>';
		} else {
			echo '<h1>Escribe la publicación</h1>';
		}
		echo '<form id="form_publicacion" action="escribir.php" method="post">';
		
		// Fecha de la publicación.
		echo '<div id="form_parte_fecha"><div><label><input id="checkbox_fecha_actual" type="checkbox" value="fecha_actual"';
		if ($fecha_actual) {
			@$dia = date('j', time());
			@$mes = date('n', time());
			@$ano = date('Y', time());
			@$hora = date('H', time());
			@$minuto = date('i', time());
			@$segundo = date('s', time());
			echo ' checked name="fecha_actual">Usar fecha y hora actuales</label></div><div id="inputs_fecha" class="campo_fecha_formulario_escribir">' .
			'<label>Fecha:</label> <input id="input_fecha_dia" type="number" min="1" max="31" name="dia" placeholder="Día" value="' . $dia . '"> ' .
			'<input id="input_fecha_mes" type="number" min="1" max="12" name="mes" placeholder="Mes" value="' . $mes . '"> ' .
			'<input id="input_fecha_ano" type="number" min="0" name="ano" placeholder="Año" value="' . $ano . '"></div><div id="inputs_hora" class="campo_fecha_formulario_escribir">' .
			'<label>Hora:</label> <input id="input_hora" type="number" min="0" max="23" name="hora" placeholder="Hora" value="' . $hora . '"> ' .
			'<input id="input_minuto" type="number" min="0" max="59" name="minuto" placeholder="Minuto" value="' . $minuto . '"> ' .
			'<input id="input_segundo" type="number" min="0" max="59" name="segundo" placeholder="Segundo" value="' . $segundo . '"></div></div>';
		} else {
			echo ' name="fecha_actual">Usar fecha y hora actuales</label></div><div id="inputs_fecha" class="campo_fecha_formulario_escribir">' .
			'<label>Fecha:</label> <input id="input_fecha_dia" type="number" min="1" max="31" name="dia" placeholder="Día" value="' . $dia . '"> ' .
			'<input id="input_fecha_mes" type="number" min="1" max="12" name="mes" placeholder="Mes" value="' . $mes . '"> ' .
			'<input id="input_fecha_ano" type="number" min="0" name="ano" placeholder="Año" value="' . $ano . '"></div><div id="inputs_hora" class="campo_fecha_formulario_escribir">' .
			'<label>Hora:</label> <input id="input_hora" type="number" min="0" max="23" name="hora" placeholder="Hora" value="' . $hora . '"> ' .
			'<input id="input_minuto" type="number" min="0" max="59" name="minuto" placeholder="Minuto" value="' . $minuto . '"> ' .
			'<input id="input_segundo" type="number" min="0" max="59" name="segundo" placeholder="Segundo" value="' . $segundo . '"></div></div>';
		}
		
		// Listado de autores para la publicación.
		echo '<div><label for="select_autor">Autor:</label> <select id="select_autor" form="form_publicacion" name="autor" class="caja_campo_formulario_escribir">';
		$cat_admin = false;
		$cat_redactor = false;
		$cat_norm = false;
		while ($usuario = $lista_usuarios->fetch_array()) {
			if (!$cat_admin && $usuario['permisos'] == 2) {
				$cat_admin = true;
				echo '<optgroup label="Administradores"></optgroup>';
			} else if (!$cat_redactor && $usuario['permisos'] == 1) {
				$cat_redactor = true;
				echo '<optgroup label="Redactores"></optgroup>';
			} else if (!$cat_norm && $usuario['permisos'] == 0) {
				$cat_norm = true;
				echo '<optgroup label="Usuarios normales"></optgroup>';
			}
			echo '<option value="' . $usuario['id'] . '"';
			if ($usuario['id'] == $autor) {
				echo ' selected';
			}
			echo '>' . $usuario['nombre'] . '</option>';
		}
		echo '</select></div>';
		
		// Listado de idiomas posibles para la publicación.
		echo '<div><label for="select_idioma">Idioma:</label> <select id="select_idioma" form="form_publicacion" name="idioma" class="caja_campo_formulario_escribir">';
		echo '<option value="es"';
		if (isset($idioma)) {
			if ($idioma == 'es') {
				echo ' selected';
			}
		}
		echo '>Español</option>';
		echo '<option value="en"';
		if (isset($idioma)) {
			if ($idioma == 'en') {
				echo ' selected';
			}
		}
		echo '>Inglés</option>';
		echo '<option value="pt"';
		if (isset($idioma)) {
			if ($idioma == 'pt') {
				echo ' selected';
			}
		}
		echo '>Portugués</option>';
		echo '<option value="fr"';
		if (isset($idioma)) {
			if ($idioma == 'fr') {
				echo ' selected';
			}
		}
		echo '>Francés</option>';
		echo '<option value="de"';
		if (isset($idioma)) {
			if ($idioma == 'de') {
				echo ' selected';
			}
		}
		echo '>Alemán</option>';
		echo '</select></div>';
		
		// Listado de categorías posibles del texto.
		echo '<div><label for="select_categoria">Categoría:</label> <select id="select_categoria" form="form_publicacion" name="categoria" class="caja_campo_formulario_escribir">';
		echo '<option value=1';
		if (isset($categoria)) {
			if ($categoria == 1) {
				echo ' selected';
			}
		}
		echo '>Noticia</option>';
		echo '<option value=2';
		if (isset($categoria)) {
			if ($categoria == 2) {
				echo ' selected';
			}
		}
		echo '>Artículo</option>';
		echo '<option value=3';
		if (isset($categoria)) {
			if ($categoria == 3) {
				echo ' selected';
			}
		}
		echo '>Análisis</option>';
		echo '<option value=4';
		if (isset($categoria)) {
			if ($categoria == 4) {
				echo ' selected';
			}
		}
		echo '>Evento</option>';
		echo '<option value=5';
		if (isset($categoria)) {
			if ($categoria == 5) {
				echo ' selected';
			}
		}
		echo '>Actualización</option>';
		echo '</select></div>';
		
		// Campos de texto del formulario.
		echo '<div><label for="input_titulo">Título:</label><input id="input_titulo" class="caja_campo_formulario_escribir" type="text" form="form_publicacion" name="titulo" value="' . $titulo . '">';
		echo '<label for="input_resumen">Resumen:</label><textarea id="input_resumen" class="caja_campo_formulario_escribir" rows=5 form="form_publicacion" name="resumen">' . $resumen . '</textarea>';
		echo '<label for="input_contenido">Contenido:</label><textarea id="input_contenido" class="caja_campo_formulario_escribir" rows=10 form="form_publicacion" name="contenido">' . $contenido . '</textarea>';
		echo '<button class="boton_normal" type="submit" name="vista_previa"><b>Vista previa</b></button> ';
		echo '<button class="boton_normal" type="submit" name="enviar_texto"><b>Publicar</b></button></div>';
		// Si está puesta la variable de editar texto la mantenemos.
		if (isset($editar)) {
			echo '<input type="hidden" name="editar" value="' . $_POST['editar'] . '">';
		}
		echo '</form>';
	}
	
	/* Comprueba si el usuario actual conectado existe en la BD y si los datos están al día según los datos de su última conexión.
	Se ocupa de insertar los nuevos usuarios cuya ID de Steam no exista aún en la BD, y a los que ya existen, les actualiza los datos.
	Se usa en 'steamauth/steamauth.php'.
	*/
	function actualizarUsuarioActual() {
		include('steamauth/userInfo.php');
		include('datosbd.php');
		$id_steam_actual = $steamprofile['steamid'];
		$nombre_steam_actual = $steamprofile['personaname'];
		$perfil_steam_actual = $steamprofile['profileurl'];
		$avatar_steam_actual = $steamprofile['avatar'];
		try {
			@$con_bd_1 = new mysqli($datosbd['conexion'], $datosbd['usuario1'], $datosbd['contra1'], $datosbd['nombre_bd']);
		} catch (Exception $e) {
			return false;
		}
		prepararConexionUTF8($con_bd_1);
		
		$sent_prep = $con_bd_1->prepare("SELECT * FROM usuarios WHERE id_steam LIKE (?)");
		if (!$sent_prep) {
			return false;
		} else {
			$sent_prep->bind_param("s", $id_steam_actual);
			$sent_prep->execute();
			$result = $sent_prep->get_result();
			
			// El usuario está registrado en la BD. Hay que comprobar que los campos están al día.
			if (mysqli_num_rows($result) == 1) {
				while($usuario = $result->fetch_assoc()) {
					// Alguno de los datos de Steam del usuario en la BD no están al día. Hay que actualizarlos.
					if (($usuario['nombre'] != $nombre_steam_actual) || ($usuario['id_steam'] != $id_steam_actual) 
						|| ($usuario['url_perfil'] != $perfil_steam_actual) || ($usuario['url_avatar'] != $avatar_steam_actual)) {
						try {
							@$con_bd_2 = new mysqli($datosbd['conexion'], $datosbd['usuario4'], $datosbd['contra4'], $datosbd['nombre_bd']);
						} catch (Exception $e) {
							return false;
						}
						prepararConexionUTF8($con_bd_2);
						
						$id_usuario = $usuario['id'];
						$sent_prep = $con_bd_2->prepare("UPDATE usuarios SET nombre=?, id_steam=?, url_perfil=?, url_avatar=? WHERE id=?");
						if (!$sent_prep) {
							return false;
						} else {
							$sent_prep->bind_param("ssssi", $nombre_steam_actual, $id_steam_actual, $perfil_steam_actual, $avatar_steam_actual, $id_usuario);
							$sent_prep->execute();
						}
						mysqli_close($con_bd_2);
					}
				}
			// El usuario no existe y hay que crearlo en la tabla 'usuarios', aunque no tendrá permisos para escribir la noticia.
			} else {
				try {
					@$con_bd_2 = new mysqli($datosbd['conexion'], $datosbd['usuario4'], $datosbd['contra4'], $datosbd['nombre_bd']);
				} catch (Exception $e) {
					return false;
				}
				prepararConexionUTF8($con_bd_2);
				
				$sent_prep = $con_bd_2->prepare("INSERT INTO usuarios (nombre, id_steam, url_perfil, url_avatar, permisos) VALUES (?, ?, ?, ?, 0)");
				if (!$sent_prep) {
					return false;
				} else {
					$sent_prep->bind_param("ssss", $nombre_steam_actual, $id_steam_actual, $perfil_steam_actual, $avatar_steam_actual);
					$sent_prep->execute();
				}
				mysqli_close($con_bd_2);
			}
			mysqli_close($con_bd_1);
			return true;
		}
	}
	
	/* A través de la API web de Steam, conseguimos un array (originalmente en JSON) con toda la información de Steam del usuario.
	Se le envía como parámetro la ID de Steam del usuario del cuál queremos conseguir la información.
	Se usa en 'acciones_post.php' (e indirectamente, en 'usuario.php').
	*/
	function obtenerDatosSteamUsuario($id_steam) {
		include("steamauth/settings.php");
		@$url = file_get_contents("http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=".$steamauth['apikey']."&steamids=".$id_steam);
		$content = json_decode($url, true);
		return $content;
	}
?>