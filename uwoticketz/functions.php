<?php
require 'config.php';

//////////////////////////////////////
//             General              //
//////////////////////////////////////

/*
* Construct the navMenu and display.
*/
function navMenu(){
	$navMenu = '';

    foreach (config('nav_menu') as $uri => $name) {
		if($uri != "submit")
			$navMenu .= "<li class='nav-item marginRight10px bold'><a class='nav-link' href='?page=".$uri."'>".$name."</li></a>";
		else
			$navMenu .= "<li class='nav-item'><a class='nav-link bold' data-toggle='modal' data-target='#submitTicketModal' href='#'>".$name."</li></a>";
    }

	echo $navMenu;
}

/*
* Get the website icon and display it.
*/
function iconImg(){
	$homeIconLink = "<a class='navbar-brand' href='?page=tickets' id='home'><img src='content/uwoticketz-logo-64x64.png'/></a>";
	echo $homeIconLink;
}

/*
* Fetches the correct page by grabbing the 'page' value from the URL.
* It then constructs the URL and displays
*/
function pageContent(){

	$page = isset($_GET['page']) ? $_GET['page'] : 'tickets';

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
		$table .= 
		"<tr>
			<td>".$row["Id"]."</td>
			<td>".$row["ComputerId"]."</td>
			<td>".$row["DateSubmitted"]."</td>
			<td>".$row["DateCompleted"]."</td>";

			if(strtolower($row["StatusName"]) == "completed")
				$table.= ("<td><select class='form-control statusSelect' disabled><option>".$row["StatusName"]."</option></select></td>");
			else
				$table.= ("<td><select class='form-control statusSelect'>".buildStatusSelection($row["StatusName"], $statusData)."</select></td>");

		$table .=
			"<td>".$row["Rating"]."</td>
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
	try{
		if(!config("conn")->query("CALL InsertTicket($computerId, '$description', 1)")){
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


//////////////////////////////////////
//          User Tickets            //
//////////////////////////////////////

function userTicketTable(){
	$userId = '';//this will be the session

	$result = config("conn")->query("CALL GetTicketsByUserId()");

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