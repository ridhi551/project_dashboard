<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload CSV File</title>
</head>
<body>
    <h2>Upload CSV File</h2>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
        <input type="file" name="csvFile" accept=".csv">
        <button type="submit" name="submit">Upload</button>
    </form>

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
        print_r($csvData);
        return $csvData;
    }

   
    $servername = "localhost";
    $username = "root"; 
    $password = ""; 
    $database = "final_year_project"; 
    $tableName = "employeeinfo"; 

    
    if (isset($_POST['submit'])) {
        // Check if file was uploaded without errors
        if (isset($_FILES['csvFile']) && $_FILES['csvFile']['error'] == 0) {
            $csvFile = $_FILES['csvFile']['tmp_name'];
            
            // Create connection
            $conn = new mysqli($servername, $username, $password, $database);

            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Prepare SQL statement
            $sql = "INSERT INTO $tableName (`emp_id`, `firstname`, `lastname`, `email`, `reg_date`) VALUES (?, ?, ?, ?, ?)";

            // Prepare and bind parameters
            $stmt = $conn->prepare($sql);

            // Read data from CSV file
            $data = readCSV($csvFile);
            
            // Insert data into MySQL table
            foreach ($data as $row) {
                $emp_id = $row['emp_id'];
                $firstname = $row['firstname'];
                $lastname = $row['lastname'];
                $email = $row['email'];
                $reg_date = $row['reg_date'];
                
                $stmt->bind_param("issss", $emp_id, $firstname, $lastname, $email, $reg_date);
                $stmt->execute();
            }

            echo "Data inserted successfully";

            // Close statement and connection
            $stmt->close();
            $conn->close();
        } else {
            echo "Error uploading file";
        }
    }
    ?>
</body>
</html>
