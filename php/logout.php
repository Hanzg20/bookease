<?php
/**
 *
 * Description: Ends the user's session and logs them out.
 *               It redirects the user to the login page or home page after logging out.
 */
// Start the session
session_start();
// Unset all of the session variables
$_SESSION = array();
// Destroy the session.
session_destroy();
// Redirect to the login page or any other page you want after logout
header("Location: ../index.php");
exit();
?>
