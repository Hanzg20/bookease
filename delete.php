<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Delete Confirmation</title>
    <link rel="stylesheet" type="text/css" href="./css/style.css" />
</head>

<body>
<?php 
/**
 * Author: Zhaoguo Han,Feng Qi, Shanghao Li
 * Date: Nov 26 2023
 * Version: 1.0
 * Description:Handles the deletion of items from the database. 
 *             It  confirms with the user before deleting a book and then removes it based on its bookID.
 * 
 */
include "headerEm.php";
require_once 'php/database.php';

$db = db_connect();
// Initialize variables
$title = $author = $genre = $rating = $review = $recommend = $imageID = '';

if (!isset($_GET['id'])) {
    header("location: home.php");
}

$id = isset($_GET["id"]) ? mysqli_real_escape_string($db, $_GET["id"]) : null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sql = "SELECT user_id FROM user_collections WHERE book_id = '$id'";
    $result_set = mysqli_query($db, $sql);
    if (mysqli_num_rows($result_set) > 0) {
        $deleteError = '✘ This book cannot be deleted, as it has been collected.';
    } else {
        $sql = "DELETE FROM books WHERE BookID = '$id'";
        $result = mysqli_query($db, $sql);
        if ($result) {
            header("Location: home.php?delete=success & deletedBookId=" . $id);
            exit;
        } else {
            $deleteError = '✘ Error deleting book: ' . mysqli_error($db);
        }
    }
} else {
    $sql = "SELECT * FROM books WHERE BookID= '$id'";
    $result_set = mysqli_query($db, $sql);

    // Fetch book information, including the image
    if ($result = mysqli_fetch_assoc($result_set)) {
        $title = htmlspecialchars($result['Title']);
        $author = htmlspecialchars($result['Author']);
        $genre = htmlspecialchars($result['Genre']);
        $rating = htmlspecialchars($result['Rating']);
        $review = htmlspecialchars($result['Review']);
        $recommend = htmlspecialchars($result['Recommend']);
        $imageID = $result['BookID']; // Assuming the image ID is the same as BookID
    } else {
        echo "<p>No such book found.</p>";
        db_disconnect($db);
        include 'footerEm.php';
        exit;
    }
}
?>
    <h4>Are you sure you want to Delete this book?</h4>
    <div class="container">
        <div id="content">
            <a class="back-link" href="admin.php">&laquo; Back to the books list</a>
            <h2><p class="book name"><?php echo $title; ?></p></h2>
             <!-- Display the book cover image -->
             <div class="form-row">
               <img src="php/get_cover.php?id=<?php echo $imageID; ?>" alt="Book Cover" class="book-cover">
             </div>
            <p class=" Author"><?php echo "Author: " . $author; ?></p>
            <p class=" Genre"><?php echo "Genre: " . $genre; ?></p>
            <p class=" Rating"><?php echo "Rating: " . $rating; ?></p>
            <form action="<?php echo htmlspecialchars('delete.php?id=' . $id); ?>" method="post">
               <div class="operation-buttons center">
                    <button type="submit" class="button name="commit">Delete this book</button>
               </div>
            </form>
            <div id="deleteErrorDiv" class="error-message">
                 <?php if (!empty($deleteError)): ?>
                 <?php echo $deleteError; ?>
                <?php endif;?>
            </div>
        </div>
    </div>
    <?phpdb_disconnect($db);?>
    <?php include 'footerEm.php';?>
</body>
</html>