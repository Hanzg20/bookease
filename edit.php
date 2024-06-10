<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Edit Book</title>
    <link rel="stylesheet" type="text/css" href="css/style.css" />
    <script src="js/script.js" defer></script>
</head>
<body>
<?php 
/**
 * Author: Zhaoguo Han,Feng Qi, Shanghao Li
 * Date: Nov 26 2023
 * Version: 1.0
 * Description:Used for editing existing books. It  has a form pre-filled with the books's 
 *            current data (fetched from the database), which can be modified and updated.
 * 
 */
include "headerEm.php";
require_once 'php/database.php';
$db = db_connect();

$title = $author = $genre = $rating = $review = $recommend = $imageID = '';
$bookFound = false;
$genreOptions = '';
$genreSql = "SELECT GenreID, GenreName FROM Genres ORDER BY GenreName";
$genreResult = mysqli_query($db, $genreSql);
// only use for SQL debug
function debugQuery($sql, $params)
{
    foreach ($params as $param) {
        if (is_string($param)) {
            $param = "'" . $param . "'";
        }
        $sql = preg_replace('/\?/', $param, $sql, 1);
    }
    return $sql;
}

if ($genreResult && mysqli_num_rows($genreResult) > 0) {
    while ($row = mysqli_fetch_assoc($genreResult)) {
        $genreOptions .= "<option value='" . htmlspecialchars($row['GenreName']) . "'>" . htmlspecialchars($row['GenreName']) . "</option>";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize input
    $id = mysqli_real_escape_string($db, $_POST['id']);
    $title = mysqli_real_escape_string($db, $_POST['title']);
    $author = mysqli_real_escape_string($db, $_POST['author']);
    $genre = mysqli_real_escape_string($db, $_POST['genre']);
    $rating = mysqli_real_escape_string($db, $_POST['rating']);
    $review = mysqli_real_escape_string($db, $_POST['review']);
    $recommend = isset($_POST['Recommend']) ? 'Y' : 'F';
    $imageID = $id;

    $cover = null;
    if (isset($_FILES['cover']) && $_FILES['cover']['error'] == 0) {
        // Read the binary data from the uploaded file
        $cover = file_get_contents($_FILES['cover']['tmp_name']);
    }

    // Update the book in the database
    $cover = null;
    if (isset($_FILES['cover']) && $_FILES['cover']['error'] == 0) {
        $cover = file_get_contents($_FILES['cover']['tmp_name']);
    }

    if ($cover != null) {
        $stmt = $db->prepare("UPDATE books SET Title = ?, Author = ?, Genre = ?, Rating = ?, Review = ?, Cover = ?, Recommend = ? WHERE BookID = ?");
        $stmt->bind_param("sssisbsi", $title, $author, $genre, $rating, $review, $cover, $recommend, $id);
        $stmt->send_long_data(5, $cover);

    } else {
        $stmt = $db->prepare("UPDATE books SET Title = ?, Author = ?, Genre = ?, Rating = ?, Review = ?, Recommend = ? WHERE BookID = ?");
        $stmt->bind_param("sssissi", $title, $author, $genre, $rating, $review, $recommend, $id);
    }

    $debugSql = debugQuery("UPDATE books SET Title = ?, Author = ?, Genre = ?, Rating = ?, Review = ?, Cover = ?, Recommend = ? WHERE BookID = ?",
    [$title, $author, $genre, $rating, $review, 'BINARY_DATA', $recommend, $id]);
    echo "Debug SQL: " . $debugSql;

    if ($stmt->execute()) {
        // Success handling
        header("Location: home.php?update=success & updatedBookId=" . $id);
        exit;
    } else {
        // Error handling
        echo "Error updating book with ID '$id': " . mysqli_error($db);
    }

} elseif (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($db, $_GET['id']);

    // Fetch the book's current details
    $sql = "SELECT * FROM books WHERE BookID = '$id'";
    $result = mysqli_query($db, $sql);
    if ($book = mysqli_fetch_assoc($result)) {
        $title = htmlspecialchars($book['Title']);
        $author = htmlspecialchars($book['Author']);
        $genre = htmlspecialchars($book['Genre']);
        $rating = htmlspecialchars($book['Rating']);
        $review = htmlspecialchars($book['Review']);
        $bookFound = true;
    } else {
        echo "<p>No such book found.</p>";
    }
}

if ($bookFound) {
    // Display edit form with book details using variables
    ?>


    <div class="container">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post"
            enctype="multipart/form-data" onsubmit="return validate();">
            <input type="hidden" name="id" value="<?php echo $id; ?>">
        <div class="form-row">
            <a class="back-link" href="home.php">&laquo; Back to the books list</a><br><br>
            <h4>Edit Book Details</h4>

            <label>Book ID:</label>
            <input type="text" name="id" value="<?php echo $id; ?>" readonly>
            <label for="cover">Cover Image:</label>
            <img id="cover-preview" src="php/get_cover.php?id=<?php echo $id; ?>" alt="Book Cover" class="book-cover">
            <input type="file" name="cover" id="cover" onchange="previewImage();"><br>
         </div>

        <div class="form-row">
            <label>Title:</label>
            <input type="text" id="title" name="title" value="<?php echo $title; ?>">
            <div id="titleError" class="error-message"></div>

            <label>Author:</label>
            <input type="text" id="author" name="author" value="<?php echo $author; ?>">
            <div id="authorError" class="error-message"></div>

            <label for="genre">Genre:</label>
            <select name="genre" id="genre" required><?php echo $genreOptions; ?></select><br>
            <div id="genreError" class="error-message"></div>

            <label>Rating:</label>
            <input type="number" id="rating" name="rating" value="<?php echo $rating; ?>" step="1">
            <div id="ratingError" class="error-message"></div>

            <label>Review:</label>
            <textarea name="review" id="review" rows="8" cols="50"><?php echo $review; ?></textarea><br>
            <div id="reviewError" class="error-message"></div>

            <div>
                <label>Recommend:</label>
                <input type="checkbox" name="Recommend" value="Y">
            </div>
            <div class="operation-buttons center">
                 <button type="submit" class="button">Update Book</button>
            </div>
        </div>
        </form>
    </div>

<?php
}
db_disconnect($db);
?>
    <?php include 'footerEm.php';?>
</body>
</html>