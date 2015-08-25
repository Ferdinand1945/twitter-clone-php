<!DOCTYPE html>
<html>
<head>
	
  	<link rel="stylesheet/less" href="/style.css">
  <link rel="stylesheet/less" href="/skeleton.css">
	<script src="/less.js"></script>
  <script src="js/jquery.min.js"></script>
    <script src="js/script.js"></script>

</head>
<body>
	<header>

      <div class="container">
        <div class="row">
          <div class="five columns"><div class="logo"><span class="pull-left">Twiiiiitter</span></div></div>
            <div class="eleven columns">
              <div class="pull-down">
			
			<?php if($User !== false){ ?>
              <div class="seven columns">
                <nav>
                    <a href="/buddies">Friends</a>
                    <a href="/public">Public Posts</a>
                    <a href="/profiles">Profiles</a>
                  <a href="/uploadPic">Your pictures</a>
                </nav>
              </div>
                <div class="two columns logout">
                <form action="/logout" method="get">
                    <input type="submit" id="btnLogOut" value="Log Out">
                </form>
                </div>
            <?php }else{ ?>
                <form method="post" class="login" action="/login">
                    <input name="username" type="text" placeholder="username">
                    <input name="password" type="password" placeholder="password">
                   <input type="submit" id="btnLogIn" value="Log In">
                </form>
            
            <?php } ?>
          </div>
        </div>
            </div>
      </div>
	</header>
    <div id="content-1" class="pull-down-xxl">
		<div class="container wrapper">
