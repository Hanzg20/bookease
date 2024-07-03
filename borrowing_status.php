<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>View borrowing status </title>
    <link rel="stylesheet" type="text/css" href="css/style.css" />
    <script src="js/script.js" defer></script>
</head>
<body>
<?php 
/**
 * Author: Zhaoguo Han
 * Date: June 29 2024
 * Version: 1.0
 * Description
 */
include "headerEm.php";
require_once 'php/database.php';
$conn = db_connect();

/*require 'db_connection.php';*/

$borrowingStatus = $conn->query("
    SELECT Books.Title, Users.UserName, user_Borrows.BorrowDate, user_Borrows.DueDate,
           CASE
               WHEN user_Borrows.DueDate < CURDATE() THEN 'Overdue'
               ELSE 'Borrowed'
           END AS Status
    FROM user_Borrows
    JOIN Books ON user_Borrows.book_id = Books.BookID
    JOIN Users ON user_Borrows.user_id = Users.UserID
")->fetch_all(MYSQLI_ASSOC);

echo json_encode(['borrowingStatus' => $borrowingStatus]);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Borrowing Status</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function loadBorrowingStatus() {
                fetch('borrowing_status.php')
                    .then(response => response.json())
                    .then(data => {
                        const borrowingStatusTableBody = document.getElementById('borrowingStatusTableBody');
                        borrowingStatusTableBody.innerHTML = '';
                        data.borrowingStatus.forEach(status => {
                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td>${status.Title}</td>
                                <td>${status.UserName}</td>
                                <td>${status.BorrowDate}</td>
                                <td>${status.DueDate}</td>
                                <td>${status.Status}</td>
                            `;
                            borrowingStatusTableBody.appendChild(row);
                        });
                    });
            }

            loadBorrowingStatus();
        });
    </script>
</head>
<body>
    <div class="container">
        <h2>Borrowing Status</h2>
        <div id="borrowingStatusList">
            <table>
                <thead>
                    <tr>
                        <th>Book Title</th>
                        <th>Borrower</th>
                        <th>Borrow Date</th>
                        <th>Due Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="borrowingStatusTableBody">
                    <!-- Borrowing status will be loaded here dynamically -->
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
