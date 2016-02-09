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
$es_pagina_publicaciones = true;
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Rebel Gamers Clan - Inicio</title>
		<meta charset="utf-8" />
		<link rel="icon" type="image/png" href="./img/logo_rgc01.png">
		<!-- Carga el CSS de RGC -->
		<link rel="stylesheet" type="text/css" href="./rgc.css">
	</head>
	<body>
		<div id="cabecera">
			<?php include('cabecera.php'); ?>
		</div>
		<div id="cuerpo_pagina">
			<section id="zona_principal">
			<?php
			// Se intenta crear la conexión a la BD para leer las publicaciones y los comentarios:
			try {
				$con_bd = new mysqli($datosbd['conexion'], $datosbd['usuario1'], $datosbd['contra1'], $datosbd['nombre_bd']);
				// Si falla, se muestra un mensaje de error, y se cambia el valor de la variable $error_bd a 'true' para que no intente usar la BD.
			} catch (Exception $e) {
				echo '<div class="notificacion_error"><b>Ha fallado la conexión con la base de datos.</b> ' .
				'Vuelve más tarde, a ver si se ha podido solucionar el problema.</div>';
				$error_bd = true;
			}
			if (isset($_SESSION['cod_publicacion'])) {
				$cod_publicacion = $_SESSION['cod_publicacion'];
				unset($_SESSION['cod_publicacion']);
				if ($cod_publicacion == 6) {
					echo '<div class="notificacion_correcto">Se ha eliminado la publicación correctamente.</div>';
				}
			}
			// Si la conexión es correcta, buscamos las publicaciones en la BD.
			if (!$error_bd) {
				prepararConexionUTF8($con_bd);
				// Listado completo de publicaciones
				if (isset($_GET['completo'])) {
					$sent = "SELECT publicaciones.id AS id_publicacion, publicaciones.fecha, publicaciones.titulo, publicaciones.resumen, " . 
					"publicaciones.contenido, publicaciones.idioma, publicaciones.texto_original, publicaciones.categoria, usuarios.id AS " .
					"id_autor, usuarios.nombre, usuarios.id_steam, usuarios.url_perfil, usuarios.url_avatar FROM publicaciones " .
					"JOIN usuarios ON usuarios.id = publicaciones.autor";
					if (isset($_GET['categoria'])) {
						$categoria_introducida = $_GET['categoria'];
						$sent = $sent . " WHERE categoria = $categoria_introducida";
					}
					$sent = $sent . " ORDER BY id_publicacion DESC";
					$result_list_textos = $con_bd->query($sent);
					if (!$result_list_textos) {
						echo '<div class="notificacion_error"><b>Ha habido un problema obteniendo los datos de las publicaciones ' .
						'de la base de datos.</b> Vuelve más tarde, a ver si se ha podido solucionar el problema.</div>';
					} else {
						while($texto = $result_list_textos->fetch_assoc()) {
							echo '<article class="caja_lectura_publicacion">';
							echo '<header><h1 class="titulo_publicacion"><a href="leer.php?id=' . $texto['id_publicacion'] . '" ' .
							'title="Leer esta publicación">' . $texto['titulo'] . '</a></h1>';
							// Si el autor es Anónimo, no se le pone un enlace a la página de su perfil.
							if ($texto['nombre'] == 'Anónimo') {
								echo '<h2 class="datos_publicacion">' . tratarCategoria($texto['categoria']) . ' - Escrito el ' .
								$texto['fecha'] . ' por <a>' . $texto['nombre'] . '</a></h2>';
								// Si el autor no es anónimo, se pone un enlace para que los usuarios puedan ver su perfil y lo que ha escrito.
							} else {
								echo '<h2 class="datos_publicacion">' . tratarCategoria($texto['categoria']) . ' - Escrito el ' .
								$texto['fecha'] . ' por <a href="usuario.php?id=' . $texto['id_autor'] .
								'" title="Ver perfil del autor">' . $texto['nombre'] . '</a></h2>';
							}
							echo '<div class="resumen_publicacion">' . $texto['resumen'] . '</div></header>';
							echo '</article><br>';
						}
					}
					// Cerramos la conexión con la BD, ya no la necesitamos.
					mysqli_close($con_bd);
					
				// Página de inicio con las publicaciones más recientes:
				} else {
					// Comprobamos si nos han enviado información de búsqueda a través del formulario:
					$sent = "SELECT publicaciones.id AS id_publicacion, publicaciones.fecha, publicaciones.titulo, publicaciones.resumen, " .
					"publicaciones.contenido, publicaciones.idioma, publicaciones.texto_original, publicaciones.categoria, usuarios.id AS id_autor, " .
					"usuarios.nombre, usuarios.id_steam, usuarios.url_perfil, usuarios.url_avatar FROM publicaciones JOIN usuarios ON usuarios.id = publicaciones.autor";
					
					if (isset($_GET['buscar'])) {
						// Descomponemos las palabras clave que nos envían a través del parámetro 'buscar'.
						$array_palabras_buscar = explode(" ", $_GET['buscar']);
						// Indicamos los carácteres que podrían ser "perjudiciales". Principalmente para evitar la inyección SQL.
						$caracteres_malos = array("\"", "'", "=", "`", "´");
						$array_palabras_buscar = str_replace($caracteres_malos, "", $array_palabras_buscar);
						$num_palabras_buscar = sizeof($array_palabras_buscar);
						
						// Si se ha introducido alguna palabra a tener en cuenta, es lo primero que añadiremos a la variable $sent.
						if ($num_palabras_buscar > 0) {
							$sent = $sent . " WHERE ((titulo LIKE '%" . $array_palabras_buscar[0] . "%' OR resumen LIKE '%" . $array_palabras_buscar[0] . 
							"%' OR contenido LIKE '%" . $array_palabras_buscar[0] . "%')";
							for ($i = 1; $i < $num_palabras_buscar; $i++) {
								$sent = $sent . " AND (titulo LIKE '%" . $array_palabras_buscar[$i] . "%' OR resumen LIKE '%" . $array_palabras_buscar[$i] . "%' OR contenido LIKE '%" . $array_palabras_buscar[0] . "%')";
							}
							$sent = $sent . ")";
						}
					}
					if (isset($_GET['categoria'])) {
						// Descomponemos las palabras clave que nos envían a través del parámetro 'categoria'.
						$array_categorias = $_GET['categoria'];
						// Indicamos los carácteres que podrían ser "perjudiciales". Principalmente para evitar la inyección SQL.
						$caracteres_malos = array("\"", "'", "=", "`", "´");
						$array_categorias = str_replace($caracteres_malos, "", $array_categorias);
						$num_categorias = sizeof($array_categorias);
						
						// Si se ha introducido alguna categoría a tener en cuenta, lo indicamos.
						if ($num_categorias > 0) {
							if (!strpos($sent, "WHERE")) {
								$sent = $sent . " WHERE (categoria = " . $array_categorias[0];
								for ($i = 1; $i < $num_categorias; $i++) {
									$sent = $sent . " OR categoria = " . $array_categorias[$i];
								}
							} else {
								$sent = $sent . " AND (categoria = " . $array_categorias[0];
								for ($i = 1; $i < $num_categorias; $i++) {
									$sent = $sent . " OR categoria = " . $array_categorias[$i];
								}
							}
							$sent = $sent . ")";
						}
					}
					
					// Si se ha introducido el nombre del autor, lo indicamos.
					if (isset($_GET['autor'])) {
						$autor_introducido = $_GET['autor'];
						if (!strpos($sent, "WHERE")) {
							$sent = $sent . " WHERE (usuarios.nombre LIKE '%" . $autor_introducido . "%')";
						} else {
							$sent = $sent . " AND (usuarios.nombre LIKE '%" . $autor_introducido . "%')";
						}
					}
					
					// Ahora el número de publicaciones. Si no se ha indicado nada, se mostrarán 5 publicaciones. En caso contrario, tantas como el usuario haya indicado.
					$num_publicaciones = 5;
					if (isset($_GET['num_publicaciones']) && is_numeric($_GET['num_publicaciones'])) {
						$num_publicaciones = $_GET['num_publicaciones'];
					}
					$sent = $sent . " ORDER BY id_publicacion DESC LIMIT " . $num_publicaciones;
					$result_list_textos = $con_bd->query($sent);
					if (!$result_list_textos) {
						echo '<div class="notificacion_error"><b>Ha habido un problema obteniendo los datos de las publicaciones de la base de datos.</b> ' . 
						'Vuelve más tarde, a ver si se ha podido solucionar el problema.</div>';
					} else {
						if (mysqli_num_rows($result_list_textos) > 0) {
							while($texto = $result_list_textos->fetch_assoc()) {
								echo '<div class="caja_lectura_publicacion">';
								echo '<h1 class="titulo_publicacion"><a href="leer.php?id=' . $texto['id_publicacion'] . '" ' .
								'title="Leer esta publicación">' . $texto['titulo'] . '</a></h1>';
								// Si el autor es Anónimo, no se le pone un enlace a la página de su perfil.
								if ($texto['nombre'] == 'Anónimo') {
									echo '<h2 class="datos_publicacion">' . tratarCategoria($texto['categoria']) . ' - Escrito el ' .
									$texto['fecha'] . ' por <a>' . $texto['nombre'] . '</a></h2>';
									// Si el autor no es anónimo, se pone un enlace para que los usuarios puedan ver su perfil y lo que ha escrito.
								} else {
									echo '<h2 class="datos_publicacion">' . tratarCategoria($texto['categoria']) . ' - Escrito el ' .
									$texto['fecha'] . ' por <a href="usuario.php?id=' . $texto['id_autor'] .
									'" title="Ver perfil del autor">' . $texto['nombre'] . '</a></h2>';
								}
								echo '<p class="resumen_publicacion">' . $texto['resumen'] . '</p>';
								echo '</div><br>';
							}
							// Botón con el listado de noticias completo
							echo '<form action="index.php?completo" method="get"><button class="boton_normal" ' .
							'title="Muestra todas las publicaciones escritas hasta ahora, ordenadas de más reciente a más antigua."' .
							' type="submit" name="completo" value="1"><b>Ver listado completo de publicaciones</b></button></form>';
						} else {
							echo '<div class="notificacion_advertencia"><b>No se han encontrado publicaciones.</b></div>';
						}
					}
					// Cerramos la conexión con la BD, ya no la necesitamos.
					mysqli_close($con_bd);
				}
			}
			?>
			</section>
			<?php include('zona_lateral.php'); ?>
		</div>
		<?php include('pie_pagina.php'); ?>
	</body> 
</html>