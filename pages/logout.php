<?php
include 'db.php'; 

if (isset($_POST['submit'])) {
    session_start();
    
    
    $deleteQuery = "DELETE FROM csv_data";
    if (!mysqli_query($conn, $deleteQuery)) {
        echo "Error deleting data: " . mysqli_error($conn);
        exit();
    }

  
    mysqli_close($conn);

    
    session_unset(); 
    session_destroy(); 

    
    header("Location: sign-in.php"); 
    exit();
}
?>
