<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>User Registration</title>
    <h1>Online Book Borrowing Management System </h1>
    <link rel="stylesheet" href="css/style.css" />
    <script src="js/script.js" defer></script>
</head>
<body>
<?php
/**
 * Description: This file handles the user registration process. It typically contains
 * a form where users can enter their details (username, email, phonenumber, password)
 * and logic to insert these details into the database.
 */
session_start();
require_once 'php/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $db = db_connect();
    $signupHint = ''; // Initialize the variable for signup message

    // Retrieve and sanitize input
    $newUsername = mysqli_real_escape_string($db, $_POST['newUsername']);
    $email = mysqli_real_escape_string($db, $_POST['email']);
    $phone = mysqli_real_escape_string($db, $_POST['phone']);
    $newPassword = password_hash(mysqli_real_escape_string($db, $_POST['newPassword']), PASSWORD_BCRYPT); // Use a more secure hashing method

    // Check for duplicate email
    $queryForDuplicate = "SELECT * FROM users WHERE email = ?";
    $stmt = mysqli_prepare($db, $queryForDuplicate);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        $_SESSION['user_exists'] = "✘ User Email exists! Please enter again.";
        header("Location: signup.php");
        exit();
    } else {
        // Prepare SQL to prevent SQL injection
        $stmt1 = mysqli_prepare($db, "INSERT INTO users (username, email, phonenumber, password) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt1, "ssss", $newUsername, $email, $phone, $newPassword);

        // Execute and check the query
        if (mysqli_stmt_execute($stmt1)) {
            $_SESSION['signup_success'] = '✓ User [' . $newUsername . '] registered successfully, you can log in!';
        } else {
            $_SESSION['signup_error'] = "✘ Error: " . mysqli_stmt_error($stmt1);
        }

        mysqli_stmt_close($stmt1);
    }
    
    mysqli_stmt_close($stmt);
    db_disconnect($db);

    header("Location: signup.php");
    exit();
}
?>
    <div class ="welcome">
        <div class="login_container">
            <div class="sameRow">
                <img src="image/logo.jpg" alt="BookEase Logo" width="100" height="100">
                <h3>Sign Up</h3>
            </div>    
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data" onsubmit="return validateSignup();">
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
                        <td><label for="phone">Phone Number:</label></td>
                        <td>
                            <input type="text" id="phone" name="phone" required />
                            <div id="phoneError" class="error-message"></div>
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
                                <?php 
                                if (isset($_SESSION['user_exists'])) {
                                    echo $_SESSION['user_exists'];
                                    unset($_SESSION['user_exists']);
                                } elseif (isset($_SESSION['signup_success'])) {
                                    echo $_SESSION['signup_success'];
                                    unset($_SESSION['signup_success']);
                                } elseif (isset($_SESSION['signup_error'])) {
                                    echo $_SESSION['signup_error'];
                                    unset($_SESSION['signup_error']);
                                }
                                ?>
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
    </div>
    <?php include 'footerEm.php';?>   
</body>
</html>