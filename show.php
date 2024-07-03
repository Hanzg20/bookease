<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Book Detail Information</title>
    <link rel="stylesheet" type="text/css" href="css/style.css" />
</head>
<body>
<?php
/**
 * 
 * Description: Used to display detailed information about a specific  book.
 *              It  retrieves data from the database based on an ID passed in
 *              the URL and displays it.
 */
include "headerEm.php";
require_once 'php/database.php';

if (isset($_GET['id'])) {
    $db = db_connect();
    $title = $author = $genre = $rating = $review = $recommend = $imageID = '';
    $id = mysqli_real_escape_string($db, $_GET['id']);

    $sql = "SELECT * FROM books WHERE BookID = '$id'";
    $result = mysqli_query($db, $sql);
    if ($book = mysqli_fetch_assoc($result)) {
        $title = htmlspecialchars($book['Title']);
        $author = htmlspecialchars($book['Author']);
        $genre = htmlspecialchars($book['Genre']);
        $rating = htmlspecialchars($book['Rating']);
        $review = htmlspecialchars($book['Review']);
        $imageID = $id;
    } else {
        echo "<p>No such book found.</p>";
    }

    db_disconnect($db);
}
?>

    <div class="container">
       <div id="content">
       <?php
        if($_SESSION['user_type'] === 'Reader'){
            echo '<a class="back-link" href="home.php">&laquo; Back to the books list</a>';
            }
        else{
            echo '<a class="back-link" href="admin.php">&laquo; Back to the books list</a>';    
            }
        ?>
       <h4>Book Details</h4>
        <?php if (isset($book)): ?>
            <form class="form">
                <div class="form-row"> <!-- 1 row -->
                     <label>Title:</label>
                    <input type="text" value="<?php echo $title; ?>" readonly><br>
                     <img src="php/get_cover.php?id=<?php echo $id; ?>" alt="Book Cover" class="book-cover">
                 </div>

                <div class="form-row"> <!-- 3 row -->
                    <label>Author:</label>
                    <input type="text" value="<?php echo $author; ?>" readonly><br>
                    <label>Genre:</label>
                    <input type="text" value="<?php echo $genre; ?>" readonly><br>
                    <label>Rating:</label>
                    <input type="text" value="<?php echo $rating; ?>" readonly><br>
                    <label>Description:</label>
                    <textarea name="review" id="review" rows="14" cols="50" readonly><?php echo $review; ?></textarea><br>
                </div>

            </form>
        <?php endif;?>
        </div>
    </div>

    <?php include 'footerEm.php';?>
</body>
</html>
