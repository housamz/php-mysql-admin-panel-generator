<?php
	include("includes/connect.php");

	$admin_email = mysqli_real_escape_string($link, $_POST['email']);
	$admin_pass = md5($_POST['password']);
	$auth = 'admin_in';

	$query = mysqli_query($link, "SELECT * FROM users WHERE email = '".$admin_email."' AND password = '".$admin_pass."'");
	if (mysqli_affected_rows($link) == 0) {
		header("location:"."index.php");
	} else {
		$row = mysqli_fetch_array($query);
		setcookie("admin_id", $row["id"]);
		setcookie("admin_pass", $admin_pass);
		setcookie("auth", $auth);
		header("location:"."home.php");
	}
?>
