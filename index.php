<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>User Login </title>
     <h1>Online Book Borrowing Management System </h1>
    <link rel="stylesheet" href="css/style.css" />
    <script src="js/script.js" defer></script>
</head>

<?php
/**
 * Description:The landing page of the website, and used as the login page.
 *             With a form for userEmail and password and logic to authenticate users.
 */

require_once 'php/database.php';
$loginError = ''; // Initialize the variable to store login error message

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $db = db_connect();
    $userEmail = $_POST["userEmail"];
    $password = $_POST["password"];
    // Hash the password using MD5 (not recommended for secure applications)
    $hashedPassword = md5($password);
    // Prepare SQL to prevent SQL injection
    $stmt = $db->prepare("SELECT * FROM users WHERE Email = ? AND password = ?");
    $stmt->bind_param("ss", $userEmail, $hashedPassword);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Start a new session and set a session variable
        session_start();
        $user = $result->fetch_assoc();

        $_SESSION["user_id"] = $user['UserID'];
        echo $user['UserID'];
        $_SESSION["user_name"] = $user['UserName'];
        $_SESSION["user_type"] = $user['UserType'];
        
        // Redirect the user to a different page
        if($user['UserType']=="Reader"){
        header("Location: home.php");
        }
        else{
        header("Location: admin.php");    
        }
        exit;
    } else {
        // User feedback for invalid login
        $loginError = '✘ Invalid userEmail or password';
    }
    $stmt->close();
    db_disconnect($db);
}
?>
<body>
    <div class ="welcome">
        <div class="login_container">
            <div class="sameRow">
                <img src="image/logo.jpg" alt="BookEase Logo" width="100" height="100">
                <h3>Login</h3>
            </div>
            <form id="loginForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" onsubmit="return validateLogin();">
            <table width="100%" height="130%">
            <tr>
                <td><label for="userEmail">User Email:</label></td>
                <td><input type="text" id="userEmail" name="userEmail" required /></td>
            </tr>
            <tr>
                <td><label for="password">Password :</label></td>
                <td><input type="password" id="password" name="password" required/></td>
            </tr>
            <tr>
                <td colspan="2">
                <div id="loginErrorDiv" class="error-message">
                    <?php if (!empty($loginError)): ?>
                        <?php echo $loginError; ?>
                        <?php endif;?>
                </div>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <div class="operation-buttons center">
                        <button type="submit" class="button">Login</button>
                    </div>
                </td>
            </tr>
            </table>
            </form>
            <p>Don't have an account? <a href="signup.php">Sign Up</a></p>
        </div>
    </div>
    <?php include 'footerEm.php';?>
  </body>
</html>