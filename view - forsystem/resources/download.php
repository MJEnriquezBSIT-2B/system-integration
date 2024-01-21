<?php
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

// Get file ID from the query parameters
$fileId = $_GET['file_id'];

// Fetch file information from the database
$sql = "SELECT * FROM files WHERE id = $fileId";
$result = mysqli_query($conn, $sql);

if ($result) {
    $file = mysqli_fetch_assoc($result);
    $fileName = $file['filename'];
    $fileContent = $file['file_content'];

    // Set appropriate headers for file download
    header("Content-disposition: attachment; filename=$fileName");
    header("Content-type: application/octet-stream");
    header("Content-Length: " . strlen($fileContent));

    // Output file content
    echo $fileContent;
} else {
    echo "File not found in the database.";
}

// Close the database connection
mysqli_close($conn);
?>