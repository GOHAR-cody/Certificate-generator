<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_FILES["csvFile"])) {
        if ($_FILES["csvFile"]["error"] !== UPLOAD_ERR_OK) {
            $_SESSION['messagecsv'] = 'Please Select a CSV First!';
            header("Location: dashboard.php");
            exit();
        }
        
        $csvFilePath = $_FILES["csvFile"]["tmp_name"];
        $csvData = array_map('str_getcsv', file($csvFilePath));

      
        array_shift($csvData);

        $deleteQuery = "DELETE FROM csv_data";
        if (!mysqli_query($conn, $deleteQuery)) {
            $_SESSION['messagecsv'] = "Internal Server Error";
            header("Location: dashboard.php");
            exit();
        }
        
        $stmt = mysqli_prepare($conn, "INSERT INTO csv_data (name, email, phone) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "sss", $name, $email, $phone);
        
        foreach ($csvData as $row) {
            $name = $row[0];
            $email = $row[1];
            $phone = $row[2];
            mysqli_stmt_execute($stmt);
        }
        
        mysqli_stmt_close($stmt);
        $_SESSION['csvData'] = $csvData;
        $_SESSION['csvnumbers'] = count($csvData);
        $_SESSION['messagecsv'] = 'CSV Uploaded Successfully';
        header("Location: dashboard.php");
        exit();
    } else {
        $_SESSION['messagecsv'] = 'Please Upload CSV File';
        header("Location: dashboard.php");
        exit();
    }
}

mysqli_close($conn);
?>
