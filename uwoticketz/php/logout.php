<?php

//Destroy all session variables and redirect user to login page.
session_unset();
session_destroy();

header('Location: login.php');
?>