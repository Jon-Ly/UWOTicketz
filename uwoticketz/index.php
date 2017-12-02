<?php
session_start();
require_once 'functions.php';

if(isset($_SESSION["accessLevel"]) && isset($_SESSION["username"]) && isset($_SESSION["userId"])){
	run();// a function in the functions.php
}else{
	header('location: login.php');
}