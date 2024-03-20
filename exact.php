<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload CSV or Excel File</title>
</head>
<body>
    <h2>Upload CSV or Excel File</h2>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
        <label for="file">Select file:</label>
        <input type="file" name="file" id="file" accept=".csv,.xlsx">
        <br><br>
        <label for="tableName">Enter table name:</label>
        <input type="text" name="tableName" id="tableName">
        <br><br>
        <button type="submit" name="submit">Upload</button>
    </form>

    <?php
    use PhpOffice\PhpSpreadsheet\IOFactory;
    require 'vendor/autoload.php';

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
        // Check if file was uploaded without errors
        if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
            // Get the uploaded file
            $fileType = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));

            // Check file type
            if ($fileType === 'csv' || $fileType === 'xlsx') {
                $filePath = $_FILES['file']['tmp_name'];

                // Get table name from user input
                $tableName = $_POST['tableName'];

                $servername = "localhost";
                $username = "root"; 
                $password = ""; 
                $database = "final_year_project"; 

                $conn = new mysqli($servername, $username, $password, $database);

                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                // Read data from CSV or Excel file and extract column names
                $spreadsheet = IOFactory::load($filePath);
                $sheet = $spreadsheet->getActiveSheet();
                $keys = $sheet->getRowIterator(1)->current()->toArray(null, false, true, false);

                /**
                 * The toArray method (PhpSpreadsheet) returns cell values for the whole worksheet in the form of a two-dimensional array of rows and columns. 
                 * The four parameters passed into this function are as follows...
                 * :Value returned in the array entry if a cell doesn't exist / empty cell is encountered.
                 * :Should formulas be calculated for each cell?
                 * :Should formatting be applied to cell values?
                 * :Should the array be indexed by actual row and column IDs (true) or by numbers counting from zero.
                */

                // Create table with dynamic column names
                $sql = "CREATE TABLE IF NOT EXISTS $tableName (";
                foreach ($keys as $columnName => $value) {
                    $sql .= "`$columnName` VARCHAR(255), ";
                }
                $sql = rtrim($sql, ", ") . ")";
                
                if ($conn->query($sql) === TRUE) {
                    echo "Table '$tableName' created successfully<br>";

                    // Insert data into MySQL table
                    $stmt = $conn->prepare("INSERT INTO $tableName (" . implode(",", array_keys($keys)) . ") VALUES (" . rtrim(str_repeat("?,", count($keys)), ",") . ")");

                    foreach ($sheet->getRowIterator(2) as $row) {
                        $data = [];
                        foreach ($row->getCellIterator() as $cell) {
                            $data[] = $cell->getValue();
                        }
                        $stmt->bind_param(str_repeat("s", count($data)), ...$data);
                        $stmt->execute();
                    }

                    echo "Data inserted successfully";

                    // Close statement
                    $stmt->close();
                } else {
                    echo "Error creating table: " . $conn->error;
                }

                // Close connection
                $conn->close();
            } else {
                echo "Error: Only CSV or Excel files are allowed.";
            }
        } else {
            echo "Error uploading file";
        }
    }
    ?>
</body>
</html>
