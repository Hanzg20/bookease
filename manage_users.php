<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>manage User Information</title>
    <link rel="stylesheet" type="text/css" href="./css/style.css" />
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

// Initialize variables
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $userType = $_POST['userType'];

    if ($action === 'add') {
        $stmt = $conn->prepare("INSERT INTO Users (UserName, Email, Password, UserType) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $email, $password, $userType);
        $stmt->execute();
    } elseif ($action === 'update') {
        $userID = $_POST['userID'];
        $stmt = $conn->prepare("UPDATE Users SET UserName = ?, Email = ?, Password = ?, UserType = ? WHERE UserID = ?");
        $stmt->bind_param("ssssi", $username, $email, $password, $userType, $userID);
        $stmt->execute();
    } elseif ($action === 'delete') {
        $userID = $_POST['userID'];
        $stmt = $conn->prepare("DELETE FROM Users WHERE UserID = ?");
        $stmt->bind_param("i", $userID);
        $stmt->execute();
    }
    header('Location: user_management.php');
    exit;
}

$users = $conn->query("SELECT * FROM Users")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Management</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <script>
        function loadUsers() {
            fetch('user_management.php')
                .then(response => response.json())
                .then(data => {
                    const userTableBody = document.getElementById('userTableBody');
                    userTableBody.innerHTML = '';
                    data.users.forEach(user => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${user.UserName}</td>
                            <td>${user.Email}</td>
                            <td>${user.UserType}</td>
                            <td>
                                <button onclick="editUser(${user.UserID})">Edit</button>
                                <button onclick="deleteUser(${user.UserID})">Delete</button>
                            </td>
                        `;
                        userTableBody.appendChild(row);
                    });
                });
        }

        document.addEventListener('DOMContentLoaded', function() {
            const userForm = document.getElementById('addUpdateUserForm');
            userForm.addEventListener('submit', function(event) {
                event.preventDefault();
                const formData = new FormData(userForm);
                formData.append('action', 'add');

                fetch('user_management.php', {
                    method: 'POST',
                    body: formData
                }).then(response => response.json())
                  .then(data => {
                      loadUsers();
                  });
            });

            loadUsers();
        });

        function editUser(userID) {
            // Code for editing user
        }

        function deleteUser(userID) {
            const formData = new FormData();
            formData.append('action', 'delete');
            formData.append('userID', userID);

            fetch('user_management.php', {
                method: 'POST',
                body: formData
            }).then(response => response.json())
              .then(data => {
                  loadUsers();
              });
        }
    </script>
</head>
<body>
    <div class="container">
        <h2>User Management</h2>
        <div id="userForm">
            <form id="addUpdateUserForm">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required><br>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required><br>
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required><br>
                <label for="userType">User Type:</label>
                <select id="userType" name="userType">
                    <option value="Reader">Reader</option>
                    <option value="Admin">Admin</option>
                </select><br>
                <button type="submit">Save User</button>
            </form>
        </div>
        <div id="userList">
            <h3>Users</h3>
            <table>
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Email</th>
                        <th>User Type</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="userTableBody">
                    <!-- Users will be loaded here dynamically -->
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
