<div class="container">
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
      <form action="/uploadPic" method="post" enctype="multipart/form-data">
    Select image to upload:
    <input type="file" name="fileToUpload" id="fileToUpload">
    <input type="submit" value="Upload Image" name="submit">
</form>
      </p>
  </div>
</div>
<div class="seven columns panel left">
  <div id="thediv">
    <h1>Your Profile!</h1>
    <div class="postTabel">
      <img class="avatar" src="http://www.gravatar.com/avatar/<?php echo $User->gravatar_hash; ?>">
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


<div class="nine columns panel left">
  <h1>What we all are saying.</h1>
  <?php foreach($tposts as $theposts){ ?>
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