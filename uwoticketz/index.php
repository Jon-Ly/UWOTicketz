<?php
session_start();
require_once 'functions.php';

if(isset($_SESSION["access_level"]) && isset($_SESSION["username"]) && isset($_SESSION["user_id"])){
	run();// a function in the functions.php
}else{
	header('location: login.php');
}