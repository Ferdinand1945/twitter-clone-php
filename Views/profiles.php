<div class="container">
<div class="six columns panel right">
	<h1>Search for Profiles</h1>
	<p>
		</p><form action="/profiles" method="post">
			<input name="query" type="text">
			<input type="submit" value="Search!">
		</form>
	<p></p>
</div>
<div id="posts" class="ten columns panel left">
    <h1>Public Profiles</h1>
    <?php foreach($profiles as $user){ ?>
    <div class="postTabel">
                    <img class="avatar" src="/images/Avatar.png"> <!--   ?php echo $user->avatar_hash; ?>"> -->

        <span class="name"><?php echo $user->name; ?></span><br> @<?php echo $user->username; ?> <br>
        <span class="time"><?php echo $user->followers; echo ($user->followers > 1) ? " followers " : " follower "; ?>
            <a href="<?php echo ($user->followed) ? "unfollow" : "follow"; ?>/<?php echo $user->id; ?>"><?php echo ($user->followed) ? "unfollow" : "follow"; ?></a></span>
        <p>
            <?php echo $user->theposts; ?>
        </p>
    </div>
    <?php } ?>
</div>
  </div>