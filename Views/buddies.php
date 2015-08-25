<div class="container wrapper">
  <div class="nine columns">
    <div id="createPost" class="panel">
      <h1>Say what you thinking at loud!</h1>
      <p>
      <form action="/theposts" method="post">
        <textarea name="text" class="inputs postText size-input-1"></textarea>
        <div class="row">
          <input type="submit" value="Post something!">
        </div>
      </form>
<form action="subir.php" method="POST" enctype="multipart/form-data">
	<label for="imagen">Imagen:</label>
	<input type="file" name="imagen" id="imagen" />
	<input type="submit" name="subir" value="Upload a pic!"/>
</form>

   </p>
</div>
</div>


<div class="seven columns panel left">
<p>Here is gonna be your avatar picture 250 x 250 px</p>
  <p>So this part still under contruction.</p>

</div>

<div class="nine columns panel left">
  <h1>What we all are saying.</h1>
  <?php foreach($tposts as $theposts){ ?>
  <div class="postTabel">
    <img class="avatar" src="/images/Avatar.png">
    <span class="name"><?php echo $theposts->name; ?></span> @<?php echo $theposts->username; ?> 
    <span class="time">
      <?php 
                                      $timeSince = time() - strtotime($theposts->created_at); 
                                      if($timeSince < 60)
                                      {
                                        echo $timeSince . "s";
                                      }
                                      else if($timeSince < 3600)
                                      {
                                        echo floor($timeSince / 60) . "m";
                                      }
                                      else if($timeSince < 86400)
                                      {
                                        echo floor($timeSince / 3600) . "h";
                                      }
                                      else{
                                        echo floor($timeSince / 86400) . "d";
                                      }
      ?>
    </span>
    <p><?php echo $theposts->theposts; ?></p>
  </div>
  <?php } ?>			
</div>

  <div class="seven columns panel left">
  <div id="thediv">
    <h1>Your Profile!</h1>
    <div class="postTabel">
      <img class="avatar" src="/images/Avatar.png">
      <span class="name"><?php echo $User->name; ?></span> @<?php echo $User->username; ?>
      <p>
        <?php echo $userData->theposts_count . " "; echo ($userData->theposts_count != 1) ? "Posts" : "Post"; ?>
        <span class="spacing"><?php echo $userData->followers . " "; echo ($userData->followers != 1) ? "Followers" : "Follower"; ?></span>
        <span class="spacing"><?php echo $userData->following . " Following"; ?></span><br>
      </p>
      <div class="boxing">
        <h4>Your last post was: </h4>
        <div class="six columns border-ram">
          <?php echo $userData->theposts; ?>
        </div>
      </div>
    </div>

  </div>
</div>
  
 
    <div class="seven columns panel left">
      <h1>Public Pictures</h1>
      <div class="pictures">
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