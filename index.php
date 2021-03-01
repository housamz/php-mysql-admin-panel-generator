<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="author" content="@housamz">

	<meta name="description" content="Mass Admin Panel">
	<title>MAGE :: MySQL Admin Generator</title>

	<link href="https://maxcdn.bootstrapcdn.com/bootswatch/3.3.7/cosmo/bootstrap.min.css" rel="stylesheet" integrity="sha384-h21C2fcDk/eFsW9sC9h0dhokq5pDinLNklTKoxIZRUn3+hvmgQSffLLQ4G4l2eEr" crossorigin="anonymous">

	<!-- Custom CSS -->
	<style type="text/css">
		.card-blockquote{border: none; margin-top: 30px}
	</style>

	<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
		<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
	<![endif]-->

</head>
<body>
<a href="https://github.com/housamz/php-mysql-admin-panel-generator"><img style="position: absolute; top: 0; right: 0; border: 0; z-index:1000" src="https://camo.githubusercontent.com/38ef81f8aca64bb9a64448d0d70f1308ef5341ab/68747470733a2f2f73332e616d617a6f6e6177732e636f6d2f6769746875622f726962626f6e732f666f726b6d655f72696768745f6461726b626c75655f3132313632312e706e67" alt="Fork me on GitHub" data-canonical-src="https://s3.amazonaws.com/github/ribbons/forkme_right_darkblue_121621.png"></a>

	<div class="container">

		<div class="row">
			<div class="col-lg-12">
				<div class="card text-white bg-primary">
					<div class="card-body">
						<blockquote class="card-blockquote">
							<h1>MAGE :: MySQL Admin Generator</h1>
						</blockquote>
					</div>
				</div>

				<div class="panel panel-default">
					<div class="panel-body">
						<div class="col-lg-9">
							<p>MAGE* is a PHP tool that helps you create a PHP Admin Panel for any MySQL database in seconds.</p>
							<p class="hidden-first">To start, please insert your database server info below:</p>
							<p class="hidden-first hide">Please select your database from below menu:</p>


							<form id="allData">
								<fieldset>
									<legend class="hidden-first">Server Info</legend>
									<div class="form-group hidden-first">
										<label for="inputHost">Host</label>
										<input type="text" class="form-control" id="inputHost" placeholder="Host" aria-describedby="hostHelp" value="127.0.0.1" required>
										<small id="hostHelp" class="form-text text-muted">Your server address, e.g. localhost</small>
									</div>

									<div class="form-group hidden-first">
										<label for="inputUsername">Username</label>
										<input type="text" class="form-control" id="inputUsername" placeholder="Username" aria-describedby="usernameHelp" value="root" required>
										<small id="usernameHelp" class="form-text text-muted">Database username, e.g. root</small>
									</div>

									<div class="form-group hidden-first">
										<label for="inputPassword">Password</label>
										<input type="password" class="form-control" id="inputPassword" placeholder="Password" autocomplete="off">
									</div>

									<button class="btn btn-primary hidden-first" id="connect">Next Step</button>

									<div class="form-group hidden-first hide">
										<legend for="dbSelect">Available Databases</legend>
										<select class="form-control" id="dbSelect">
											<option value="">Please select</option>
										</select>
									</div>
									<div class="form-group hidden-first hide">
										<input type="checkbox" id="htmlEditor">
										<label for="htmlEditor">Enable full HTML editor for columns of type TEXT</label>
									</div>

									<button class="btn btn-primary hidden-first hide" id="generate">Generate Admin Panel</button>
								</fieldset>
							</form>
							<br />
						</div>

						<div class="col-lg-3 alert alert-info">
							<h3>Disclaimer</h3>
							<ul>
								<li>Mage doesn't provide a secure CRUD system. The goal of this tool is to make a fast admin panel to control a MySQL database, but securing that is your own responsibility.</li>
								<li>This may harm your MySQL database, so it's a good idea to keep a backup. </li>
							</ul>
						</div>

						<div class="clearfix"></div><br>
						<div class="col-lg-12">
							<small class="text-muted">
								<strong>* meɪdʒ</strong> 
								<em>noun: mage; plural noun: mages</em> 
								<strong>a magician or learned person.</strong>
							</small>
						</div>
					</div>

				</div>
			</div>

		</div>
	</div>

	<!-- jQuery Version 1.11.1 -->
	<script src="https://code.jquery.com/jquery-1.12.0.min.js"></script>

	<!-- Bootstrap Core JavaScript -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>

	<script type="text/javascript">
		$(document).ready( function () {
			$("#connect, #generate").on("click", function (e) {
				e.preventDefault();
				action = $(this).attr("id");

				if (action === "generate") {
					$("#generate").text("Please Wait...");
					$("#generate").attr("disabled","disabled");
				}

				$.ajax({
					type: "POST",
					url: "handler.php",
					dataType: "JSON",
					data: {
						action: action,
						host: $("#inputHost").val(),
						username: $("#inputUsername").val(),
						password: $("#inputPassword").val(),
						database: $("#dbSelect").val(),
						htmlEditor: $("#htmlEditor").is(":checked") ? 1 : 0,
					},
					success: function(response) {
						if (action === "generate") {
							$("#generate").text("Generate");
							$("#generate").removeAttr("disabled");
						}

						if(response.status === "success") {
							$("#dbSelect").append(response.result);
							$(".hidden-first").toggleClass("hide");
						} else if(response.status === "finished") {
							$(".panel-body").html(response.message);
						} else {
							alert(response.message);
						}
					},
					error: function(XMLHttpRequest, textStatus, errorThrown) {
						console.log(errorThrown);
					}
				});
			});

		});
	</script>


</body>
</html>