<?php
$file = $_GET['file']; // Get the file name from the query string
$filepath = 'C:/xampp/htdocs/BBO/' . $file; // Path to the file

// Check if the file exists
if (file_exists($filepath)) {
    // Set appropriate headers for the file download
    header('Content-Description: File Transfer');
    header('Content-Type: application/pdf'); // Adjust if necessary for a different file type
    header('Content-Disposition: attachment; filename="' . basename($filepath) . '"');
    header('Content-Length: ' . filesize($filepath));
    readfile($filepath); // Output the file contents
    exit;
} else {
    echo "File not found.";
}
?>

