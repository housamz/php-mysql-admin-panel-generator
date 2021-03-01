<?php
error_reporting(0);
// header to return JSON to the jQuery Ajax request
header('Content-Type: application/json');

// function for removing last colon
function removeLastChar($string) {
	$string = substr($string, 0, -1);
	return $string;
}

// function to create files
function createFile($database, $myFile, $stringData) {
	$path = "generated/".$database.date("Y-m-d_H-i");
	if (!file_exists($path)) {
		mkdir($path, 0777);
	}
	if (file_exists($path."/includes")) {

	} else {
		mkdir($path."/includes", 0777);
	}
	$fh = fopen($path."/".$myFile.".php", 'w') or die("can't open file");
	fwrite($fh, $stringData);
	fclose($fh);
}

// function to return a random glyph icon to be used in the side bar links
function random_glyph_icon() {
	$glyph_icons = array("asterisk", "plus", "euro", "eur", "minus", "cloud", "envelope", "pencil", "glass", "music", "search", "heart", "star", "star-empty", "user", "film", "th-large", "th", "th-list", "ok", "remove", "zoom-in", "zoom-out", "off", "signal", "cog", "file", "time", "road", "download-alt", "download", "upload", "inbox", "play-circle", "repeat", "refresh", "list-alt", "lock", "flag", "headphones", "volume-off", "volume-down", "volume-up", "qrcode", "barcode", "tag", "tags", "book", "bookmark", "print", "camera", "font", "bold", "italic", "text-height", "text-width", "align-left", "align-center", "align-right", "align-justify", "list", "indent-left", "indent-right", "facetime-video", "picture", "map-marker", "adjust", "tint", "share", "check", "move", "step-backward", "fast-backward", "backward", "play", "pause", "stop", "forward", "fast-forward", "step-forward", "eject", "chevron-left", "chevron-right", "plus-sign", "minus-sign", "remove-sign", "ok-sign", "question-sign", "info-sign", "screenshot", "remove-circle", "ok-circle", "ban-circle", "arrow-left", "arrow-right", "arrow-up", "arrow-down", "share-alt", "resize-full", "resize-small", "exclamation-sign", "gift", "leaf", "fire", "eye-open", "eye-close", "warning-sign", "plane", "calendar", "random", "comment", "magnet", "chevron-up", "chevron-down", "retweet", "shopping-cart", "folder-close", "folder-open", "resize-vertical", "resize-horizontal", "hdd", "bullhorn", "bell", "certificate", "thumbs-up", "thumbs-down", "hand-right", "hand-left", "hand-up", "hand-down", "circle-arrow-right", "circle-arrow-left", "circle-arrow-up", "circle-arrow-down", "globe", "wrench", "tasks", "filter", "briefcase", "fullscreen", "dashboard", "paperclip", "heart-empty", "link", "phone", "pushpin", "usd", "gbp", "sort", "sort-by-alphabet", "sort-by-alphabet-alt", "sort-by-order", "sort-by-order-alt", "sort-by-attributes", "sort-by-attributes-alt", "unchecked", "expand", "collapse-down", "collapse-up", "log-in", "flash", "new-window", "record", "save", "open", "saved", "import", "export", "send", "floppy-disk", "floppy-saved", "floppy-remove", "floppy-save", "floppy-open", "credit-card", "transfer", "cutlery", "header", "compressed", "earphone", "phone-alt", "tower", "stats", "sd-video", "hd-video", "subtitles", "sound-stereo", "sound-dolby", "sound-5-1", "sound-6-1", "sound-7-1", "copyright-mark", "registration-mark", "cloud-download", "cloud-upload", "tree-conifer", "tree-deciduous", "cd", "save-file", "open-file", "level-up", "copy", "paste", "alert", "equalizer", "king", "queen", "pawn", "bishop", "knight", "baby-formula", "tent", "blackboard", "bed", "apple", "erase", "hourglass", "lamp", "duplicate", "piggy-bank", "scissors", "bitcoin", "btc", "xbt", "yen", "jpy", "ruble", "rub", "scale", "ice-lolly", "ice-lolly-tasted", "education", "option-horizontal", "option-vertical", "menu-hamburger", "modal-window", "oil", "grain", "sunglasses", "text-size", "text-color", "text-background", "object-align-top", "object-align-bottom", "object-align-horizontal", "object-align-left", "object-align-vertical", "object-align-right", "triangle-right", "triangle-left", "triangle-bottom", "triangle-top", "console", "superscript", "subscript", "menu-left", "menu-right", "menu-down", "menu-up");

	$rand = array_rand($glyph_icons, 1);
	return $glyph_icons[$rand];
}


if($_POST) {

	// getting parameters from the ajax request
	$action 	= $_POST["action"];
	$host 		= $_POST["host"];
	$username 	= $_POST["username"];
	$password 	= $_POST["password"];

	// connecting to the host
	$link = mysqli_connect($host, $username, $password);

	if (!$link) {
		die(json_encode(array('status' => 'error','message'=> 'Could not connect: ' . mysqli_error($link))));
	}

	// if the action is to connect, we request all databases
	if($action == "connect") {
		$result = '';
		$res = mysqli_query($link, "SHOW DATABASES");
		if (!$res) {
			die(json_encode(array('status' => 'error','message'=> 'Listing databases failed: ' . mysqli_error($link))));
		}
		while ($row = mysqli_fetch_assoc($res)) {
			$result .= "<option value=\"" .$row['Database'] . "\">" .$row['Database'] . "</option>";
		}

		if(!$result) {
			echo json_encode(array('status' => 'error','message'=> 'Error in data collection'));
		} else {
			echo json_encode(array('status' => 'success','result'=> $result));
		}

	}

	// process starts if the action is to generate the admin panel
	else if ($action == "generate") {

		// get the database name
		$database = $_POST["database"];

		// gather success info and display to user at the end
		$message = "The operations that were performed are: <ul>";

		// select the database
		$db_link = mysqli_select_db($link, $database);
		if (!$db_link) {
			die(json_encode(array('status' => 'error','message'=> 'Couldn\'t select database: ' . mysqli_error($link))));
		}

		// creating the users table if it doesn't exist
		$sql = "CREATE TABLE IF NOT EXISTS `users` (`id` int(11) NOT NULL AUTO_INCREMENT, `name` varchar(255) NOT NULL, `email` varchar(255) NOT NULL, `password` varchar(255) NOT NULL, `role` int(11) NOT NULL, PRIMARY KEY (`id`) ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2";

		$res = mysqli_query($link, $sql);
		if (!$res) {
			die(json_encode(array('status' => 'error','message'=> 'Couldn\'t create users table: ' . mysqli_error($link))));
		}

		// inserting the entry for admin, password is MD5'ed
		mysqli_query($link, "INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`) VALUES (1, 'Admin', 'admin', '21232f297a57a5a743894a0e4a801fc3', 1)");

		// loop to show all the tables and fields
		$loop = mysqli_query($link, "SHOW tables FROM $database");

		if (!$loop) {
			die(json_encode(array('status' => 'error','message'=> 'Couldn\'t select table: ' . mysqli_error($link))));
		}

		// the generation process starts here
		// collecting DB connection info to generate includes/connect.php file
		$connection = "<?php
		\$link = mysqli_connect(\"$host\", \"$username\", \"$password\");
		mysqli_select_db(\$link, \"$database\");
		mysqli_query(\$link, \"SET CHARACTER SET utf8\");
		?>
		";

		// starting the save.php file which controls create, update, and delete operations on the database.
		$save = "<?php
		include(\"includes/connect.php\");

		$"."cat = $"."_POST['cat'];
		$"."cat_get = $"."_GET['cat'];
		$"."act = $"."_POST['act'];
		$"."act_get = $"."_GET['act'];
		$"."id = $"."_POST['id'];
		$"."id_get = $"."_GET['id'];

		";

		// collecting the home.php page which shows a full database table of contents
		$index = "<?php
		include \"includes/header.php\";
		?>
		<table class=\"table table-striped\">
		<tr>
		<th class=\"not\">Table</th>
		<th class=\"not\">Entries</th>
		</tr>
		";

		// collecting data for the includes/header.php page which is the header of all pages
		$header = '<?php
		error_reporting(0);
		session_start();
		if ($_COOKIE["auth"] != "admin_in") {header("location:"."./");}
			include("includes/connect.php");
			include("includes/data.php");
			?>
			<!DOCTYPE html>
			<html lang="en">
			<head>
				<meta charset="utf-8">
				<meta http-equiv="X-UA-Compatible" content="IE=edge">
				<meta name="viewport" content="width=device-width, initial-scale=1">
				<meta name="author" content="@housamz">

				<meta name="description" content="Mass Admin Panel">
				<title>' .ucfirst($database). ' Admin Panel</title>
				<link href="https://maxcdn.bootstrapcdn.com/bootswatch/3.3.7/cosmo/bootstrap.min.css" rel="stylesheet" integrity="sha384-h21C2fcDk/eFsW9sC9h0dhokq5pDinLNklTKoxIZRUn3+hvmgQSffLLQ4G4l2eEr" crossorigin="anonymous">

				<!-- Custom CSS -->
				<link rel="stylesheet" href="includes/style.css">
				<link href="//cdn.datatables.net/1.10.16/css/dataTables.bootstrap.min.css" rel="stylesheet" type="text/css" />

				<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
				<!-- WARNING: Respond.js doesnt work if you view the page via file:// -->
				<!--[if lt IE 9]>
					<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
					<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
				<![endif]-->
			</head>

			<body>

			<div class="wrapper">
				<!-- Sidebar Holder -->
				<nav id="sidebar" class="bg-primary">
					<div class="sidebar-header">
						<h3>
							' .ucfirst($database). ' Admin Panel<br>
							<i id="sidebarCollapse" class="glyphicon glyphicon-circle-arrow-left"></i>
						</h3>
						<strong>
							' .ucfirst($database). '<br>
							<i id="sidebarExtend" class="glyphicon glyphicon-circle-arrow-right"></i>
						</strong>
					</div><!-- /sidebar-header -->

					<!-- start sidebar -->
					<ul class="list-unstyled components">
						<li>
							<a href="home.php" aria-expanded="false">
								<i class="glyphicon glyphicon-home"></i>
								Home
							</a>
						</li>
			';

			// looping all the database tables
			while($table = mysqli_fetch_array($loop)) {

				// having a name for the table in two cases, all small caps and capitalised
				$table_name = $table[0];
				$capital = ucfirst($table_name);
				$small = strtolower($table_name);

				// collecting the contents for the table main page tableName.php
				$show = "<?php
				include \"includes/header.php\";
				?>

				<a class=\"btn btn-primary\" href=\"edit-".$small.".php?act=add\"> <i class=\"glyphicon glyphicon-plus-sign\"></i> Add New " . $capital . "</a>

				<h1>" . $capital . "</h1>
				<p>This table includes <?php echo counting(\"".$table_name."\", \"id\");?> ".$small.".</p>

				<table id=\"sorted\" class=\"table table-striped table-bordered\">
				<thead>
				<tr>
				";

				// collecting data for the edit page
				$edit = "<?php
				include \"includes/header.php\";
				\$data=[];

				$"."act = $"."_GET['act'];
				if($"."act == \"edit\") {
					$"."id = $"."_GET['id'];
					$".$small." = getById(\"".$table_name."\", $"."id);
				}
				?>

				<form method=\"post\" action=\"save.php\" enctype='multipart/form-data'>
					<fieldset>
						<legend class=\"hidden-first\">Add New ".$capital."</legend>
						<input name=\"cat\" type=\"hidden\" value=\"".$table_name."\">
						<input name=\"id\" type=\"hidden\" value=\"<?=$"."id?>\">
						<input name=\"act\" type=\"hidden\" value=\"<?=$"."act?>\">
				";

				// continue the save page
				$save .= "
				if($"."cat == \"".$table_name."\" || $"."cat_get == \"".$table_name."\") {
					";

				// continue the home page
				$index .= "
				<tr>
					<td><a href=\"" . $small . ".php\">" . $capital . "</a></td>
					<td><?=counting(\"" . $table_name . "\", \"id\")?></td>
				</tr>
				";

				// continue the sidebar in header
				$icon = random_glyph_icon();
				$header .= "<li><a href=\"" . $small . ".php\"> <i class=\"glyphicon glyphicon-".$icon."\"></i>" . $capital . " <span class=\"pull-right\"><?=counting(\"" . $table_name . "\", \"id\")?></span></a></li>\n";

				$head='';
				$body='';
				$mid='';

				$insert='';
				$update='';
				$values='';
				// finding all the columns in a table
				$row = mysqli_query($link, "SHOW columns FROM " . $table_name)
					or die ('cannot select table fields');

				// looping in the columns
				while ($col = mysqli_fetch_array($row)) {
					// data for the table in the show page tableName.php
					$head .= "		\t<th>" . ucfirst(str_replace("_", " ", $col[0])) . "</th>\n";
					$body .= "	\t<td><?php echo $".$small."s['" . $col[0] . "']?></td>\n";

					if($col[3] != "PRI") {
						if($col[1] == "text") {
							$ckeditor = "";
							if ($_POST["htmlEditor"]) { $ckeditor = "ckeditor "; }

							// continue the edit page with a text area for a type text column
							$edit .= "
							<label>" . ucfirst(str_replace("_", " ", $col[0])) . "</label>
							<textarea class=\"". $ckeditor ."form-control\" name=\"" . $col[0] . "\"><?=$".$small."['" . $col[0] . "']?></textarea><br>
							";
						} else {
							// continue the edit page with an input field
							$edit .= "
							<label>" . ucfirst(str_replace("_", " ", $col[0])) . "</label>
							<input class=\"form-control\" type=\"text\" name=\"" . $col[0] . "\" value=\"<?=$".$small."['" . $col[0] . "']?>\" /><br>
							";
						}
					}

					// check if the column is not the ID to create the corresponding save and insert data
					if ($col[0] != 'id') {

						$save .= "$" . $col[0] . " = addslashes(htmlentities($"."_POST[\"" . $col[0] . "\"], ENT_QUOTES));\n";

						$insert .= " `" . $col[0] . "` ,";

						if($col[0] == "password") {
							$attach_password = 1;
							$values .= " '\".md5($" . $col[0] . ").\"',";

						}else{
							$attach_password = 0;
							$values .= " '\".$" . $col[0] . ".\"' ,";
							$update .= " `" . $col[0] . "` =  '\".$" . $col[0] . ".\"' ,";
						}
					}

				} // end row loop

				// continue show page top part
				$head .= "
				<th class=\"not\">Edit</th>
				<th class=\"not\">Delete</th>
				</tr>
				</thead>";

				// show page central part
				$mid = "
				<?php
				$".$small." = getAll(\"".$table_name."\");
				if($".$small.") foreach ($".$small." as $".$small."s):
					?>
					<tr>";

				// build the whole page
				$show .= $head."\n";
				$show .= $mid."\n";
				$show .= $body."\n";
				$show .= "
						<td><a href=\"edit-".$small.".php?act=edit&id=<?php echo $".$small."s['id']?>\"><i class=\"glyphicon glyphicon-edit\"></i></a></td>
						<td><a href=\"save.php?act=delete&id=<?php echo $".$small."s['id']?>&cat=".$table_name."\" onclick=\"return navConfirm(this.href);\"><i class=\"glyphicon glyphicon-trash\"></i></a></td>
						</tr>
					<?php endforeach; ?>
					</table>
					<?php include \"includes/footer.php\";?>
				";

				$edit .= "<br>
					<input type=\"submit\" value=\" Save \" class=\"btn btn-success\">
					</form>
					<?php include \"includes/footer.php\";?>
				";

				$save .= "

				if($"."act == \"add\") {
					mysqli_query(\$link, \"INSERT INTO `".$table_name."` ( ".removeLastChar($insert).") VALUES (".removeLastChar($values).") \");
				}elseif ($"."act == \"edit\") {
					mysqli_query(\$link, \"UPDATE `".$table_name."` SET ".removeLastChar($update)." WHERE `id` = '\".$"."id.\"' \"); ";

				if($attach_password == 1) {
					$save .= "
					if($"."_POST[\"password\"] && $"."_POST[\"password\"] != \"\") {
						mysqli_query(\$link, \"UPDATE `".$table_name."` SET  `password` =  '\".md5($"."password).\"' WHERE `id` = '\".$"."id.\"' \");
					}
					";
				}

				$save .= "
					}elseif ($"."act_get == \"delete\") {
						mysqli_query(\$link, \"DELETE FROM `".$table_name."` WHERE id = '\".$"."id_get.\"' \");
					}
					header(\"location:\".\"".$small.".php\");
				}
				";

				// creating the show page tableName.php
				createFile($database, $small, $show);
				$message .= "<li>Created page: ".$small.".php</li>";

				// creating the edit page edit-tableName.php
				createFile($database, "edit-".$small, $edit);
				$message .= "<li>Created page: edit-".$small.".php</li>";

				// empty all variables
				$head = "";
				$body = "";

				$insert = "";
				$values = "";
				$update = "";

			} //end table loop

			$save .= "?>";

			$footer ='
					</div>
				</div>

				<!-- jQuery Version 1.11.1 -->
				<script src="https://code.jquery.com/jquery-1.12.0.min.js"></script>

				<!-- Bootstrap Core JavaScript -->
				<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>

				<script type="text/javascript" src="//cdn.ckeditor.com/4.4.3/standard/ckeditor.js"></script>
				<script type="text/javascript" src="//cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
				<script type="text/javascript" src="//cdn.datatables.net/1.10.16/js/dataTables.bootstrap.min.js"></script>

				<script type="text/javascript">
					$(document).ready(function () {
						$("#sidebarCollapse, #sidebarExtend").on("click", function () {
							$("#sidebar").toggleClass("active");
						});

						$("#sorted").DataTable( {
							"bStateSave": true,
							"sPaginationType": "full_numbers"
						});
					});
				</script>

				<script type="text/javascript">
					function navConfirm(loc) {
						if (confirm("Are you sure?")) {
							window.location.href = loc;
						}
						return false;
					}
				</script>
			</body>
			</html>';

			$index .= "</table>
			<?php include \"includes/footer.php\";?>
			";

			$header .= "<li><a href=\"logout.php\"><i class=\"glyphicon glyphicon-log-out\"></i> Logout</a></li>
				</ul>

				<div class=\"visit\">
					<p class=\"text-center\">Created using MAGE &hearts;</p>
					<a href=\"https://github.com/housamz/php-mysql-admin-panel-generator\" target=\"_blank\" >Visit Project</a>
				</div>
			</nav><!-- /end sidebar -->

			<!-- Page Content Holder -->
			<div id=\"content\">";

createFile($database, "includes/connect", $connection);
$message .= "<li>Created connect.php for database connection info.</li>";

createFile($database, "save", $save);
$message .= "<li>Created save.php for create, update, and delete operations on the database.</li>";

createFile($database, "includes/footer", $footer);
$message .= "<li>Created footer.php to hold pages footer.</li>";

createFile($database, "home", $index);
$message .= "<li>Created home.php to have tables at the start page.</li>";

createFile($database, "includes/header", $header);
$message .= "<li>Created header.php to hold pages header.</li>";

$library = "library/";
$path = "generated/".$database.date("Y-m-d_H-i");

copy($library."index.php", $path."/index.php");
$message .= "<li>Created index.php to have login page.</li>";

copy($library."login.php", $path."/login.php");
$message .= "<li>Created login.php to control login.</li>";

copy($library."logout.php", $path."/logout.php");
$message .= "<li>Created logout.php to control login.</li>";

copy($library."data.php", $path."/includes/data.php");
$message .= "<li>Created data.php to have all functions ready.</li>";

copy($library."style.css", $path."/includes/style.css");
$message .= "<li>Created style.css for styling</li></ul>";

echo json_encode(array('status' => 'finished','message'=> '<h1>Finished!</h1><h3>Username: admin<br> Password: admin<br><br><a href="'.$path.'" target="_blank">Visit the Admin Panel <i class="glyphicon glyphicon-new-window"></i></a></h3><br><br>'.$message));

	}

} else {
	echo json_encode(array('status' => 'error','message'=> 'Unknown error occurred'));
}
?>
