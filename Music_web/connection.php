<?php
$dbhost = 'localhost:3308';
$dbuser = 'root';
$dbpass = '';
$conn = mysqli_connect($dbhost, $dbuser, $dbpass);//it opens a connection to a MySQLi Server
if(!$conn) {
	die('Could not connect: ' . mysqli_error($conn));
}
echo "";
echo "<br>";
//selecting database
$db = mysqli_select_db($conn,'music_database');
if(!$db) {
echo 'Select database first';
}
else{
echo '';
}
?>