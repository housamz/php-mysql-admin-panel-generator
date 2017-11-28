<?php
	include("includes/connect.php");

	$adminemail = mysql_real_escape_string($_POST['email']);
	$adminpass = md5($_POST['password']);
	$auth = 'admin_in';

	$query = mysql_query("SELECT * FROM users WHERE email = '".$adminemail."' AND password = '".$adminpass."'");
	if (mysql_affected_rows() == 0 ){
		header("location:"."index.php");
	} else {
		$row = mysql_fetch_array($query);
		setcookie("adminid",$row["id"]);
		setcookie("adminpass",$adminpass);
		setcookie("auth",$auth);
		header("location:"."home.php");
	}
?>
