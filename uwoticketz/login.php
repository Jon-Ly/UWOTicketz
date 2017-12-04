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
		<form class="form_default" action="login.php" method="POST">
			<h2 class="form-signin-heading">UWO Ticketz</h2>
			<input type="text" class="form-control letterNumeric" name="username" placeholder="Email Address" maxlength="8" required autofocus/>
			<input type="password" class="form-control" name="password" placeholder="Password" required/>
			<hr/>
			<button class="btn btn-lg btn-primary btn-block" type="submit">Login</button>
		</form>
	</div>
</div>

<?php
	require "config.php";

	session_start();

	if(isset($_SESSION["accessLevel"]) && isset($_SESSION["username"])){
		header('Location: index.php');
	}

	if(isset($_POST["username"]) && isset($_POST["password"])){
	
		$user = $_POST["username"];
		$pass = $_POST["password"];

		$isValidLogin = verify_pwd($user, $pass);

		if($isValidLogin){
			echo "<p class='redText'>Incorrect Login</p>";
		}else{
			$result = config("conn")->query("CALL GetAccessLevelByUser('$user')")->fetch();
			$_SESSION["accessLevel"] = $result["AccessLevel"];
			$result = config("conn")->query("CALL GetUserId('$user')")->fetch();
			$_SESSION["userId"] = $result["Id"];
			$_SESSION["username"] = $user;
			header('Location: index.php');
		}
	}

	function get_salt($user) {
		global $db;
		try {
			$query = "SELECT Salt FROM user WHERE Username='$user'";
			$stmt = $db->prepare($query);
			$stmt->execute();
			$s = $stmt->fetch(PDO::FETCH_ASSOC);
			return $s['Salt'];
		} catch (PDOException $e) {
			db_disconnect();
			exit("Aborting: There was a database error");
		}
	}

	function verify_pwd($user, $password) {
	    global $db;
		$ret = false;
		$h = crypt($password, get_salt($user));
		try {
			$query = "SELECT Password FROM user WHERE Username='$user'";
			$stmt = $db->prepare($query);
			$stmt->execute();
			$hash = $stmt->fetch(PDO::FETCH_ASSOC);
			$ret = $h === $hash['Password'] ? true : false;
		} catch (PDOException $e) {
			 db_disconnect();
			 exit("Aborting: There was a database error when " .
			 "verifying password");
		}
		return $ret;
	}
}
?>

</body>
</html>