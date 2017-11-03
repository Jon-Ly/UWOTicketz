<?php

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

if(isset($_POST["computerId"]) && isset($_POST["description"])){
	submitTicket($_POST["computerId"], $_POST["description"]);
}

/*
* Inserts a new ticket.
*
* @computerId int
* @description string
*/
function submitTicket($computerId, $description){
	//if(!config("conn")->query("CALL InsertTicket(".$computerId.", ".$description.", 1)")){
	//	echo json_encode(array("errorMessage" => "Inserting the ticket was unsuccessful. Please contact IT."));
	//}

	echo "hello";
}

//////////////////////////////////////
//             Computers            //
//////////////////////////////////////


//////////////////////////////////////
//              Users               //
//////////////////////////////////////


//////////////////////////////////////
//              Login               //
//////////////////////////////////////


//////////////////////////////////////
//          Past Tickets            //
//////////////////////////////////////