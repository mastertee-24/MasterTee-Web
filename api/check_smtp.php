<?php

$username = getenv("MASTERTEE_SMTP_USER");
$password = getenv("MASTERTEE_SMTP_PASSWORD");

echo "<h2>MasterTee SMTP Configuration Test</h2>";

if ($username) {
    echo "<p>SMTP Username: Configured</p>";
} else {
    echo "<p>SMTP Username: Missing</p>";
}

if ($password) {
    echo "<p>SMTP Password: Configured</p>";
} else {
    echo "<p>SMTP Password: Missing</p>";
}

?>