<?php

session_start();
require_once __DIR__ . "/db.php";

// Allow POST requests only
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    exit("Invalid request method.");
}

// Collect applicant information
$full_name = trim($_POST["full_name"] ?? "");
$phone_number = trim($_POST["phone_number"] ?? "");
$email_address = trim($_POST["email_address"] ?? "");
$date_of_birth = trim($_POST["date_of_birth"] ?? "");
$gender = trim($_POST["gender"] ?? "");
$residential_address = trim($_POST["residential_address"] ?? "");

$previous_technical_experience =
    trim($_POST["previous_technical_experience"] ?? "");

$company_source = trim($_POST["company_source"] ?? "");
$why_learn = trim($_POST["why_learn"] ?? "");

$previous_repair_knowledge =
    trim($_POST["previous_repair_knowledge"] ?? "");

$guardian_name = trim($_POST["guardian_name"] ?? "");
$guardian_phone = trim($_POST["guardian_phone"] ?? "");
$relationship = trim($_POST["relationship"] ?? "");

$agreed_to_terms =
    isset($_POST["agree_to_terms"]) ? 1 : 0;


// Validate required fields
if (
    $full_name === "" ||
    $phone_number === "" ||
    $email_address === "" ||
    $date_of_birth === "" ||
    $gender === "" ||
    $residential_address === "" ||
    $previous_technical_experience === "" ||
    $company_source === "" ||
    $why_learn === "" ||
    !$agreed_to_terms
) {
    http_response_code(400);
    exit("Please complete all required fields.");
}


// Validate email
if (!filter_var($email_address, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    exit("Please enter a valid email address.");
}


// Validate gender
$allowed_genders = ["Male", "Female"];

if (!in_array($gender, $allowed_genders, true)) {
    http_response_code(400);
    exit("Please select a valid gender.");
}


// Validate date of birth
$date = DateTime::createFromFormat("!Y-m-d", $date_of_birth);

if (
    !$date ||
    $date->format("Y-m-d") !== $date_of_birth ||
    $date > new DateTime("today")
) {
    http_response_code(400);
    exit("Please enter a valid date of birth.");
}


// Validate field lengths
if (
    strlen($full_name) > 255 ||
    strlen($phone_number) > 20 ||
    strlen($email_address) > 255 ||
    strlen($residential_address) > 500 ||
    strlen($previous_technical_experience) > 255 ||
    strlen($company_source) > 255 ||
    strlen($guardian_name) > 255 ||
    strlen($guardian_phone) > 20 ||
    strlen($relationship) > 100
) {
    http_response_code(400);
    exit("One or more fields are too long.");
}


// Validate optional dropdowns
$allowed_experience = [
    "",
    "No previous experience",
    "Beginner",
    "Some repair experience",
    "Experienced technician"
];

$allowed_sources = [
    "",
    "Friend or Family",
    "Social Media",
    "Google",
    "Walk-in",
    "Other"
];

$allowed_relationships = [
    "",
    "Father",
    "Mother",
    "Guardian",
    "Other"
];

if (
    !in_array($previous_technical_experience, $allowed_experience, true) ||
    !in_array($company_source, $allowed_sources, true) ||
    !in_array($relationship, $allowed_relationships, true)
) {
    http_response_code(400);
    exit("Invalid dropdown selection.");
}


// Handle passport photograph
if (
    !isset($_FILES["passport"]) ||
    $_FILES["passport"]["error"] !== UPLOAD_ERR_OK
) {
    http_response_code(400);
    exit("Please upload a valid passport photograph.");
}

$file = $_FILES["passport"];

// Maximum size: 2MB
if ($file["size"] > 2 * 1024 * 1024) {
    http_response_code(400);
    exit("Passport photograph must not exceed 2MB.");
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


// Store photographs outside the public website folder
$upload_dir = dirname(__DIR__, 2)
            . "/mastertee_private_passports/";

if (!is_dir($upload_dir)) {
    if (!mkdir($upload_dir, 0700, true)) {
        http_response_code(500);
        exit("Unable to create passport upload directory.");
    }
}


// Generate unique filename
$filename = bin2hex(random_bytes(16))
          . "." . $allowed_types[$mime];

$destination = $upload_dir . $filename;

if (!move_uploaded_file($file["tmp_name"], $destination)) {
    http_response_code(500);
    exit("Unable to save passport photograph.");
}


// Save application to MySQL
try {

    $sql = "INSERT INTO apprenticeship_applications
    (
        full_name,
        phone_number,
        email_address,
        date_of_birth,
        gender,
        residential_address,
        previous_technical_experience,
        company_source,
        why_learn,
        previous_repair_knowledge,
        guardian_name,
        guardian_phone,
        relationship,
        passport,
        agreed_to_terms
    )
    VALUES
    (
        :full_name,
        :phone_number,
        :email_address,
        :date_of_birth,
        :gender,
        :residential_address,
        :previous_technical_experience,
        :company_source,
        :why_learn,
        :previous_repair_knowledge,
        :guardian_name,
        :guardian_phone,
        :relationship,
        :passport,
        :agreed_to_terms
    )";

    $stmt = $conn->prepare($sql);

    $stmt->execute([
        ":full_name" => $full_name,
        ":phone_number" => $phone_number,
        ":email_address" => $email_address,
        ":date_of_birth" => $date_of_birth,
        ":gender" => $gender,
        ":residential_address" => $residential_address,
        ":previous_technical_experience" =>
            $previous_technical_experience,
        ":company_source" => $company_source,
        ":why_learn" => $why_learn,
        ":previous_repair_knowledge" =>
            $previous_repair_knowledge,
        ":guardian_name" => $guardian_name ?: null,
        ":guardian_phone" => $guardian_phone ?: null,
        ":relationship" => $relationship,
        ":passport" => $filename,
        ":agreed_to_terms" => $agreed_to_terms
    ]);

    // Redirect to the Bootstrap success page
$_SESSION['application_success'] = true;

header("Location: application_success.php");
exit;

} catch (PDOException $e) {
    @unlink($destination);

    error_log("Database Error: " . $e->getMessage());

    http_response_code(500);
    exit("Unable to save your application. Please try again.");
}
?>