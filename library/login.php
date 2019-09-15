<?php
	include("includes/connect.php");

	$adminemail = mysqli_real_escape_string($link, $_POST['email']);
	$adminpass = md5($_POST['password']);
	$auth = 'admin_in';

	$query = mysqli_query($link, "SELECT * FROM users WHERE email = '".$adminemail."' AND password = '".$adminpass."'");
	if (mysqli_affected_rows($link) == 0 ){
		header("location:"."index.php");
	} else {
		$row = mysqli_fetch_array($query);
		setcookie("adminid",$row["id"]);
		setcookie("adminpass",$adminpass);
		setcookie("auth",$auth);
		header("location:"."home.php");
	}
?>
