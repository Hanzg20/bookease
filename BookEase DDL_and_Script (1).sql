-- Create the 'bookease' database and set up user privileges
CREATE DATABASE IF NOT EXISTS bookease;
GRANT USAGE ON *.* TO 'book'@'localhost' IDENTIFIED BY 'password';
GRANT ALL PRIVILEGES ON bookease.* TO 'book'@'localhost';
FLUSH PRIVILEGES;

USE bookease;

-- Drop existing tables if they exist
DROP TABLE IF EXISTS user_collections;
DROP TABLE IF EXISTS user_borrows;
DROP TABLE IF EXISTS Users;
DROP TABLE IF EXISTS Genres;
DROP TABLE IF EXISTS Books;

-- Create the Users table with an additional field for user type (Reader/Admin)
CREATE TABLE Users (
    UserID INT AUTO_INCREMENT PRIMARY KEY,
    UserName VARCHAR(60) NOT NULL,
    Email VARCHAR(100) NOT NULL,
    phonenumber varchar(20) NOT NULL,
    Password VARCHAR(60) NOT NULL,
    UserType ENUM('Reader', 'Admin') DEFAULT 'Reader' -- Adding user type to differentiate between readers and admins
);

-- Create the Genres table
CREATE TABLE Genres (
    GenreID INT AUTO_INCREMENT PRIMARY KEY,
    GenreName VARCHAR(60) NOT NULL
);

-- Create the Books table with additional fields for borrow status and due date
CREATE TABLE Books (
    BookID INT AUTO_INCREMENT PRIMARY KEY,
    Title VARCHAR(100) NOT NULL,
    Cover BLOB,
    Author VARCHAR(50),
    Genre VARCHAR(50),
    Rating INT,
    Review TEXT,
    Recommend CHAR(1), -- 'Y' or 'N'
    ISBN VARCHAR(20), -- ISBN number
    Publisher VARCHAR(100), -- Publisher name
    BorrowStatus ENUM('Available', 'Borrowed') DEFAULT 'Available' -- Status to indicate if the book is borrowed
);

-- Create the user_collections table to manage book-user relationships
CREATE TABLE user_collections (
    user_id INT,
    book_id INT,
    PRIMARY KEY (user_id, book_id),
    FOREIGN KEY (user_id) REFERENCES Users(UserID),
    FOREIGN KEY (book_id) REFERENCES Books(BookID)
) ENGINE=InnoDB; -- InnoDB engine for foreign key support

-- Create the user_borrows table to manage book borrowing records
CREATE TABLE user_borrows (
    user_id INT,
    book_id INT,
    BorrowDate DATE, -- Date when the book was borrowed
    ReturnDate DATE, -- Date when the book was returned
    DueDate DATE, -- Date by which the book should be returned
    PRIMARY KEY (user_id, book_id, BorrowDate),
    FOREIGN KEY (user_id) REFERENCES Users(UserID),
    FOREIGN KEY (book_id) REFERENCES Books(BookID)
) ENGINE=InnoDB; -- InnoDB engine for foreign key support

-- Populate the Genres table with initial data
INSERT INTO Genres (GenreName) VALUES
('Fantasy'),
('Science Fiction'),
('Mystery'),
('Horror'),
('Classic Fiction'),
('Thriller'),
('Contemporary'),
('Romance'),
('Literary Fiction');

-- Populate the Books table with initial data
INSERT INTO Books (Title, Author, Genre, Rating, Review, ISBN, Publisher) VALUES
('Harry Potter and the Sorcerer''s Stone', 'J.K. Rowling', 'Fantasy', 4, 'Excellent book!', '978-0439708180', 'Scholastic'),
('1984', 'George Orwell', 'Science Fiction', 5, 'Dystopian classic', '978-0451524935', 'Signet Classics'),
('The Murder of Roger Ackroyd', 'Agatha Christie', 'Mystery', 3, 'Intriguing mystery', '978-0062073563', 'William Morrow Paperbacks'),
('The Shining', 'Stephen King', 'Horror', 2, 'Classic horror', '978-0307743657', 'Anchor'),
('To Kill a Mockingbird', 'Harper Lee', 'Classic Fiction', 4, 'Powerful and moving', '978-0061120084', 'Harper Perennial Modern Classics'),
('The Hobbit', 'J.R.R. Tolkien', 'Fantasy', 1, 'Epic fantasy adventure', '978-0345339683', 'Del Rey');

-- Populate the Users table with initial data
INSERT INTO Users (UserName, Email, Password, UserType) VALUES
('User1', 'user1@example.com', '81dc9bdb52d04dc20036dbd8313ed055', 'Reader'), -- Passwords are MD5 hashed for demonstration
('User2', 'user2@example.com', '81dc9bdb52d04dc20036dbd8313ed055', 'Reader'),
('User3', 'user3@example.com', '81dc9bdb52d04dc20036dbd8313ed055', 'Reader'),
('User4', 'user4@example.com', '81dc9bdb52d04dc20036dbd8313ed055', 'Reader'),
('Admin1', 'admin1@example.com', '81dc9bdb52d04dc20036dbd8313ed055', 'Admin');

-- Update Books table for borrowed books status
UPDATE Books SET BorrowStatus = 'Available';

-- Book Search Function
DELIMITER //
CREATE PROCEDURE SearchBooks(IN searchType VARCHAR(50), IN searchQuery VARCHAR(100))
BEGIN
    SET @sql = CONCAT('SELECT * FROM Books WHERE ', searchType, ' LIKE ''%', searchQuery, '%''');
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
END //
DELIMITER ;

-- Book Collection Function
DELIMITER //
CREATE PROCEDURE CollectBook(IN userID INT, IN bookID INT)
BEGIN
    DECLARE collectionCount INT;
    
    SELECT COUNT(*) INTO collectionCount FROM user_collections WHERE user_id = userID AND book_id = bookID;

    IF collectionCount = 0 THEN
        INSERT INTO user_collections (user_id, book_id) VALUES (userID, bookID);
        SELECT 'Collection successful.';
    ELSE
        SELECT 'This book is already in your collection.';
    END IF;
END //
DELIMITER ;

-- Book Borrowing Function
DELIMITER //
CREATE PROCEDURE BorrowBook(IN userID INT, IN bookID INT)
BEGIN
    DECLARE bookStatus ENUM('Available', 'Borrowed');
    DECLARE dueDate DATE;

    -- Check if the book is available
    SELECT BorrowStatus INTO bookStatus FROM Books WHERE BookID = bookID;

    IF bookStatus = 'Available' THEN
        -- Update the book's status to 'Borrowed' and set the due date
        SET dueDate = DATE_ADD(CURDATE(), INTERVAL 14 DAY); -- 2 weeks from today
        UPDATE Books SET BorrowStatus = 'Borrowed', DueDate = dueDate WHERE BookID = bookID;

        -- Insert the record into user_borrows
        INSERT INTO user_borrows (user_id, book_id, BorrowDate, DueDate) VALUES (userID, bookID, CURDATE(), dueDate);

        -- Return success message
        SELECT 'Borrowing is successful. Due date: ', dueDate;
    ELSE
        -- Return failure message
        SELECT 'The book is already borrowed.';
    END IF;
END //
DELIMITER ;

-- Book Returning Function
DELIMITER //
CREATE PROCEDURE ReturnBook(IN userID INT, IN bookID INT)
BEGIN
    DECLARE borrowDate DATE;
    
    -- Get the borrow date
    SELECT BorrowDate INTO borrowDate FROM user_borrows WHERE user_id = userID AND book_id = bookID AND ReturnDate IS NULL;

    -- Update the book's status to 'Available'
    UPDATE Books SET BorrowStatus = 'Available', DueDate = NULL WHERE BookID = bookID;

    -- Update the record in user_borrows
    UPDATE user_borrows SET ReturnDate = CURDATE() WHERE user_id = userID AND book_id = bookID AND BorrowDate = borrowDate;

    -- Return success message
    SELECT 'Return is successful.';
END //
DELIMITER ;

-- Book Management Functions (Add, Modify, Delete Book)
DELIMITER //
CREATE PROCEDURE AddBook(IN title VARCHAR(100), IN author VARCHAR(50), IN genre VARCHAR(50), IN rating INT, IN review TEXT, IN isbn VARCHAR(20), IN publisher VARCHAR(100))
BEGIN
    INSERT INTO Books (Title, Author, Genre, Rating, Review, ISBN, Publisher) VALUES (title, author, genre, rating, review, isbn, publisher);
    SELECT 'Book added successfully.';
END //
DELIMITER ;

DELIMITER //
CREATE PROCEDURE ModifyBook(IN bookID INT, IN title VARCHAR(100), IN author VARCHAR(50), IN genre VARCHAR(50), IN rating INT, IN review TEXT, IN isbn VARCHAR(20), IN publisher VARCHAR(100))
BEGIN
    UPDATE Books SET Title = title, Author = author, Genre = genre, Rating = rating, Review = review, ISBN = isbn, Publisher = publisher WHERE BookID = bookID;
    SELECT 'Book updated successfully.';
END //
DELIMITER ;

DELIMITER //
CREATE PROCEDURE DeleteBook(IN bookID INT)
BEGIN
    DELETE FROM Books WHERE BookID = bookID;
    SELECT 'Book deleted successfully.';
END //
DELIMITER ;

-- Reader Management Functions (Add, Modify, Delete Reader)
DELIMITER //
CREATE PROCEDURE AddReader(IN userName VARCHAR(60), IN email VARCHAR(100), IN password VARCHAR(60))
BEGIN
    INSERT INTO Users (UserName, Email, Password, UserType) VALUES (userName, email, password, 'Reader');
    SELECT 'Reader added successfully.';
END //
DELIMITER ;

DELIMITER //
CREATE PROCEDURE ModifyReader(IN userID INT, IN userName VARCHAR(60), IN email VARCHAR(100), IN password VARCHAR(60))
BEGIN
    UPDATE Users SET UserName = userName, Email = email, Password = password WHERE UserID = userID AND UserType = 'Reader';
    SELECT 'Reader updated successfully.';
END //
DELIMITER ;

DELIMITER //
CREATE PROCEDURE DeleteReader(IN userID INT)
BEGIN
    DELETE FROM Users WHERE UserID = userID AND UserType = 'Reader';
    SELECT 'Reader deleted successfully.';
END //
DELIMITER ;
