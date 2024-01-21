<?php
session_start();

// Include your database connection code here if not already included

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Your faculty authentication logic (replace with your actual logic)
    if (isFaculty($email, $password)) {
        // Faculty login successful
        $_SESSION['user_type'] = 'faculty';
        // Redirect to faculty dashboard or home page
        header('Location: faculty_dashboard.php');
        exit();
    } else {
        // Faculty login failed
        $_SESSION['error_message'] = 'invalid_creds';
        $_SESSION['error_timeout'] = time() + 3; // Set a timeout of 3 seconds
        header('Location: login_page.php'); // Redirect back to login page
        exit();
    }
}

// Function to check faculty credentials (replace with your actual logic)
function isFaculty($email, $password) {
    // Your logic to check faculty credentials in the database
    // Example: SELECT * FROM faculty WHERE email = '$email' AND password = '$password'
    // Return true if valid, false otherwise
    return false;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include(__DIR__ . "/partials/head.php"); ?>
    <!-- Add additional head elements as needed -->
</head>

<body>

    <section class="vh-100" style="background-color: #fefefe">
        <div class="container py-5 h-100">
            <div class="row d-flex align-items-center justify-content-between h-100">
                <div class="col-md-8 col-lg-7 col-xl-6" style="width: 400px;">
                    <!-- Your logo or image -->
                    <img src="../public/images/inovalogo.svg" style="width:100%; object-fit: contain;" class="img-fluid" alt="Logo">
                </div>
                <div class="col-md-7 col-lg-5 col-xl-5 offset-xl-1 bg-light card rounded-lg shadow-p3" style="padding-inline: 50px; padding-block: 50px">
                    <form action="../controller/auth/loginController.php" method="POST">
                        <!-- Email input -->
                        <div class="form-outline mb-4">
                            <input type="email" id="email" name="email" class="form-control form-control-lg" />
                            <label class="form-label" for="email">Email address</label>
                        </div>

                        <!-- Password input -->
                        <div class="form-outline mb-3">
                            <input type="password" id="password" name="password" class="form-control form-control-lg" />
                            <label class="form-label" for="password">Password</label>
                            <?php
                            // Display any error messages
                            if (isset($_SESSION['error_message']) && $_SESSION["error_message"] == "invalid_creds") {
                                echo '<p id="error-message" class="mt-1 text-xs text-red-600">' . 'Invalid Credentials' . '</p>';
                                // Check if the timeout has passed, and if so, remove the error message
                                if (isset($_SESSION['error_timeout']) && $_SESSION['error_timeout'] < time()) {
                                    unset($_SESSION['error_message']);
                                    unset($_SESSION['error_timeout']);
                                }
                            }
                            ?>
                        </div>

                        <!-- Submit button -->
                        <button type="submit" class="btn btn-primary btn-lg btn-block">Login</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <?php include(__DIR__ . "/partials/footer.php"); ?>

    <script>
        // JavaScript to hide the error message after 3 seconds
        setTimeout(function () {
            var errorMessage = document.getElementById('error-message');
            if (errorMessage) {
                errorMessage.style.display = 'none';
            }
        }, 3000);
    </script>

    <!-- Add additional scripts or links as needed -->

</body>

</html>
