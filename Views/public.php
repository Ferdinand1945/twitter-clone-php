<div class="container">
<div class="six columns panel right">
	<h1>Search for posts!</h1>
	<p>
		</p><form action="/public" method="post">
			<input name="query" type="text">
			<input type="submit" value="Search!">
		</form>
	<p></p>
</div> 

<div id="posts" class="ten columns panel left">
	<h1>Public Post</h1>
	<?php foreach($ribbits as $theposts){ ?>
        <div class="postTabel">
            <img class="avatar" src="http://www.gravatar.com/avatar/<?php echo $theposts->gravatar_hash; ?>">
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
  </div>