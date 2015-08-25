<?php
mysql_connect('mysql1099.servage.net', 'dariostruz31', 'fernando1985') or die(mysql_error()) ;
mysql_select_db("dariostruz31") or die(mysql_error()) ;


if ($_FILES["imagen"]["error"] > 0){
	echo "ha ocurrido un error";
} else {

	$permitidos = array("image/jpg", "image/jpeg", "image/gif", "image/png");
	$limite_kb = 300;

	if (in_array($_FILES['imagen']['type'], $permitidos) && $_FILES['imagen']['size'] <= $limite_kb * 2048){
		$ruta = "uploads/" . $_FILES['imagen']['name'];

		if (!file_exists($ruta)){

			$resultado = @move_uploaded_file($_FILES["imagen"]["tmp_name"], $ruta);
			if ($resultado){
				$nombre = $_FILES['imagen']['name'];
				@mysql_query("INSERT INTO imagenes (imagen) VALUES ('$nombre')") ;
				echo "el archivo ha sido movido exitosamente";
			} else {
				echo "ocurrio un error al mover el archivo.";
			}
		} else {
			echo $_FILES['imagen']['name'] . ", este archivo existe";
		}
	} else {
		echo "archivo no permitido, es tipo de archivo prohibido o excede el tamano de $limite_kb Kilobytes";
	}
}

?>