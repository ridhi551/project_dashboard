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


$servername = "localhost";
$username = "root";
$password = ""; 
$database = "final_year_project"; 
$tableName = "employeeinfo"; 


$conn = new mysqli($servername, $username, $password, $database);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


function insertData($conn, $tableName, $data) {
    
    $sql = "INSERT INTO $tableName (`emp_id`, `firstname`, `lastname`, `email`, `reg_date`) VALUES (?, ?, ?, ?, ?)";

    // Prepare and bind parameters
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issss", $id, $first_name, $last_name, $email, $joining_date);

    // Insert data into table
    foreach ($data as $row) {
        $id = $row['emp_id'];
        $first_name = $row['firstname'];
        $last_name = $row['lastname'];
        $email = $row['email'];
        $joining_date = $row['reg_date'];
        
        $stmt->execute();
    }

    // Close statement
    $stmt->close();
}

// CSV file to read
$csvFile = 'dummy_gummy.csv'; 
$data = readCSV($csvFile);

// Insert data into MySQL table
insertData($conn, $tableName, $data);

echo "Data inserted successfully";

// Close connection
$conn->close();

?>
