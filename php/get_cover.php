<?php
/**
 * Author: Zhaoguo Han,Feng Qi, Shanghao Li
 * Date: Nov 26 2023
 * Version: 1.0
 * Description:This file  be responsible for retrieving and displaying 
 *             image files, such as book covers, from the database or file system.
 * 
 */
require_once 'database.php';
$db = db_connect();

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($db, $_GET['id']);

    $sql = "SELECT Cover FROM books WHERE BookID = '$id'";
    $result = mysqli_query($db, $sql);

    if ($row = mysqli_fetch_assoc($result)) {
        header("Content-Type: image/jpeg"); // Adjust the content type as needed (e.g., image/png, image/gif)
        echo $row['Cover'];
    } else {
        // Handle error or no image found
        echo "No image found.";
    }
} else {
    // Handle error, no ID provided
    echo "No ID provided.";
}

db_disconnect($db);
?>
