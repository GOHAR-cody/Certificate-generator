<?php
session_start();

if(isset($_POST['htmlContent'])) {
    $htmlContent = $_POST['htmlContent'];
    
    $_SESSION['htmlContent'] = $htmlContent;
    $message= 'Html Uploaded Sucessfully';
    $_SESSION['messagehtml'] = $message;
    header("Location: dashboard.php");
    exit();
} else {
    $message= 'Error Uploading Html';
    $_SESSION['messagehtml'] = $message;
    header("Location: dashboard.php");
    
}
?>
