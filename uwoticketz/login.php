<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>UWO Ticketz</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous">
	<link rel="stylesheet" href="styles/stylesheet.css">
	<script
		src="https://code.jquery.com/jquery-3.2.1.min.js"
		integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4="
		crossorigin="anonymous">
	</script>
	<script 
		src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" 
		integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4" 
		crossorigin="anonymous">
	</script>
	<script 
		src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js" 
		integrity="sha384-h0AbiXch4ZDo7tp9hKZ4TsHbi047NrKGLO3SEJAg45jXxnGIfYzk4Si90RDIqNm1" 
		crossorigin="anonymous">
	</script>
	<script src="scripts/index.js"></script>
	<script src="scripts/login.js"></script>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light" id="header">
  <a class='navbar-brand' href='login.php' id='home'><img src='content/uwoticketz-logo-64x64.png'/></a>
</nav>

<div id="mainContent">
	<div class="wrapper">
		<form class="formDefault" action="login.php" method="POST">
			<h2 class="form-signin-heading">UWO Ticketz</h2>
				<!-- PHP -->
				<?php
					require "config.php";

					session_start();

					//sessions are already set, user tried to press back. Redirect.
					if(isset($_SESSION["access_level"]) && isset($_SESSION["username"])){
						header('Location: index.php');
					}

					if(isset($_POST["username"]) && isset($_POST["password"])){
	
						$user = $_POST["username"];
						$pass = $_POST["password"];
						
						$result = config("conn")->query("CALL GetUser('$user')")->fetch();

						//first login, set password
						if($result["Id"] != null && $result["Password"] == null){
							$_SESSION["username"] = $user;
							$result = config("conn")->query("CALL GetUser('$user')")->fetch();
							$_SESSION["user_id"] = $result["Id"];
							header('Location: firstLogin.php');
						}else{
							$securedPass = config("conn")->query("CALL GetPassword('$user')");
							$securedPass = $securedPass->fetch();
							$isValid = password_verify($pass, $securedPass["Password"]);

							if(!$isValid){
								echo "<p class='redText'>Incorrect Login</p>";
							}else{
								$result = config("conn")->query("CALL GetAccessLevelByUser('$user')")->fetch();
								$_SESSION["access_level"] = $result["AccessLevel"];
								$result = config("conn")->query("CALL GetUser('$user')")->fetch();
								$_SESSION["user_id"] = $result["Id"];
								$_SESSION["username"] = $user;
								header('Location: index.php');
							}
						}
					}
			?>
			<!-- PHP -->
			<input type="text" class="form-control letterNumeric" name="username" placeholder="Username" maxlength="8" required autofocus/>
			<input type="password" class="form-control" name="password" placeholder="Password"/>
			<hr/>
			<button class="btn btn-lg btn-primary btn-block" type="submit">Login</button>
		</form>
	</div>
</div>

</body>
</html>