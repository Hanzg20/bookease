<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Recommended Books</title>
</head>

<body>
<?php
/**
 * Author: Zhaoguo Han,Feng Qi, Shanghao Li
 * Date: Nov 26 2023
 * Version: 1.0
 * Description:This file manage the user's favorite books.
 *             It can delete books to a user's list of favorites and display books.
 *
 */
include "headerEm.php";
require_once 'php/database.php';


$userID = $_SESSION['user_id']; // Retrieve the user ID from the session
$db = db_connect();

// Check if a book is marked for delete
if (isset($_GET['delete_book'])) {
    $deleteBookID = mysqli_real_escape_string($db, $_GET['delete_book']);

    // SQL to delete the book from the user's collection
    $deleteSQL = "DELETE FROM user_collections WHERE user_id = '$userID' AND book_id = '$deleteBookID'";
    $deleteResult = mysqli_query($db, $deleteSQL);
    if (!$deleteResult) {
        die("Delete query failed: " . mysqli_error($db));
    }
}

// SQL to retrieve recommended books for the current user
$sql = "SELECT books.BookID, books.Title, books.Author,books.Genre
                FROM books
                INNER JOIN user_collections ON books.BookID = user_collections.book_id
                WHERE user_collections.user_id = '$userID'";

$result_set = mysqli_query($db, $sql);

if (!$result_set) {
    die("Query failed: " . mysqli_error($db));
}

?>

    <div class="container">
        <div id="content">
            <a class="back-link" href="home.php">&laquo; Back to the books list</a>
            <div class="subjects listing">
                <h3>My Collection books</h3>
            </div>
            <table class="list">
                <tr>
                    <th>Book Id</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Genre</th>
                    <th> ... </th>
                </tr>
                <?php if (mysqli_num_rows($result_set) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result_set)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['BookID']); ?></td>
                    <td><?php echo htmlspecialchars($row['Title']); ?></td>
                    <td><?php echo htmlspecialchars($row['Author']); ?></td>
                    <td><?php echo htmlspecialchars($row['Genre']); ?></td>
                    <td>
                        <a href="?delete_book=<?php echo urlencode($row['BookID']); ?>"
                            onclick="return confirm('Are you sure you want to delete this book your collection?')">Delete</a>
                    </td>
                </tr>
                <?php endwhile;?>
                <?php else: ?>
                <tr>
                    <td colspan="3">No favorite books found.</td>
                </tr>
                <?php endif;?>
            </table>
            <?phpdb_disconnect($db);?>
        </div>
    </div>
    <?php include 'footerEm.php';?>
</body>
</html>