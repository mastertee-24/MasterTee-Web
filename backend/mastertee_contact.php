<?php

require_once __DIR__ . "/db.php";
require_once __DIR__ ."/send_notification.php";

// Accept POST requests only
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    exit("Invalid request method.");
}

// Collect form data
$full_name = trim($_POST["full_name"] ?? "");
$phone_number = trim($_POST["phone_number"] ?? "");
$email = trim($_POST["email"] ?? "");
$subject = trim($_POST["subject"] ?? "");
$device_brand = trim($_POST["device_brand"] ?? "");
$device_model = trim($_POST["device_model"] ?? "");
$imei_serial = trim($_POST["imei_serial"] ?? "");
$message = trim($_POST["message"] ?? "");

// Validate required fields
if (
    $full_name === "" ||
    $phone_number === "" ||
    !filter_var($email, FILTER_VALIDATE_EMAIL) ||
    $subject === "" ||
    $device_brand === "" ||
    $device_model === "" ||
    $message === ""
) {
    http_response_code(400);
    exit("Please complete all required fields correctly.");
}

// Validate subject against allowed options
$allowed_subjects = [
    "Phone Repair",
    "Screen Replacement",
    "Battery Replacement",
    "Software Problem",
    "General Enquiry",
    "Other"
];

if (!in_array($subject, $allowed_subjects, true)) {
    http_response_code(400);
    exit("Please select a valid subject.");
}

// Check database column lengths
if (
    strlen($full_name) > 100 ||
    strlen($phone_number) > 20 ||
    strlen($email) > 250 ||
    strlen($device_brand) > 50 ||
    strlen($device_model) > 50 ||
    strlen($imei_serial) > 100
) {
    http_response_code(400);
    exit("One or more fields exceed the allowed length.");
}

// Process optional image
$phone_image = null;
$uploaded_file_path = null;

if (
    isset($_FILES["phone_image"]) &&
    $_FILES["phone_image"]["error"] !== UPLOAD_ERR_NO_FILE
) {

    $file = $_FILES["phone_image"];

    if ($file["error"] !== UPLOAD_ERR_OK) {
        http_response_code(400);
        exit("Image upload failed.");
    }

    // Maximum image size: 5MB
    if ($file["size"] > 5 * 1024 * 1024) {
        http_response_code(400);
        exit("Image must not exceed 5MB.");
    }

    // Check actual file type
    $finfo = new finfo(FILEINFO_MIME_TYPE);

    $mime = $finfo->file($file["tmp_name"]);

    $allowed_types = [
        "image/jpeg" => "jpg",
        "image/png" => "png",
        "image/webp" => "webp"
    ];

    if (!isset($allowed_types[$mime])) {
        http_response_code(400);
        exit("Only JPG, PNG and WebP images are allowed.");
    }

    // Store uploads outside the public website folder
    $upload_dir = dirname(__DIR__, 2)
                . "/mastertee_private_uploads/";

    if (!is_dir($upload_dir)) {
        if (!mkdir($upload_dir, 0700, true)) {
            http_response_code(500);
            exit("Unable to create upload directory.");
        }
    }

    $filename = bin2hex(random_bytes(16))
              . "." . $allowed_types[$mime];

    $destination = $upload_dir . $filename;

    if (!move_uploaded_file($file["tmp_name"], $destination)) {
        http_response_code(500);
        exit("Unable to save the uploaded image.");
    }

    $phone_image = $filename;
    $uploaded_file_path = $destination;
}

// Save information to MySQL
try {

    $sql = "INSERT INTO mastertee_contact
    (
        Full_name,
        Phone_number,
        Email,
        Subject,
        Device_brand,
        Device_model,
        imei_serial,
        Phone_image,
        message
    )
    VALUES
    (
        :full_name,
        :phone_number,
        :email,
        :subject,
        :device_brand,
        :device_model,
        :imei_serial,
        :phone_image,
        :message
    )";

    $stmt = $conn->prepare($sql);

   $stmt->execute([
    ":full_name" => $full_name,
    ":phone_number" => $phone_number,
    ":email" => $email,
    ":subject" => $subject,
    ":device_brand" => $device_brand,
    ":device_model" => $device_model,
    ":imei_serial" => $imei_serial,
    ":phone_image" => $phone_image,
    ":message" => $message
]);

// Get the ID of the newly saved customer submission
$submissionId = (int) $conn->lastInsertId();

// Send email notification after saving to MySQL
$emailSent = sendCustomerNotification(
    $submissionId,
    $full_name,
    $subject,
    $device_brand,
    $device_model
);

if (!$emailSent) {
    error_log(
        "Email notification failed for submission ID: " .
        $submissionId
    );
}

echo "Your message has been submitted successfully!";

} catch (PDOException $e) {

    // Remove uploaded image if database insertion fails
    if ($uploaded_file_path !== null) {
        @unlink($uploaded_file_path);
    }

    error_log("Contact form error: " . $e->getMessage());

    http_response_code(500);

    echo "Unable to submit your message. Please try again.";

}