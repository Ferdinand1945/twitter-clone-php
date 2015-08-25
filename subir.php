<?php
mysql_connect('mysql1099.servage.net', 'dariostruz31', 'fernando1985') or die(mysql_error()) ;
mysql_select_db("dariostruz31") or die(mysql_error()) ;


if ($_FILES["imagen"]["error"] > 0){
	echo "There was an error while uploading, my friend";
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
                header("Location: /uploadPic");      
            
            } else {
				echo "There was an error while uploading the picture.";
			}
		} else {
			echo $_FILES['imagen']['name'] . ", THIS FILE ALREADY EXISTS";
		}
	} else {
		echo "File not allowed, this extention is forbidden or the file exede the $limite_kb Kilobytes allowed.";
	}
}

?>