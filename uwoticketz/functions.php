<?php
require 'config.php';

//////////////////////////////////////
//     Session Timeout Check        //
//////////////////////////////////////

$time = $_SERVER['REQUEST_TIME'];

$timeout_duration = 1200; //20 minute session duration

if(isset($_SESSION["last_activity"]) &&
	($time - $_SESSION["last_activity"]) > $timeout_duration){
	
	header('location: login.php');
}

$_SESSION['last_activity'] = $time;

//////////////////////////////////////
//             General              //
//////////////////////////////////////

function isAdmin(){
	return (isset($_SESSION["accessLevel"]) && strtolower($_SESSION["accessLevel"]) == "it");
}
function isUser(){
	return (isset($_SESSION["accessLevel"]) && strtolower($_SESSION["accessLevel"]) == "user");
}
function isAuditor(){
	return (isset($_SESSION["accessLevel"]) && strtolower($_SESSION["accessLevel"]) == "auditor");
}

/*
* Construct the navMenu and display it.
*/
function navMenu(){
	$navMenu = '';

	if(strtolower($_SESSION["accessLevel"]) == "it"){
		foreach (config('nav_menu') as $uri => $name) {
			if($uri != "submit")
				$navMenu .= "<li class='nav-item marginRight10px bold'><a class='nav-link' href='?page=".$uri."'>".$name."</li></a>";
			else
				$navMenu .= "<li class='nav-item'><a class='nav-link bold' data-toggle='modal' data-target='#submitTicketModal' href='#'>".$name."</li></a>";
		}
	}else if(strtolower($_SESSION["accessLevel"]) == "user"){
		foreach (config('user_nav_menu') as $uri => $name) {
			if($uri != "submit")
				$navMenu .= "<li class='nav-item marginRight10px bold'><a class='nav-link' href='?page=".$uri."'>".$name."</li></a>";
			else
				$navMenu .= "<li class='nav-item'><a class='nav-link bold' data-toggle='modal' data-target='#submitTicketModal' href='#'>".$name."</li></a>";
		}
	}else if(strtolower($_SESSION["accessLevel"]) == "auditor"){
		foreach (config('auditor_nav_menu') as $uri => $name) {
			if($uri != "submit")
				$navMenu .= "<li class='nav-item marginRight10px bold'><a class='nav-link' href='?page=".$uri."'>".$name."</li></a>";
			else
				$navMenu .= "<li class='nav-item'><a class='nav-link bold' data-toggle='modal' data-target='#submitTicketModal' href='#'>".$name."</li></a>";
		}
	}else{
		header('location: login.php');
	}

	echo $navMenu;
}

/*
* Get the website icon and display it.
*/
function iconImg(){

	$link = "";

	if(isAdmin() || isAuditor())
		$link = "?page=tickets";
	if(isUser())
		$link = "?page=userTickets";

	$homeIconLink = "<a class='navbar-brand' href='$link' id='home'><img src='content/uwoticketz-logo-64x64.png'/></a>";
	echo $homeIconLink;
}

/*
* Fetches the correct page by grabbing the 'page' value from the URL.
* It then constructs the URL and displays
*/
function pageContent(){

	if(!isAdmin() && !isAuditor() && !isUser()){
		header('location: logout.php');
	}

	$page = "";

	if(isAdmin() || isAuditor())
		$page = isset($_GET['page']) ? $_GET['page'] : 'tickets';
	if(isUser()){
		if(isset($_GET['page']) && $_GET['page'] == 'logout')
			$page = 'logout';
		else
			$page = 'userTickets';
	}
    $path = getcwd().'/'.config('template_path').'/'.$page.'.php';

    if (file_exists(filter_var($path, FILTER_SANITIZE_URL))) {
        include $path;
    } else {
        include config('template_path').'/404.php';
    }
}

/*
* Serves the template.php onto the page.
* template.php is the "parent" php.
*/
function run(){
	/*
	*Evaluates the specified file. In this case,
	*display the template.php onto the page.
	*/
	include config('template_path')."/template.php";
}

//////////////////////////////////////
//             Tickets              //
//////////////////////////////////////

/**
* Construct the Tickets table.
*/
function ticketTable(){

	$result = config("conn")->query("CALL GetAllTickets()");

	$statuses = config("conn")->query("CALL GetAllStatuses()");

	$statusData = $statuses->fetchAll();

	$table = "";

	while ($row = $result->fetch()){
		$ticketId = $row["Id"];
		$table .= 
		"<tr>
			<td>$ticketId</td>
			<td>".$row["ComputerId"]."</td>
			<td>".$row["DateSubmitted"]."</td>
			<td>".$row["DateCompleted"]."</td>";

			if(strtolower($row["StatusName"]) == "completed")
				$table.= ("<td><select class='form-control statusSelect' disabled><option>".$row["StatusName"]."</option></select></td>");
			else
				$table.= ("<td><select class='form-control statusSelect'>".buildStatusSelection($row["StatusName"], $statusData)."</select></td>");

		$table .=
			"<td>".$row["Rating"]."</td>
			<td>
				<form id='view_ticket_form_$ticketId' method='GET'>
					<input type='text' name='ticket_id' value='$ticketId'/ hidden aria-hidden='true'>
					<button class='btn btn-info view_ticket_button' type='submit' id='view_ticket_button$ticketId'>View</button
				</form>
			</td>
		</tr>";
	}

	echo $table;
}

/**
* One of the columns is a select, let's create that here.
*/
function buildStatusSelection($statusName, $statuses){
	$selection = "";

	for($x = 0; $x < count($statuses); $x++){
	    $row = $statuses[$x];
		if($row["StatusName"] == $statusName){
			if(strtolower($statusName) == "completed"){
				$selection .= "<option value='".$row["Id"]."' selected>".$row["StatusName"]."</option>";
			}else{
				$selection .= "<option value='".$row["Id"]."' selected>".$row["StatusName"]."</option>";
			}
		}else{
			$selection .= "<option value='".$row["Id"]."' name='".$row["StatusName"]."' >".$row["StatusName"]."</option>";
		}
	}

	return $selection;
}

/**
* This triggers when an admin changes the status of a ticket on the front page.
*/
function updateTicketStatus($ticketNumber, $statusId, $statusName){
	try{		
		if(!config("conn")->query("CALL UpdateTicketStatus($ticketNumber, $statusId, '$statusName')")){
			throw new Exception("Unable to change the status of that ticket.");
		}

		$result = config("conn")->query("CALL GetTimeStampForTicket($ticketNumber)");

		$row = $result->fetch();

		echo json_encode(array("dateCompleted" => $row["DateCompleted"]));
	}catch(Exception $e){
		echo $e;
	}
}

if(isset($_POST["ticketNumber"]) && isset($_POST["statusId"]) && isset($_POST["statusName"])){
	$ticketNumber = $_POST["ticketNumber"];
	$statusId = $_POST["statusId"];
	$statusName = $_POST["statusName"];
	updateTicketStatus($ticketNumber, $statusId, $statusName);
}

//////////////////////////////////////
//          Submit Ticket           //
//////////////////////////////////////

/*
* Inserts a new ticket.
*
* @computerId int
* @description string
*/
function insertTicket($computerId, $description){
	session_start();
	$userId = $_SESSION["userId"];
	try{
		if(!config("conn")->query("CALL InsertTicket($computerId, '$description', $userId)")){
			throw new Exception("The computer number could not be found. Please contact IT.");
		}
		echo json_encode(array());
	}catch(Exception $e){
		echo $e;
	}
}

if(isset($_POST["computerId"]) && isset($_POST["description"])){
	$computerId = $_POST["computerId"];
	$description = $_POST["description"];
	insertTicket($computerId, $description);
}

//////////////////////////////////////
//             Computers            //
//////////////////////////////////////

function computerTable(){
	$result = config("conn")->query("CALL GetAllComputers()");

	$table = "";

	while ($row = $result->fetch()){
		$table .= 
		"<tr>
			<td>".$row["Id"]."</td>
			<td>".$row["LocationName"]."</td>
		</tr>";
	}

	echo $table;
}

function getLocationList(){
	$result = config("conn")->query("CALL GetAllLocations()");

	$selection = "<option value='-1'></option>";

	while ($row = $result->fetch()){
		
		$value = $row["Id"];

		$selection .= 
		"<option value='$value'>"
			.$row["LocationName"].
		"</option>";
	}

	echo $selection;
}

function insertComputer($computerId, $location){
	try{
		if(!config("conn")->query("CALL InsertComputer($computerId, $location)")){
			throw new Exception("The computer could not be entered.");
		}
		echo json_encode(array());
	}catch(Exception $e){
		echo $e;
	}
}

if(isset($_POST["computerNumber"]) && isset($_POST["location"])){
	$computerId = $_POST["computerNumber"];
	$location = $_POST["location"];
	insertComputer($computerId, $location);
}

/*
* Gets a computer by computerId
*
* @computerId int
* @description string
*/
function getComputerById($computerId){
	
	$result = config("conn")->query("CALL GetComputerById($computerId)");

	$row = $result->fetch();

	echo json_encode($row);
}

if(isset($_GET["computerId"])){
	$computerId = $_GET["computerId"];
	getComputerById($computerId);
}
//////////////////////////////////////
//              Users               //
//////////////////////////////////////

function userTable(){
	$result = config("conn")->query("CALL GetAllUsers()");

	$table = "";

	while ($row = $result->fetch()){
		$table .= 
		"<tr>
			<td>".$row["FirstName"]."</td>
			<td>".$row["LastName"]."</td>
			<td>".$row["Username"]."</td>
			<td>".$row["AccessLevel"]."</td>
		</tr>";
	}

	echo $table;
}

function insertUser($firstName, $lastName, $username, $accessLevel){
	try{
		if(!config("conn")->query("CALL InsertUser('$firstName', '$lastName', '$username', $accessLevel)")){
			throw new Exception("The user could not be entered.");
		}
		echo json_encode(array());
	}catch(Exception $e){
		echo $e;
	}
}

if(isset($_POST["firstName"]) && isset($_POST["lastName"]) && isset($_POST["username"]) && isset($_POST["accessLevel"])){
	$firstName = $_POST["firstName"];
	$lastName = $_POST["lastName"];
	$username = $_POST["username"];
	$accessLevel = $_POST["accessLevel"];
	insertUser($firstName, $lastName, $username, $accessLevel);
}

function accessLevelList(){
	$result = config("conn")->query("CALL GetAllAccessLevels()");

	$selection = "<option value='-1'></option>";

	while ($row = $result->fetch()){
		
		$value = $row["Id"];

		$selection .= 
		"<option value='$value'>"
			.$row["AccessLevel"].
		"</option>";
	}

	echo $selection;
}

//////////////////////////////////////
//              Login               //
//////////////////////////////////////

function generate_salt() {
   $iv = mcrypt_create_iv(22,MCRYPT_DEV_URANDOM);
   $encoded_iv = base64_encode($iv);
   $encoded_iv = str_replace('+','.',$encoded_iv);
   $salt = '$2y$10$' . $encoded_iv . '$';
   return $salt;
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

function change_pwd($user, $pwd) {
   global $db;
   $s = generate_salt();
   $i = crypt($pwd, $s);
   try{
      $query = "UPDATE user SET Password='$i'  WHERE Username='$user'";
      $stmt = $db->prepare($query);
      $stmt->execute();
      $query = "UPDATE user SET Salt='$s'  WHERE Username='$user'";
      $stmt = $db->prepare($query);
      $stmt->execute();
      return true;
   }catch (PDOException $e) {
      db_disconnect();
      exit("Aborting: There was a database error when changing password");
   }
}

//////////////////////////////////////
//          User Tickets            //
//////////////////////////////////////

function userTicketTable(){
	$userId = $_SESSION["userId"];
	$result = config("conn")->query("CALL GetTicketsByUserId('$userId')");

	$table = "";

	$ticket_ids = array();

	while ($row = $result->fetch()){
		$ticketId = $row["TicketId"];
		array_push($ticket_ids, $ticketId);
		$table .= 
		"<tr id='ticket-$ticketId'>
			<td>".$ticketId."</td>
			<td>".$row["ComputerId"]."</td>
			<td>".$row["DateSubmitted"]."</td>
			<td>".$row["LocationName"]."</td>
			<td>
				<form id='view_ticket_form_$ticketId' method='GET'>
					<input type='text' name='ticket_id' value='$ticketId'/ hidden aria-hidden='true'>
					<button class='btn btn-info view_ticket_button' type='submit' id='view_ticket_button$ticketId'>View</button
				</form>
			</td>
		</tr>";
	}

	$_SESSION["ticket_ids"] = $ticket_ids;

	echo $table;
}

function insertComment($ticketNumber, $comment){
	session_start();
	//pull ticket data, check if data matches the user's userId, if not - redirect to 404.php
	$userId = $_SESSION["userId"];

	try{
		if(!config("conn")->query("CALL InsertComment($userId, $ticketNumber, '$comment')")){
			throw new Exception("Unable to insert the new comment.");
		}

		$username = config("conn")->query("CALL GetUsernameById($userId)");

		$datetime = config("conn")->query("SELECT CURRENT_TIMESTAMP");

		$datetime = $datetime->fetch();
		$ticketInfo = $username->fetch();

		array_push($ticketInfo, $datetime["CURRENT_TIMESTAMP"]);

		echo json_encode($ticketInfo);
	}catch(Exception $e){
		echo $e;
	}
}

if(isset($_POST["ticket_id"]) && isset($_POST["comment"])){
	$comment = $_POST["comment"];
	$ticket_id = $_POST["ticket_id"];
	insertComment($ticket_id, $comment);
}

function getTicketInformation($ticket_id){

	$results = config("conn")->query("CALL GetTicketInformationById($ticket_id)");

	$data = array();

	while($row = $results->fetch()){
		array_push($data, $row);
	}

	echo json_encode($data);
}


if(isset($_GET["ticket_id"])){
	$ticket_id = $_GET["ticket_id"];
	getTicketInformation($ticket_id);
}