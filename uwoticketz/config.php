<?php
/**
 * Used to store website configuration information.
 *
 * @var string
 */
function config($key = '')
{
	
	//If you're on the campus, you need to change server name to the SE server --> softeng.cs.uwosh.edu.
	//Username and database are the same.
	//Password is s0688747 (wooo look a password, super secure)
	
	//Server config
	$host = "localhost";
	$db = "lyj47";
	$username = "root";
	$password = "";
	$charset = 'utf8mb4';

	$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
	$opt = [
		PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
		PDO::ATTR_EMULATE_PREPARES   => false,
	];
	//View config
    $config = [
        'name' => 'UWO Ticketz',
        'it_nav_menu' => [
            '' => '',
            'computers' => 'Computers',
            'users' => 'Users',
			'userTickets' => 'Personal Tickets',
            'submit' => 'Submit Ticket',
			'logout' => 'Logout'
        ],
		'auditor_nav_menu' => [
			'' => '',
			'userTickets' => 'Personal Tickets',
            'submit' => 'Submit Ticket',
			'report' => 'Report',
			'logout' => 'Logout'
		],
		'user_nav_menu' =>[
			'' => '',
			'submit' => 'Submit Ticket',
			'logout' => 'Logout'
		],
        'template_path' => 'php',
		'conn' => new PDO($dsn, $username, $password, $opt)
    ];

    return isset($config[$key]) ? $config[$key] : null;
}