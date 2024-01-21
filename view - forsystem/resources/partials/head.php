<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>InovaClass</title>
	<link rel="stylesheet" href="./../../public/css/style.css">
	<link rel="stylesheet" href="./../../public/js/jquery.js">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
	<script src="./../../plugins/sweetalert2/sweetalert2.all.js"></script>
	<!-- Google Font: Source Sans Pro -->
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
	<!-- IonIcons -->
	<link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
	<!-- Theme style -->
	<link rel="stylesheet" href="./../../dist/css/adminlte.min.css">
	<!-- Select2 -->
	<link rel="stylesheet" href="../../plugins/select2/css/select2.min.css">
	<link rel="stylesheet" href="../../plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
	<link rel="icon" type="image/x-icon" href="../../public/images/inovanav.svg">
	<script>
		const Toast = Swal.mixin({
			toast: true,
			position: 'top-end',
			showConfirmButton: false,
			timer: 3000,
			timerProgressBar: true,
			didOpen: (toast) => {
				toast.addEventListener('mouseenter', Swal.stopTimer)
				toast.addEventListener('mouseleave', Swal.resumeTimer)
			}
		})
	</script>
</head>

<body>
	<?php
	// Start the session if it's not started
	if (session_status() == PHP_SESSION_NONE) {
		session_start();
	}

	// Check if the user is not logged in
	if (!isset($_SESSION["user"])) {
		header("location: ../index.php");
		exit(); // Ensure that no further code is executed after the redirect
	}
	// Check if the user has the correct role
	if ($_SESSION["user"]["role"] !== "1") {
		header("location:../index.php");
		exit(); // Ensure that no further code is executed after the redirect
	}
	?>