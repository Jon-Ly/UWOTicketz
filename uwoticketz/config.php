<?php
/**
 * Used to store website configuration information.
 *
 * @var string
 */
function config($key = '')
{
	
	//If you're on the campus, you need to change server name to the SE server.
	//Username and database are the same.
	//Password is s0688747 (wooo look a password, super secure)
	
	//Server config
	$servername = "localhost";
	$database = "lyj47";
	$username = "lyj47";
	$password = "";

	//View config
    $config = [
        'name' => 'UWO Ticketz',
        'nav_menu' => [
            '' => '',
            'computers' => 'Computers',
            'users' => 'Users',
            'submit' => 'Submit Ticket',
        ],
        'template_path' => 'php',
		'conn' => mysqli_connect($servername, $username, $password, $database)
    ];

	// Check connection

	if (!$config["conn"]) {
		die("Connection failed: " . mysqli_connect_error());
	}

    return isset($config[$key]) ? $config[$key] : null;
}