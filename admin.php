<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Main Services Page</title>
    <link rel="stylesheet" type="text/css" href="css/style.css" />

</head>

<?php
/**
 
 * Description:The main dashboard or homepage seen by users after logging in.
 *             It  displays an overview of books list content , user-log in information.
 *             and allow uers input search criteria of the specific books by titel,author and genre.
 */
include "headerEm.php";
require_once 'php/database.php';

$db = db_connect();
$page_title = 'Books';

$search_type = isset($_GET['search_type']) ? $_GET['search_type'] : '';
$search_query = isset($_GET['search_query']) ? mysqli_real_escape_string($db, trim($_GET['search_query'])) : '';
// Building SQL query for books listing

$sql = "SELECT * FROM books";
if (!empty($search_query)) {
    $sql .= " WHERE {$search_type} LIKE '%{$search_query}%'";
}
$sql .= " ORDER BY bookid";
$result_set = mysqli_query($db, $sql);
//echo $sql;

if (!$result_set) {
    die("Database query failed.");
}
?>
<!-- Display the message -->
<?php if (isset($message)): ?>
    <p><?php echo $message; ?></p>
<?php endif;?>

<!-- Rest of your HTML code remains unchanged -->

<!-- Search form -->
<form action="admin.php" method="get" class="search-form">
    <label for="type">Search by:</label>
    <select name="search_type" id="type" required>
        <option value="Title">Title</option>
        <option value="Author">Author</option>
        <option value="Genre">Genre</option>
    </select>
    <input type="text" name="search_query" id="input" placeholder="Criteria " />
    <input type="submit" value="Search" />
</form>

<?php

if (isset($_GET['id']) && isset($_SESSION['user_id'])) {
    $book_id = mysqli_real_escape_string($db, $_GET['id']);
    $user_id = $_SESSION['user_id'];

    // Check if the book is already in the user's collection
    $check_query = "SELECT * FROM user_collections WHERE user_id = $user_id AND book_id = $book_id";
    $check_result = mysqli_query($db, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        echo "<p class='success-message'>Book " . htmlspecialchars($book_id) . " is already in your collection.</p>";
    } else {
        $insert_query = "INSERT INTO user_collections (user_id, book_id) VALUES ($user_id, $book_id)";
        $insert_result = mysqli_query($db, $insert_query);

        if ($insert_result) {
            echo "<p class='success-message'>Book " . htmlspecialchars($book_id) . " added to your collection successfully.</p>";
        } else {
            echo "<p class='error-message'>Error adding book " . htmlspecialchars($book_id) . " to your collection. Please try again.</p>";
        }
    }
}
?>
    <?php if (isset($_GET['update']) && isset($_GET['updatedBookId'])): ?>
    <p class="success-message">Book [<?php echo htmlspecialchars($_GET['updatedBookId']); ?>] updated successfully!</p>
    <?php endif;?>

    <?php if (isset($_GET['delete']) && isset($_GET['deletedBookId'])): ?>
    <p class="success-message">Book [<?php echo htmlspecialchars($_GET['deletedBookId']); ?>] deleted successfully!</p>
    <?php endif;?>

<div class="container">
    <div id="content">
        <div class="subjects listing">
            <h3>BOOKS LIST</h3>
            <div class="actions">
                <a class="action" href="new.php">Create New Books</a>
            </div>
        </div>
        <table class="list">
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Author</th>
                <th>Genre</th>
                <th>Rating</th>
                <th>&nbsp;</th>
                <th>Action</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
            </tr>
            <?php if (mysqli_num_rows($result_set) > 0): ?>
                <?php while ($book = mysqli_fetch_assoc($result_set)): ?>
                    <?php
$highlightClass = '';
if (isset($_GET['updatedBookId']) && $_GET['updatedBookId'] == $book['BookID']) {
    $highlightClass = 'highlight';
}
?>
                    <tr class="<?php echo $highlightClass; ?>">
                        <td><?php echo htmlspecialchars($book['BookID']); ?></td>
                        <td><?php echo htmlspecialchars($book['Title']); ?></td>
                        <td><?php echo htmlspecialchars($book['Author']); ?></td>
                        <td><?php echo htmlspecialchars($book['Genre']); ?></td>
                        <td><?php echo htmlspecialchars($book['Rating']); ?></td>
                        <td><a class="action" href="?id=<?php echo urlencode($book['BookID']); ?>">Status</a></td>
                        <td><a class="action" href="show.php?id=<?php echo urlencode($book['BookID']); ?>">View</a></td>
                        <td><a class="action" href="edit.php?id=<?php echo urlencode($book['BookID']); ?>">Edit</a></td>
                        <td><a class="action" href="delete.php?id=<?php echo urlencode($book['BookID']); ?>">Delete</a></td>
                    </tr>
                <?php endwhile;?>
            <?php else: ?>
                <tr>
                    <td colspan="9" width=800>No records found.</td>
                </tr>
            <?php endif;?>
        </table>
    </div>
</div>
<?php db_disconnect($db);?>
<?php include 'footerEm.php';?>
</body>
</html>