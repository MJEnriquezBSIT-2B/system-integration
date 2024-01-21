<?php
include(__DIR__ . "/partials/head.php");
include(__DIR__ . "/partials/nav.php");
include(__DIR__ . "/partials/sidebar.php");

// Start the session if it's not started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Fetch the faculty's ID from the session
if (isset($_SESSION['user']['faculty_id'])) {
    $loggedFacultyId = $_SESSION['user']['faculty_id'];
} else {
    // Redirect to the login page or handle the case where the user is not logged in
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

// Handle file upload and database insertion
$uploadMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $uploadDir = 'uploads/';

    // Define the allowed file extensions
    $allowedExtensions = ['pdf', 'docx', 'doc', 'txt', 'xlsx', 'xls', 'sql', 'jpg', 'jpeg', 'png', 'gif', 'zip', 'pptx', 'ppt', 'pbix', 'pbit'];

    $fileName = $_FILES['file']['name'];
    $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);

    if (in_array(strtolower($fileExtension), $allowedExtensions)) {
        $uploadFile = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadFile)) {
            // Read file content
            $fileContent = file_get_contents($uploadFile);
            $fileContent = mysqli_real_escape_string($conn, $fileContent);

            // Insert file information into the database
            $fileDescription = isset($_POST['file_description']) ? $_POST['file_description'] : '';
            $subjectCode = isset($_POST['subject_code']) ? $_POST['subject_code'] : '';
            $uploadDate = date("Y-m-d H:i:s");

            // Fetch the faculty's ID from the session
            if (isset($_SESSION['user']['faculty_id'])) {
                $loggedFacultyId = $_SESSION['user']['faculty_id'];

                // Insert faculty ID along with other file information
                $sql = "INSERT INTO files (filename, file_description, upload_date, file_content, faculty_id, subject_code) 
                        VALUES ('$fileName', '$fileDescription', '$uploadDate', '$fileContent', '$loggedFacultyId', '$subjectCode')";

                if (mysqli_query($conn, $sql)) {
                    $uploadMessage = '<div class="alert alert-success" role="alert">File uploaded and record inserted successfully!</div>';
                } else {
                    $uploadMessage = '<div class="alert alert-danger" role="alert">Error inserting record: ' . mysqli_error($conn) . '</div>';
                }
            } else {
                $uploadMessage = '<div class="alert alert-danger" role="alert">Error: Faculty ID not found in session.</div>';
            }
        } else {
            $uploadMessage = '<div class="alert alert-danger" role="alert">Error uploading file.</div>';
        }
    } else {
        $uploadMessage = '<div class="alert alert-danger" role="alert">Invalid file type. Allowed types: ' . implode(', ', $allowedExtensions) . '</div>';
    }
}

// Handle file deletion
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['file_id'])) {
    $fileId = $_GET['file_id'];

    // Delete file information from the database
    $deleteSql = "DELETE FROM files WHERE id = $fileId";

    if (mysqli_query($conn, $deleteSql)) {
        $uploadMessage = '<div class="alert alert-success" role="alert">File record deleted successfully!</div>';
    } else {
        $uploadMessage = '<div class="alert alert-danger" role="alert">Error deleting file record from the database: ' . mysqli_error($conn) . '</div>';
    }
}

// Handle file update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'update') {
    $fileId = $_POST['file_id'];
    $newDescription = mysqli_real_escape_string($conn, $_POST['file_description']);

    // Check if a new file is being uploaded
    if (isset($_FILES['update_file'])) {
        $uploadDir = 'uploads/';
        $allowedExtensions = ['pdf', 'docx', 'doc', 'txt'];

        $newFileName = $_FILES['update_file']['name'];
        $newFileExtension = pathinfo($newFileName, PATHINFO_EXTENSION);

        if (in_array(strtolower($newFileExtension), $allowedExtensions)) {
            $newUploadFile = $uploadDir . $newFileName;

            if (move_uploaded_file($_FILES['update_file']['tmp_name'], $newUploadFile)) {
                // Read new file content
                $newFileContent = file_get_contents($newUploadFile);
                $newFileContent = mysqli_real_escape_string($conn, $newFileContent);

                // Update file content and description in the database
                $updateSql = "UPDATE files 
                              SET filename = '$newFileName', file_description = '$newDescription', file_content = '$newFileContent' 
                              WHERE id = $fileId";

                if (mysqli_query($conn, $updateSql)) {
                    $uploadMessage .= '<div class="alert alert-success" role="alert">File and description updated successfully!</div>';
                } else {
                    $uploadMessage .= '<div class="alert alert-danger" role="alert">Error updating file and description: ' . mysqli_error($conn) . '</div>';
                }
            } else {
                $uploadMessage .= '<div class="alert alert-danger" role="alert">Error uploading new file.</div>';
            }
        } else {
            $uploadMessage .= '<div class="alert alert-danger" role="alert">Invalid file type. Allowed types: ' . implode(', ', $allowedExtensions) . '</div>';
        }
    } else {
        // Update file description only
        $updateSql = "UPDATE files SET file_description = '$newDescription' WHERE id = $fileId";

        if (mysqli_query($conn, $updateSql)) {
            $uploadMessage .= '<div class="alert alert-success" role="alert">File description updated successfully!</div>';
        } else {
            $uploadMessage .= '<div class="alert alert-danger" role="alert">Error updating file description: ' . mysqli_error($conn) . '</div>';
        }
    }
}

// Display the uploaded files from the database
$sql = "SELECT * FROM files";
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
    <title>File Upload Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    
    <style>
        /* Style for the table container */
        .table-container {
            margin-top: 20px;
        }

        /* Style for the table */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
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

        /* Style for the download button */
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

        /* Style for the update and delete buttons */
        .btn-update, .btn-delete {
            padding: 6px 10px;
            margin-right: 5px;
            background-color: #007bff;
            color: #fff;
            border: 1px solid #007bff;
            border-radius: 4px;
            text-decoration: none;
            display: inline-block;
            cursor: pointer;
        }

        /* Style for the update modal */
        #updateModal {
            display: none;
        }

        /* Responsive styles for small screens */
        @media (max-width: 767px) {
            th, td {
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
                                <h1>File Upload Dashboard</h1>
                                <?php include(__DIR__ . "/partials/footer.php"); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
<div class="container-fluid">
    <!-- Form Row -->
    <div class="row">
        <div class="col-md-12">
            <form action="default.php" method="post" enctype="multipart/form-data">
                <div class="form-group row">
                    <label for="file" class="col-sm-2 col-form-label">Choose File:</label>
                    <div class="col-sm-8">
                        <input type="file" name="file" class="form-control-file">
                    </div>
                    <div class="col-sm-2">
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="file_description" class="col-sm-2 col-form-label">File Description:</label>
                    <div class="col-sm-10">
                        <input type="text" name="file_description" class="form-control">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="subject_code" class="col-sm-2 col-form-label">Subject Code:</label>
                    <div class="col-sm-10">
                        <!-- Use a select dropdown for subject code -->
                        <select name="subject_code" class="form-control">
                            <?php
                            // Fetch subject codes from the faculty_subjects table
                            $subjectCodesQuery = "SELECT DISTINCT subject_code FROM faculty_subjects WHERE faculty_id = '$loggedFacultyId'";
                            $subjectCodesResult = mysqli_query($conn, $subjectCodesQuery);

                            if ($subjectCodesResult) {
                                while ($row = mysqli_fetch_assoc($subjectCodesResult)) {
                                    echo "<option value='{$row['subject_code']}'>{$row['subject_code']}</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </form>
            <?php echo $uploadMessage; ?>
        </div>
    </div>

                <!-- Uploaded Files Table Row -->
    <div class="row mt-4">
        <div class="col-md-12">
            <!-- Display uploaded files from the database in a table -->
            <h2>Uploaded Files</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>File Name</th>
                        <th>File Description</th>
                        <th>Subject Code</th>
                        <th>Upload Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($uploadedFiles as $file) : ?>
                        <?php
                        // Check if the faculty ID associated with the file matches the logged-in faculty's ID
                        if ($file['faculty_id'] == $loggedFacultyId) :
                        ?>
                            <tr>
                                <td><?php echo $file['id']; ?></td>
                                <td><?php echo $file['filename']; ?></td>
                                <td><?php echo $file['file_description']; ?></td>
                                <td><?php echo $file['subject_code']; ?></td>
                                <td><?php echo $file['upload_date']; ?></td>
                                <td>
                                    <!-- Modify the download link to point to a PHP script -->
                                    <a href="download.php?file_id=<?php echo $file['id']; ?>" class="btn btn-success btn-sm">Download</a>
                                    <button class="btn btn-info btn-sm" onclick="openUpdateModal(<?php echo $file['id']; ?>)">Update</button>
                                    <button class="btn btn-danger btn-sm" onclick="confirmDelete(<?php echo $file['id']; ?>)">Delete</button>
                                </td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

    <!-- Modal for File Update -->
    <div class="modal fade" id="updateModal" tabindex="-1" role="dialog" aria-labelledby="updateModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateModalLabel">Update File Information</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Update Form -->
                    <form action="default.php?action=update" method="post" enctype="multipart/form-data">
                        <input type="hidden" id="updateFileId" name="file_id">
                        <div class="form-group">
                            <label for="updateFile">Choose New File:</label>
                            <input type="file" name="update_file" class="form-control-file">
                        </div>
                        <div class="form-group">
                            <label for="updateFileDescription">File Description:</label>
                            <input type="text" class="form-control" id="updateFileDescription" name="file_description">
                        </div>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <!-- Add this script at the end of your HTML body -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        function confirmDelete(fileId) {
            var confirmDelete = confirm("Are you sure you want to delete this file?");
            if (confirmDelete) {
                // Handle deletion logic here, you can redirect to delete.php or trigger an AJAX request
                window.location.href = "default.php?action=delete&file_id=" + fileId;
            }
        }

        function openUpdateModal(fileId) {
            // Set the file ID in the modal form
            document.getElementById('updateFileId').value = fileId;

            // Open the update modal
            $('#updateModal').modal('show');
        }
    </script>
</body>

</html>