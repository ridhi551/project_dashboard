<?php

// Function to read CSV file and return data as an associative array
function readCSV($csvFile) {
    $csvData = [];
    
    if (($handle = fopen($csvFile, "r")) !== FALSE) {
        $keys = fgetcsv($handle); // Get the column headers as keys
        while (($data = fgetcsv($handle)) !== FALSE) {
            $rowData = [];
            foreach ($keys as $index => $key) {
                $rowData[$key] = $data[$index]; // Map values to keys
            }
            $csvData[] = $rowData;
        }
        fclose($handle);
    }
    
    return $csvData;
}

// Database connection parameters
$servername = "localhost";
$username = "root"; // Replace with your MySQL username
$password = ""; // Replace with your MySQL password
$database = "final_year_project"; // Replace with your MySQL database name
$tableName = "employeeinfo"; // Replace with your MySQL table name

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to insert data into MySQL table
function insertData($conn, $tableName, $data) {
    // Prepare SQL statement
    $sql = "INSERT INTO $tableName (emp_id, firstname, lastname, email, reg_date) VALUES (?, ?, ?, ?, ?)";

    // Prepare and bind parameters
    $stmt = $conn->prepare($sql);

    // Insert data into table
    foreach ($data as $row) {
        $id = $row['emp_id'];
        $first_name = $row['firstname'];
        $last_name = $row['lastname'];
        $email = $row['email'];
        $joining_date = $row['reg_date'];
        
        $stmt->bind_param("issss", $id, $first_name, $last_name, $email, $joining_date);
        $stmt->execute();
    }

    // Close statement
    $stmt->close();
}

// Check if the form was submitted
if (isset($_POST['submit'])) {
    // Check if file was uploaded without errors
    if (isset($_FILES['csvFile']) && $_FILES['csvFile']['error'] == 0) {
        $csvFile = $_FILES['csvFile']['tmp_name'];
        
        // Read data from CSV file
        $data = readCSV($csvFile);
        
        // Insert data into MySQL table
        insertData($conn, $tableName, $data);
        
        echo "Data inserted successfully";
    } else {
        echo "Error uploading file";
    }
}

// Close connection
$conn->close();

?>
