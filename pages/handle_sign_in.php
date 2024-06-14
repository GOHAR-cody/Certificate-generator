<?php 
session_start();
if (isset($_POST['submit'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message= 'Invalid email format';
        $_SESSION['message']= $message;
        header("Location: sign-in.php");
        exit;
    }

    
    $validEmail = 'goharfatimaali7@gmail.com'; //change email
    $validPassword = 'gohar';       //change password

   
    if ($email === $validEmail && $password === $validPassword) {
        session_start();
        $_SESSION['login'] = true;
        header("Location: dashboard.php");
        exit;
    } else {
        $message= 'Invalid credentials';
        $_SESSION['message']= $message;
        header("Location: sign-in.php");
        exit;
    }
}
?>
