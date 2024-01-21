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

// Fetch the updated list of files
$sql = "SELECT * FROM files";
$result = mysqli_query($conn, $sql);

// Display the updated list of files
while ($row = mysqli_fetch_assoc($result)) {
    echo '<tr>';
    echo '<td>' . $row['id'] . '</td>';
    echo '<td>' . $row['filename'] . '</td>';
    echo '<td>' . $row['file_description'] . '</td>';
    echo '<td>' . $row['upload_date'] . '</td>';
    echo '<td>';
    echo '<a href="uploads/' . $row['filename'] . '" download class="btn btn-success btn-sm">Download</a>';
    echo '<button class="btn btn-info btn-sm">Update</button>';
    echo '<a href="?action=delete&file_id=' . $row['id'] . '" class="btn btn-danger btn-sm">Delete</a>';
    echo '</td>';
    echo '</tr>';
}
?>
