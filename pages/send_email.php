<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dompdf\Dompdf;

require_once __DIR__ . '/../vendor/autoload.php';

$response = array();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['email']) && isset($_POST['name']) && isset($_POST['certificate'])) {
        $email = $_POST['email'];
        $name = $_POST['name'];
        $certificate = $_POST['certificate'];

        $domain = substr(strrchr($email, "@"), 1);

       
        function isValidDomain($domain) {
            $mx_records = dns_get_record($domain, DNS_MX);
            if (!empty($mx_records)) {
                return true; 
            } else {
                return false; 
            }
        }

       
        if (!isValidDomain($domain)) {
            
            $_SESSION['email_status'][$email] = 'Failure';
            $response['status'] = 'failure';
            $response['message'] = 'Invalid email domain.';
            $response['error_info'] = 'The domain ' . $domain . ' does not have valid MX records.';
            
        } else {
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'goharfatimaali7@gmail.com'; //change email
                $mail->Password   = 'xxxx xxxx xxxx xxxx';      //change password
                $mail->SMTPSecure = 'tls';
                $mail->Port       = 587;

                $mail->setFrom('goharfatimaali7@gmail.com', 'Your Name');
                $mail->addAddress($email, $name);

                $dompdf = new Dompdf();
                $dompdf->loadHtml($certificate);
                $dompdf->render();
                $pdfContent = $dompdf->output();
                $mail->addStringAttachment($pdfContent, 'certificate.pdf', 'base64', 'application/pdf');

                $mail->isHTML(true);
                $mail->Subject = 'Your Certificate';
                $mail->Body    = 'Dear ' . $name . ',<br><br> Please find your certificate attached.<br><br>Best regards,';

                if ($mail->send()) {
                    $_SESSION['email_status'][$email] = 'Success';
                    $response['status'] = 'success';
                    $response['message'] = 'Email sent successfully!';
                } else {
                    $_SESSION['email_status'][$email] = 'Error';
                    $response['status'] = 'error';
                    $response['message'] = 'Email could not be sent.';
                    $response['error_info'] = $mail->ErrorInfo;
                }
            } catch (Exception $e) {
                error_log("Email sending failed: " . $e->getMessage());
                $_SESSION['email_status'][$email] = 'Error';
                $response['status'] = 'error';
                $response['message'] = 'Email could not be sent.';
                $response['error_info'] = $e->getMessage();
            }
        }
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Invalid input.';
    }
} else {
    $response['status'] = 'error';
    $response['message'] = 'Invalid request method.';
}



header('Content-Type: application/json');
echo json_encode($response);
?>
