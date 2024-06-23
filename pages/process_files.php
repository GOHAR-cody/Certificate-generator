<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="../assets/img/favicon.jpg">
    <title>Certificates</title>
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
    <link href="../assets/css/nucleo-icons.css" rel="stylesheet" />
    <link href="../assets/css/nucleo-svg.css" rel="stylesheet" />
    <link href="../assets/css/custom.css" rel="stylesheet" />
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    <link id="pagestyle" href="../assets/css/soft-ui-dashboard.css?v=1.0.7" rel="stylesheet" />
    <script defer data-site="YOUR_DOMAIN_HERE" src="https://api.nepcha.com/js/nepcha-analytics.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    <style>
        .loading-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            z-index: 9999;
            justify-content: center;
            align-items: center;
        }
        .spinner-border {
            width: 3rem;
            height: 3rem;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg blur blur-rounded top-0 z-index-3 shadow position-relative my-3 py-2 start-0 end-0 mx-4">
        <div class="container-fluid pe-0">
            <a class="navbar-brand font-weight-bolder ms-lg-0 ms-3 " href="../pages/dashboard.php">
                 Certificates
            </a>
            <button class="navbar-toggler shadow-none ms-2" type="button" data-bs-toggle="collapse" data-bs-target="#navigation" aria-controls="navigation" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon mt-2">
                    <span class="navbar-toggler-bar bar1"></span>
                    <span class="navbar-toggler-bar bar2"></span>
                    <span class="navbar-toggler-bar bar3"></span>
                </span>
            </button>
            <div class="collapse navbar-collapse" id="navigation"></div>
        </div>
    </nav>

    <div class="container mt-5"></div>

    <div class="loading-overlay">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <div class="container">
        <?php
        include 'db.php';
        

        if (isset($_SESSION['htmlContent'])) {
            $htmlContent = $_SESSION['htmlContent'];
            if (!empty($htmlContent)) {
                $sql = "SELECT name, email, phone FROM csv_data";
                $result = mysqli_query($conn, $sql);

                if (mysqli_num_rows($result) > 0) {
                    echo '<table class="table table-striped">';
                    echo '<thead>';
                    echo '<tr>';
                    echo '<th>Name</th>';
                    echo '<th>Email</th>';
                    echo '<th>Phone</th>';
                    echo '<th>Status</th>';
                    echo '<th>Action</th>';
                    echo '</tr>';
                    echo '</thead>';
                    echo '<tbody>';

                    while ($row = mysqli_fetch_assoc($result)) {
                        $status = getStatus($row['email']); // Function to get email status
                        $personalizedHtmlContent = personalizeHtmlContent($htmlContent, $row);
                        
                        // Debugging output
                        echo '<!-- Debug: Personalized HTML content: ' . htmlspecialchars($personalizedHtmlContent) . ' -->';

                        echo '<tr>';
                        echo '<td>' . $row['name'] . '</td>';
                        echo '<td>' . $row['email'] . '</td>';
                        echo '<td>' . $row['phone'] . '</td>';
                        echo '<td id="status_' . $row['email'] . '">' . $status . '</td>';
                        echo '<td><form class="emailForm" method="post">';
                        echo '<input type="hidden" name="email" value="' . $row['email'] . '">';
                        echo '<input type="hidden" name="name" value="' . $row['name'] . '">';
                        echo '<input type="hidden" name="certificate" value="' . htmlspecialchars($personalizedHtmlContent) . '">';
                        echo '<button type="button" class="btn bg-gradient-sign-in shadow" onclick="sendEmail(this)">Mail</button>';
                        echo '</form></td>';
                        echo '</tr>';
                    }

                    echo '</tbody>';
                    echo '</table>';
                } else {
                    $_SESSION['messageprocess'] = "Please Upload CSV!";
                    header("Location: dashboard.php");
                    exit();
                }
            } else {
                $_SESSION['messageprocess'] = "No Html Uploaded!";
                header("Location: dashboard.php");
                exit();
            }
        } else {
            $_SESSION['messageprocess'] = "HTML content or CSV data is not Uploaded!";
            header("Location: dashboard.php");
            exit();
        }

        mysqli_close($conn);

        function getStatus($email) {
            if (isset($_SESSION['email_status'][$email])) {
                return $_SESSION['email_status'][$email];
            } else {
                return "Pending";
            }
        }

        function personalizeHtmlContent($htmlContent, $row) {
            foreach ($row as $key => $value) {
                $htmlContent = str_replace("{{" . $key . "}}", $value, $htmlContent);
            }
            return $htmlContent;
        }
        ?>
    </div>

    <script>
        function sendEmail(button) {
            var formData = new FormData(button.closest('form'));
            var email = formData.get('email');
            var statusCell = document.getElementById('status_' + email);
            statusCell.textContent = 'Sending...';

            $.ajax({
                url: 'send_email.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.status === 'success') {
                        statusCell.textContent = 'Success';
                    } else {
                        statusCell.textContent = 'Error';
                        console.error(response.message);
                    }
                },
                error: function() {
                    statusCell.textContent = 'Error';
                }
            });
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
</body>
</html>
