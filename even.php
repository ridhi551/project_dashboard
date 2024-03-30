<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Even Semester Courses</title>
<style>
    table {
        border-collapse: collapse;
        width: 50%;
        margin: auto;
    }
    th, td {
        border: 1px solid #dddddd;
        text-align: left;
        padding: 8px;
    }
    th {
        background-color: #f2f2f2;
    }
</style>
</head>
<body>

<?php
// Database connection
$servername = "localhost";
$username = "root"; // Replace with your MySQL username
$password = ""; // Replace with your MySQL password
$dbname = "final_year_project";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to retrieve course_code and course_title from the first_sem table for even semesters (multiples of 2)
$sql = "SELECT course_code, course_title FROM first_sem WHERE semester % 2 = 0";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Output data of each row in a table
    echo "<table>";
    echo "<tr><th>Course Code</th><th>Course Title</th></tr>";
    while($row = $result->fetch_assoc()) {
        echo "<tr><td>".$row["course_code"]."</td><td>".$row["course_title"]."</td></tr>";
    }
    echo "</table>";
} else {
    echo "No courses found for even semesters.";
}

// Close connection
$conn->close();
?>

</body>
</html>
