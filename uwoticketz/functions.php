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
		foreach (config('it_nav_menu') as $uri => $name) {
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
	$_SESSION["page"] = $page;
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
* Display the Tickets table.
*/
function ticketTable(){
	echo constructTicketTable();
}

/**
* Set up like this so other functions can call this and get an update table.
*/
function constructTicketTable(){
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

			if(isAdmin()){
				if(strtolower($row["StatusName"]) == "completed" || strtolower($row["StatusName"]) == "ignored")
					$table.= ("<td><select class='form-control statusSelect' disabled><option>".$row["StatusName"]."</option></select></td>");
				else if($row["UserAssignedId"] != null && $_SESSION['userId'] != $row["UserAssignedId"]){
					$table.= ("<td><select class='form-control statusSelect' disabled><option>".$row["StatusName"]."</option></select></td>");
				}
				else
					$table.= ("<td><select class='form-control statusSelect'>".buildStatusSelection($row["StatusName"], $statusData)."</select></td>");
			}else if(isAuditor()){
				$table.= ("<td><select class='form-control statusSelect' disabled><option>".$row["StatusName"]."</option></select></td>");
			}
			if($row["Rating"] == 1){
				$table .= 
				"<td>
					<button class='btn btn-primary disabled' disabled>👍</button>
				</td>";
			}
			else if($row["Rating"] == 2){
				$table .= 
				"<td>
					<button class='btn btn-danger disabled' disabled>👎</button>
				</td>";
			}
			else{
				$table .= "<td></td>";
			}

		$table .=
			"<td>
				<form id='view_ticket_form_$ticketId' method='GET'>
					<input type='text' name='ticket_id' value='$ticketId' hidden aria-hidden='true'/>
					<button class='btn btn-info view_ticket_button' type='submit' id='view_ticket_button_$ticketId'>View</button>
				</form>
			</td>
		</tr>";
	}

	return $table;
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
	session_start();
	try{		
		if(!config("conn")->query("CALL UpdateTicketStatus($ticketNumber, $statusId, '$statusName', ".$_SESSION["userId"].")")){
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

		$arr = array();

		if($_SESSION["page"] == "userTickets")
			array_push($arr, constructUserTicketTable());
		else if($_SESSION["page"] == "tickets")
			array_push($arr, constructTicketTable());

		echo json_encode($arr);
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
	echo constructComputerTable();
}

function constructComputerTable(){
	$result = config("conn")->query("CALL GetAllComputers()");

	$table = "";

	while ($row = $result->fetch()){
		$id = $row["Id"];
		$location = $row["LocationName"];
		$table .= 
		"<tr>
			<td>".$id."</td>
			<td>".$location."</td>";

		$table .=
			"<td>
				<form id='edit_computer_form_$id' method='POST'>
					<input type='text' name='computer_id' value='$id' hidden aria-hidden='true'/>
					<button class='btn btn-info edit_computer_button' type='submit' id='edit_computer_button_$id'>Edit</button>
				</form>
			</td>";

		$table .=
			"<td>
				<form id='delete_computer_form_$id' method='POST'>
					<input type='text' name='computer_id' value='$id' hidden aria-hidden='true'/>
					<button class='btn btn-info remove_computer_button' type='submit' id='delete_computer_button_$id'>Delete</button>
				</form>
			</td>
		</tr>";
	}

	return $table;
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

function updateComputer($computer_id, $location, $previous_computer_id){
	
	try{
		if(!config("conn")->query("CALL UpdateComputer($computer_id, $location, $previous_computer_id)")){
			throw new Exception("Unable to update computer information.");
		}

		$arr = array();

		array_push($arr, constructComputerTable());

		echo json_encode($arr);
	}
	catch(Exception $e){
		echo $e;
	}
}

if(isset($_POST["computer_id"]) && isset($_POST["location"]) && isset($_POST["previous_computer_id"])){
	$computer_id = addslashes($_POST["computer_id"]);
	$location = addslashes($_POST["location"]);
	$previous_computer_id = addslashes($_POST["previous_computer_id"]);
	updateComputer($computer_id, $location, $previous_computer_id); 
}
else if(isset($_POST["computer_id"])){
	$computer_id = addslashes($_POST["computer_id"]);
	deleteComputer($computer_id);
}

function deleteComputer($computer_id){
	try{
		if(!config("conn")->query("CALL DeleteComputer($computer_id)")){
			throw new Exception("Unable to update computer information.");
		}
		$arr = array();

		array_push($arr, constructComputerTable());

		echo json_encode($arr);
	}
	catch(Exception $e){
		echo $e;
	}
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
	echo constructUserTable();
}

function constructUserTable(){
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

	return $table;
}

function insertUser($firstName, $lastName, $username, $accessLevel){

	try{
		if(!config("conn")->query("CALL InsertUser('$firstName', '$lastName', '$username', $accessLevel)")){
			throw new Exception("The user could not be entered.");
		}
		
		$arr = array();
		array_push($arr, constructUserTable());

		echo json_encode($arr);
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
//          User Tickets            //
//////////////////////////////////////

function userTicketTable(){
	echo constructUserTicketTable();
}

function constructUserTicketTable(){
	$userId = $_SESSION["userId"];
	$result = config("conn")->query("CALL GetTicketsByUserId('$userId')");

	$table = "";

	while ($row = $result->fetch()){
		$ticketId = $row["TicketId"];
		$table .= 
		"<tr id='ticket-$ticketId'>
			<td>".$ticketId."</td>
			<td>".$row["ComputerId"]."</td>
			<td>".$row["DateSubmitted"]."</td>
			<td>".$row["LocationName"]."</td>";
		
		if($row["Rating"] == 1){
			$table .=
			"<td>
				<button class='btn btn-primary disabled' disabled>👍</button>
			</td>";
		}
		else if($row["Rating"] == 2){
			$table .=
			"<td>
				<button class='btn btn-danger disabled' disabled>👎</button>
			</td>";
		}
		else if($row["Status"] > 2){
			$table .=
			"<td>
				<button class='good_rate_button btn btn-primary marginRight10px'>👍</button>
				<button class='bad_rate_button btn btn-danger'>👎</button>
			</td>";
		}else{
			$table .=
			"<td></td>";
		}
		$table .=
			"<td>
				<form id='view_ticket_form_$ticketId' method='GET'>
					<input type='text' name='ticket_id' value='$ticketId'/ hidden aria-hidden='true'>
					<button class='btn btn-info view_ticket_button' type='submit' id='view_ticket_button_$ticketId'>View</button
				</form>
			</td>
		</tr>";
	}

	return $table;
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

function insertRating($ticket_id, $rating){
	session_start();
	try{
		if(!config("conn")->query("CALL InsertRating($ticket_id, $rating)")){
			throw new Exception("Unable to insert rating.");
		}

		$arr = array();
		array_push($arr, constructUserTicketTable());

		echo json_encode($arr);

	}catch(Exception $e){
		echo $e;
	}
}

if(isset($_POST["ticket_id"]) && isset($_POST["comment"])){
	$comment = $_POST["comment"];
	$ticket_id = $_POST["ticket_id"];
	insertComment($ticket_id, $comment);
}
else if(isset($_POST["ticket_id"]) && isset($_POST["rating"])){
	$ticket_id = addslashes($_POST["ticket_id"]);
	$rating = addslashes($_POST["rating"]);
	insertRating($ticket_id, $rating);
}
else if(isset($_GET["ticket_id"])){
	$ticket_id = $_GET["ticket_id"];
	getTicketInformation($ticket_id);
}

function getTicketInformation($ticket_id){
	$results = config("conn")->query("CALL GetTicketInformationById($ticket_id)");
	$user = config("conn")->query("CALL GetUserByTicket($ticket_id)")->fetch();

	$data = array();

	array_push($data, $user);

	while($row = $results->fetch()){
		array_push($data, $row);
	}

	echo json_encode($data);
}

//////////////////////////////////////
//             Reports              //
//////////////////////////////////////

function generateReport(){
	$result = config("conn")->query("CALL GetReportData()");

	$table = "";

	while($row = $result->fetch()){

		$table .=
		"<tr>
			<td>".$row["TicketId"]."</td>
			<td>".$row["ComputerId"]."</td>
			<td>".$row["Username"]."</td>
		";

	}
}