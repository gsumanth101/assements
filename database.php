<?php
// File: database.php
define('DB_HOST', 'localhost');
define('DB_USER', 'user');
define('DB_PASS', '#*Eyebook@123*#');
// define('DB_USER', 'root');
// define('DB_PASS', '');

define('DB_NAME', 'eyebook');
define('GEMINI_API_KEY', 'AIzaSyCv6jdoUC3YslqkNj42YNZEhjtWmbBkYEM');

// Create a connection without selecting a database
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create the database if it doesn't exist
$conn->query("CREATE DATABASE IF NOT EXISTS " . DB_NAME);

// Select the database
$conn->select_db(DB_NAME);

// Create tables if they don't exist
$conn->query("CREATE TABLE IF NOT EXISTS assessments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    deadline DATETIME NOT NULL
)");

$conn->query("CREATE TABLE IF NOT EXISTS questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    assessment_id INT,
    question_text TEXT NOT NULL,
    options JSON NOT NULL,
    correct_answer VARCHAR(255) NOT NULL,
    marks INT NOT NULL,
    FOREIGN KEY (assessment_id) REFERENCES assessments(id)
)");

$conn->query("CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE
)");

$conn->query("CREATE TABLE IF NOT EXISTS assessment_results (
    id INT AUTO_INCREMENT PRIMARY KEY,
    assessment_id INT,
    student_id INT,
    score INT NOT NULL,
    FOREIGN KEY (assessment_id) REFERENCES assessments(id),
    FOREIGN KEY (student_id) REFERENCES students(id)
)");
