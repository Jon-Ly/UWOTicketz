<?php
require 'config.php';

//////////////////////////////////////
//     Session Timeout Check        //
//////////////////////////////////////

$time = $_SERVER['REQUEST_TIME'];

$timeout_duration = 1200; //20 minute session duration

if(isset($_SESSION["last_activity"]) &&
	($time - $_SESSION["last_activity"]) > $timeout_duration){
	
	session_unset();
	session_destroy();

	header('Location: login.php');
}

//reset timeout to 20 mins if user did an action
$_SESSION['last_activity'] = $time;

//////////////////////////////////////
//             General              //
//////////////////////////////////////

/**
* The different permission levels
*/
function isAdmin(){
	return (isset($_SESSION["access_level"]) && strtolower($_SESSION["access_level"]) == "it");
}
function isUser(){
	return (isset($_SESSION["access_level"]) && strtolower($_SESSION["access_level"]) == "user");
}
function isAuditor(){
	return (isset($_SESSION["access_level"]) && strtolower($_SESSION["access_level"]) == "auditor");
}

/*
* Construct the navMenu and display it.
*/
function navMenu(){
	$navMenu = '';

	if(strtolower($_SESSION["access_level"]) == "it"){
		foreach (config('it_nav_menu') as $uri => $name) {
			if($uri != "submit")
				$navMenu .= "<li class='nav-item marginRight10px bold'><a class='nav-link' href='?page=".$uri."'>".$name."</li></a>";
			else
				$navMenu .= "<li class='nav-item'><a class='nav-link bold' data-toggle='modal' data-target='#submitTicketModal' href='#'>".$name."</li></a>";
		}
	}else if(strtolower($_SESSION["access_level"]) == "user"){
		foreach (config('user_nav_menu') as $uri => $name) {
			if($uri != "submit")
				$navMenu .= "<li class='nav-item marginRight10px bold'><a class='nav-link' href='?page=".$uri."'>".$name."</li></a>";
			else
				$navMenu .= "<li class='nav-item'><a class='nav-link bold' data-toggle='modal' data-target='#submitTicketModal' href='#'>".$name."</li></a>";
		}
	}else if(strtolower($_SESSION["access_level"]) == "auditor"){
		foreach (config('auditor_nav_menu') as $uri => $name) {
			if($uri != "submit")
				$navMenu .= "<li class='nav-item marginRight10px bold'><a class='nav-link' href='?page=".$uri."'>".$name."</li></a>";
			else
				$navMenu .= "<li class='nav-item'><a class='nav-link bold' data-toggle='modal' data-target='#submitTicketModal' href='#'>".$name."</li></a>";
		}
	}else{ //not logged in
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
* Construct the ticket table html
*/
function constructTicketTable(){
	$result = config("conn")->query("CALL GetAllTickets()");

	$statuses = config("conn")->query("CALL GetAllStatuses()");

	$statusData = $statuses->fetchAll();

	$table = "";

	while ($row = $result->fetch()){
		$ticket_id = $row["Id"];
		$table .= 
		"<tr>
			<td>$ticket_id</td>
			<td>".$row["ComputerId"]."</td>
			<td>".$row["DateSubmitted"]."</td>
			<td>".$row["DateCompleted"]."</td>";

			if(isAdmin()){
				if(strtolower($row["StatusName"]) == "completed" || strtolower($row["StatusName"]) == "ignored")
					$table.= ("<td><select class='form-control statusSelect' disabled><option>".$row["StatusName"]."</option></select></td>");
				else if($row["UserAssignedId"] != null && $_SESSION['user_id'] != $row["UserAssignedId"]){
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
				<form id='view_ticket_form_$ticket_id' method='GET'>
					<input type='text' name='ticket_id' value='$ticket_id' hidden aria-hidden='true'/>
					<button class='btn btn-info view_ticket_button' type='submit' id='view_ticket_button_$ticket_id'>View</button>
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
function updateTicketStatus($ticket_number, $statusId, $statusName){
	session_start();
	try{		
		if(!config("conn")->query("CALL UpdateTicketStatus($ticket_number, $statusId, '$statusName', ".$_SESSION["user_id"].")")){
			throw new Exception("Unable to change the status of that ticket.");
		}

		$result = config("conn")->query("CALL GetTimeStampForTicket($ticket_number)");

		$row = $result->fetch();

		echo json_encode(array("dateCompleted" => $row["DateCompleted"]));
	}catch(Exception $e){
		echo $e;
	}
}

//Check if the user is updating a ticket.
if(isset($_POST["ticket_number"]) && isset($_POST["statusId"]) && isset($_POST["statusName"])){
	$ticket_number = $_POST["ticket_number"];
	$statusId = $_POST["statusId"];
	$statusName = $_POST["statusName"];
	updateTicketStatus($ticket_number, $statusId, $statusName);
}

//////////////////////////////////////
//          Submit Ticket           //
//////////////////////////////////////

/*
* Inserts a new ticket.
*
* @computer_id int
* @description string
*/
function insertTicket($computer_id, $description){
	session_start();
	$user_id = $_SESSION["user_id"];
	try{
		if(!config("conn")->query("CALL InsertTicket($computer_id, '$description', $user_id)")){
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

//check if a user is inserting a ticket
if(isset($_POST["computer_id"]) && isset($_POST["description"])){
	$computer_id = $_POST["computer_id"];
	$description = $_POST["description"];
	insertTicket($computer_id, $description);
}

//////////////////////////////////////
//             Computers            //
//////////////////////////////////////

/*
* Display the computer table
*/
function computerTable(){
	echo constructComputerTable();
}

/*
* Construct the computer table html.
*/
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

/*
* Returns a list of locations
*/
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

/*
* Inserts a computer.
*
* @computer_id int
* @location int
*/
function insertComputer($computer_id, $location){
	try{
		if(!config("conn")->query("CALL InsertComputer($computer_id, $location)")){
			throw new Exception("The computer could not be entered.");
		}
		echo json_encode(array());
	}catch(Exception $e){
		echo $e;
	}
}

//checks if the user is inserting a computer
if(isset($_POST["computer_number"]) && isset($_POST["location"])){
	$computer_id = $_POST["computer_number"];
	$location = $_POST["location"];
	insertComputer($computer_id, $location);
}

/*
* Updates a computer's data.
*
* @computer_id int
* @location int
* @previous_computer_id int
*/
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

//check if the user is updating computer data
if(isset($_POST["computer_id"]) && isset($_POST["location"]) && isset($_POST["previous_computer_id"])){
	$computer_id = addslashes($_POST["computer_id"]);
	$location = addslashes($_POST["location"]);
	$previous_computer_id = addslashes($_POST["previous_computer_id"]);
	updateComputer($computer_id, $location, $previous_computer_id); 
}
//check if the user is deleting a computer.
else if(isset($_POST["computer_id"])){
	$computer_id = addslashes($_POST["computer_id"]);
	deleteComputer($computer_id);
}

/*
* Delets a computer by its number.
*
* @computer_id int
*/
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
* Gets a computer by computer_id
*
* @computer_id int
* @description string
*/
function getComputerById($computer_id){
	
	$result = config("conn")->query("CALL GetComputerById($computer_id)");

	$row = $result->fetch();

	echo json_encode($row);
}

//check if a user is computer data
if(isset($_GET["computer_id"])){
	$computer_id = $_GET["computer_id"];
	getComputerById($computer_id);
}
//////////////////////////////////////
//              Users               //
//////////////////////////////////////

/*
* displays the users table.
*/
function userTable(){
	echo constructUserTable();
}

/*
* constructs the user table html.
*/
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

/*
* Inserts a new user.
*
* @first_name string
* @last_name string
* @username string
* @access_level int
*/
function insertUser($first_name, $last_name, $username, $access_level){

	try{
		if(!config("conn")->query("CALL InsertUser('$first_name', '$last_name', '$username', $access_level)")){
			throw new Exception("The user could not be entered.");
		}
		
		$arr = array();
		array_push($arr, constructUserTable());

		echo json_encode($arr);
	}catch(Exception $e){
		echo $e;
	}
}

//check if a user wants to insert a user.
if(isset($_POST["first_name"]) && isset($_POST["last_name"]) && isset($_POST["username"]) && isset($_POST["access_level"])){
	$first_name = $_POST["first_name"];
	$last_name = $_POST["last_name"];
	$username = $_POST["username"];
	$access_level = $_POST["access_level"];
	insertUser($first_name, $last_name, $username, $access_level);
}

/*
* Returns a list of the access levels
*/
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

/*
* displays user ticket table
*/
function userTicketTable(){
	echo constructUserTicketTable();
}

/*
* constructs the user ticket table html
*/
function constructUserTicketTable(){
	$user_id = $_SESSION["user_id"];
	$result = config("conn")->query("CALL GetTicketsByUserId('$user_id')");

	$table = "";

	while ($row = $result->fetch()){
		$ticket_id = $row["TicketId"];
		$table .= 
		"<tr id='ticket-$ticket_id'>
			<td>".$ticket_id."</td>
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
				<form id='view_ticket_form_$ticket_id' method='GET'>
					<input type='text' name='ticket_id' value='$ticket_id'/ hidden aria-hidden='true'>
					<button class='btn btn-info view_ticket_button' type='submit' id='view_ticket_button_$ticket_id'>View</button
				</form>
			</td>
		</tr>";
	}

	return $table;
}

/**
* Inserts a comment
*
* @ticket_number int
* @comment string
*/
function insertComment($ticket_number, $comment){
	session_start();
	//pull ticket data, check if data matches the user's user_id, if not - redirect to 404.php
	$user_id = $_SESSION["user_id"];

	try{
		if(!config("conn")->query("CALL InsertComment($user_id, $ticket_number, '$comment')")){
			throw new Exception("Unable to insert the new comment.");
		}

		$username = config("conn")->query("CALL GetUsernameById($user_id)");

		$datetime = config("conn")->query("SELECT CURRENT_TIMESTAMP");

		$datetime = $datetime->fetch();
		$ticket_info = $username->fetch();

		array_push($ticket_info, $datetime["CURRENT_TIMESTAMP"]);

		echo json_encode($ticket_info);
	}catch(Exception $e){
		echo $e;
	}
}

/*
* Inserts a ticket rating.
*
* @ticket_id int
* @rating int
*/
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

//check if a user is inserting a new comment.
if(isset($_POST["ticket_id"]) && isset($_POST["comment"])){
	$comment = $_POST["comment"];
	$ticket_id = $_POST["ticket_id"];
	insertComment($ticket_id, $comment);
}
//check if a user is inserting a new rating.
else if(isset($_POST["ticket_id"]) && isset($_POST["rating"])){
	$ticket_id = addslashes($_POST["ticket_id"]);
	$rating = addslashes($_POST["rating"]);
	insertRating($ticket_id, $rating);
}
//check if a user is getting ticket data
else if(isset($_GET["ticket_id"])){
	$ticket_id = $_GET["ticket_id"];
	getTicketInformation($ticket_id);
}

/*
* Returns ticket information for a given ticket number.
*
* @ticket_id
*/
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

/*
* Generates a new report.
* --INCOMPLETE--
*/
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