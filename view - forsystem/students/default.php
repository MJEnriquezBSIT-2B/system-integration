<?php
// Include necessary files and start the session
include(__DIR__ . "/partials/head.php");
include(__DIR__ . "/partials/nav.php");
include(__DIR__ . "/partials/sidebar.php");

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in
if (!isset($_SESSION['user']['student_id'])) {
    // Redirect to the login page if not logged in
    header("Location: ../index.php");
    exit();
}

// Database configuration
$host = "127.0.0.1:3306";
$username = "u557880975_cms_user";
$password = "cms_@Dm1n_p@ssw0rd";
$database = "u557880975_cms";

// Create a database connection
$conn = mysqli_connect($host, $username, $password, $database);

// Check the connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch the student_id from the session
$studentId = $_SESSION['user']['student_id'];

// Display the uploaded files from the database for the student with subject_code join
$sql = "SELECT files.*, subjects.description as subject_description
        FROM files
        JOIN faculty_subject_students ON files.subject_code = faculty_subject_students.subject_code
        JOIN subjects ON files.subject_code = subjects.subject_code
        WHERE faculty_subject_students.student_id = '$studentId'
          AND files.faculty_id = faculty_subject_students.faculty_id";

$result = mysqli_query($conn, $sql);

// Fetch data from the result set
$uploadedFiles = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $uploadedFiles[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Download Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <style>
        /* Add your custom styles here */
        .table-container {
            margin-top: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        td {
            background-color: #fff;
        }

        .btn-download {
            padding: 6px 10px;
            margin-right: 5px;
            background-color: #28a745;
            color: #fff;
            border: 1px solid #28a745;
            border-radius: 4px;
            text-decoration: none;
            display: inline-block;
            cursor: pointer;
        }

        @media (max-width: 767px) {
            th,
            td {
                padding: 8px;
            }
        }
    </style>

</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <!-- Content Header -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-md-12">
                            <!-- Title in the upper right corner -->
                            <div class="title-container">
                                <?php include(__DIR__ . "/partials/footer.php"); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="container-fluid">
                <!-- Uploaded Files Table Row -->
                <h2>Files</h2>
                <div class="row mt-4">
                    <div class="col-md-12">
                        <!-- Display uploaded files from the database in a table -->
                        <div class="table-container">
                            
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>File Name</th>
                <th>Subject Code</th>
                <th>Subject name</th>
                <th>File Description</th>
                <th>Upload Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($uploadedFiles as $file) : ?>
                <tr>
                    <td><?php echo $file['id']; ?></td>
                    <td><?php echo $file['filename']; ?></td>
                    <td><?php echo $file['subject_code']; ?></td>
                    <td><?php echo $file['subject_description']; ?></td>
                    <td><?php echo $file['file_description']; ?></td>
                    <td><?php echo $file['upload_date']; ?></td>
                    <td>
                        <a href="download.php?file_id=<?php echo $file['id']; ?>" class="btn btn-success btn-sm">Download</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add this script at the end of your HTML body -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
