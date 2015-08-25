<div class="container">
<div id="posts" class="sixteen columns panel left">
    <h1>Your Pictures</h1>  

  
  <div class="container">
    <div class="row pictures">
  <?php
//conexion a la base de datos
mysql_connect('mysql1099.servage.net', 'dariostruz31', 'fernando1985') or die(mysql_error()) ;
mysql_select_db("dariostruz31") or die(mysql_error()) ;

//vamos a crear nuestra consulta SQL
$consulta = "SELECT imagen FROM imagenes";
//con mysql_query la ejecutamos en nuestra base de datos indicada anteriormente
//de lo contrario mostraremos el error que ocaciono la consulta y detendremos la ejecucion.
$resultado= @mysql_query($consulta) or die(mysql_error());

//si el resultado fue exitoso
//obtendremos los datos que ha devuelto la base de datos
//y con un ciclo recorreremos todos los resultados
while ($datos = @mysql_fetch_assoc($resultado) ){
	//ruta va a obtener un valor parecido a "imagenes/nombre_imagen.jpg" por ejemplo
	$ruta = "uploads/" . $datos['imagen'];

	//ahora solamente debemos mostrar la imagen
	echo "<img src='$ruta' />";
}

?>
  
  </div>
  </div>
  </div>
  </div>