<?php
include 'php/check_session.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" type="text/css" href="css/style.css" />
    <title>BookEase</title>
</head>
<body>
    <div class="header">
        <nav>
            <a href="home.php">Home</a>
            <a href="favourite.php">My Profile</a>
            <?php if ($_SESSION['user_type'] === 'Admin'): ?>
                <a href="manage_books.php">Manage Books</a>
                <a href="manage_users.php">Manage Users</a>
                <a href="view_borrowing_status.php">View Status</a>
            <?php elseif ($_SESSION['user_type'] === 'Reader'): ?>
                <a href="search_books.php">Search Books</a>
                <a href="borrow_books.php">Borrow Books</a>
                <a href="return_books.php">return Books</a>
            <?php endif; ?>
        </nav>
        <div class="user-info">
            <img src="image/loginImage1.png" alt="Profile" class="profile-pic" />
            <span class="username">Welcome: <span id="usernameDisplay"><?php echo htmlspecialchars($username); ?></span></span>
            <a href="php/logout.php" class="logout-link">Logout</a>
        </div>
    </div>
    <script src="js/script.js" defer></script>
</body>
</html>
