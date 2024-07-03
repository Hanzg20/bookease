<?php
/**
 * 
 * Description: This script checks if a user is logged in (by checking session variables).
 *              It's included at the top of pages that require a user to be authenticated.
 */

session_start();

// Check if the user is logged in by looking for 'user_id' in the session
if (isset($_SESSION['user_name']) && isset($_SESSION['user_id'])) {
    // If the 'user_name' and 'user_id' session variables are set, retrieve additional user information
    $username = $_SESSION['user_name'];
    $userid = $_SESSION['user_id'];
} else {
    // If the user is not logged in, redirect to the login page
    header('Location: index.php');
    exit;
}
?>
