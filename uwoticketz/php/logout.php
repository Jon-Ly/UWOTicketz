<?php

session_start();

unset($_SESSION["accessLevel"]);
unset($_SESSION["username"]);

header('Location: login.php');
?>