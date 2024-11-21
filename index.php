<?php
// File: index.php
session_start();
require_once 'functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assessment System</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-4">
        <h1 class="text-3xl font-bold mb-6">Assessment System</h1>
        <div class="flex space-x-4">
            <a href="faculty.php" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Faculty</a>
            <a href="student.php" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Student</a>
        </div>
    </div>
</body>
</html>