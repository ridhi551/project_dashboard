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
        <label for="csvFile">Select CSV file:</label>
        <input type="file" name="csvFile" id="csvFile" accept=".csv">
        <br><br>
        <label for="tableName">Enter table name:</label>
        <input type="text" name="tableName" id="tableName">
        <br><br>
        <button type="submit" name="submit">Upload</button>
    </form>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
        // Check if file was uploaded without errors
        if (isset($_FILES['csvFile']) && $_FILES['csvFile']['error'] == 0) {
            // Get the uploaded CSV file
            $csvFile = $_FILES['csvFile']['tmp_name'];

            // Read data from CSV file and extract column names
            if (($handle = fopen($csvFile, "r")) !== FALSE) {
                $keys = fgetcsv($handle);
                fclose($handle);
            }

            
            $tableName = $_POST['tableName'];

            
            $servername = "localhost";
            $username = "root"; 
            $password = ""; 
            $database = "final_year_project"; 

            
            $conn = new mysqli($servername, $username, $password, $database);

            
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Create table with dynamic column names
            $sql = "CREATE TABLE IF NOT EXISTS $tableName (";
            foreach ($keys as $key) {
                $sql .= "$key VARCHAR(255), ";
            }
            $sql = rtrim($sql, ", ") . ")";
            
            if ($conn->query($sql) === TRUE) {
                echo "Table '$tableName' created successfully<br>";

                
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

                // Function to insert data into MySQL table
                function insertData($conn, $tableName, $data) {
                    // Prepare SQL statement
                    $sql = "INSERT INTO $tableName (".implode(",", array_keys($data[0])).") VALUES (".str_repeat("?,", count($data[0])-1)."?)";

                    // Prepare and bind parameters
                    $stmt = $conn->prepare($sql);
                    if (!$stmt) {
                        die("Error: " . $conn->error); // Add error handling for SQL query preparation
                    }

                    // Insert data into table
                    foreach ($data as $row) {
                        $stmt->bind_param(str_repeat("s", count($row)), ...array_values($row));
                        $stmt->execute();
                    }

                    echo "Data inserted successfully";
                    // Close statement
                    $stmt->close();
                }

                // Read data from CSV file
                $data = readCSV($csvFile);

                // Insert data into MySQL table
                insertData($conn, $tableName, $data);

            } else {
                echo "Error creating table: " . $conn->error;
            }

            // Close connection
            $conn->close();
        } else {
            echo "Error uploading file";
        }
    }
    ?>
</body>
</html>
