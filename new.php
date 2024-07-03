<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Add New Book</title>
    <link rel="stylesheet" type="text/css" href="css/style.css" />
    <script src="js/script.js" defer></script>
</head>

<body>
<?php
/**
 * Description:A form that allows users to add new  books to the database. 
 *             It usually contains input fields for various attributes of the item.
 * 
 */
include "headerEm.php";
require_once 'php/database.php';
$db = db_connect();
$genreOptions = '';
$genreSql = "SELECT GenreID, GenreName FROM Genres ORDER BY GenreName";
$genreResult = mysqli_query($db, $genreSql);

if ($genreResult && mysqli_num_rows($genreResult) > 0) {
    while ($row = mysqli_fetch_assoc($genreResult)) {
        $genreOptions .= "<option value='" . htmlspecialchars($row['GenreName']) . "'>" . htmlspecialchars($row['GenreName']) . "</option>";
    }
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = isset($_POST['title']) ? mysqli_real_escape_string($db, $_POST['title']) : '';
    $author = isset($_POST['author']) ? mysqli_real_escape_string($db, $_POST['author']) : '';
    $genreName = isset($_POST['genre']) ? mysqli_real_escape_string($db, $_POST['genre']) : '';
    $rating = isset($_POST['rating']) ? mysqli_real_escape_string($db, $_POST['rating']) : '';
    $review = isset($_POST['review']) ? mysqli_real_escape_string($db, $_POST['review']) : '';
    $recommend = isset($_POST['Recommend']) ? 'Y' : 'F';

    $cover = null;
    if (isset($_FILES['cover']) && $_FILES['cover']['error'] == 0) {
        $cover = file_get_contents($_FILES['cover']['tmp_name']);
    }

    // SQL to insert new book with prepared statement
    $stmt = $db->prepare("INSERT INTO books (Title, Cover, Author, Genre, Rating, Review) VALUES (?, ?, ?, ?, ?, ?)");
    // Bind parameters
    $stmt->bind_param("sbssss", $title, $cover, $author, $genreName, $rating, $review);

    // Bind the blob parameter (if mysqli)
    if ($cover !== null) {
        $stmt->send_long_data(1, $cover);
    }

    if ($stmt->execute()) {
        // Success handling
        echo "<p class='success-message'>New record created successfully.</p>";
    } else {
        // Error handling
        echo "<p class='success-message'>New record created. Error: " . $stmt->error . "</p>";
    }
}
?>
    <div class="container">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data"
            onsubmit="return validate(true);">
            <div id="content">
                <a class="back-link" href="home.php">&laquo; Back to the books list</a>
                <h4>ADDING BOOKS</h4>
                <div class="form-row">
                    <!-- First row -->
                    <label for="title">Title:</label>
                    <input type="text" name="title" id="title"><br>
                    <div id="titleError"></div>


                    <label for="cover">Cover Image:</label>
                    <input type="file" name="cover" id="cover" onchange="previewImage();"><br>
                    <img id="cover-preview"  src="#" alt="Book Cover" class="book-cover">

                    <label for="author">Author:</label>
                    <input type="text" name="author" id="author"><br>
                    <div id="authorError"></div>

                </div>

                <div class="form-row">
                    <!-- Second row -->
                    <label for="genre">Genre:</label>
                    <select name="genre" id="genre" required> <?php echo $genreOptions; ?> </select><br>
                    <div id="genreError"></div>

                    
                    <label for="rating">Rating:</label>
                    <input type="number" name="rating" id="rating" step="1"><br>
                    <div id="ratingError"></div>


                    <label for="review">Review:</label>
                    <textarea name="review" id="review" rows="10" cols="50" ></textarea><br>
                    <div id="reviewError"></div>

                </div>
                    <label>Recommend & Collect:</label>
                    <input type="checkbox" name="Recommend" value="Y">
                    
        
                <div class="operation-buttons center">
                    <button type="submit" class="button">Add Book</button>
                </div>
             </div>
       </form>
    </div>
<?php
db_disconnect($db);
include 'footerEm.php';
?>
</body>
</html>
