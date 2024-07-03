<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>User Registration</title>
    <link rel="stylesheet" href="css/style.css" />
    <script src="js/script.js" defer></script>
</head>

<?php
/**
 * Author: Zhaoguo Han,Feng Qi, Shanghao Li
 * Date: Nov 26 2023
 * Version: 1.0
 * Description:This file handles the user registration process. It typically contains
 *             a form where users can enter their details (username, email, password)
 *             and logic to insert these details into the database.
 */
require_once 'php/database.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $db = db_connect();
    $signupHint = ''; // Initialize the variable to signup  message

    // Retrieve and sanitize input
    $newUsername = mysqli_real_escape_string($db, $_POST['newUsername']);
    $email = mysqli_real_escape_string($db, $_POST['email']);
    $newPassword = md5(mysqli_real_escape_string($db, $_POST['newPassword'])); // Note: Consider using a more secure hashing method

    // Prepare SQL to prevent SQL injection
    $stmt = $db->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $newUsername, $email, $newPassword);
    
    // Execute and check the query  
    if ($stmt->execute()) {
        $signupHint = ' ✓ User['.$newUsername .'] registered successfully, you can log in!';
    } else {
        $signupHint ="✘ Error: ". $stmt->error;
    }

    $stmt->close();
    db_disconnect($db);
}
?>
<body>
    <div class="login_container">
        <h3>HLQ Book Cataloging System - Sign Up</h3>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data"
         onsubmit="return validateSignup();">
        <table width="100%" height="130%">
                <tr>
                    <td><label for="email">Email:</label></td>
                    <td>
                        <input type="text" id="email" name="email" required />
                        <div id="emailError" class="error-message"></div>
                    </td>
                </tr>
                <tr>
                    <td><label for="newUsername">User Name:</label></td>
                    <td>
                        <input type="text" id="newUsername" name="newUsername" required />
                        <div id="newUsernameError" class="error-message"></div>
                    </td>
                </tr>
                <tr>
                    <td><label for="newPassword">Password:</label></td>
                    <td>
                        <input type="password" id="newPassword" name="newPassword" required />
                        <div id="newPasswordError" class="error-message"></div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <div id="loginErrorDiv" class="error-message">
                            <?php if (!empty($signupHint)): ?>
                                <?php echo $signupHint; ?>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <div class="operation-buttons center">
                            <button type="submit" class="button">Sign Up</button>
                        </div>
                    </td>
                </tr>
            </table>
        </form>
        <p>Already have an account? <a href="index.php">Login</a></p>
    </div>
</body>
</html>
