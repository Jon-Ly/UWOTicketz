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
        $navMenu .= "<li class='nav-item'><a class='nav-link' href='?page=".$uri."'></li>".$name."</a>";
    }

	echo $navMenu;
}

/*
* Get the website icon and display it.
*/
function iconImg(){
	$homeIconLink = "<a class='navbar-brand' href='?page=tickets' id='home'><img src='content/uwoticketz.png'/></a>";
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
	*display the php onto the page.
	*/
	include config('template_path')."/template.php";
}

//////////////////////////////////////
//             Tickets              //
//////////////////////////////////////

function ticketTable(){
	$result = config("conn")->query("CALL GetAllTickets()");

	$table = "";

	while ($row = mysqli_fetch_array($result)){
		$table .= 
		"<tr>
			<td>".$row["Id"]."</td>
			<td>".$row["ComputerId"]."</td>
			<td>".$row["DateSubmitted"]."</td>
			<td>".$row["DateCompleted"]."</td>
			<td>".$row["StatusName"]."</td>
			<td>".$row["Rating"]."</td>
		</tr>";
	}

	echo $table;
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
function submitTicket($computerId, $description){
	try{
		if(!config("conn")->query("CALL InsertTicket($computerId, '$description', 1)")){
			throw new Exception("The computer number could not be found. Please contact IT.");
		}
		echo json_encode(array());
	}catch(Exception $e){
		echo $e->getMessage();
	}
}

if(isset($_POST["computerId"]) && isset($_POST["description"])){
	$computerId = $_POST["computerId"];
	$description = $_POST["description"];
	submitTicket($computerId, $description);
}

//////////////////////////////////////
//             Computers            //
//////////////////////////////////////

function computerTable(){
	$result = config("conn")->query("CALL GetAllComputers()");

	$table = "";

	while ($row = mysqli_fetch_array($result)){
		$table .= 
		"<tr>
			<td>".$row["Id"]."</td>
			<td>".$row["LocationId"]."</td>
		</tr>";
	}

	echo $table;
}

/*
* Gets a computer by computerId
*
* @computerId int
* @description string
*/
function getComputerById($computerId){
	
	$result = config("conn")->query("CALL GetComputerById($computerId)");

	$row = mysqli_fetch_array($result);

	echo json_encode($row);
}

if(isset($_GET["computerId"])){
	$computerId = $_GET["computerId"];
	getComputerById($computerId);
}
//////////////////////////////////////
//              Users               //
//////////////////////////////////////


//////////////////////////////////////
//              Login               //
//////////////////////////////////////


//////////////////////////////////////
//          Past Tickets            //
//////////////////////////////////////