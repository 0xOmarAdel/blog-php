<?php
	require_once "conn.php";

	if(isset($_POST['send-msg'])){
		require "mail.php";

		$from_name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
		$from = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
		$body = filter_var( $_POST['msg'], FILTER_SANITIZE_STRING);

		if(!empty($from_name && !empty($from) && !empty($body))){
			$subject = "Contact Form";

			$to = "omaradelawad20013@gmail.com";

			if(smtpmailer($to, $from, $from_name, $subject, $body)){
				$result = "<div class='alert alert-success' role='alert'>Your msg has been sent successfully</div>";
			}else{
				$result = "<div class='alert alert-danger' role='alert'>There was an error while sending your msg</div>";
			}
		}else{
			$result = "<div class='alert alert-danger' role='alert'>All fields are required obviously.</div>";
		}

	}
?>

<!DOCTYPE html>
<html>
<head>
	<title>Blog - Contact Us</title>
	<link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/all.min.css">
	<link rel="stylesheet" href="css/main-styles.css">
    <link rel="stylesheet" href="css/nav.css">
    <link rel="stylesheet" href="css/sidebar.css">
	<link rel="stylesheet" href="css/contact-us.css">
</head>
<body>
	<?php include "nav.php"; ?>
	<div class="container">
		<div class="row">
			<div class="col-xl-7">
				<div class="contact-us">
					<p class="heading font-weight-bold text-center">Contact Us</p>
					<?php if(isset($result)){ echo $result;} ?>
					<form action="" method="POST" onSubmit="return validate_form(this)">
						<div class="input-wrapper w-100">
							<input type="text" class="form-control mb-2" name="name" placeholder="Full Name" required>
						</div>
                        <div class="input-wrapper w-100">
                     		<input type="email" class="form-control mb-2" name="email" placeholder="E-mail" required>
						</div>
                        <textarea rows="5" class="form-control mb-3" name="msg" placeholder="Msg ..." required></textarea>
                        <div class="input-wrapper w-100">
                        	<button type="submit" class="btn btn-primary d-block m-auto" name="send-msg">
							    <i class="fas fa-paper-plane pr-2"></i> Send
							</button>
						</div>
                    </form>
				</div>
			</div>
			<div class="col-xl-4 offset-xl-1">
                <div class="side-bar">
                    <?php include "sidebar.php"; ?>
                </div>
			</div>
		</div>
	</div>
	<?php include "footer.php"; ?>
	<script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
	<script src="js/main.js"></script>
</body>
</html>