<?php
session_start();

// Only display this page after a successful application
if (empty($_SESSION['application_success'])) {
    header("Location: ../HTML/apprenticeship.html");
    exit;
}

// Clear the success flag to prevent repeated access
unset($_SESSION['application_success']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Application Successful | MasterTee Tech</title>

    <!-- Bootstrap CSS -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <!-- Bootstrap Icons -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
        rel="stylesheet"
    >

    <style>
        body {
            background: linear-gradient(135deg, #eaf3ff, #ffffff);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', sans-serif;
        }

        .success-card {
            background: #ffffff;
            border-radius: 20px;
            padding: 50px 35px;
            max-width: 550px;
            width: 100%;
            text-align: center;
            box-shadow: 0 15px 45px rgba(0, 50, 120, 0.12);
            animation: fadeUp 0.7s ease;
        }

        .success-icon {
            width: 100px;
            height: 100px;
            background: #e6f9ee;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
        }

        .success-icon i {
            font-size: 55px;
            color: #198754;
        }

        .success-title {
            font-weight: 700;
            color: #003b80;
            margin-bottom: 15px;
        }

        .success-text {
            color: #666;
            font-size: 16px;
            line-height: 1.8;
        }

        .brand-name {
            color: #0056b3;
            font-weight: 700;
        }

        .btn-home {
            background: #0056b3;
            color: white;
            border-radius: 10px;
            padding: 12px 25px;
            font-weight: 600;
            border: none;
        }

        .btn-home:hover {
            background: #003d82;
            color: white;
        }

        .btn-apply {
            border: 2px solid #0056b3;
            color: #0056b3;
            border-radius: 10px;
            padding: 10px 25px;
            font-weight: 600;
        }

        .btn-apply:hover {
            background: #0056b3;
            color: white;
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(25px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 576px) {
            .success-card {
                padding: 35px 20px;
            }

            .success-title {
                font-size: 24px;
            }
        }
    </style>
</head>

<body>

    <div class="container">

        <div class="success-card mx-auto">

            <!-- Success Icon -->
            <div class="success-icon">
                <i class="bi bi-check-circle-fill"></i>
            </div>

            <!-- Heading -->
            <h2 class="success-title">
                Application Submitted!
            </h2>

            <!-- Message -->
            <p class="success-text">
                Congratulations! Your apprenticeship application
                has been successfully submitted to
                <span class="brand-name">
                    MasterTee Tech Communication.
                </span>
            </p>

            <p class="success-text">
                Our team will review your application and
                contact you using the information you provided.
            </p>

            <!-- Bootstrap Alert -->
            <div class="alert alert-success mt-4" role="alert">
                <i class="bi bi-shield-check me-2"></i>
                Your application has been received successfully.
            </div>

            <!-- Buttons -->
            <div class="d-flex flex-column flex-sm-row
                        justify-content-center gap-3 mt-4">

                <a href="../index.html"
                   class="btn btn-home">
                    <i class="bi bi-house-door me-2"></i>
                    Back to Home
                </a>

                <a href="../pages/apprenticeship.html"
                   class="btn btn-apply">
                    <i class="bi bi-arrow-repeat me-2"></i>
                    Apply Again
                </a>

            </div>

            <hr class="my-4">

            <p class="text-muted small mb-0">
                &copy; <?php echo date("Y"); ?>
                MasterTee Tech Communication.
                All rights reserved.
            </p>

        </div>

    </div>

</body>
</html>